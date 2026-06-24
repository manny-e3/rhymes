<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check the database driver
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            // MySQL specific raw SQL to modify the enum values
            DB::statement("ALTER TABLE rev_sync_logs MODIFY COLUMN area ENUM('books', 'sales', 'inventory', 'products')");
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
            // Revert to original enum values
            DB::statement("ALTER TABLE rev_sync_logs MODIFY COLUMN area ENUM('books', 'sales', 'inventory')");
        } else {
            // For SQLite and other databases, we'll skip this migration
        }
    }
};