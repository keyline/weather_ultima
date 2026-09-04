<?php

namespace App\Mail;

use App\Models\BrevoSetting;
use App\Models\SmtpSetting;
use Throwable;

/**
 * Applies the admin-managed outgoing mail configuration at runtime.
 *
 * Precedence: an active Brevo configuration wins, then an active SMTP
 * configuration, otherwise the server's .env / config values stand.
 */
class MailChannelConfigurator
{
    public static function configure(): void
    {
        try {
            $brevo = BrevoSetting::current();

            if ($brevo->is_active && $brevo->hasApiKey()) {
                $brevo->applyToMailConfig();

                return;
            }

            SmtpSetting::current()->applyToMailConfig();
        } catch (Throwable) {
            // Settings tables not migrated yet, or the database is unavailable —
            // leave the environment's mail configuration in place.
        }
    }
}
