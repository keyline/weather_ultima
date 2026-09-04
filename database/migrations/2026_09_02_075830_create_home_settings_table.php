<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('banner_title')->nullable();
            $table->string('banner_subtitle', 500)->nullable();
            $table->string('banner_image_path')->nullable();
            $table->string('banner_image_alt')->nullable();
            $table->string('founder_name')->nullable();
            $table->string('founder_designation')->nullable();
            $table->text('founder_intro')->nullable();
            $table->text('founder_description')->nullable();
            $table->string('founder_image_path')->nullable();
            $table->string('founder_signature_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_settings');
    }
};
