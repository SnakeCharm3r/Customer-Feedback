@props([
    'label'   => 'Actions',
    'color'   => 'secondary',
    'size'    => 'sm',
    'icon'    => null,
    'align'   => 'end',
])
<div class="dropdown">
    <button type="button"
            class="btn btn-{{ $color }} btn-{{ $size }} dropdown-toggle"
            data-bs-toggle="dropdown"
            aria-expanded="false">
        @if($icon)<i class="{{ $icon }} me-1"></i>@endif{{ $label }}
    </button>
    <ul class="dropdown-menu dropdown-menu-{{ $align }} shadow-sm border-0">
        {{ $slot }}
    </ul>
</div>
