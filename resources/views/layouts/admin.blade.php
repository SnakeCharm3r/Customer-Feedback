<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-layout="vertical"
      data-topbar="light"
      data-sidebar="dark"
      data-sidebar-size="lg"
      data-sidebar-image="none"
      data-preloader="disable">
@include('includes.head')

<body>
<div id="layout-wrapper">

    @include('partials.header')
    @include('partials.sidebar')
    <div class="vertical-overlay"></div>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Page title / breadcrumb --}}
                @hasSection('title')
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex align-items-center justify-content-between">
                            <h4 class="mb-0">@yield('title')</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('dashboard') }}">Dashboard</a>
                                    </li>
                                    @hasSection('breadcrumb')
                                        @yield('breadcrumb')
                                    @else
                                        <li class="breadcrumb-item active">@yield('title')</li>
                                    @endif
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @yield('content')

            </div>
        </div>
        @include('partials.footer')
    </div>

</div>
@include('includes.scripts')
</body>
</html>
