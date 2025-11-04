<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Video;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Like a video
     */
    public function store(Request $request, Video $video)
    {
        $user = auth()->user();

        // Check if already liked
        if ($user->hasLiked($video->id)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'You already liked this video.',
                    'likes_count' => $video->likes()->count()
                ], 400);
            }
            return back()->with('error', 'You already liked this video.');
        }

        $user->likes()->create([
            'video_id' => $video->id,
        ]);

        // Create notification (don't notify yourself)
        if ($video->user_id !== $user->id) {
            Notification::create([
                'user_id' => $video->user_id,
                'actor_id' => $user->id,
                'type' => 'like',
                'notifiable_id' => $video->id,
                'notifiable_type' => Video::class,
            ]);
        }

        $likesCount = $video->likes()->count();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Video liked!',
                'likes_count' => $likesCount
            ]);
        }

        return back()->with('success', 'Video liked!');
    }

    /**
     * Unlike a video
     */
    public function destroy(Request $request, Video $video)
    {
        $user = auth()->user();

        $user->likes()
            ->where('video_id', $video->id)
            ->delete();

        $likesCount = $video->likes()->count();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Video unliked!',
                'likes_count' => $likesCount
            ]);
        }

        return back()->with('success', 'Video unliked!');
    }
}
