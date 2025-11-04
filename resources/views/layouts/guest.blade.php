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
</head>
<body class="bg-white text-gray-900 font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#25F4EE] via-[#FE2C55] to-[#000000] items-center justify-center p-12">
            <div class="text-center">
                <div class="flex items-center justify-center gap-3 mb-6">
                    <i class="ri-tiktok-fill text-[80px] text-white"></i>
                </div>
                <h1 class="text-6xl font-black text-white mb-4 tracking-tight">TikTok</h1>
                <p class="text-xl text-white/90 font-medium">Make Your Day</p>
            </div>
        </div>

        <!-- Right Side - Auth Form -->
        <div class="flex-1 flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                        <i class="ri-tiktok-fill text-[48px] text-black"></i>
                        <span class="text-[36px] font-black text-black tracking-tight">TikTok</span>
                    </a>
                </div>

                <!-- Auth Form Content -->
                <div class="bg-white">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
