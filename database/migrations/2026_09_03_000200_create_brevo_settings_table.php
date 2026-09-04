<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brevo_settings', function (Blueprint $table): void {
            $table->id();
            $table->text('api_key')->nullable();
            $table->string('sender_name')->default('Weather Ultima');
            $table->string('sender_email')->default('hello@example.com');
            $table->string('reply_to_email')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brevo_settings');
    }
};
