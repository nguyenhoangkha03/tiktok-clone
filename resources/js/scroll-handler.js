// Scroll Handler for TikTok Clone - Infinite Scroll
export default function initScrollHandler() {
    let isLoading = false;
    let currentPage = 1;
    let hasMoreVideos = true;

    const feedContainer = document.querySelector('.video-feed-container');
    if (!feedContainer) {
        console.log('No feed container found');
        return;
    }

    // Detect khi user scroll gần đến cuối
    const scrollObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !isLoading && hasMoreVideos) {
                loadMoreVideos();
            }
        });
    }, {
        root: null,
        rootMargin: '200px', // Load trước 200px
        threshold: 0
    });

    // Observe phần tử cuối cùng của feed
    function observeLastVideo() {
        const videos = document.querySelectorAll('.video-container');
        if (videos.length > 0) {
            const lastVideo = videos[videos.length - 1];
            scrollObserver.observe(lastVideo);
        }
    }

    // Load thêm videos
    async function loadMoreVideos() {
        if (isLoading || !hasMoreVideos) return;

        isLoading = true;
        showLoadingIndicator();

        try {
            const currentUrl = new URL(window.location.href);
            const nextPage = currentPage + 1;

            // Tạo URL với pagination
            currentUrl.searchParams.set('page', nextPage);

            const response = await fetch(currentUrl.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to load more videos');
            }

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newVideos = doc.querySelectorAll('.video-container');

            if (newVideos.length === 0) {
                hasMoreVideos = false;
                showNoMoreVideosMessage();
            } else {
                // Append videos mới vào feed
                newVideos.forEach(video => {
                    feedContainer.appendChild(video.cloneNode(true));
                });

                currentPage = nextPage;

                // Re-initialize video player cho videos mới
                if (window.initVideoPlayer) {
                    window.initVideoPlayer();
                }

                // Observe video cuối cùng mới
                observeLastVideo();
            }
        } catch (error) {
            console.error('Error loading more videos:', error);
            showErrorMessage();
        } finally {
            isLoading = false;
            hideLoadingIndicator();
        }
    }

    // UI helpers
    function showLoadingIndicator() {
        const loader = document.createElement('div');
        loader.id = 'scroll-loader';
        loader.className = 'flex items-center justify-center py-8';
        loader.innerHTML = `
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-pink-500"></div>
        `;
        feedContainer.appendChild(loader);
    }

    function hideLoadingIndicator() {
        const loader = document.getElementById('scroll-loader');
        if (loader) {
            loader.remove();
        }
    }

    function showNoMoreVideosMessage() {
        const message = document.createElement('div');
        message.className = 'text-center py-8 text-gray-400';
        message.innerHTML = `
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p>You've seen all videos!</p>
            <p class="text-sm mt-2">Follow more creators to see more content</p>
        `;
        feedContainer.appendChild(message);
    }

    function showErrorMessage() {
        const message = document.createElement('div');
        message.className = 'text-center py-8 text-red-400';
        message.innerHTML = `
            <p>Failed to load more videos</p>
            <button onclick="location.reload()" class="mt-4 px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600">
                Retry
            </button>
        `;
        feedContainer.appendChild(message);
    }

    // Smooth scroll behavior
    function enableSmoothScroll() {
        const container = document.querySelector('.video-feed-scroll');
        if (!container) return;

        let isScrolling = false;
        let scrollTimeout;

        container.addEventListener('scroll', () => {
            isScrolling = true;

            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                isScrolling = false;

                // Snap to nearest video after scroll ends
                snapToNearestVideo(container);
            }, 150);
        });
    }

    function snapToNearestVideo(container) {
        const videos = Array.from(document.querySelectorAll('.video-container'));
        const containerRect = container.getBoundingClientRect();
        const containerCenter = containerRect.top + containerRect.height / 2;

        let nearestVideo = null;
        let minDistance = Infinity;

        videos.forEach(video => {
            const rect = video.getBoundingClientRect();
            const videoCenter = rect.top + rect.height / 2;
            const distance = Math.abs(videoCenter - containerCenter);

            if (distance < minDistance) {
                minDistance = distance;
                nearestVideo = video;
            }
        });

        if (nearestVideo && minDistance > 50) {
            nearestVideo.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    // Initialize
    observeLastVideo();
    enableSmoothScroll();

    console.log('Scroll Handler initialized');
}
