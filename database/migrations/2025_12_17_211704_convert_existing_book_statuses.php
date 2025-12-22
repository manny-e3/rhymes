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
            // First, expand the enum to include all possible values temporarily
            DB::statement("ALTER TABLE books MODIFY COLUMN status ENUM('pending', 'accepted', 'stocked', 'rejected', 'pending_review', 'send_review_copy', 'approved_awaiting_delivery', 'temp_approved') DEFAULT 'pending'");
            
            // Convert existing status values to new values
            // Handle each status individually to avoid conflicts
            
            // First convert 'accepted' to avoid conflict with 'approved_awaiting_delivery'
            DB::statement("UPDATE books SET status = 'temp_approved' WHERE status = 'accepted'");
            
            // Then convert 'pending' to 'pending_review'
            DB::statement("UPDATE books SET status = 'pending_review' WHERE status = 'pending'");
            
            // Finally convert 'temp_approved' to 'approved_awaiting_delivery'
            DB::statement("UPDATE books SET status = 'approved_awaiting_delivery' WHERE status = 'temp_approved'");
            
            // Now update the column definition to use only new enum values
            DB::statement("ALTER TABLE books MODIFY COLUMN status ENUM('pending_review', 'send_review_copy', 'rejected', 'approved_awaiting_delivery', 'stocked') DEFAULT 'pending_review'");
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
            // Expand enum to include old and new values
            DB::statement("ALTER TABLE books MODIFY COLUMN status ENUM('pending', 'accepted', 'stocked', 'rejected', 'pending_review', 'send_review_copy', 'approved_awaiting_delivery') DEFAULT 'pending'");
            
            // Convert status values back to old values
            DB::statement("UPDATE books SET status = 'pending' WHERE status = 'pending_review'");
            DB::statement("UPDATE books SET status = 'pending' WHERE status = 'send_review_copy'");
            DB::statement("UPDATE books SET status = 'accepted' WHERE status = 'approved_awaiting_delivery'");
            DB::statement("UPDATE books SET status = 'stocked' WHERE status = 'stocked'");
            DB::statement("UPDATE books SET status = 'rejected' WHERE status = 'rejected'");
            
            // Revert column definition to old enum values
            DB::statement("ALTER TABLE books MODIFY COLUMN status ENUM('pending', 'accepted', 'stocked', 'rejected') DEFAULT 'pending'");
        } else {
            // For SQLite and other databases, we'll skip this migration
        }
    }
};