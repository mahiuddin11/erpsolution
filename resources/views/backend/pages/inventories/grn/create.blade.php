@extends('backend.layouts.master')

@section('title')
Inventorie - {{ $title }}
@endsection
@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"> Inventorie </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('inventorySetup.purchaseorder.index'))
                    <li class="breadcrumb-item"><a href="{{ route('inventorySetup.purchaseorder.index') }}">Purchase</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Good Received Note</span></li>
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
                <h3 class="card-title">Add New Good Received Note</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('inventory-purchaseorder-list'))
                    <a class="btn btn-default" href="{{ route('inventory-purchaseorder-list') }}"><i
                            class="fa fa-list"></i>
                        Good Received Note List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('inventorySetup.goodrcvnote.store') }}"
                    novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <span class="bg-green" style="padding: 5px; font-weight : bold"
                                for="validationCustom01">Good Received Note * : {{ $grnInv }}</span>
                            <input type="hidden" name="grnCode" class="form-control" id="" value="{{ $grnInv }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label>Date:</label>
                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                <input type="text" name="date" data-toggle="datetimepicker"
                                    value="{{ date('YYYY-mm-dd') }}" class="form-control datetimepicker-input"
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

                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Purchase Voucher * :</label>
                            <select class="form-control select2" id="purreq" name="purchase_voucher">
                                <option selected disabled value="">--Select--</option>
                                
                                @foreach ($Purchases as $key => $value)
                                
                                <option value="{{ $value->id }}">
                                    {{ $value->invoice_no }}
                                </option>
                                @endforeach
                            </select>
                            @error('purchase_voucher')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        {{-- <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Supplier * :</label>
                            <select class="form-control select2" id="supplier_id" name="subblier_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($suppliers as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->supplierCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('subblier_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div> --}}

                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Project * :</label>
                            <select readonly id="branch_id" class="form-control select2" name="project_id">
                                <option selected disabled value="">--Select--</option>

                            </select>
                            @error('project_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>


                    <table class="table table-bordered table-hover" id="show_item">
                        <thead>
                            <tr>
                                <th colspan="8">Select Product Item</th>
                            </tr>
                            <tr>
                                <td class="text-center"><strong>Category</strong></td>
                                <td class="text-center"><strong>Product</strong></td>
                                <td class="text-center"><strong>Type</strong></td>
                                <td class="text-center" style="width:10%"><strong>Request </strong></td>
                                <td class="text-center" style="width:10%"><strong>Approve </strong></td>
                                <td class="text-center" style="width:10%"><strong>Remaining </strong></td>
                                <td class="text-center"><strong>Unit Price</strong></td>
                                <td class="text-center" style="width:20%"><strong>Total</strong></td>
                            </tr>
                        </thead>
                        <tbody id="main-table">
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>

                        <tfoot>

                            <tr>
                                <td class="
                                            text-right">
                                    <strong>Sub-Total</strong>
                                </td>
                                <td class="text-right"><strong class=""></strong></td>
                                <td class="
                                            text-right"><strong class="ttlqty"></strong>
                                </td>
                                <td class="text-right"><strong class="approvett"></strong></td>
                                <td class="text-right"><strong class="remainingtt"></strong></td>
                                <td class="text-right"><strong class="ttlunitprice"></strong></td>
                                <td class="text-right"><strong class="grandtotal"></strong></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="
                        form-row form-group">
                        <div class="col-md-12">
                            <label for="">Note</label>
                            <textarea name="note" class="form-control" name="note" id="" cols="10" rows="4"></textarea>
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

<script type="text/javascript">
    $(document).ready(function () {

        $('#purreq').on('change', function () {
            let id = $(this).val();

            $.ajax({
                url: "{{ route('inventorySetup.goodrcvnote.searchgrn') }}",
                method: 'GET',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id
                },
                dataType: 'json',
                success: function (data) {
                    console.log(data)
                    $('.delrow').remove();
                    $('#main-table').html(data.prdetails);
                    $('#branch_id').html(data.branch);
                    $('#supplier_id').html(data.supplier);
                    $('.reset_unitprice').val('');
                    $('#advancepayment').val(data.purchaseorder);
                    $('.reset_qty').val('');
                    $('.reset_total').val('');
                    $(".reset").val(null).trigger("change");
                    $('.paid_amount').removeAttr('readonly');
                    $('.paid_amount').val('')
                    $('.cart_due').text('');
                    findqtyamoun();
                    findunitamount();
                    findgrandtottal();
                    durculect()
                }
            })
        })


        function durculect() {
            var grandtotal = 0;

            $.each($('.total'), function (index, item) {
                total = number_format($(item).val());
                grandtotal += total;
            });
            var advancepayment = $('#advancepayment').val();
            var due = grandtotal - parseInt(advancepayment);
            $('.cart_due').text(due);
            $('.input-checker').val();
        }

        $('.paymentval').on('keyup change', function () {
            var grandtotal = 0;

            $.each($('.total'), function (index, item) {
                total = number_format($(item).val());
                grandtotal += total;
            });

            var advancepayment = $('#advancepayment').val();
            var due = grandtotal - parseInt(advancepayment);
            var payment = $(this).val();
            if (payment <= due) {
                var amount = due - payment;
            } else {
                // alert('Payment Amount can not greater than ' + due + ' amount');
                alertMessage.error('Payment Amount can not greater than ' + due + ' amount')
                $(this).val('');
                durculect()
            }

            $('.cart_due').text(amount);

        })

        var findqtyamoun = function () {
            var ttlqty = 0;
            var approve = 0;
            var remaining = 0;

            $.each($('.ttlqty'), function () {
                console.log($(this).val());
                qty = number_format($(this).val());
                ttlqty += qty;
            });
            $.each($('.approve'), function () {
                console.log($(this).val());
                qty = number_format($(this).val());
                approve += qty;
            });
            $.each($('.remaining'), function () {
                console.log($(this).val());
                qty = number_format($(this).val());
                remaining += qty;
            });
            $('.ttlqty').text(number_format(ttlqty));
            $('.approvett').text(number_format(approve));
            $('.remainingtt').text(number_format(remaining));

        };

        var findunitamount = function () {
            var ttlunitprice = 0;
            $.each($('.ttlunitprice'), function () {
                unitprice = number_format($(this).val());
                ttlunitprice += unitprice;
            });
            $('.ttlunitprice').text(number_format(ttlunitprice));
        };

        var findgrandtottal = function () {
            var grandtotal = 0;

            $.each($('.total'), function (index, item) {
                total = number_format($(item).val());
                grandtotal += total;
            });

            // let vatE = $('.vat');
            let discountE = $('.discount');
            let paidAmountE = $('.paid_amount');

            let vat = 0; //number_format(vatE.val());
            let discount = number_format(discountE.val());
            let paidAmount = number_format(paidAmountE.val());
            //calculate discount
            let cal_vat = percentageCalculate(grandtotal, vat);
            let cal_grandtotal = grandTotalCalculate(grandtotal, discount, cal_vat);

            let cart_net_total = $('.cart_net_total');

            $('.grandtotal').text(number_format(grandtotal));
            cart_net_total.text(cal_grandtotal);

        };


        $(document).on('click', '.delete_item', function () {
            // if (confirm("Are you sure?")) {
            //     // $(this).parents('tr').remove();
            //     // findqtyamoun();
            //     // findunitamount();
            //     // findgrandtottal();
            // }

            let deleteitem = () => {
                $(this).parents('tr').remove();
                findqtyamoun();
                findunitamount();
                findgrandtottal();
            }

            alertMessage.confirm('You want to remove this', deleteitem);
        });

        // check payment type by joy
        $(document).on('change', '.payment_type', function () {
            const self = $(this);
            const val = self.val();

            if (val == '' || val == null || val == 0) {
                return false;
            }
            checkTypeAndGetAccountInfo(val);

        });

        // get account balance and show by html
        $(document).on('change', '.accounts', function () {
            // settings.transfer.checkBalance
            const self = $(this);
            const val = self.val();

            if (val == '' || val == null || val == 0) {
                return false;
            }
            getBalance(val);
        });

        // Quantity price calculate
        $(document).on('input', '.qty', function () {
            let self = $(this);
            var requestQty = self.closest('tr').find('.ttlqty').val();
            var approve = self.closest('tr').find('.approve').val();
            var totalval = requestQty - approve;
            var value = self.val();
            if (totalval + 1 <= value) {
                // alert('Remaining quantity cannot greater than Requested quantity')
                alertMessage.error('Remaining quantity cannot greater than Requested quantity');
                self.val(0);
            }
            let parent = self.parents('tr');
            let qty = number_format(self.val());

            if (qty == '' || qty == null) {
                $(this).val(0);
                qty = 0;
            }

            let unitPrice = number_format(parent.find('.unitprice').val());

            let total = number_format(unitPrice * qty);

            parent.find('.total').val(number_format(total));
            findqtyamoun();
            findgrandtottal();
            durculect()
        });

        $(document).on('input', '.input-checker', function () {
            var grandtotal = $('.grandtotal').text();
            grandtotal = Number(grandtotal);

            if (isNaN(grandtotal) || grandtotal < 1) {
                // alert('Please Add some item first.');
                alertMessage.error('Please Add some item first.');
                return false;
            }
            findgrandtottal();

        });



        $(document).on('change', '.payment_type', function () {

            let payment_type = $(this).val();
            if (payment_type == '' || payment_type == null) {
                $('#submit').prop('disabled', true);
                $('.paid_amount').prop('readonly', true);
            } else {
                $('.paid_amount').prop('readonly', false);
            }

        });

        $(document).on('keyup', '.paid_amount', function () {
            let paidAmount = number_format($(this).val());
            let balance = number_format($('.balance').val());
            console.log(balance)
            let paymentType = $('.payment_type').val();

            if (paymentType.toLowerCase() == 'cash' && balance < paidAmount) {
                $('#submit').prop('disabled', true);

                $('.payment_amount_error').html(
                    '<span class="error text-red text-bold">Payed amount cannot be greater then balance.</span>'
                );

            } else {
                $('#submit').prop('disabled', false);
                $('.payment_amount_error').html('')
            }
        });

    });

    function dueCalculate(amount, paid_amount) {
        return number_format(number_format(amount) - number_format(paid_amount));
    }

    function grandTotalCalculate(total, discount = 0, vat = 0, result = 0) {
        result = (total + vat) - discount;
        return number_format(result);

    }

    function percentageCalculate(amount, disc) {
        return number_format(amount * disc * .01);
    }

    function number_format(number, decimal = 2) {
        number = Number(number);
        return Number(parseFloat(number).toFixed(decimal));
    }

    function getProductList(cat_id) {
        if (cat_id == '' || cat_id == null || cat_id == 0) {
            return false;
        }
        $.ajax({
            "url": "{{ route('inventorySetup.purchase.getProductList') }}",
            "type": "GET",
            cache: false,
            data: {
                "_token": "{{ csrf_token() }}",
                cat_id: cat_id
            },
            success: function (data) {
                $('#productID').select2();
                $('#productID option').remove();
                $('#productID').append($(data));
                $("#productID").trigger("select2:updated");
            }
        });
    }

    function getUnitPrice(productId) {

        if (productId == '' || productId == null || productId == 0) {
            return false;
        }

        $.ajax({
            "url": "{{ route('inventorySetup.purchase.unitPice') }}",
            "type": "GET",
            cache: false,
            data: {
                "_token": "{{ csrf_token() }}",
                productId: productId
            },
            success: function (data) {
                $("#unitprice").val(data);
            }
        });
    }

    // 
    function checkTypeAndGetAccountInfo(type) {
        if (type == "cash") {
            $.ajax({
                "url": "{{ route('inventorySetup.purchase.accounts') }}",
                "type": "GET",
                cache: false,
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function (data) {
                    let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Account</label>
                                <select name="chart_of_account_id" class="form-control select2 accounts">
                                    ${data}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Balance</label>
                                <input name="balance" type="text" class="form-control balance" placeholder="Ex:31424" readonly />
                            </div>
                        </div>
                    </div>
                    `;
                    $('.account-section').html(html);
                    $('.accounts').select2();
                }
            });
        } else if (type == "check") {
            let html = `<div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Account Number</label>
                        <input name="account_number" type="text" class="form-control" placeholder="Ex:1234234" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Check Number</label>
                        <input name="check_number" type="text" class="form-control" placeholder="Ex:31424" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Bank Name</label>
                        <input name="bank" type="text" class="form-control" placeholder="Ex:Bank Of Asia" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Bank Branch Name</label>
                        <input name="bank_branch" type="text" class="form-control" placeholder="Ex:Dhaka" />
                    </div>
                </div>
            </div>
            `;
            $('.account-section').html(html);
        } else {
            let html = '';
            $('.account-section').html(html);
        }
    }

    //get balance of selected account
    function getBalance(account_id) {
        $.ajax({
            "url": "{{ route('settings.transfer.checkBalance') }}",
            "type": "GET",
            cache: false,
            data: {
                // "_token": "{{ csrf_token() }}",
                account_id: account_id
            },
            success: function (data) {
                $('.balance').val(data);
            }
        });

    }
</script>

@endsection