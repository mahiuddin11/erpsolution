<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', 'AGB')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('backend.layouts.partials.styles')
    @yield('styles')
    <script src="{{ asset('backend/jquery.min.js') }}"></script>
</head>

<body class="sidebar-mini skin-purple-light sidebar-mini layout-fixed  text-sm">
    <?php
    $companyDetails = DB::table('companies')
        ->where('status', 'Active')
        ->orderBy('id', 'DESC')
        ->first();
    ?>
    @include('backend.layouts.partials.alertmessage')
    @include('backend.layouts.partials.header')
    @include('backend.layouts.partials.sidebar')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        @yield('navbar-content')
        <section class="content">
            <div class="container-fluid">
                @yield('admin-content')
            </div>
        </section>
    </div>


    @include('backend.layouts.partials.footer')
    @include('backend.layouts.partials.alertmessage')
    @include('backend.layouts.partials.scripts')
    @include('backend.layouts.partials.messages')
    @yield('scripts')
</body>

</html>
