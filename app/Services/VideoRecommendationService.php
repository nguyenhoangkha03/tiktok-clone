<?php

namespace App\Services;

use App\Models\Video;
use App\Models\VideoView;
use App\Models\NotInterested;
use Illuminate\Support\Facades\DB;

class VideoRecommendationService
{
    /**
     * Get recommended videos for user (Hybrid approach: 70% personalized, 30% trending)
     *
     * @param int $perPage
     * @param int|null $userId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getRecommendedVideos($perPage = 10, $userId = null)
    {
        // Calculate how many of each type
        $personalizedCount = (int) ceil($perPage * 0.7); // 70% personalized
        $trendingCount = $perPage - $personalizedCount; // 30% trending

        $recommendedIds = collect();

        if ($userId) {
            // Get personalized recommendations
            $personalizedIds = $this->getPersonalizedVideos($userId, $personalizedCount);
            $recommendedIds = $recommendedIds->merge($personalizedIds);
        }

        // Fill remaining with trending
        $remainingCount = $perPage - $recommendedIds->count();
        if ($remainingCount > 0) {
            $trendingIds = $this->getTrendingVideos($userId, $remainingCount, $recommendedIds->toArray());
            $recommendedIds = $recommendedIds->merge($trendingIds);
        }

        // Shuffle to mix personalized and trending
        $recommendedIds = $recommendedIds->shuffle();

        // Get videos with relationships
        $videos = Video::with([
                'user',
                'likes',
                'comments.user',
                'comments.replies.user',
                'comments.likes'
            ])
            ->withCount(['likes', 'comments', 'favorites'])
            ->whereIn('id', $recommendedIds)
            ->get()
            ->sortBy(function ($video) use ($recommendedIds) {
                return $recommendedIds->search($video->id);
            });

        // Convert to paginator
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $videos,
            $videos->count(),
            $perPage,
            request()->get('page', 1),
            ['path' => request()->url()]
        );
    }

    /**
     * Get personalized video recommendations based on user history
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    protected function getPersonalizedVideos($userId, $limit)
    {
        // Get user's viewing history (creators they watch most)
        $preferredCreators = VideoView::where('video_views.user_id', $userId)
            ->select('videos.user_id', DB::raw('COUNT(*) as view_count'), DB::raw('AVG(watch_time) as avg_watch_time'))
            ->join('videos', 'video_views.video_id', '=', 'videos.id')
            ->groupBy('videos.user_id')
            ->orderByDesc('avg_watch_time')
            ->orderByDesc('view_count')
            ->limit(10)
            ->pluck('videos.user_id');

        // Get videos not watched yet from preferred creators + exclude "not interested"
        $notInterestedIds = NotInterested::where('user_id', $userId)->pluck('video_id');
        $watchedIds = VideoView::where('video_views.user_id', $userId)->pluck('video_id');

        return Video::whereIn('user_id', $preferredCreators)
            ->whereNotIn('id', $watchedIds)
            ->whereNotIn('id', $notInterestedIds)
            ->where('user_id', '!=', $userId) // Don't recommend own videos
            ->inRandomOrder()
            ->limit($limit)
            ->pluck('id');
    }

    /**
     * Get trending videos based on engagement score
     *
     * @param int|null $userId
     * @param int $limit
     * @param array $excludeIds
     * @return \Illuminate\Support\Collection
     */
    protected function getTrendingVideos($userId, $limit, $excludeIds = [])
    {
        $query = Video::select('videos.*')
            ->selectRaw('COUNT(DISTINCT likes.id) as likes_count')
            ->selectRaw('COUNT(DISTINCT comments.id) as comments_count')
            ->selectRaw('COUNT(DISTINCT favorites.id) as favorites_count')
            ->selectRaw('COALESCE(views_agg.avg_watch_time, 0) as avg_watch_time')
            ->selectRaw('
                (
                    COALESCE(videos.views, 0) * 1 +
                    COUNT(DISTINCT likes.id) * 3 +
                    COUNT(DISTINCT comments.id) * 5 +
                    COUNT(DISTINCT favorites.id) * 7 +
                    COALESCE(views_agg.avg_watch_time, 0) * 10
                ) as engagement_score
            ')
            ->leftJoin('likes', function($join) {
                $join->on('videos.id', '=', 'likes.video_id');
            })
            ->leftJoin('comments', function($join) {
                $join->on('videos.id', '=', 'comments.video_id');
            })
            ->leftJoin('favorites', 'videos.id', '=', 'favorites.video_id')
            ->leftJoin(
                DB::raw('(SELECT video_id, AVG(watch_time) as avg_watch_time FROM video_views GROUP BY video_id) as views_agg'),
                'videos.id', '=', 'views_agg.video_id'
            )
            ->groupBy('videos.id', 'videos.views', 'views_agg.avg_watch_time')
            ->where('videos.created_at', '>=', now()->subDays(30)) // Recent videos only
            ->whereNotIn('videos.id', $excludeIds);

        // Exclude own videos and "not interested"
        if ($userId) {
            $query->where('videos.user_id', '!=', $userId);

            $notInterestedIds = NotInterested::where('user_id', $userId)->pluck('video_id');
            if ($notInterestedIds->isNotEmpty()) {
                $query->whereNotIn('videos.id', $notInterestedIds);
            }
        }

        return $query
            ->orderByDesc('engagement_score')
            ->limit($limit)
            ->pluck('videos.id');
    }

    /**
     * Calculate engagement score for a video
     *
     * @param \App\Models\Video $video
     * @return float
     */
    public function calculateEngagementScore($video)
    {
        $avgWatchTime = $video->videoViews()->avg('watch_time') ?? 0;

        return (
            $video->views * 1 +
            $video->likes_count * 3 +
            $video->comments_count * 5 +
            $video->favorites_count * 7 +
            $avgWatchTime * 10
        );
    }
}
