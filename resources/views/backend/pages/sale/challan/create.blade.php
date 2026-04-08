@extends('backend.layouts.master')

@section('title')
Delivary Challan - {{ $title }}
@endsection
@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"> Delivary Challan </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('sale.challan.index'))
                    <li class="breadcrumb-item"><a href="{{ route('sale.challan.index') }}">Delivary Challan
                        </a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Delivary Challan</span></li>
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
                <h3 class="card-title">Add New Delivary Challan</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('sale-challan-list'))
                    <a class="btn btn-default" href="{{ route('sale-challan-list') }}"><i class="fa fa-list"></i>
                        delivary Challan List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('sale.challan.store') }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <span class="bg-green" style="padding: 5px; font-weight : bold" for="validationCustom01">
                                Chalan Code * : {{ $invoice_no }}</span>
                            <input type="hidden" name="deliveryCode" class="form-control" value="{{ $invoice_no }}">
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
                            <label for="validationCustom01">Sale Invoice * :</label>
                            <select class="form-control select2" id="sales_id" name="sales_id">
                                <option selected disabled value="">--Select--</option>
                                @php echo $sales @endphp
                            </select>
                            @error('purchase_requisition')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Customer * :</label>
                            <select class="form-control select2 coustomer_id" id="customer_id" name="coustomer_id">
                                <option selected disabled value="">--Select--</option>
                                {{-- @foreach ($customer as $key => $value)
                                <option value="{{ $value->id }}">
                                {{ $value->customerCode . ' - ' . $value->name }}
                                </option>
                                @endforeach --}}
                            </select>
                            @error('customer_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Branch * :</label>
                            <select readonly id="branch_id" class="form-control select2" name="branch_id">
                                <option selected disabled value="">--Select--</option>
                            </select>
                            @error('branch_id')
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


                        </tbody>

                        <tfoot>

                            <tr>
                                <td class="
                                            text-right">
                                    <strong>Sub-Total(BDT)</strong>
                                </td>
                                <td class="text-right"><strong class=""></strong></td>
                                <td class="
                                            text-right"><strong class="ttlqty"></strong>
                                </td>

                                <td class="text-right"><strong class=""></strong></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="
                        form-row form-group">
                        <div class="col-md-8">
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

        $('#sales_id').on('change', function () {

            let saleId = $(this).val();

            $.ajax({
                url: "{{ route('sale.challan.salesDetails') }}",
                method: 'GET',
                data: {
                    "_token": "{{ csrf_token() }}",
                    saleId: saleId
                },
                dataType: 'json',
                success: function (data) {
console.log(data);
                    $('.delrow').remove();
                    $('#main-table').append(data.prdetails);
                    $('#branch_id').html(data.branch);
                    $('#customer_id').html(data.customer);
                    $('.paid_amount').attr('disabled', false);
                    $('.reset_unitprice').val('');
                    $('.reset_qty').val('');
                    $('.reset_total').val('');
                    $(".reset").val(null).trigger("change");


                }
            })
        })



        $(document).on('click', '#add_item', function () {

            var parent = $(this).parents('tr');

            var supid = $('.subblier_id').val();
            var catId = $('.catName').val();
            var purreq = $('#purreq').val();

            var catName = $(".catName").find('option:selected').attr('catName');

            var proId = $('.proName').val();
            var proName = $(".proName").find('option:selected').attr('proName');

            var qty = number_format(parent.find('.qty').val());

            if (purreq == '' || purreq == null) {
                alertMessage.error("Purchase Requisition can't be empty.");
                return false;
            }
            if (supid == '' || supid == null) {
                alertMessage.error("Supplier can't be empty.");
                return false;
            }
            if (catId == '' || catId == null) {
                alertMessage.error("Category can't be empty.");
                return false;
            }
            if (proId == '' || proId == null) {
                alertMessage.error("Product can't be empty.");
                return false;
            }

            var unitprice = number_format(parent.find('.unitprice').val());

            // start check duplicate product  
            let seaschproduct = $('#productID option:selected')[0].getAttribute("value");
            let tbody = $('#main-table').find(".new_item" + seaschproduct).length;
            // let tbody2 = $('main-table').find("new_item" + seaschproduct);
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
                        <td class="text-right"><input type="number" class="form-control ttlqty qnty" name="qty[]" value="${qty}"></td>
                        <td class="text-right">${unitprice}<input type="hidden" class="ttlunitprice unitprice" id="unitprice" name="unitprice[]" value="${unitprice}">
                        </td>
                        <td class="text-right">
                            <input type="text" class="total form-control" readonly name="total[]" value="${total}">
                        </td>
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

        });

        $(document).on('click', '.delete_item', function () {

            let deleteitem = () => {
                $(this).parents('tr').remove();

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
            let qty = parent.find('.saleqty').val();

            if (qty < self.val()) {
                alertMessage.error('Can not increase');
                self.val('')
            } else {
                return true;
            }

        });

        $(document).on('input', '.input-checker', function () {
            var grandtotal = $('.grandtotal').text();
            grandtotal = Number(grandtotal);

            if (isNaN(grandtotal) || grandtotal < 1) {
                // lert('Please Add some item first.');
                alertMessage.error('Please Add some item first.');

                return false;
            }


        });

    });


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

        //get balance of selected account
</script>
@endsection