@extends('backend.layouts.master')

@section('title')
    Inventory - {{ $title }}
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Inventory </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('inventorySetup.purchase.index'))
                            <li class="breadcrumb-item"><a href="{{ route('inventorySetup.purchase.index') }}">Purchase
                                    List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Purchase</span></li>
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
                    <h3 class="card-title">Add New Purchase</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('inventorySetup.category.index'))
                            <a class="btn btn-default" href="{{ route('inventorySetup.purchase.index') }}"><i
                                    class="fa fa-list"></i>
                                Purchase List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('inventorySetup.purchase.store') }}"
                        novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-2 mb-3">
                                <label>Invoice Number :</label>
                                <input class="bg-green form-control" readonly=""
                                    style="padding: 5px; font-weight : bold; width: 100%" value="{{ $invoice_no }} ">
                                <input type="hidden" name="invoice_no" class="form-control" id=""
                                    value="{{ $invoice_no }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label>Custom Invoice:</label>
                                <input type="text" name="custom_invoice" required class="form-control" id=""
                                    value="">
                                @error('custom_invoice')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label>Date:</label>
                                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                    <input type="text" name="date" readonly data-toggle="datetimepicker"
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
                            <div class="col-md-2 mb-3">
                                <label for="validationCustom01">Branch * :</label>
                                <select class="form-control select2" id="branch_id" name="branch_id">
                                    <option selected disabled value="">--Select Branch--</option>
                                    @foreach ($branch as $key => $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->branchCode . ' - ' . $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-2 mb-3">
                                <label for="validationCustom02">Sub-Warehouse * :</label>
                                <select class="form-control select2" id="sub_warehouse_id" name="sub_warehouse_id">
                                    <option selected disabled value="">--Select Sub-Warehouse--</option>
                                    @foreach ($wearhouses as $key => $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->branchCode . ' - ' . $value->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('sub_warehouse_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- <div class="col-md-3 mb-3">
                                <label>Supplier * :</label>
                                <select class="form-control select2 supid" name="supplier_id">
                                    <option selected disabled value="">--Select Supplier--</option>
                                    @foreach ($supplier as $key => $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->supplierCode . ' - ' . $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div> --}}
                            <div class="col-md-2 mb-3">
                                <label for="ledger_id">Ledger * :
                                    <button type="button" class="btn btn-sm btn-primary ml-2" data-toggle="modal"
                                        data-target="#addSupplierModal">
                                        +
                                    </button>
                                </label>
                                <select class="form-control select2 supid" name="ledger_id" id="ledger_id">
                                    <option selected disabled value="">--Select Ledger--</option>
                                    <x-account :setAccounts="$ledgers" />
                                </select>
                                @error('ledger_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-group">
                                    <label>Payment Type *: </label>
                                    <select class="form-control select2 payment_type" name="payment_type">
                                        <option selected disabled value="">--Payment Type--</option>
                                        <option value="Adjustment">Adjustment</option>
                                        <option selected value="due">Due</option>
                                    </select>
                                    @error('payment_type')
                                        <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{-- html load by js --}}
                            <div class="account-section col-md-12"></div>

                            <table class="table table-bordered table-hover" id="show_item">
                                <thead>
                                    <tr>
                                        <th colspan="8">Select Product Item</th>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><strong>Category</strong></td>
                                        <td class="text-center"><strong>Product</strong></td>
                                        <td class="text-center"><strong>Type</strong></td>
                                        <td class="text-center"><strong>Quantity</strong></td>
                                        <td class="text-center"><strong>Unit Price</strong></td>
                                        <td class="text-center"><strong>Total</strong></td>
                                        <td class="text-center"><strong>Action</strong></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select onchange="getProductList(this.value)"
                                                class="select2 form-control catName reset" id="form-field-select-3"
                                                data-placeholder="Search Category">
                                                <option disabled selected>---Select Category---</option>
                                                <?php
                                            foreach ($category_info as $eachInfo) :
                                                ?>
                                                <option catName="{{ $eachInfo->name }}" value="{{ $eachInfo->id }}">
                                                    {{ $eachInfo->name }}</option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="select2 form-control proName reset" id="productID"
                                                data-placeholder="Search Product" onchange="getUnitPrice(this.value)">
                                                <option disabled selected>---Select Product---</option>
                                            </select>
                                            <span class="text-success purchaseprice"></span>
                                        </td>
                                        <td>
                                            <select class="select2 form-control purchasetype" id="purchasetype"
                                                data-placeholder="Search Product">
                                                @foreach (config('purchaseType') as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-success purchasetypeerror"></span>
                                        </td>
                                        <td>
                                            <input type="number" step="any"
                                                class="form-control text-right qty reset_qty" placeholder="Qty"
                                                min="0">
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="0" id="unitprice"
                                                class="form-control text-right unitprice reset_unitprice"
                                                placeholder="Unit Price" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="any" readonly
                                                class="form-control text-right total reset_total" id="total"
                                                placeholder="Total">
                                        </td>
                                        <td>
                                            <a id="add_item" class="btn btn-info" style="white-space: nowrap"
                                                href="javascript:;" title="Add Item">
                                                <i class="fa fa-plus"></i>
                                                Add Item
                                            </a>
                                        </td>
                                    </tr>

                                </tbody>
                                <tfoot>

                                    <tr>
                                        <td class="text-right"><strong>Sub-Total(BDT)</strong></td>
                                        <td class="text-right"><strong class=""></strong></td>
                                        <td class="text-right"><strong class=""></strong></td>
                                        <td class="text-right"><strong class="ttlqty"></strong>
                                        </td>
                                        <td class="text-right"><strong class="ttlunitprice"></strong></td>
                                        <td class="text-right"><strong class="grandtotal"></strong></td>
                                        <td class="text-right"><strong class=""></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="
                                                row mb-2">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="input-group">
                                        <textarea cols="100" rows="3" class="form-control" name="narration" placeholder="Narration"
                                            type="text"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <input type="hidden" name="cart_vat" class="input_vat">
                                <input type="hidden" name="input_net_total" class="input_net_total">
                                <input type="hidden" name="cart_due" class="input_due">

                                <table class="table table-bordered table-hover" id="cart_output">
                                    <tr>
                                        <th><span>Total</span></th>
                                        <th class="text-right"><span class="grandtotal"></span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th><span>Discount(-)</span></th>
                                        <th class="text-right">
                                            <input type="number" step="any"
                                                class="form-control discount input-checker" name="discount"
                                                placeholder="Ex:5">
                                            @error('discount')
                                                <span class=" error text-red text-bold">{{ $message }}</span>
                                            @enderror
                                        </th>
                                    </tr>
                                    {{-- <tr>
                                    <th><span>Vat</span></th>
                                    <th class="text-right">
                                        <input type="number" step="any" class="form-control vat input-checker"
                                            name="vat" placeholder="Ex:5" readonly>
                                        @error('vat')
                                        <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </th>
                                </tr> --}}
                                    <tr>
                                        <th><span>Net Total</span></th>
                                        <th class="text-right"><span class="cart_net_total"></span>
                                        </th>
                                    </tr>

                                    <tr class="d-none">
                                        <th><span>Payment(-) *</span></th>
                                        <th class="text-right">
                                            <input type="number" step="any" id="paymentTypeCheck"
                                                class="form-control  paid_amount input-checker" name="paid_amount"
                                                placeholder="Ex:5">
                                            <div class="payment_amount_error"></div>
                                            @error('paid_amount')
                                                <span class=" error text-red text-bold">{{ $message }}</span>
                                            @enderror
                                        </th>
                                    </tr>
                                    <tr id="duevalid">
                                        <th><span>Total Due</span></th>
                                        <th class="text-right"><span class="cart_due"></span>
                                        </th>
                                    </tr>
                                </table>
                                <!-- /.card -->
                            </div>

                        </div>
                        <div class="form-group float-right">
                            <button class="btn btn-info " type="submit" id="submit">
                                <i class="fa fa-save"></i>&nbsp; &nbsp; Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </div>

    <div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog" aria-labelledby="addSupplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSupplierModalLabel">Add Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" id="addSupplierForm"
                    action="{{ route('inventorySetup.purchase.supplierCreate') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="validationCustom01">Supplier Name * :</label>
                            <input type="text" name="name" class="form-control" id="validationCustom01"
                                placeholder="Supplier Name" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="validationCustom02"> E-mail :</label>
                            <input type="text" name="email" class="form-control" id="validationCustom02"
                                placeholder="E-mail" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="validationCustom01">Phone * :</label>
                            <input type="text" name="phone" class="form-control" id="validationCustom01"
                                placeholder="Phone" value="{{ old('phone') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="validationCustom01">BIN :</label>
                            <input type="text" name="specialNumber" class="form-control" id="validationCustom01"
                                placeholder="BIN Number" value="{{ old('specialNumber') }}">
                        </div>
                        <div class="form-group">
                            <label for="validationCustom02">Address :</label>
                            <input name="address" class="form-control" id="validationCustom02" placeholder="Address"
                                value="{{ old('address') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- /.col-->

    <script type="text/javascript">
        $(document).ready(function() {
            // Supplier  Create 
            $('#addSupplierForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#addSupplierModal').modal('hide');
                            $('select[name="ledger_id"]').append(
                                `<option value="${response.accounts.id}" selected>${response.accounts.account_name}</option>`
                            );
                        } else {
                            alert('Error adding Unit');
                        }
                    },
                    error: function(error) {
                        alert('An error occurred');
                    }
                });
                $("button[type='submit']").prop('disabled', false);
            });
        });
        $(document).ready(function() {

            $(document).on('change', '#purchase_type', function() {

                let purchasetype = $('#purchase_type').val();
                if (purchasetype == "Project") {
                    $('.typeproject').removeClass('d-none');
                    $('.typebranch').addClass('d-none');
                } else {
                    $('.typebranch').removeClass('d-none');
                    $('.typeproject').addClass('d-none');
                }

            });

            var findqtyamoun = function() {

                var ttlqty = 0;
                $.each($('.ttlqty'), function() {
                    qty = number_format($(this).val());
                    ttlqty += qty;
                });
                $('.ttlqty').text(number_format(ttlqty));

            };



            var findunitamount = function() {
                var ttlunitprice = 0;
                $.each($('.ttlunitprice'), function() {
                    unitprice = number_format($(this).val());
                    ttlunitprice += unitprice;
                });
                $('.ttlunitprice').text(number_format(ttlunitprice));
            };

            var findgrandtottal = function() {
                var grandtotal = 0;

                $.each($('.total'), function(index, item) {
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
                let cal_due = dueCalculate(cal_grandtotal, paidAmount);

                let cart_net_total = $('.cart_net_total');
                let cart_due = $('.cart_due');
                let paid_amount = $('.paid_amount');

                $('.grandtotal').text(number_format(grandtotal));
                cart_net_total.text(cal_grandtotal);
                cart_due.text(cal_due);
                $('.input_vat').val(cal_vat);
                $('.input_net_total').val(cal_grandtotal);

                let paymenttypes = $('.payment_type').val();
                if (paymenttypes.toLowerCase() == 'cash' || paymenttypes.toLowerCase() == 'check') {
                    paid_amount.val(cal_grandtotal);
                    $('#submit').prop('disabled', false);
                }
                $('.input_due').val(cal_due);

            };


            $(document).on('click', '#add_item', function() {
                var parent = $(this).parents('tr');

                var supid = $('.supid').val();
                var purchasetypeval = $('.purchasetype').find('option:selected').val();
                var purchasetypetext = $('.purchasetype').find('option:selected').text();

                var catId = $('.catName').val();
                var catName = $(".catName").find('option:selected').text();

                var proId = $('.proName').val();
                var proName = $(".proName").find('option:selected').text();

var qty = parseFloat(parent.find('.qty').val());
var unitprice = parseFloat(parent.find('.unitprice').val());




                if (supid == '' || supid == null) {
                    alertMessage.error("Supplier can't be empty.");
                    return false;
                }

                if (purchasetypeval == '' || purchasetypeval == null) {
                    alertMessage.error("Please Select Type.");
                    return false;
                }
                if (catId == '' || catId == null) {
                    alertMessage.error("Category can't be empty.");
                    return false;
                }
                if (proId == '' || proId == null || proId == 'all') {
                    alertMessage.error("Product can't be empty.");
                    return false;
                }

                // start check duplicate product  
                let seaschproduct = $('#productID option:selected')[0].getAttribute("value");
                let tbody = $('tbody').find(".new_item" + seaschproduct).length;
                let tbody2 = $('tbody').find("new_item" + seaschproduct);
                console.log(tbody);

                if (tbody > 0) {
                    alertMessage.error('This product already exist');
                    return;
                }
                // end check duplicate product  

                if (qty == '' || qty == null || qty == 0) {
                    alertMessage.error('Quantity cannot be empty');
                    return false;
                } else {
                    var total = qty * unitprice;

                    var grandtotal = 0;

                    $.each($('.checktotal'), function(index, item) {

                        totaltt = number_format($(item).val());
                        grandtotal += totaltt;
                    });

                    let accountAmountCheck = total + grandtotal;

                    let paymenttypes = $('.payment_type').val();

                    if (paymenttypes != null) {
                        if (paymenttypes.toLowerCase() == 'cash') {
                            var balance = $('.balance').val();
                            var account = $('.accounts').val();
                            if (account != null) {
                                if (accountAmountCheck >= balance) {
                                    alertMessage.error(
                                        'purchase product amount can not greater than account balance');
                                    return;
                                }
                            } else {
                                alertMessage.error('Please Select Your payment Account');
                                return
                            }
                        } else if (paymenttypes.toLowerCase() == "check") {

                            var accountnum = $('.accountnum').val();
                            var checknum = $('.checknum').val();
                            var banknum = $('.banknum').val();
                            var bankbranchnum = $('.bankbranchnum').val();

                            if ((accountnum == "") || (checknum == "") || (banknum == "") || (
                                    bankbranchnum == "")) {
                                alertMessage.error('Please Enter Your Check Information');
                                return
                            }

                        }
                    } else {
                        alertMessage.error('Please Complete your basic required field');
                        return
                    }

                    const row = `
                    <tr class="new_item${proId}">
                        <td style="padding-left:15px;">${catName}<input type="hidden" name="catName[]" value="${catId}"></td>
                        <td class="text-right">${proName}<input type="hidden" class="add_quantity " product-id="${proId}" name="proName[]" value="${proId}"></td>
                        <td class="text-right">${purchasetypetext}<input type="hidden" name="purchasetype[]" value="${purchasetypeval}"></td>
                        <td class="text-right">${qty}<input type="hidden" class="ttlqty" name="qty[]" value="${qty}"></td>
                        <td class="text-right">${unitprice}<input type="hidden" class="ttlunitprice" name="unitprice[]" value="${unitprice}">
                        </td>
                        <td class="text-right">${total}
                            <input type="hidden" class="total checktotal" name="total[]" value="${total}">
                        </td>
                        <td>
                            <a del_id="${proId}" class="delete_item btn form-control btn-danger" href="javascript:;" title="">
                                <i class="fa fa-times"></i>&nbsp;Remove
                            </a>
                        </td>
                    </tr>
                `;
                    $("#show_item tbody").append(row);
                }

                $('.reset_unitprice').val('');
                $('.reset_qty').val('');
                $('.reset_total').val('');
                $(".reset").val(null).trigger("change");

                findqtyamoun();
                findunitamount();
                findgrandtottal();
            });

            $(document).on('click', '.delete_item', function() {

                let deleteitem = () => {
                    $(this).parents('tr').remove();
                    findqtyamoun();
                    findunitamount();
                    findgrandtottal();
                }

                alertMessage.confirm('You want to remove this', deleteitem);

            });

            // check payment type by joy
            $(document).on('change', '.payment_type', function() {
                const self = $(this);
                const val = self.val();

                if (val == '' || val == null || val == 0) {
                    return false;
                }
                checkTypeAndGetAccountInfo(val);

            });

            // get account balance and show by html
            $(document).on('change', '.accounts', function() {
                // settings.transfer.checkBalance
                const self = $(this);
                const val = self.val();

                if (val == '' || val == null || val == 0) {
                    return false;
                }
                getBalance(val);
            });

// Quantity price calculate
$(document).on('input', '.qty', function() {
    let self = $(this);
    let parent = self.parents('tr');

    let qty = parseFloat(self.val());
    if (isNaN(qty) || qty <= 0) {
        qty = 1;
        self.val(qty);
    }

    let unitPrice = parseFloat(parent.find('.unitprice').val());
    if (isNaN(unitPrice) || unitPrice <= 0) {
        unitPrice = 1;
        parent.find('.unitprice').val(unitPrice);
    }

    let total = unitPrice * qty;
    parent.find('.total').val(total.toFixed(2));
});

// Unit price to quantity calculate
$(document).on('input', '.unitprice', function() {
    let self = $(this);
    let parent = self.parents('tr');

    let unitPrice = parseFloat(self.val());
    if (isNaN(unitPrice) || unitPrice <= 0) {
        unitPrice = 1;
        self.val(unitPrice);
    }

    let qty = parseFloat(parent.find('.qty').val());
    if (isNaN(qty) || qty <= 0) {
        qty = 1;
        parent.find('.qty').val(qty);
    }

    let total = unitPrice * qty;
    parent.find('.total').val(total.toFixed(2));
});


            $(document).on('input', '.input-checker', function() {
                var grandtotal = $('.grandtotal').text();
                grandtotal = Number(grandtotal);

                if (isNaN(grandtotal) || grandtotal < 1) {
                    alertMessage.error('Please Add some item first.');
                    return false;
                }
                findgrandtottal();

            });

            if ($('.payment_type').val() == '' || $('.payment_type').val() == null) {
                $('#submit').prop('disabled', true);
                $('.paid_amount').prop('readonly', true);
            } else {
                $('.paid_amount').prop('readonly', false);

            }

            $(document).on('change', '.payment_type', function() {
                let payment_type = $(this).val();

                if (payment_type == '' || payment_type == null) {
                    $('#submit').prop('disabled', true);
                    $('.paid_amount').prop('readonly', true);
                } else {
                    $('.paid_amount').prop('readonly', false);
                }


                if (payment_type.toLowerCase() == "cash") {
                    $('#paymentTypeCheck').prop('readonly', true);
                    $('#submit').prop('disabled', true);
                    $('#account_id').show();
                    $('#duevalid').hide();

                } else if (payment_type.toLowerCase() == "check") {
                    $('#paymentTypeCheck').prop('readonly', true);
                    $('#submit').prop('disabled', true);
                    $('#account_id').hide();
                    $('#duevalid').hide();
                } else {
                    $('#paymentTypeCheck').prop('readonly', true);
                    findgrandtottal();
                    $('#paymentTypeCheck').val('');
                    $('#submit').prop('disabled', false);
                    $('#account_id').hide();
                    $('#duevalid').show();
                }
            });

            $(document).on('keyup', '.paid_amount', function() {
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
                success: function(data) {
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
            var supplier_id = $('.supid').val();
            if (supplier_id == '' || supplier_id == null) {
                alertMessage.error("Supplier can't be empty.");
                return false;
            }
            // alert('saf');
            $.ajax({
                "url": "{{ route('inventorySetup.purchase.unitPice') }}",
                "type": "GET",
                dataType: 'json',
                cache: false,
                data: {
                    "_token": "{{ csrf_token() }}",
                    productId: productId,
                    supplier_id: supplier_id
                },
                success: function(data) {
                    if (data.purchase_price == 0) {
                        $("#unitprice").attr('readonly', false);
                        $("#unitprice").val(data.purchase_price);
                        $(".purchaseprice").html("Last PP :" + data.lastPurchasePrice);
                    } else {
                        $("#unitprice").attr('readonly', true);
                        $("#unitprice").val(data.purchase_price);
                        $(".purchaseprice").html("Last PP :" + data.lastPurchasePrice);
                    }
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
                    success: function(data) {
                        let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Account</label>
                                <select name="chart_of_account_id" class="form-control select2 accounts">
                                    <option disabled selected>All Account</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_name }}</option>

                                        @if ($account->subAccount->isNotEmpty())
                                            @foreach ($account->subAccount as $subaccount)
                                                <option value="{{ $subaccount->id }}">-{{ $subaccount->account_name }}
                                                </option>

                                                @if ($subaccount->subAccount->isNotEmpty())
                                                    @foreach ($subaccount->subAccount as $subaccount2)
                                                        <option value="{{ $subaccount2->id }}">
                                                            --{{ $subaccount2->account_name }}</option>
                                                        @if ($subaccount2->subAccount->isNotEmpty())
                                                            @foreach ($subaccount2->subAccount as $subaccount3)
                                                                <option value="{{ $subaccount3->id }}" disabled>
                                                                    ---{{ $subaccount3->account_name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
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
                        <input name="account_number" type="text" class="form-control accountnum" placeholder="Ex:1234234" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Check Number</label>
                        <input name="check_number" type="text" class="form-control checknum" placeholder="Ex:31424" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Bank Name</label>
                        <input name="bank" type="text" class="form-control banknum" placeholder="Ex:Bank Of Asia" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Bank Branch Name</label>
                        <input name="bank_branch" type="text" class="form-control bankbranchnum" placeholder="Ex:Dhaka" />
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
                success: function(data) {
                    $('.balance').val(data);
                }
            });

        }
    </script>
@endsection
