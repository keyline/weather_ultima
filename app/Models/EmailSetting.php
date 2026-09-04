<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'contact_notification_email',
    'product_notification_email',
    'sender_name',
    'contact_subject',
    'product_subject',
    'contact_notifications_enabled',
    'product_notifications_enabled',
])]
class EmailSetting extends Model
{
    /**
     * Sensible defaults for a fresh settings row.
     *
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'contact_notification_email' => 'info@weather.com',
            'product_notification_email' => 'info@weather.com',
            'sender_name' => 'Weather Ultima',
            'contact_subject' => 'New website contact enquiry',
            'product_subject' => 'New website product enquiry',
            'contact_notifications_enabled' => true,
            'product_notifications_enabled' => true,
        ];
    }

    /**
     * The single settings row, creating one with defaults on first access.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], static::defaults());
    }

    protected function casts(): array
    {
        return [
            'contact_notifications_enabled' => 'boolean',
            'product_notifications_enabled' => 'boolean',
        ];
    }
}
