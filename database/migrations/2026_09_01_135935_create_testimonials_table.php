<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->string('designation')->nullable();
            $table->string('company')->nullable();
            $table->text('review');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_enabled')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
