<?php

namespace App\Events;

use App\Models\LiveChat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $liveChat;

    /**
     * Create a new event instance.
     */
    public function __construct(LiveChat $liveChat)
    {
        $this->liveChat = $liveChat;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('live-stream.' . $this->liveChat->live_stream_id),
        ];
    }

    /**
     * Data to broadcast
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->liveChat->id,
            'message' => $this->liveChat->message,
            'user' => [
                'id' => $this->liveChat->user->id,
                'name' => $this->liveChat->user->name,
                'username' => $this->liveChat->user->username,
                'avatar' => $this->liveChat->user->avatar,
            ],
            'created_at' => $this->liveChat->created_at->toISOString(),
        ];
    }
}
