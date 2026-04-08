@extends('backend.layouts.master')

@section('title')
Cashbook - {{ $title }}
@endsection
@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"> Cashbook </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('payment.customer.index'))
                    <li class="breadcrumb-item"><a href="{{ route('payment.customer.index') }}">Customer Payment
                            List</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>New Customer Payment</span></li>
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
                <h3 class="card-title">Add Customer Payment</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('payment.customer.index'))
                    <a class="btn btn-default" href="{{ route('payment.customer.index') }}"><i class="fa fa-list"></i>
                        Customer Payment List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('payment.customer.store') }}" novalidate>
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
                            <label for="validationCustom01"> Bank Name *:</label>
                            <input type="text" name="bank_name" placeholder="Check no" class="form-control">
                            @error('bank_name')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Check Date *:</label>
                            <div class="input-group date" id="reservationdate1" data-target-input="nearest">
                                <input type="text" name="check_date" data-toggle="datetimepicker"
                                    value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
                                    data-target="#reservationdate1" />
                                <div class="input-group-append" data-target="#reservationdate1"
                                    data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            @error('check_date')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01"> Check no *:</label>
                            <input type="text" name="check_no" placeholder="Check no" class="form-control">
                            @error('check_no')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01"> Branch Name * :</label>
                            <select class="form-control select2" onchange="getCustomerList(this.value)"
                                name="branch_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($branch as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->branchCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('account_branch_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3 customerList" id="custid" style="display: none">
                            <label for="validationCustom01">Customer * :</label>
                        </div>


                        <div class="col-md-4 mb-3 invoicelist" id="invid" style="display: none">
                            <label for="validationCustom01">invoice * :</label>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Account Name * :</label>
                            <select class="form-control select2" id="account_id" name="account_id">
                                <option selected disabled value="">--Select--</option>

                            </select>
                            <span style="color :red; " id="showamount"></span>
                            @error('account_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Amount * :</label>
                            <input type="text" name="amount" id="amount" class="form-control"
                                onkeyup="cehckBalance(this.value)" placeholder="Amount" value="{{ old('amount') }}"
                                required>
                            @error('amount')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom02">Note :</label>
                            <textarea name="note" class="form-control" rows="1" id="validationCustom02"
                                placeholder="Note">{{ old('note') }}</textarea>
                            @error('note')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <button class="btn btn-info" type="submit"><i class="fa fa-save"></i> &nbsp;Save</button>
                </form>
            </div>
        </div>

    </div>
</div>




<script>
    function getCustomerList(branch_id) {
        $.ajax({
            url: "/admin/getAllBranchCustomeList/", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                branch_id: branch_id
            },
            success: function (data) {

                $("#custid").show()
                let html = `
              
                        <div class="form-group">
                            <label>Customer</label>
                            <select name="customer_id" class="form-control customersList select2 customerList" onchange="getInvoiceList(this.value)">
                                ${data}
                            </select>
                        </div>
                   
                `;
                $('.customerList').html(html);
                $('.customersList').select2();
            },
        });

        $.ajax({
            url: "{{route('settings.expense.accountsearch')}}",
            method: "GET",
            data: {
                "_token": "{{csrf_token()}}",
                branch_id: branch_id
            },
            success: function (data) {
                // alert(data);
                $('#account_id').html(data);
            }
        })

    }


    function getInvoiceList(customer_id) {

        $.ajax({
            url: "/admin/getAllDueInvoiceList/", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                customer_id: customer_id
            },
            success: function (val) {
                $("#invid").show()
                let html = `
              
                        <div class="form-group">
                            <label>Invoice</label>
                            <select name="invoice_id" class="form-control invoiceslist select2 invoicelist"
                                onchange="getInvoiceDueBalance(this.value)">
                                ${val}
                            </select>
                    <span style="color :red; " id="showaDueAmount"></span>
                
                </div>
                `;
                $('.invoicelist').html(html);
                $('.invoiceslist').select2();
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
            },
        });
    }


    function getInvoiceDueBalance(sale_id) {

        $.ajax({
            url: "/admin/dueInvoiceAmount/", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                sale_id: sale_id
            },
            success: function (val) {

                if (val > 0) {
                    $("#showaDueAmount").html('<span>This Invoice Due Balance : ' + val + '</span>');
                    $("#showaDueAmount").attr('data-id', val);
                    $("#currentDueBalance").val(val);
                } else {
                    //  console.log(val);
                    var val = 0;
                    $("#showaDueAmount").html('<span>This Invoice Due Balance : ' + val + '</span>');
                    $("#showaDueAmount").attr('data-id', val);
                    $("#currentDueBalance").val(val);
                }

                //   getCustomer(sale_id);

            },
            // error: function() {
            //     alert('Error while request..');
            // }
        });
    }

    function getCustomer(sale_id) {


        $.ajax({
            url: "/admin/getCustomerDetails/", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                sale_id: sale_id
            },
            dataType: 'json',
            success: function (data) {
                $("#customername").val(data.name);
                $("#customerId").val(data.id);
            },

        });
    }

    function cehckBalance(amount) {
        var reminingAmount = $("#showamount").attr('data-id');
        var inoviceDue = $("#showaDueAmount").attr('data-id');
        if ((reminingAmount < parseFloat(amount)) || (inoviceDue < parseFloat(amount))) {
            // lert('Opps !! Please check your account balance and due balance.');
            alertMessage.error(' Please check your account balance and due balance.');
            $("#amount").val('');
        }
    }
</script>

@endsection