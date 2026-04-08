@extends('backend.layouts.master')

@section('title')
    Financial Year List - {{ $title }}
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Financial Year </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('financial.yearindex'))
                            <li class="breadcrumb-item"><a href="{{ route('financial.yearindex') }}">Financial Year List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Financial Year</span></li>
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
                    <h3 class="card-title">Add New Financial Year</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('financial.year.index'))
                            <a class="btn btn-default" href="{{ route('financial.year.index') }}"><i class="fa fa-list"></i>
                                Financial Year List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('financial.year.store') }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Financial Year * :</label>
                                <input type="text" name="f_year" class="form-control" id="validationCustom01"
                                    placeholder="Financial Year" value="{{ old('f_year') }}">
                                @error('f_year')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Financial Year Start* :</label>
                                <input type="text" class="form-control" name="f_year_start">
                                @error('f_year_start')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Financial Year End* :</label>
                                <input type="text" class="form-control" name="f_year_end">
                                @error('f_year_end')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Status * :</label>
                                <select name="status" class="form-control">
                                    <option selected disabled>Select status</option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                                @error('status')
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
