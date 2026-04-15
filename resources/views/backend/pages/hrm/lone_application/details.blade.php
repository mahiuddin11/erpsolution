@extends('backend.layouts.master')
@section('title')
Hrm - {{$title}}
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
                    Hrm </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('home') }}">Dashboard</a></li>
                    @if(helper::roleAccess('hrm.lone.index'))
                    <li class="breadcrumb-item"><a href="{{route('hrm.lone.index') }}">Hrm</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Loan Application Details</span></li>
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
            <div class="card-header text-center">
                <h3 class="card-title">Loan Application Details</h3>
                <div class="card-tools">
                    @if(helper::roleAccess('hrm.lone.create'))
                    <a class="btn btn-default" href="{{ route('hrm.lone.create') }}"><i
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
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Branch</th>
                                <th>Amount</th>
                                <th>Loan Adjustment</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                           <tr>
                                <td>{{$lone->employee->name}}</td>
                                <td>{{$lone->branch->name}}</td>
                                <td>{{$lone->amount}}</td>
                                <td>{{$lone->lone_adjustment}}</td>
                                <td>{{$lone->reason}}</td>
                                <td>{{$lone->status}}</td>
                            </tr>
                           
                        </tbody>
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
