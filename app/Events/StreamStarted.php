<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StreamStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $liveStreamId;
    public $streamerId;

    /**
     * Create a new event instance.
     */
    public function __construct($liveStreamId, $streamerId)
    {
        $this->liveStreamId = $liveStreamId;
        $this->streamerId = $streamerId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('live-stream.' . $this->liveStreamId),
        ];
    }

    /**
     * Data to broadcast
     */
    public function broadcastWith(): array
    {
        return [
            'streamer_id' => $this->streamerId,
        ];
    }
}
