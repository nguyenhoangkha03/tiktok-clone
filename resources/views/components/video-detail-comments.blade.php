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
            'id' => $c->user->id,
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
                'id' => $r->user->id,
                'name' => $r->user->name,
                'username' => $r->user->username,
                'avatar' => $r->user->avatar
            ]
        ])->toArray()
    ])->toArray();
@endphp

<!-- Comments Data -->
<script type="application/json" id="comments-data-{{ $video->id }}">
    @json($commentsData)
</script>

<script>
window.videoComments_{{ $video->id }} = {
    emojis: ['😀', '😂', '🤣', '😊', '😍', '🥰', '😘', '😎', '🤔', '😢', '😭', '😡', '👍', '👎', '👏', '🙏', '❤️', '🔥', '✨', '🎉', '💯', '😇', '🤗', '🤩', '😜', '🥳', '😱', '🙌', '💪', '👀', '💕']
};
</script>

<div
    x-data="{
        comments: [],
        replyingTo: null,
        replyContent: '',
        showReplyEmojiPicker: false,
        newComment: '',
        isSubmitting: false,
        showEmojiPicker: false,
        emojis: [],

        init() {
            this.comments = JSON.parse(document.getElementById('comments-data-{{ $video->id }}').textContent);
            this.emojis = window.videoComments_{{ $video->id }}.emojis;
        },

        toggleLike(commentId, isReply = false, parentIndex = null) {
            const comment = isReply
                ? this.comments[parentIndex].replies.find(r => r.id === commentId)
                : this.comments.find(c => c.id === commentId);
            if (!comment) return;

            const url = comment.is_liked ? `/comments/${commentId}/unlike` : `/comments/${commentId}/like`;
            const method = comment.is_liked ? 'DELETE' : 'POST';

            fetch(url, {
                method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(data => {
                if (data.likes_count !== undefined) {
                    comment.is_liked = !comment.is_liked;
                    comment.likes_count = data.likes_count;
                }
            });
        },

        async submitReply(commentId, commentIndex) {
            const content = this.replyContent.trim();
            if (!content) return;

            const response = await fetch('/videos/{{ $video->id }}/comments', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content, parent_id: commentId })
            });

            const data = await response.json();
            if (response.ok) {
                let avatarPath = data.comment.user.avatar;
                if (avatarPath && avatarPath.startsWith('http')) {
                    const url = new URL(avatarPath);
                    avatarPath = url.pathname.substring(1);
                }

                this.comments[commentIndex].replies.push({
                    id: data.comment.id,
                    content: data.comment.content,
                    created_at: 'just now',
                    likes_count: 0,
                    is_liked: false,
                    user: {
                        id: data.comment.user.id,
                        name: data.comment.user.name,
                        username: data.comment.user.username,
                        avatar: avatarPath
                    }
                });
                this.comments[commentIndex].replies_count++;
                this.replyContent = '';
                this.replyingTo = null;
            }
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
                    body: JSON.stringify({ content })
                });

                const data = await response.json();
                if (response.ok) {
                    let avatarPath = data.comment.user.avatar;
                    if (avatarPath && avatarPath.startsWith('http')) {
                        const url = new URL(avatarPath);
                        avatarPath = url.pathname.substring(1);
                    }

                    this.comments.unshift({
                        id: data.comment.id,
                        content: data.comment.content,
                        created_at: 'just now',
                        likes_count: 0,
                        is_liked: false,
                        replies_count: 0,
                        user: {
                            id: data.comment.user.id,
                            name: data.comment.user.name,
                            username: data.comment.user.username,
                            avatar: avatarPath
                        },
                        replies: []
                    });

                    // Update comment count in tab (desktop)
                    const tabCount = document.querySelector('#comments-tab-count-{{ $video->id }}');
                    if (tabCount) {
                        const current = parseInt(tabCount.textContent.replace(/,/g, ''));
                        tabCount.textContent = current + 1;
                    }

                    // Update comment count in stats section (icon area)
                    const commentCount = document.querySelector('#comments-count-{{ $video->id }}');
                    if (commentCount) {
                        const current = parseInt(commentCount.textContent.replace(/,/g, ''));
                        commentCount.textContent = (current + 1).toLocaleString();
                    }

                    // Update comment count in mobile section
                    const mobileCount = document.querySelector('#comments-mobile-count-{{ $video->id }}');
                    if (mobileCount) {
                        const current = parseInt(mobileCount.textContent.replace(/,/g, ''));
                        mobileCount.textContent = current + 1;
                    }

                    this.newComment = '';
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.isSubmitting = false;
            }
        }
    }"
    class="flex flex-col h-full"
>
    <!-- Comments List -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
        <template x-for="(comment, index) in comments" :key="comment.id">
            <div>
                <!-- Main Comment -->
                <div class="flex items-start space-x-3">
                    <a :href="'/@' + comment.user.username" class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0 ring-2 ring-white/10">
                        <template x-if="comment.user.avatar">
                            <img :src="'/' + comment.user.avatar" :alt="comment.user.name" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!comment.user.avatar">
                            <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                                <span class="text-sm font-bold text-white" x-text="comment.user.name.charAt(0)"></span>
                            </div>
                        </template>
                    </a>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-1">
                            <a :href="'/@' + comment.user.username" class="font-semibold text-gray-900 dark:text-white text-sm hover:underline truncate" x-text="comment.user.name"></a>
                            <span class="text-gray-500 dark:text-white/40 text-xs flex-shrink-0" x-text="comment.created_at"></span>
                        </div>
                        <p class="text-gray-700 dark:text-white/80 text-sm leading-relaxed break-words mb-2" x-text="comment.content"></p>

                        <!-- Action buttons -->
                        <div class="flex items-center space-x-4 text-xs">
                            <button
                                @click="toggleLike(comment.id)"
                                class="flex items-center space-x-1 hover:text-[#FE2C55] transition"
                                :class="comment.is_liked ? 'text-[#FE2C55]' : 'text-gray-600 dark:text-white/60'"
                            >
                                <i :class="comment.is_liked ? 'ri-heart-fill' : 'ri-heart-line'" class="text-sm" :style="comment.is_liked ? 'color: #FE2C55' : ''"></i>
                                <span x-text="comment.likes_count || ''"></span>
                            </button>
                            @auth
                            <button @click="replyingTo = replyingTo === comment.id ? null : comment.id" class="text-gray-600 dark:text-white/60 hover:text-gray-900 dark:hover:text-white font-medium">
                                Reply
                            </button>
                            @endauth
                            <span x-show="comment.replies_count > 0" class="text-gray-500 dark:text-white/40" x-text="comment.replies_count + ' ' + (comment.replies_count === 1 ? 'reply' : 'replies')"></span>
                        </div>

                        <!-- Reply Input -->
                        @auth
                        <div x-show="replyingTo === comment.id" x-transition class="mt-3 relative" style="display: none;">
                            <!-- Reply Emoji Picker Popup -->
                            <div x-show="showReplyEmojiPicker"
                                 @click.away="showReplyEmojiPicker = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute bottom-12 left-0 right-0 bg-white dark:bg-gray-700 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-600 p-3 z-20"
                                 style="display: none; max-height: 220px;">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-gray-900 dark:text-gray-100 font-semibold text-xs">Quick Emojis</h4>
                                    <button @click="showReplyEmojiPicker = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-8 gap-1.5 overflow-y-auto" style="max-height: 160px;">
                                    <template x-for="emoji in emojis" :key="emoji">
                                        <button
                                            @click="replyContent += emoji; showReplyEmojiPicker = false;"
                                            class="text-lg hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg p-1.5 transition-all hover:scale-110"
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
                                    class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition flex-shrink-0"
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
                                    class="flex-1 bg-gray-100 dark:bg-white/5 text-gray-900 dark:text-white border border-gray-300 dark:border-white/10 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-[#FE2C55] text-xs"
                                    @keydown.enter="submitReply(comment.id, index)"
                                    @keydown.space.stop
                                />
                                <button @click="submitReply(comment.id, index)" class="px-3 py-1.5 bg-[#FE2C55] text-white rounded-lg hover:bg-[#FE2C55]/90 transition text-xs font-semibold">
                                    Reply
                                </button>
                            </div>
                        </div>
                        @endauth

                        <!-- Replies -->
                        <div x-show="comment.replies_count > 0" class="mt-3 space-y-3 pl-4 border-l-2 border-gray-200 dark:border-white/10">
                            <template x-for="reply in comment.replies" :key="reply.id">
                                <div class="flex items-start space-x-2">
                                    <a :href="'/@' + reply.user.username" class="w-7 h-7 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0 ring-2 ring-white/10">
                                        <template x-if="reply.user.avatar">
                                            <img :src="'/' + reply.user.avatar" :alt="reply.user.name" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!reply.user.avatar">
                                            <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                                                <span class="text-xs font-bold text-white" x-text="reply.user.name.charAt(0)"></span>
                                            </div>
                                        </template>
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <a :href="'/@' + reply.user.username" class="font-semibold text-gray-900 dark:text-white text-xs hover:underline truncate" x-text="reply.user.name"></a>
                                            <span class="text-gray-500 dark:text-white/40 text-[10px] flex-shrink-0" x-text="reply.created_at"></span>
                                        </div>
                                        <p class="text-gray-700 dark:text-white/80 text-xs leading-relaxed break-words mb-1" x-text="reply.content"></p>
                                        <button
                                            @click="toggleLike(reply.id, true, index)"
                                            class="flex items-center space-x-1 hover:text-[#FE2C55] transition text-[10px]"
                                            :class="reply.is_liked ? 'text-[#FE2C55]' : 'text-gray-600 dark:text-white/60'"
                                        >
                                            <i :class="reply.is_liked ? 'ri-heart-fill' : 'ri-heart-line'" class="text-xs" :style="reply.is_liked ? 'color: #FE2C55' : ''"></i>
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
        <div x-show="comments.length === 0" class="text-center py-20">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center">
                <i class="ri-chat-3-line text-gray-400 dark:text-white/30 text-3xl"></i>
            </div>
            <p class="text-gray-700 dark:text-white/60 font-medium">No comments yet</p>
            <p class="text-gray-500 dark:text-white/40 text-sm mt-1">Be the first to comment!</p>
        </div>
    </div>

    <!-- Comment Form -->
    @auth
    <div class="p-4 border-t border-gray-200 dark:border-white/10 flex-shrink-0">
        <!-- Emoji Picker Popup -->
        <div x-show="showEmojiPicker"
             @click.away="showEmojiPicker = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="absolute bottom-20 left-4 right-4 bg-white dark:bg-gray-700 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-600 p-4 z-10"
             style="display: none; max-height: 250px;">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-gray-900 dark:text-gray-100 font-semibold text-sm">Quick Emojis</h4>
                <button @click="showEmojiPicker = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-8 gap-2 overflow-y-auto" style="max-height: 180px;">
                <template x-for="emoji in emojis" :key="emoji">
                    <button
                        @click="newComment += emoji; showEmojiPicker = false; $refs.commentInput.focus();"
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
                placeholder="Add comment..."
                class="flex-1 bg-gray-100 dark:bg-white/5 text-gray-900 dark:text-white border border-gray-300 dark:border-white/10 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-[#FE2C55] text-sm placeholder-gray-500 dark:placeholder-white/40 transition"
                @keydown.enter="submitComment()"
                @keydown.space.stop
                :disabled="isSubmitting"
            />
            <button
                @click="submitComment()"
                :disabled="isSubmitting"
                class="px-5 py-2.5 bg-[#FE2C55] text-white rounded-lg hover:bg-[#FE2C55]/90 transition-all duration-200 font-semibold text-sm"
                x-text="isSubmitting ? 'Posting...' : 'Post'"
            >
                Post
            </button>
        </div>
    </div>
    @else
    <div class="p-4 border-t border-gray-200 dark:border-white/10 text-center flex-shrink-0">
        <a href="{{ route('login') }}" class="text-[#FE2C55] font-semibold hover:underline">Login to comment</a>
    </div>
    @endauth
</div>
