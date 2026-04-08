@extends('backend.layouts.master')
@section('title')
Cashbook - {{$title}}
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
                    Cashbook </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('home') }}">Dashboard</a></li>
                    @if(helper::roleAccess('inventorySetup.adjust.index'))
                    <li class="breadcrumb-item"><a href="{{route('inventorySetup.adjust.index') }}">Credit Adjust</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Credit Adjustment List</span></li>
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
                <h3 class="card-title">Credit Adjust List</h3>
                <div class="card-tools">
                    @if(helper::roleAccess('inventorySetup.adjustCredit.create'))
                    <a class="btn btn-default" href="{{ route('inventorySetup.adjustCredit.create') }}"><i
                            class="fas fa-plus"></i>Add New</a>
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
                                <th>Account</th>
                                <th>Customer</th>
                                <th>Branch</th>
                                <th>Expire</th>
                                <th>Payment Type</th>
                                <th>Debit</th>
                                <th>Credit</th>
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
                                <th>Account</th>
                                <th>Customer</th>
                                <th>Branch</th>
                                <th>Expire</th>
                                <th>Paymnet Type</th>
                                <th>Debit</th>
                                <th>Credit</th>
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
@endsection
@section('scripts')
@include('backend.pages.inventories.adjust.script')
@endsection