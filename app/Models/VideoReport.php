<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_id',
        'reason',
        'description',
        'status',
    ];

    /**
     * Get the user who reported
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reported video
     */
    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
