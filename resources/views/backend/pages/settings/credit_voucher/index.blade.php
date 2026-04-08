@extends('backend.layouts.master')
@section('title')
    Account - {{ $title }}
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
                        Account </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('settings.credit.voucher.index'))
                            <li class="breadcrumb-item"><a href="{{ route('settings.credit.voucher.index') }}">Receive
                                    Voucher</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Receive Voucher List</span></li>
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
                    <h3 class="card-title">Receive Voucher List</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('settings.credit.voucher.create'))
                            <a class="btn btn-default" href="{{ route('settings.credit.voucher.create') }}"><i
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
                                    <th>Voucher no</th>
                                    <th>Amount</th>
                                    <th>Project</th>
                                    <th>Approved By</th>
                                    <th>Admin Viewer</th>
                                    <th>Update By</th>
                                    <th>Date</th>
                                    <th>Note</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>SL</th>
                                    <th>Voucher no</th>
                                    <th>Amount</th>
                                    <th>Project</th>
                                    <th>Approved By</th>
                                    <th>Admin Viewer</th>
                                    <th>Update By</th>
                                    <th>Date</th>
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
    @include('backend.pages.settings.credit_voucher.script')
@endsection
