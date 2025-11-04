<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $video->title ?? $video->user->name . ' on TikTok' }} | {{ config('app.name', 'TikTok Clone') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('tiktok-logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Dark Mode Script (Must load before body) -->
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>

    <style>
        /* Custom Scrollbar for Sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.3);
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.5);
        }

        /* Dark mode scrollbar */
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Hide scrollbar but keep functionality */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-black text-white font-sans antialiased overflow-hidden">
    <div class="h-screen flex">
        <!-- Left: Video Section (Takes remaining space) -->
        <div class="flex-1 relative bg-black">
            <!-- Header Overlay -->
            <div class="absolute top-0 left-0 right-0 z-50 bg-gradient-to-b from-black/60 to-transparent">
                <div class="px-6 py-4 flex items-center justify-between">
                    <!-- Left: Close Button -->
                    <a href="{{ route('profile.show', $video->user->username) }}" class="w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition backdrop-blur-sm">
                        <i class="ri-close-line text-white text-2xl"></i>
                    </a>

                    <!-- Center: Search -->
                    <div class="flex-1 max-w-sm mx-4" x-data="{
                        searchQuery: '',
                        searchResults: [],
                        showResults: false,
                        loading: false,
                        searchTimeout: null,
                        async searchVideos() {
                            if (this.searchQuery.trim().length < 2) {
                                this.searchResults = [];
                                this.showResults = false;
                                return;
                            }

                            clearTimeout(this.searchTimeout);
                            this.searchTimeout = setTimeout(async () => {
                                this.loading = true;
                                try {
                                    const response = await fetch(`/api/videos/search?q=${encodeURIComponent(this.searchQuery)}&limit=5`);
                                    const data = await response.json();
                                    this.searchResults = data.videos || [];
                                    this.showResults = true;
                                } catch (error) {
                                    console.error('Search error:', error);
                                } finally {
                                    this.loading = false;
                                }
                            }, 300);
                        }
                    }" @click.away="showResults = false">
                        <div class="relative">
                            <input
                                type="text"
                                x-model="searchQuery"
                                @input="searchVideos()"
                                @focus="if (searchResults.length > 0) showResults = true"
                                placeholder="Find related content"
                                class="w-full bg-white/10 text-white placeholder-white/50 border border-white/20 rounded-full px-4 py-2 pr-10 focus:outline-none focus:border-white/40 transition backdrop-blur-sm text-sm"
                            />
                            <i class="ri-search-line absolute right-3 top-1/2 -translate-y-1/2 text-white/50 text-lg" x-show="!loading"></i>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2" x-show="loading" style="display: none;">
                                <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                            </div>

                            <!-- Search Results Dropdown -->
                            <div
                                x-show="showResults && searchResults.length > 0"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="absolute top-full mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-white/10 overflow-hidden z-50"
                                style="display: none;"
                            >
                                <template x-for="video in searchResults" :key="video.id">
                                    <a :href="`/@${video.user.username}/video/${video.id}`" class="flex items-center gap-3 p-3 hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                        <div class="w-16 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden flex-shrink-0">
                                            <template x-if="video.thumbnail">
                                                <img :src="video.thumbnail" :alt="video.title" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!video.thumbnail">
                                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#FE2C55] to-[#25F4EE]">
                                                    <i class="ri-play-circle-fill text-white text-xl"></i>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-gray-900 dark:text-white font-semibold text-sm truncate" x-text="video.title || 'Untitled'"></p>
                                            <p class="text-gray-600 dark:text-gray-400 text-xs truncate" x-text="`@${video.user.username}`"></p>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-gray-500 dark:text-gray-500 text-xs flex items-center gap-1">
                                                    <i class="ri-eye-line"></i>
                                                    <span x-text="video.views >= 1000 ? (video.views / 1000).toFixed(1) + 'K' : video.views"></span>
                                                </span>
                                                <span class="text-gray-500 dark:text-gray-500 text-xs flex items-center gap-1">
                                                    <i class="ri-heart-line"></i>
                                                    <span x-text="video.likes_count >= 1000 ? (video.likes_count / 1000).toFixed(1) + 'K' : video.likes_count"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Menu -->
                    <div class="relative" x-data="{ showMenu: false }">
                        <button
                            @click="showMenu = !showMenu"
                            class="w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition backdrop-blur-sm"
                        >
                            <i class="ri-more-fill text-white text-2xl"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            x-show="showMenu"
                            @click.away="showMenu = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 rounded-xl shadow-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 z-50"
                            style="display: none;"
                        >
                            <div class="py-2">
                                <!-- Download video -->
                                <a href="{{ asset($video->video_path) }}" download="{{ $video->title ?? 'video' }}" class="flex items-center px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                    <i class="ri-download-line text-lg mr-3"></i>
                                    <span class="text-sm font-medium">Tải về</span>
                                </a>

                                <!-- Report option -->
                                <button class="w-full flex items-center px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                    <i class="ri-flag-line text-lg mr-3"></i>
                                    <span class="text-sm font-medium">Report</span>
                                </button>

                                @auth
                                    @if($video->user_id === auth()->id())
                                        <div class="border-t border-gray-200 dark:border-white/10 my-2"></div>
                                        <!-- Owner options -->
                                        <a href="{{ route('videos.edit', $video) }}" class="flex items-center px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                            <i class="ri-edit-line text-lg mr-3"></i>
                                            <span class="text-sm font-medium">Edit video</span>
                                        </a>
                                        <form action="{{ route('videos.destroy', $video) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this video?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full flex items-center px-4 py-3 text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                                <i class="ri-delete-bin-line text-lg mr-3"></i>
                                                <span class="text-sm font-medium">Delete video</span>
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Video Player (Full Height) -->
            <div class="h-full flex items-center justify-center">
                <div class="relative h-full flex items-center justify-center max-w-[420px] w-full">
                    <div
                        class="relative w-full h-full flex items-center justify-center"
                        x-data="{
                            muted: false,
                            isPaused: false,
                            showPlayIcon: false,
                            playIconTimeout: null,
                            currentTime: 0,
                            duration: 0,
                            volume: 100,
                            isSeeking: false,
                            updateProgress() {
                                if (!this.isSeeking) {
                                    this.currentTime = this.$refs.videoElement.currentTime;
                                    this.duration = this.$refs.videoElement.duration;
                                }
                            }
                        }"
                    >
                        <!-- Video -->
                        <video
                            class="w-full h-full object-contain cursor-pointer"
                            loop
                            playsinline
                            preload="auto"
                            x-ref="videoElement"
                            autoplay
                            @timeupdate="updateProgress()"
                            @loadedmetadata="updateProgress()"
                            @click="
                                if ($refs.videoElement.paused) {
                                    $refs.videoElement.play();
                                    isPaused = false;
                                    showPlayIcon = true;
                                    clearTimeout(playIconTimeout);
                                    playIconTimeout = setTimeout(() => { showPlayIcon = false; }, 500);
                                } else {
                                    $refs.videoElement.pause();
                                    isPaused = true;
                                    showPlayIcon = true;
                                    clearTimeout(playIconTimeout);
                                    playIconTimeout = setTimeout(() => { showPlayIcon = false; }, 500);
                                }
                            "
                        >
                            <source src="{{ asset($video->video_path) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>

                        <!-- Play/Pause Icon (Center) -->
                        <div
                            x-show="showPlayIcon"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-50"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-50"
                            class="absolute inset-0 flex items-center justify-center pointer-events-none z-20"
                            style="display: none;"
                        >
                            <!-- Pause Icon -->
                            <div x-show="!isPaused" class="w-20 h-20 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center">
                                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>

                            <!-- Play Icon -->
                            <div x-show="isPaused" class="w-20 h-20 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center" style="display: none;">
                                <svg class="w-10 h-10 text-white ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Video Progress Bar (Bottom) -->
                        <div class="absolute bottom-0 left-0 right-0 px-4 pb-4 z-20">
                            <div class="flex items-center space-x-3 text-white text-sm">
                                <span class="text-xs font-medium" x-text="Math.floor(currentTime / 60).toString().padStart(2, '0') + ':' + Math.floor(currentTime % 60).toString().padStart(2, '0')">00:00</span>
                                <div
                                    class="flex-1 h-1 bg-white/20 rounded-full overflow-hidden cursor-pointer"
                                    @click.stop="
                                        isSeeking = true;
                                        const video = $refs.videoElement;
                                        if (!video || !video.duration) {
                                            isSeeking = false;
                                            return;
                                        }

                                        const rect = $el.getBoundingClientRect();
                                        const x = $event.clientX - rect.left;
                                        const percentage = Math.max(0, Math.min(1, x / rect.width));
                                        const targetTime = percentage * video.duration;

                                        video.currentTime = targetTime;

                                        setTimeout(() => {
                                            isSeeking = false;
                                            updateProgress();
                                        }, 100);
                                    "
                                >
                                    <div class="h-full bg-white rounded-full transition-all duration-100" :style="'width: ' + (duration > 0 ? (currentTime / duration * 100) : 0) + '%'"></div>
                                </div>
                                <span class="text-xs font-medium" x-text="Math.floor(duration / 60).toString().padStart(2, '0') + ':' + Math.floor(duration % 60).toString().padStart(2, '0')">00:00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Arrows (Right side, centered vertically) -->
            <div class="absolute right-6 top-1/2 -translate-y-1/2 flex flex-col space-y-4 z-50">
                <!-- Up Arrow (Previous Video) -->
                @if($prevVideo)
                    <a href="{{ route('videos.show', [$prevVideo->user->username, $prevVideo]) }}" class="w-14 h-14 rounded-full bg-black/40 backdrop-blur-sm flex items-center justify-center hover:bg-black/60 transition">
                        <i class="ri-arrow-up-s-line text-white text-3xl"></i>
                    </a>
                @else
                    <div class="w-14 h-14 rounded-full bg-black/20 backdrop-blur-sm flex items-center justify-center opacity-50 cursor-not-allowed">
                        <i class="ri-arrow-up-s-line text-white text-3xl"></i>
                    </div>
                @endif

                <!-- Down Arrow (Next Video) -->
                @if($nextVideo)
                    <a href="{{ route('videos.show', [$nextVideo->user->username, $nextVideo]) }}" class="w-14 h-14 rounded-full bg-black/40 backdrop-blur-sm flex items-center justify-center hover:bg-black/60 transition">
                        <i class="ri-arrow-down-s-line text-white text-3xl"></i>
                    </a>
                @else
                    <div class="w-14 h-14 rounded-full bg-black/20 backdrop-blur-sm flex items-center justify-center opacity-50 cursor-not-allowed">
                        <i class="ri-arrow-down-s-line text-white text-3xl"></i>
                    </div>
                @endif
            </div>

            <!-- Volume Control (Bottom Right) -->
            <div
                class="absolute right-6 bottom-3 z-50"
                x-data="{
                    showVolumeSlider: false,
                    volumeLevel: 100,
                    isMuted: false,
                    init() {
                        const video = document.querySelector('video');
                        if (video) {
                            this.volumeLevel = video.volume * 100;
                            this.isMuted = video.muted;

                            // Update state when video volume changes
                            video.addEventListener('volumechange', () => {
                                this.volumeLevel = video.volume * 100;
                                this.isMuted = video.muted;
                            });
                        }
                    }
                }"
                @mouseenter="showVolumeSlider = true"
                @mouseleave="showVolumeSlider = false"
            >
                <!-- Volume Slider (appears above button) -->
                <div
                    x-show="showVolumeSlider"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="absolute bottom-full -mb-1 left-1/2 -translate-x-1/2 bg-black/60 backdrop-blur-sm rounded-full px-3 py-4"
                    style="display: none;"
                    @click.stop
                >
                    <input
                        type="range"
                        min="0"
                        max="100"
                        x-model="volumeLevel"
                        @input="
                            const video = document.querySelector('video');
                            if (video) {
                                video.volume = volumeLevel / 100;
                                if (volumeLevel > 0 && video.muted) {
                                    video.muted = false;
                                } else if (volumeLevel == 0) {
                                    video.muted = true;
                                }
                            }
                        "
                        class="h-20 w-1 bg-white/30 rounded-full appearance-none cursor-pointer"
                        style="accent-color: #fff; writing-mode: bt-lr; -webkit-appearance: slider-vertical;"
                    >
                </div>

                <!-- Mute Button -->
                <button
                    @click="
                        const video = document.querySelector('video');
                        if (video) {
                            video.muted = !video.muted;
                        }
                    "
                    class="w-14 h-14 rounded-full bg-black/40 backdrop-blur-sm flex items-center justify-center hover:bg-black/60 transition"
                >
                    <i class="ri-volume-up-line text-white text-2xl" x-show="!isMuted && volumeLevel > 50"></i>
                    <i class="ri-volume-down-line text-white text-2xl" x-show="!isMuted && volumeLevel > 0 && volumeLevel <= 50" style="display: none;"></i>
                    <i class="ri-volume-mute-line text-white text-2xl" x-show="isMuted || volumeLevel === 0" style="display: none;"></i>
                </button>
            </div>
        </div>

        <!-- Right: Sidebar (Fixed width 450px on desktop) -->
        <div class="hidden lg:block w-[450px] flex-shrink-0 border-l border-gray-200 dark:border-white/10 bg-white dark:bg-black">
            <div class="h-screen flex flex-col">
                <!-- User Info & Actions -->
                <div class="p-6 border-b border-gray-200 dark:border-white/10">
                    <div class="flex items-start justify-between mb-4">
                        <!-- User Avatar & Name -->
                        <a href="{{ route('profile.show', $video->user->username) }}" class="flex items-center space-x-3 group">
                            <div class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0 ring-2 ring-gray-200 dark:ring-white/20">
                                @if($video->user->avatar)
                                    <img src="{{ asset($video->user->avatar) }}" alt="{{ $video->user->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                                        <span class="text-base font-bold text-white">{{ substr($video->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="text-gray-900 dark:text-white font-bold text-lg group-hover:underline">{{ $video->user->username }}</div>
                                <div class="text-gray-600 dark:text-white/60 text-sm">{{ $video->user->name }}</div>
                            </div>
                        </a>

                        <!-- Follow Button -->
                        @auth
                            @if($video->user_id !== auth()->id())
                                <x-follow-button :user="$video->user" class="ml-auto" />
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="px-6 py-2 bg-[#FE2C55] text-white rounded font-bold hover:bg-[#FE2C55]/90 transition text-sm ml-auto">
                                Follow
                            </a>
                        @endauth
                    </div>

                    <!-- Title -->
                    @if($video->title)
                        <h3 class="text-gray-900 dark:text-white font-semibold text-base mb-2">{{ $video->title }}</h3>
                    @endif

                    <!-- Description -->
                    @if($video->description)
                        <p class="text-gray-700 dark:text-white/80 text-sm leading-relaxed">{{ $video->description }}</p>
                    @endif

                    <!-- Stats & Actions -->
                    <div class="flex items-center space-x-6 mt-4 pt-4 border-t border-gray-200 dark:border-white/10">
                        <!-- Like -->
                        <div class="flex items-center space-x-2">
                            @auth
                                <button
                                    onclick="toggleLike({{ $video->id }}, {{ $video->isLikedBy(auth()->user()) ? 'true' : 'false' }})"
                                    id="like-btn-{{ $video->id }}"
                                    class="w-10 h-10 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-white/20 transition"
                                >
                                    <i class="ri-heart-{{ $video->isLikedBy(auth()->user()) ? 'fill text-[#FE2C55]' : 'line text-gray-800 dark:text-white' }} text-xl" id="like-icon-{{ $video->id }}"></i>
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="w-10 h-10 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-white/20 transition">
                                    <i class="ri-heart-line text-gray-800 dark:text-white text-xl"></i>
                                </a>
                            @endauth
                            <span class="text-gray-900 dark:text-white font-semibold" id="likes-count-{{ $video->id }}">{{ number_format($video->likes_count) }}</span>
                        </div>

                        <!-- Comment -->
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center">
                                <i class="ri-chat-3-line text-gray-800 dark:text-white text-xl"></i>
                            </div>
                            <span class="text-gray-900 dark:text-white font-semibold" id="comments-count-{{ $video->id }}">{{ number_format($video->comments_count) }}</span>
                        </div>

                        <!-- Bookmark/Favorite -->
                        <div class="flex items-center space-x-2">
                            @auth
                                <button
                                    onclick="toggleFavorite({{ $video->id }}, {{ $video->isFavoritedBy(auth()->user()) ? 'true' : 'false' }})"
                                    id="favorite-btn-{{ $video->id }}"
                                    class="w-10 h-10 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-white/20 transition"
                                >
                                    <i class="ri-bookmark-{{ $video->isFavoritedBy(auth()->user()) ? 'fill text-[#FE2C55]' : 'line text-gray-800 dark:text-white' }} text-xl" id="favorite-icon-{{ $video->id }}"></i>
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="w-10 h-10 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-white/20 transition">
                                    <i class="ri-bookmark-line text-gray-800 dark:text-white text-xl"></i>
                                </a>
                            @endauth
                            <span class="text-gray-900 dark:text-white font-semibold" id="favorites-count-{{ $video->id }}">{{ number_format($video->favorites->count()) }}</span>
                        </div>

                        <!-- Share -->
                        <div class="relative" x-data="{ showShareMenu: false }">
                            <button
                                @click="showShareMenu = !showShareMenu"
                                class="w-10 h-10 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-white/20 transition"
                            >
                                <i class="ri-share-forward-line text-gray-800 dark:text-white text-xl"></i>
                            </button>

                            <!-- Share Menu -->
                            <div
                                x-show="showShareMenu"
                                @click.away="showShareMenu = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute left-0 top-full mt-2 w-56 rounded-xl shadow-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 z-50"
                                style="display: none;"
                            >
                                <div class="py-2">
                                    <div class="px-4 py-3 border-b border-gray-200 dark:border-white/10">
                                        <p class="text-gray-900 dark:text-white font-semibold text-sm">Share to</p>
                                    </div>

                                    <!-- Share to Facebook -->
                                    <a
                                        href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('videos.show', [$video->user->username, $video])) }}"
                                        target="_blank"
                                        class="flex items-center px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition"
                                    >
                                        <i class="ri-facebook-fill text-lg mr-3 text-[#1877F2]"></i>
                                        <span class="text-sm font-medium">Share Facebook</span>
                                    </a>

                                    <!-- Share to X -->
                                    <a
                                        href="https://twitter.com/intent/tweet?url={{ urlencode(route('videos.show', [$video->user->username, $video])) }}&text={{ urlencode($video->title ?? 'Check out this video') }}"
                                        target="_blank"
                                        class="flex items-center px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition"
                                    >
                                        <i class="ri-twitter-x-fill text-lg mr-3"></i>
                                        <span class="text-sm font-medium">X</span>
                                    </a>

                                    <!-- Share to Reddit -->
                                    <a
                                        href="https://www.reddit.com/submit?url={{ urlencode(route('videos.show', [$video->user->username, $video])) }}&title={{ urlencode($video->title ?? 'Check out this video') }}"
                                        target="_blank"
                                        class="flex items-center px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition"
                                    >
                                        <i class="ri-reddit-fill text-lg mr-3 text-[#FF4500]"></i>
                                        <span class="text-sm font-medium">Share Reddit</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Copy Link -->
                    <div class="mt-4 flex items-center space-x-2 border border-gray-200 dark:border-white/10 rounded-lg px-3 py-2 bg-gray-100 dark:bg-white/5">
                        <input
                            type="text"
                            value="{{ route('videos.show', [$video->user->username, $video]) }}"
                            readonly
                            class="flex-1 bg-transparent text-gray-600 dark:text-white/60 text-sm focus:outline-none border-0 overflow-hidden text-ellipsis whitespace-nowrap"
                        />
                        <button
                            onclick="navigator.clipboard.writeText('{{ route('videos.show', [$video->user->username, $video]) }}'); this.querySelector('span').textContent = 'Copied!'; setTimeout(() => this.querySelector('span').textContent = 'Copy link', 2000)"
                            class="text-gray-700 dark:text-white/80 hover:text-gray-900 dark:hover:text-white font-semibold text-sm whitespace-nowrap"
                        >
                            <span>Copy link</span>
                        </button>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200 dark:border-white/10 flex-shrink-0" x-data="{ activeTab: 'comments' }">
                    <div class="flex">
                        <button
                            @click="activeTab = 'comments'"
                            :class="activeTab === 'comments' ? 'border-gray-900 dark:border-white text-gray-900 dark:text-white' : 'border-transparent text-gray-600 dark:text-white/60 hover:text-gray-900 dark:hover:text-white'"
                            class="flex-1 px-4 py-3 font-semibold border-b-2 transition"
                            id="comments-tab-{{ $video->id }}"
                        >
                            Comments (<span id="comments-tab-count-{{ $video->id }}">{{ $video->comments_count }}</span>)
                        </button>
                        <button
                            @click="activeTab = 'creator'"
                            :class="activeTab === 'creator' ? 'border-gray-900 dark:border-white text-gray-900 dark:text-white' : 'border-transparent text-gray-600 dark:text-white/60 hover:text-gray-900 dark:hover:text-white'"
                            class="flex-1 px-4 py-3 font-semibold border-b-2 transition"
                        >
                            Creator videos
                        </button>
                    </div>

                    <!-- Comments Tab -->
                    <div x-show="activeTab === 'comments'" style="height: calc(100vh - 400px);">
                        <x-video-detail-comments :video="$video" />
                    </div>

                    <!-- Creator Videos Tab -->
                    <div x-show="activeTab === 'creator'" style="display: none; height: calc(100vh - 400px);">
                        <div class="overflow-y-auto h-full p-4 custom-scrollbar">
                            @if($creatorVideos->count() > 0)
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($creatorVideos as $creatorVideo)
                                        <a href="{{ route('videos.show', [$creatorVideo->user->username, $creatorVideo]) }}" class="group relative aspect-[9/16] bg-white/5 rounded-lg overflow-hidden hover:ring-2 hover:ring-white/20 transition">
                                            @if($creatorVideo->thumbnail)
                                                <img src="{{ asset($creatorVideo->thumbnail) }}" alt="{{ $creatorVideo->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#FE2C55] to-[#25F4EE]">
                                                    <i class="ri-play-circle-fill text-white text-3xl opacity-60"></i>
                                                </div>
                                            @endif

                                            <!-- Stats Overlay -->
                                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-2">
                                                <div class="flex items-center justify-between text-white text-xs">
                                                    <div class="flex items-center space-x-1">
                                                        <i class="ri-eye-line"></i>
                                                        <span>{{ number_format($creatorVideo->views) }}</span>
                                                    </div>
                                                    <div class="flex items-center space-x-1">
                                                        <i class="ri-heart-fill"></i>
                                                        <span>{{ number_format($creatorVideo->likes_count) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-20">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center">
                                        <i class="ri-video-line text-gray-400 dark:text-white/30 text-3xl"></i>
                                    </div>
                                    <p class="text-gray-700 dark:text-white/60 font-medium">No other videos</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Comments Section (Below video on mobile) -->
    <div class="lg:hidden bg-white dark:bg-gray-800 px-4 py-6 absolute bottom-0 left-0 right-0 max-h-[50vh] overflow-y-auto">
        <h3 class="text-gray-900 dark:text-white font-bold text-lg mb-4">Comments (<span id="comments-mobile-count-{{ $video->id }}">{{ $video->comments_count }}</span>)</h3>

        <div class="space-y-4">
            @forelse($video->comments as $comment)
                <div class="flex items-start space-x-3">
                    <a href="{{ route('profile.show', $comment->user->username) }}" class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0">
                        @if($comment->user->avatar)
                            <img src="{{ asset($comment->user->avatar) }}" alt="{{ $comment->user->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                                <span class="text-sm font-bold text-white">{{ substr($comment->user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </a>
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <a href="{{ route('profile.show', $comment->user->username) }}" class="font-semibold text-gray-900 dark:text-white text-sm hover:underline">
                                {{ $comment->user->name }}
                            </a>
                            <span class="text-gray-500 dark:text-gray-400 text-xs">{{ $comment->created_at->diffForHumans(null, true) }}</span>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 text-sm">{{ $comment->content }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">No comments yet</p>
            @endforelse
        </div>

        <!-- Mobile Comment Form -->
        @auth
        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <form action="{{ route('comments.store', $video) }}" method="POST" class="flex items-center space-x-2">
                @csrf
                <input
                    type="text"
                    name="content"
                    placeholder="Add comment..."
                    class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border-0 rounded-full px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] text-sm"
                    required
                />
                <button
                    type="submit"
                    class="px-5 py-2.5 bg-[#FE2C55] text-white rounded-full hover:bg-[#FE2C55]/90 transition font-semibold text-sm"
                >
                    Post
                </button>
            </form>
        </div>
        @else
        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 text-center">
            <a href="{{ route('login') }}" class="text-[#FE2C55] font-semibold hover:underline">Login to comment</a>
        </div>
        @endauth
    </div>

    <script>
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Toggle Like
        let likeStates = {};
        async function toggleLike(videoId, isLiked) {
            const likeBtn = document.getElementById(`like-btn-${videoId}`);
            const likeIcon = document.getElementById(`like-icon-${videoId}`);
            const likesCount = document.getElementById(`likes-count-${videoId}`);

            // Prevent double clicks
            if (likeStates[videoId]) return;
            likeStates[videoId] = true;

            try {
                const url = isLiked
                    ? `/videos/${videoId}/unlike`
                    : `/videos/${videoId}/like`;

                const method = isLiked ? 'DELETE' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    // Update UI
                    if (isLiked) {
                        likeIcon.className = 'ri-heart-line text-gray-800 dark:text-white text-xl';
                        likeBtn.onclick = () => toggleLike(videoId, false);
                    } else {
                        likeIcon.className = 'ri-heart-fill text-[#FE2C55] text-xl';
                        likeBtn.onclick = () => toggleLike(videoId, true);
                    }

                    // Update count
                    likesCount.textContent = data.likes_count >= 1000
                        ? (data.likes_count / 1000).toFixed(1) + 'K'
                        : data.likes_count;
                }
            } catch (error) {
                console.error('Error toggling like:', error);
            } finally {
                likeStates[videoId] = false;
            }
        }

        // Toggle Favorite
        let favoriteStates = {};
        async function toggleFavorite(videoId, isFavorited) {
            const favoriteBtn = document.getElementById(`favorite-btn-${videoId}`);
            const favoriteIcon = document.getElementById(`favorite-icon-${videoId}`);
            const favoritesCount = document.getElementById(`favorites-count-${videoId}`);

            // Prevent double clicks
            if (favoriteStates[videoId]) return;
            favoriteStates[videoId] = true;

            try {
                const url = isFavorited
                    ? `/videos/${videoId}/unfavorite`
                    : `/videos/${videoId}/favorite`;

                const method = isFavorited ? 'DELETE' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    // Update UI
                    if (isFavorited) {
                        favoriteIcon.className = 'ri-bookmark-line text-gray-800 dark:text-white text-xl';
                        favoriteBtn.onclick = () => toggleFavorite(videoId, false);
                    } else {
                        favoriteIcon.className = 'ri-bookmark-fill text-[#FE2C55] text-xl';
                        favoriteBtn.onclick = () => toggleFavorite(videoId, true);
                    }

                    // Update count
                    favoritesCount.textContent = data.favorites_count >= 1000
                        ? (data.favorites_count / 1000).toFixed(1) + 'K'
                        : data.favorites_count;
                }
            } catch (error) {
                console.error('Error toggling favorite:', error);
            } finally {
                favoriteStates[videoId] = false;
            }
        }

        // Submit Comment
        async function submitComment(event, videoId) {
            event.preventDefault();

            const form = document.getElementById(`comment-form-${videoId}`);
            const input = document.getElementById(`comment-input-${videoId}`);
            const content = input.value.trim();

            if (!content) return;

            try {
                const response = await fetch(`/videos/${videoId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content })
                });

                const data = await response.json();

                if (response.ok) {
                    // Clear input
                    input.value = '';

                    // Hide "No comments" message if exists
                    const noCommentsMsg = document.getElementById(`no-comments-${videoId}`);
                    if (noCommentsMsg) {
                        noCommentsMsg.remove();
                    }

                    // Add new comment to the list
                    const commentsList = document.getElementById(`comments-list-${videoId}`);
                    const newCommentHTML = createCommentHTML(data.comment);

                    // Insert at the beginning of the list
                    commentsList.insertAdjacentHTML('afterbegin', newCommentHTML);

                    // Update comments count in tab
                    const commentsTab = commentsList.closest('[x-data]');
                    if (commentsTab) {
                        const tabButton = document.querySelector('button[\\@click="activeTab = \'comments\'"]');
                        if (tabButton) {
                            const currentCount = parseInt(tabButton.textContent.match(/\\d+/)[0]);
                            tabButton.textContent = `Comments (${currentCount + 1})`;
                        }
                    }

                    // Scroll to top to show new comment
                    commentsList.scrollTop = 0;
                }
            } catch (error) {
                console.error('Error submitting comment:', error);
            }
        }

        // Create comment HTML
        function createCommentHTML(comment) {
            const isDark = document.documentElement.classList.contains('dark');
            const timeAgo = 'just now';

            // Get user avatar or initials
            let avatarHTML = '';
            if (comment.user.avatar) {
                avatarHTML = `<img src="${comment.user.avatar}" alt="${comment.user.name}" class="w-full h-full object-cover">`;
            } else {
                const initial = comment.user.name.charAt(0).toUpperCase();
                avatarHTML = `
                    <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                        <span class="text-sm font-bold text-white">${initial}</span>
                    </div>
                `;
            }

            return `
                <div class="flex items-start space-x-3">
                    <a href="/@${comment.user.username}" class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0 ring-2 ring-white/10">
                        ${avatarHTML}
                    </a>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-1">
                            <a href="/@${comment.user.username}" class="font-semibold text-gray-900 dark:text-white text-sm hover:underline truncate">
                                ${comment.user.name}
                            </a>
                            <span class="text-gray-500 dark:text-white/40 text-xs flex-shrink-0">${timeAgo}</span>
                        </div>
                        <p class="text-gray-700 dark:text-white/80 text-sm leading-relaxed break-words">${escapeHtml(comment.content)}</p>
                    </div>
                </div>
            `;
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Show Embed Code
        function showEmbedCode() {
            const videoUrl = '{{ route("videos.show", [$video->user->username, $video]) }}';
            const embedCode = `<iframe width="560" height="315" src="${videoUrl}" frameborder="0" allowfullscreen></iframe>`;

            // Create a prompt-like dialog
            const userResponse = prompt('Copy this embed code:', embedCode);

            // If user clicked OK, copy to clipboard
            if (userResponse !== null) {
                navigator.clipboard.writeText(embedCode).then(() => {
                    alert('Embed code copied to clipboard!');
                });
            }
        }
    </script>
</body>
</html>
