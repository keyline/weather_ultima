<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('instagram_settings', function (Blueprint $table): void {
            $table->id();
            $table->text('access_token')->nullable();
            $table->string('instagram_user_id')->nullable();
            $table->string('username')->nullable();
            $table->date('token_expires_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_settings');
    }
};
