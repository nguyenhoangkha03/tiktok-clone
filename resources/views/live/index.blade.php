<x-layouts.tiktok>
    <div class="max-w-7xl mx-auto px-4 py-8 mt-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 dark:text-white">LIVE</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Watch live streams from creators</p>
        </div>

        @if($liveStreams->count() > 0)
            <!-- Live Streams Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($liveStreams as $stream)
                    <a href="{{ route('live.show', $stream) }}" class="group relative bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105">
                        <!-- Thumbnail / Placeholder -->
                        <div class="relative aspect-video bg-gradient-to-br from-purple-500 via-pink-500 to-red-500">
                            <!-- LIVE Badge -->
                            <div class="absolute top-4 left-4 px-3 py-1 bg-red-600 text-white font-bold text-sm rounded-full flex items-center gap-1.5 animate-pulse">
                                <span class="w-2 h-2 bg-white rounded-full"></span>
                                LIVE
                            </div>

                            <!-- Viewers Count -->
                            <div class="absolute top-4 right-4 px-3 py-1 bg-black/60 backdrop-blur-sm text-white font-semibold text-sm rounded-full flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ number_format($stream->viewers_count) }}
                            </div>

                            <!-- Play Icon Overlay -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Stream Info -->
                        <div class="p-4">
                            <!-- User Info -->
                            <div class="flex items-center gap-3 mb-3">
                                @if($stream->user->avatar)
                                    <img src="{{ asset($stream->user->avatar) }}" alt="{{ $stream->user->name }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                        <span class="text-white font-bold">{{ substr($stream->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-900 dark:text-white truncate">{{ $stream->user->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ '@' . $stream->user->username }}</p>
                                </div>
                            </div>

                            <!-- Title -->
                            <h3 class="font-bold text-gray-900 dark:text-white mb-1 line-clamp-2">{{ $stream->title }}</h3>

                            @if($stream->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $stream->description }}</p>
                            @endif

                            <!-- Time -->
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                Started {{ $stream->started_at->diffForHumans() }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $liveStreams->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No live streams right now</h3>
                @auth
                <p class="text-gray-600 dark:text-gray-400">Be the first to go live!</p>
                @else
                <p class="text-gray-600 dark:text-gray-400 mb-6">Check back later for live streams</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#FE2C55] hover:bg-[#FE2C55]/90 text-white rounded-lg transition-all font-bold">
                    Login to Go LIVE
                </a>
                @endauth
            </div>
        @endif
    </div>

    <!-- Floating Go LIVE Button -->
    @auth
    <a href="{{ route('live.create') }}" class="fixed bottom-6 right-6 lg:bottom-8 lg:right-8 px-6 py-4 bg-[#FE2C55] hover:bg-[#FE2C55]/90 text-white rounded-full transition-all font-bold flex items-center gap-2 shadow-2xl hover:shadow-3xl hover:scale-110 z-40 group">
        <svg class="w-6 h-6 group-hover:animate-pulse" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
        </svg>
        <span class="font-black text-lg">Go LIVE</span>
    </a>
    @endauth
</x-layouts.tiktok>
