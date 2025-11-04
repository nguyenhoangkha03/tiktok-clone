<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveChat extends Model
{
    protected $fillable = [
        'live_stream_id',
        'user_id',
        'message',
    ];

    /**
     * Get the live stream this message belongs to
     */
    public function liveStream()
    {
        return $this->belongsTo(LiveStream::class);
    }

    /**
     * Get the user who sent this message
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
