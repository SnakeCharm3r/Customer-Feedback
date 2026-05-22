@props([
    'color'   => 'primary',
    'size'    => null,
    'outline' => false,
    'icon'    => null,
    'href'    => null,
    'type'    => 'button',
])
@php
    $variant = $outline ? "btn-outline-{$color}" : "btn-{$color}";
    $sizeClass = $size ? "btn-{$size}" : '';
    $classes = trim("btn {$variant} {$sizeClass}");
@endphp
@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)<i class="{{ $icon }} me-1"></i>@endif{{ $slot }}
</a>
@else
<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)<i class="{{ $icon }} me-1"></i>@endif{{ $slot }}
</button>
@endif
