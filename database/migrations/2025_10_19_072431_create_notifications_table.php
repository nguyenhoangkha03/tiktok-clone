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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Người nhận notification
            $table->foreignId('actor_id')->nullable()->constrained('users')->onDelete('cascade'); // Người thực hiện action (ví dụ: người like, comment, follow)
            $table->string('type'); // like, comment, follow, mention
            $table->foreignId('notifiable_id')->nullable(); // ID của object liên quan (video_id, comment_id, etc)
            $table->string('notifiable_type')->nullable(); // Model type (Video, Comment, etc)
            $table->text('data')->nullable(); // JSON data bổ sung
            $table->timestamp('read_at')->nullable(); // Thời gian đọc
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('actor_id');
            $table->index(['user_id', 'read_at']); // For unread notifications query
            $table->index(['notifiable_id', 'notifiable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
