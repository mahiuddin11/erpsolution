@extends('backend.layouts.master')

@section('title')
    Settings - {{ $title }}
@endsection


@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Financial Year List </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('financial.yearindex'))
                            <li class="breadcrumb-item"><a href="{{ route('financial.yearindex') }}">Financial Year
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Finacial Year</span></li>
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
                    <h3 class="card-title">Finacial Year List</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('financial.year.create'))
                            <a class="btn btn-default" href="{{ route('financial.year.create') }}"><i
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

                    <form class="needs-validation" method="POST"
                        action="{{ route('financial.year.update', $editInfo->id) }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6 mb-1">
                                <label for="">Financial Year<span class="text-danger">*</span></label>
                                <input type="text" value="{{ $editInfo->f_year }}" class="form-control input-rounded"
                                    name="f_year">
                            </div>
                            <div class="col-md-6 mb-1">
                                <label for="">Financial Year Start<span class="text-danger">*</span></label>
                                <input type="text" value="{{ $editInfo->f_year_start }}"
                                    class="form-control input-rounded" name="f_year_start">
                            </div>
                            <div class="col-md-6 mb-1">
                                <label for="">Financial Year End<span class="text-danger">*</span></label>
                                <input type="text" value="{{ $editInfo->f_year_end }}" class="form-control input-rounded"
                                    name="f_year_end">
                            </div>
                            <div class="col-md-6">
                                <label for="">Status</label>
                                <select class="form-control mb-1" name="status">
                                    <option selected disabled>Select Status</option>

                                    <option value="yes" @if ($editInfo->status == 'yes') {{ 'selected' }} @endif>
                                        Yes</option>
                                    <option value="no" @if ($editInfo->status == 'no') {{ 'selected' }} @endif>
                                        No</option>

                                </select>
                                @error('status')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <button class="btn btn-info" type="submit"><i class="fa fa-save"></i>&nbsp;Update</button>
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
