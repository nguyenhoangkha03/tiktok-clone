<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TikTok Clone') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('tiktok-logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Dark Mode Script (Must load before body) -->
    <script>
        // Check localStorage and apply dark mode immediately to prevent flash
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans antialiased overflow-x-hidden transition-colors duration-200">
    <div class="flex h-screen bg-white dark:bg-gray-900">
        <!-- Sidebar -->
        <aside class="hidden lg:block w-80 fixed h-full overflow-y-auto bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 z-50">
            <x-sidebar />
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-80 overflow-y-auto bg-white dark:bg-gray-900">
            <!-- User Avatar Menu (Desktop) -->
            @auth
                <div class="hidden lg:block fixed top-6 right-6 z-40" x-data="{ open: false }">
                    <button @click="open = !open" class="relative">
                        <div class="w-10 h-10 rounded-full overflow-hidden ring-2 ring-gray-200 dark:ring-gray-600 hover:ring-[#FE2C55] transition-all cursor-pointer">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-[#FE2C55] to-[#25F4EE] flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                    </button>

                    <!-- Dropdown Menu -->
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 py-2"
                        style="display: none;"
                    >
                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ '@' . auth()->user()->username }}</p>
                        </div>

                        <!-- Menu Items -->
                        <a href="{{ route('profile.show', auth()->user()->username) }}" class="flex items-center px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">View Profile</span>
                        </a>

                        <a href="{{ route('settings') }}" class="flex items-center px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Settings</span>
                        </a>

                        <div class="border-t border-gray-100 dark:border-gray-700 my-2"></div>

                        <!-- Theme Toggle -->
                        <div class="px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition" x-data="{
                            darkMode: localStorage.getItem('darkMode') === 'true',
                            toggleDarkMode() {
                                this.darkMode = !this.darkMode;
                                localStorage.setItem('darkMode', this.darkMode);
                                if (this.darkMode) {
                                    document.documentElement.classList.add('dark');
                                } else {
                                    document.documentElement.classList.remove('dark');
                                }
                            }
                        }">
                            <button @click="toggleDarkMode()" class="flex items-center justify-between w-full">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Dark Mode</span>
                                </div>
                                <div class="relative">
                                    <input type="checkbox" x-model="darkMode" class="sr-only">
                                    <div :class="darkMode ? 'bg-[#FE2C55]' : 'bg-gray-300'" class="w-10 h-5 rounded-full transition-colors"></div>
                                    <div :class="darkMode ? 'translate-x-5' : 'translate-x-0'" class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full transition-transform"></div>
                                </div>
                            </button>
                        </div>

                        <div class="border-t border-gray-100 dark:border-gray-700 my-2"></div>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-2.5 hover:bg-red-50 dark:hover:bg-red-900/20 transition text-left">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span class="text-sm font-medium text-red-600 dark:text-red-400">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endauth

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="fixed top-4 right-20 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="fixed top-4 right-20 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>

        <!-- Mobile Bottom Navigation -->
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 z-40">
            <div class="flex justify-around items-center h-16">
                <a href="{{ route('home') }}" class="flex flex-col items-center {{ request()->routeIs('home') ? 'text-black' : 'text-gray-500' }}">
                    <i class="ri-home-{{ request()->routeIs('home') ? 'fill' : 'line' }} text-[24px]"></i>
                    <span class="text-xs mt-0.5">Home</span>
                </a>
                <a href="{{ route('following') }}" class="flex flex-col items-center {{ request()->routeIs('following') ? 'text-black' : 'text-gray-500' }}">
                    <i class="ri-group-{{ request()->routeIs('following') ? 'fill' : 'line' }} text-[24px]"></i>
                    <span class="text-xs mt-0.5">Following</span>
                </a>
                @auth
                <a href="{{ route('videos.create') }}" class="flex flex-col items-center text-[#FE2C55]">
                    <i class="ri-add-box-fill text-[28px]"></i>
                </a>
                <a href="{{ route('profile.show', auth()->user()->username) }}" class="flex flex-col items-center {{ request()->routeIs('profile.show') ? 'text-black' : 'text-gray-500' }}">
                    <i class="ri-user-{{ request()->routeIs('profile.show') ? 'fill' : 'line' }} text-[24px]"></i>
                    <span class="text-xs mt-0.5">Profile</span>
                </a>
                @else
                <a href="{{ route('login') }}" class="flex flex-col items-center text-[#FE2C55]">
                    <i class="ri-login-box-line text-[24px]"></i>
                    <span class="text-xs mt-0.5">Login</span>
                </a>
                @endauth
            </div>
        </nav>
    </div>

    <!-- Unread Messages Polling Script -->
    @auth
    <script>
        // Poll unread messages count every 5 seconds
        async function updateUnreadMessagesCount() {
            try {
                const response = await fetch('{{ route('messages.unread') }}');
                if (response.ok) {
                    const data = await response.json();
                    const badge = document.getElementById('unread-messages-badge');

                    if (badge && data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.classList.remove('hidden');
                    } else if (badge) {
                        badge.classList.add('hidden');
                    }
                }
            } catch (error) {
                console.error('Error fetching unread messages count:', error);
            }
        }

        // Initial call
        updateUnreadMessagesCount();

        // Poll every 5 seconds
        setInterval(updateUnreadMessagesCount, 5000);
    </script>
    @endauth

    @stack('scripts')
</body>
</html>
