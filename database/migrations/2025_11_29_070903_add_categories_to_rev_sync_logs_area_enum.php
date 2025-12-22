<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check the database driver
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE rev_sync_logs MODIFY COLUMN area ENUM('books', 'sales', 'inventory', 'products', 'categories')");
        } else {
            // For SQLite and other databases, we'll skip this migration
            // SQLite doesn't support MODIFY COLUMN directly
            // The enum will be handled differently in SQLite
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check the database driver
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE rev_sync_logs MODIFY COLUMN area ENUM('books', 'sales', 'inventory', 'products')");
        } else {
            // For SQLite and other databases, we'll skip this migration
        }
    }
};