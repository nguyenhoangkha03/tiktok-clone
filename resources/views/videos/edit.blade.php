<x-layouts.tiktok>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-2xl bg-gray-900 rounded-lg shadow-xl p-8">
            <h1 class="text-3xl font-bold text-white mb-6">Edit Video</h1>

            <form action="{{ route('videos.update', $video) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- TikTok URL -->
                <div>
                    <label for="tiktok_url" class="block text-sm font-medium text-gray-300 mb-2">
                        TikTok Video URL *
                    </label>
                    <input
                        type="url"
                        name="tiktok_url"
                        id="tiktok_url"
                        value="{{ old('tiktok_url', $video->tiktok_url) }}"
                        required
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-tiktok-pink focus:border-transparent"
                        placeholder="https://www.tiktok.com/@username/video/1234567890"
                    >
                    @error('tiktok_url')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-400">
                        Paste the full TikTok video URL
                    </p>
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-300 mb-2">
                        Title (Optional)
                    </label>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        value="{{ old('title', $video->title) }}"
                        maxlength="255"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-tiktok-pink focus:border-transparent"
                        placeholder="Give your video a title"
                    >
                    @error('title')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-300 mb-2">
                        Description (Optional)
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        maxlength="2000"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-tiktok-pink focus:border-transparent resize-none"
                        placeholder="Tell viewers more about your video..."
                    >{{ old('description', $video->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-between pt-4">
                    <div class="flex items-center space-x-4">
                        <a
                            href="{{ route('videos.show', [$video->user->username, $video]) }}"
                            class="px-6 py-3 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-800 transition font-semibold"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="px-6 py-3 bg-tiktok-pink text-white rounded-lg hover:bg-pink-600 transition font-semibold"
                        >
                            Update Video
                        </button>
                    </div>

                    <!-- Delete Button -->
                    <button
                        type="button"
                        onclick="document.getElementById('delete-form').submit()"
                        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold"
                    >
                        Delete Video
                    </button>
                </div>
            </form>

            <!-- Delete Form -->
            <form
                id="delete-form"
                action="{{ route('videos.destroy', $video) }}"
                method="POST"
                class="hidden"
                onsubmit="return confirm('Are you sure you want to delete this video? This action cannot be undone.')"
            >
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</x-layouts.tiktok>
