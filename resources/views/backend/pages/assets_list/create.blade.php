@extends('backend.layouts.master')

@section('title')
    Asset List - {{ $title }}
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Asset List </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('assets.list.index'))
                            <li class="breadcrumb-item"><a href="{{ route('assets.list.index') }}">Asset List</a></li>
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
                        @if (helper::roleAccess('assets.list.index'))
                            <a class="btn btn-default" href="{{ route('assets.list.index') }}"><i class="fa fa-list"></i>
                                Asset List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('assets.list.store') }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Date * :</label>
                                <input type="date" name="_date" class="form-control" id="validationCustom01"
                                    placeholder="Asset Name" value="{{ old('date') ?? date('Y-m-d') }}">
                                @error('_date')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Asset Name * :</label>
                                <input type="text" name="name" class="form-control" id="validationCustom01"
                                    placeholder="Asset Name" value="{{ old('name') }}">
                                @error('name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Asset Category * :</label>
                                <select class="form-control" name="category_asset_id" id="">
                                    @foreach ($assetCat as $assetCat)
                                    <option value="{{$assetCat->id}}">{{$assetCat->category_name}}</option>
                                    @endforeach
                                </select>
                                @error('category_asset_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Account Head * :</label>
                                <select class="form-control select2" name="account_id" id="">
                                        <x-account :setAccounts="$accounts" />
                                </select>
                                @error('account_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Payment * :</label>
                                <select class="form-control select2" name="payment_account" id="">
                                    <x-account :setAccounts="$payments" />
                                    <option value="14">Due</option>
                                </select>
                     
                                @error('payment_account')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Quantity:</label>
                                <input type="number" name="qty" class="form-control" id="validationCustom01"
                                    placeholder="Quantity" value="{{ old('qty') }}">
                                @error('qty')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Amount:</label>
                                <input type="text" name="amount" class="form-control" id="validationCustom01"
                                    placeholder="Amount" value="{{ old('amount') }}">
                                @error('amount')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Status * :</label>
                                <select name="status" class="form-control">
                                    <option selected disabled>Select status</option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                                @error('status')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div> --}}

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
