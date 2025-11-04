<!-- Sidebar Component - TikTok Official Style -->
<div class="flex flex-col h-full bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700">
    <!-- Logo -->
    <div class="px-5 pt-6 pb-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <i class="ri-tiktok-fill text-[32px] leading-none text-black dark:text-white"></i>
            <span class="text-[28px] font-black text-black dark:text-white tracking-tight">TikTok</span>
        </a>
    </div>

    <!-- Search -->
    <div class="px-5 pb-2" x-data="{
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
                    const response = await fetch(`/api/videos/search?q=${encodeURIComponent(this.searchQuery)}&limit=8`);
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
                placeholder="Search"
                class="w-full py-2.5 pl-10 pr-3 bg-[#F1F1F2] dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full text-[15px] text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-gray-300 focus:border transition-colors">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" x-show="!loading">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <div class="absolute left-3 top-1/2 -translate-y-1/2" x-show="loading" style="display: none;">
                <div class="w-5 h-5 border-2 border-gray-400 border-t-gray-600 dark:border-gray-500 dark:border-t-gray-300 rounded-full animate-spin"></div>
            </div>

            <!-- Search Results Dropdown -->
            <div
                x-show="showResults && searchResults.length > 0"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="absolute top-full mt-2 left-0 right-0 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden z-50 max-h-[400px] overflow-y-auto custom-scrollbar"
                style="display: none;"
            >
                <template x-for="video in searchResults" :key="video.id">
                    <a :href="`/@${video.user.username}/video/${video.id}`" class="flex items-center gap-3 p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <div class="w-12 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden flex-shrink-0">
                            <template x-if="video.thumbnail">
                                <img :src="video.thumbnail" :alt="video.title" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!video.thumbnail">
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#FE2C55] to-[#25F4EE]">
                                    <i class="ri-play-circle-fill text-white text-lg"></i>
                                </div>
                            </template>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 dark:text-white font-semibold text-sm truncate line-clamp-2" x-text="video.title || 'Untitled'"></p>
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

    <!-- Navigation -->
    <nav class="flex-1 px-2 pt-1 flex flex-col gap-2 overflow-y-auto custom-scrollbar">
        <!-- For You -->
        <a href="{{ route('home') }}" class="flex items-center gap-4 px-3 py-2 {{ request()->routeIs('home') ? 'text-[#FE2C55]' : 'text-black dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors rounded-md group">
            <i class="ri-home-{{ request()->routeIs('home') ? 'fill' : 'line' }} text-[32px] leading-none"></i>
            <span class="text-[18px] {{ request()->routeIs('home') ? 'font-bold' : 'font-semibold' }}">For You</span>
        </a>

        <!-- Explore -->
        <a href="{{ route('explore') }}" class="flex items-center gap-4 px-3 py-2 {{ request()->routeIs('explore') ? 'text-[#FE2C55]' : 'text-black dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors rounded-md group">
            <i class="ri-compass-3-{{ request()->routeIs('explore') ? 'fill' : 'line' }} text-[32px] leading-none"></i>
            <span class="text-[18px] {{ request()->routeIs('explore') ? 'font-bold' : 'font-semibold' }}">Explore</span>
        </a>

        <!-- Following -->
        @auth
        <a href="{{ route('following') }}" class="flex items-center gap-4 px-3 py-2 {{ request()->routeIs('following') ? 'text-[#FE2C55]' : 'text-black dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors rounded-md group">
            <i class="ri-group-{{ request()->routeIs('following') ? 'fill' : 'line' }} text-[32px] leading-none"></i>
            <span class="text-[18px] {{ request()->routeIs('following') ? 'font-bold' : 'font-semibold' }}">Following</span>
        </a>
        @else
        <a href="{{ route('login') }}" class="flex items-center gap-4 px-3 py-2 text-black dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors rounded-md group">
            <i class="ri-group-line text-[32px] leading-none"></i>
            <span class="text-[18px] font-semibold">Following</span>
        </a>
        @endauth

        <!-- Friends -->
        @auth
        <a href="{{ route('friends') }}" class="flex items-center gap-4 px-3 py-2 {{ request()->routeIs('friends') ? 'text-[#FE2C55]' : 'text-black dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors rounded-md group">
            <i class="ri-user-heart-{{ request()->routeIs('friends') ? 'fill' : 'line' }} text-[32px] leading-none"></i>
            <span class="text-[18px] {{ request()->routeIs('friends') ? 'font-bold' : 'font-semibold' }}">Friends</span>
        </a>
        @else
        <a href="{{ route('login') }}" class="flex items-center gap-4 px-3 py-2 text-black dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors rounded-md group">
            <i class="ri-user-heart-line text-[32px] leading-none"></i>
            <span class="text-[18px] font-semibold">Friends</span>
        </a>
        @endauth

        <!-- Messages (Only show when authenticated) -->
        @auth
        <a href="{{ route('messages.index') }}" class="flex items-center gap-4 px-3 py-2 {{ request()->routeIs('messages.*') ? 'text-[#FE2C55]' : 'text-black dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors rounded-md group relative">
            <div class="relative">
                <i class="ri-message-3-{{ request()->routeIs('messages.*') ? 'fill' : 'line' }} text-[32px] leading-none"></i>
                <!-- Unread Badge -->
                <span id="unread-messages-badge" class="absolute -top-1 -right-1 bg-[#FE2C55] text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 hidden"></span>
            </div>
            <span class="text-[18px] {{ request()->routeIs('messages.*') ? 'font-bold' : 'font-semibold' }}">Messages</span>
        </a>

        <!-- Notifications (Only show when authenticated) -->
        <a href="{{ route('notifications.index') }}" class="flex items-center gap-4 px-3 py-2 {{ request()->routeIs('notifications.*') ? 'text-[#FE2C55]' : 'text-black dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors rounded-md group relative">
            <div class="relative">
                <i class="ri-notification-3-{{ request()->routeIs('notifications.*') ? 'fill' : 'line' }} text-[32px] leading-none"></i>
                <!-- Notification Badge -->
                <span id="unread-notifications-badge" class="absolute -top-1 -right-1 bg-[#FE2C55] text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 hidden"></span>
            </div>
            <span class="text-[18px] {{ request()->routeIs('notifications.*') ? 'font-bold' : 'font-semibold' }}">Notifications</span>
        </a>
        @endauth

        <!-- LIVE -->
        <a href="{{ route('live.index') }}" class="flex items-center gap-4 px-3 py-2 {{ request()->routeIs('live.*') ? 'text-[#FE2C55] bg-gray-50 dark:bg-gray-700' : 'text-black dark:text-gray-200' }} hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors rounded-md group">
            <i class="ri-tv-line text-[32px] leading-none"></i>
            <span class="text-[18px] font-semibold">LIVE</span>
        </a>

        <!-- Upload -->
        @auth
        <a href="{{ route('videos.create') }}" class="flex items-center gap-4 px-3 py-2 text-black dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors rounded-md group">
            <i class="ri-add-box-line text-[32px] leading-none"></i>
            <span class="text-[18px] font-semibold">Upload</span>
        </a>
        @else
        <a href="{{ route('login') }}" class="flex items-center gap-4 px-3 py-2 text-black dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors rounded-md group">
            <i class="ri-add-box-line text-[32px] leading-none"></i>
            <span class="text-[18px] font-semibold">Upload</span>
        </a>
        @endauth

        <!-- Profile -->
        @auth
        <a href="{{ route('profile.show', auth()->user()->username) }}" class="flex items-center gap-4 px-3 py-2 {{ request()->routeIs('profile.show') ? 'text-[#FE2C55]' : 'text-black dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors rounded-md group">
            <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0 {{ request()->routeIs('profile.show') ? 'ring-2 ring-[#FE2C55] ring-offset-2' : '' }}">
                @if(auth()->user()->avatar)
                    <img src="{{ asset(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                        <span class="text-sm font-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                @endif
            </div>
            <span class="text-[18px] {{ request()->routeIs('profile.show') ? 'font-bold' : 'font-semibold' }}">Profile</span>
        </a>
        @else
        <a href="{{ route('login') }}" class="flex items-center gap-4 px-3 py-2 text-black dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors rounded-md group">
            <i class="ri-user-line text-[32px] leading-none"></i>
            <span class="text-[18px] font-semibold">Profile</span>
        </a>
        @endauth

        @auth
        <!-- Divider -->
        <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>

        <!-- Following Section Header -->
        <div class="px-3 py-2">
            <span class="text-[13px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Following</span>
        </div>

        <!-- Following Users List -->
        @php
            $followingUsers = auth()->user()->following()->latest()->get();
        @endphp

        @forelse($followingUsers as $followedUser)
            <a href="{{ route('profile.show', $followedUser->username) }}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors rounded-md group">
                <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0">
                    @if($followedUser->avatar)
                        <img src="{{ asset($followedUser->avatar) }}" alt="{{ $followedUser->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                            <span class="text-sm font-bold text-white">{{ substr($followedUser->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[15px] font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $followedUser->name }}</p>
                    <p class="text-[12px] text-gray-500 dark:text-gray-400 truncate">{{ '@' . $followedUser->username }}</p>
                </div>
            </a>
        @empty
            <div class="px-3 py-4 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">You're not following anyone yet</p>
                <a href="{{ route('home') }}" class="text-sm text-[#FE2C55] font-semibold hover:underline mt-1 inline-block">
                    Discover users
                </a>
            </div>
        @endforelse
        @endauth
    </nav>

    <!-- Bottom Section -->
    @auth
    <div >
        <div class="p-4">
            <button onclick="document.getElementById('logout-form').submit()"  class="block w-full bg-[#FE2C55] hover:bg-[#FE2C55]/90 text-white text-center font-bold py-2.5 rounded-md transition-colors">
            Logout
        </button>
        </div>
        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
            @csrf
        </form>
    </div>
    @else
    <!-- Guest User - Login -->
    <div class="p-4">
        <a href="{{ route('login') }}" class="block w-full bg-[#FE2C55] hover:bg-[#FE2C55]/90 text-white text-center font-bold py-2.5 rounded-md transition-colors">
            Log in
        </a>
    </div>

    <!-- Footer -->
    <div class="px-4 pb-4 space-y-3">
        <div class="space-y-2">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Company</p>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Program</p>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Terms & Policies</p>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500">© 2025 TikTok</p>
    </div>
    @endauth
</div>

<style>
/* Light theme scrollbar */
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

/* Dark theme scrollbar */
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}
</style>
