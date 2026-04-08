@extends('backend.layouts.master')

@section('title')
    Hrm - {{ $title }}
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
                        @if (helper::roleAccess('hrm.award.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.award.index') }}">Award List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Award</span></li>
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
                    <h3 class="card-title">Award Edit</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('hrm.award.create'))
                            <a class="btn btn-default" href="{{ route('hrm.award.create') }}"><i class="fas fa-plus"></i>
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
                    <form class="needs-validation" method="POST" action="{{ route('hrm.award.update', $editInfo->id) }}"
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
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ $editInfo->name }}" name="name">
                                        @error('name')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Employee Name <span class="text-danger">*</span></label>
                                        <select name="employee_id" class="form-control">
                                            @foreach ($employees as $value)
                                                <option @if ($editInfo->employee_id == $value->id) selected @endif;
                                                    value="{{ $value->id }}">{{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('name')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Description <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ $editInfo->desc }}" name="desc">
                                        @error('desc')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Gift Item <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ $editInfo->gift_item }}" name="gift_item">
                                        @error('gift_item')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Date<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control input-rounded"
                                            value="{{ $editInfo->date }}" name="date">
                                        @error('date')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Award By<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ $editInfo->award_by }}" name="award_by">
                                        @error('award_by')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    {{-- <div class="col-md-4 mb-1">
                                        <label for="">Employee Id<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ $->employee_id }}" name="empoyee_id">
                                        @error('empoyee_id')
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
