<?php

namespace App\Http\Controllers;

use App\Events\IceCandidateShared;
use App\Events\StreamAnswerCreated;
use App\Events\StreamOfferCreated;
use App\Events\StreamStarted;
use App\Events\ViewerRequestedOffer;
use App\Models\LiveStream;
use Illuminate\Http\Request;

class WebRTCSignalingController extends Controller
{
    /**
     * Streamer creates an offer for a specific viewer
     */
    public function sendOffer(Request $request, LiveStream $liveStream)
    {
        // Verify user is the stream owner
        if ($liveStream->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'offer' => 'required|array',
            'target_user_id' => 'required|integer', // Target viewer
        ]);

        // Broadcast offer to specific viewer
        broadcast(new StreamOfferCreated(
            $liveStream->id,
            $validated['offer'],
            auth()->id(),
            $validated['target_user_id']
        ))->toOthers();

        return response()->json([
            'message' => 'Offer sent successfully',
        ]);
    }

    /**
     * Viewer sends an answer to streamer
     */
    public function sendAnswer(Request $request, LiveStream $liveStream)
    {
        $validated = $request->validate([
            'answer' => 'required|array',
            'target_user_id' => 'required|integer', // Target streamer
        ]);

        // Broadcast answer to the streamer
        broadcast(new StreamAnswerCreated(
            $liveStream->id,
            $validated['answer'],
            auth()->id(),
            $validated['target_user_id']
        ))->toOthers();

        return response()->json([
            'message' => 'Answer sent successfully',
        ]);
    }

    /**
     * Share ICE candidate to specific peer
     */
    public function sendIceCandidate(Request $request, LiveStream $liveStream)
    {
        $validated = $request->validate([
            'candidate' => 'required|array',
            'target_user_id' => 'required|integer', // Target peer
        ]);

        // Broadcast ICE candidate to specific peer
        broadcast(new IceCandidateShared(
            $liveStream->id,
            $validated['candidate'],
            auth()->id(),
            $validated['target_user_id']
        ))->toOthers();

        return response()->json([
            'message' => 'ICE candidate sent successfully',
        ]);
    }

    /**
     * Notify that stream has started (streamer broadcasts)
     */
    public function notifyStreamStarted(LiveStream $liveStream)
    {
        // Verify user is the stream owner
        if ($liveStream->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        \Log::info('Broadcasting StreamStarted event', [
            'live_stream_id' => $liveStream->id,
            'streamer_id' => auth()->id(),
        ]);

        broadcast(new StreamStarted(
            $liveStream->id,
            auth()->id()
        ))->toOthers();

        return response()->json([
            'message' => 'Stream started notification sent',
        ]);
    }

    /**
     * Viewer requests offer from streamer
     */
    public function requestOffer(LiveStream $liveStream)
    {
        broadcast(new ViewerRequestedOffer(
            $liveStream->id,
            auth()->id(),
            auth()->user()->name
        ))->toOthers();

        return response()->json([
            'message' => 'Offer request sent',
        ]);
    }
}
