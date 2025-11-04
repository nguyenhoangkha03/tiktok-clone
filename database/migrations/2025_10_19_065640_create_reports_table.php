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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade'); // Người report
            $table->foreignId('reported_user_id')->constrained('users')->onDelete('cascade'); // Người bị report
            $table->string('reason'); // Lý do: spam, harassment, inappropriate, etc.
            $table->text('description')->nullable(); // Mô tả chi tiết
            $table->enum('status', ['pending', 'reviewed', 'resolved'])->default('pending');
            $table->timestamps();

            // Prevent duplicate reports
            $table->unique(['reporter_id', 'reported_user_id']);

            // Indexes for performance
            $table->index('reporter_id');
            $table->index('reported_user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
