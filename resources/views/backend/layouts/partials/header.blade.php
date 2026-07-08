<!-- Navbar -->

<style>
    nav.main-header ul li a {
        font-size: 18px;
        color: #333 !important;
        font-weight: 500 !important;
    }

    ul.quick_access_btn {
        list-style: none;
        margin: 0;
    }

    ul.quick_access_btn li {
        display: inline-block;
    }

    ul.quick_access_btn li .btn {
        font-size: 18px !important;
        font-weight: 500 !important;
        color: #333;
        text-transform: uppercase;
        border: 2px solid #28a745;
        background: #28a74529;
    }

    ul.quick_access_btn li {
        display: inline-block;
        margin: 0 10px;
    }

    ul.quick_access_btn li .btn:hover {
        background: #28a745;
        transition: all .3s linear;
        color: #fff !important;
    }

    .main-header .navbar-nav .nav-item a {
        font-weight: 500 !important;
        text-transform: uppercase;
        padding: 0 20px;
    }

    .main-header .navbar-nav .nav-item a .fas {
        font-size: 30px;
        color: #28a745;
    }

    .content-wrapper section.content .card.card-default .card-header h3 {
        font-size: 24px;
    }

    ol.breadcrumb li.breadcrumb-item a {
        font-size: 16px;
        font-weight: 500 !important;
    }

    table#systemDatatable tbody td a:hover .fa {
        color: #28a745;
        transition: all .3s linear;
    }

    section.content .card.card-default .card-header:before {
        width: 20px;
        height: 20px;
        content: '';
        position: absolute;
        background: #28a745;
        left: 0;
        top: 0;
        border-radius: 0 10px;
    }

    /* Added: 2026-07-08 - Top navbar responsive fixes */

    /* Prevent the whole navbar row from ever forcing horizontal scroll */
    nav.main-header.navbar {
        flex-wrap: nowrap;
        padding-right: 4px;
    }

    /* User dropdown: keep avatar + name from squeezing other icons on small screens */
    .main-header .user-menu .dropdown-toggle {
        display: flex;
        align-items: center;
        padding: 0 8px;
        max-width: 100%;
        overflow: hidden;
    }

    .main-header .user-menu .user-image {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
        margin-left: 6px;
        flex-shrink: 0;
    }

    .main-header .user-menu .dropdown-toggle span.user-name-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 250px;
    }

    /* Search block: keep it inside viewport width when toggled open on mobile */
    .navbar-search-block {
        max-width: 100%;
    }

    @media (max-width: 575.98px) {

        /* Modified: 2026-07-08 - username hidden now, so icons can be a bit bigger without crowding */
        .main-header .navbar-nav .nav-item a .fas {
            font-size: 26px;
        }

        .main-header .navbar-nav .nav-item a {
            padding: 0 12px;
        }

        .main-header .user-menu .user-image {
            width: 34px;
            height: 34px;
        }

        .main-header .user-menu .dropdown-toggle {
            padding: 0 12px;
        }

        /* Modified: 2026-07-08 - user name now stays visible on mobile too (per request);
           only constrain its width with ellipsis so it can't push other icons off-screen */
        .main-header .user-menu .dropdown-toggle span.user-name-text {
            display: inline-block;
            /* max-width: 110px; */
        }

        /* Fullscreen toggle is not useful in mobile browsers - hide to save space */
        .main-header .navbar-nav.ml-auto>li.nav-item.d-none-mobile {
            display: none !important;
        }

        /* Make the search dropdown span the full width under the navbar instead of overflowing */
        .navbar-search-block {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            width: 100%;
            padding: 8px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
            z-index: 1030;
        }

        .navbar-search-block .form-control-navbar {
            width: 100%;
        }

        ol.breadcrumb li.breadcrumb-item a {
            font-size: 13px;
        }
    }
</style>


<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-border-all"></i></a>
        </li>
    </ul>

    <ul class="quick_access_btn" style="display: none">
        <li>
            <a href="{{ route('sale.sale.create') }}" class="btn btn-sm"> <i class="fas fa fa-shopping-bag "></i> </a>
        </li>
        <li>
            <a href="{{ route('inventorySetup.purchase.index') }}" class="btn btn-sm"> <i
                    class="fas fa fa-shopping-cart "></i> </a>
        </li>
        <li>
            <a href="{{ route('inventorySetup.product.index') }}" class="btn btn-sm"> <i class="fa fa-plus-circle"></i>
            </a>
        </li>
        <li>
            <a href="{{ route('inventorySetup.stockAdjustment.index') }}" class="btn btn-sm"> <i
                    class="fa fa-compress"></i> </a>
        </li>
        <li>
            <a href="{{ route('inventorySetup.stockAdjustment.index') }}" class="btn btn-sm"><i
                    class="fas fa-seedling"></i> </a>
        </li>

        <li>
            <a href="{{ route('inventorySetup.customer.index') }}" class="btn btn-sm"> <i class="fas fa-user-tag"></i>
            </a>
        </li>

        <li>
            <a href="{{ route('inventorySetup.supplier.index') }}" class="btn btn-sm"> <i
                    class="fas fa-user-shield"></i> </a>
        </li>
        <li>
            <a href="{{ route('inventorySetup.transfer.index') }}" class="btn btn-sm"> <i class="fas fa-users"></i></a>
        </li>
        <li>
            <a href="{{ route('inventorySetup.transfer.index') }}" class="btn btn-sm"> <i
                    class="fas fa-money-check-alt"></i> </a>
        </li>
        <li>
            <a href="{{ route('inventorySetup.transfer.index') }}" class="btn btn-sm"> <i
                    class="fas fa-money-bill-alt"></i> </a>
        </li>
        <li>
            <a href="{{ route('inventorySetup.transfer.index') }}" class="btn btn-sm"> <i
                    class="far fa-money-bill-alt"></i> </a>
        </li>
        <li>
            <a href="{{ route('inventorySetup.transfer.index') }}" class="btn btn-sm"> <i class="fas fa-adjust"></i>
            </a>
        </li>


        <li>
            <a href="{{ route('inventorySetup.transfer.index') }}" class="btn btn-sm"> <i
                    class="fab fa-expeditedssl"></i> </a>
        </li>

        <li>
            <a href="{{ route('project.project.create') }}" class="btn btn-sm"> <i class="fas fa-swatchbook"></i> </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">

            <div class="navbar-search-block">
                <form class="form-inline">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Search"
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <!-- Messages Dropdown Menu -->
        <!-- <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
        <!-- <div class="media">
                        <img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Brad Diesel
                                <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">Call me whenever you can...</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div> -->
        <!-- Message End -->
        <!-- </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
        <!-- <div class="media">
                        <img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                John Pierce
                                <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">I got your message bro</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div> -->
        <!-- Message End -->
        <!-- </a> -->
        <!-- <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
        <!-- <div class="media">
                        <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Nora Silvester
                                <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">The subject goes here</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div> -->
        <!-- Message End -->
        <!-- </a> -->
        <!-- <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
            </div>
        </li> -->
        <!-- Notifications Dropdown Menu -->
        <!-- <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">15</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 4 new messages
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 friend requests
                    <span class="float-right text-muted text-sm">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 new reports
                    <span class="float-right text-muted text-sm">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li> -->

        <li class="dropdown user user-menu">

            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <span class="user-name-text"> {{ Auth()->user()->name }}</span>

                @if (isset($companyDetails->logo))
                    <img class="user-image" src="{{ asset('/backend/logo/' . $companyDetails->logo) }}"
                        alt="User profile picture">
                @endif
            </a>
            <ul class="dropdown-menu">
                <!-- User image -->
                <li class="user-header  bg-info">
                    @if (isset($companyDetails->logo))
                        <img src="{{ asset('/backend/logo/' . $companyDetails->logo) }}" class="img-circle"
                            alt="User Image">
                    @endif
                    <p>
                        {{ Auth()->user()->name }}
                        <small>Member since Nov. 2012</small>
                    </p>
                </li>
                <!-- Menu Body -->

                <!-- Menu Footer-->
                <li class="user-footer">
                    <div class="row">
                        <div class="col-6">
                            <div class="pull-left">
                                <a href="{{ route('usermanage.userRole.profile', auth()->id()) }}"
                                    class="btn btn-default btn-flat btn-block">Profile</a>
                            </div>
                        </div>
                        <div class="col-6">

                            <div class="pull-right">
                                <a class="btn btn-default btn-flat btn-block" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                      document.getElementById('admin-logout-form').submit();">Log
                                    Out</a>
                                <form id="admin-logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </li>



        {{-- Modified: 2026-07-08 - added "d-none-mobile" hook class so fullscreen icon can be hidden on phones via media query --}}
        <li class="nav-item d-none-mobile">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                <i class="fas fa-th-large"></i>
            </a>
        </li> -->
    </ul>
</nav>
