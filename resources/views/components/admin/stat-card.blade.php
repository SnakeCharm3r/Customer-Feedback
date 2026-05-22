@props([
    'label',
    'value',
    'icon'    => 'bi bi-bar-chart',
    'color'   => 'primary',
    'trend'   => null,
    'trendUp' => true,
])
<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="avatar-sm flex-shrink-0 me-3">
                <span class="avatar-title bg-{{ $color }}-subtle rounded-circle fs-3">
                    <i class="{{ $icon }} text-{{ $color }}"></i>
                </span>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <p class="text-muted text-truncate mb-0 small">{{ $label }}</p>
                <h4 class="fw-bold mt-1 mb-0">{{ $value }}</h4>
                @if($trend)
                <p class="mb-0 mt-1 small {{ $trendUp ? 'text-success' : 'text-danger' }}">
                    <i class="bi bi-arrow-{{ $trendUp ? 'up' : 'down' }}-right me-1"></i>{{ $trend }}
                </p>
                @endif
            </div>
        </div>
    </div>
</div>
