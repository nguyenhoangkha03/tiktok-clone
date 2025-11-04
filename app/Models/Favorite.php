<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'video_id',
    ];

    /**
     * Get the user that favorited the video.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the video that was favorited.
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }
}
