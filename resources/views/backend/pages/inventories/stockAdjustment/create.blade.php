@extends('backend.layouts.master')

@section('title')
    Inventory - {{ $title }}
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Inventory</h1>
                </div>


                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">Dashboard</a>
                        </li>

                        @if (helper::roleAccess('inventorySetup.stockAdjustment.index'))
                            <li class="breadcrumb-item">
                                <a href="{{ route('inventorySetup.stockAdjustment.index') }}">
                                    Stock Adjustment List
                                </a>
                            </li>
                        @endif

                        <li class="breadcrumb-item active">
                            <span>Add New Stock Adjustment</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Add New Adjustment</h3>


                    <div class="card-tools">
                        @if (helper::roleAccess('inventorySetup.stockAdjustment.index'))
                            <a class="btn btn-default" href="{{ route('inventorySetup.stockAdjustment.index') }}">
                                <i class="fa fa-list"></i>
                                Stock Adjustment List
                            </a>
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
                    <form class="needs-validation" method="POST"
                        action="{{ route('inventorySetup.stockAdjustment.store') }}" novalidate>

                        @csrf

                        <div class="form-row">
                            {{-- Invoice Number --}}
                            <div class="col-md-2 mb-3">
                                <label>Invoice Number :</label>

                                <input class="bg-green form-control" readonly
                                    style="padding: 5px; font-weight : bold; width: 100%" value="{{ $invoice_no }}">

                                <input type="hidden" name="invoice_no" class="form-control" value="{{ $invoice_no }}">
                            </div>

                            {{-- Date --}}
                            <div class="col-md-3 mb-3">
                                <label>Date:</label>

                                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                    <input type="text" name="date" data-toggle="datetimepicker"
                                        value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
                                        data-target="#reservationdate" />

                                    <div class="input-group-append" data-target="#reservationdate"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>

                                @error('date')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Branch --}}
                            <div class="col-md-3 mb-3">
                                <label>Branch * :</label>

                                <select class="form-control select2" id="branch_id" name="branch_id">
                                    <option selected disabled value="">--Select Branch--</option>

                                    @foreach ($branches as $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('branch_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Warehouse --}}
                            <div class="col-md-3 mb-3">
                                <label>Warehouse * :</label>

                                <select class="form-control select2" id="warehouse_id" name="warehouse_id"
                                    data-placeholder="--Select Warehouse--">
                                    <option selected disabled value="">--Select Warehouse--</option>
                                </select>

                                @error('warehouse_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Adjustment Type --}}
                            <div class="col-md-3 mb-3">
                                <div class="form-group">
                                    <label>Adjustment Type * :</label>

                                    <select class="form-control select2" name="adjustment_type">
                                        <option selected disabled value="">--Adjustment Type--</option>
                                        <option value="Gain">Gain</option>
                                        <option value="Loss">Loss</option>
                                        <option value="Damage">Damage</option>
                                    </select>

                                    @error('adjustment_type')
                                        <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- html load by js --}}
                            <div class="account-section col-md-12"></div>

                            {{-- Product Table --}}
                            <table class="table table-bordered table-hover" id="show_item">
                                <thead>
                                    <tr>
                                        <th colspan="9">Select Product Item</th>
                                    </tr>

                                    <tr>
                                        <td class="text-center"><strong>Category</strong></td>
                                        <td class="text-center"><strong>Product</strong></td>
                                        <td class="text-center"><strong>Product Type</strong></td>
                                        <td class="text-center"><strong>Quantity</strong></td>
                                        <td class="text-center"><strong>Unit Price</strong></td>
                                        <td class="text-center"><strong>Total</strong></td>
                                        <td class="text-center"><strong>Action</strong></td>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        {{-- Category --}}
                                        <td>
                                            <select onchange="getProductList(this.value)"
                                                class="select2 form-control catName reset" id="form-field-select-3"
                                                data-placeholder="Search Category">
                                                <option disabled selected>---Select Category---</option>

                                                @foreach ($category_info as $eachInfo)
                                                    <option catName="{{ $eachInfo->name }}" value="{{ $eachInfo->id }}">
                                                        {{ $eachInfo->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        {{-- Product --}}
                                        <td>
                                            <select class="select2 form-control proName reset" id="productID"
                                                data-placeholder="Search Product" onchange="getUnitPrice(this.value)">
                                                <option disabled selected>---Select Product---</option>
                                            </select>
                                        </td>

                                        {{-- Product Type --}}
                                        <td>
                                            <select class="select2 form-control purchaseType reset" id="purchaseType"
                                                data-placeholder="Select Type">
                                                <option disabled selected value="">--Type--</option>
                                                <option value="local">Local</option>
                                                <option value="imported">Imported</option>
                                            </select>
                                        </td>

                                        {{-- Quantity --}}
                                        <td>
                                            <input type="number" step="any"
                                                class="form-control text-right qty reset_qty" placeholder="Qty"
                                                min="0">
                                        </td>

                                        {{-- Unit Price --}}
                                        <td>
                                            <input type="number" step="any" readonly min="0" id="unitprice"
                                                class="form-control text-right unitprice reset_unitprice"
                                                placeholder="Unit Price">
                                        </td>

                                        {{-- Total --}}
                                        <td>
                                            <input type="number" step="any" readonly
                                                class="form-control text-right total reset_total" id="total"
                                                placeholder="Total">
                                        </td>

                                        {{-- Add Item --}}
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
                                        <td class="text-right"><strong class="ttlqty"></strong></td>
                                        <td class="text-right"><strong class="ttlunitprice"></strong></td>
                                        <td class="text-right"><strong class="grandtotal"></strong></td>
                                        <td class="text-right"><strong class=""></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Reason + Total --}}
                        <div class="row mb-2">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Reason * :</label>

                                    @error('narration')
                                        <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror

                                    <div class="input-group">
                                        <textarea cols="100" rows="3" class="form-control" name="narration" placeholder="Type here the reason"
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
                                        <th class="text-right"><span class="grandtotal"></span></th>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- Save --}}
                        <div class="form-group float-right">
                            <button class="btn btn-info" type="submit">
                                <i class="fa fa-save"></i>
                                &nbsp; &nbsp; Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {

            /*
            |--------------------------------------------------------------------------
            | Quantity Total
            |--------------------------------------------------------------------------
            */

            var findqtyamoun = function() {
                var ttlqty = 0;

                $.each($('.ttlqty'), function() {
                    qty = number_format($(this).val());
                    ttlqty += qty;
                });

                $('.ttlqty').text(number_format(ttlqty));
            };

            /*
            |--------------------------------------------------------------------------
            | Unit Price Total
            |--------------------------------------------------------------------------
            */

            var findunitamount = function() {
                var ttlunitprice = 0;

                $.each($('.ttlunitprice'), function() {
                    unitprice = number_format($(this).val());
                    ttlunitprice += unitprice;
                });

                $('.ttlunitprice').text(number_format(ttlunitprice));
            };

            /*
            |--------------------------------------------------------------------------
            | Grand Total
            |--------------------------------------------------------------------------
            */

            var findgrandtottal = function() {
                var grandtotal = 0;

                $.each($('.total'), function(index, item) {
                    total = number_format($(item).val());
                    grandtotal += total;
                });

                let discountE = $('.discount');
                let paidAmountE = $('.paid_amount');
                let vat = 0;
                let discount = number_format(discountE.val());
                let paidAmount = number_format(paidAmountE.val());

                let cal_vat = percentageCalculate(grandtotal, vat);
                let cal_grandtotal = grandTotalCalculate(
                    grandtotal,
                    discount,
                    cal_vat
                );
                let cal_due = dueCalculate(cal_grandtotal, paidAmount);

                let cart_net_total = $('.cart_net_total');
                let cart_due = $('.cart_due');

                $('.grandtotal').text(number_format(grandtotal));
                cart_net_total.text(cal_grandtotal);
                cart_due.text(cal_due);

                $('.input_vat').val(cal_vat);
                $('.input_net_total').val(cal_grandtotal);
                $('.input_due').val(cal_due);
            };

            /*
            |--------------------------------------------------------------------------
            | Add Item
            |--------------------------------------------------------------------------
            */

            $(document).on('click', '#add_item', function() {
                var parent = $(this).parents('tr');
                var catId = $('.catName').val();
                var catName = $(".catName").find('option:selected').attr('catName');
                var proId = $('.proName').val();
                var proName = $(".proName").find('option:selected').attr('proName');
                var purchaseType = $('.purchaseType').val();
                var qty = number_format(parent.find('.qty').val());
                var unitprice = number_format(parent.find('.unitprice').val());

                if (purchaseType == '' || purchaseType == null) {
                    alertMessage.error('Please select Product Type (Local/Import).');
                    return false;
                }

                if (qty == '' || qty == null || qty == 0) {
                    return false;
                } else {
                    var total = qty * unitprice;

                    const row = `
                    <tr class="new_item${proId}">
                        <td style="padding-left:15px;">
                            ${catName}
                            <input type="hidden" name="catName[]" value="${catId}">
                        </td>
                        <td class="text-right">
                            ${proName}
                            <input type="hidden" class="add_quantity" name="proName[]" value="${proId}">
                        </td>
                        <td class="text-right">
                            ${purchaseType}
                            <input type="hidden" name="purchaseType[]" value="${purchaseType}">
                        </td>
                        <td class="text-right">
                            ${qty}
                            <input type="hidden" class="ttlqty" name="qty[]" value="${qty}">
                        </td>
                        <td class="text-right">
                            ${unitprice}
                            <input type="hidden" class="ttlunitprice" name="unitprice[]" value="${unitprice}">
                        </td>
                        <td class="text-right">
                            ${total}
                            <input type="hidden" class="total" name="total[]" value="${total}">
                        </td>
                        <td>
                            <a del_id="${proId}" class="delete_item btn form-control btn-danger"
                                href="javascript:;" title="">
                                <i class="fa fa-times"></i>
                                &nbsp;Remove
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

            /*
            |--------------------------------------------------------------------------
            | Delete Item
            |--------------------------------------------------------------------------
            */

            $(document).on('click', '.delete_item', function() {
                let deleteitem = () => {
                    $(this).parents('tr').remove();
                    findqtyamoun();
                    findunitamount();
                    findgrandtottal();
                }

                alertMessage.confirm('You want to remove this', deleteitem);
            });

            /*
            |--------------------------------------------------------------------------
            | Payment Type
            |--------------------------------------------------------------------------
            */

            $(document).on('change', '.payment_type', function() {
                const self = $(this);
                const val = self.val();

                if (val == '' || val == null || val == 0) {
                    return false;
                }

                checkTypeAndGetAccountInfo(val);
            });

            /*
            |--------------------------------------------------------------------------
            | Account
            |--------------------------------------------------------------------------
            */

            $(document).on('change', '.accounts', function() {
                const self = $(this);
                const val = self.val();

                if (val == '' || val == null || val == 0) {
                    return false;
                }

                getBalance(val);
            });

            /*
            |--------------------------------------------------------------------------
            | Quantity Change
            |--------------------------------------------------------------------------
            */

            $(document).on('input', '.qty', function() {
                let self = $(this);
                let parent = self.parents('tr');
                let qty = number_format(self.val());

                if (qty == '' || qty == null) {
                    $(this).val(1);
                    qty = 1;
                }

                let unitPrice = number_format(parent.find('.unitprice').val());
                let total = number_format(unitPrice * qty);

                parent.find('.total').val(number_format(total));
            });

            /*
            |--------------------------------------------------------------------------
            | Input Checker
            |--------------------------------------------------------------------------
            */

            $(document).on('input', '.input-checker', function() {
                var grandtotal = $('.grandtotal').text();
                grandtotal = Number(grandtotal);

                if (isNaN(grandtotal) || grandtotal < 1) {
                    alertMessage.error('Please Add some item first.');
                    return false;
                }

                findgrandtottal();
            });

            /*
            |--------------------------------------------------------------------------
            | Payment Initial State
            |--------------------------------------------------------------------------
            */

            if ($('.payment_type').val() == '' || $('.payment_type').val() == null) {
                $('#submit').prop('disabled', true);
                $('.paid_amount').prop('readonly', true);
            } else {
                $('.paid_amount').prop('readonly', false);
            }

            /*
            |--------------------------------------------------------------------------
            | Payment Type Change
            |--------------------------------------------------------------------------
            */

            $(document).on('change', '.payment_type', function() {
                let payment_type = $(this).val();

                if (payment_type == '' || payment_type == null) {
                    $('#submit').prop('disabled', true);
                    $('.paid_amount').prop('readonly', true);
                } else {
                    $('.paid_amount').prop('readonly', false);
                }
            });

            /*
            |--------------------------------------------------------------------------
            | Paid Amount
            |--------------------------------------------------------------------------
            */

            $(document).on('keyup', '.paid_amount', function() {
                let paidAmount = number_format($(this).val());
                let balance = number_format($('.balance').val());
                let paymentType = $('.payment_type').val();

                if (
                    paymentType &&
                    paymentType.toLowerCase() == 'cash' &&
                    balance < paidAmount
                ) {
                    $('#submit').prop('disabled', true);

                    $('.payment_amount_error').html(
                        '<span class="error text-red text-bold">' +
                        'Payed amount cannot be greater then balance.' +
                        '</span>'
                    );
                } else {
                    $('#submit').prop('disabled', false);
                    $('.payment_amount_error').html('');
                }
            });

            /*
            |--------------------------------------------------------------------------
            | Branch -> Warehouse
            |--------------------------------------------------------------------------
            */

            let warehouseLoading = false;

            $(document).on('change', '#branch_id', function(e) {
                var branchId = $(this).val();

                console.log('Branch ID Selected:', branchId);

                /*
                |--------------------------------------------------------------------------
                | Branch Empty
                |--------------------------------------------------------------------------
                */

                if (branchId == '' || branchId == null) {
                    $('#warehouse_id').html(
                        '<option selected disabled value="">--Select Warehouse--</option>'
                    );

                    $('#warehouse_id').trigger('change.select2');

                    return false;
                }

                /*
                |--------------------------------------------------------------------------
                | Prevent Duplicate AJAX
                |--------------------------------------------------------------------------
                */

                if (warehouseLoading) {
                    return false;
                }

                warehouseLoading = true;

                /*
                |--------------------------------------------------------------------------
                | Loading Option
                |--------------------------------------------------------------------------
                */

                $('#warehouse_id').html(
                    '<option selected disabled value="">Loading Warehouse...</option>'
                );

                $('#warehouse_id').trigger('change.select2');

                /*
                |--------------------------------------------------------------------------
                | Get Warehouse
                |--------------------------------------------------------------------------
                */

                $.ajax({
                    url: "{{ route('inventorySetup.stockAdjustment.getWarehouseList') }}",
                    type: "GET",
                    cache: false,
                    data: {
                        branch_id: branchId
                    },

                    success: function(data) {
                        console.log('Warehouse Data Received:', data);

                        /*
                        |--------------------------------------------------------------------------
                        | Reset Warehouse Dropdown
                        |--------------------------------------------------------------------------
                        */

                        $('#warehouse_id').html(
                            '<option selected disabled value="">--Select Warehouse--</option>'
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | Append Warehouse Data
                        |--------------------------------------------------------------------------
                        */

                        $('#warehouse_id').append(data);

                        /*
                        |--------------------------------------------------------------------------
                        | Refresh Select2
                        |--------------------------------------------------------------------------
                        */

                        $('#warehouse_id').trigger('change.select2');
                    },

                    error: function(xhr, status, error) {
                        console.log('Warehouse AJAX Error:', error);
                        console.log('HTTP Status:', xhr.status);
                        console.log('Response:', xhr.responseText);

                        $('#warehouse_id').html(
                            '<option selected disabled value="">--Select Warehouse--</option>'
                        );

                        $('#warehouse_id').trigger('change.select2');
                    },

                    complete: function() {
                        warehouseLoading = false;
                    }
                });
            });

            /*
            |--------------------------------------------------------------------------
            | Select2 Specific Event
            |--------------------------------------------------------------------------
            |
            | কিছু Select2 configuration-এ native change event-এর পরিবর্তে
            | select2:select event পাওয়া যেতে পারে।
            |
            | এখানে native change event-কে trigger করা হচ্ছে।
            | ফলে মূল AJAX logic একই জায়গায় থাকবে।
            |--------------------------------------------------------------------------
            */

            $(document).on('select2:select', '#branch_id', function() {
                $(this).trigger('change');
            });
        });

        /*
        |--------------------------------------------------------------------------
        | Due Calculate
        |--------------------------------------------------------------------------
        */

        function dueCalculate(amount, paid_amount) {
            return number_format(
                number_format(amount) - number_format(paid_amount)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Grand Total Calculate
        |--------------------------------------------------------------------------
        */

        function grandTotalCalculate(total, discount = 0, vat = 0, result = 0) {
            result = (total + vat) - discount;
            return number_format(result);
        }

        /*
        |--------------------------------------------------------------------------
        | Percentage Calculate
        |--------------------------------------------------------------------------
        */

        function percentageCalculate(amount, disc) {
            return number_format(amount * disc * .01);
        }

        /*
        |--------------------------------------------------------------------------
        | Number Format
        |--------------------------------------------------------------------------
        */

        function number_format(number, decimal = 2) {
            number = Number(number);
            return Number(parseFloat(number).toFixed(decimal));
        }

        /*
        |--------------------------------------------------------------------------
        | Get Product List
        |--------------------------------------------------------------------------
        */

        function getProductList(cat_id) {
            if (cat_id == '' || cat_id == null || cat_id == 0) {
                return false;
            }

            $.ajax({
                "url": "{{ route('inventorySetup.stockAdjustment.getProductListforadjust') }}",
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

        /*
        |--------------------------------------------------------------------------
        | Get Unit Price
        |--------------------------------------------------------------------------
        */

        function getUnitPrice(productId) {
            if (productId == '' || productId == null || productId == 0) {
                return false;
            }

            $.ajax({
                "url": "{{ route('inventorySetup.stockAdjustment.unitPriceforadjust') }}",
                "type": "GET",
                cache: false,
                data: {
                    "_token": "{{ csrf_token() }}",
                    productId: productId
                },

                success: function(data) {
                    $("#unitprice").val(data);
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Get Balance
        |--------------------------------------------------------------------------
        */

        function getBalance(account_id) {
            $.ajax({
                "url": "{{ route('settings.transfer.checkBalance') }}",
                "type": "GET",
                cache: false,
                data: {
                    account_id: account_id
                },

                success: function(data) {
                    $('.balance').val(data);
                }
            });
        }
    </script>
@endsection
