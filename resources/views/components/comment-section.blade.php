@props(['video'])

<div class="flex flex-col h-full">
    <!-- Comments Header -->
    <div class="p-4 border-b border-gray-800">
        <h3 class="font-bold text-white">Comments ({{ $video->comments_count }})</h3>
    </div>

    <!-- Comments List -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4">
        @forelse($video->comments as $comment)
            <div class="flex items-start space-x-3">
                <!-- Avatar -->
                <div class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-bold">{{ substr($comment->user->name, 0, 1) }}</span>
                </div>

                <!-- Comment Content -->
                <div class="flex-1">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('profile.show', $comment->user->username) }}" class="font-semibold text-white text-sm hover:underline">
                            {{ $comment->user->name }}
                        </a>
                        <span class="text-gray-500 text-xs">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-300 text-sm mt-1">{{ $comment->content }}</p>

                    <!-- Delete Button (Only for comment owner) -->
                    @if(auth()->id() === $comment->user_id)
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="mt-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 text-xs hover:underline">Delete</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-gray-500">No comments yet. Be the first to comment!</p>
            </div>
        @endforelse
    </div>

    <!-- Comment Form -->
    <div class="p-4 border-t border-gray-800">
        <form action="{{ route('comments.store', $video) }}" method="POST">
            @csrf
            <div class="flex items-center space-x-3">
                <input
                    type="text"
                    name="content"
                    placeholder="Add a comment..."
                    maxlength="500"
                    required
                    class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-full text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-tiktok-pink"
                >
                <button
                    type="submit"
                    class="px-6 py-2 bg-tiktok-pink text-white rounded-full hover:bg-pink-600 transition font-semibold"
                >
                    Post
                </button>
            </div>
            @error('content')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </form>
    </div>
</div>
