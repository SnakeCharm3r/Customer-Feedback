@props([
    'title',
    'icon' => null,
    'tone' => 'green',
    'flush' => false,
])

<section {{ $attributes->class(['dashboard-panel', 'dashboard-panel--'.$tone]) }}>
    <header class="dashboard-panel__header">
        <div class="dashboard-panel__heading">
            @if($icon)
                <span class="dashboard-panel__icon" aria-hidden="true"><i class="bi {{ $icon }}"></i></span>
            @endif
            <h2>{{ $title }}</h2>
        </div>
        @isset($actions)
            <div class="dashboard-panel__actions">{{ $actions }}</div>
        @endisset
    </header>
    <div class="dashboard-panel__body {{ $flush ? 'dashboard-panel__body--flush' : '' }}">
        {{ $slot }}
    </div>
    @isset($footer)
        <footer class="dashboard-panel__footer">{{ $footer }}</footer>
    @endisset
</section>
