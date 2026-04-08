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
                    Project</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('project.projectreturn.index'))
                    <li class="breadcrumb-item"><a href="{{ route('project.projectreturn.index') }}">Project</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Product Return List</span></li>
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
                <h3 class="card-title">Product Return List</h3>
                <div class="card-tools">
                    @php
                    $return = empty($projectreturn->status) ? "Approve":$projectreturn->status;
                    $condition = $project ? $project:"Complete";
                    @endphp
                    @if (helper::roleAccess('project.projectreturn.create'))
                    <a class="btn btn-default" href="{{ route('project.projectreturn.create') }}"><i
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
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Project</th>
                                <th>Branch Id</th>
                                <th>In stock</th>
                                <th>Return</th>
                                <th>Return By</th>
                                <th>status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>SL</th>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Project</th>
                                <th>Branch Id</th>
                                <th>In stock</th>
                                <th>Return</th>
                                <th>Return By</th>
                                <th>status</th>
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

<div class="modal fade" id="projectreturnapprove" tabindex="-1" role="dialog" aria-labelledby="projectreturnapprove"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectreturnapprove">Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row" style="padding: 20px;">
                <div class="col-md-6">
                    <form action="{{route('project.projectreturn.approve')}}" class="pushInput" method="post">
                        @csrf
                        <input type="hidden" value="Approve" name="status" class="btn btn-danger btn-block" />
                        <button type="submit" class="btn btn-success btn-block">Approve</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form action="{{route('project.projectreturn.approve')}}" class="pushInput" method="post">
                        @csrf
                        <input type="hidden" value="Cancel" name="status" class="btn btn-danger btn-block" />
                        <button type="submit" class="btn btn-danger btn-block">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).on('click', '.returnid', function () {
        var id = $(this).attr('dataId');
        var input = '<input type="hidden"  name="projectReturnId" value="' + id + '" />';
        $('.pushInput').append(input);
    })

</script>
@endsection
@section('scripts')
@include('backend.pages.projectreturn.script')
@endsection