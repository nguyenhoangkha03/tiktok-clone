<x-layouts.tiktok>
    <div class="min-h-screen bg-white dark:bg-gray-900 py-8">
        <div class="max-w-3xl mx-auto px-4">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('profile.show', auth()->user()->username) }}" class="inline-flex items-center space-x-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span class="font-semibold">Back to Profile</span>
                </a>
                <h1 class="text-3xl font-black text-gray-900 dark:text-gray-100">Settings</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your account settings and preferences</p>
            </div>

            <!-- Update Password -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm mb-6 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                    <h2 class="text-xl font-black text-white">Update Password</h2>
                    <p class="text-white/90 text-sm mt-1">Ensure your account is using a long, random password to stay secure</p>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="bg-white dark:bg-gray-800 border border-red-200 dark:border-red-800 rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-red-500 to-orange-500 px-6 py-4">
                    <h2 class="text-xl font-black text-white">Delete Account</h2>
                    <p class="text-white/90 text-sm mt-1">Permanently delete your account and all of your data</p>
                </div>
                <div class="p-6">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-layouts.tiktok>
