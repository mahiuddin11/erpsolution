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
                    HRM </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if(helper::roleAccess('hrm.attendance.index'))
                    <li class="breadcrumb-item"><a href="attendance{{route('hrm.attendance.index')}}">Attendance List</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Edit Attendance</span></li>
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
                <h3 class="card-title">Attendance Edit</h3>
                <div class="card-tools">
                    @if(helper::roleAccess('hrm.attendance.create'))
                    <a class="btn btn-default" href="{{ route('hrm.attendance.create') }}"><i class="fas fa-plus"></i>
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
                <form class="needs-validation" method="POST" action="{{ route('hrm.attendance.update', $model->id) }}"
                    novalidate>
                    @csrf
                     <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Basic Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- <div class="col-md-4 mb-1">
                                    <label for="">Emplyee_ID <span class="text-danger">*</span></label>
                                    <select name="emplyee_id" class="form-control">
                                        <option value="active">Select_ID</option>
                                
                                    @foreach ($employees as $key => $value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control input-rounded" value="{{$model->emplyee_id}}" name="emplyee_id">
                                
                                    @error('emplyee_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div> --}}
                                <div class="col-md-4 mb-1">
                                    <label for="">Date</label>
                                    <input type="date" class="form-control input-rounded" value="{{$model->date}}" name="date">
                                    @error('date')
                                        <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                    
                                <div class="col-md-4 mb-1">
                                    <label for="">Sign_In</label>
                                    {{--<textarea value="{{old('time')}}" name="sign_in" class="form-control input-rounded"></textarea>--}}
                                    <input type="time" name="sign_in" class="form-control" placeholder="Sign_in" value="{{$model->sign_in}}">
                                    @error('sign_in')
                                        <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-1">
                                    <label for="">Sign_Out</label>
                                    {{--<textarea value="{{old('sign_out')}}" name="sign_out"
                                        class="form-control input-rounded"></textarea>--}}
                                    <input type="time" name="sign_out" class="form-control" placeholder="Sign_out" value="{{$model->sign_out}}">
                                    @error('sign_out')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                                {{-- <div class="col-md-4 mb-1">
                                    <label for="">Status</label> --}}
                                    {{--<textarea value="{{old('permanent_address')}}" name="permanent_address"
                                        class="form-control input-rounded"></textarea>
                                    <input type="text" name="status" class="form-control" placeholder="Status">--}}
                                    {{-- <select name="status" class="form-control">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    @error('status')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div> --}}
                            </div>
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