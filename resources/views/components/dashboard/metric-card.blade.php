@props([
    'label',
    'value',
    'icon' => 'bi-activity',
    'tone' => 'green',
    'href' => null,
    'badge' => null,
    'meta' => null,
    'progress' => null,
])

@php
    $tag = $href ? 'a' : 'div';
@endphp
<{{ $tag }} @if($href) href="{{ $href }}" @endif
    {{ $attributes->class(['dashboard-metric', 'dashboard-metric--'.$tone, 'text-decoration-none' => $href]) }}>
    <div class="dashboard-metric__body">
        <div class="dashboard-metric__content">
            <span class="dashboard-metric__label">{{ $label }}</span>
            <strong class="dashboard-metric__value">{{ $value }}</strong>
            @if($progress !== null)
                <div class="dashboard-metric__progress" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                    <span style="width: {{ max(0, min(100, (int) $progress)) }}%"></span>
                </div>
            @endif
            @if($badge)
                <span class="dashboard-metric__badge">{{ $badge }}</span>
            @endif
            @if($meta)
                <span class="dashboard-metric__meta">{{ $meta }}</span>
            @endif
        </div>
        <span class="dashboard-metric__icon" aria-hidden="true"><i class="bi {{ $icon }}"></i></span>
    </div>
</{{ $tag }}>
