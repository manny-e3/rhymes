<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->boolean('recall_requested')->default(false)->after('admin_notes');
            $table->text('recall_reason')->nullable()->after('recall_requested');
            $table->timestamp('recall_requested_at')->nullable()->after('recall_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['recall_requested', 'recall_reason', 'recall_requested_at']);
        });
    }
};