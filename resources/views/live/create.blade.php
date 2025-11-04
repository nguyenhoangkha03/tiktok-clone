<x-layouts.tiktok>
    <div class="max-w-3xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('live.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to LIVE
            </a>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white">Start Live Stream</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Set up your live stream details</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700">
            <form action="{{ route('live.store') }}" method="POST" class="p-8 space-y-6">
                @csrf

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                        Stream Title <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        maxlength="255"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition"
                        placeholder="What's your stream about?"
                    >
                    @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                        Description (Optional)
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        maxlength="1000"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition resize-none"
                        placeholder="Tell viewers what they can expect..."
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-bold text-blue-900 dark:text-blue-100 mb-1">Live Stream Tips</h4>
                            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                                <li>• Make sure you have a stable internet connection</li>
                                <li>• Interact with your viewers to keep them engaged</li>
                                <li>• Choose a well-lit environment for better video quality</li>
                                <li>• You can end your stream anytime from the stream page</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('live.index') }}" class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition font-bold">
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="px-8 py-3 bg-[#FE2C55] hover:bg-[#FE2C55]/90 text-white rounded-lg transition-all font-bold flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                        </svg>
                        Go LIVE Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.tiktok>
