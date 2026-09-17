@props([
    'title' => null,
    'count' => null,
    'filtered' => false,
])

<section {{ $attributes->merge(['class' => 'ui-table-panel']) }}>
    @if($title || $count !== null || isset($actions))
        <header class="ui-table-panel__header">
            <div class="ui-table-panel__heading">
                @if($title)
                    <h2 class="ui-table-panel__title">{{ $title }}</h2>
                @endif

                @if($count !== null)
                    <span class="ui-table-panel__count">{{ $count }}</span>
                @endif

                @if($filtered)
                    <span class="ui-table-panel__filtered">
                        <i class="bi bi-funnel me-1" aria-hidden="true"></i>Filtered
                    </span>
                @endif
            </div>

            @isset($actions)
                <div class="ui-table-panel__actions">{{ $actions }}</div>
            @endisset
        </header>
    @endif

    <div class="ui-table-panel__scroll">
        {{ $slot }}
    </div>

    @isset($footer)
        <footer class="ui-table-panel__footer">{{ $footer }}</footer>
    @endisset
</section>
