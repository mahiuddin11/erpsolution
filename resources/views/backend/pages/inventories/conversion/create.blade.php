@extends('backend.layouts.master')

@section('title')
    Inventory - {{ $title }}
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Inventory </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('inventorySetup.conversion.index'))
                            <li class="breadcrumb-item"><a href="{{ route('inventorySetup.conversion.index') }}">conversion
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Conversion</span></li>
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
                    <h3 class="card-title">Add New Conversion</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('inventorySetup.conversion.index'))
                            <a class="btn btn-default" href="{{ route('inventorySetup.conversion.index') }}"><i
                                    class="fa fa-list"></i>
                                conversion List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('inventorySetup.conversion.store') }}"
                        novalidate>
                        @csrf
                        <div class="form-row">

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Title * :</label>
                                <input type="text" name="title" class="form-control" id="validationCustom01"
                                    placeholder="Name" value="{{ old('title') }}">
                                @error('title')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Rate * :</label>
                                <input type="text" name="rate" class="form-control" id="validationCustom01"
                                    placeholder="Rate" value="{{ old('rate') }}">
                                @error('rate')
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
