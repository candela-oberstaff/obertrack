@props(['user' => null, 'size' => '10', 'classes' => '', 'name' => null, 'avatar' => null])

@php
    $sizeClass = "w-{$size} h-{$size}";
    $fontSize = $size >= 10 ? 'text-lg' : 'text-xs';
    if ($size >= 16) $fontSize = 'text-xl';

    $displayId = $user ? $user->id : null;
    $displayName = $user ? $user->name : ($name ?: 'Usuario');
    $displayAvatar = $user ? $user->avatar : $avatar;
    
    $avatarSrc = null;
    if ($displayAvatar) {
        $avatarSrc = filter_var($displayAvatar, FILTER_VALIDATE_URL) 
            ? $displayAvatar 
            : asset('avatars/' . $displayAvatar);
    }
    
    // Final fallback to UI Avatars if no avatar is set
    $fallbackUrl = "https://ui-avatars.com/api/?name=" . urlencode($displayName) . "&color=FFFFFF&background=22A9C8";
    $avatarSrc = $avatarSrc ?: $fallbackUrl;
@endphp

<div class="{{ $sizeClass }} rounded-full overflow-hidden border-2 border-white shadow-sm bg-gray-100 flex-shrink-0 {{ $classes }}">
    <img src="{{ $avatarSrc }}" 
         alt="{{ $displayName }}" 
         class="w-full h-full object-cover"
         onerror="this.src='{{ $fallbackUrl }}'">
</div>
