<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveStream extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'thumbnail',
        'status',
        'viewers_count',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Get the user who is streaming
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if stream is currently live
     */
    public function isLive()
    {
        return $this->status === 'live';
    }

    /**
     * Scope to get only live streams
     */
    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }
}
