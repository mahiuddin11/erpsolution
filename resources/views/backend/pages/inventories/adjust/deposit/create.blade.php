@extends('backend.layouts.master')

@section('title')
Customer - {{ $title }}
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
                    @if (helper::roleAccess('inventorySetup.adjustCredit.index'))
                    <li class="breadcrumb-item"><a href="{{ route('inventorySetup.adjustCredit.index') }}">Payment
                            List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Payment</span></li>
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
                <h3 class="card-title">Add New Deposit Adjust</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('inventorySetup.adjustDeposit.index'))
                    <a class="btn btn-default" href="{{ route('inventorySetup.adjustDeposit.index') }}"><i class="fa fa-list"></i>
                        Adjust List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('inventorySetup.adjustDeposit.store') }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label>Date:</label>
                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                <input type="text" name="date" data-toggle="datetimepicker" value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input" data-target="#reservationdate" />
                                <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            @error('date')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
{{-- 
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01"> Bank Name*:</label>
                            <input type="text" name="bank_name" placeholder="Check no" class="form-control">
                            @error('bank_name')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div> --}}

                        <div class="col-md-4 mb-3">
                            <label>Check Date *:</label>
                            <div class="input-group date" id="reservationdate1" data-target-input="nearest">
                                <input type="text" name="check_date" data-toggle="datetimepicker" value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input" data-target="#reservationdate1" />
                                <div class="input-group-append" data-target="#reservationdate1" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            @error('check_date')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- <div class="col-md-4 mb-3">
                            <label for="validationCustom01"> Check no *:</label>
                            <input type="text" name="check_no" placeholder="Check no" class="form-control">
                            @error('check_no')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div> --}}
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01"> Branch Name * : * :</label>
                            <select class="form-control select2" name="branch_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($branch as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->branchCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Type * :</label>
                            <select class="form-control" name="payment_type" onchange="showAccountList(this.value)">
                                <option value="Deposit">Deposit</option>
                            </select>
                            @error('payment_type')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

      
                        <div id="accountList" class="col-md-4 mb-3">
                            <label for="validationCustom01">Account Name * :</label>
                            <select class="form-control select2" name="account_id">
                                <option selected disabled value="">--Select--</option>
                          <x-account :setAccounts="$account"  />
                            </select>

                            @error('account_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01"> Customer * :</label>
                            <select class="form-control select2" name="customer_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($customer as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->customerCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Amount * :</label>
                            <input type="text" name="amount" id="amount" class="form-control" placeholder="Amount" value="{{ old('amount') }}" required>
                            @error('amount')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="col-md-4 mb-3">
                            <label for="validationCustom02">Note * :</label>
                            <input name="note" class="form-control" id="validationCustom02" placeholder="Note" value="{{ old('note') }}" required>
                            @error('note')
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


<script>
    function showAccountList(typeofadjustomet) {
        if (typeofadjustomet == 'Deposit') {
            $("#accountList").show(500);
            $("#expireddate").hide(500);
        } else {
            $("#accountList").hide(500);
            $("#expireddate").show(500);
        }
    }

    function getAccountBalance(account_id) {
        $.ajax({
            url: "/admin/getAccountBalance/", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                account_id: account_id
            },
            success: function(val) {
                $("#showamount").html('<span>Cureent Balance : ' + val + '</span>');
                $("#showamount").attr('data-id', val);
                $("#currentBalance").val(val);

            },
            error: function() {
                // lert('Error while request..');
                alertMessage.error(' Error while request..');
            }
        });
    }

    function cehckBalance(amount) {

        var reminingAmount = $("#showamount").attr('data-id');

        if (reminingAmount < parseFloat(amount)) {
            // lert('Opps !! Your desired amount of money is not in the Account...');
            alertMessage.error(' Your desired amount of money is not in the Account...');

            $("#amount").val('');
        }
    }
</script>

@endsection