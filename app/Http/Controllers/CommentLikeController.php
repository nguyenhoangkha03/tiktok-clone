<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentLikeController extends Controller
{
    /**
     * Like a comment
     */
    public function store(Comment $comment)
    {
        $user = auth()->user();

        // Check if already liked
        if ($comment->likes()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'Already liked this comment'
            ], 400);
        }

        // Like the comment
        $comment->likes()->attach($user->id);

        return response()->json([
            'message' => 'Comment liked successfully',
            'likes_count' => $comment->likes()->count()
        ]);
    }

    /**
     * Unlike a comment
     */
    public function destroy(Comment $comment)
    {
        $user = auth()->user();

        // Check if liked
        if (!$comment->likes()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'You have not liked this comment'
            ], 400);
        }

        // Unlike the comment
        $comment->likes()->detach($user->id);

        return response()->json([
            'message' => 'Comment unliked successfully',
            'likes_count' => $comment->likes()->count()
        ]);
    }
}
