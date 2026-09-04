<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_page_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('banner_title')->nullable();
            $table->text('intro_heading')->nullable();
            $table->text('intro_body')->nullable();
            $table->string('intro_statement')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_page_settings');
    }
};
