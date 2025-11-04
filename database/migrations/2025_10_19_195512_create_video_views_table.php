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
        Schema::create('video_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Nullable for guests
            $table->string('session_id')->nullable(); // For tracking guests
            $table->integer('watch_time')->default(0); // Seconds watched
            $table->decimal('completion_rate', 5, 2)->default(0); // Percentage (0-100)
            $table->boolean('completed')->default(false); // Watched till end
            $table->timestamp('viewed_at');
            $table->timestamps();

            // Indexes for performance
            $table->index(['video_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->unique(['video_id', 'user_id', 'session_id']); // Prevent duplicate tracking
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_views');
    }
};
