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
                @yield('content')
            </div>
        </div>
        @include('partials.footer')
    </div>

</div>
@include('includes.scripts')
</body>
</html>
