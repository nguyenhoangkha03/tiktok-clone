<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'bio',
        'website',
        'instagram',
        'youtube',
        'facebook',
        'twitter',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // Liked Videos: videos mà user này đã like
    public function likedVideos()
    {
        return $this->belongsToMany(Video::class, 'likes', 'user_id', 'video_id')
                    ->withTimestamps();
    }

    // Favorited Videos: videos mà user này đã favorite
    public function favoritedVideos()
    {
        return $this->belongsToMany(Video::class, 'favorites', 'user_id', 'video_id')
                    ->withTimestamps();
    }

    // Following: người mà user này đang follow
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
                    ->withTimestamps();
    }

    // Followers: những người đang follow user này
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
                    ->withTimestamps();
    }

    // Blocks: người mà user này đã block
    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'blocks', 'blocker_id', 'blocked_id')
                    ->withTimestamps();
    }

    // Blockers: những người đã block user này
    public function blockers()
    {
        return $this->belongsToMany(User::class, 'blocks', 'blocked_id', 'blocker_id')
                    ->withTimestamps();
    }

    // Reports: reports mà user này đã tạo
    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    // Reported: reports về user này
    public function reportedReports()
    {
        return $this->hasMany(Report::class, 'reported_user_id');
    }

    // Notifications: notifications cho user này
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Helper methods
    public function isFollowing($userId)
    {
        return $this->following()->where('following_id', $userId)->exists();
    }

    public function hasLiked($videoId)
    {
        return $this->likes()->where('video_id', $videoId)->exists();
    }

    public function hasFavorited($videoId)
    {
        return $this->favorites()->where('video_id', $videoId)->exists();
    }

    public function hasBlocked($userId)
    {
        return $this->blockedUsers()->where('blocked_id', $userId)->exists();
    }

    public function hasReported($userId)
    {
        return $this->reports()->where('reported_user_id', $userId)->exists();
    }
}
