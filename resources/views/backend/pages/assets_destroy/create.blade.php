@extends('backend.layouts.master')

@section('title')
    Asset Destroy Items - {{ $title }}
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Amsset Destroy Ites </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('assets.destroy.index'))
                            <li class="breadcrumb-item"><a href="{{ route('assets.destroy.index') }}">Asset Destroy Items</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add Destroy items</span></li>
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
                    <h3 class="card-title">Add New Destroy Items</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('assets.destroy.index'))
                            <a class="btn btn-default" href="{{ route('assets.destroy.index') }}"><i class="fa fa-list"></i>
                                Destroy Items</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('assets.destroy.store') }}" novalidate>
                        @csrf
                        <div class="form-row">

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Asset Category * </label>
                                <select class="form-control" name="assetlist_id">
                                    <option selected disabled>Select Category</option>
                                    @foreach ($assetList as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                                @error('assetlist_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Reason * </label>
                                <input type="text" name="reason" class="form-control" id="validationCustom01"
                                    placeholder="Reason" value="{{ old('reason') }}">
                                @error('reason')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Date</label>
                                <input type="date" class="form-control" name="destroy_date">
                                @error('destroy_date')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Quantity</label>
                                <input type="number" name="qty" class="form-control" id="validationCustom01"
                                    placeholder="Quantity" value="{{ old('qty') }}">
                                @error('qty')
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
