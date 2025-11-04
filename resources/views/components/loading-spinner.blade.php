@props(['size' => 'md', 'color' => 'pink'])

@php
    $sizeClasses = [
        'sm' => 'w-6 h-6',
        'md' => 'w-12 h-12',
        'lg' => 'w-16 h-16',
        'xl' => 'w-24 h-24',
    ];

    $colorClasses = [
        'pink' => 'border-tiktok-pink',
        'cyan' => 'border-tiktok-cyan',
        'white' => 'border-white',
        'gray' => 'border-gray-500',
    ];

    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $colorClass = $colorClasses[$color] ?? $colorClasses['pink'];
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center justify-center']) }}>
    <div class="{{ $sizeClass }} {{ $colorClass }} border-4 border-t-transparent rounded-full animate-spin"></div>
</div>
