<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather_settings', function (Blueprint $table): void {
            $table->id();
            $table->text('application_key')->nullable();
            $table->text('api_key')->nullable();
            $table->string('kolkata_mac')->nullable();
            $table->string('deoghar_mac')->nullable();
            $table->string('sundarban_mac')->nullable();
            $table->string('bardhaman_mac')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_settings');
    }
};
