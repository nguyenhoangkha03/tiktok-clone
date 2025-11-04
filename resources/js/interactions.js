// Interactions - AJAX for Like, Comment, Follow
export default function initInteractions() {
    // AJAX Comment Submit
    initCommentSubmit();

    // AJAX Like
    initLikeAjax();

    // AJAX Follow
    initFollowAjax();

    // Double tap to like (mobile)
    initDoubleTapLike();

    // Like animation
    initLikeAnimation();

    console.log('Interactions initialized');
}

// AJAX Comment Submit
function initCommentSubmit() {
    const commentForms = document.querySelectorAll('.comment-form');

    commentForms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            const input = form.querySelector('input[name="content"]');

            // Disable submit button
            submitButton.disabled = true;
            submitButton.innerHTML = 'Posting...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    // Add new comment to list
                    addCommentToList(data.comment);

                    // Update comment count
                    updateCommentCount(data.video_id, 1);

                    // Clear input
                    input.value = '';

                    // Show success message
                    showToast('Comment posted!', 'success');
                } else {
                    showToast(data.message || 'Failed to post comment', 'error');
                }
            } catch (error) {
                console.error('Error posting comment:', error);
                showToast('Something went wrong', 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Post';
            }
        });
    });
}

function addCommentToList(comment) {
    const commentsList = document.querySelector('.comments-list');
    if (!commentsList) return;

    const emptyState = commentsList.querySelector('.empty-state');
    if (emptyState) {
        emptyState.remove();
    }

    const commentHTML = `
        <div class="flex items-start space-x-3 comment-item">
            <div class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-bold">${comment.user.name.charAt(0)}</span>
            </div>
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <a href="/profile/${comment.user.username}" class="font-semibold text-white text-sm hover:underline">
                        ${comment.user.name}
                    </a>
                    <span class="text-gray-500 text-xs">just now</span>
                </div>
                <p class="text-gray-300 text-sm mt-1">${escapeHtml(comment.content)}</p>
            </div>
        </div>
    `;

    commentsList.insertAdjacentHTML('afterbegin', commentHTML);
}

function updateCommentCount(videoId, delta) {
    const countElements = document.querySelectorAll(`[data-video-id="${videoId}"] .comment-count`);
    countElements.forEach(el => {
        const current = parseInt(el.textContent) || 0;
        el.textContent = current + delta;
    });
}

// AJAX Like/Unlike
function initLikeAjax() {
    const likeButtons = document.querySelectorAll('.ajax-like-button');

    likeButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();

            const videoId = this.dataset.videoId;
            const isLiked = this.dataset.liked === 'true';
            const url = isLiked ? `/videos/${videoId}/unlike` : `/videos/${videoId}/like`;
            const method = isLiked ? 'DELETE' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    // Toggle liked state
                    this.dataset.liked = !isLiked;

                    // Update like count
                    const countElement = this.querySelector('.like-count');
                    if (countElement) {
                        countElement.textContent = data.likes_count;
                    }

                    // Update icon color
                    const icon = this.querySelector('svg');
                    if (!isLiked) {
                        icon.classList.add('fill-pink-500', 'stroke-pink-500');
                    } else {
                        icon.classList.remove('fill-pink-500', 'stroke-pink-500');
                    }

                    // Add burst animation
                    this.classList.add('like-burst');
                    setTimeout(() => {
                        this.classList.remove('like-burst');
                    }, 600);
                } else {
                    showToast(data.message || 'Failed to like video', 'error');
                }
            } catch (error) {
                console.error('Error liking video:', error);
                showToast('Something went wrong', 'error');
            }
        });
    });
}

// AJAX Follow/Unfollow
function initFollowAjax() {
    const followButtons = document.querySelectorAll('.ajax-follow-button');

    followButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();

            const userId = this.dataset.userId;
            const isFollowing = this.dataset.following === 'true';
            const url = isFollowing ? `/users/${userId}/unfollow` : `/users/${userId}/follow`;
            const method = isFollowing ? 'DELETE' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    // Toggle following state
                    this.dataset.following = !isFollowing;

                    // Update button text/visibility
                    if (!isFollowing) {
                        this.classList.add('hidden');
                        showToast('Following!', 'success');
                    } else {
                        this.classList.remove('hidden');
                        showToast('Unfollowed', 'info');
                    }
                } else {
                    showToast(data.message || 'Failed to follow user', 'error');
                }
            } catch (error) {
                console.error('Error following user:', error);
                showToast('Something went wrong', 'error');
            }
        });
    });
}

// Double tap to like (mobile gesture)
function initDoubleTapLike() {
    const videoContainers = document.querySelectorAll('.video-container');

    videoContainers.forEach(container => {
        let lastTap = 0;
        const doubleTapDelay = 300;

        container.addEventListener('touchend', (e) => {
            const currentTime = new Date().getTime();
            const tapLength = currentTime - lastTap;

            if (tapLength < doubleTapDelay && tapLength > 0) {
                // Double tap detected
                e.preventDefault();

                const likeButton = container.querySelector('.like-button');
                if (likeButton && !likeButton.classList.contains('liked')) {
                    likeButton.click();

                    // Show heart animation
                    showHeartAnimation(e.touches[0] || e.changedTouches[0], container);
                }
            }

            lastTap = currentTime;
        });
    });
}

function showHeartAnimation(touch, container) {
    const heart = document.createElement('div');
    heart.className = 'double-tap-heart';
    heart.innerHTML = `
        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
        </svg>
    `;

    heart.style.position = 'absolute';
    heart.style.left = `${touch.clientX - 48}px`;
    heart.style.top = `${touch.clientY - 48}px`;
    heart.style.pointerEvents = 'none';
    heart.style.zIndex = '100';
    heart.style.animation = 'heartBurst 0.8s ease-out';

    container.appendChild(heart);

    setTimeout(() => {
        heart.remove();
    }, 800);
}

// Like button animation
function initLikeAnimation() {
    const likeButtons = document.querySelectorAll('.like-button');

    likeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Add burst animation
            this.classList.add('like-burst');

            setTimeout(() => {
                this.classList.remove('like-burst');
            }, 600);
        });
    });
}

// Toast notification
function showToast(message, type = 'info') {
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) {
        existingToast.remove();
    }

    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };

    const toast = document.createElement('div');
    toast.className = `toast-notification fixed bottom-20 left-1/2 transform -translate-x-1/2 ${colors[type]} text-white px-6 py-3 rounded-full shadow-lg z-50 transition-opacity duration-300`;
    toast.textContent = message;

    document.body.appendChild(toast);

    // Fade in
    setTimeout(() => {
        toast.style.opacity = '1';
    }, 10);

    // Fade out and remove
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

// Utility: Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Alpine.js data for video actions
export function videoActions(videoId, initialLiked, initialLikesCount, initialFavorited, initialFavoritesCount, initialFollowing, userId, isAuthenticated) {
    return {
        liked: initialLiked,
        likesCount: initialLikesCount,
        favorited: initialFavorited,
        favoritesCount: initialFavoritesCount,
        following: initialFollowing,
        showShareMenu: false,
        showComments: false,

        async toggleLike() {
            if (!isAuthenticated) {
                window.location.href = '/login';
                return;
            }

            const url = this.liked ? `/videos/${videoId}/unlike` : `/videos/${videoId}/like`;
            const method = this.liked ? 'DELETE' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    this.liked = !this.liked;
                    this.likesCount = data.likes_count;
                } else {
                    showToast(data.message || 'Failed to like video', 'error');
                }
            } catch (error) {
                console.error('Error liking video:', error);
                showToast('Something went wrong', 'error');
            }
        },

        async toggleFollow() {
            if (!isAuthenticated) {
                window.location.href = '/login';
                return;
            }

            const url = this.following ? `/users/${userId}/unfollow` : `/users/${userId}/follow`;
            const method = this.following ? 'DELETE' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    this.following = !this.following;
                    showToast(this.following ? 'Following!' : 'Unfollowed', this.following ? 'success' : 'info');

                    // Dispatch event to update follower count globally
                    if (data.follower_count !== undefined) {
                        window.dispatchEvent(new CustomEvent('follower-count-updated', {
                            detail: {
                                userId: userId,
                                followerCount: data.follower_count
                            }
                        }));
                    }
                } else {
                    showToast(data.message || 'Failed to follow user', 'error');
                }
            } catch (error) {
                console.error('Error following user:', error);
                showToast('Something went wrong', 'error');
            }
        },

        async toggleFavorite() {
            if (!isAuthenticated) {
                window.location.href = '/login';
                return;
            }

            const url = this.favorited ? `/videos/${videoId}/unfavorite` : `/videos/${videoId}/favorite`;
            const method = this.favorited ? 'DELETE' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    this.favorited = !this.favorited;
                    this.favoritesCount = data.favorites_count;
                    showToast(this.favorited ? 'Added to favorites!' : 'Removed from favorites', this.favorited ? 'success' : 'info');
                } else {
                    showToast(data.message || 'Failed to favorite video', 'error');
                }
            } catch (error) {
                console.error('Error favoriting video:', error);
                showToast('Something went wrong', 'error');
            }
        },

        toggleComments() {
            this.showComments = !this.showComments;
        }
    };
}

// Alpine.js data for comment section
export function commentSection(videoId, initialComments = []) {
    return {
        comments: initialComments,
        newComment: '',
        isSubmitting: false,

        async submitComment() {
            if (!this.newComment.trim() || this.isSubmitting) return;

            this.isSubmitting = true;

            try {
                const response = await fetch(`/videos/${videoId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: this.newComment
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    this.comments.unshift(data.comment);
                    this.newComment = '';
                    showToast('Comment posted!', 'success');
                } else {
                    showToast(data.message || 'Failed to post comment', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Something went wrong', 'error');
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
