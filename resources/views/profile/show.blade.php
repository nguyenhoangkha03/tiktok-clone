<x-layouts.tiktok>
    <div class="min-h-screen bg-white dark:bg-gray-900 pb-20"
         x-data="{
             followerCount: {{ $followersCount }},
             userId: {{ $user->id }},
             showEditModal: false,
             showFollowersModal: false,
             showFollowingModal: false,
             showReportModal: false,
             isBlocked: {{ auth()->check() && auth()->user()->hasBlocked($user->id) ? 'true' : 'false' }},
             blockLoading: false,
             async toggleBlock() {
                 console.log('toggleBlock called, isBlocked:', this.isBlocked);
                 if (this.blockLoading) return;
                 this.blockLoading = true;

                 const url = this.isBlocked
                     ? '/users/{{ $user->id }}/unblock'
                     : '/users/{{ $user->id }}/block';
                 const method = this.isBlocked ? 'DELETE' : 'POST';

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
                         this.isBlocked = data.is_blocked;

                         // Show success message
                         const message = this.isBlocked ? 'User blocked successfully' : 'User unblocked successfully';
                         alert(message);

                         // Reload page to update UI
                         setTimeout(() => window.location.reload(), 1000);
                     } else {
                         alert(data.message || 'An error occurred');
                     }
                 } catch (error) {
                     console.error('Error toggling block:', error);
                     alert('An error occurred. Please try again.');
                 } finally {
                     this.blockLoading = false;
                 }
             }
         }"
         @follower-count-updated.window="if ($event.detail.userId === userId) followerCount = $event.detail.followerCount"
         @open-report-modal.window="console.log('Event received: open-report-modal'); showReportModal = true"
         @toggle-block.window="console.log('Event received: toggle-block'); toggleBlock()"
        <!-- Profile Header -->
        <div class="max-w-4xl mx-auto px-4 py-8">
            <div class="flex items-start space-x-6">
                <!-- Avatar -->
                <div class="w-28 h-28 rounded-full flex items-center justify-center text-4xl font-bold flex-shrink-0 shadow-lg ring-2 ring-gray-100 overflow-hidden"
                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    @if($user->avatar)
                        <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-white">{{ substr($user->name, 0, 1) }}</span>
                    @endif
                </div>

                <!-- User Info -->
                <div class="flex-1">
                    <h1 class="text-3xl font-black text-gray-900 dark:text-gray-100">{{ $user->name }}</h1>
                    <p class="text-gray-600 dark:text-gray-400 text-lg mt-1">{{ '@' . $user->username }}</p>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2 mt-4" x-data="{ showShareMenu: false }">
                        @if(auth()->id() === $user->id)
                            <!-- Own Profile: Edit Profile -->
                            <button
                                @click="showEditModal = true"
                                class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition font-semibold text-sm">
                                Edit profile
                            </button>
                        @else
                            <!-- Other's Profile: Follow Button -->
                            <x-follow-button :user="$user" />

                            <!-- Message Button -->
                            @auth
                                <a href="{{ route('messages.show', $user->username) }}" class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition font-semibold text-sm min-w-[100px] text-center">
                                    Message
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition font-semibold text-sm min-w-[100px] text-center">
                                    Message
                                </a>
                            @endauth
                        @endif

                        <!-- Share Button -->
                        <div class="relative">
                            <button @click="showShareMenu = !showShareMenu" class="w-10 h-10 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition flex items-center justify-center">
                                <i class="ri-share-forward-line text-gray-700 dark:text-gray-200 text-lg"></i>
                            </button>

                            <!-- Share Dropdown -->
                            <div
                                x-show="showShareMenu"
                                @click.away="showShareMenu = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="absolute right-0 mt-2 w-56 rounded-xl shadow-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 z-50"
                                style="display: none;"
                            >
                                <div class="py-2">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('profile.show', $user->username)) }}" target="_blank" class="flex items-center px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                        <i class="ri-facebook-fill text-lg mr-3 text-[#1877F2]"></i>
                                        <span class="text-sm font-medium">Facebook</span>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('profile.show', $user->username)) }}&text={{ urlencode('Check out @' . $user->username . ' on TikTok') }}" target="_blank" class="flex items-center px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                        <i class="ri-twitter-x-fill text-lg mr-3"></i>
                                        <span class="text-sm font-medium">X</span>
                                    </a>
                                    <button onclick="navigator.clipboard.writeText('{{ route('profile.show', $user->username) }}'); alert('Link copied!')" class="w-full flex items-center px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                        <i class="ri-link text-lg mr-3"></i>
                                        <span class="text-sm font-medium">Copy link</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Button (3 dots) -->
                        <div class="relative" x-data="{ showMoreMenu: false }">
                            <button @click="showMoreMenu = !showMoreMenu" class="w-10 h-10 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition flex items-center justify-center">
                                <i class="ri-more-fill text-gray-700 dark:text-gray-200 text-lg"></i>
                            </button>

                            <!-- More Menu Dropdown -->
                            <div
                                x-show="showMoreMenu"
                                @click.away="showMoreMenu = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="absolute right-0 mt-2 w-48 rounded-xl shadow-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 z-50"
                                style="display: none;"
                            >
                                <div class="py-2">
                                    @if(auth()->id() === $user->id)
                                        <!-- Own Profile: Settings -->
                                        <a href="{{ route('settings') }}" class="w-full flex items-center px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                            <i class="ri-settings-3-line text-lg mr-3"></i>
                                            <span class="text-sm font-medium">Settings</span>
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                                <i class="ri-logout-box-line text-lg mr-3"></i>
                                                <span class="text-sm font-medium">Logout</span>
                                            </button>
                                        </form>
                                    @else
                                        <!-- Other's Profile: Report & Block -->
                                        @auth
                                        <button @click="console.log('Report clicked'); window.dispatchEvent(new CustomEvent('open-report-modal')); showMoreMenu = false" class="w-full flex items-center px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                            <i class="ri-flag-line text-lg mr-3"></i>
                                            <span class="text-sm font-medium">Report</span>
                                        </button>
                                        <button
                                            @click="console.log('Block clicked'); window.dispatchEvent(new CustomEvent('toggle-block')); showMoreMenu = false"
                                            class="w-full flex items-center px-4 py-3 text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                            <i class="ri-spam-line text-lg mr-3"></i>
                                            <span class="text-sm font-medium">Block</span>
                                        </button>
                                        @else
                                        <!-- Guest: Redirect to login -->
                                        <a href="{{ route('login') }}" class="w-full flex items-center px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                            <i class="ri-flag-line text-lg mr-3"></i>
                                            <span class="text-sm font-medium">Report</span>
                                        </a>
                                        <a href="{{ route('login') }}" class="w-full flex items-center px-4 py-3 text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                            <i class="ri-spam-line text-lg mr-3"></i>
                                            <span class="text-sm font-medium">Block</span>
                                        </a>
                                        @endauth
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center space-x-8 mt-5">
                        <div>
                            <p class="text-xl font-black text-gray-900 dark:text-gray-100">{{ $videos->total() }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Videos</p>
                        </div>
                        <button @click="showFollowersModal = true" class="text-left hover:underline">
                            <p class="text-xl font-black text-gray-900 dark:text-gray-100" x-text="followerCount.toLocaleString()">{{ $followersCount }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Followers</p>
                        </button>
                        <button @click="showFollowingModal = true" class="text-left hover:underline">
                            <p class="text-xl font-black text-gray-900 dark:text-gray-100">{{ $followingCount }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Following</p>
                        </button>
                        <div>
                            <p class="text-xl font-black text-gray-900 dark:text-gray-100">{{ number_format($totalLikes) }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Likes</p>
                        </div>
                    </div>

                    @if($user->bio)
                        <p class="text-gray-700 dark:text-gray-300 mt-4 text-[15px]">{{ $user->bio }}</p>
                    @else
                        @if(auth()->id() === $user->id)
                            <p class="text-gray-400 dark:text-gray-500 mt-4 text-[15px] italic">Add a bio to tell people about yourself</p>
                        @endif
                    @endif

                    <!-- Social Links -->
                    @php
                        $hasSocialLinks = !empty($user->website) || !empty($user->instagram) || !empty($user->youtube) || !empty($user->facebook) || !empty($user->twitter);
                    @endphp
                    @if($hasSocialLinks)
                        <div class="flex flex-wrap items-center gap-2 mt-4">
                            @if(!empty($user->website))
                                <a href="{{ $user->website }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 px-3 py-2 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all text-sm group">
                                    <i class="ri-global-line text-base group-hover:scale-110 transition-transform"></i>
                                    <span class="font-semibold">Website</span>
                                </a>
                            @endif
                            @if(!empty($user->instagram))
                                <a href="https://instagram.com/{{ $user->instagram }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 px-3 py-2 rounded-full bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 text-pink-600 dark:text-pink-400 hover:from-purple-100 hover:to-pink-100 dark:hover:from-purple-900/30 dark:hover:to-pink-900/30 transition-all text-sm group">
                                    <i class="ri-instagram-line text-base group-hover:scale-110 transition-transform"></i>
                                    <span class="font-semibold">{{ $user->instagram }}</span>
                                </a>
                            @endif
                            @if(!empty($user->youtube))
                                <a href="{{ str_starts_with($user->youtube, 'http') ? $user->youtube : 'https://youtube.com/@' . $user->youtube }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 px-3 py-2 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 transition-all text-sm group">
                                    <i class="ri-youtube-line text-base group-hover:scale-110 transition-transform"></i>
                                    <span class="font-semibold">{{ str_starts_with($user->youtube, 'http') ? parse_url($user->youtube, PHP_URL_HOST) : $user->youtube }}</span>
                                </a>
                            @endif
                            @if(!empty($user->facebook))
                                <a href="{{ str_starts_with($user->facebook, 'http') ? $user->facebook : 'https://facebook.com/' . $user->facebook }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 px-3 py-2 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all text-sm group">
                                    <i class="ri-facebook-line text-base group-hover:scale-110 transition-transform"></i>
                                    <span class="font-semibold">{{ str_starts_with($user->facebook, 'http') ? parse_url($user->facebook, PHP_URL_HOST) : $user->facebook }}</span>
                                </a>
                            @endif
                            @if(!empty($user->twitter))
                                <a href="https://x.com/{{ $user->twitter }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 px-3 py-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700 transition-all text-sm group">
                                    <i class="ri-twitter-x-line text-base group-hover:scale-110 transition-transform"></i>
                                    <span class="font-semibold">{{ $user->twitter }}</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="max-w-4xl mx-auto px-4 mt-8" x-data="{
            activeTab: 'videos',
            videoSort: 'latest',
            showSortMenu: false,
            username: '{{ $user->username }}',
            videos: {{ Js::from($videos->items()) }},
            get sortedVideos() {
                let sorted = [...this.videos];
                if (this.videoSort === 'latest') {
                    return sorted.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                } else if (this.videoSort === 'oldest') {
                    return sorted.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                } else if (this.videoSort === 'trending') {
                    return sorted.sort((a, b) => (b.likes_count + b.views) - (a.likes_count + a.views));
                }
                return sorted;
            }
        }">
            <!-- Tab Buttons -->
            <div class="border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center justify-center space-x-12 flex-1">
                <button
                    @click="activeTab = 'videos'"
                    :class="activeTab === 'videos' ? 'border-gray-900 dark:border-gray-100 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                    class="py-4 px-2 border-b-2 font-bold transition-all"
                >
                    <span class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span>Videos</span>
                    </span>
                </button>

                <button
                    @click="activeTab = 'liked'"
                    :class="activeTab === 'liked' ? 'border-gray-900 dark:border-gray-100 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                    class="py-4 px-2 border-b-2 font-bold transition-all"
                >
                    <span class="flex items-center space-x-2">
                        <svg class="w-5 h-5" :fill="activeTab === 'liked' ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span>Liked</span>
                    </span>
                </button>
                </div>

                <!-- Sort Filter (only show on Videos tab) -->
                <div class="relative pr-4" x-show="activeTab === 'videos'" x-transition>
                    <button
                        @click="showSortMenu = !showSortMenu"
                        class="flex items-center space-x-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition text-sm font-semibold"
                    >
                        <i class="ri-filter-3-line text-base"></i>
                        <span x-text="videoSort === 'latest' ? 'Mới nhất' : (videoSort === 'trending' ? 'Thịnh hành' : 'Cũ nhất')"></span>
                        <i class="ri-arrow-down-s-line text-base"></i>
                    </button>

                    <!-- Sort Dropdown -->
                    <div
                        x-show="showSortMenu"
                        @click.away="showSortMenu = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="absolute right-4 mt-2 w-48 rounded-xl shadow-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 z-50"
                        style="display: none;"
                    >
                        <div class="py-2">
                            <button
                                @click="videoSort = 'latest'; showSortMenu = false"
                                :class="videoSort === 'latest' ? 'bg-gray-100 dark:bg-gray-700' : ''"
                                class="w-full flex items-center justify-between px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                            >
                                <div class="flex items-center">
                                    <i class="ri-time-line text-lg mr-3"></i>
                                    <span class="text-sm font-medium">Mới nhất</span>
                                </div>
                                <i x-show="videoSort === 'latest'" class="ri-check-line text-[#FE2C55]"></i>
                            </button>
                            <button
                                @click="videoSort = 'trending'; showSortMenu = false"
                                :class="videoSort === 'trending' ? 'bg-gray-100 dark:bg-gray-700' : ''"
                                class="w-full flex items-center justify-between px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                            >
                                <div class="flex items-center">
                                    <i class="ri-fire-line text-lg mr-3"></i>
                                    <span class="text-sm font-medium">Thịnh hành</span>
                                </div>
                                <i x-show="videoSort === 'trending'" class="ri-check-line text-[#FE2C55]"></i>
                            </button>
                            <button
                                @click="videoSort = 'oldest'; showSortMenu = false"
                                :class="videoSort === 'oldest' ? 'bg-gray-100 dark:bg-gray-700' : ''"
                                class="w-full flex items-center justify-between px-4 py-3 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                            >
                                <div class="flex items-center">
                                    <i class="ri-history-line text-lg mr-3"></i>
                                    <span class="text-sm font-medium">Cũ nhất</span>
                                </div>
                                <i x-show="videoSort === 'oldest'" class="ri-check-line text-[#FE2C55]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Videos Tab Content -->
            <div x-show="activeTab === 'videos'" x-transition class="pt-6">
                @if($videos->count() > 0)
                    <div class="grid grid-cols-3 gap-4">
                        <template x-for="video in sortedVideos" :key="video.id">
                            <a :href="`/@${username}/video/${video.id}`" class="aspect-[9/16] bg-gray-100 dark:bg-gray-800 rounded-xl overflow-hidden group relative shadow-md hover:shadow-xl transition-all duration-200">
                                <!-- Thumbnail -->
                                <template x-if="video.thumbnail">
                                    <img :src="`/${video.thumbnail}`" :alt="video.title" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!video.thumbnail">
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#FE2C55] to-[#25F4EE]">
                                        <svg class="w-16 h-16 text-white opacity-60" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                        </svg>
                                    </div>
                                </template>

                                <!-- Overlay -->
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-all duration-200 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center space-x-4 text-white">
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="font-bold" x-text="video.views.toLocaleString()"></span>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="font-bold" x-text="video.likes_count.toLocaleString()"></span>
                                            </div>
                                        </div>
                                        <template x-if="video.title">
                                            <p class="text-white text-sm mt-2 px-3 line-clamp-2 font-medium" x-text="video.title"></p>
                                        </template>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $videos->links() }}
                    </div>
                @else
                    <x-empty-state
                        icon="video"
                        title="No videos yet"
                        message="This user hasn't uploaded any videos."
                        :actionUrl="auth()->id() === $user->id ? route('videos.create') : null"
                        :actionText="auth()->id() === $user->id ? 'Upload Video' : null"
                    />
                @endif
            </div>

            <!-- Liked Tab Content -->
            <div x-show="activeTab === 'liked'" x-transition class="pt-6" style="display: none;">
                @if($likedVideos->count() > 0)
                    <div class="grid grid-cols-3 gap-4">
                        @foreach($likedVideos as $video)
                            <a href="{{ route('videos.show', [$video->user->username, $video]) }}" class="aspect-[9/16] bg-gray-100 dark:bg-gray-800 rounded-xl overflow-hidden group relative shadow-md hover:shadow-xl transition-all duration-200">
                                <!-- Thumbnail -->
                                @if($video->thumbnail)
                                    <img src="{{ asset($video->thumbnail) }}" alt="{{ $video->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-500 to-pink-500">
                                        <svg class="w-16 h-16 text-white opacity-60" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Overlay -->
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-all duration-200 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center space-x-4 text-white mb-2">
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="font-bold">{{ number_format($video->views) }}</span>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="font-bold">{{ number_format($video->likes_count) }}</span>
                                            </div>
                                        </div>
                                        <p class="text-gray-200 text-xs">by {{ '@' . $video->user->username }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $likedVideos->links() }}
                    </div>
                @else
                    <x-empty-state
                        icon="heart"
                        title="No liked videos"
                        message="{{ auth()->id() === $user->id ? 'You haven\'t liked any videos yet.' : 'This user hasn\'t liked any videos yet.' }}"
                        :actionUrl="auth()->id() === $user->id ? route('home') : null"
                        :actionText="auth()->id() === $user->id ? 'Discover Videos' : null"
                    />
                @endif
            </div>
        </div>

        <!-- Edit Profile Modal -->
        <div
            x-show="showEditModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showEditModal = false"
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
                class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
            >
                <!-- Modal Header -->
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
                    <h2 class="text-2xl font-black text-gray-900">Edit Profile</h2>
                    <button
                        @click="showEditModal = false"
                        class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center transition"
                    >
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    @method('PATCH')

                    <!-- Profile Photo -->
                    <div class="flex items-center space-x-6">
                        <div id="modal-avatar-preview" class="w-24 h-24 rounded-full flex items-center justify-center text-4xl font-bold flex-shrink-0 shadow-lg ring-2 ring-gray-100 overflow-hidden"
                             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            @if($user->avatar)
                                <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-white">{{ substr($user->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div>
                            <label for="modal-avatar-input" class="inline-block px-4 py-2 bg-[#FE2C55] text-white rounded-lg hover:bg-[#FE2C55]/90 transition font-semibold text-sm cursor-pointer">
                                Change Photo
                            </label>
                            <input
                                type="file"
                                id="modal-avatar-input"
                                name="avatar"
                                accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                style="display: none;"
                            />
                            <p class="text-gray-500 text-xs mt-2">JPG, PNG or GIF. Max 2MB</p>
                        </div>
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-900 mb-2">
                            Name
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                            placeholder="Your name"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-bold text-gray-900 mb-2">
                            Username
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">@</span>
                            <input
                                type="text"
                                name="username"
                                id="username"
                                value="{{ old('username', $user->username) }}"
                                required
                                class="w-full pl-8 pr-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                                placeholder="username"
                            >
                        </div>
                        <p class="text-gray-500 text-xs mt-1">Your unique username on TikTok</p>
                        @error('username')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-900 mb-2">
                            Email
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                            placeholder="your@email.com"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="block text-sm font-bold text-gray-900 mb-2">
                            Bio
                        </label>
                        <textarea
                            name="bio"
                            id="bio"
                            rows="4"
                            maxlength="200"
                            class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all resize-none"
                            placeholder="Tell people about yourself..."
                        >{{ old('bio', $user->bio) }}</textarea>
                        <p class="text-gray-500 text-xs mt-1">{{ strlen($user->bio ?? '') }}/200</p>
                        @error('bio')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Social Links Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-black text-gray-900 mb-4">Social Links</h3>
                        <div class="space-y-4">
                            <!-- Website -->
                            <div>
                                <label for="website" class="block text-sm font-bold text-gray-900 mb-2">
                                    <i class="ri-global-line mr-1"></i> Website
                                </label>
                                <input
                                    type="url"
                                    name="website"
                                    id="website"
                                    value="{{ old('website', $user->website) }}"
                                    class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                                    placeholder="https://yourwebsite.com"
                                >
                                @error('website')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Instagram -->
                            <div>
                                <label for="instagram" class="block text-sm font-bold text-gray-900 mb-2">
                                    <i class="ri-instagram-line mr-1"></i> Instagram
                                </label>
                                <input
                                    type="text"
                                    name="instagram"
                                    id="instagram"
                                    value="{{ old('instagram', $user->instagram) }}"
                                    class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                                    placeholder="username"
                                >
                                @error('instagram')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- YouTube -->
                            <div>
                                <label for="youtube" class="block text-sm font-bold text-gray-900 mb-2">
                                    <i class="ri-youtube-line mr-1"></i> YouTube
                                </label>
                                <input
                                    type="text"
                                    name="youtube"
                                    id="youtube"
                                    value="{{ old('youtube', $user->youtube) }}"
                                    class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                                    placeholder="Channel name or URL"
                                >
                                @error('youtube')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Facebook -->
                            <div>
                                <label for="facebook" class="block text-sm font-bold text-gray-900 mb-2">
                                    <i class="ri-facebook-line mr-1"></i> Facebook
                                </label>
                                <input
                                    type="text"
                                    name="facebook"
                                    id="facebook"
                                    value="{{ old('facebook', $user->facebook) }}"
                                    class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                                    placeholder="Profile name or URL"
                                >
                                @error('facebook')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- X (Twitter) -->
                            <div>
                                <label for="twitter" class="block text-sm font-bold text-gray-900 mb-2">
                                    <i class="ri-twitter-x-line mr-1"></i> X
                                </label>
                                <input
                                    type="text"
                                    name="twitter"
                                    id="twitter"
                                    value="{{ old('twitter', $user->twitter) }}"
                                    class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                                    placeholder="username"
                                >
                                @error('twitter')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button
                            type="button"
                            @click="showEditModal = false"
                            class="px-6 py-3 border-2 border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-bold"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-3 bg-[#FE2C55] hover:bg-[#FE2C55]/90 text-white rounded-lg transition-all duration-200 hover:scale-[1.02] font-bold focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:ring-offset-2"
                        >
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Followers Modal -->
        <div
            x-show="showFollowersModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showFollowersModal = false"
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
                class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[80vh] flex flex-col"
            >
                <!-- Modal Header -->
                <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-black text-gray-900">Followers</h2>
                    <button
                        @click="showFollowersModal = false"
                        class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center transition"
                    >
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-y-auto p-6">
                    @forelse($followers as $follower)
                        <div class="flex items-center justify-between py-3 hover:bg-gray-50 rounded-lg px-3 -mx-3">
                            <a href="{{ route('profile.show', $follower->username) }}" class="flex items-center gap-3 flex-1">
                                @if($follower->avatar)
                                    <img src="{{ asset($follower->avatar) }}" alt="{{ $follower->name }}" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                                @else
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
                                         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <span class="text-lg font-bold text-white">{{ substr($follower->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-[16px] font-bold text-gray-900 truncate">{{ $follower->name }}</p>
                                    <p class="text-[14px] text-gray-500 truncate">{{ '@' . $follower->username }}</p>
                                    @if($follower->bio)
                                        <p class="text-[13px] text-gray-600 truncate mt-1">{{ $follower->bio }}</p>
                                    @endif
                                </div>
                            </a>
                            @if(auth()->check() && auth()->id() !== $follower->id)
                                <x-follow-button :user="$follower" class="ml-auto" />
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-gray-500 font-semibold">No followers yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Following Modal -->
        <div
            x-show="showFollowingModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showFollowingModal = false"
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
                class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[80vh] flex flex-col"
            >
                <!-- Modal Header -->
                <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-black text-gray-900">Following</h2>
                    <button
                        @click="showFollowingModal = false"
                        class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center transition"
                    >
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-y-auto p-6">
                    @forelse($following as $followedUser)
                        <div class="flex items-center justify-between py-3 hover:bg-gray-50 rounded-lg px-3 -mx-3">
                            <a href="{{ route('profile.show', $followedUser->username) }}" class="flex items-center gap-3 flex-1">
                                @if($followedUser->avatar)
                                    <img src="{{ asset($followedUser->avatar) }}" alt="{{ $followedUser->name }}" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                                @else
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
                                         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <span class="text-lg font-bold text-white">{{ substr($followedUser->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-[16px] font-bold text-gray-900 truncate">{{ $followedUser->name }}</p>
                                    <p class="text-[14px] text-gray-500 truncate">{{ '@' . $followedUser->username }}</p>
                                    @if($followedUser->bio)
                                        <p class="text-[13px] text-gray-600 truncate mt-1">{{ $followedUser->bio }}</p>
                                    @endif
                                </div>
                            </a>
                            @if(auth()->check() && auth()->id() !== $followedUser->id)
                                <x-follow-button :user="$followedUser" class="ml-auto" />
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-gray-500 font-semibold">Not following anyone yet</p>
                        </div>
                    @endforelse
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
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full"
            >
                <!-- Modal Header -->
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-black text-gray-900 dark:text-gray-100">Report User</h2>
                    <button
                        @click="showReportModal = false"
                        class="w-10 h-10 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition"
                    >
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form action="{{ route('reports.store', $user) }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    <p class="text-sm text-gray-600 dark:text-gray-400">Why are you reporting <strong>{{ '@' . $user->username }}</strong>?</p>

                    <!-- Reason Options -->
                    <div class="space-y-2">
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                            <input type="radio" name="reason" value="spam" required class="w-4 h-4 text-[#FE2C55] focus:ring-[#FE2C55]">
                            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-100">Spam</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                            <input type="radio" name="reason" value="harassment" required class="w-4 h-4 text-[#FE2C55] focus:ring-[#FE2C55]">
                            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-100">Harassment or bullying</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                            <input type="radio" name="reason" value="inappropriate" required class="w-4 h-4 text-[#FE2C55] focus:ring-[#FE2C55]">
                            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-100">Inappropriate content</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                            <input type="radio" name="reason" value="fake_account" required class="w-4 h-4 text-[#FE2C55] focus:ring-[#FE2C55]">
                            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-100">Fake account</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                            <input type="radio" name="reason" value="other" required class="w-4 h-4 text-[#FE2C55] focus:ring-[#FE2C55]">
                            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-100">Other</span>
                        </label>
                    </div>

                    <!-- Additional Description -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-900 dark:text-gray-100 mb-2">
                            Additional details (optional)
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="3"
                            maxlength="500"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all resize-none"
                            placeholder="Tell us more about why you're reporting this user..."
                        ></textarea>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max 500 characters</p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end space-x-3 pt-4">
                        <button
                            type="button"
                            @click="showReportModal = false"
                            class="px-6 py-3 border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all font-bold"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-3 bg-[#FE2C55] hover:bg-[#FE2C55]/90 text-white rounded-lg transition-all duration-200 hover:scale-[1.02] font-bold focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:ring-offset-2"
                        >
                            Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Avatar Upload Script -->
    <script>
        console.log('[Profile] Avatar upload script loading...');

        document.addEventListener('DOMContentLoaded', function() {
            const avatarInput = document.getElementById('modal-avatar-input');
            console.log('[Profile] Avatar input found:', avatarInput);

            if (avatarInput) {
                avatarInput.addEventListener('change', function(e) {
                    console.log('[Profile] File input changed');
                    const file = e.target.files[0];

                    if (file) {
                        console.log('[Profile] File details:', file.name, file.size, file.type);

                        // Validate file size (max 2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            alert('File size must be less than 2MB');
                            this.value = '';
                            return;
                        }

                        // Validate file type
                        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                        if (!validTypes.includes(file.type)) {
                            alert('Please select a valid image file (JPG, PNG, GIF, or WEBP)');
                            this.value = '';
                            return;
                        }

                        // Preview the image
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const preview = document.getElementById('modal-avatar-preview');
                            if (preview) {
                                preview.innerHTML = `<img src="${event.target.result}" alt="Preview" class="w-full h-full object-cover">`;
                                console.log('[Profile] Preview updated');
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                console.error('[Profile] Avatar input not found!');
            }
        });
    </script>
</x-layouts.tiktok>
