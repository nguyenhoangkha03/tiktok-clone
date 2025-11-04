<x-layouts.tiktok>
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-2xl bg-white rounded-lg shadow-lg border border-gray-100 p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900 mb-2">Upload Video</h1>
                <p class="text-gray-600">Share your TikTok video with the community</p>
            </div>

            <form action="/video-create" method="POST" enctype="multipart/form-data" class="space-y-6" id="video-upload-form">
                @csrf

                <!-- Hidden field for thumbnail -->
                <input type="hidden" name="thumbnail" id="thumbnail-input">

                <!-- Video Upload -->
                <div>
                    <label for="video" class="block text-sm font-semibold text-gray-700 mb-2">
                        Video File *
                    </label>
                    <div class="relative">
                        <input
                            type="file"
                            name="video"
                            id="video"
                            accept="video/mp4,video/mov,video/avi,video/wmv,video/flv,video/webm,video/mpeg"
                            required
                            class="hidden"
                            onchange="handleVideoUpload(this)"
                        >

                        <!-- Upload Area -->
                        <div id="upload-area">
                            <label for="video" class="flex items-center justify-center w-full px-6 py-12 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-[#F1F1F2] hover:bg-gray-100 transition-all">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <span class="font-semibold text-[#FE2C55]">Click to upload</span> or drag and drop
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">MP4, MOV, AVI, WMV, FLV, WebM, MPEG (max 100MB)</p>
                                </div>
                            </label>
                        </div>

                        <!-- Preview Area -->
                        <div id="preview-area" class="hidden">
                            <div class="bg-black rounded-lg overflow-hidden">
                                <video id="video-preview" controls class="w-full max-h-96 mx-auto">
                                    <source id="video-source" src="" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                            <div class="mt-4 flex items-center justify-between bg-[#F1F1F2] p-4 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <svg class="h-10 w-10 text-[#FE2C55]" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                                    </svg>
                                    <div>
                                        <p id="file-name" class="text-sm font-semibold text-gray-900"></p>
                                        <p id="file-size" class="text-xs text-gray-500"></p>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    onclick="changeVideo()"
                                    class="px-4 py-2 bg-white border-2 border-[#FE2C55] text-[#FE2C55] rounded-lg hover:bg-[#FE2C55] hover:text-white transition-all font-semibold text-sm"
                                >
                                    Change Video
                                </button>
                            </div>
                        </div>
                    </div>
                    @error('video')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <script>
                    function handleVideoUpload(input) {
                        const file = input.files[0];
                        if (!file) return;

                        // Get file info
                        const fileName = file.name;
                        const fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

                        // Update file info
                        document.getElementById('file-name').textContent = fileName;
                        document.getElementById('file-size').textContent = fileSize;

                        // Create preview URL
                        const videoURL = URL.createObjectURL(file);
                        const videoPreview = document.getElementById('video-preview');
                        const videoSource = document.getElementById('video-source');

                        videoSource.src = videoURL;
                        videoPreview.load();

                        // Generate thumbnail when video is loaded
                        videoPreview.addEventListener('loadeddata', function() {
                            generateThumbnail(videoPreview);
                        }, { once: true });

                        // Show preview, hide upload area
                        document.getElementById('upload-area').classList.add('hidden');
                        document.getElementById('preview-area').classList.remove('hidden');
                    }

                    function generateThumbnail(video) {
                        // Seek to 1 second to get a good frame
                        video.currentTime = 1;

                        video.addEventListener('seeked', function() {
                            // Create canvas to capture frame
                            const canvas = document.createElement('canvas');
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;

                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                            // Convert to base64
                            const thumbnail = canvas.toDataURL('image/jpeg', 0.8);

                            // Store in hidden input
                            document.getElementById('thumbnail-input').value = thumbnail;

                            console.log('Thumbnail generated successfully');
                        }, { once: true });
                    }

                    function changeVideo() {
                        // Reset file input
                        document.getElementById('video').value = '';

                        // Clear thumbnail
                        document.getElementById('thumbnail-input').value = '';

                        // Clear preview
                        const videoPreview = document.getElementById('video-preview');
                        const videoSource = document.getElementById('video-source');
                        videoPreview.pause();
                        videoSource.src = '';

                        // Show upload area, hide preview
                        document.getElementById('upload-area').classList.remove('hidden');
                        document.getElementById('preview-area').classList.add('hidden');
                    }
                </script>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                        Title (Optional)
                    </label>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        value="{{ old('title') }}"
                        maxlength="255"
                        class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                        placeholder="Give your video a title"
                    >
                    @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Description (Optional)
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        maxlength="2000"
                        class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all resize-none"
                        placeholder="Tell viewers more about your video..."
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-4">
                    <a
                        href="{{ route('home') }}"
                        class="px-6 py-3 border-2 border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="px-6 py-3 bg-[#FE2C55] hover:bg-[#FE2C55]/90 text-white rounded-lg transition-all duration-200 hover:scale-[1.02] font-bold focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:ring-offset-2"
                    >
                        Upload Video
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.tiktok>
