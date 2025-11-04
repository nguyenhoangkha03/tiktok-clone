<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Notification;
use App\Models\Video;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a new comment or reply
     */
    public function store(Request $request, Video $video)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = $video->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        // Create notification (don't notify yourself)
        if ($video->user_id !== auth()->id()) {
            Notification::create([
                'user_id' => $video->user_id,
                'actor_id' => auth()->id(),
                'type' => 'comment',
                'notifiable_id' => $video->id,
                'notifiable_type' => Video::class,
                'data' => [
                    'comment' => $validated['content'],
                ],
            ]);
        }

        // Load user relationship for JSON response
        $comment->load('user');

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Comment added successfully!',
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at,
                    'parent_id' => $comment->parent_id,
                    'likes_count' => 0,
                    'is_liked' => false,
                    'replies_count' => 0,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'username' => $comment->user->username,
                        'avatar' => $comment->user->avatar ? asset($comment->user->avatar) : null,
                    ]
                ],
                'video_id' => $video->id
            ]);
        }

        return back()->with('success', 'Comment added successfully!');
    }

    /**
     * Delete a comment
     */
    public function destroy(Comment $comment)
    {
        // Check if user owns the comment
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted successfully!');
    }
}
