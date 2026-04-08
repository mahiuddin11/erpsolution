@extends('backend.layouts.master')

@section('title')
    Asset List - {{ $title }}
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Asset Warranty List </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('assets.warranty.index'))
                            <li class="breadcrumb-item"><a href="{{ route('assets.warranty.index') }}">Asset Warranty </a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Asset</span></li>
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
                    <h3 class="card-title">Add New Asset</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('assets.warranty.index'))
                            <a class="btn btn-default" href="{{ route('assets.warranty.index') }}"><i
                                    class="fa fa-list"></i>
                                Asset Warranty</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('assets.warranty.store') }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Asset Name * :</label>
                                <select name="assetlist_id" class="form-control" id="">
                                    @foreach ($assetList as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach

                                </select>
                                @error('assetlist_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Start Date:</label>
                                <input type="string" name="form_date" class="form-control" id="validationCustom01"
                                    placeholder="Warranty End Date" value="{{ old('form_date') }}">
                                @error('form_date')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">End Date:</label>
                                <input type="string" name="to_date" class="form-control" id="validationCustom01"
                                    placeholder="Warranty Start Date" value="{{ old('to_date') }}">
                                @error('to_date')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Select Type * :</label>
                                <select name="type" class="form-control">
                                    <option selected disabled>Select Warranty Type</option>
                                    <option value="1">Guarantee</option>
                                    <option value="2">Warranty</option>
                                    <option value="3">Both</option>
                                </select>
                                @error('type')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Description If has both warranty and Guarantee * :</label>
                                <textarea name="desc" class="form-control" id="" cols="10" rows="3">

                                </textarea>
                                @error('desc')
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
