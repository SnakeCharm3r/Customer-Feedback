<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-layout="vertical"
      data-topbar="light"
      data-sidebar="dark"
      data-sidebar-size="lg"
      data-sidebar-image="none"
      data-preloader="disable">
@include('includes.head')

<body class="admin-shell-body">
<div id="layout-wrapper" class="admin-shell">

    @include('partials.header')
    @include('partials.sidebar')
    <div class="vertical-overlay"></div>

    <div class="main-content app-main-content">
        <main class="page-content app-page-content">
            <div class="container-fluid app-page-container">
                @yield('content')
            </div>
        </main>
        @include('partials.footer')
    </div>

</div>
@include('includes.scripts')
</body>
</html>
