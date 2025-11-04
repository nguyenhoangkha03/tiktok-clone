@props(['video'])

@php
    $isAuthenticated = auth()->check();
    $isLiked = $isAuthenticated ? auth()->user()->hasLiked($video->id) : false;
    $isFavorited = $isAuthenticated ? auth()->user()->hasFavorited($video->id) : false;
    $isFollowing = $isAuthenticated ? auth()->user()->isFollowing($video->user_id) : false;
@endphp

<div
    x-data="videoActions(
        {{ $video->id }},
        {{ $isLiked ? 'true' : 'false' }},
        {{ $video->likes_count }},
        {{ $isFavorited ? 'true' : 'false' }},
        {{ $video->favorites_count ?? 0 }},
        {{ $isFollowing ? 'true' : 'false' }},
        {{ $video->user_id }},
        {{ $isAuthenticated ? 'true' : 'false' }}
    )"
    class="flex flex-col items-center space-y-4"
>
    <!-- User Avatar with Follow Button -->
    @if(!$isAuthenticated || auth()->id() !== $video->user_id)
        <div class="relative mb-2">
            <a href="{{ route('profile.show', $video->user->username) }}" class="block w-12 h-12 rounded-full ring-2 ring-white/50 overflow-hidden shadow-lg hover:scale-110 transition-transform duration-200">
                @if($video->user->avatar)
                    <img src="{{ asset($video->user->avatar) }}" alt="{{ $video->user->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                        <span class="text-lg font-bold text-white">{{ substr($video->user->name, 0, 1) }}</span>
                    </div>
                @endif
            </a>
            <button
                @click="toggleFollow()"
                x-show="!following"
                class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-6 h-6 rounded-full bg-[#FE2C55] hover:bg-[#FE2C55]/90 flex items-center justify-center transition-all duration-200 shadow-lg hover:scale-110"
            >
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                </svg>
            </button>
        </div>
    @else
        <a href="{{ route('profile.show', $video->user->username) }}" class="block w-12 h-12 rounded-full ring-2 ring-white/50 overflow-hidden shadow-lg hover:scale-110 transition-transform duration-200 mb-2">
            @if($video->user->avatar)
                <img src="{{ asset($video->user->avatar) }}" alt="{{ $video->user->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                    <span class="text-lg font-bold text-white">{{ substr($video->user->name, 0, 1) }}</span>
                </div>
            @endif
        </a>
    @endif

    <!-- Like Button -->
    <div class="flex flex-col items-center">
        <button
            @click="toggleLike()"
            class="like-button w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-xl hover:shadow-2xl transition-all duration-200 hover:scale-110 border border-gray-100"
            :class="liked ? 'scale-110' : ''"
        >
            <svg class="w-7 h-7 transition-all duration-200" :class="liked ? 'fill-[#FE2C55] text-[#FE2C55]' : 'text-gray-800'" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
        <span class="text-gray-900 text-[13px] font-bold mt-2" x-text="likesCount >= 1000 ? (likesCount/1000).toFixed(1) + 'K' : likesCount"></span>
    </div>

    <!-- Comment Button -->
    <div class="flex flex-col items-center">
        <button
            @click="$dispatch('toggle-comments')"
            class="w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-xl hover:shadow-2xl hover:scale-110 transition-all duration-200 border border-gray-100"
        >
            <svg class="w-7 h-7 text-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
        </button>
        <span class="text-gray-900 text-[13px] font-bold mt-2 comment-count">{{ $video->comments_count >= 1000 ? number_format($video->comments_count/1000, 1) . 'K' : $video->comments_count }}</span>
    </div>

    <!-- Bookmark/Favorite Button -->
    <div class="flex flex-col items-center">
        <button
            @click="toggleFavorite()"
            class="favorite-button w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-xl hover:shadow-2xl transition-all duration-200 hover:scale-110 border border-gray-100"
            :class="favorited ? 'scale-110' : ''"
        >
            <svg class="w-7 h-7 transition-all duration-200" :class="favorited ? 'fill-[#FFC107] text-[#FFC107]' : 'text-gray-800'" :fill="favorited ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
            </svg>
        </button>
        <span class="text-gray-900 text-[13px] font-bold mt-2" x-text="favoritesCount >= 1000 ? (favoritesCount/1000).toFixed(1) + 'K' : favoritesCount"></span>
    </div>

    <!-- Share Button -->
    <div class="flex flex-col items-center relative">
        <button @click="showShareMenu = !showShareMenu" class="w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-xl hover:shadow-2xl hover:scale-110 transition-all duration-200 border border-gray-100">
            <i class="ri-share-forward-fill text-[28px] text-gray-800"></i>
        </button>
        <span class="text-gray-900 text-[13px] font-bold mt-2">{{ $video->shares_count ?? 0 }}</span>

        <!-- Share Menu -->
        <div
            x-show="showShareMenu"
            @click.away="showShareMenu = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transform transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute right-16 bottom-0 rounded-xl shadow-2xl p-2 w-56 z-10 border border-gray-200 bg-white"
            style="display: none;"
        >
            <!-- Share to Facebook -->
            <button
                @click="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent('{{ route('videos.show', [$video->user->username, $video]) }}'), '_blank', 'width=600,height=400'); showShareMenu = false"
                class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg text-[14px] transition font-medium flex items-center space-x-3"
            >
                <svg class="w-5 h-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span>Share to Facebook</span>
            </button>

            <!-- Share to X (Twitter) -->
            <button
                @click="window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent('{{ route('videos.show', [$video->user->username, $video]) }}') + '&text=' + encodeURIComponent('{{ $video->title ?? 'Check out this video!' }}'), '_blank', 'width=600,height=400'); showShareMenu = false"
                class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg text-[14px] transition font-medium flex items-center space-x-3"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
                <span>Share to X</span>
            </button>

            <!-- Copy Link -->
            <button
                @click="navigator.clipboard.writeText('{{ route('videos.show', [$video->user->username, $video]) }}'); showShareMenu = false; $dispatch('show-toast', {message: 'Link copied!', type: 'success'})"
                class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg text-[14px] transition font-medium flex items-center space-x-3"
            >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span>Copy Link</span>
            </button>
        </div>
    </div>

    <!-- Small Avatar at Bottom -->
    <div class="flex flex-col items-center mt-2">
        <a href="{{ route('profile.show', $video->user->username) }}" class="block w-10 h-10 rounded-full overflow-hidden ring-2 ring-white shadow-md hover:scale-110 transition-transform duration-200">
            @if($video->user->avatar)
                <img src="{{ asset($video->user->avatar) }}" alt="{{ $video->user->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                    <span class="text-sm font-bold text-white">{{ substr($video->user->name, 0, 1) }}</span>
                </div>
            @endif
        </a>
    </div>
</div>
