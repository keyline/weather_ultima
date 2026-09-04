<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class BrevoApiTransportTest extends TestCase
{
    public function test_it_posts_the_message_to_the_brevo_api(): void
    {
        Config::set('mail.default', 'brevo');
        Config::set('mail.mailers.brevo', ['transport' => 'brevo', 'key' => 'xkeysib-test-key']);
        Config::set('mail.from', ['address' => 'from@weather.test', 'name' => 'Weather Ultima']);

        Http::fake(['api.brevo.com/*' => Http::response(['messageId' => '<abc@brevo>'], 201)]);

        Mail::raw('Hello from the test.', function ($message): void {
            $message->to('recipient@weather.test')->subject('Brevo transport test');
        });

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.brevo.com/v3/smtp/email'
                && $request->hasHeader('api-key', 'xkeysib-test-key')
                && $request['subject'] === 'Brevo transport test'
                && $request['to'][0]['email'] === 'recipient@weather.test'
                && $request['sender']['email'] === 'from@weather.test'
                && str_contains($request['textContent'], 'Hello from the test.');
        });
    }

    public function test_it_raises_a_transport_exception_on_api_failure(): void
    {
        Config::set('mail.default', 'brevo');
        Config::set('mail.mailers.brevo', ['transport' => 'brevo', 'key' => 'bad-key']);
        Config::set('mail.from', ['address' => 'from@weather.test', 'name' => 'Weather Ultima']);

        Http::fake(['api.brevo.com/*' => Http::response(['message' => 'unauthorized'], 401)]);

        $this->expectException(TransportException::class);

        Mail::raw('x', fn ($message) => $message->to('r@weather.test')->subject('s'));
    }
}
