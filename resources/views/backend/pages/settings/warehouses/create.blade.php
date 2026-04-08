@extends('backend.layouts.master')

@section('title')
    Settings - {{ $title }}
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
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('settings.warehouses.index'))
                            <li class="breadcrumb-item"><a href="{{ route('settings.warehouses.index') }}">Warehouses
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Warehouses</span></li>
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
                    <h3 class="card-title">Add New Warehouses</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('settings.warehouses.index'))
                            <a class="btn btn-default" href="{{ route('settings.warehouses.index') }}"><i
                                    class="fa fa-list"></i> Warehouses List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('settings.warehouses.store') }}"
                        novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <span class="bg-green" style="padding: 5px; font-weight: bold;"
                                    for="validationCustom01">warehouses Code *: {{ $branchCode }}</span>
                                <input type="hidden" name="branchCode" class="form-control" value="{{ $branchCode }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">warehouses Name *:</label>
                                <select name="parent_id" class="form-control select2" id="">
                                    <option selected value="10000">Not Applicable</option>
                                    @foreach ($parents as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Warehouses Name *:</label>
                                <input type="text" name="name" class="form-control" id="validationCustom01"
                                    placeholder="Warehouses Name" value="{{ old('name') }}">
                                @error('name')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom02">E-mail *:</label>
                                <input type="text" name="email" class="form-control" id="validationCustom02"
                                    placeholder="E-mail" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Phone *:</label>
                                <input type="text" name="phone" class="form-control" id="validationCustom01"
                                    placeholder="Phone" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom02">Address *:</label>
                                <input name="address" class="form-control" id="validationCustom02" placeholder="Address"
                                    value="{{ old('address') }}" required>
                                @error('address')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row  text-right">
                            <div class="col-md-12">
                                <button class="btn btn-info" type="submit"><i class="fa fa-save"></i>
                                    &nbsp;Save</button>
                            </div>

                        </div>
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
