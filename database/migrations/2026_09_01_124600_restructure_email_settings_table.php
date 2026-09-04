<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_settings', function (Blueprint $table): void {
            $table->renameColumn('notification_email', 'contact_notification_email');
            $table->renameColumn('subject', 'contact_subject');
            $table->renameColumn('notifications_enabled', 'contact_notifications_enabled');
        });

        Schema::table('email_settings', function (Blueprint $table): void {
            $table->string('product_notification_email')->nullable()->after('contact_notification_email');
            $table->string('product_subject')->nullable()->after('contact_subject');
            $table->boolean('product_notifications_enabled')->default(true)->after('contact_notifications_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('email_settings', function (Blueprint $table): void {
            $table->dropColumn(['product_notification_email', 'product_subject', 'product_notifications_enabled']);
        });

        Schema::table('email_settings', function (Blueprint $table): void {
            $table->renameColumn('contact_notification_email', 'notification_email');
            $table->renameColumn('contact_subject', 'subject');
            $table->renameColumn('contact_notifications_enabled', 'notifications_enabled');
        });
    }
};
