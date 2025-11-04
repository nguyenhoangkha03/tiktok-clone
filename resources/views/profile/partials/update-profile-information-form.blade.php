<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('patch')

        <!-- Avatar Upload -->
        <div>
            <label class="block text-sm font-bold text-gray-900 mb-3">
                Profile Photo
            </label>
            <div class="flex items-center space-x-6">
                <!-- Current Avatar Preview -->
                <div class="relative">
                    <div id="avatar-preview" class="w-24 h-24 rounded-full bg-gradient-to-br from-[#FE2C55] to-[#25F4EE] flex items-center justify-center overflow-hidden">
                        @if($user->avatar)
                            <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl font-black text-white">{{ substr($user->name, 0, 1) }}</span>
                        @endif
                    </div>
                </div>

                <!-- Upload Button -->
                <div class="flex-1">
                    <label for="avatar-input" class="inline-block px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-all font-semibold text-sm border border-gray-300 cursor-pointer select-none">
                        Change Photo
                    </label>
                    <input
                        type="file"
                        id="avatar-input"
                        name="avatar"
                        accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                        style="display: none;"
                    />
                    <p class="text-gray-500 text-xs mt-2">JPG, PNG, GIF or WEBP. Max 2MB</p>
                    @error('avatar')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div>
            <label for="name" class="block text-sm font-bold text-gray-900 mb-2">
                Name
            </label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
                class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                placeholder="Your name"
            />
            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="username" class="block text-sm font-bold text-gray-900 mb-2">
                Username
            </label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">@</span>
                <input
                    id="username"
                    name="username"
                    type="text"
                    value="{{ old('username', $user->username) }}"
                    required
                    class="w-full pl-8 pr-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                    placeholder="username"
                />
            </div>
            <p class="text-gray-500 text-xs mt-1">Your unique username on TikTok</p>
            @error('username')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-bold text-gray-900 mb-2">
                Email
            </label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                placeholder="your@email.com"
            />
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="underline text-sm text-yellow-900 hover:text-yellow-700 font-semibold">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <label for="bio" class="block text-sm font-bold text-gray-900 mb-2">
                Bio
            </label>
            <textarea
                id="bio"
                name="bio"
                rows="4"
                maxlength="200"
                class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all resize-none"
                placeholder="Tell people about yourself..."
            >{{ old('bio', $user->bio) }}</textarea>
            <p class="text-gray-500 text-xs mt-1">{{ strlen($user->bio ?? '') }}/200</p>
            @error('bio')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Social Links Section -->
        <div class="border-t border-gray-200 pt-6 mt-6">
            <h3 class="text-lg font-black text-gray-900 mb-4">Social Links</h3>
            <div class="space-y-4">
                <!-- Website -->
                <div>
                    <label for="website" class="block text-sm font-bold text-gray-900 mb-2">
                        <i class="ri-global-line mr-1"></i> Website
                    </label>
                    <input
                        id="website"
                        name="website"
                        type="url"
                        value="{{ old('website', $user->website) }}"
                        class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                        placeholder="https://yourwebsite.com"
                    />
                    @error('website')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Instagram -->
                <div>
                    <label for="instagram" class="block text-sm font-bold text-gray-900 mb-2">
                        <i class="ri-instagram-line mr-1"></i> Instagram
                    </label>
                    <input
                        id="instagram"
                        name="instagram"
                        type="text"
                        value="{{ old('instagram', $user->instagram) }}"
                        class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                        placeholder="username"
                    />
                    @error('instagram')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- YouTube -->
                <div>
                    <label for="youtube" class="block text-sm font-bold text-gray-900 mb-2">
                        <i class="ri-youtube-line mr-1"></i> YouTube
                    </label>
                    <input
                        id="youtube"
                        name="youtube"
                        type="text"
                        value="{{ old('youtube', $user->youtube) }}"
                        class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                        placeholder="Channel name or URL"
                    />
                    @error('youtube')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Facebook -->
                <div>
                    <label for="facebook" class="block text-sm font-bold text-gray-900 mb-2">
                        <i class="ri-facebook-line mr-1"></i> Facebook
                    </label>
                    <input
                        id="facebook"
                        name="facebook"
                        type="text"
                        value="{{ old('facebook', $user->facebook) }}"
                        class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                        placeholder="Profile name or URL"
                    />
                    @error('facebook')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- X (Twitter) -->
                <div>
                    <label for="twitter" class="block text-sm font-bold text-gray-900 mb-2">
                        <i class="ri-twitter-x-line mr-1"></i> X
                    </label>
                    <input
                        id="twitter"
                        name="twitter"
                        type="text"
                        value="{{ old('twitter', $user->twitter) }}"
                        class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                        placeholder="username"
                    />
                    @error('twitter')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-[#FE2C55] hover:bg-[#FE2C55]/90 text-white rounded-lg transition-all duration-200 hover:scale-[1.02] font-bold focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:ring-offset-2">
                Save Changes
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-600 font-semibold"
                >✓ Saved successfully!</p>
            @endif
        </div>
    </form>

    <!-- Avatar Preview Script -->
    <script>
        (function() {
            console.log('Avatar upload script loading...');

            function initAvatarUpload() {
                const avatarInput = document.getElementById('avatar-input');
                console.log('Avatar input found:', avatarInput);

                if (avatarInput) {
                    // Test click
                    console.log('Avatar input exists, adding change listener');

                    avatarInput.addEventListener('change', function(e) {
                        console.log('File selected:', e.target.files);
                        const file = e.target.files[0];
                        if (file) {
                            console.log('File details:', file.name, file.size, file.type);

                            // Validate file size (max 2MB)
                            if (file.size > 2 * 1024 * 1024) {
                                alert('File size must be less than 2MB');
                                this.value = '';
                                return;
                            }

                            // Validate file type
                            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                            if (!validTypes.includes(file.type)) {
                                alert('Please select a valid image file (JPG, PNG, GIF, or WEBP)');
                                this.value = '';
                                return;
                            }

                            // Preview the image
                            const reader = new FileReader();
                            reader.onload = function(event) {
                                const preview = document.getElementById('avatar-preview');
                                if (preview) {
                                    preview.innerHTML = `<img src="${event.target.result}" alt="Preview" class="w-full h-full object-cover">`;
                                    console.log('Preview updated');
                                }
                            };
                            reader.readAsDataURL(file);
                        }
                    });

                    // Debug: add click handler to label as fallback
                    const labels = document.querySelectorAll('label[for="avatar-input"]');
                    console.log('Labels found:', labels.length);
                    labels.forEach(function(label) {
                        label.addEventListener('click', function(e) {
                            console.log('Label clicked');
                            // If for some reason label doesn't work, manually trigger
                            setTimeout(function() {
                                avatarInput.click();
                            }, 0);
                        });
                    });
                } else {
                    console.error('Avatar input not found!');
                }
            }

            // Try immediately
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAvatarUpload);
            } else {
                initAvatarUpload();
            }
        })();
    </script>
</section>
