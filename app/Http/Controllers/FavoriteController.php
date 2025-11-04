<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Video;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Add video to favorites
     */
    public function store(Request $request, Video $video)
    {
        $user = auth()->user();

        // Check if already favorited
        if ($user->hasFavorited($video->id)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'You already favorited this video.',
                    'favorites_count' => $video->favorites()->count()
                ], 400);
            }
            return back();
        }

        $user->favorites()->create([
            'video_id' => $video->id,
        ]);

        // Create notification (don't notify yourself)
        if ($video->user_id !== $user->id) {
            Notification::create([
                'user_id' => $video->user_id,
                'actor_id' => $user->id,
                'type' => 'favorite',
                'notifiable_id' => $video->id,
                'notifiable_type' => Video::class,
            ]);
        }

        $favoritesCount = $video->favorites()->count();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Video favorited!',
                'favorites_count' => $favoritesCount
            ]);
        }

        return back();
    }

    /**
     * Remove video from favorites
     */
    public function destroy(Request $request, Video $video)
    {
        $user = auth()->user();

        $user->favorites()->where('video_id', $video->id)->delete();

        $favoritesCount = $video->favorites()->count();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Video unfavorited!',
                'favorites_count' => $favoritesCount
            ]);
        }

        return back();
    }
}
