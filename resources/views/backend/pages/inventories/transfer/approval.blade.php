@extends('backend.layouts.master')

@section('title')
    Inventory - {{ $title }}
@endsection
@section('navbar-content')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Stock Transfer </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('inventorySetup.transfer.index'))
                            <li class="breadcrumb-item"><a href="{{ route('inventorySetup.transfer.index') }}">Transfer
                                    List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Stock Transfer</span></li>
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
                    <h3 class="card-title">Edit Stock Transfer</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('inventorySetup.transfer.index'))
                            <a class="btn btn-default" href="{{ route('inventorySetup.transfer.index') }}"><i
                                    class="fa fa-list"></i>
                                Transfer List</a>
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
                        action="{{ route('inventorySetup.transfer.approval_store', $editInfo->id) }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-2 mb-3">
                                <label>Invoice Number :</label>
                                <input type="hidden" name="transferId" value="{{ $editInfo->id }}">
                                <input type="hidden" name="approvalstatus" value="{{ $editInfo->status }}">
                                <input class="bg-green form-control" readonly=""
                                    style="padding: 5px; font-weight : bold; width: 100%"
                                    value="{{ $editInfo->voucher_code }} ">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label>Date * :</label>
                                @php
                                    $date = $editInfo->date ? \Carbon\Carbon::parse()->format('Y-m-d') : '';
                                @endphp
                                <input type="date" name="date" class="form-control" placeholder="Date"
                                    value="{{ $date }}">
                                @error('date')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>From Branch * :</label>
                                <select class="form-control select2" name="from_branch_id">
                                    <option selected disabled value="">--Select From Branch--</option>
                                    @foreach ($branch as $key => $value)
                                        <option value="{{ $value->id }}"
                                            {{ $editInfo->from_branch_id == $value->id ? 'selected' : '' }}>
                                            {{ $value->branchCode . ' - ' . $value->name }}</option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>To Branch * :</label>
                                <select class="form-control select2" name="to_branch_id">
                                    <option selected disabled value="">--Select To Branch--</option>
                                    @foreach ($branch as $key => $value)
                                        <option value="{{ $value->id }}"
                                            {{ $editInfo->to_branch_id == $value->id ? 'selected' : '' }}>
                                            {{ $value->branchCode . ' - ' . $value->name }}</option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>




                            <table class="table table-bordered table-hover" id="show_item">

                                <thead>
                                    <tr>
                                        <th colspan="8">Select Product Item</th>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><strong>Category</strong></td>
                                        <td class="text-center"><strong>Product</strong></td>
                                        <td class="text-center"><strong>Quantity</strong></td>
                                        <td class="text-center"><strong>Unit Price</strong></td>
                                        <td class="text-center"><strong>Total</strong></td>
                                        {{-- <td class="text-center"><strong>Action</strong></td> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- <tr>
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
                                        </td>
                                        <td>
                                            <input type="number" step="any" class="form-control text-right qty reset_qty"
                                                placeholder="Qty" min="0">
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
                                    </tr> --}}
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
                                                {{ $detail->qty }}
                                                <input type="hidden" class="ttlqty" name="qty[]"
                                                    value="{{ $detail->qty }}">
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

                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>

                                    <tr>
                                        <td class="text-right"><strong>Sub-Total(BDT)</strong></td>
                                        <td class="text-right"><strong
                                                class=""></strong></td>
                                    <td class="
                                                text-right"><strong
                                                    class="ttlqty">{{ $editInfo->qty ?? 0 }}</strong>
                                        </td>
                                        <td class="text-right"><strong
                                                class="ttlunitprice">{{ $editInfo->subtotal ?? 0 }}</strong></td>
                                        <td class="text-right"><strong
                                                class="grandtotal">{{ $editInfo->grand_total ?? 0 }}</strong></td>
                                        {{-- <td class="text-right"><strong
                                                class=""></strong></td> --}}
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="
                                                row mb-2">
                            <div class="col-md-8">
                                <div class="form-group">

                                    <div class="input-group">
                                        <textarea cols="100" rows="3" class="form-control" name="narration"
                                            placeholder="Narration"
                                            type="text">{{ $editInfo->narration ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">

                                <input type="hidden" name="cart_vat" class="input_vat">
                                <input type="hidden" name="input_net_total" class="input_net_total">
                                <input type="hidden" name="cart_due" class="input_due">

                                <table class="table table-bordered table-hover" id="cart_output">
                                    <tbody>
                                        <tr>
                                            <td nowrap align="right"><strong>Total </strong></td>
                                            <td align="right"> <strong id="gtoal" class="grandtotal"></strong></td>
                                        </tr>
                                        <tr>
                                            <td nowrap align="right"><strong>Shiping Charge ( + )
                                                </strong></td>
                                            <td align="right"><strong>
                                                    {{ $editInfo->shipping }}
                                                </strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td nowrap align="right"><strong>Net Total</strong></td>
                                            <td align="right"><strong id="ntotal">{{ $editInfo->subtotal }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 102px;">

                                                <div class="clearfix"></div>
                                                <div class="clearfix form-actions float-right">
                                                    <div class="col-md-offset-1 col-md-10">
                                                        <button class="btn btn-info" id="subMitButton" type="submit">
                                                            Save
                                                        </button>
                                                        &nbsp; &nbsp; &nbsp;

                                                    </div>
                                                </div>
                                            </td>
                                        </tr>


                                    </tbody>
                                </table>
                                <!-- /.card -->

                                <!-- /.card -->
                            </div>

                        </div>

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


                var proId = $('.proName').val();
                var proName = $(".proName").find('option:selected').attr('proName');

                var qty = number_format(parent.find('.qty').val());



                var unitprice = number_format(parent.find('.unitprice').val());



                if (qty == '' || qty == null || qty == 0) {

                    return false;
                } else {
                    var total = qty * unitprice;
                    const row = `
                    <tr class="new_item${proId}">
                        <td style="padding-left:15px;">${catName}<input type="hidden" name="catName[]" value="${catId}"></td>
                        <td class="text-right">${proName}<input type="hidden" class="add_quantity" name="proName[]" value="${proId}"></td>
                    
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

            $.ajax({
                "url": "{{ route('inventorySetup.purchase.unitPice') }}",
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
                success: function(data) {
                    $('.balance').val(data);
                }
            });

        }
    </script>

@endsection
