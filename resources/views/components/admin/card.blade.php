@props([
    'title'   => null,
    'actions' => null,
    'flush'   => false,
    'class'   => '',
])
<div class="card {{ $class }}">
    @if($title || $actions)
    <div class="card-header d-flex align-items-center justify-content-between">
        @if($title)
            <h5 class="card-title mb-0">{{ $title }}</h5>
        @endif
        @if($actions)
            <div class="card-actions d-flex gap-2 align-items-center">
                {{ $actions }}
            </div>
        @endif
    </div>
    @endif
    <div class="{{ $flush ? 'card-body p-0' : 'card-body' }}">
        {{ $slot }}
    </div>
</div>
