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
        if (filter_var($displayAvatar, FILTER_VALIDATE_URL)) {
             $avatarSrc = $displayAvatar;
        } else {
             // 1. Check if it's in Storage (app/public/avatars)
             // The controller stores it as 'avatars/filename.jpg'
             if (\Illuminate\Support\Facades\Storage::disk('public')->exists('avatars/' . $displayAvatar)) {
                 $avatarSrc = \Illuminate\Support\Facades\Storage::url('avatars/' . $displayAvatar);
             } 
             // 2. Fallback: Check if it's in public/avatars (Legacy)
             elseif (file_exists(public_path('avatars/' . $displayAvatar))) {
                 $avatarSrc = asset('avatars/' . $displayAvatar);
             }
             // 3. Fallback: Try strict path if it was saved without folder prefix
             elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($displayAvatar)) {
                  $avatarSrc = \Illuminate\Support\Facades\Storage::url($displayAvatar);
             }
        }
    }
    
    // Final fallback to UI Avatars if no avatar is set or found
    $fallbackUrl = "https://ui-avatars.com/api/?name=" . urlencode($displayName) . "&color=FFFFFF&background=22A9C8";
    $avatarSrc = $avatarSrc ?: $fallbackUrl;
@endphp

<div class="{{ $sizeClass }} rounded-full overflow-hidden border-2 border-white shadow-sm bg-gray-100 flex-shrink-0 {{ $classes }}">
    <img src="{{ $avatarSrc }}" 
         alt="{{ $displayName }}" 
         class="w-full h-full object-cover"
         onerror="this.src='{{ $fallbackUrl }}'">
</div>
