<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ViewerCountUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $liveStreamId;
    public $viewerCount;

    /**
     * Create a new event instance.
     */
    public function __construct($liveStreamId, $viewerCount)
    {
        $this->liveStreamId = $liveStreamId;
        $this->viewerCount = $viewerCount;
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
            'viewer_count' => $this->viewerCount,
        ];
    }
}
