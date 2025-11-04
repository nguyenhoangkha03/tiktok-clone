<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoReport;
use Illuminate\Http\Request;

class VideoReportController extends Controller
{
    /**
     * Store a video report
     */
    public function store(Request $request, Video $video)
    {
        $validated = $request->validate([
            'reason' => 'required|in:spam,inappropriate,violence,harassment,false_info,other',
            'description' => 'nullable|string|max:500',
        ]);

        // Check if user already reported this video
        $existingReport = VideoReport::where('user_id', auth()->id())
            ->where('video_id', $video->id)
            ->first();

        if ($existingReport) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'You have already reported this video.'
                ], 400);
            }
            return back()->with('error', 'You have already reported this video.');
        }

        VideoReport::create([
            'user_id' => auth()->id(),
            'video_id' => $video->id,
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Thank you for your report. We will review it shortly.'
            ]);
        }

        return back()->with('success', 'Thank you for your report. We will review it shortly.');
    }
}
