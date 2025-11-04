<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StreamOfferCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $liveStreamId;
    public $offer;
    public $streamerId;
    public $targetUserId;

    /**
     * Create a new event instance.
     */
    public function __construct($liveStreamId, $offer, $streamerId, $targetUserId)
    {
        $this->liveStreamId = $liveStreamId;
        $this->offer = $offer;
        $this->streamerId = $streamerId;
        $this->targetUserId = $targetUserId;
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
            'offer' => $this->offer,
            'streamer_id' => $this->streamerId,
            'target_user_id' => $this->targetUserId,
        ];
    }
}
