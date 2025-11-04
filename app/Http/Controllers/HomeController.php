<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Services\VideoRecommendationService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $recommendationService;

    public function __construct(VideoRecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Display For You feed (personalized recommendations)
     */
    public function index()
    {
        $userId = auth()->id();
        $videos = $this->recommendationService->getRecommendedVideos(10, $userId);

        return view('home', compact('videos'));
    }

    /**
     * Display Following feed (videos from followed users)
     */
    public function following()
    {
        // Nếu chưa đăng nhập, hiển thị empty với prompt đăng nhập
        if (!auth()->check()) {
            $videos = Video::query()->paginate(0); // Empty collection
            return view('following', compact('videos'));
        }

        $followingIds = auth()->user()->following()->pluck('users.id');

        $videos = Video::with([
                'user',
                'likes',
                'comments.user',
                'comments.replies.user',
                'comments.likes'
            ])
            ->withCount(['likes', 'comments', 'favorites'])
            ->whereIn('user_id', $followingIds)
            ->latest()
            ->paginate(10);

        return view('following', compact('videos'));
    }

    /**
     * Display Friends feed (videos from mutual follows)
     */
    public function friends()
    {
        // Nếu chưa đăng nhập, hiển thị empty với prompt đăng nhập
        if (!auth()->check()) {
            $videos = Video::query()->paginate(0); // Empty collection
            return view('friends', compact('videos'));
        }

        // Get users that current user follows
        $followingIds = auth()->user()->following()->pluck('users.id');

        // Get users that follow current user back (mutual follows = friends)
        $friendIds = auth()->user()->followers()
            ->whereIn('users.id', $followingIds)
            ->pluck('users.id');

        $videos = Video::with([
                'user',
                'likes',
                'comments.user',
                'comments.replies.user',
                'comments.likes'
            ])
            ->withCount(['likes', 'comments', 'favorites'])
            ->whereIn('user_id', $friendIds)
            ->latest()
            ->paginate(10);

        return view('friends', compact('videos'));
    }

    /**
     * Display Explore page (popular/trending videos)
     */
    public function explore()
    {
        // Get trending videos based on likes and views
        $videos = Video::with(['user'])
            ->withCount(['likes', 'comments', 'favorites'])
            ->orderByDesc('views')
            ->orderByDesc('likes_count')
            ->paginate(24);

        return view('explore', compact('videos'));
    }
}
