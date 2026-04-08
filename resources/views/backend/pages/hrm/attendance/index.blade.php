@extends('backend.layouts.master')
@section('title')
Hrm - {{$title}}
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
                    <li class="breadcrumb-item"><a href="{{route('home') }}">Dashboard</a></li>
                    @if(helper::roleAccess('hrm.attendance.index'))
                        <li class="breadcrumb-item"><a href="{{route('hrm.attendance.index') }}">Hrm</a>
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
                    @if(helper::roleAccess('hrm.attendance.create'))
                        <a class="btn btn-default" href="{{ route('hrm.attendance.create') }}"><i class="fas fa-plus"></i>
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
@endsection