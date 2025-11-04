<?php

use App\Http\Controllers\BlockController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\LiveChatController;
use App\Http\Controllers\LiveStreamController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotInterestedController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\VideoReportController;
use App\Http\Controllers\VideoViewController;
use App\Http\Controllers\WebRTCSignalingController;
use Illuminate\Support\Facades\Route;

// Public routes (Guest có thể xem, giống TikTok)
Route::get('/', function () {
    return redirect()->route('home');
});

Route::get('/dashboard', function () {
    return redirect()->route('home');
});

// Home & Feed - Public (guest có thể xem)
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/following', [HomeController::class, 'following'])->name('following');
Route::get('/friends', [HomeController::class, 'friends'])->name('friends');
Route::get('/explore', [HomeController::class, 'explore'])->name('explore');

// Live streaming routes (Public can view)
Route::get('/live', [LiveStreamController::class, 'index'])->name('live.index');

// Profile show - Public (TikTok-style URL)
Route::get('/@{username}', [ProfileController::class, 'show'])->name('profile.show');

// API Search - Public
Route::get('/api/videos/search', [VideoController::class, 'search'])->name('api.videos.search');

// Video view tracking - Public (guests can also track)
Route::post('/api/videos/{video}/track-view', [VideoViewController::class, 'trackView'])->name('videos.track-view');

// Authenticated routes (Phải đăng nhập)
Route::middleware(['auth'])->group(function () {
    // Video create/edit/delete - Cần auth (create phải trước {video})
    Route::get('/videos/create', [VideoController::class, 'create'])->name('videos.create');
    Route::post('/video-create', function () {
        // Validate request
        $validated = request()->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'video' => 'required|file|mimes:mp4,mov,avi,wmv,flv,webm,mpeg|max:102400', // Max 100MB
            'thumbnail' => 'nullable|string', // Base64 image from client
        ], [
            'video.required' => 'Please select a video file.',
            'video.file' => 'Please upload a valid file.',
            'video.mimes' => 'Video must be in MP4, MOV, AVI, WMV, FLV, WebM, or MPEG format.',
            'video.max' => 'Video size must not exceed 100MB.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 2000 characters.',
        ]);

        try {
            // Handle video upload
            if (request()->hasFile('video')) {
                $video = request()->file('video');

                // Generate unique filename
                $filename = time() . '_' . auth()->id() . '.' . $video->getClientOriginalExtension();

                // Create videos directory if not exists
                if (!file_exists(public_path('videos'))) {
                    mkdir(public_path('videos'), 0777, true);
                }

                // Move to public/videos directory
                $video->move(public_path('videos'), $filename);

                // Store video path
                $validated['video_path'] = 'videos/' . $filename;
            }

            // Handle thumbnail (base64 from client-side canvas)
            if (!empty($validated['thumbnail'])) {
                // Create thumbnails directory if not exists
                if (!file_exists(public_path('thumbnails'))) {
                    mkdir(public_path('thumbnails'), 0777, true);
                }

                // Extract base64 image data
                $thumbnailData = $validated['thumbnail'];
                if (preg_match('/^data:image\/(\w+);base64,/', $thumbnailData, $type)) {
                    $thumbnailData = substr($thumbnailData, strpos($thumbnailData, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif

                    $thumbnailData = base64_decode($thumbnailData);
                    $thumbnailFilename = time() . '_' . auth()->id() . '_thumb.jpg';

                    file_put_contents(public_path('thumbnails/' . $thumbnailFilename), $thumbnailData);
                    $validated['thumbnail'] = 'thumbnails/' . $thumbnailFilename;
                }
            }

            // Remove video and thumbnail from validated array as we've already handled them
            unset($validated['video']);

            // Add user_id
            $validated['user_id'] = auth()->id();

            // Create video record
            $videoRecord = \App\Models\Video::create($validated);

            return redirect()->route('videos.show', [auth()->user()->username, $videoRecord->id])
                ->with('success', 'Video uploaded successfully!');
        } catch (\Exception $e) {
            // Delete uploaded files if database insert fails
            if (isset($validated['video_path']) && file_exists(public_path($validated['video_path']))) {
                unlink(public_path($validated['video_path']));
            }
            if (isset($validated['thumbnail']) && file_exists(public_path($validated['thumbnail']))) {
                unlink(public_path($validated['thumbnail']));
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to upload video. Please try again. Error: ' . $e->getMessage());
        }
    })->name('videos.store');
    Route::get('/videos/{video}/edit', [VideoController::class, 'edit'])->name('videos.edit');
    Route::put('/videos/{video}', [VideoController::class, 'update'])->name('videos.update');
    Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy');

    // Settings - Cần auth
    Route::get('/settings', [ProfileController::class, 'edit'])->name('settings');
    Route::patch('/settings', [ProfileController::class, 'update'])->name('settings.update');
    Route::delete('/settings', [ProfileController::class, 'destroy'])->name('settings.destroy');

    // Like routes - Cần auth
    Route::post('/videos/{video}/like', [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/videos/{video}/unlike', [LikeController::class, 'destroy'])->name('likes.destroy');

    // Favorite routes - Cần auth
    Route::post('/videos/{video}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/videos/{video}/unfavorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    // Comment routes - Cần auth
    Route::post('/videos/{video}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Comment like routes - Cần auth
    Route::post('/comments/{comment}/like', [CommentLikeController::class, 'store'])->name('comment-likes.store');
    Route::delete('/comments/{comment}/unlike', [CommentLikeController::class, 'destroy'])->name('comment-likes.destroy');

    // Follow routes - Cần auth
    Route::post('/users/{user}/follow', [FollowController::class, 'store'])->name('follows.store');
    Route::delete('/users/{user}/unfollow', [FollowController::class, 'destroy'])->name('follows.destroy');

    // Message routes - Cần auth
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/@{username}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/@{username}/get', [MessageController::class, 'getMessages'])->name('messages.get');
    Route::get('/api/messages/unread-count', [MessageController::class, 'unreadCount'])->name('messages.unread');

    // Report routes - Cần auth
    Route::post('/users/{user}/report', [ReportController::class, 'store'])->name('reports.store');

    // Block routes - Cần auth
    Route::post('/users/{user}/block', [BlockController::class, 'store'])->name('blocks.store');
    Route::delete('/users/{user}/unblock', [BlockController::class, 'destroy'])->name('blocks.destroy');

    // Notification routes - Cần auth
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread');

    // Video Report routes - Cần auth
    Route::post('/videos/{video}/report', [VideoReportController::class, 'store'])->name('video-reports.store');

    // Not Interested routes - Cần auth
    Route::post('/videos/{video}/not-interested', [NotInterestedController::class, 'store'])->name('not-interested.store');

    // Live streaming authenticated routes
    Route::get('/live/create', [LiveStreamController::class, 'create'])->name('live.create');
    Route::post('/live', [LiveStreamController::class, 'store'])->name('live.store');
    Route::post('/live/{liveStream}/end', [LiveStreamController::class, 'end'])->name('live.end');

    // Live chat routes - Cần auth
    Route::get('/api/live/{liveStream}/chat', [LiveChatController::class, 'index'])->name('live.chat.index');
    Route::post('/api/live/{liveStream}/chat', [LiveChatController::class, 'store'])->name('live.chat.store');

    // WebRTC signaling routes - Cần auth
    Route::post('/api/live/{liveStream}/webrtc/start', [WebRTCSignalingController::class, 'notifyStreamStarted'])->name('webrtc.start');
    Route::post('/api/live/{liveStream}/webrtc/offer', [WebRTCSignalingController::class, 'sendOffer'])->name('webrtc.offer');
    Route::post('/api/live/{liveStream}/webrtc/answer', [WebRTCSignalingController::class, 'sendAnswer'])->name('webrtc.answer');
    Route::post('/api/live/{liveStream}/webrtc/ice-candidate', [WebRTCSignalingController::class, 'sendIceCandidate'])->name('webrtc.ice');
    Route::post('/api/live/{liveStream}/webrtc/request-offer', [WebRTCSignalingController::class, 'requestOffer'])->name('webrtc.request');
});

// Live stream show - Public (phải sau authenticated routes để tránh conflict với /live/create)
Route::get('/live/{liveStream}', [LiveStreamController::class, 'show'])->name('live.show');

// Video show - Public (phải sau authenticated routes để tránh conflict)
// New TikTok-style URL: /@username/video/{video}
Route::get('/@{username}/video/{video}', [VideoController::class, 'show'])->name('videos.show');

require __DIR__ . '/auth.php';
