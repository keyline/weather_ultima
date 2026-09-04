<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_enquiries', function (Blueprint $table): void {
            $table->dropIndex('contact_submissions_status_created_at_index');
            $table->dropIndex('contact_submissions_status_index');
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('contact_enquiries', function (Blueprint $table): void {
            $table->string('status')->default('new')->index();
            $table->index(['status', 'created_at']);
        });
    }
};
