@extends('backend.layouts.master')
@section('title')
    Hrm - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }

        .badge {
            display: inline-block;
            padding: .50em 1.50em;
            font-size: 90%;
        }

        /* Added: 2026-07-08 - Responsive fixes */

        /* Card header: stack title/tools nicely on small screens */
        .card-header.flex-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: nowrap;
        }

        .card-header .card-tools {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
            margin-left: auto;
        }



        /* On extra-small screens, let title and tools take full width and stack */
        @media (max-width: 575.98px) {

            div.dataTables_filter,
            div.dataTables_wrapper {
                margin-top: 10px
            }

            .card-header.flex-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-header .card-tools {
                width: 100%;
                justify-content: flex-start;
            }

            .card-header .card-tools .btn {
                flex: 1 1 auto;
                text-align: center;
            }

            .content-header h1 {
                font-size: 1.25rem;
            }

            .breadcrumb {
                flex-wrap: wrap;
                font-size: 0.8rem;
            }
        }

        /* Table action buttons - group tightly and wrap on small screens */
        .action-btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .action-btn-group .btn {
            padding: .25rem .5rem;
            font-size: .8rem;
        }

        /* Ensure table-responsive scroll is smooth and has visible scrollbar hint */
        .table-responsive {
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 767.98px) {


            #systemDatatable.table td,
            #systemDatatable.table th {
                white-space: nowrap;
                font-size: .85rem;
            }

            .mobile-action-group {
                display: flex;
                width: 100%;
                gap: 6px;
                margin-top: 6px;
                align-items: center;
            }

            .mobile-add-btn {
                flex: 1;
            }

            .mobile-export-btn {
                width: 48px;
                flex-shrink: 0;
            }

            .mobile-export-btn .btn {
                width: 100%;
                padding: 6px;
            }

            .mobile-export-btn .dropdown-toggle::after {
                display: none;
            }

            /* Top Row */
            .dataTables_wrapper .row:first-child {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: nowrap;
                margin: 0;
            }

            /* Show Entries */
            .dataTables_wrapper .dataTables_length {
                width: 48%;
                margin-bottom: 5px;
            }

            /* Search */
            .dataTables_wrapper .dataTables_filter {
                width: 48%;
                text-align: right;
                margin-bottom: 5px;
            }

            /* Select Box */
            .dataTables_wrapper .dataTables_length select {
                width: 65px;
                display: inline-block;
            }

            /* Search Input */
            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
                max-width: 130px;
                margin-left: 5px;
            }

            /* Label alignment */
            .dataTables_wrapper .dataTables_length label,
            .dataTables_wrapper .dataTables_filter label {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-size: 13px;
                margin-bottom: 0;
            }

            .dataTables_wrapper .row:last-child {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .dataTables_wrapper .dataTables_info {
                text-align: center;
                margin-bottom: 10px;
            }

            .dataTables_wrapper .dataTables_paginate {
                text-align: center !important;
                overflow-x: auto;
                white-space: nowrap;
                width: 100%;
            }

            .dataTables_wrapper .pagination {
                justify-content: center;
                flex-wrap: nowrap;
            }
        }

        /* Added: 2026-07-08 - Mobile export dropdown: make relocated DataTables buttons look like dropdown items */
        #buttonsMobile {
            min-width: 160px;
            padding: 4px 0;
        }

        #buttonsMobile .btn,
        #buttonsMobile button,
        #buttonsMobile a {
            display: block !important;
            width: 100%;
            text-align: left;
            padding: 6px 16px !important;
            border: none !important;
            border-radius: 0 !important;
            background: transparent !important;
            color: #212529 !important;
            font-size: .875rem;
        }

        #buttonsMobile .btn:hover,
        #buttonsMobile button:hover,
        #buttonsMobile a:hover {
            background-color: #f8f9fa !important;
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12 col-sm-6">
                    <h1 class="m-0">
                        HRM
                    </h1>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('hrm.attendance.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.attendance.index') }}">Hrm</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Attendance List</span></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                {{-- Modified: 2026-07-08 - flex-header class added for responsive stacking --}}
                <div class="card-header flex-header">
                    <h3 class="card-title mb-0">Attendance List</h3>

                    <div class="card-tools">

                        {{-- Desktop Add Button --}}
                        @if (helper::roleAccess('hrm.attendance.create'))
                            <a class="btn btn-primary btn-sm d-none d-sm-inline-block"
                                href="{{ route('hrm.attendance.create') }}">
                                <i class="fas fa-plus"></i>
                                Custom Attendance
                            </a>
                        @endif

                        {{-- Desktop Export Buttons --}}
                        <span id="buttons" class="d-none d-sm-inline-flex flex-wrap align-items-center"></span>

                        {{-- Mobile Action Area --}}
                        @if (helper::roleAccess('hrm.attendance.create'))
                            <div class="d-flex d-sm-none w-100 mobile-action-group">

                                <a class="btn btn-primary btn-sm mobile-add-btn"
                                    href="{{ route('hrm.attendance.create') }}">
                                    <i class="fas fa-plus"></i>
                                    Custom Attendance
                                </a>

                                <div class="dropdown mobile-export-btn">
                                    <button class="btn btn-default btn-sm dropdown-toggle w-100" type="button"
                                        id="exportDropdownToggle" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class="fas fa-file-export"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right" id="buttonsMobile"
                                        aria-labelledby="exportDropdownToggle">
                                    </div>
                                </div>

                            </div>
                        @endif

                        {{-- Desktop Only --}}
                        <a class="btn btn-tool btn-default d-none d-sm-inline-block" data-card-widget="collapse"
                            title="Collapse">
                            <i class="fas fa-minus"></i>
                        </a>

                        <a class="btn btn-tool btn-default d-none d-sm-inline-block" data-card-widget="remove"
                            title="Close">
                            <i class="fas fa-times"></i>
                        </a>

                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0 p-sm-2">
                    <div class="table-responsive">
                        <table id="systemDatatable"
                            class="display table-hover table table-bordered table-striped nowrap w-100">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Employee Name</th>
                                    <th>Date</th>
                                    <th>Sign In</th>
                                    <th>Location IN</th>
                                    <th>Sign Out</th>
                                    <th>Location Out</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>SL</th>
                                    <th>Employee Name</th>
                                    <th>Date</th>
                                    <th>Sign In</th>
                                    <th>Location IN</th>
                                    <th>Sign Out</th>
                                    <th>Location Out</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">

                </div>
            </div>
        </div>
        <!-- /.col-->
    </div>
@endsection
@section('scripts')
    @include('backend.pages.hrm.attendance.script')

    {{-- Added: 2026-07-08 - Move Excel/CSV/PDF export buttons between desktop bar and mobile dropdown --}}
    <script>
        (function() {
            var MOBILE_BREAKPOINT = 576; // matches Bootstrap's d-sm-* breakpoint

            function relocateExportButtons() {
                var $desktop = $('#buttons');
                var $mobile = $('#buttonsMobile');

                if ($desktop.length === 0 || $mobile.length === 0) {
                    return;
                }

                var isMobile = window.innerWidth < MOBILE_BREAKPOINT;

                if (isMobile && $desktop.children().length > 0) {
                    // Move (not clone) the real DataTables button nodes into the dropdown
                    $desktop.children().appendTo($mobile);
                } else if (!isMobile && $mobile.children().length > 0) {
                    // Move them back to the normal inline position
                    $mobile.children().appendTo($desktop);
                }
            }

            // Added: 2026-07-08 - Run once export buttons exist.
            // DataTables Buttons extension usually renders asynchronously (inside initComplete
            // or right after table init), so we poll briefly until #buttons actually has content.
            var attempts = 0;
            var waitForButtons = setInterval(function() {
                attempts++;
                if ($('#buttons').children().length > 0 || attempts > 40) {
                    clearInterval(waitForButtons);
                    relocateExportButtons();
                }
            }, 250);

            // Re-check on every resize (debounced) so rotating a device or resizing a browser
            // window moves the buttons to the correct container.
            var resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(relocateExportButtons, 200);
            });
        })();
    </script>
@endsection


{{-- @extends('backend.layouts.master')
@section('title')
    Hrm - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }

        .badge {
            display: inline-block;
            padding: .50em 1.50em;
            font-size: 90%;
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        HRM </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('hrm.attendance.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.attendance.index') }}">Hrm</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Attendance List</span></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Attendance List</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('hrm.attendance.create'))
                            <a class="btn btn-default" href="{{ route('hrm.attendance.create') }}"><i
                                    class="fas fa-plus"></i>
                                Custom Attendance</a>
                        @endif
                        <span id="buttons"></span>
                        <a class="btn btn-tool btn-default" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </a>
                        <a class="btn btn-tool btn-default" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="systemDatatable" class="display table-hover table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Emplyee_Name</th>
                                    <th>Date</th>
                                    <th>Sign In</th>
                                    <th>location IN</th>
                                    <th>Sign_Out</th>
                                    <th>location Out</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>SL</th>
                                    <th>Emplyee_Name</th>
                                    <th>Date</th>
                                    <th>Sign In</th>
                                    <th>location IN</th>
                                    <th>Sign_Out</th>
                                    <th>location Out</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">

                </div>
            </div>
        </div>
        <!-- /.col-->
    </div>
@endsection
@section('scripts')
    @include('backend.pages.hrm.attendance.script')
@endsection --}}
