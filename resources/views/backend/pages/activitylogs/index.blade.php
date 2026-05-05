@extends('backend.layouts.master')
@section('title')
    - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }
    </style>
    <style>
        .context-menu {
            background: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 160px;
            padding: 5px 0;
        }

        .context-menu .list-group-item {
            padding: 10px 15px;
            cursor: pointer;
            border: none;
        }

        .context-menu .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .context-menu .list-group-item i {
            margin-right: 8px;
            width: 18px;
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0">
                        {{ $title ?? '' }} </h1> --}}
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('inventorySetup.adjust.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.employee.index') }}">Hrm</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>User Activity logs</span></li>
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
                    <h3 class="card-title">{{ $title ?? '' }}</h3>
                    <div class="card-tools">
                        {{-- @if (helper::roleAccess('assets.category.create'))
                            <a class="btn btn-default" href="{{ route('assets.category.create') }}"><i
                                    class="fas fa-plus"></i>Add New</a>
                        @endif --}}
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
                                    <th>Date & Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Module</th>
                                    <th>Description</th>
                                    <th>Change Data</th>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                    <th>Device</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTable Ajax দিয়ে ডাটা আসবে -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>SL</th>
                                    <th>Date & Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Module</th>
                                    <th>Description</th>
                                    <th>Change Data</th>
                                    <th>Status</th>
                                    <th>IP Address</th>
                                    <th>Device</th>
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
    @include('backend.pages.activitylogs.script')
@endsection
