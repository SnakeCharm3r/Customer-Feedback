@props([
    'wrapperClass' => '',
    'maxHeight' => null,
])

<div class="ui-data-table {{ $wrapperClass }}"
     @if($maxHeight) style="max-height: {{ $maxHeight }}; overflow-y: auto;" @endif>
    <table {{ $attributes->class(['table', 'align-middle', 'mb-0']) }}>
        {{ $slot }}
    </table>
</div>
