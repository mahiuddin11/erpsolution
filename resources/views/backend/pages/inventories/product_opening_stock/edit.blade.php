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
                        @if (helper::roleAccess('inventorySetup.productOS.index'))
                            <li class="breadcrumb-item"><a href="{{ route('inventorySetup.productOS.index') }}">Product Opening Stock List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Product Opening Stock</span></li>
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
                    <h3 class="card-title">Edit Product Opening Stock</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('inventorySetup.productOS.index'))
                            <a class="btn btn-default" href="{{ route('inventorySetup.productOS.index') }}"><i
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
                    <form class="needs-validation" method="POST"
                        action="{{ route('inventorySetup.productOS.update', $editInfo->id) }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-2 mb-3">
                                <label>Invoice Number :</label>
                                <input class="bg-green form-control" readonly=""
                                    style="padding: 5px; font-weight : bold; width: 100%"
                                    value="{{ $editInfo->invoice_no }} ">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label>Date * :</label>
                                @php
                                    $date = $editInfo->date ? \Carbon\Carbon::parse()->format('Y-m-d') : '';
                                @endphp
                                <label>Date:</label>
                                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                    <input type="text" name="date" data-toggle="datetimepicker"
                                        value="{{ $date }}" class="form-control datetimepicker-input"
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
                                <label for="validationCustom01">Cost Center *:</label>
                                <select class="form-control select2" disabled id="cost_center">
                                    <option value="0">No Cost Center</option>
                                    <option {{ $editInfo->project_id ? 'selected' : '' }} value="project">Project</option>
                                    <option {{ $editInfo->branch_id ? 'selected' : '' }} value="branch">Branch</option>
                                </select>
                                <span class="error text-red text-bold"></span>
                            </div>

                            <div class="col-md-4 mb-3" id="project_div" style="display: none;">
                                <label for="validationCustom01">Project *:</label>
                                <select class="form-control select2" id="project_id" name="project_id">
                                    <option value="0">--Select--</option>
                                    @foreach ($projects as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $editInfo->project_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="error text-red text-bold"></span>
                            </div>
                            <div class="col-md-4 mb-3" id="branch_div" style="display: none;">
                                <label for="validationCustom01">Branch *:</label>
                                <select class="form-control select2" id="branch_id" name="branch_id">
                                    <option value="0">--Select--</option>
                                    <!-- Add branch options here -->
                                    @foreach ($branchs as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ $editInfo->branch_id == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="error text-red text-bold"></span>
                            </div>



                            <table class="table table-bordered table-hover" id="show_item">

                                <thead>
                                    <tr>
                                        <th colspan="8">Select Product Item</th>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><strong>Category</strong></td>
                                        <td class="text-center"><strong>Product</strong></td>
                                        <td class="text-center"><strong>Purchase Type</strong></td>
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
                                                    {{ $eachInfo->name }}
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="select2 form-control proName reset" id="productID"
                                                data-placeholder="Search Product" onchange="getUnitPrice(this.value)">
                                                <option disabled selected>---Select Product---</option>
                                            </select>
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
                                                placeholder="Unit Price">
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
                                    @foreach ($editInfo->details as $detail)
                                        <tr class="new_item">
                                            <td style="padding-left:15px;">
                                                {{ $detail->product->category->name ?? '' }}
                                                <input type="hidden" name="catName[]"
                                                    value="{{ $detail->product->category->id ?? '' }}">
                                            </td>
                                            <td class="text-right">
                                                {{ $detail->product->name ?? '' }}
                                                <input type="hidden" class="add_quantity" name="proName[]"
                                                    value="{{ $detail->product->id ?? '' }}">
                                            </td>

                                            <td class="text-right">
                                                {{ $detail->purchasetype }}
                                                <input type="hidden" name="purchasetype[]"
                                                    value="{{ $detail->purchasetype }}">
                                            </td>
                                            <td class="text-right">
                                                {{ $detail->quantity }}
                                                <input type="hidden" class="ttlqty" name="qty[]"
                                                    value="{{ $detail->quantity }}">
                                            </td>
                                            <td class="text-right">
                                                {{ $detail->unit_price }}
                                                <input type="hidden" class="ttlunitprice" name="unitprice[]"
                                                    value="{{ $detail->unit_price }}">
                                            </td>
                                            <td class="text-right">
                                                {{ $detail->total_price }}
                                                <input type="hidden" class="total" name="total[]"
                                                    value="{{ $detail->total_price }}">
                                            </td>
                                            <td>
                                                <a del_id="${proId}" class="delete_item btn form-control btn-danger"
                                                    href="javascript:;">

                                                    <i class="fa fa-times"></i>&nbsp;Remove
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-right"><strong>Sub-Total(BDT)</strong></td>
                                        <td class="text-right"><strong class=""></strong></td>
                                        <td class="text-right"><strong class=""></strong></td>
                                        <td class="text-right"><strong
                                                class="ttlqty">{{ $editInfo->quantity ?? 0 }}</strong>
                                        </td>
                                        <td class="text-right"><strong
                                                class="ttlunitprice">{{ $editInfo->subtotal ?? 0 }}</strong></td>
                                        <td class="text-right"><strong
                                                class="grandtotal">{{ $editInfo->grand_total ?? 0 }}</strong></td>
                                        <td class="text-right"><strong class=""></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-9">
                                <div class="form-group">

                                    <div class="input-group">
                                        <textarea cols="100" rows="3" class="form-control" name="narration" placeholder="Narration"
                                            type="text">{{ $editInfo->note ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">

                                <input type="hidden" name="cart_vat" class="input_vat">
                                <input type="hidden" name="input_net_total" class="input_net_total">
                                <input type="hidden" name="cart_due" class="input_due">

                                <table class="table table-bordered table-hover" id="cart_output">
                                    <tr>
                                        <th width="30%"><span>Grand Total</span></th>
                                        <th width="35%" class="text-right"><span
                                                class="grandtotal">{{ $editInfo->grandtotal }}</span>
                                        </th>
                                        <th></th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        {{-- <div class="form-group">
                            <button class="btn btn-info" type="submit">
                                <i class="fa fa-save"></i>&nbsp;Save
                            </button>
                        </div> --}}
                    </form>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">

            </div>
        </div>
    </div>
    <!-- /.col-->
    <script type="text/javascript">
        $(document).ready(function() {
            findgrandtottal();
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


            $(document).on('click', '#add_item', function() {

                var parent = $(this).parents('tr');

                var supid = $('.supid').val();
                var catId = $('.catName').val();

                var catName = $(".catName").find('option:selected').attr('catName');

                //            var subcatID = $('.subCat').val();
                //            var subCat = $(".subCat").find('option:selected').attr('subCat');

                var proId = $('.proName').val();
                var proName = $(".proName").find('option:selected').attr('proName');

                //            var unit_id = $('.unitName').val();
                //            var unitName = $(".unitName").find('option:selected').attr('unitName');

                //  var unit = $('.unit').val();
                var qty = number_format(parent.find('.qty').val());



                var unitprice = number_format(parent.find('.unitprice').val());
                var purchasetypeval = $('.purchasetype').find('option:selected').val();
                var purchasetypetext = $('.purchasetype').find('option:selected').text();


                if (purchasetypeval == '' || purchasetypeval == null) {
                    alertMessage.error("Please Select Type.");
                    return false;
                }

                //            if (supid == '' || supid == null) {
                //               // productItemValidation("Supplier can't be empty.");
                //                return false;
                //            }
                //            if (catId == '' || catId == null) {
                //               // productItemValidation("Category can't be empty.");
                //                return false;
                //            }
                //            if (proId == '' || proId == null) {
                //               // productItemValidation("Product can't be empty.");
                //                return false;
                //            }


                if (qty == '' || qty == null || qty == 0) {

                    return false;
                } else {
                    var total = qty * unitprice;
                    const row = `
                    <tr class="new_item${proId}">
                        <td style="padding-left:15px;">${catName}<input type="hidden" name="catName[]" value="${catId}"></td>
                        <td class="text-right">${proName}<input type="hidden" class="add_quantity" name="proName[]" value="${proId}"></td>
                          <td class="text-right">${purchasetypetext}<input type="hidden" name="purchasetype[]" value="${purchasetypeval}"></td>s
                        <td class="text-right">${qty}<input type="hidden" class="ttlqty" name="qty[]" value="${qty}"></td>
                        <td class="text-right">${unitprice}<input type="hidden" class="ttlunitprice" name="unitprice[]" value="${unitprice}">
                        </td>
                        <td class="text-right">${total}
                            <input type="hidden" class="total" name="total[]" value="${total}">
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
                // if (confirm("Are you sure?")) {
                //     $(this).parents('tr').remove();
                //     findqtyamoun();
                //     findunitamount();
                //     findgrandtottal();
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
                let qty = number_format(self.val());

                if (qty == '' || qty == null) {
                    $(this).val(1);
                    qty = 1;
                }

                let unitPrice = number_format(parent.find('.unitprice').val());

                let total = number_format(unitPrice * qty);

                parent.find('.total').val(number_format(total));

            });

            $(document).on('input', '.input-checker', function() {
                var grandtotal = $('.grandtotal').text();
                grandtotal = Number(grandtotal);

                if (isNaN(grandtotal) || grandtotal < 1) {
                    // lert('Please Add some item first.');
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


        function findgrandtottal() {
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

            $('.grandtotal').text(number_format(grandtotal));
            cart_net_total.text(cal_grandtotal);
            cart_due.text(cal_due);


            $('.input_vat').val(cal_vat);
            $('.input_net_total').val(cal_grandtotal);
            $('.input_due').val(cal_due);


        };

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
        // 
        $(document).on('input', '.unitprice', function() {

            let self = $(this);
            let parent = self.parents('tr');
            let unitprice = number_format(self.val());

            if (unitprice == '' || unitprice == null) {
                $(this).val(1);
                unitprice = 1;
            }

            let qty = number_format(parent.find('.qty').val());

            let total = number_format(unitprice * qty);

            parent.find('.total').val(number_format(total));

        });

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


        $(document).ready(function() {
            // Function to handle showing/hiding based on the cost center value
            function toggleCostCenterFields(value) {
                $('#project_div').hide();
                $('#branch_div').hide();

                // Clear selections when switching
                // $('#project_id').val('0').trigger('change');
                // $('#branch_id').val('0').trigger('change');

                if (value === 'project') {
                    $('#project_div').show();
                } else if (value === 'branch') {
                    $('#branch_div').show();
                }
            }

            // Initial check when the page loads
            var initialValue = $('#cost_center').val();
            toggleCostCenterFields(initialValue);

            // Event listener for change
            $(document).on('change', "#cost_center", function() {
                var value = this.value;
                toggleCostCenterFields(value);
            });
        });
    </script>
@endsection
