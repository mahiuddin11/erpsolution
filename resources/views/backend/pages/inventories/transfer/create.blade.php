@extends('backend.layouts.master')
@section('title')
Stock d- {{ $title }}
@endsection

@section('styles')
<style>
    .bootstrap-switch-large {
        width: 200px;
    }
</style>
@endsection

@section('navbar-content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    Stock </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('inventorySetup.transfer.index'))
                    <li class="breadcrumb-item"><a href="{{ route('inventorySetup.transfer.index') }}">Sale</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Stock Tranfer List</span></li>
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
                <h3 class="card-title">Stock Tranfer </h3>
            </div>
            <div class="card-body">
                <form class="needs-validation" method="POST" action="{{ route('inventorySetup.transfer.store') }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">Invoice Number :</label>
                            <input class="bg-green form-control" readonly="" style="padding: 5px; font-weight : bold; width: 100%" value="{{ $invoice_no }} " for="validationCustom01">
                            <input type="hidden" name="invoice_no" class="form-control" id="" value="{{ $invoice_no }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Date:</label>
                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                <input type="text" name="date" data-toggle="datetimepicker" value="{{ date('YYYY-mm-dd') }}" class="form-control datetimepicker-input" data-target="#reservationdate" />
                                <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            @error('date')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">From Branch * :</label>
                            <select class="form-control select2 from_branch" id="from_branch_id" name="from_branch_id" onchange="duplicateBranchCheck()">
                                <option selected disabled value="">--Select Branch--</option>
                                @foreach ($branch as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->branchCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">To Branch * :</label>
                            <select class="form-control select2" id="to_branch_id" name="to_branch_id" onchange="duplicateBranchCheck()">
                                <option selected disabled value="">--Select Branch--</option>
                                @foreach ($tobranch as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->branchCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <table class=" table-responsive table table-bordered">
                            <tr>
                                <td>
                                    <div class="col-md-9 float-left ">
                                        Sales Item
                                    </div>
                                    <div class="col-md-3 float-right">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 10px!important;">
                                    <div class="col-md-12">
                                        <div class="col-md-12 float-left">
                                            <div class="panel panel-default">
                                                <div class="panel-body">

                                                    <table class="table table-bordered table-hover tableAddItem" id="show_item">
                                                        <thead>
                                                            <tr>

                                                                <th nowrap style="width:20%" align="center" id="">
                                                                    <strong>Product Category <span style="color:red;">
                                                                            *</span></strong>
                                                                </th>
                                                                <th nowrap style="width:25%" align="center" id="">
                                                                    <strong>Product <span style="color:red;">
                                                                            *</span></strong>
                                                                </th>
                                                                <th nowrap style="width:10%" align="center">
                                                                    <strong>Quantity <span style="color:red;">
                                                                            *</span></strong>
                                                                </th>
                                                                <th nowrap style="width:12%" align="center"><strong>Unit
                                                                        Price(BDT) <span style="color:red;">
                                                                            *</span></strong></th>
                                                                <th nowrap style="width:13%" align="center">
                                                                    <strong>Total Price(BDT) <span style="color:red;">
                                                                            *</span></strong>
                                                                </th>
                                                                <th align="center" style="width:5%">
                                                                    <strong>Action</strong>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td id="product_td">
                                                                    <select onchange="getProductList(this.value)" class="select2 form-control catName" id="form-field-select-3" data-placeholder="Search Category">
                                                                        <option disabled selected>--- Select Category
                                                                            ---</option>
                                                                        <?php foreach ($category_info as $eachInfo) : ?>
                                                                            <option catName="{{ $eachInfo->name }}" value="{{ $eachInfo->id }}">
                                                                                {{ $eachInfo->name }}
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>
                                                                <td id="product_td">
                                                                    <select class="select2 form-control proName" id="productID" data-placeholder="Search Product" onchange="getUnitPrice(this.value)">
                                                                        <option disabled selected>---Select Product---
                                                                        </option>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input type="text" readonly class="form-control  " style="height: 20px;" id="currentStock" placeholder="0">
                                                                    <input type="text" style="height: 20px;" class="form-control  qty" id="qty" onkeyup="qtyPriceCal(this.value);" placeholder="0">
                                                                </td>

                                                                <td><input type="text" readonly class="form-control text-right  unitprice" id="unitpice" placeholder="0.00"></td>
                                                                <td><input type="text" class="form-control text-right ttlamount total" id="total" placeholder="0.00" readonly="readonly"></td>
                                                                <td>
                                                                    <a id="add_item" class="btn btn-info form-control" href="javascript:;" title="Add Item">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td align="right"><strong>Sub-Total(BDT)</strong></td>
                                                                <td align="right"><strong class=""></strong></td>
                                                                <td align="
                                                                            right"><strong class="ttlqty"></strong>
                                                                </td>
                                                                <td align="right"><strong class="ttlunitprice"></strong>
                                                                </td>
                                                                <td align="right"><strong class="grandtotal"></strong>
                                                                </td>
                                                                <td align="right"><strong class=""></strong></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <table class="">
                                                    <tr>
                                                        <td>
                                                            <textarea style="
                                                                                                                                                                                        border:none;" cols="157" class="form-control" name="narration" placeholder="Note......" type="text"></textarea>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-4 float-right">
                                                <div class="panel  panel-default">
                                                    <div class="panel-body">

                                                        <table class="table table-bordered table-hover ">
                                                            <tbody>
                                                                <tr>
                                                                    <td nowrap align="right"><strong>Total </strong>
                                                                    </td>
                                                                    <td align="right"> <strong id="gtoal" class="grandtotal"></strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td nowrap align="right"><strong>Shiping Charge ( +
                                                                            )
                                                                        </strong></td>
                                                                    <td>
                                                                        <input type="text" autocomplete="off" onkeyup="shipingCalculation(this.value)" id="disCount" style="text-align: right" name="shipping" value="" class="form-control" placeholder="0.00" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" />
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td nowrap align="right"><strong>Net Total</strong>
                                                                    </td>
                                                                    <td align="right"><strong id="ntotal" class="grandtotal abc"></strong></td>
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

                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </td>
                            </tr>

                        </table>



                    </div>

                </form>
            </div>



        </div>
    </div>
    <!-- /.col-->
</div>

<script type="text/javascript">
    $(document).ready(function() {

        var findqtyamount = function() {

            var ttlqty = 0;
            $.each($('.ttlqty'), function() {
                qty = $(this).val();
                qty = Number(qty);
                ttlqty += qty;
            });
            $('.ttlqty').text(parseFloat(ttlqty).toFixed(2));

        };

        var findunitamount = function() {
            var ttlunitprice = 0;
            $.each($('.ttlunitprice'), function() {
                unitprice = $(this).val();
                unitprice = Number(unitprice);
                ttlunitprice += unitprice;
            });
            $('.ttlunitprice').text(parseFloat(ttlunitprice).toFixed(2));
        };

        var findgrandtottal = function() {
            var grandtotal = 0;
            $.each($('.grandtotal'), function() {
                total = $(this).val();
                total = Number(total);
                grandtotal += total;
            });
            $('.grandtotal').text(parseFloat(grandtotal).toFixed(2));
        };


        $("#add_item").click(function() {


            // var supid = $('.supid').val();
            var catId = $('.catName').val();
            var catName = $(".catName").find('option:selected').attr('catName');


            var proId = $('.proName').val();
            var proName = $(".proName").find('option:selected').attr('proName');

            //            var unit_id = $('.unitName').val();
            //            var unitName = $(".unitName").find('option:selected').attr('unitName');

            var unit = $('.unit').val();
            var qty = $('.qty').val();



            var unitprice = $('.unitprice').val();

            var total = $('.total').val();

            if (catId == '' || catId == null) {
                // productItemValidation("Category can't be empty.");
                return false;
            }
            if (proId == '' || proId == null) {
                // productItemValidation("Product can't be empty.");
                return false;
            }


            if (qty == '' || qty == null || qty == 0) {
                //   productItemValidation("Quantity can't be empty or zero.");
                return false;
            } else {

                $("#show_item tbody").append('<tr class="new_item' + proId +
                    '">\n\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <td style="padding-left:15px;">' +
                    catName +
                    '<input type="hidden" name="catName[]" value="' +
                    catId +
                    '"></td>\n\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <td align="right">' +
                    proName +
                    '<input type="hidden" class="add_quantity" name="proName[]" value="' +
                    proId +
                    '"></td>\n\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        \n\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <td align="right">' +
                    qty +
                    '<input type="hidden" class="ttlqty" name="qty[]" value="' +
                    qty +
                    '"></td>\n\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <td align="right">' +
                    unitprice +
                    '<input type="hidden" class="ttlunitprice unitparice" name="unitprice[]" value="' +
                    unitprice +
                    '"></td>\n\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <td align="right">' +
                    total +
                    '<input type="hidden" class="grandtotal" name="total[]" value="' +
                    total +
                    '"></td>\n\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        \n\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        \n\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <td><a del_id="' +
                    proId +
                    '" class="delete_item btn form-control btn-danger" href="javascript:;" title=""><i class="fa fa-times"></i></a></td></tr>'
                );
            }

            $('.unitprice').val('');
            $('.qty').val('');
            $('.total').val('');
            // $('.unitName').val('').trigger('chosen:updated');
            $(".catName").val(null).trigger("change");
            $(".proName").val(null).trigger("change");
            //$('.subCat').val('').trigger('chosen:updated');
            findqtyamount();
            findunitamount();
            findgrandtottal();
            checkDepositAndCreditBalance();
        });

        $(document).on('click', '.delete_item', function() {
            // if (confirm("Are you sure?")) {
            //     var id = $(this).attr("del_id");
            //     $('.new_item' + id).remove();
            //     findqtyamount();
            //     findunitamount();
            //     findgrandtottal();
            //     checkDepositAndCreditBalance();
            // }

            let deleteitem = () => {
                var id = $(this).attr("del_id");
                $('.new_item' + id).remove();
                findqtyamount();
                findunitamount();
                findgrandtottal();
                checkDepositAndCreditBalance();
            }

            alertMessage.confirm('You want to remove this', deleteitem);

        });

        $("#from_branch_id").on("change", function() {
            $(".catName").val(null).trigger("change");
            $(".proName").val(null).trigger("change");
        })

    });



    function checkDepositAndCreditBalance() {

        var paymentType = $("#paymentType").val();

        if (paymentType == '') {
            paymentType = 'Cash';
        }

        console.log(paymentType);
        var customer_currentBalance = $("#customer_currentBalance").val();
        // var totalDue = document.getElementById("totalDue").innerText;

        var totalDue = $("#totalDue").text();
        var expireDatas = $("#expireData").val();


        var todaysDate = new Date().toISOString().slice(0, 10);

        if (expireDatas == '') {
            expireDatas = todaysDate;
        }
        var btn = document.getElementById('subMitButton');
        if ((paymentType == 'Deposit') && (parseFloat(customer_currentBalance) < parseFloat(totalDue))) {
            //  console.log('1');
            btn.disabled = true;
        } else if (((paymentType == 'Credit') && (parseFloat(customer_currentBalance) < parseFloat(totalDue))) || (
                expireDatas < todaysDate)) {
            // console.log('2');
            btn.disabled = true;
        } else if (paymentType == 'Cash') {
            //  console.log('3');
            btn.disabled = false;
        } else {
            //  console.log('4');
            btn.disabled = false;
        }

    }
</script>


<script>
    function shipingCalculation(amount) {
        var gtoal = document.getElementById("gtoal").innerText;
        var afterDiscount = (parseFloat(gtoal)) + (parseFloat(amount));
        console.log(afterDiscount);
        $('.abc').text(parseFloat(afterDiscount).toFixed(2));
    }

    function paymentCalculation(payamount) {
        var ntotal = document.getElementById("ntotal").innerText;
        var totalDue = ntotal - payamount;
        $('.finalDue').text(parseFloat(totalDue).toFixed(2));
    }

    function qtyPriceCal(qty) {
        var unitpice = $('#unitpice').val();
        var currentStock = $('#currentStock').val();
        if (parseFloat(qty) > currentStock) {
            $('.ttlamount').val('');
            $('#qty').val('');
            // lert('The desired product stock is not available');
            alertMessage.error('The desired product stock is not available');
        } else {
            var ttlqtys = document.getElementById('total').value = unitpice * qty;
        }
    }

    function getProductList(cat_id) {
        var from_branch_id = $('#from_branch_id').val();
        if (from_branch_id == null) {
            alertMessage.error('From Branch Are not selected');
            return;
        }

        $.ajax({
            "url": "{{ route('inventorySetup.transfer.getProductListTransfer') }}",
            "type": "GET",
            cache: false,
            data: {
                "_token": "{{ csrf_token() }}",
                cat_id: cat_id,
                branch_id: from_branch_id,
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
        $.ajax({
            "url": "{{ route('inventorySetup.purchase.unitPice') }}",
            "type": "GET",
            cache: false,
            data: {
                "_token": "{{ csrf_token() }}",
                productId: productId
            },
            success: function(data) {
                $("#unitpice").val(data);
            }
        });

        $.ajax({
            "url": "{{ route('sale.sale.getProductStock') }}",
            "type": "GET",
            cache: false,
            data: {
                "_token": "{{ csrf_token() }}",
                productId: productId
            },
            success: function(data) {
                $("#currentStock").val(data);
            }
        });
    }
</script>
<script>
    function duplicateBranchCheck() {
        var fromBranch = $('#from_branch_id').val();
        var tobranch = $('#to_branch_id').val();

        if (fromBranch == tobranch) {
            // lert('Branch you give cannot be Same');
            alertMessage.error('Branch you give cannot be Same');

            $("#from_branch_id").val('').select2();
            $("#to_branch_id").val('').select2();
        }
    }
</script>

@endsection
@section('scripts')
@endsection