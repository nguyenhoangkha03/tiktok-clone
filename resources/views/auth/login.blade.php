<x-guest-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="text-center lg:text-left">
            <h2 class="text-3xl font-black text-gray-900 mb-2">Log in to TikTok</h2>
            <p class="text-gray-600">Welcome back! Please enter your details.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                    placeholder="Email or username">
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent transition-all"
                    placeholder="Password">
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="w-4 h-4 rounded border-gray-300 text-[#FE2C55] focus:ring-[#FE2C55] focus:ring-offset-0 cursor-pointer">
                    <span class="ml-2 text-sm text-gray-700">Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-[#FE2C55] hover:text-[#FE2C55]/80 transition">
                        Forgot password?
                    </a>
                @endif
            </div>

            <!-- Login Button -->
            <button type="submit"
                class="w-full bg-[#FE2C55] hover:bg-[#FE2C55]/90 text-white font-bold py-3.5 rounded-lg transition-all duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:ring-offset-2">
                Log in
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

        <!-- Register Link -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-semibold text-[#FE2C55] hover:text-[#FE2C55]/80 transition">
                    Sign up
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>
