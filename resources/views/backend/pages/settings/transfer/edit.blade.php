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
                    <li class="breadcrumb-item"><a href="{{ route('settings.branch.index') }}">Branch List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Edit Branch</span></li>
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
                <h3 class="card-title">Branch List</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('settings.branch.create'))
                    <a class="btn btn-default" href="{{ route('settings.branch.create') }}"><i class="fas fa-plus"></i>
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
                    action="{{ route('settings.transfer.update', $balancetransfar->id) }}" novalidate>
                    @csrf
                    <div class="form-row">

                        <div class="col-md-4 mb-3">

                            <label>Date:</label>
                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                <input type="text" name="date" data-toggle="datetimepicker"
                                    value="{{ $balancetransfar->date }}" class="form-control datetimepicker-input"
                                    data-target="#reservationdate" />
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
                            <label for="validationCustom01">Branch Name * :</label>
                            <select class="form-control select2" id="branch_id" name="branch_id">
                                <option selected disabled value="">--Select--</option>

                                @foreach ($branch as $key => $value)
                                <option {{ $balancetransfar->branch_id == $value->id ? 'selected' : '' }}
                                    value="{{ $value->id }}">
                                    {{ $value->branchCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">From Account * :</label>
                            <select class="form-control select2" name="from_account_id" id="from_account_id"
                                onchange="getAccountBalance(this.value)">
                                <option selected disabled value="">--Select--</option>

                                @foreach ($accounts as $key => $value)
                                <option {{ $balancetransfar->from_account_id == $value->id ? 'selected' : '' }}
                                    value="{{ $value->id }}">
                                    {{ $value->accountCode . ' - ' . $value->account_name }}</option>
                                @endforeach
                            </select>
                            <span style="color :red; " data-id="{{ $remainingBalance }}" id="showamount">Cureent
                                Balance: {{ $remainingBalance }}</span>
                            @error('from_account_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">To Account Name * :</label>
                            <select class="form-control select2" name="to_account_id" id="to_account_id"
                                onchange="checkAccountSelector()">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($accounts as $key => $value)
                                <option {{ $balancetransfar->to_account_id == $value->id ? 'selected' : '' }}
                                    value="{{ $value->id }}">
                                    {{ $value->accountCode . ' - ' . $value->account_name }}</option>
                                @endforeach
                            </select>

                            @error('from_account_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Amount * :</label>
                            <input type="text" value="{{ $balancetransfar->amount }}" name="amount"
                                onkeyup="cehckBalance(this.value)" class="form-control" id="amount" placeholder="Amount"
                                value="{{ old('amount') }}" required>
                            @error('amount')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom02">Note :</label>
                            <textarea name="note" rows="1" class="form-control" id="validationCustom02"
                                placeholder="Note" value="{{ old('note') }}"
                                required>{{ $balancetransfar->note }}</textarea>
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
                $('#to_account_id').val('').trigger("change");
            },
            error: function () {
                // lert('Please select Account Name*')
                alertMessage.error('Please select Account Name*');
            }
        });
    }

    function cehckBalance(amount) {

        var reminingAmount = $("#showamount").attr('data-id');

        if (reminingAmount < parseFloat(amount)) {
            // lert('Opps !! Your desired amount of money is not in the Account...');
            alertMessage.error('Your desired amount of money is not in the Account...');
            $("#amount").val('');
        }
    }


    function checkAccountSelector() {
        var from_account_id = document.getElementById("from_account_id").value;
        var to_account_id = document.getElementById("to_account_id").value;

        if (from_account_id == to_account_id) {
            // lert('Opps !! Account can not be same');
            alertMessage.error('Account can not be same');
            $('#to_account_id').val('').trigger("change");
        }

    }

    $(document).ready(function () {
        $('#branch_id').on('change', function () {
            let branch_id = $(this).val();
            $.ajax({
                url: "{{route('settings.expense.accountsearch')}}",
                method: "GET",
                data: {
                    "_token": "{{csrf_token()}}",
                    branch_id: branch_id
                },
                success: function (data) {
                    // alert(data);
                    $('#from_account_id').html(data);
                }
            })
        })
    })
</script>



@endsection