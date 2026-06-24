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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('isbn')->unique();
            $table->string('title');
            $table->string('genre');
            $table->decimal('price', 10, 2);
            $table->string('book_type'); // physical, digital, both
            $table->text('description');
            $table->enum('status', ['pending_review', 'send_review_copy', 'approved_awaiting_delivery', 'stocked', 'rejected'])->default('pending_review');
            $table->text('admin_notes')->nullable();
            $table->string('rev_book_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};