@extends('backend.layouts.master')

@section('title')
Settings - {{$title}}
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
                    @if(helper::roleAccess('settings.sms_setting.index'))
                    <li class="breadcrumb-item"><a href="{{route('settings.sms_setting.index')}}">SMS Setting List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Edit SMS Setting</span></li>
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
                <h3 class="card-title">SMS Setting List</h3>
                <div class="card-tools">
                @if(helper::roleAccess('settings.sms_setting.index'))
                    <a class="btn btn-default" href="{{ route('settings.sms_setting.create') }}"><i class="fas fa-plus"></i>
                @endif
                        Add New</a>
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
                    action="{{ route('settings.sms_setting.update',$editInfo->id) }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Api Key * :</label>
                            <input type="text" name="api_key" class="form-control" id="validationCustom01"
                                placeholder="Api Key " value="{{ $editInfo->api_key }}">
                            @error('api_key')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom02">Api Secret * :</label>
                            <input type="text" name="api_secret" class="form-control" id="validationCustom02"
                                placeholder="Api Secret" value="{{ $editInfo->api_secret  }}" required>
                            @error('api_secret')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Mobile * :</label>
                            <input type="text" name="sender_mobile" class="form-control" id="validationCustom01"
                                placeholder="Mobile" value="{{ $editInfo->sender_mobile  }}" required>
                            @error('sender_mobile')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Sales * :</label>
                            <input type="text" name="sales" class="form-control" id="validationCustom01"
                                placeholder="Sales" value="{{ $editInfo->sales  }}" required>
                            @error('sales')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Purchases * :</label>
                            <input type="text" name="purchases" class="form-control" id="validationCustom01"
                                placeholder="Purchases" value="{{ $editInfo->purchases  }}" required>
                            @error('purchases')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Payment Voucher * :</label>
                            <input type="text" name="payment_voucher" class="form-control" id="validationCustom01"
                                placeholder="Payment Voucher" value="{{ $editInfo->payment_voucher  }}" required>
                            @error('payment_voucher')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Receive Voucher * :</label>
                            <input type="text" name="receive_voucher" class="form-control" id="validationCustom01"
                                placeholder="Receive Voucher" value="{{ $editInfo->receive_voucher  }}" required>
                            @error('receive_voucher')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
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