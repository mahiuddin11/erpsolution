@extends('backend.layouts.master')
@section('title')
    Purchase - {{ $title }}
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
                    <h1 class="m-0">Purchase Return</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><span>Purchase Return List</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Purchase Return List</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('inventorySetup.purchase.return.create'))
                            <a class="btn btn-default" href="{{ route('inventorySetup.purchase.return.create') }}"><i
                                    class="fas fa-plus"></i>Add
                                New</a>
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
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="systemDatatable" class="display table-hover table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Return Invoice No</th>

                                    <th>Date</th>
                                    <th>Branch</th>
                                    <th>Warehouse</th>
                                    <th>Supplier</th>
                                    <th>Return Qty</th>
                                    <th>Return Amount</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>SL</th>
                                    <th>Return Invoice No</th>

                                    <th>Date</th>
                                    <th>Branch</th>
                                    <th>Warehouse</th>
                                    <th>Supplier</th>
                                    <th>Return Qty</th>
                                    <th>Return Amount</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('backend.pages.inventories.purchase.return.script')
@endsection
