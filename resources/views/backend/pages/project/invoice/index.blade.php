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
                    Invoice </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('project.invoiceCreate.index'))
                    <li class="breadcrumb-item"><a href="{{ route('project.invoiceCreate.index') }}">Invoice</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Invoice List</span></li>
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
                        <h3 class="card-title">Invoice List</h3>
                        <div class="card-tools">
                            @if (helper::roleAccess('project.invoiceCreate.create'))
                            <a class="btn btn-default" href="{{ route('project.invoiceCreate.create') }}"><i
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
                                        <th>Code</th>
                                        <th>Branch</th>
                                        <th>Project</th>
                                        <th>Customer</th>
                                        <th>Profit</th>
                                        <th>Value</th>
                                        <th>Note</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th>Code</th>
                                        <th>Branch</th>
                                        <th>Project</th>
                                        <th>Customer</th>
                                        <th>Profit</th>
                                        <th>Value</th>
                                        <th>Note</th>
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
    </div>
</div>

@endsection
@section('scripts')
@include('backend.pages.project.invoice.script')
@endsection