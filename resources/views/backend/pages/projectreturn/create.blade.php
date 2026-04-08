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
                    @if (helper::roleAccess('inventorySetup.projectreturn.index'))
                    <li class="breadcrumb-item"><a href="{{ route('inventorySetup.projectreturn.index') }}">Project</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Return product</span></li>
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
                <h3 class="card-title">Add New Return product</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('inventory-projectreturn-list'))
                    <a class="btn btn-default" href="{{ route('inventory-projectreturn-list') }}"><i
                            class="fa fa-list"></i>
                        Return product List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('project.projectreturn.store') }}"
                    novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <span class="bg-green" style="padding: 5px; font-weight : bold"
                                for="validationCustom01">Stock Return * : {{ $puInv }}</span>
                            <input type="hidden" name="grnCode" class="form-control" id="" value="{{ $puInv }}">
                        </div>
                    </div>
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
                            <label for="validationCustom01">Project * :</label>
                            <select class="form-control select2 project_id" name="project_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach($project as $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->projectCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('project_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Branch * :</label>
                            <select class="form-control select2 branch_id" name="branch_id">
                                <option selected disabled>
                                    --- Select Branch ---
                                </option>
                                @foreach($branchs as $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->branchCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
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
                                <td class="text-center"><strong>Product</strong></td>
                                <td class="text-center" style="width:20%"><strong>Stock </strong></td>
                                <td class="text-center" style="width:20%"><strong>Return Quantity </strong></td>
                                <td class="text-center" style="width:10%"><strong>Action </strong></td>
                            </tr>
                        </thead>
                        <tbody id="main-table">
                            <tr>
                                <td>
                                    <select class="select2 form-control proName reset" id="productID"
                                        data-placeholder="Search Product">
                                        <option disabled selected>---Select Product---</option>

                                    </select>
                                </td>

                                <td>
                                    <input type=" number" step="any" readonly
                                        class="form-control text-right stockcls reset_stock" placeholder="Stock"
                                        min="0">
                                </td>
                                <td>
                                    <input type="number" step="any" min="0" id="useQty"
                                        class="form-control text-right useQty reset_useprice" placeholder="Use Qty">
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
                                <td class="
                                            text-right">
                                    <strong>Sub-Total</strong>
                                </td>
                                <td class="text-right"><strong class="stockcount"></strong></td>
                                <td class="
                                            text-right"><strong class="qtyuse"></strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label for="">Note</label>
                            <textarea name="note" class="form-control" id="" cols="10" rows="5"></textarea>
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

        $('#useQty').on('input', function () {
            var stock = $('.stockcls').val();
            var self = Number($(this).val());
            if (stock < (self)) {
                $(this).val('');
                alertMessage.error('Product quantity is not available in stock');
            }
        });

        $('#productID').on('change', function () {
            let id = $(this).val();
            let project_id = $('.project_id').val();

            $.ajax({
                url: "{{ route('project.projectreturn.searchproduct') }}",
                method: 'GET',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id,
                    project_id: project_id,
                },
                dataType: 'json',
                success: function (data) {
                    $('.stockcls').val(data.stock);
                    $('#useQty').val(data.stock);
                }
            })
        })



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


        var findstockqty = function () {
            var findstockqty = 0;
            $.each($('.stock_qty'), function () {
                stock = Number($(this).val());
                findstockqty += stock;
            });
            $('.stockcount').text(findstockqty);
        };

        var stockuse = function () {
            var use = 0;
            $.each($('.useQty'), function () {
                qty = Number($(this).val());
                use += qty;
            });
            $('.qtyuse').text(use);
        };


        var findqtyamoun = function () {

            var stcokqty = 0;
            $.each($('.stock_qty'), function () {
                qty = $(this).val();
                stcokqty += qty;
            });

            $('.stockcount').text(stcokqty);

        };

        $('.project_id').on('change', function () {
            var self = $(this).val();
            $.ajax({
                url: "{{route('project.projectreturn.getstockdata')}}",
                method: "GET",
                data: {
                    "_token": "{{csrf_token()}}",
                    project_id: self
                },
                success: function (data) {
                    $('#productID').html(data);
                }
            })
            $('.reset_useprice').val('');
            $('.reset_stock').val('');
            $(".reset").val(null).trigger("change");

        })

        var findunitamount = function () {
            var ttlunitprice = 0;
            $.each($('.useQty'), function () {
                unitprice = $(this).val();
                ttlunitprice += unitprice;
            });
            $('.qtyuse').text(ttlunitprice);
        };

        $(document).on('click', '.delete_item', function () {

            let deleteitem = () => {
                $(this).parents('tr').remove();
                findqtyamoun();
                findunitamount();
            }

            alertMessage.confirm('You want to remove this', deleteitem);
        });


        $(document).on('click', '#add_item', function () {

            var parent = $(this).parents('tr');

            var proId = $('.proName').val();

            var proName = $(".proName").find('option:selected').attr('proName');

            var stock = $('.stockcls').val();

            var useQty = $('.useQty').val();

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


            if (useQty == '' || useQty == null || useQty == 0) {
                alertMessage.error('Use Quantity cannot be empty');
                return false;
            } else {
                const row = `
                    <tr class="new_item${proId}">
                        <td class="text-right">${proName}<input type="hidden" class="add_quantity" name="product_nm[]" value="${proId}"></td>
                    
                        <td class="text-right"><input type="number" readonly   class="stock_qty form-control" name="stock[]" value="${stock}"></td>
                        <td class="text-right"><input type="number" readonly id="useQtyid" class="form-control useQty" name="return_Qty[]" value="${useQty}">
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

            $('.reset_useprice').val('');
            $('.reset_stock').val('');
            $(".reset").val(null).trigger("change");

            findstockqty();
            stockuse();
        });
    });

</script>

@endsection