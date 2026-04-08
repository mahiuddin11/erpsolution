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
                    <li class="breadcrumb-item"><a href="{{ route('inventorySetup.adjustCredit.index') }}">Customer</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Return Deposit</span></li>
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
                <h3 class="card-title">Add New Return Deposit </h3>
                <div class="card-tools">
                    @if (helper::roleAccess('inventorySetup.returnDeposit.returnindex'))
                    <a class="btn btn-default" href="{{ route('inventorySetup.returnDeposit.returnindex') }}"><i
                            class="fa fa-list"></i>
                        Return Deposit List</a>
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
                    action="{{ route('inventorySetup.returnDeposit.returnstore') }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label>Date:</label>
                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                <input type="text" name="date" data-toggle="datetimepicker" value="{{ date('Y-m-d') }}"
                                    class="form-control datetimepicker-input" data-target="#reservationdate" />
                                <div class="input-group-append" data-target="#reservationdate"
                                    data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            @error('date')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01"> Customer * :</label>
                            <select onchange="getdepositeAmount(this.value)" class="form-control select2"
                                name="customer_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($customer as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->customerCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                            <span class="error  text-red " id="amountalt" data-val=""></span>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01"> Branch Name * : * :</label>
                            <select  class="form-control select2" name="branch_id">
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


                        <div class="col-md-4 mb-4 ">
                            <label for="validationCustom01">Accounts *:</label>
                            <select onchange="getAccountBalance(this.value)" name="account_id"
                                class="form-control accountsList select2" id="account_id">
                                <option selected disabled>-- Select -- </option>
                                <x-account :setAccounts="$account"  />
                            </select>
                            <span style="color :red; " id="showamount" data-val=""></span>
                            @error('account_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Credit * :</label>
                            <input type="number" name="credit" id="credit" onkeyup="checkbalance(this.value)"
                                class="form-control" placeholder="Amount" value="{{ old('credit') }}" required>
                            @error('credit')
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

    function getAccountList(branchId) {
        $.ajax({
            url: "/admin/getAllAccountHead/", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                branchId: branchId
            },
            success: function (data) {
                $('#account_id').html(data);
                $('.accountsList').select2();
                $('#showamount').text('');
                $("#showamount").attr('data-id', "");
            },
        });
    }

    function getdepositeAmount(countomerid) {

        $.ajax({
            url: "{{route('customer.deposite.balance')}}", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                countomerid: countomerid
            },
            success: function (data) {
                $('#amountalt').text('Available Amount: ' + data);
                $('#amountalt').attr('data-val', data);
            },
        });
    }

    function getAccountBalance(account_id) {
        $.ajax({
            url: "/admin/getAccountBalance/", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                account_id: account_id
            },
            success: function (val) {
                $("#showamount").html('<span>Cureent Balance : ' + val + '</span>');
                $("#showamount").attr('data-id', val);
                $("#currentBalance").val(val);
                $("#credit").val('');
            },
            error: function () {
                // lert('Error while request..');
                alertMessage.error('Error while request..');

            }
        });
    }

    function checkbalance(amount) {
        var account = $('#showamount').attr('data-id');
        var customer = $('#amountalt').attr('data-val');

        if ((amount > parseInt(customer)) || (amount > parseInt(account))) {
            $('#credit').val('');
            alertMessage.error('Amount is not available');
        }
    }
</script>

@endsection