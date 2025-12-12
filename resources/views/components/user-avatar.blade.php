@props(['user', 'size' => '10', 'classes' => ''])

@php
    // Map size identifier to actual pixel/tailwind sizes if needed, or just use as w/h classes
    // Common sizes: 10 (2.5rem), 8 (2rem), 12 (3rem), etc.
    $sizeClass = "w-{$size} h-{$size}";
    $fontSize = $size >= 10 ? 'text-lg' : 'text-xs';
    if ($size >= 16) $fontSize = 'text-xl';
@endphp

@if($user->avatar)
    <img src="{{ asset('avatars/' . $user->avatar) }}" 
         alt="{{ $user->name }}" 
         class="{{ $sizeClass }} rounded-full object-cover shadow-sm {{ $classes }}">
@else
    <div class="{{ $sizeClass }} rounded-full bg-primary flex items-center justify-center text-white font-bold {{ $fontSize }} shadow-md {{ $classes }}">
        {{ substr($user->name, 0, 1) }}
    </div>
@endif
