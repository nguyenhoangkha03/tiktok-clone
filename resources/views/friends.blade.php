<x-layouts.tiktok>
    <div class="video-feed-scroll video-feed-container h-screen overflow-y-scroll snap-y snap-mandatory scrollbar-hide smooth-scroll bg-white dark:bg-gray-900" x-data="videoNavigation()">
        @forelse($videos as $video)
            <x-video-card :video="$video" />
        @empty
            <x-empty-state
                icon="users"
                title="No videos from friends"
                message="Friends are people who follow each other. Follow more people to make friends!"
                :actionUrl="route('home')"
                actionText="Discover People"
            />
        @endforelse

        <!-- Video Navigation Buttons -->
        @if($videos->count() > 0)
            <div class="fixed right-8 top-1/2 -translate-y-1/2 flex flex-col space-y-4 z-30 hidden lg:flex">
                <!-- Previous Video Button -->
                <button
                    @click="scrollToPrevious"
                    class="w-12 h-12 bg-white/90 hover:bg-white rounded-full shadow-lg flex items-center justify-center transition-all hover:scale-110 border border-gray-200"
                    title="Previous video (Arrow Up)"
                >
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                </button>

                <!-- Next Video Button -->
                <button
                    @click="scrollToNext"
                    class="w-12 h-12 bg-white/90 hover:bg-white rounded-full shadow-lg flex items-center justify-center transition-all hover:scale-110 border border-gray-200"
                    title="Next video (Arrow Down)"
                >
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        @endif
    </div>

    <!-- Video Navigation Script -->
    <script>
        function videoNavigation() {
            return {
                scrollToNext() {
                    const activeContainer = document.querySelector('.video-container.active');
                    if (!activeContainer) {
                        const firstContainer = document.querySelector('.video-container');
                        if (firstContainer) {
                            firstContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                        return;
                    }

                    const allContainers = Array.from(document.querySelectorAll('.video-container'));
                    const currentIndex = allContainers.indexOf(activeContainer);
                    const nextIndex = Math.min(allContainers.length - 1, currentIndex + 1);

                    if (nextIndex !== currentIndex) {
                        allContainers[nextIndex].scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                },

                scrollToPrevious() {
                    const activeContainer = document.querySelector('.video-container.active');
                    if (!activeContainer) {
                        const firstContainer = document.querySelector('.video-container');
                        if (firstContainer) {
                            firstContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                        return;
                    }

                    const allContainers = Array.from(document.querySelectorAll('.video-container'));
                    const currentIndex = allContainers.indexOf(activeContainer);
                    const prevIndex = Math.max(0, currentIndex - 1);

                    if (prevIndex !== currentIndex) {
                        allContainers[prevIndex].scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            };
        }
    </script>
</x-layouts.tiktok>
