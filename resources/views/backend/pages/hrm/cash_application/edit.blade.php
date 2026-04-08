@extends('backend.layouts.master')

@section('title')
Hrm - {{$title}}
@endsection


@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    Hrm </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if(helper::roleAccess('settings.adjust.index'))
                    <li class="breadcrumb-item"><a href="{{route('hrm.cashapplicaon.index')}}">Cash Requisition List</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Edit Cash Requisition</span></li>
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
                <h3 class="card-title">Cash Requisition Edit</h3>
                <div class="card-tools">
                    @if(helper::roleAccess('hrm.cashapplicaon.create'))
                    <a class="btn btn-default" href="{{ route('hrm.cashapplicaon.create') }}"><i class="fas fa-plus"></i>
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
                <form class="needs-validation" method="POST" action="{{ route('hrm.cashapplicaon.update',$model->id) }} " enctype="multipart/form-data"
                    novalidate>
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-1">
                            <label for=""> EMPLOYEE NAME <span class="text-danger">*</span></label>
                            <select  class="select2 form-control select2-lg" aria-label=".select2-lg example"
                                name="employee_id">
                                <option selected >Select employee</option>
                                @foreach ($employees as $employee)
                                <option {{$model->employee_id == $employee->id ? 'selected':""}}
                                    value="{{$employee->id}}">{{$employee->name}}
                                </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
               
                        <div class="col-md-6 mb-1">
                            <label for="">Amount</label>
                            <input type="number" class="form-control input-rounded" name="amount" value="{{$model->amount}}" >
                            @error('amount')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    
                        <div class="col-md-6 mb-1">
                            <label for="">Cash Requisition Reason</label>
                            <textarea name="reason"  cols="4" rows="4" class="form-control">{{$model->reason}}</textarea>
                            @error('reason')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                       
                    </div>

                    <button class="btn btn-info" type="submit"><i class="fa fa-save"></i> &nbsp;Save</button>
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