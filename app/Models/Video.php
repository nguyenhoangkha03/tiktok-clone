<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'video_path',
        'thumbnail',
        'views',
    ];

    protected $casts = [
        'views' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function videoViews()
    {
        return $this->hasMany(VideoView::class);
    }

    // Helper methods
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function likesCount()
    {
        return $this->likes()->count();
    }

    public function commentsCount()
    {
        return $this->comments()->count();
    }

    public function isFavoritedBy($user)
    {
        if (!$user) {
            return false;
        }

        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    public function isLikedBy($user)
    {
        if (!$user) {
            return false;
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
