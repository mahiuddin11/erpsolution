@extends('backend.layouts.master')

@section('title')
    Award - {{ $title }}
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
                        @if (helper::roleAccess('hrm.award.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.award.index') }}">Award
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Award</span></li>
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
                    <h3 class="card-title">Add New Award</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('hrm.award.index'))
                            <a class="btn btn-default" href="{{ route('hrm.award.index') }}"><i class="fa fa-list"></i>
                                Award List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('hrm.award.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Basic details</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-1">
                                        <label for="">Award Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-rounded" value="{{ old('name') }}"
                                            name="name">
                                        @error('name')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Employee Id<span class="text-danger">*</span></label>
                                        <select name="employee_id" class="form-control">
                                            <option selected disabled> Please Select Employee</option>
                                            @foreach ($employees as $value)
                                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('employee_id')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Gift Item</label>
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ old('gift_item') }}" name="gift_item">
                                        @error('gift_item')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Date</label>
                                        <input type="date" class="form-control input-rounded"
                                            value="{{ old('date') }}" name="date">
                                        @error('date')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Award By</label>
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ old('award_by') }}" name="award_by">
                                        @error('award_by')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Award Description</label>
                                        <textarea name="desc" class="form-control" id="" cols="5" rows="2">

                                        </textarea>
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
