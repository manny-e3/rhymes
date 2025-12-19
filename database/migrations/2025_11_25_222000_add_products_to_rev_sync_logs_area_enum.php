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
        // MySQL specific raw SQL to modify the enum values
        DB::statement("ALTER TABLE rev_sync_logs MODIFY COLUMN area ENUM('books', 'sales', 'inventory', 'products')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE rev_sync_logs MODIFY COLUMN area ENUM('books', 'sales', 'inventory')");
    }
};