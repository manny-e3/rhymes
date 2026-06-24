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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'bulk', 'personal', 'newsletter', 'sales_report'
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('subject');
            $table->text('content');
            $table->json('recipients'); // Array of user IDs or email addresses
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->string('status')->default('pending'); // 'pending', 'processing', 'completed', 'failed'
            $table->json('metadata')->nullable(); // Additional data like filters used, errors, etc.
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
