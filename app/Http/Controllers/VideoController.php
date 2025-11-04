<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('videos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log request info
        \Log::info('Video upload request received', [
            'user_id' => auth()->id(),
            'has_file' => $request->hasFile('video'),
            'all_input' => $request->all()
        ]);

        // TEMPORARY TEST - just log and return
        \Log::info('Store method reached!', [
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);

        return redirect()->route('home')->with('success', 'Test successful! Route is working.');

        /*
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'video' => 'required|file|mimes:mp4,mov,avi,wmv,flv,webm,mpeg|max:102400', // Max 100MB
        ], [
            'video.required' => 'Please select a video file.',
            'video.file' => 'Please upload a valid file.',
            'video.mimes' => 'Video must be in MP4, MOV, AVI, WMV, FLV, WebM, or MPEG format.',
            'video.max' => 'Video size must not exceed 100MB.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 2000 characters.',
        ]);
        */

        try {
            // Handle video upload
            if ($request->hasFile('video')) {
                $video = $request->file('video');

                // Generate unique filename
                $filename = time() . '_' . auth()->id() . '.' . $video->getClientOriginalExtension();

                // Move to public/videos directory
                $video->move(public_path('videos'), $filename);

                // Store video path
                $validated['video_path'] = 'videos/' . $filename;
            }

            // Remove video from validated array as we've already handled it
            unset($validated['video']);

            // Create video record
            $videoRecord = auth()->user()->videos()->create($validated);

            return redirect()->route('videos.show', ['video' => $videoRecord->id])
                ->with('success', 'Video uploaded successfully!');
        } catch (\Exception $e) {
            // Delete uploaded file if database insert fails
            if (isset($validated['video_path']) && file_exists(public_path($validated['video_path']))) {
                unlink(public_path($validated['video_path']));
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to upload video. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($username, Video $video)
    {
        // Verify username matches video owner
        if ($video->user->username !== $username) {
            abort(404);
        }

        $video->load([
                'user',
                'likes',
                'comments.user',
                'comments.replies.user',
                'comments.likes'
            ])
            ->loadCount(['likes', 'comments', 'favorites']);

        // Increment views
        $video->incrementViews();

        // Get other videos from this creator (for "Creator videos" tab)
        $creatorVideos = Video::where('user_id', $video->user_id)
            ->where('id', '!=', $video->id)
            ->withCount(['likes', 'comments', 'favorites'])
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        // Get previous and next videos (only from same creator)
        // prevVideo = newer video (created_at >)
        // nextVideo = older video (created_at <)
        $prevVideo = Video::where('user_id', $video->user_id)
            ->where('created_at', '>', $video->created_at)
            ->orderBy('created_at')
            ->with('user')
            ->first();

        $nextVideo = Video::where('user_id', $video->user_id)
            ->where('created_at', '<', $video->created_at)
            ->orderByDesc('created_at')
            ->with('user')
            ->first();

        return view('videos.show', compact('video', 'creatorVideos', 'prevVideo', 'nextVideo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Video $video)
    {
        // Check if user owns the video
        if ($video->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('videos.edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $video)
    {
        // Check if user owns the video
        if ($video->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'video' => 'nullable|file|mimes:mp4,mov,avi,wmv,flv,webm,mpeg|max:102400', // Optional for update
        ], [
            'video.file' => 'Please upload a valid file.',
            'video.mimes' => 'Video must be in MP4, MOV, AVI, WMV, FLV, WebM, or MPEG format.',
            'video.max' => 'Video size must not exceed 100MB.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 2000 characters.',
        ]);

        try {
            $oldVideoPath = null;

            // Handle new video upload if provided
            if ($request->hasFile('video')) {
                $oldVideoPath = $video->video_path;

                $videoFile = $request->file('video');
                $filename = time() . '_' . auth()->id() . '.' . $videoFile->getClientOriginalExtension();
                $videoFile->move(public_path('videos'), $filename);
                $validated['video_path'] = 'videos/' . $filename;
            }

            unset($validated['video']);
            $video->update($validated);

            // Delete old video file if new one was uploaded
            if ($oldVideoPath && file_exists(public_path($oldVideoPath))) {
                unlink(public_path($oldVideoPath));
            }

            return redirect()->route('videos.show', [$video->user->username, $video])
                ->with('success', 'Video updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update video. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        // Check if user owns the video
        if ($video->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete video file from storage
        if ($video->video_path && file_exists(public_path($video->video_path))) {
            unlink(public_path($video->video_path));
        }

        $video->delete();

        return redirect()->route('home')
            ->with('success', 'Video deleted successfully!');
    }

    /**
     * Search videos by keyword (API endpoint)
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 10);

        if (strlen($query) < 2) {
            return response()->json(['videos' => []]);
        }

        $videos = Video::with('user')
            ->withCount('likes')
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->orderByDesc('views')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($video) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'description' => $video->description,
                    'thumbnail' => $video->thumbnail ? asset($video->thumbnail) : null,
                    'views' => $video->views,
                    'likes_count' => $video->likes_count,
                    'user' => [
                        'id' => $video->user->id,
                        'name' => $video->user->name,
                        'username' => $video->user->username,
                        'avatar' => $video->user->avatar ? asset($video->user->avatar) : null,
                    ],
                ];
            });

        return response()->json(['videos' => $videos]);
    }
}
