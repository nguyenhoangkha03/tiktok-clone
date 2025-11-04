<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoView;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VideoViewController extends Controller
{
    /**
     * Track video view with watch time and completion rate
     */
    public function trackView(Request $request, Video $video)
    {
        $validated = $request->validate([
            'watch_time' => 'required|integer|min:0',
            'video_duration' => 'required|numeric|min:0',
        ]);

        $watchTime = $validated['watch_time'];
        $duration = $validated['video_duration'];
        $completionRate = $duration > 0 ? min(($watchTime / $duration) * 100, 100) : 0;
        $completed = $completionRate >= 95; // Consider completed if watched 95%+

        // Get or create session ID for guests
        $sessionId = $request->session()->get('session_id');
        if (!$sessionId) {
            $sessionId = Str::uuid()->toString();
            $request->session()->put('session_id', $sessionId);
        }

        $userId = auth()->id();

        // Update or create view record
        VideoView::updateOrCreate(
            [
                'video_id' => $video->id,
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId, // Use session_id only for guests
            ],
            [
                'watch_time' => $watchTime,
                'completion_rate' => $completionRate,
                'completed' => $completed,
                'viewed_at' => now(),
            ]
        );

        // Increment video views counter
        $video->incrementViews();

        return response()->json([
            'success' => true,
            'completion_rate' => round($completionRate, 2),
            'completed' => $completed,
        ]);
    }
}
