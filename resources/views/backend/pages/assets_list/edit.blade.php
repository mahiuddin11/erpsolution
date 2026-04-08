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
                        Assets List </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('assets.category.index'))
                            <li class="breadcrumb-item"><a href="{{ route('assets.category.index') }}">Asset
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Assets Cateogory</span></li>
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
                    <h3 class="card-title">Asset List</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('assets.list.create'))
                            <a class="btn btn-default" href="{{ route('assets.list.create') }}"><i class="fas fa-plus"></i>
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

                    <form class="needs-validation" method="POST" action="{{ route('assets.list.update', $editInfo->id) }}"
                        novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6 mb-1">
                                <label for="">Date<span class="text-danger">*</span></label>
                                <input type="date" value="{{ $editInfo->_date }}" class="form-control input-rounded"
                                    name="_date">
                            </div>
                            <div class="col-md-6 mb-1">
                                <label for="">Asset Name</label>
                                <input type="text" class="form-control input-rounded" name="name"
                                    value="{{ $editInfo->name }}" placeholder="Asset name">
                            </div>
                            <div class="col-md-6 mb-1">
                                <label for="">Asset Category</label>
                                <select class="select2 form-control" name="category_asset_id"
                                    aria-label=".select2-lg example">
                                    <option selected disabled>Select category</option>
                                    @foreach ($assetCat as $value)
                                        <option value="{{ $value->id }}"
                                            {{ $value->id == $editInfo->category_asset_id ? 'selected' : '' }}>
                                            {{ $value->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-1">
                                <label for="">Account Head</label>
                                <select class="select2 form-control accounthead" onchange="availablebalance()"
                                    name="account_id">
                                    <option selected disabled>Select Account</option>

                                    @foreach ($accounts as $value)
                                        <option value="{{ $value->id }}"
                                            {{ $value->id == $editInfo->account_id ? 'selected' : '' }}>
                                            {{ $value->account_name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-success account-message"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Payment * :</label>
                                <select class="form-control select2" name="payment_account" id="">
                                    <x-account :setAccounts="$payments" :selectVal="$editInfo->payment_account" />
                                    <option {{$editInfo->payment_account == 14 ? "selected":""}} value="14">Due</option>
                                </select>
                                @error('payment_account')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-1">
                                <label for="">Quantity(PCs)</label>
                                <input type="number" class="form-control input-rounded" name="qty"
                                    value="{{ $editInfo->qty }}" placeholder="Quantity">
                            </div>
                            <div class="col-md-6 mb-1">
                                <label for="">Amount</label>
                                <input type="number" class="form-control input-rounded" name="amount"
                                    value="{{ $editInfo->amount }}" placeholder="Amount">
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
