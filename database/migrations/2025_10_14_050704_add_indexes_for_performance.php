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
        // Add indexes to videos table
        Schema::table('videos', function (Blueprint $table) {
            $table->index('user_id'); // For queries filtering by user
            $table->index('created_at'); // For latest() queries
            $table->index(['user_id', 'created_at']); // Composite index for user's latest videos
        });

        // Add indexes to likes table
        Schema::table('likes', function (Blueprint $table) {
            $table->index('video_id'); // For counting likes per video
            $table->index('created_at'); // For latest likes
        });

        // Add indexes to comments table
        Schema::table('comments', function (Blueprint $table) {
            $table->index('video_id'); // For fetching comments per video
            $table->index('user_id'); // For user's comments
            $table->index('created_at'); // For latest comments
        });

        // Add indexes to follows table
        Schema::table('follows', function (Blueprint $table) {
            $table->index('follower_id'); // For getting who someone follows
            $table->index('following_id'); // For getting someone's followers
            $table->index('created_at'); // For latest follows
        });

        // Add index to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('username'); // For profile lookups
            $table->index('created_at'); // For newest users
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->dropIndex(['video_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['video_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('follows', function (Blueprint $table) {
            $table->dropIndex(['follower_id']);
            $table->dropIndex(['following_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['username']);
            $table->dropIndex(['created_at']);
        });
    }
};
