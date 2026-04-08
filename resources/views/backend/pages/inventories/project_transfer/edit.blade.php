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
                    @if (helper::roleAccess('project.transferproject.index'))
                    <li class="breadcrumb-item"><a href="{{ route('project.transferproject.index') }}">Project 
                        </a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Edit Transfer</span></li>
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
                <h3 class="card-title">Edit Project Transfer</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('project.transferproject.list'))
                    <a class="btn btn-default" href="{{ route('project.transferproject.list') }}"><i
                            class="fa fa-list"></i>
                            Project Transfer List</a>
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
                    action="{{ route('project.transferproject.update',$editInfo->id) }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <span class="bg-green" style="padding: 5px; font-weight : bold"
                                for="validationCustom01">Purchase Order Code * : {{ $editInfo->invoice_no }}</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label>Date:</label>
                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                <input type="text" name="date" data-toggle="datetimepicker"
                                    value="{{ $editInfo->order_date }}" class="form-control datetimepicker-input"
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
                            <label for="validationCustom01">Purchase Requisition * :</label>
                            <select class="form-control select2" id="purreq" name="purchase_requisition">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($purchaserequisitions as $key => $value)
                                <option {{ $editInfo->purchase_requisition_id == $value->id ? 'selected' : '' }}
                                    value="{{ $value->id }}">
                                    {{ $value->invoice_no }}
                                </option>
                                @endforeach
                            </select>
                            @error('purchase_requisition')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                      <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Branch * :</label>
                            <select class="form-control select2 branch_id" onchange="checkstock(this.value)" name="branch_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($branchs as $key => $value)
                                <option {{$value->id == $editInfo->branch_id ? "selected":"" }} value="{{ $value->id }}">
                                    {{ $value->branchCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Project * :</label>
                            <select readonly id="project_id" class="form-control select2" name="project_id">
                                <option selected value="{{ $editInfo->project_id }}">{{ $editInfo->project->name ?? "N/A" }}
                                </option>
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
                                <td class="text-center"><strong>Quantity</strong></td>
                                <td class="text-center"><strong>Action</strong></td>
                            </tr>
                        </thead>
                        <tbody id="main-table">
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
                                </td>
                                <td>
                                    <input type="number" step="any" class="form-control text-right qty reset_qty"
                                        placeholder="Qty" min="0">
                                </td>

                                <td>
                                    <a id="add_item" class="btn btn-info" style="white-space: nowrap"
                                        href="javascript:;" title="Add Item">
                                        <i class="fa fa-plus"></i>
                                        Add Item
                                    </a>
                                </td>
                            </tr>
                            @php
                            $qty = 0;
                            $unitprice = 0;
                            $totalprice = 0;
                            @endphp

                            @foreach ($editInfo->details as $value)

                            @php
                            $qty += $value->qty;
                            $unitprice += $value->unit_price;
                            $totalprice +=$value->total_price;
                            @endphp

                            <tr class="delrow new_item{{ $value->product_id }}">
                                <td>
                                    {{ $value->category->name }}
                                    <input type="hidden" name="category_nm[]" value=" {{ $value->category_id }}">
                                </td>
                                <td class="text-right"> {{ $value->product->name }} <input type="hidden"
                                        name="product_nm[]" value=" {{ $value->product_id }}"></td>
                                <td class="text-right"> <input class="ttlqty qnty form-control" type="number"
                                        name="qty[]" value="{{ $value->qty }}"></td>
                                <td>
                                    <a del_id="  {{ $value->product_id }}"
                                        class="delete_item btn form-control btn-danger" href="javascript:;" title="">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                        <tfoot>

                            <tr>
                                <td class="
                                            text-right">
                                    <strong>Sub-Total(BDT)</strong>
                                </td>
                                <td class="text-right"><strong class=""></strong></td>
                                <td class="text-right"><strong class="ttlqty">{{$qty}}</strong>
                                </td>
                                <td class="text-right"><strong class=""></strong></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="
                        form-row form-group">
                        <div class="col-md-12">
                            <label for="">Note</label>
                            <textarea name="note" class="form-control" name="note" id="" cols="10" rows="4"></textarea>
                        </div>
                        {{-- <div class="col-md-4">

                            <table class="table table-bordered table-hover" id="cart_output">
                                <tr>
                                    <th><span>Total</span></th>
                                    <th class="text-right"><span class="grandtotal fixedtotal">{{$totalprice}}</span>
                                    </th>
                                </tr>
           
                                @php
                                $total = $totalprice - $editInfo->advance_payment;
                                @endphp
                                <tr>
                                    <th><span>Total Due</span></th>
                                    <th class="text-right"><span class="cart_due">{{$total}}</span>
                                    </th>
                                </tr>
                            </table>
                        </div> --}}
                    </div>
                    @if($editInfo->status !== "Accepted")
                    <button class="btn btn-info" type="submit"><i class="fa fa-save"></i> &nbsp;Update</button>
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
    $(document).ready(function () {



        $('#purreq').on('change', function () {
            let id = $(this).val();

            $.ajax({
                url: "{{ route('project.transferproject.searchpr') }}",
                method: 'GET',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id
                },
                dataType: 'json',
                success: function (data) {

                    $('.delrow').remove();
                    $('#main-table').append(data.prdetails);
                    $('#project_id').html(data.branch);
                    $('.reset_unitprice').val('');
                    $('.reset_qty').val('');
                    $('.reset_total').val('');
                    $(".reset").val(null).trigger("change");
                    $('.paid_amount').removeAttr('readonly');
                    $('.paid_amount').val('')
                    $('.cart_due').text('');
                    findqtyamoun();
                    findunitamount();
                    findgrandtottal();

                }
            })
        });


        var grandtotal = $('.fixedtotal').text();
        $('.paid_amount').on('keyup change', function () {
            var grandtotal = $('.fixedtotal').text();
            let number = $(this).val();
            var total = grandtotal - number;

            if (number <= parseInt(grandtotal)) {
                $('.cart_due').text(total);
            } else {
                $(this).val('')
                $('.cart_due').text('');
                // alert('You can ');
            }

        })

        var findqtyamoun = function () {
            var ttlqty = 0;
            $.each($('.ttlqty'), function () {
                console.log($(this).val());
                qty = number_format($(this).val());
                ttlqty += qty;
            });
            $('.ttlqty').text(number_format(ttlqty));

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
            $('.cart_due').text(parseInt(grandtotal));
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

            $('.grandtotal').text(number_format(grandtotal));
            cart_net_total.text(cal_grandtotal);
            cart_due.text(cal_due);


            $('.input_vat').val(cal_vat);
            $('.input_net_total').val(cal_grandtotal);
            $('.input_due').val(cal_due);


        };



        $(document).on('click', '#add_item', function () {

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
                        <td style="padding-left:15px;">${catName}<input type="hidden" name="category_nm[]" value="${catId}"></td>
                        <td class="text-right">${proName}<input type="hidden" class="add_quantity" name="product_nm[]" value="${proId}"></td>
                        <td class="text-right"><input type="number" class="form-control ttlqty qnty" name="qty[]" value="${qty}"></td>
                        <td>
                            <a del_id="${proId}" class="delete_item btn form-control btn-danger" href="javascript:;" title="">
                                <i class="fa fa-times"></i>
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

        $(document).on('click', '.delete_item', function () {
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

        $(document).on('input', '.qnty', function () {
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

            findqtyamoun();
            findunitamount();
            findgrandtottal();
        });

        $(document).on('input', '.input-checker', function () {
            var grandtotal = $('.grandtotal').text();
            grandtotal = Number(grandtotal);

            if (isNaN(grandtotal) || grandtotal < 1) {
                // lert('Please Add some item first.');
                alertMessage.error('Please Add some item first.');
                return false;
            }
            findgrandtottal();

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