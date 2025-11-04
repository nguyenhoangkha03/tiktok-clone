<x-guest-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="text-center lg:text-left">
            <h2 class="text-3xl font-black text-gray-900 mb-2">Sign up for TikTok</h2>
            <p class="text-gray-600">Create your account to start sharing videos.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                    class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                    placeholder="Enter your full name">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autocomplete="username"
                    class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                    placeholder="Choose a unique username">
                @error('username')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                    class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                    placeholder="Enter your email address">
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                    placeholder="Create a strong password">
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                    placeholder="Confirm your password">
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Register Button -->
            <button type="submit"
                class="w-full bg-[#FE2C55] hover:bg-[#FE2C55]/90 text-white font-bold py-3.5 rounded-lg transition-all duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:ring-offset-2">
                Sign up
            </button>
        </form>

        <!-- Divider -->
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white text-gray-500">Or</span>
            </div>
        </div>

        <!-- Login Link -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-[#FE2C55] hover:text-[#FE2C55]/80 transition">
                    Log in
                </a>
            </p>
        </div>

        <!-- Terms -->
        <p class="text-xs text-center text-gray-500 mt-6">
            By continuing, you agree to TikTok's
            <a href="#" class="underline hover:text-gray-700">Terms of Service</a>
            and confirm that you have read TikTok's
            <a href="#" class="underline hover:text-gray-700">Privacy Policy</a>.
        </p>
    </div>
</x-guest-layout>
