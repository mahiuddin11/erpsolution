@extends('backend.layouts.master')

@section('title')
    Project - {{ $title }}
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Project </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('inventory-purchaserequisition-list'))
                            <li class="breadcrumb-item"><a href="{{ route('inventory-purchaserequisition-list') }}">Project
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Approve Project Requisition</span></li>
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
                    <h3 class="card-title">Approve Project Requisition</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('project.Productrequisition.index'))
                            <a class="btn btn-default" href="{{ route('project.RequisitionAction.index') }}"><i
                                    class="fa fa-list"></i>
                                Project Requisition List</a>
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
                    <form class="needs-validation submit_form" method="POST"
                        action="{{ route('project.RequisitionAction.storeapprove', $requisition->id) }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <span class="bg-green" style="padding: 5px; font-weight : bold"
                                    for="validationCustom01">Requisition Code * : {{ $requisition->invoice_no }}</span>
                                <input type="hidden" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Date * :</label>
                                <input type="date" name="date" readonly value="{{ $requisition->date }}"
                                    class="form-control" id="validationCustom01" placeholder="Date">
                                @error('date')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Branch * :</label>
                                <select class="form-control select2 branch_id " onchange="checkstock(this.value)"
                                    name="branch_id">
                                    @if ($user->branch_id == null)
                                        <option selected disabled value="">--Select--</option>
                                        @foreach ($branch as $key => $value)
                                            <option value="{{ $value->id }}">
                                                {{ $value->branchCode . ' - ' . $value->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option selected value="{{ $requisition->branch_id }}">
                                            {{ $requisition->branch->branchCode . ' - ' . $requisition->branch->name }}
                                        </option>
                                    @endif
                                </select>
                                @error('branch_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                                {{-- <span class=" error text-red text-bold branchmess"></span> --}}
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Project * :</label>
                                <select disabled class="form-control select2 project_id" name="project_id">
                                    <option selected disabled value="">--Select--</option>
                                    @foreach ($projects as $key => $value)
                                        <option {{ $requisition->project_id == $value->id ? 'selected' : '' }}
                                            value="{{ $value->id }}">
                                            {{ $value->projectCode . ' - ' . $value->name }}
                                        </option>
                                    @endforeach
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
                                    <td class="text-center"><strong>Suppler Name</strong></td>
                                    <td class="text-center"><strong>Quantity</strong></td>
                                    <td class="text-center"><strong>Unit Price</strong></td>
                                    <td class="text-center"><strong>Total</strong></td>
                                    <td class="text-center"><strong>Action</strong></td>
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


                                @foreach ($requisitionDetails as $value)
                                    <tr>
                                        <td class="new_item{{ $value->product_id }}">
                                            {{ $value->category->name }}
                                            <input type="hidden" name="category_nm[]" value="{{ $value->category_id }}">
                                        </td>

                                        <td>{{ $value->product->name }} <input type="hidden" name="product_nm[]"
                                                value="{{ $value->product_id }}" class="product_id"></td>
                                        <td class="new_item">
                                            <select class="form-control supplier_id" name="supplier_id">
                                                <option selected value="">--Select--</option>
                                                @foreach ($suppliers as $supplier)
                                                    <option class="form-control" value="{{ $supplier->id }}">
                                                        {{ $supplier->name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </td>
                                        <td> <input type="number" class="ttlqty qty count_total form-control"
                                                name="qty[]" value="{{ $value->qty }}">
                                            <div class="productStockCheck{{ $value->product_id }}"></div>
                                        </td>
                                        <td> <input type="number" id="unitprice"
                                                class="ttlunitprice unitprice count_total form-control" name="unitprice[]"
                                                value="{{ $value->unit_price }}"></td>
                                        <td><input type="text" name="total[]" readonly class="total form-control"
                                                value="{{ $value->total_price }}"></td>
                                        <td>
                                            <a del_id="${proId}" class="delete_item btn form-control btn-danger"
                                                href="javascript:;" title="">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right">Sub-Total(BDT)</td>
                                    <td class="text-right"><strong class=""></strong></td>
                                    <td class="
                                            text-right"><strong
                                            class="ttlqty">{{ $requisition->total_qty }}</strong>
                                    </td>
                                    <td class="text-right"><strong
                                            class="ttlunitprice">{{ $requisition->unitprice_price }}</strong></td>
                                    <td class="text-right"><strong
                                            class="grandtotal">{{ $requisition->total_price }}</strong></td>
                                    <td class="text-right"><strong class=""></strong></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="form-row form-group">
                            <div class="col-md-6">
                                <label for="">Note</label>
                                <textarea name="note" class="form-control" name="note" id="" cols="10" rows="4">{{ $requisition->note }}</textarea>
                            </div>
                        </div>
                        @if ($requisition->status == 'Pending' || $requisition->status == 'Cancel')
                            <button class="btn btn-success" type="submit"><i class="fa fa-save"></i>
                                &nbsp;Approve</button>
                        @endif
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
        function checkstock(branchid) {

            var branch_id = branchid;
            var branch_id = $('.branch_id').val();
            var product_nm = $("input[name='product_nm[]']").map(function() {
                return $(this).val();
            }).get();
            var qty = $("input[name='qty[]']").map(function() {
                return $(this).val();
            }).get();
            $.ajax({

                url: "{{ route('project.RequisitionAction.checkstock') }}",
                method: 'GET',
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                    branch_id: branch_id,
                    product_nm: product_nm,
                    qty: qty
                },
                success: function(data) {
                    $.each(data, function(key, value) {
                        $('.productStockCheck' + key).html(value);
                    })
                }

            });
        }


        $(document).ready(function() {
            let branch_id = $('.branch_id').val();

            checkstock(branch_id);

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

            function checkstockmen() {

                var branch_id = $('.branch_id').val();
                var product_nm = $("input[name='product_nm[]']").map(function() {
                    return $(this).val();
                }).get();
                var qty = $("input[name='qty[]']").map(function() {
                    return $(this).val();
                }).get();
                $.ajax({

                    url: "{{ route('project.RequisitionAction.checkstock') }}",
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        branch_id: branch_id,
                        product_nm: product_nm,
                        qty: qty
                    },
                    success: function(data) {
                        $.each(data, function(key, value) {
                            $('.productStockCheck' + key).html(value);
                        })
                    }

                });
            }

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

                $('.grandtotal').text(number_format(grandtotal));
                cart_net_total.text(cal_grandtotal);
                cart_due.text(cal_due);


                $('.input_vat').val(cal_vat);
                $('.input_net_total').val(cal_grandtotal);
                $('.input_due').val(cal_due);


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

                if (catId == '' || catId == null) {
                    alertMessage.error("Category can't be empty.");
                    return false;
                }
                if (proId == '' || proId == null) {
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
                    const row = `
                    <tr class="new_item${proId}">
                        <td style="padding-left:15px;">${catName}<input type="hidden" name="category_nm[]" value="${catId}"></td>
                        <td class="text-right">${proName}<input type="hidden" class="add_quantity" name="product_nm[]" value="${proId}"></td>
                    
                        <td class="text-right"><input type="number" class="ttlqty qty form-control" name="qty[]" value="${qty}"></td>
                        <td class="text-right">${unitprice}<input type="hidden" id="unitprice" class="ttlunitprice unitprice" name="unitprice[]" value="${unitprice}">
                        </td>
                        <td class="text-right">
                            <input type="text" readonly class="total form-control" name="total[]" id="total" value="${total}">
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
            $(document).on('input', '.count_total', function() {
                let parent = $(this).parents('tr');
                let self = parent.find('.qty');
                let qty = number_format(self.val());

                if (qty == '' || qty == null) {
                    self.val(1);
                    qty = 1;
                }

                let unitPrice = number_format(parent.find('.unitprice').val());

                let total = number_format(unitPrice * qty);
                parent.find('.total').val(number_format(total));
                checkstockmen();
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

        $('.supplier_id').on('change', function() {
            var productId = $(this).closest('tr').find('.product_id').val();
            $.ajax({
                "url": "{{ route('inventorySetup.purchase.unitPice') }}",
                "type": "GET",
                cache: false,
                data: {
                    "_token": "{{ csrf_token() }}",
                    productId: productId,
                    supplier_id: $(this).val()
                },
                success: (data) => {
                    let price = $(this).closest('tr').find('.unitprice').val(data);
                    let qty = $(this).closest('tr').find('.ttlqty').val();
                    $(this).closest('tr').find('.total').val(qty * data);

                }
            });
        })



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
                success: function(data) {
                    $('.balance').val(data);
                }
            });

        }

        $('.submit_form').on('submit', function(e) {
            let project = $('.project_id').val();
            $(this).append(`<input name="project" value="${project}" type="hidden" />`);
        })
    </script>


@endsection
