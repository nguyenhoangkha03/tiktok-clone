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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocker_id')->constrained('users')->onDelete('cascade'); // Người block
            $table->foreignId('blocked_id')->constrained('users')->onDelete('cascade'); // Người bị block
            $table->timestamps();

            // Prevent duplicate blocks
            $table->unique(['blocker_id', 'blocked_id']);

            // Indexes for performance
            $table->index('blocker_id');
            $table->index('blocked_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
