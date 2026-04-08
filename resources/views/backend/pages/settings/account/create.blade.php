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
                        @if (helper::roleAccess('settings.account.index'))
                            <li class="breadcrumb-item"><a href="{{ route('settings.account.index') }}">Account List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Account</span></li>
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
                    <h3 class="card-title">Add New Account</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('settings.account.index'))
                            <a class="btn btn-default" href="{{ route('settings.account.index') }}"><i
                                    class="fa fa-list"></i>
                                Account List</a>
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
                
                <div class="card-body">
                    <form class="needs-validation" method="POST" action="{{ route('settings.account.store') }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <span class="bg-green" style="padding: 5px; font-weight : bold"
                                    for="validationCustom01">Account Code * : {{ $accountCode }}</span>
                                <input type="hidden" name="accountCode" class="form-control" id=""
                                    value="{{ $accountCode }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for=""> Account List <span class="text-danger">*</span></label>
                                <select name="parent_id" class="custom-select select2" id="parent_id">
                                    <option selected disabled>--select Account--</option>
                                    @foreach ($accounts as $account)
                                        <option 
                                            value="{{ $account->id }}">
                                            {{ $account->account_name }} {{ $account->parent->account_name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Account Name * :</label>
                                <input type="text" name="account_name" class="form-control" id="validationCustom01"
                                    placeholder="Account Name" value="{{ old('account_name') }}">
                                @error('account_name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom02"> Account Number :</label>
                                <input type="text" name="account_code" class="form-control" id="validationCustom02"
                                    placeholder="Account Number" value="{{ old('account_code') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom02"> Bank Name (if need) :</label>
                                <input type="text" name="bank_name" class="form-control" id="validationCustom02"
                                    placeholder="Bank Name" value="{{ old('bank_name') }}" required>
                            </div>

                            <div class="col-md-6 mb-3 d-none" id="depreciation">
                                <label for="validationCustom02"> Depreciation (%) :</label>
                                <input type="number" name="depreciation" class="form-control" id="validationCustom02"
                                    placeholder="" value="{{ old('depreciation') }}">
                            </div>

                            <select class="d-none" id="accountcheck">
                                <option value="2">sdf</option>
                                <x-account :setAccounts="$getFixasset" />
                            </select>

                        </div>
                        <div class="form-row">
                            <div class="card" style="width: 18rem;">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="billbybillpayment" id="flexCheckDefault">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            Bill By Bill Payment
                                        </label>
                                    </div>
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

    <script>
        $(document).ready(function() {
            $('#parent_id').on('change', function() {
                let val = $(this).val();
                var sum = [];
                $.each($('#accountcheck option'), function(k, v) {
                    sum.push($(this).val());
                });
                if ($.inArray(val, sum) != -1) {
                    $('#depreciation').removeClass('d-none');
                } else {
                    $('#depreciation').addClass('d-none');
                }
            })

        });
    </script>
@endsection
