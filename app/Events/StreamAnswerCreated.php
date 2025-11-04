<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StreamAnswerCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $liveStreamId;
    public $answer;
    public $viewerId;
    public $targetUserId;

    /**
     * Create a new event instance.
     */
    public function __construct($liveStreamId, $answer, $viewerId, $targetUserId)
    {
        $this->liveStreamId = $liveStreamId;
        $this->answer = $answer;
        $this->viewerId = $viewerId;
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
            'answer' => $this->answer,
            'viewer_id' => $this->viewerId,
            'target_user_id' => $this->targetUserId,
        ];
    }
}
