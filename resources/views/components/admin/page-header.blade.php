@props([
    'title',
    'breadcrumbs' => [],
    'actions'     => null,
])
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">{{ $title }}</h4>
            <div class="d-flex align-items-center gap-2">
                @if($actions)
                    {{ $actions }}
                @endif
                <ol class="breadcrumb m-0 ms-3">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    @foreach($breadcrumbs as $crumb)
                        @if($loop->last)
                            <li class="breadcrumb-item active">{{ $crumb }}</li>
                        @else
                            <li class="breadcrumb-item">{{ $crumb }}</li>
                        @endif
                    @endforeach
                    @if(empty($breadcrumbs))
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    @endif
                </ol>
            </div>
        </div>
    </div>
</div>
