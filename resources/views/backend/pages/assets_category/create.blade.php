@extends('backend.layouts.master')

@section('title')
    Category - {{ $title }}
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Category </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('assets.category.index'))
                            <li class="breadcrumb-item"><a href="{{ route('assets.category.index') }}">Category List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Category</span></li>
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
                    <h3 class="card-title">Add New Category</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('assets.category.index'))
                            <a class="btn btn-default" href="{{ route('assets.category.index') }}"><i
                                    class="fa fa-list"></i>
                                Category List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('assets.category.store') }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Category Name * :</label>
                                <input type="text" name="category_name" class="form-control" id="validationCustom01"
                                    placeholder="Category Name" value="{{ old('category_name') }}">
                                @error('category_name')
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
