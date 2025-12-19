<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE rev_sync_logs MODIFY COLUMN area ENUM('books', 'sales', 'inventory', 'products', 'categories')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE rev_sync_logs MODIFY COLUMN area ENUM('books', 'sales', 'inventory', 'products')");
    }
};
