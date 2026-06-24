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
            // First, expand the enum to include the new values
            DB::statement("ALTER TABLE books MODIFY COLUMN status ENUM('pending_review', 'send_review_copy', 'rejected', 'approved_awaiting_delivery', 'stocked', 'edited_pending_approval', 'recall_requested', 'recalled') DEFAULT 'pending_review'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            // Revert to original enum values without the new recall statuses
            DB::statement("ALTER TABLE books MODIFY COLUMN status ENUM('pending_review', 'send_review_copy', 'rejected', 'approved_awaiting_delivery', 'stocked', 'edited_pending_approval') DEFAULT 'pending_review'");
        }
    }
};