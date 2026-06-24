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
        Schema::create('rev_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('area', ['books', 'sales', 'inventory', 'products']);
            $table->enum('status', ['success', 'error']);
            $table->text('message');
            $table->json('payload')->nullable(); // request/response data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rev_sync_logs');
    }
};