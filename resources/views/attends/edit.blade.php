{{--@extends('attends.layout')
@section('content')
        <div class="row" style="margin-top: 20px">
            <div class="col-lg-12 margin-tb">
                <div style="text-align:center;">
                    <h4>Edit Emplyee Information</h4>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{url('attends.index')}}">Back</a>
                </div>
            </div>    
        </div>
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Woops@</strong>There are some problem in your input.<br><br>
                @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </div>
        @endif
        <form action="{{ route('attends.update',$attend->id)}}" method="POST" enctype="multipart/form-data" style="margin-top: 20px">

            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="from-group">
                        <strong>Emplyee_ID</strong>
                        <input type="text" name="emplyee_id" class="form-control" value="{{ $attend->emplyee_id}}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="from-group">
                        <strong>Date</strong>
                        <input type="text" name="date" class="form-control" placeholder="Date">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="from-group">
                        <strong>Sign_In</strong>
                        <input type="text" name="sign_in" class="form-control" placeholder="Sign_In">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="from-group">
                        <strong>Sign_Out</strong>
                        <input type="text" name="sign_out" class="form-control" placeholder="Sign_out">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="from-group">
                        <strong>Status</strong>
                        <input type="text" name="status" class="form-control" value="{{ $attend->status}}">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12" style="margin-top: 20px">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        
        </form>
@endsection --}}

@extends('attends.layouts')

@section('content')
{{--Employee - {{ $attend }}--}}
@endsection
@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"> Hrm </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('hrm.employee.index'))
                    <li class="breadcrumb-item"><a href="{{ route('hrm.employee.index') }}">employee
                            List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New employee</span></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection

@section('content')


<div class="row">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Add New Employee</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('hrm.employee.index'))
                    <a class="btn btn-default" href="{{ route('hrm.employee.index') }}"><i class="fa fa-list"></i>
                        Employee List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('hrm.employee.store') }}" novalidate>
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Basic details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-1">
                                    <label for="">Emplyee_ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control input-rounded" value="{{old('emplyee_id')}}"
                                        name="name">
                                    @error('emplyee_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-1">
                                    <label for="">Date</label>
                                    <input type="date" class="form-control input-rounded" value="{{old('dob')}}"
                                        name="dob">
                                    @error('dob')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-1">
                                    <label for="">Sign_In <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control input-rounded" value="{{old('sign_in')}}"
                                        name="name">
                                    @error('sign_in')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-1">
                                    <label for="">Sign_Out <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control input-rounded" value="{{old('sign_out')}}"
                                        name="name">
                                    @error('sign_out')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-1">
                                    <label for="">Status <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control input-rounded" value="{{old('status')}}"
                                        name="name">
                                    @error('status')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>


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