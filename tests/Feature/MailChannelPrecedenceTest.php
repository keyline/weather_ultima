<?php

namespace Tests\Feature;

use App\Mail\MailChannelConfigurator;
use App\Models\BrevoSetting;
use App\Models\SmtpSetting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MailChannelPrecedenceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_brevo_wins_when_active(): void
    {
        BrevoSetting::current()->update(['api_key' => 'xkeysib-key', 'is_active' => true]);
        SmtpSetting::current()->update(['host' => 'smtp.example.test', 'is_active' => true]);

        MailChannelConfigurator::configure();

        $this->assertSame('brevo', config('mail.default'));
    }

    public function test_smtp_is_used_when_brevo_is_off(): void
    {
        BrevoSetting::current()->update(['api_key' => 'xkeysib-key', 'is_active' => false]);
        SmtpSetting::current()->update(['host' => 'smtp.example.test', 'is_active' => true]);

        MailChannelConfigurator::configure();

        $this->assertSame('smtp', config('mail.default'));
        $this->assertSame('smtp.example.test', config('mail.mailers.smtp.host'));
    }

    public function test_brevo_without_a_key_falls_through_to_smtp(): void
    {
        BrevoSetting::current()->update(['api_key' => null, 'is_active' => true]);
        SmtpSetting::current()->update(['host' => 'smtp.example.test', 'is_active' => true]);

        MailChannelConfigurator::configure();

        $this->assertSame('smtp', config('mail.default'));
    }

    public function test_env_config_is_left_untouched_when_nothing_is_active(): void
    {
        config(['mail.default' => 'log']);

        BrevoSetting::current();
        SmtpSetting::current();

        MailChannelConfigurator::configure();

        $this->assertSame('log', config('mail.default'));
    }
}
