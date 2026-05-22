@props([
    'color'  => 'primary',
    'pill'   => true,
    'soft'   => false,
])
@php
    $cls = $soft
        ? "badge bg-{$color}-subtle text-{$color}"
        : "badge bg-{$color}";
    if ($pill) $cls .= ' rounded-pill';
@endphp
<span {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</span>
