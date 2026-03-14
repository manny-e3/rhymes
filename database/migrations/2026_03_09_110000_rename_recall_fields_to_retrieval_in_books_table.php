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
        // 1. Rename columns in books table
        Schema::table('books', function (Blueprint $table) {
            $table->renameColumn('recall_requested', 'retrieval_requested');
            $table->renameColumn('recall_reason', 'retrieval_reason');
            $table->renameColumn('recall_requested_at', 'retrieval_requested_at');
        });

        // 2. Update status enum values
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            // First, update existing records
            DB::table('books')->where('status', 'recall_requested')->update(['status' => 'retrieval_requested']);
            DB::table('books')->where('status', 'recalled')->update(['status' => 'retrieved']);

            // Then modify the enum
            DB::statement("ALTER TABLE books MODIFY COLUMN status ENUM('pending_review', 'send_review_copy', 'rejected', 'approved_awaiting_delivery', 'stocked', 'edited_pending_approval', 'retrieval_requested', 'retrieved') DEFAULT 'pending_review'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            // Revert records
            DB::table('books')->where('status', 'retrieval_requested')->update(['status' => 'recall_requested']);
            DB::table('books')->where('status', 'retrieved')->update(['status' => 'recalled']);

            // Revert enum
            DB::statement("ALTER TABLE books MODIFY COLUMN status ENUM('pending_review', 'send_review_copy', 'rejected', 'approved_awaiting_delivery', 'stocked', 'edited_pending_approval', 'recall_requested', 'recalled') DEFAULT 'pending_review'");
        }

        Schema::table('books', function (Blueprint $table) {
            $table->renameColumn('retrieval_requested', 'recall_requested');
            $table->renameColumn('retrieval_reason', 'recall_reason');
            $table->renameColumn('retrieval_requested_at', 'recall_requested_at');
        });
    }
};
