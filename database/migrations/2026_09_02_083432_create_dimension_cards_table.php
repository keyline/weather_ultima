<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dimension_cards', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('description', 500);
            $table->string('image')->nullable();
            $table->string('link_url')->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_enabled')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dimension_cards');
    }
};
