<?php

namespace App\Http\Controllers;

use App\Events\ViewerJoined;
use App\Models\LiveStream;
use Illuminate\Http\Request;

class LiveStreamController extends Controller
{
    /**
     * Display all live streams
     */
    public function index()
    {
        $liveStreams = LiveStream::with('user')
            ->live()
            ->orderBy('viewers_count', 'desc')
            ->orderBy('started_at', 'desc')
            ->paginate(12);

        return view('live.index', compact('liveStreams'));
    }

    /**
     * Show create live stream form
     */
    public function create()
    {
        return view('live.create');
    }

    /**
     * Start a new live stream
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $liveStream = LiveStream::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => 'live',
            'started_at' => now(),
        ]);

        return redirect()->route('live.show', $liveStream)
            ->with('success', 'Live stream started successfully!');
    }

    /**
     * Show a specific live stream
     */
    public function show(LiveStream $liveStream)
    {
        $liveStream->load('user');

        // Increment viewers count
        $liveStream->increment('viewers_count');

        // Notify streamer that a viewer joined (for WebRTC signaling)
        if (auth()->check() && auth()->id() !== $liveStream->user_id) {
            broadcast(new ViewerJoined(
                $liveStream->id,
                auth()->id(),
                auth()->user()->name
            ))->toOthers();
        }

        return view('live.show', compact('liveStream'));
    }

    /**
     * End a live stream
     */
    public function end(LiveStream $liveStream)
    {
        // Check if user owns this stream
        if ($liveStream->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $liveStream->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        return redirect()->route('live.index')
            ->with('success', 'Live stream ended.');
    }
}
