<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ViewerRequestedOffer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $liveStreamId;
    public $viewerId;
    public $viewerName;

    /**
     * Create a new event instance.
     */
    public function __construct($liveStreamId, $viewerId, $viewerName)
    {
        $this->liveStreamId = $liveStreamId;
        $this->viewerId = $viewerId;
        $this->viewerName = $viewerName;
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
            'viewer_id' => $this->viewerId,
            'viewer_name' => $this->viewerName,
        ];
    }
}
