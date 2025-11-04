// Video Player Logic for TikTok Clone
export default function initVideoPlayer() {
    // Observer để detect video trong viewport
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.3 // Video phải hiển thị 30% mới được coi là "in view"
    };

    const videoObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const videoContainer = entry.target;
            const videoElement = videoContainer.querySelector('video');
            const iframe = videoContainer.querySelector('iframe');

            if (entry.isIntersecting) {
                // Video vào viewport - auto play
                videoContainer.classList.add('active');
                console.log('Video in viewport:', videoContainer.dataset.videoId);

                // Auto-play video element nếu có
                if (videoElement) {
                    // Thử play với sound trước
                    videoElement.muted = false;
                    videoElement.play().then(() => {
                        console.log('Auto-play with sound succeeded');
                        // Sync Alpine state
                        const alpineData = Alpine.$data(videoContainer);
                        if (alpineData) {
                            alpineData.muted = false;
                        }
                    }).catch(err => {
                        // Nếu fail, thử lại với muted
                        console.log('Auto-play with sound prevented, trying muted:', err);
                        videoElement.muted = true;
                        videoElement.play().then(() => {
                            // Sync Alpine state
                            const alpineData = Alpine.$data(videoContainer);
                            if (alpineData) {
                                alpineData.muted = true;
                            }
                        }).catch(err2 => {
                            console.log('Auto-play prevented:', err2);
                        });
                    });
                }

                // Pause tất cả videos khác
                document.querySelectorAll('.video-container').forEach(v => {
                    if (v !== videoContainer) {
                        v.classList.remove('active');
                        const otherVideo = v.querySelector('video');
                        if (otherVideo) {
                            otherVideo.pause();
                        }
                    }
                });
            } else {
                // Video ra khỏi viewport - pause
                videoContainer.classList.remove('active');
                if (videoElement) {
                    videoElement.pause();
                }
            }
        });
    }, observerOptions);

    // Observe tất cả video containers
    const videoContainers = document.querySelectorAll('.video-container');
    videoContainers.forEach(container => {
        videoObserver.observe(container);
    });

    // Auto-play video đầu tiên ngay lập tức
    setTimeout(() => {
        const firstContainer = document.querySelector('.video-container');
        const firstVideo = firstContainer?.querySelector('video');
        if (firstVideo && firstContainer) {
            // Thử play với sound trước
            firstVideo.muted = false;
            firstVideo.play().then(() => {
                console.log('Auto-play with sound succeeded');
                // Sync Alpine state
                const alpineData = Alpine.$data(firstContainer);
                if (alpineData) {
                    alpineData.muted = false;
                }
            }).catch(err => {
                // Nếu fail, thử lại với muted
                console.log('Auto-play with sound prevented, trying muted:', err);
                firstVideo.muted = true;
                firstVideo.play().then(() => {
                    // Sync Alpine state
                    const alpineData = Alpine.$data(firstContainer);
                    if (alpineData) {
                        alpineData.muted = true;
                    }
                }).catch(err2 => {
                    console.log('Auto-play completely prevented:', err2);
                });
            });
        }
    }, 100);

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        const activeVideo = document.querySelector('.video-container.active video');
        if (!activeVideo) return;

        switch(e.key) {
            case ' ': // Space - pause/play
                e.preventDefault();
                if (activeVideo.paused) {
                    activeVideo.play();
                } else {
                    activeVideo.pause();
                }
                break;
            case 'm': // M - mute/unmute
                activeVideo.muted = !activeVideo.muted;
                break;
            case 'ArrowUp': // Up - previous video
                e.preventDefault();
                scrollToAdjacentVideo('up');
                break;
            case 'ArrowDown': // Down - next video
                e.preventDefault();
                scrollToAdjacentVideo('down');
                break;
        }
    });

    // Helper function để scroll đến video kế tiếp
    function scrollToAdjacentVideo(direction) {
        const activeContainer = document.querySelector('.video-container.active');
        if (!activeContainer) return;

        const allContainers = Array.from(document.querySelectorAll('.video-container'));
        const currentIndex = allContainers.indexOf(activeContainer);

        let targetIndex;
        if (direction === 'up') {
            targetIndex = Math.max(0, currentIndex - 1);
        } else {
            targetIndex = Math.min(allContainers.length - 1, currentIndex + 1);
        }

        if (targetIndex !== currentIndex) {
            allContainers[targetIndex].scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    // Initialize progress bar tracking
    initProgressTracking();

    // Initialize watch time tracking
    initWatchTimeTracking();

    console.log('Video Player initialized');
}

// Progress tracking for native video elements
function initProgressTracking() {
    const videoElements = document.querySelectorAll('.video-container video');

    videoElements.forEach(video => {
        video.addEventListener('timeupdate', () => {
            const container = video.closest('.video-container');
            const progressBar = container.querySelector('.video-progress-bar');

            if (progressBar) {
                const progress = (video.currentTime / video.duration) * 100;
                progressBar.style.width = `${progress}%`;
            }
        });

        // Reset progress when video ends
        video.addEventListener('ended', () => {
            const container = video.closest('.video-container');
            const progressBar = container.querySelector('.video-progress-bar');

            if (progressBar) {
                progressBar.style.width = '0%';
            }
        });
    });
}

// Volume control component (for Alpine.js)
export function volumeControl() {
    return {
        volume: 100,
        muted: false,

        toggleMute() {
            this.muted = !this.muted;
            const video = this.$el.closest('.video-container').querySelector('video');
            if (video) {
                video.muted = this.muted;
            }
        },

        setVolume(value) {
            this.volume = value;
            this.muted = value === 0;
            const video = this.$el.closest('.video-container').querySelector('video');
            if (video) {
                video.volume = value / 100;
                video.muted = this.muted;
            }
        }
    };
}

// Progress bar component (for Alpine.js)
export function progressBar() {
    return {
        progress: 0,
        duration: 0,
        currentTime: 0,

        init() {
            const video = this.$el.closest('.video-container').querySelector('video');
            if (video) {
                video.addEventListener('timeupdate', () => {
                    this.currentTime = video.currentTime;
                    this.duration = video.duration;
                    this.progress = (video.currentTime / video.duration) * 100;
                });

                video.addEventListener('loadedmetadata', () => {
                    this.duration = video.duration;
                });
            }
        },

        seek(event) {
            const video = this.$el.closest('.video-container').querySelector('video');
            if (video && this.duration) {
                const rect = event.currentTarget.getBoundingClientRect();
                const clickX = event.clientX - rect.left;
                const percentage = clickX / rect.width;
                video.currentTime = percentage * this.duration;
            }
        },

        formatTime(seconds) {
            if (!seconds || isNaN(seconds)) return '0:00';
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }
    };
}

// Watch time tracking for recommendation algorithm
function initWatchTimeTracking() {
    const videoContainers = document.querySelectorAll('.video-container');
    const watchTimeData = new Map(); // Store watch time per video

    videoContainers.forEach(container => {
        const video = container.querySelector('video');
        const videoId = container.dataset.videoId;

        if (!video || !videoId) return;

        let watchStartTime = null;
        let totalWatchTime = 0;
        let isTracking = false;

        // Start tracking when video plays
        video.addEventListener('play', () => {
            if (!isTracking) {
                watchStartTime = Date.now();
                isTracking = true;
            }
        });

        // Pause tracking when video pauses
        video.addEventListener('pause', () => {
            if (isTracking && watchStartTime) {
                totalWatchTime += (Date.now() - watchStartTime) / 1000; // Convert to seconds
                watchStartTime = null;
                isTracking = false;
            }
        });

        // Track when video leaves viewport (pause)
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting && isTracking) {
                    // Video left viewport, save watch time
                    if (watchStartTime) {
                        totalWatchTime += (Date.now() - watchStartTime) / 1000;
                        watchStartTime = null;
                        isTracking = false;
                    }

                    // Send tracking data to backend
                    if (totalWatchTime > 0) {
                        sendWatchTimeData(videoId, totalWatchTime, video.duration);
                        totalWatchTime = 0; // Reset
                    }
                }
            });
        }, { threshold: 0.1 });

        observer.observe(container);

        // Track on page unload
        window.addEventListener('beforeunload', () => {
            if (isTracking && watchStartTime) {
                totalWatchTime += (Date.now() - watchStartTime) / 1000;
            }

            if (totalWatchTime > 0) {
                // Use sendBeacon for reliable tracking on page unload
                const data = new FormData();
                data.append('watch_time', Math.floor(totalWatchTime));
                data.append('video_duration', video.duration || 0);

                navigator.sendBeacon(`/api/videos/${videoId}/track-view`, data);
            }
        });

        // Track when video ends
        video.addEventListener('ended', () => {
            if (isTracking && watchStartTime) {
                totalWatchTime += (Date.now() - watchStartTime) / 1000;
                watchStartTime = null;
                isTracking = false;
            }

            if (totalWatchTime > 0) {
                sendWatchTimeData(videoId, totalWatchTime, video.duration);
                totalWatchTime = 0;
            }
        });
    });
}

// Send watch time data to backend
async function sendWatchTimeData(videoId, watchTime, duration) {
    try {
        const response = await fetch(`/api/videos/${videoId}/track-view`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                watch_time: Math.floor(watchTime),
                video_duration: duration || 0,
            }),
        });

        if (response.ok) {
            const data = await response.json();
            console.log('Watch time tracked:', data);
        }
    } catch (error) {
        console.error('Failed to track watch time:', error);
    }
}
