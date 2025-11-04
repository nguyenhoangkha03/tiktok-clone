@props(['video'])

@php
$commentsData = $video->comments
    ->filter(fn($c) => $c->parent_id === null)
    ->values()
    ->map(fn($c) => [
        'id' => $c->id,
        'content' => $c->content,
        'created_at' => $c->created_at->diffForHumans(null, true),
        'likes_count' => $c->likes->count(),
        'is_liked' => auth()->check() ? $c->isLikedBy(auth()->id()) : false,
        'replies_count' => $c->replies->count(),
        'user' => [
            'name' => $c->user->name,
            'username' => $c->user->username,
            'avatar' => $c->user->avatar
        ],
        'replies' => $c->replies->map(fn($r) => [
            'id' => $r->id,
            'content' => $r->content,
            'created_at' => $r->created_at->diffForHumans(null, true),
            'likes_count' => $r->likes->count(),
            'is_liked' => auth()->check() ? $r->isLikedBy(auth()->id()) : false,
            'user' => [
                'name' => $r->user->name,
                'username' => $r->user->username,
                'avatar' => $r->user->avatar
            ]
        ])->toArray()
    ])->toArray();
@endphp

<div
    class="video-container video-fade-in relative w-full h-screen snap-start flex items-center justify-center bg-gray-50 dark:bg-gray-900"
    data-video-id="{{ $video->id }}"
    x-data="{
        showComments: false,
        muted: false,
        isPaused: false,
        showPlayIcon: false,
        playIconTimeout: null,
        showMoreMenu: false,
        showReportModal: false,
        reportReason: '',
        reportDescription: '',
        isSubmittingReport: false
    }"
>
    <!-- Video Player Container with Action Buttons -->
    <div class="flex items-center gap-4">
        <!-- Video Player -->
        <div class="relative" style="width: 420px; max-width: 90vw;">
            <div
                class="relative w-full bg-black rounded-2xl overflow-hidden shadow-2xl"
                style="aspect-ratio: 9/16;"
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
                <!-- Video -->
                <video
                    class="w-full h-full object-cover video-player cursor-pointer"
                    data-video-id="{{ $video->id }}"
                    loop
                    playsinline
                    preload="metadata"
                    x-ref="videoElement"
                    x-bind:muted="muted"
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
                    <div x-show="!isPaused" class="w-20 h-20 rounded-full bg-black/60 backdrop-blur-sm flex items-center justify-center">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>

                    <!-- Play Icon -->
                    <div x-show="isPaused" class="w-20 h-20 rounded-full bg-black/60 backdrop-blur-sm flex items-center justify-center" style="display: none;">
                        <svg class="w-10 h-10 text-white ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>

                <!-- Volume Control (Top Left) -->
                <div
                    x-data="{ showVolume: false, volume: 100 }"
                    @mouseenter="showVolume = true"
                    @mouseleave="showVolume = false"
                    @click.stop
                    class="absolute top-4 left-4 flex items-center gap-2 z-10"
                >
                    <!-- Mute Button -->
                    <button
                        @click.stop="
                            if (muted) {
                                muted = false;
                                $refs.videoElement.muted = false;
                                $refs.videoElement.volume = volume / 100;
                            } else {
                                muted = true;
                                $refs.videoElement.muted = true;
                            }
                        "
                        class="w-10 h-10 rounded-full bg-black/40 backdrop-blur-sm flex items-center justify-center hover:bg-black/60 transition"
                    >
                        <svg x-show="!muted && volume > 50" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd"/>
                        </svg>
                        <svg x-show="!muted && volume > 0 && volume <= 50" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                            <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM12.828 11.828a4 4 0 000-5.656l1.415-1.415a6 6 0 010 8.486l-1.415-1.415z" clip-rule="evenodd"/>
                        </svg>
                        <svg x-show="muted || volume === 0" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                            <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM12.293 7.293a1 1 0 011.414 0L15 8.586l1.293-1.293a1 1 0 111.414 1.414L16.414 10l1.293 1.293a1 1 0 01-1.414 1.414L15 11.414l-1.293 1.293a1 1 0 01-1.414-1.414L13.586 10l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <!-- Volume Slider -->
                    <div
                        x-show="showVolume"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        @click.stop
                        class="bg-black/40 backdrop-blur-sm rounded-full px-3 flex items-center"
                        style="display: none; height: 40px;"
                    >
                        <input
                            type="range"
                            min="0"
                            max="100"
                            x-model="volume"
                            @input.stop="
                                $refs.videoElement.volume = volume / 100;
                                if (volume > 0 && muted) {
                                    muted = false;
                                    $refs.videoElement.muted = false;
                                } else if (volume === 0) {
                                    muted = true;
                                    $refs.videoElement.muted = true;
                                }
                            "
                            class="w-24 h-1 bg-white/30 rounded-full appearance-none cursor-pointer volume-slider"
                            style="accent-color: #FE2C55;"
                        >
                    </div>
                </div>

                <!-- More Options (Top Right) -->
                <div class="absolute top-4 right-4 z-10">
                    <button
                        @click.stop="showMoreMenu = !showMoreMenu"
                        class="w-10 h-10 rounded-full bg-black/40 backdrop-blur-sm flex items-center justify-center hover:bg-black/60 transition"
                    >
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                        </svg>
                    </button>

                    <!-- More Menu Dropdown -->
                    <div
                        x-show="showMoreMenu"
                        @click.away="showMoreMenu = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 top-12 w-48 rounded-xl shadow-2xl bg-white border border-gray-200 py-2 z-20"
                        style="display: none;"
                    >
                        <!-- Report -->
                        @auth
                        <button
                            @click.stop="showReportModal = true; showMoreMenu = false"
                            class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 transition font-medium flex items-center space-x-3 text-[14px]"
                        >
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Report</span>
                        </button>

                        <!-- Not Interested -->
                        <button
                            @click.stop="
                                showMoreMenu = false;
                                fetch('/videos/{{ $video->id }}/not-interested', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    alert(data.message);
                                    // Optionally: hide or skip this video
                                })
                                .catch(error => console.error('Error:', error));
                            "
                            class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 transition font-medium flex items-center space-x-3 text-[14px]"
                        >
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            <span>Not Interested</span>
                        </button>
                        @else
                        <a
                            href="{{ route('login') }}"
                            class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 transition font-medium flex items-center space-x-3 text-[14px]"
                        >
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Report</span>
                        </a>
                        <a
                            href="{{ route('login') }}"
                            class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 transition font-medium flex items-center space-x-3 text-[14px]"
                        >
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            <span>Not Interested</span>
                        </a>
                        @endauth

                        <div class="border-t border-gray-200 my-1"></div>

                        <!-- Download -->
                        <a
                            href="{{ asset($video->video_path) }}"
                            download
                            @click.stop="showMoreMenu = false"
                            class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 transition font-medium flex items-center space-x-3 text-[14px]"
                        >
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span>Download</span>
                        </a>

                        <!-- Copy Link -->
                        <button
                            @click.stop="
                                navigator.clipboard.writeText('{{ route('videos.show', [$video->user->username, $video]) }}');
                                showMoreMenu = false;
                            "
                            class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 transition font-medium flex items-center space-x-3 text-[14px]"
                        >
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span>Copy Link</span>
                        </button>
                    </div>
                </div>

                <!-- Video Info (Bottom Left) -->
                <div class="absolute bottom-4 left-4 right-20 pr-4" x-data="{ showFullDescription: false }">
                    <!-- Username -->
                    <a href="{{ route('profile.show', $video->user->username) }}" class="flex items-center group mb-2">
                        <span class="text-white font-bold text-[16px] drop-shadow-lg group-hover:underline">{{ '@' . $video->user->username }}</span>
                        <svg class="w-4 h-4 text-white ml-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </a>

                    <!-- Title (if exists) -->
                    @if($video->title)
                        <h3 class="text-white font-semibold text-[15px] drop-shadow-lg mb-1 line-clamp-1">
                            {{ $video->title }}
                        </h3>
                    @endif

                    <!-- Description (if exists) -->
                    @if($video->description)
                        <div class="text-white text-[14px] drop-shadow-lg">
                            <p :class="showFullDescription ? '' : 'line-clamp-2'" class="leading-relaxed">
                                {{ $video->description }}
                            </p>
                            @if(strlen($video->description) > 100)
                                <button
                                    @click.stop="showFullDescription = !showFullDescription"
                                    class="text-white/80 hover:text-white font-semibold text-[13px] mt-1"
                                    x-text="showFullDescription ? 'Show less' : 'Show more'"
                                >
                                    Show more
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons (Right Side - Outside Video Frame) -->
        <div class="flex-shrink-0" @toggle-comments.window="showComments = !showComments">
            <x-video-actions :video="$video" />
        </div>
    </div>

    <!-- Comment Sidebar -->
    <div
        x-show="showComments"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @click.away="showComments = false"
        @add-comment="$event.stopPropagation()"
        class="absolute right-0 top-0 h-full w-full md:w-96 bg-white dark:bg-gray-800 shadow-2xl z-50 flex flex-col border-l border-gray-200 dark:border-gray-700"
        style="display: none;"
    >
        <!-- Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-gray-900 dark:text-gray-100 font-bold text-[17px]">Comments</h3>
            <button @click="showComments = false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Comments Data -->
        <script type="application/json" id="comments-data-{{ $video->id }}">
            @json($commentsData)
        </script>

        <!-- Comments List -->
        <div class="flex-1 overflow-y-auto p-5 space-y-5 comments-list"
             x-data="{
                 replyingTo: null,
                 replyContent: '',
                 showReplyEmojiPicker: false,
                 emojis: ['😀', '😂', '🤣', '😊', '😍', '🥰', '😘', '😎', '🤔', '😢', '😭', '😡', '👍', '👎', '👏', '🙏', '❤️', '🔥', '✨', '🎉', '💯', '😇', '🤗', '🤩', '😜', '🥳', '😱', '🙌', '💪', '👀', '💕'],
                 comments: [],

                 init() {
                     const dataEl = document.getElementById('comments-data-{{ $video->id }}');
                     this.comments = JSON.parse(dataEl.textContent);
                     console.log('Video {{ $video->id }} comments:', this.comments.length);

                     // Listen for comment added event
                     window.addEventListener('comment-added-{{ $video->id }}', (e) => {
                         console.log('Received comment-added event for video {{ $video->id }}:', e.detail);
                         this.addNewComment(e.detail);
                     });
                 },

                 addNewComment(commentData) {
                     this.comments.unshift(commentData);
                     console.log('Comment added to video {{ $video->id }}:', commentData);
                 },

                 addReplyEmoji(emoji) {
                     this.replyContent += emoji;
                     this.showReplyEmojiPicker = false;
                 },

                 async toggleLike(commentId, isReply = false, parentIndex = null) {
                     const comment = isReply ? this.comments[parentIndex].replies.find(r => r.id === commentId) : this.comments.find(c => c.id === commentId);
                     if (!comment) return;

                     const url = comment.is_liked ? `/comments/${commentId}/unlike` : `/comments/${commentId}/like`;
                     const method = comment.is_liked ? 'DELETE' : 'POST';

                     try {
                         const response = await fetch(url, {
                             method,
                             headers: {
                                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                 'Accept': 'application/json'
                             }
                         });
                         const data = await response.json();
                         if (response.ok) {
                             comment.is_liked = !comment.is_liked;
                             comment.likes_count = data.likes_count;
                         }
                     } catch (error) {
                         console.error('Error:', error);
                     }
                 },

                 async submitReply(parentId, parentIndex) {
                     const content = this.replyContent.trim();
                     if (!content) return;

                     try {
                         const response = await fetch('/videos/{{ $video->id }}/comments', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                 'Accept': 'application/json'
                             },
                             body: JSON.stringify({
                                 content,
                                 parent_id: parentId
                             })
                         });
                         const data = await response.json();
                         if (response.ok) {
                             let avatarPath = data.comment.user.avatar;
                             if (avatarPath && avatarPath.startsWith('http')) {
                                 avatarPath = avatarPath.replace(/^https?:\/\/[^\/]+\//, '');
                             }

                             this.comments[parentIndex].replies.push({
                                 id: data.comment.id,
                                 content: data.comment.content,
                                 created_at: 'just now',
                                 likes_count: 0,
                                 is_liked: false,
                                 user: {
                                     name: data.comment.user.name,
                                     username: data.comment.user.username,
                                     avatar: avatarPath
                                 }
                             });
                             this.comments[parentIndex].replies_count++;
                             this.replyContent = '';
                             this.replyingTo = null;
                         }
                     } catch (error) {
                         console.error('Error:', error);
                     }
                 }
             }"
        >
            <template x-for="(comment, index) in comments" :key="comment.id">
                <div class="comment-item">
                    <!-- Main Comment -->
                    <div class="flex items-start space-x-3">
                        <div class="w-9 h-9 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0 ring-2 ring-gray-200">
                            <template x-if="comment.user.avatar">
                                <img :src="'/' + comment.user.avatar" :alt="comment.user.name" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!comment.user.avatar">
                                <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                                    <span class="text-sm font-bold text-white" x-text="comment.user.name.charAt(0)"></span>
                                </div>
                            </template>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-1">
                                <a :href="'/@' + comment.user.username" class="font-semibold text-gray-900 dark:text-gray-100 text-[14px] hover:underline truncate" x-text="comment.user.name"></a>
                                <span class="text-gray-500 dark:text-gray-400 text-[12px] flex-shrink-0" x-text="comment.created_at"></span>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300 text-[14px] leading-relaxed break-words mb-2" x-text="comment.content"></p>

                            <!-- Action buttons -->
                            <div class="flex items-center space-x-4 text-[13px]">
                                <button @click="toggleLike(comment.id)" class="flex items-center space-x-1 hover:text-[#FE2C55] transition" :class="comment.is_liked ? 'text-[#FE2C55]' : 'text-gray-600 dark:text-gray-400'">
                                    <i :class="comment.is_liked ? 'ri-heart-fill' : 'ri-heart-line'" class="text-[16px]" :style="comment.is_liked ? 'color: #FE2C55' : ''"></i>
                                    <span x-text="comment.likes_count || ''"></span>
                                </button>
                                <button @click="replyingTo = replyingTo === comment.id ? null : comment.id" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-medium">
                                    Reply
                                </button>
                                <span x-show="comment.replies_count > 0" class="text-gray-500 dark:text-gray-400" x-text="comment.replies_count + ' ' + (comment.replies_count === 1 ? 'reply' : 'replies')"></span>
                            </div>

                            <!-- Reply Input -->
                            <div x-show="replyingTo === comment.id" x-transition class="mt-3 relative" style="display: none;">
                                <!-- Reply Emoji Picker Popup -->
                                <div x-show="showReplyEmojiPicker"
                                     @click.away="showReplyEmojiPicker = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute bottom-12 left-0 right-0 bg-white dark:bg-gray-700 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-600 p-3 z-20"
                                     style="display: none; max-height: 220px;">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-gray-900 dark:text-gray-100 font-semibold text-[13px]">Quick Emojis</h4>
                                        <button @click="showReplyEmojiPicker = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-8 gap-1.5 overflow-y-auto" style="max-height: 160px;">
                                        <template x-for="emoji in emojis" :key="emoji">
                                            <button
                                                @click="addReplyEmoji(emoji)"
                                                class="text-xl hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg p-1.5 transition-all hover:scale-110"
                                                x-text="emoji"
                                            ></button>
                                        </template>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <!-- Emoji Button for Reply -->
                                    <button
                                        type="button"
                                        @click="showReplyEmojiPicker = !showReplyEmojiPicker"
                                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition flex-shrink-0"
                                        :class="showReplyEmojiPicker ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : ''"
                                    >
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 3 3 0 01-4.242 0 1 1 0 00-1.415 1.414 5 5 0 007.072 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>

                                    <input
                                        type="text"
                                        x-model="replyContent"
                                        placeholder="Write a reply..."
                                        class="flex-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-[#FE2C55] text-[13px]"
                                        @keydown.enter="submitReply(comment.id, index)"
                                        @keydown.space.stop
                                    />
                                    <button @click="submitReply(comment.id, index)" class="px-4 py-2 bg-[#FE2C55] text-white rounded-full hover:bg-[#FE2C55]/90 transition text-[13px] font-semibold">
                                        Reply
                                    </button>
                                </div>
                            </div>

                            <!-- Replies -->
                            <div x-show="comment.replies_count > 0" class="mt-3 space-y-3 pl-4 border-l-2 border-gray-200 dark:border-gray-700">
                                <template x-for="(reply, replyIndex) in comment.replies" :key="reply.id">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-7 h-7 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0 ring-2 ring-gray-200">
                                            <template x-if="reply.user.avatar">
                                                <img :src="'/' + reply.user.avatar" :alt="reply.user.name" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!reply.user.avatar">
                                                <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                                                    <span class="text-xs font-bold text-white" x-text="reply.user.name.charAt(0)"></span>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <a :href="'/@' + reply.user.username" class="font-semibold text-gray-900 dark:text-gray-100 text-[13px] hover:underline truncate" x-text="reply.user.name"></a>
                                                <span class="text-gray-500 dark:text-gray-400 text-[11px] flex-shrink-0" x-text="reply.created_at"></span>
                                            </div>
                                            <p class="text-gray-700 dark:text-gray-300 text-[13px] leading-relaxed break-words mb-1" x-text="reply.content"></p>
                                            <button @click="toggleLike(reply.id, true, index)" class="flex items-center space-x-1 hover:text-[#FE2C55] transition text-[12px]" :class="reply.is_liked ? 'text-[#FE2C55]' : 'text-gray-600 dark:text-gray-400'">
                                                <i :class="reply.is_liked ? 'ri-heart-fill' : 'ri-heart-line'" class="text-[14px]" :style="reply.is_liked ? 'color: #FE2C55' : ''"></i>
                                                <span x-text="reply.likes_count || ''"></span>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <div x-show="comments.length === 0" class="empty-state text-center py-16">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <p class="text-gray-700 dark:text-gray-200 font-medium text-[15px]">No comments yet</p>
                <p class="text-gray-500 dark:text-gray-400 text-[13px] mt-1">Be the first to comment!</p>
            </div>
        </div>

        <!-- Comment Form -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800"
             x-data="{
                 newComment: '',
                 isSubmitting: false,
                 showEmojiPicker: false,
                 emojis: ['😀', '😂', '🤣', '😊', '😍', '🥰', '😘', '😎', '🤔', '😢', '😭', '😡', '👍', '👎', '👏', '🙏', '❤️', '🔥', '✨', '🎉', '💯', '😇', '🤗', '🤩', '😜', '🥳', '😱', '🙌', '💪', '👀', '💕'],

                 addEmoji(emoji) {
                     this.newComment += emoji;
                     this.showEmojiPicker = false;
                     this.$refs.commentInput.focus();
                 },

                 async submitComment() {
                     const content = this.newComment.trim();
                     if (!content || this.isSubmitting) return;

                     this.isSubmitting = true;

                     try {
                         const response = await fetch('/videos/{{ $video->id }}/comments', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                 'Accept': 'application/json'
                             },
                             body: JSON.stringify({
                                 content
                             })
                         });
                         const data = await response.json();
                         if (response.ok) {
                             // Extract just the path from avatar URL if it's a full URL
                             let avatarPath = data.comment.user.avatar;
                             if (avatarPath && avatarPath.startsWith('http')) {
                                 avatarPath = avatarPath.replace(/^https?:\/\/[^\/]+\//, '');
                             }

                             // Dispatch event to add comment to list
                             const commentData = {
                                 id: data.comment.id,
                                 content: data.comment.content,
                                 created_at: 'just now',
                                 likes_count: 0,
                                 is_liked: false,
                                 replies_count: 0,
                                 user: {
                                     name: data.comment.user.name,
                                     username: data.comment.user.username,
                                     avatar: avatarPath
                                 },
                                 replies: []
                             };

                             console.log('Dispatching add-comment event for video {{ $video->id }}:', commentData);

                             // Dispatch custom event that bubbles up
                             window.dispatchEvent(new CustomEvent('comment-added-{{ $video->id }}', {
                                 detail: commentData
                             }));

                             this.newComment = '';
                         } else {
                             alert(data.message || 'Failed to post comment');
                         }
                     } catch (error) {
                         console.error('Error:', error);
                         alert('Something went wrong');
                     } finally {
                         this.isSubmitting = false;
                     }
                 }
             }">
            <!-- Emoji Picker Popup -->
            <div x-show="showEmojiPicker"
                 @click.away="showEmojiPicker = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute bottom-20 left-4 right-4 bg-white dark:bg-gray-700 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-600 p-4 z-10"
                 style="display: none; max-height: 250px;">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-gray-900 dark:text-gray-100 font-semibold text-[14px]">Quick Emojis</h4>
                    <button @click="showEmojiPicker = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-8 gap-2 overflow-y-auto" style="max-height: 180px;">
                    <template x-for="emoji in emojis" :key="emoji">
                        <button
                            @click="addEmoji(emoji)"
                            class="text-2xl hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg p-2 transition-all hover:scale-110"
                            x-text="emoji"
                        ></button>
                    </template>
                </div>
            </div>

            <div class="flex items-center space-x-2 relative">
                <!-- Emoji Button -->
                <button
                    type="button"
                    @click="showEmojiPicker = !showEmojiPicker"
                    class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition"
                    :class="showEmojiPicker ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : ''"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 3 3 0 01-4.242 0 1 1 0 00-1.415 1.414 5 5 0 007.072 0z" clip-rule="evenodd"/>
                    </svg>
                </button>

                <input
                    type="text"
                    x-model="newComment"
                    x-ref="commentInput"
                    placeholder="Add a comment..."
                    class="flex-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-full px-5 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-[#FE2C55] text-[14px] placeholder-gray-500 dark:placeholder-gray-400 transition"
                    @keydown.enter="submitComment()"
                    @keydown.space.stop
                    :disabled="isSubmitting"
                />
                <button
                    @click="submitComment()"
                    :disabled="isSubmitting"
                    class="px-5 py-2.5 bg-[#FE2C55] text-white rounded-full hover:bg-[#FE2C55]/90 transition-all duration-200 font-semibold text-[14px] hover:scale-105 disabled:opacity-50"
                    x-text="isSubmitting ? 'Posting...' : 'Post'"
                >
                    Post
                </button>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div
        x-show="showReportModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="showReportModal = false"
        class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
        style="display: none;"
    >
        <div
            @click.stop
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-2xl max-w-md w-full"
        >
            <!-- Modal Header -->
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-black text-gray-900">Report Video</h2>
                <button
                    @click="showReportModal = false"
                    class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center transition"
                >
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form @submit.prevent="
                isSubmittingReport = true;
                fetch('/videos/{{ $video->id }}/report', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        reason: reportReason,
                        description: reportDescription
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    showReportModal = false;
                    reportReason = '';
                    reportDescription = '';
                })
                .catch(error => console.error('Error:', error))
                .finally(() => isSubmittingReport = false);
            " class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Why are you reporting this video?</label>
                    <select
                        x-model="reportReason"
                        required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent"
                    >
                        <option value="">Select a reason</option>
                        <option value="spam">Spam</option>
                        <option value="inappropriate">Inappropriate content</option>
                        <option value="violence">Violence or dangerous content</option>
                        <option value="harassment">Harassment or bullying</option>
                        <option value="false_info">False information</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Additional details (optional)</label>
                    <textarea
                        x-model="reportDescription"
                        rows="3"
                        maxlength="500"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent resize-none"
                        placeholder="Provide more details..."
                    ></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button
                        type="button"
                        @click="showReportModal = false"
                        class="px-5 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="!reportReason || isSubmittingReport"
                        class="px-5 py-2.5 bg-[#FE2C55] text-white rounded-lg hover:bg-[#FE2C55]/90 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                        x-text="isSubmittingReport ? 'Submitting...' : 'Submit Report'"
                    >
                        Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
