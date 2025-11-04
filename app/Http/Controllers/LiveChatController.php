<?php

namespace App\Http\Controllers;

use App\Events\LiveChatMessageSent;
use App\Models\LiveChat;
use App\Models\LiveStream;
use Illuminate\Http\Request;

class LiveChatController extends Controller
{
    /**
     * Get chat messages for a live stream
     */
    public function index(LiveStream $liveStream)
    {
        $messages = LiveChat::with('user')
            ->where('live_stream_id', $liveStream->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'messages' => $messages
        ]);
    }

    /**
     * Send a chat message
     */
    public function store(Request $request, LiveStream $liveStream)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // Check if stream is still live
        if (!$liveStream->isLive()) {
            return response()->json([
                'error' => 'This live stream has ended.'
            ], 403);
        }

        // Create chat message
        $chat = LiveChat::create([
            'live_stream_id' => $liveStream->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        // Load user relationship
        $chat->load('user');

        // Broadcast the message
        broadcast(new LiveChatMessageSent($chat))->toOthers();

        return response()->json([
            'message' => 'Message sent successfully',
            'chat' => $chat
        ]);
    }
}
