<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recaptcha_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('site_key')->nullable();
            $table->text('secret_key')->nullable();
            $table->string('version', 20)->default('v2');
            $table->decimal('minimum_score', 3, 2)->default(0.5);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recaptcha_settings');
    }
};
