<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('contact_submissions', 'contact_enquiries');
    }

    public function down(): void
    {
        Schema::rename('contact_enquiries', 'contact_submissions');
    }
};
