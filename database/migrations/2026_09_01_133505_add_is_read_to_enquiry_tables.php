<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['contact_enquiries', 'product_enquiries'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->boolean('is_read')->default(false)->index()->after('message');
            });
        }
    }

    public function down(): void
    {
        foreach (['contact_enquiries', 'product_enquiries'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->dropColumn('is_read');
            });
        }
    }
};
