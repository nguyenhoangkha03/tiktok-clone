<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotInterested extends Model
{
    use HasFactory;

    protected $table = 'not_interested';

    protected $fillable = [
        'user_id',
        'video_id',
    ];

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the video
     */
    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
