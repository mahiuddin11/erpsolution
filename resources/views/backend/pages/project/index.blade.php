@extends('backend.layouts.master')
@section('title')
Project - {{ $title }}
@endsection

@section('styles')
<style>
    .bootstrap-switch-large {
        width: 200px;
    }
</style>
@endsection
@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    Project </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('project.project.index'))
                    <li class="breadcrumb-item"><a href="{{ route('project.project.index') }}">Project</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Project List</span></li>
                </ol>
            </div><!-- /.col -->
            {{-- @if (\Session::has('errorss'))
            <br>
            <br>
            <div class="col-sm-12">
                <div class="alert alert-warning">
                    <h5>{!! \Session::get('errorss') !!}</h5>
                </div>
            </div><!-- /.container-fluid -->
            @endif --}}
        </div>
        @endsection

        @section('admin-content')
        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Project List</h3>
                        <div class="card-tools">
                            @if (helper::roleAccess('project.project.create'))
                            <a class="btn btn-default" href="{{ route('project.project.create') }}"><i
                                    class="fas fa-plus"></i>
                                Add New</a>
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
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Manager</th>
                                        <th>Budget</th>
                                        {{-- <th>Received</th> --}}
                                        <th>Address</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Status</th>
                                        <th>Condition</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>SL</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Manager</th>
                                        <th>Budget</th>
                                        {{-- <th>Received</th> --}}
                                        <th>Address</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Status</th>
                                        <th>Condition</th>
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

        <div class="modal fade" id="projectcompleate" tabindex="-1" role="dialog" aria-labelledby="projectcompleate"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projectcompleate">Action</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="row" style="padding: 20px;">
                        <div class="col-md-12">
                            <form action="{{route('project.project.complete')}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <input type="date" name="close_date" required class="form-control"
                                            id="validationCustom01" placeholder="close_date">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <input type="hidden" name="projectid" class="projectid">
                                        <button type="submit" class="btn btn-success btn-block">Complete</button>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <button type="button" data-dismiss="modal"
                                            class="btn btn-danger btn-block ">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).on('click', '.complateid', function () {
        var complateid = $('.complateid').attr('dataId');
        console.log(complateid);
        $('.projectid').val(complateid);
    })
        </script>

        @endsection
        @section('scripts')
        @include('backend.pages.project.script')
        @endsection