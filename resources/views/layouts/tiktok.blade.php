<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TikTok Clone') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white font-sans antialiased overflow-x-hidden">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="hidden lg:block w-64 border-r border-gray-800 fixed h-full overflow-y-auto">
            <x-sidebar />
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 overflow-y-auto">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>

        <!-- Mobile Bottom Navigation -->
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-black border-t border-gray-800 z-40">
            <div class="flex justify-around items-center h-16">
                <a href="{{ route('home') }}" class="flex flex-col items-center {{ request()->routeIs('home') ? 'text-white' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    <span class="text-xs mt-1">Home</span>
                </a>
                <a href="{{ route('following') }}" class="flex flex-col items-center {{ request()->routeIs('following') ? 'text-white' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                    <span class="text-xs mt-1">Following</span>
                </a>
                @auth
                <a href="{{ route('videos.create') }}" class="flex flex-col items-center text-tiktok-pink">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                </a>
                <a href="{{ route('profile.show', auth()->user()->username) }}" class="flex flex-col items-center {{ request()->routeIs('profile.show') ? 'text-white' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs mt-1">Profile</span>
                </a>
                @else
                <a href="{{ route('login') }}" class="flex flex-col items-center text-tiktok-pink">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs mt-1">Login</span>
                </a>
                @endauth
            </div>
        </nav>
    </div>

    <!-- Notification Badge Script -->
    @auth
    <script>
        // Update notification badge count
        async function updateNotificationBadge() {
            try {
                const response = await fetch('/api/notifications/unread-count');
                const data = await response.json();
                const badge = document.getElementById('unread-notifications-badge');

                console.log('Notification count:', data.count);

                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                } else {
                    console.error('Notification badge element not found!');
                }
            } catch (error) {
                console.error('Error fetching notification count:', error);
            }
        }

        // Update on page load (with delay to ensure DOM is ready)
        document.addEventListener('DOMContentLoaded', function() {
            updateNotificationBadge();

            // Update every 10 seconds (faster than 30 seconds)
            setInterval(updateNotificationBadge, 10000);
        });

        // Also update when page becomes visible again
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                updateNotificationBadge();
            }
        });
    </script>
    @endauth
</body>
</html>
