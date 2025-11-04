<?php

namespace App\Http\Controllers;

use App\Models\NotInterested;
use App\Models\Video;
use Illuminate\Http\Request;

class NotInterestedController extends Controller
{
    /**
     * Mark video as not interested
     */
    public function store(Request $request, Video $video)
    {
        // Check if already marked as not interested
        $existing = NotInterested::where('user_id', auth()->id())
            ->where('video_id', $video->id)
            ->first();

        if ($existing) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Already marked as not interested.'
                ], 400);
            }
            return back();
        }

        NotInterested::create([
            'user_id' => auth()->id(),
            'video_id' => $video->id,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Video marked as not interested. We will show you less content like this.'
            ]);
        }

        return back()->with('success', 'Video marked as not interested.');
    }
}
