<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weather_settings', function (Blueprint $table): void {
            $table->string('kolkata_image_path')->nullable();
            $table->string('deoghar_image_path')->nullable();
            $table->string('sundarban_image_path')->nullable();
            $table->string('bardhaman_image_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('weather_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'kolkata_image_path',
                'deoghar_image_path',
                'sundarban_image_path',
                'bardhaman_image_path',
            ]);
        });
    }
};
