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
                    @if(helper::roleAccess('hrm.leave.index'))
                    <li class="breadcrumb-item"><a href="{{route('hrm.leave.index') }}">Hrm</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Leave Approve Application Details</span></li>
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
                <h3 class="card-title">Leave Application Details</h3>
                {{-- <div class="card-tools">
                    @if(helper::roleAccess('hrm.leave.create'))
                    <a class="btn btn-default" href="{{ route('hrm.leave.create') }}"><i
                            class="fas fa-plus"></i>Add New</a>
                    @endif
                    <span id="buttons"></span>
                    <a class="btn btn-tool btn-default" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </a>
                    <a class="btn btn-tool btn-default" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </a>
                </div> --}}
            </div>
            <!-- /.card-header -->
            <div class="card-body">
            <form action="{{route('hrm.leaveapprove.approve',$leave->id)}}" method="get">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Branch</th>
                                <th>Application Date</th>
                                <th>Application End Date</th>
                                <th>Reason</th>
                                <th>Payment Status</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                                <td>{{$leave->employee->name}}</td>
                                <td>{{$leave->branch->name}}</td>
                                <td>{{$leave->apply_date}}</td>
                                <td>{{$leave->end_date}}</td>
                                <td>{{$leave->reason}}</td>
                                <td>           
                                    <select name="payment_status" class="form-control">
                                    <option {{ $leave->payment_status == 'paid' ? 'selected' : '' }} value="paid">Paid
                                    </option>
                                    <option {{ $leave->payment_status == 'non-paid' ? 'selected' : '' }} value="non-paid">
                                        Non-Paid</option>
                                </select>
                                </td>
                                <td>{{$leave->status}}</td>
                            </tr>
                           
                        </tbody>
                    </table>
                </div>
                <div class="row text-center">
                    <div class="col-md-12">
                        <button class="btn btn-info" type="submit">Approve</button>
                    </div> 
                </div>
            </form>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">

            </div>
        </div>
    </div>
    <!-- /.col-->
</div>
@endsection
