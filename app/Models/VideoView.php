<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoView extends Model
{
    protected $fillable = [
        'video_id',
        'user_id',
        'session_id',
        'watch_time',
        'completion_rate',
        'completed',
        'viewed_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'viewed_at' => 'datetime',
        'completion_rate' => 'decimal:2',
    ];

    /**
     * Get the video that was viewed
     */
    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Get the user who viewed (if authenticated)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
