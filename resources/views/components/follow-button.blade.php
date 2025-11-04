@props(['user', 'class' => ''])

@if(!auth()->check() || auth()->id() !== $user->id)
    @auth
    <div
        x-data="{
            following: {{ auth()->user()->isFollowing($user->id) ? 'true' : 'false' }},
            loading: false,
            async toggleFollow() {
                if (this.loading) return;
                this.loading = true;

                const url = this.following
                    ? '/users/{{ $user->id }}/unfollow'
                    : '/users/{{ $user->id }}/follow';
                const method = this.following ? 'DELETE' : 'POST';

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (response.ok) {
                        this.following = data.is_following;

                        // Dispatch event to update follower count
                        window.dispatchEvent(new CustomEvent('follower-count-updated', {
                            detail: {
                                userId: {{ $user->id }},
                                followerCount: data.follower_count
                            }
                        }));
                    }
                } catch (error) {
                    console.error('Error toggling follow:', error);
                } finally {
                    this.loading = false;
                }
            }
        }"
        class="{{ $class }}"
    >
        <button
            @click="toggleFollow()"
            :disabled="loading"
            class="px-5 py-2.5 rounded-md transition font-semibold disabled:opacity-50 text-sm min-w-[100px]"
            :class="following ? 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700' : 'bg-[#FE2C55] text-white hover:bg-[#FE2C55]/90'"
            x-text="loading ? 'Loading...' : (following ? 'Following' : 'Follow')"
        >
        </button>
    </div>
    @else
    <!-- Guest user: Redirect to login -->
    <a href="{{ route('login') }}" class="px-5 py-2.5 bg-[#FE2C55] text-white rounded-md hover:bg-[#FE2C55]/90 transition font-semibold text-sm text-center min-w-[100px] inline-block {{ $class }}">
        Follow
    </a>
    @endauth
@endif
