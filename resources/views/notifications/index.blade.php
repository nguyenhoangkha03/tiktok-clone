<x-layouts.tiktok>
    <div class="min-h-screen bg-white dark:bg-gray-900 pb-20">
        <!-- Header -->
        <div class="max-w-2xl mx-auto px-4 py-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-black text-gray-900 dark:text-gray-100">Notifications</h1>

                @if($notifications->where('read_at', null)->count() > 0)
                <form action="{{ route('notifications.readAll') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-[#FE2C55] hover:text-[#FE2C55]/80 transition">
                        Mark all as read
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-w-2xl mx-auto">
            @forelse($notifications as $notification)
                <div class="border-b border-gray-200 dark:border-gray-700 {{ $notification->isUnread() ? 'bg-blue-50 dark:bg-blue-900/10' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <div class="px-4 py-4 flex items-start gap-3">
                        <!-- Actor Avatar -->
                        @if($notification->actor)
                        <a href="{{ route('profile.show', $notification->actor->username) }}" class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full overflow-hidden">
                                @if($notification->actor->avatar)
                                    <img src="{{ asset($notification->actor->avatar) }}" alt="{{ $notification->actor->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                                        <span class="text-lg font-bold text-white">{{ substr($notification->actor->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                        </a>
                        @endif

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1">
                                    @if($notification->type === 'like')
                                        <p class="text-sm text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('profile.show', $notification->actor->username) }}" class="font-bold hover:underline">{{ $notification->actor->name }}</a>
                                            <span class="text-gray-600 dark:text-gray-400"> liked your video</span>
                                        </p>
                                    @elseif($notification->type === 'comment')
                                        <p class="text-sm text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('profile.show', $notification->actor->username) }}" class="font-bold hover:underline">{{ $notification->actor->name }}</a>
                                            <span class="text-gray-600 dark:text-gray-400"> commented on your video</span>
                                        </p>
                                        @if(isset($notification->data['comment']))
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $notification->data['comment'] }}</p>
                                        @endif
                                    @elseif($notification->type === 'follow')
                                        <p class="text-sm text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('profile.show', $notification->actor->username) }}" class="font-bold hover:underline">{{ $notification->actor->name }}</a>
                                            <span class="text-gray-600 dark:text-gray-400"> started following you</span>
                                        </p>
                                    @elseif($notification->type === 'favorite')
                                        <p class="text-sm text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('profile.show', $notification->actor->username) }}" class="font-bold hover:underline">{{ $notification->actor->name }}</a>
                                            <span class="text-gray-600 dark:text-gray-400"> favorited your video</span>
                                        </p>
                                    @endif

                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>

                                <!-- Unread indicator -->
                                @if($notification->isUnread())
                                <div class="w-2 h-2 bg-[#FE2C55] rounded-full flex-shrink-0 mt-1"></div>
                                @endif
                            </div>
                        </div>

                        <!-- Thumbnail (if video notification) -->
                        @if($notification->notifiable_type === 'App\Models\Video' && $notification->notifiable)
                        <a href="{{ route('videos.show', [$notification->notifiable->user->username, $notification->notifiable->id]) }}" class="flex-shrink-0">
                            <div class="w-16 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
                                @if($notification->notifiable->thumbnail)
                                    <img src="{{ asset($notification->notifiable->thumbnail) }}" alt="Video" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#FE2C55] to-[#25F4EE]">
                                        <i class="ri-play-circle-fill text-white text-xl"></i>
                                    </div>
                                @endif
                            </div>
                        </a>
                        @endif
                    </div>

                    <!-- Actions (Mark as read / Delete) -->
                    <div class="px-4 pb-3 flex items-center gap-3">
                        @if($notification->isUnread())
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-[#FE2C55] hover:text-[#FE2C55]/80 transition">
                                Mark as read
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-red-600 transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center">
                    <i class="ri-notification-off-line text-6xl text-gray-300 dark:text-gray-600"></i>
                    <p class="mt-4 text-gray-500 dark:text-gray-400 font-semibold">No notifications yet</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">When you get notifications, they'll show up here</p>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($notifications->hasPages())
            <div class="px-4 py-6">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>
</x-layouts.tiktok>
