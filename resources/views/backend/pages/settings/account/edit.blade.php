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
                        Settings </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('settings.branch.index'))
                            <li class="breadcrumb-item"><a href="{{ route('settings.account.index') }}">Account List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Account</span></li>
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
                    <h3 class="card-title">Account List</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('settings.branch.create'))
                            <a class="btn btn-default" href="{{ route('settings.account.create') }}"><i
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
                        action="{{ route('settings.account.update', $editInfo->id) }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for=""> Account List <span class="text-danger">*</span></label>
                                <select name="parent_id" class="custom-select select2" id="parent_id">
                                    <option selected disabled>--select Account--</option>
                                    @foreach ($accounts as $account)
                                        <option  {{ $editInfo->parent_id == $account->id ? "selected":"" }}
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
                                    placeholder="Account Name" value="{{ $editInfo->account_name }}">
                                @error('account_name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom02"> Account Number :</label>
                                <input type="text" name="account_code" class="form-control" id="validationCustom02"
                                    placeholder="Account Number" value="{{ $editInfo->accountCode }}" required>
                                @error('account_code')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom02">Bank Name (if need) :</label>
                                <input type="text" name="bank_name" class="form-control" id="validationCustom02"
                                    placeholder="Bank Name" value="{{ $editInfo->bank_name }}" required>
                            </div>
                             @if ($editInfo->depreciation != 0)
                             <div class="col-md-6 mb-3" id="depreciation">
                                 <label for="validationCustom02"> Depreciation (%) :</label>
                                 <input type="number" value="{{$editInfo->depreciation}}" name="depreciation" class="form-control" id="validationCustom02"
                                     placeholder=""  >
                             </div>
                             @endif
                        </div>

                        <div class="form-row">
                            <div class="card" style="width: 18rem;">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" {{$editInfo->bill_by_bill == 1 ? "checked":""}} type="checkbox" value="1" name="billbybillpayment" id="flexCheckDefault">
                                        <label class="form-check-label" for="flexCheckDefault">
                                          Bill By Bill Payment
                                        </label>
                                    </div>
                                </div>
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
