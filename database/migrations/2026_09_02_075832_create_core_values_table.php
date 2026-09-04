<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_values', function (Blueprint $table): void {
            $table->id();
            $table->string('icon', 32)->nullable();
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_enabled')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_values');
    }
};
