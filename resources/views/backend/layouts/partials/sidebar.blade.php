@php
    use App\Helpers\Helper;
    $allNavigations = Helper::navigation();
    $submenuAccess = explode(',', Helper::getRoleAccessParent());
    $accessmainmenu = Helper::getRoleRootList();
    $activeClass = str_replace('.', '-', Route::currentRouteName());
@endphp
<style>
    .user_profile_section .profile_img img {
        height: 110px !important;
    }

    .fa-user-circle:before {
        content: "\f2bd";
        font-family: 'Font Awesome 5 Free';
    }

    .user_profile_section {
        text-align: center;
        border-bottom: 2px solid #28a745;
        padding: 10px 0;
    }

    .user_management_box .user_icon_left i {
        font-size: 20px;
        color: #fff;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        /* background: #28a74578; */
        text-align: center;
        line-height: 30px;
    }

    .profile_information a {
        font-size: 18px;
        font-weight: 600;
        color: #fff;
        text-transform: uppercase;
    }

    .profile_information {
        padding-top: 10px;
    }

    .user_management_box .user_icon_right i {
        font-size: 20px;
        color: #fff;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        /* background: #28a74578; */
        text-align: center;
        line-height: 30px;
    }

    .user_management_box .user_icon_right {
        float: right;
        clear: both;
        background: transparent;
    }

    .user_management_box .user_icon_left {
        background: transparent;
    }

    div#main_admin_sidebar_section {
        padding-top: 20px;
    }

    .layout-fixed .main-sidebar {
        bottom: 0;
        float: none;
        left: 0;
        position: fixed;
        top: 0;
        background: #0f172a !important;
    }

    div#main_admin_sidebar_section nav ul li a {
        font-size: 18px;
        color: #fff !important;
        padding: 10px;
        border: 0;
    }

    .small-box {
        border-radius: .25rem;
        box-shadow: 0 0 1px rgb(158 134 134 / 13%), 0 1px 3px rgb(97 85 85 / 20%);
        display: block;
        margin-bottom: 20px;
        position: relative;
        background: #fff;
    }

    div#main_admin_sidebar_section nav ul li:hover {
        background: #2c2b48;
    }

    div#main_admin_sidebar_section nav ul li a i {
        margin-right: 5px;
    }

    ul.nav.nav-treeview li a p:before {
        content: '';
        width: 25px;
        height: 2px;
        background: #fff;
        position: absolute;
        left: 0;
        top: 45%;
    }

    ul.nav.nav-treeview li a p {
        margin-left: 16px;
        font-size: 17px;
    }

    div#main_admin_sidebar_section nav ul li a :hover {
        margin-left: 10px;
        transition: all .3s linear;
    }

    div#main_admin_sidebar_section ul.nav.nav-treeview {
        padding: 0 13px;
    }

    div#main_admin_sidebar_section ul.nav.nav-treeview li a.nav-link:hover {
        background: #373651 !important;
    }

    div#main_admin_sidebar_section ul.nav ul.nav.nav-treeview li {
        border-left: 2px solid #fff;
    }

    div#main_admin_sidebar_section ul.nav.nav-treeview li a:hover p {
        margin-left: 28px !important;
        transition: all .3s linear;
    }
</style>

<aside class=" elevation-4    main-sidebar elevation-4 sidebar-light-danger">
    {{-- <a href="{{ route('home') }}" class="brand-link">
        <img src="{{ asset('/backend/logo/' . $companyDetails->logo) }}" alt=""
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Master AGB</span>
    </a> --}}

    <div class="sidebar_top_profile">
        <div class="container-fluid">
            <div class="row">
                {{-- <div class="col-md-12">
                    <div class="user_management_box">
                        <a href="#" class="btn btn-sm user_icon_left"> <i class="far fa-bell"></i></a>
                        <a href="#" class="btn btn-sm user_icon_right"> <i class="fal fa-user-circle"></i></a>

                    </div>
                </div> --}}
            </div>
            <div class="user_profile_section">
                <div class="row">
                    <div class="col-md-12">
                        <div class="">
                            @if (isset($companyDetails->logo))
                                <a href="{{ route('home') }}">
                                    <img width="180px" src="{{ asset('/backend/logo/' . $companyDetails->logo) }}"
                                        style="" alt="">
                                </a>
                            @endif
                        </div>
                        <div class="profile_information">
                            <a href="#"> {{ Auth()->user()->name }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Sidebar -->
    <div class="sidebar" id="main_admin_sidebar_section">
        <!-- Sidebar user panel (optional) -->
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-compact" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link">
                        <i style="color: rgb(9, 250, 101)" class="fas fa-border-all"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                @foreach ($allNavigations as $key => $value)
                    @if (in_array($value->uniqueName, $accessmainmenu))
                        @php
                            $menuHtml = '';
                            $open_menu = '';
                            if (!empty($value->submenu)) {
                                $menuHtml .= '<ul class="nav nav-treeview">';
                                foreach ($value->submenu as $key => $submenu) {
                                    if (in_array($submenu->uniqueName, $submenuAccess)) {
                                        $menuHtml .= '<li class="nav-item">';
                                        foreach ($submenu->childMenu as $route) {
                                            if ($route->navigate_status == 1) {
                                                $open_menu .= str_replace('.', '-', $route->route) . ' ';
                                                $menuHtml .=
                                                    '<a href="' . route($route->route) . '" class="nav-link ">';
                                                $menuHtml .= '<p>' . $submenu->label . '</p>';
                                                $menuHtml .= '</a>';
                                            }
                                        }
                                        $menuHtml .= '</li>';
                                    }
                                }
                                $menuHtml .= '</ul>';
                            }
                        @endphp
                        <li class="nav-item {{ $open_menu }}">
                            <a href="#" class="nav-link">
                                <i style="color: rgb(9, 250, 101)" class="fas {{ $value->icon }} "></i>
                                <p>
                                    {{ $value->label }}
                                    <i style="color: rgb(9, 250, 101)" class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            {!! $menuHtml !!}

                        </li>
                    @endif
                @endforeach

            </ul>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<script>
    // Get the active class from the Blade variable
    var activeClass = "{{ $activeClass }}";

    // Get all elements with the active class
    var elements = document.getElementsByClassName(activeClass);

    // Loop through the elements and add the 'menu-open' class
    for (var i = 0; i < elements.length; i++) {
        elements[i].classList.add('menu-open');
    }
</script>
