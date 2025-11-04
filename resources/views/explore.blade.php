<x-layouts.tiktok>
    <div class="min-h-screen bg-white dark:bg-gray-900 py-8 pb-20">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900 dark:text-gray-100 mb-2">Explore</h1>
                <p class="text-gray-600 dark:text-gray-400">Discover trending and popular videos</p>
            </div>

            <!-- Video Grid -->
            @if($videos->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    @foreach($videos as $video)
                        <a href="{{ route('videos.show', [$video->user->username, $video]) }}" class="group relative aspect-[9/16] bg-gray-100 dark:bg-gray-800 rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300">
                            <!-- Video Thumbnail/Placeholder -->
                            <div class="w-full h-full relative">
                                @if($video->thumbnail)
                                    <img src="{{ asset($video->thumbnail) }}" alt="{{ $video->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#FE2C55] to-[#25F4EE]"></div>
                                @endif

                                <!-- Play Icon Overlay -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-60 group-hover:opacity-100 transition-opacity drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                    </svg>
                                </div>

                                <!-- Video Info Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <!-- User Avatar & Name -->
                                    <div class="absolute top-3 left-3 right-3 flex items-center space-x-2">
                                        <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0 ring-2 ring-white/50">
                                            @if($video->user->avatar)
                                                <img src="{{ asset($video->user->avatar) }}" alt="{{ $video->user->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                                                    <span class="text-xs font-bold text-white">{{ substr($video->user->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="text-white text-sm font-semibold truncate drop-shadow-lg">{{ $video->user->name }}</span>
                                    </div>

                                    <!-- Stats at Bottom -->
                                    <div class="absolute bottom-3 left-3 right-3">
                                        <div class="flex items-center space-x-4 text-white text-sm mb-2">
                                            <!-- Views -->
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="font-bold drop-shadow">{{ number_format($video->views) }}</span>
                                            </div>

                                            <!-- Likes -->
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="font-bold drop-shadow">{{ number_format($video->likes_count) }}</span>
                                            </div>

                                            <!-- Comments -->
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="font-bold drop-shadow">{{ number_format($video->comments_count) }}</span>
                                            </div>
                                        </div>

                                        <!-- Title/Description -->
                                        @if($video->title)
                                            <p class="text-white text-sm font-medium line-clamp-2 drop-shadow-lg">{{ $video->title }}</p>
                                        @elseif($video->description)
                                            <p class="text-white text-sm line-clamp-2 drop-shadow-lg">{{ $video->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Hover Effect Border -->
                            <div class="absolute inset-0 ring-2 ring-[#FE2C55] rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $videos->links() }}
                </div>
            @else
                <x-empty-state
                    icon="video"
                    title="No videos available"
                    message="Check back later for trending content!"
                    :actionUrl="route('home')"
                    actionText="Go to Home"
                />
            @endif
        </div>
    </div>
</x-layouts.tiktok>
