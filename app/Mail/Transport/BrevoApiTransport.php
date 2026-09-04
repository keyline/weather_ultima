<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

/**
 * Sends mail through Brevo's transactional email HTTP API (v3/smtp/email)
 * using Laravel's HTTP client — no extra Composer dependency required.
 */
class BrevoApiTransport extends AbstractTransport
{
    private const ENDPOINT = 'https://api.brevo.com/v3/smtp/email';

    public function __construct(private readonly string $apiKey)
    {
        parent::__construct();
    }

    public function __toString(): string
    {
        return 'brevo+api';
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $envelope = $message->getEnvelope();

        $response = Http::withHeaders([
            'api-key' => $this->apiKey,
            'accept' => 'application/json',
        ])->asJson()->post(self::ENDPOINT, $this->payload($email, $envelope));

        if ($response->failed()) {
            throw new TransportException(
                'Brevo API request failed ('.$response->status().'): '.$response->body()
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Email $email, Envelope $envelope): array
    {
        $sender = $email->getFrom()[0] ?? $envelope->getSender();

        $payload = [
            'sender' => $this->address($sender),
            'to' => array_map($this->address(...), $this->recipients($email, $envelope)),
            'subject' => $email->getSubject() ?? '',
        ];

        $html = $email->getHtmlBody();
        $text = $email->getTextBody();

        if (filled($html)) {
            $payload['htmlContent'] = is_string($html) ? $html : (string) stream_get_contents($html);
        }

        if (filled($text)) {
            $payload['textContent'] = is_string($text) ? $text : (string) stream_get_contents($text);
        }

        if (! isset($payload['htmlContent']) && ! isset($payload['textContent'])) {
            $payload['textContent'] = ' ';
        }

        if ($cc = $email->getCc()) {
            $payload['cc'] = array_map($this->address(...), $cc);
        }

        if ($bcc = $email->getBcc()) {
            $payload['bcc'] = array_map($this->address(...), $bcc);
        }

        if ($replyTo = $email->getReplyTo()) {
            $payload['replyTo'] = $this->address($replyTo[0]);
        }

        $attachments = [];

        foreach ($email->getAttachments() as $attachment) {
            $filename = $attachment->getPreparedHeaders()
                ->getHeaderParameter('content-disposition', 'filename') ?? 'attachment';

            $attachments[] = [
                'name' => $filename,
                'content' => base64_encode($attachment->getBody()),
            ];
        }

        if ($attachments !== []) {
            $payload['attachment'] = $attachments;
        }

        return $payload;
    }

    /**
     * Message recipients excluding cc / bcc addresses.
     *
     * @return array<int, Address>
     */
    private function recipients(Email $email, Envelope $envelope): array
    {
        $excluded = array_merge($email->getCc(), $email->getBcc());

        return array_values(array_filter(
            $envelope->getRecipients(),
            fn (Address $address): bool => ! in_array($address, $excluded, true),
        ));
    }

    /**
     * @return array{email: string, name?: string}
     */
    private function address(Address $address): array
    {
        $payload = ['email' => $address->getAddress()];

        if ($address->getName() !== '') {
            $payload['name'] = $address->getName();
        }

        return $payload;
    }
}
