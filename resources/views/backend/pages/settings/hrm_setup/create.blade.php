@extends('backend.layouts.master')

@section('title')
Settings - {{$title}}
@endsection
@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"> Settings </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    @if(helper::roleAccess('settings.branch.index'))
                    <li class="breadcrumb-item"><a href="{{ route('settings.hrm.setup.index') }}">Hrm Setup List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Hrm Setup</span></li>
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
                <h3 class="card-title">Add New Setup</h3>
                <div class="card-tools">
                    @if(helper::roleAccess('settings.hrm.setup.index'))
                    <a class="btn btn-default" href="{{ route('settings.hrm.setup.index') }}"><i class="fa fa-list"></i>
                        Hrm Setup List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('settings.branch.store') }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-12">
                            <h3>Allowance</h3>
                        </div>
                        <div class="col-md-3 mb-3 ml-5">
                            <label for="validationCustom01">Medical * :</label>
                            <input type="number" name="medical" class="form-control" id="validationCustom01" placeholder="Medical" value="{{ old('medical') }}">
                            @error('medical')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">Travel * :</label>
                            <input type="number" name="travel" class="form-control" id="validationCustom01" placeholder="Travel" value="{{ old('travel') }}">
                            @error('travel')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">Food * :</label>
                            <input type="number" name="food" class="form-control" id="validationCustom01" placeholder="Food" value="{{ old('food') }}">
                            @error('food')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom02"> Late Minutes * :</label>
                            <input type="number" name="late_minutes" class="form-control" id="validationCustom02" placeholder="Late Minutes" value="{{ old('late_minutes') }}" required>
                            @error('late_minutes')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom02">Monthly Working Days * :</label>
                            <input type="number" name="working_days" class="form-control" id="validationCustom02" placeholder="Days" value="{{ old('working_days') }}" required>
                            @error('working_days')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Phone * :</label>
                            <input type="text" name="phone" class="form-control" id="validationCustom01" placeholder="Phone" value="{{ old('phone') }}" required>
                            @error('phone')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom02">Address* :</label>
                            <input name="address" class="form-control" id="validationCustom02" placeholder="Address" value="{{ old('address') }}" required>
                            @error('address')
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