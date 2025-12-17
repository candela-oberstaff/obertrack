@props(['status', 'priority' => 'medium'])

@php
    $styles = [
        'por_hacer' => 'bg-red-500 text-white',
        'en_proceso' => 'bg-yellow-400 text-white',
        'finalizado' => 'bg-green-400 text-white',
    ];

    $labels = [
        'por_hacer' => 'Por hacer',
        'en_proceso' => 'En proceso',
        'finalizado' => 'Finalizado',
    ];

    $style = $styles[$status] ?? 'bg-gray-200 text-gray-800';
    $label = $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
@endphp

<span class="inline-flex items-center px-4 py-1 rounded-full text-sm font-medium {{ $style }}">
    {{ $label }}
    <!-- Optional Chevron if it was a dropdown, omitted for static view -->
</span>
