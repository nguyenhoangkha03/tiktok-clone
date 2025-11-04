<x-layouts.tiktok>
    <div class="min-h-screen bg-white dark:bg-gray-900">
        <div class="max-w-4xl mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-black text-gray-900 dark:text-gray-100">Messages</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Your conversations</p>
            </div>

            <!-- Conversations List -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                @forelse($conversationsData as $conversation)
                    <a href="{{ route('messages.show', $conversation['user']->username) }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                        <!-- Avatar -->
                        <div class="w-14 h-14 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0 ring-2 {{ $conversation['unread_count'] > 0 ? 'ring-[#FE2C55]' : 'ring-gray-200 dark:ring-gray-600' }}">
                            @if($conversation['user']->avatar)
                                <img src="{{ asset($conversation['user']->avatar) }}" alt="{{ $conversation['user']->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                                    <span class="text-lg font-bold text-white">{{ substr($conversation['user']->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Message Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="font-bold text-gray-900 dark:text-white truncate">
                                    {{ $conversation['user']->name }}
                                </h3>
                                <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 ml-2">
                                    {{ $conversation['latest_message']->created_at->diffForHumans(null, true) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 truncate {{ $conversation['unread_count'] > 0 ? 'font-semibold' : '' }}">
                                @if($conversation['latest_message']->sender_id === auth()->id())
                                    <span class="text-gray-500 dark:text-gray-500">You: </span>
                                @endif
                                {{ $conversation['latest_message']->message }}
                            </p>
                        </div>

                        <!-- Unread Badge -->
                        @if($conversation['unread_count'] > 0)
                            <div class="w-6 h-6 bg-[#FE2C55] rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-bold">{{ $conversation['unread_count'] }}</span>
                            </div>
                        @endif
                    </a>
                @empty
                    <div class="text-center py-20">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <i class="ri-message-3-line text-gray-400 dark:text-gray-500 text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No messages yet</h3>
                        <p class="text-gray-600 dark:text-gray-400">Start a conversation by visiting a user's profile</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.tiktok>
