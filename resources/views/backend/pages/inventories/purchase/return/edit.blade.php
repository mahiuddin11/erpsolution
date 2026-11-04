@extends('backend.layouts.master')

@section('title')
    inventory - {{ $title }}
@endsection

@section('styles')
    <style>
        .purchase-item-table-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        #show_item {
            min-width: 900px;
        }

        textarea[name="remarks"] {
            width: 100% !important;
            max-width: 100%;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
            min-height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        @media (max-width: 767px) {
            .form-row>[class*="col-"] {
                margin-bottom: 10px;
            }

            .card-body {
                padding: 10px;
            }

            #show_item {
                min-width: 800px;
            }
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Purchase Return </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('inventorySetup.purchase.return.index'))
                            <li class="breadcrumb-item"><a
                                    href="{{ route('inventorySetup.purchase.return.index') }}">Purchase Return List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Purchase Return</span></li>
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
                    <h3 class="card-title">Edit Purchase Return</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('inventorySetup.purchase.return.index'))
                            <a class="btn btn-default" href="{{ route('inventorySetup.purchase.return.index') }}"><i
                                    class="fa fa-list"></i> Purchase Return List</a>
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
                        action="{{ route('inventorySetup.purchase.return.update', $purchaseReturn->id) }}" id="return-form"
                        novalidate>
                        @csrf
                        @method('PUT')
                        <div class="form-row">
                            <div class="col-md-2 col-sm-6 col-12 mb-3">
                                <label>Return No :</label>
                                <input class="bg-green form-control" readonly
                                    style="padding: 5px; font-weight: bold; width: 100%"
                                    value="{{ $purchaseReturn->return_no }}">
                            </div>
                            <div class="col-md-2 col-sm-6 col-12 mb-3">
                                <label>Return Date * :</label>
                                <input type="date" name="return_date" class="form-control"
                                    value="{{ old('return_date', $purchaseReturn->return_date ? \Carbon\Carbon::parse($purchaseReturn->return_date)->format('Y-m-d') : date('Y-m-d')) }}"
                                    required>
                                @error('return_date')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-2 col-sm-6 col-12 mb-3">
                                <label for="validationCustom01">Branch * :</label>
                                <select class="form-control select2" id="branch_id" name="branch_id" required
                                    onchange="getWarehousesByBranch(this.value)">
                                    <option selected disabled value="">--Select Branch--</option>
                                    @foreach ($branch as $value)
                                        <option value="{{ $value->id }}"
                                            {{ old('branch_id', $purchaseReturn->branch_id) == $value->id ? 'selected' : '' }}>
                                            {{ $value->branchCode . ' - ' . $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-2 col-sm-6 col-12 mb-3">
                                <label for="validationCustom02">Warehouse * :</label>
                                <select class="form-control select2" id="sub_warehouse_id" name="warehouse_id" required>
                                    <option disabled value="">--Select Warehouse--</option>
                                    @foreach ($wearhouses as $value)
                                        <option value="{{ $value->id }}"
                                            {{ old('warehouse_id', $purchaseReturn->warehouse_id) == $value->id ? 'selected' : '' }}>
                                            {{ ($value->warehouseCode ?? ($value->branchCode ?? '')) . ' - ' . $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="warehouse_source" id="warehouse_source" value="">
                                @error('warehouse_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-2 col-sm-6 col-12 mb-3">
                                <label for="ledger_id">Ledger * :
                                    <button type="button" class="btn btn-sm btn-primary ml-2" data-toggle="modal"
                                        data-target="#addSupplierModal">+</button>
                                </label>
                                <select class="form-control select2 supid" name="ledger_id" id="ledger_id" required>
                                    <option selected disabled value="">--Select Ledger--</option>
                                    <x-account :setAccounts="$ledgers" :selectVal="old('ledger_id', $purchaseReturn->ledger_id)" />
                                </select>
                                @error('ledger_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="table-responsive purchase-item-table-wrapper">
                                    <table class="table table-bordered table-hover" id="show_item">
                                        <thead>
                                            <tr>
                                                <th colspan="9">Select Product Item</th>
                                            </tr>
                                            <tr>
                                                <td class="text-center"><strong>Category</strong></td>
                                                <td class="text-center"><strong>Product</strong></td>
                                                <td class="text-center"><strong>Type</strong></td>
                                                <td class="text-center"><strong>Current Stock</strong></td>
                                                <td class="text-center"><strong>Return Qty</strong></td>
                                                <td class="text-center"><strong>Unit Price</strong></td>
                                                <td class="text-center"><strong>Total</strong></td>
                                                <td class="text-center"><strong>Reason</strong></td>
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
                                                        <?php foreach ($category_info as $eachInfo) : ?>
                                                        <option catName="{{ $eachInfo->name }}"
                                                            value="{{ $eachInfo->id }}">{{ $eachInfo->name }}</option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="select2 form-control proName reset" id="productID"
                                                        data-placeholder="Search Product"
                                                        onchange="getProductStockData()">
                                                        <option disabled selected>---Select Product---</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="select2 form-control purchasetype" id="purchasetype"
                                                        data-placeholder="Search Product"
                                                        onchange="getProductStockData()">
                                                        @foreach (config('purchaseType') as $key => $value)
                                                            <option value="{{ $key }}">{{ $value }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control text-right"
                                                        id="current-stock-display" value="" readonly
                                                        placeholder="—">
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
                                                    <input type="text" class="form-control reasoninput reset_reason"
                                                        placeholder="Reason (optional)">
                                                </td>
                                                <td>
                                                    <a id="add_item" class="btn btn-info" style="white-space: nowrap"
                                                        href="javascript:;" title="Add Item">
                                                        <i class="fa fa-plus"></i> Add Item
                                                    </a>
                                                </td>
                                            </tr>
                                            @foreach ($items as $item)
                                                @php
                                                    $proId = $item['product_id'];
                                                    $lineTotal = round(
                                                        (float) $item['returned_qty'] * (float) $item['unit_price'],
                                                        2,
                                                    );
                                                    $typeText =
                                                        config('purchaseType')[$item['purchasetype']] ??
                                                        ucfirst($item['purchasetype']);
                                                @endphp
                                                <tr class="new_item{{ $proId }}">
                                                    <td style="padding-left:15px;">{{ $item['category_name'] ?? '—' }}
                                                    </td>
                                                    <td class="text-right">{{ $item['product_name'] }}<input
                                                            type="hidden" class="add_quantity"
                                                            product-id="{{ $proId }}" name="product_id[]"
                                                            value="{{ $proId }}"></td>
                                                    <td class="text-right">{{ $typeText }}<input type="hidden"
                                                            name="purchasetype[]" value="{{ $item['purchasetype'] }}">
                                                    </td>
                                                    <td class="text-right">—</td>
                                                    <td class="text-right">{{ $item['returned_qty'] }}<input
                                                            type="hidden" class="ttlqty" name="returned_qty[]"
                                                            value="{{ $item['returned_qty'] }}"></td>
                                                    <td class="text-right">{{ $item['unit_price'] }}<input type="hidden"
                                                            class="ttlunitprice" name="unit_price[]"
                                                            value="{{ $item['unit_price'] }}"></td>
                                                    <td class="text-right">{{ number_format($lineTotal, 2) }}<input
                                                            type="hidden" class="total checktotal"
                                                            value="{{ $lineTotal }}"></td>
                                                    <td class="text-right">{{ $item['reason'] ?? '' }}<input
                                                            type="hidden" name="reason[]"
                                                            value="{{ $item['reason'] }}"></td>
                                                    <td>
                                                        <a del_id="{{ $proId }}"
                                                            class="delete_item btn form-control btn-danger"
                                                            href="javascript:;" title="">
                                                            <i class="fa fa-times"></i>&nbsp;Remove
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-right"><strong>Sub-Total(BDT)</strong></td>
                                                <td class="text-right"><strong></strong></td>
                                                <td class="text-right"><strong></strong></td>
                                                <td class="text-right"><strong class="ttlqty"></strong></td>
                                                <td class="text-right"><strong class="ttlunitprice"></strong></td>
                                                <td class="text-right"><strong class="grandtotal"></strong></td>
                                                <td class="text-right"><strong></strong></td>
                                                <td class="text-right"><strong></strong></td>
                                                <td class="text-right"><strong></strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-8 col-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <textarea rows="4" class="form-control" name="remarks" placeholder="Remarks (optional)">{{ old('remarks', $purchaseReturn->remarks) }}</textarea>
                                    </div>
                                </div>
                                @error('remarks')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="cart_output">
                                        <tr>
                                            <th><span>Grand Total</span></th>
                                            <th class="text-right"><span class="grandtotal"></span></th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="form-group float-right">
                            <button class="btn btn-primary" type="submit" id="submit">
                                <i class="fa fa-save"></i>&nbsp; &nbsp; Update Return
                            </button>
                            <a href="{{ route('inventorySetup.purchase.return.index') }}"
                                class="btn btn-default">Cancel</a>
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
        function initPurchaseSelect2() {
            $('.select2').not('#productID').not('.accounts').each(function() {
                let $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                let $modal = $select.closest('.modal');
                let options = {
                    width: '100%',
                    dropdownAutoWidth: false
                };
                if ($modal.length) {
                    options.dropdownParent = $modal;
                }
                $select.select2(options);
            });
        }

        // পেজ Bootstrap modal-এর ভেতরে দ্বিতীয়/তৃতীয়বার লোড হলে select2 রি-ইনিট করা
        $(document).on('shown.bs.modal', '.modal', function() {
            let $modal = $(this);
            $modal.find('.select2').each(function() {
                let $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.select2({
                    width: '100%',
                    dropdownAutoWidth: false,
                    dropdownParent: $modal
                });
            });
        });

        // প্রথম item add করার পর Branch, Warehouse, Ledger, Supplier, Project লক / সব item মুছলে আনলক
        function updateHeaderLock() {
            let itemCount = $('#show_item tbody tr[class^="new_item"]').length;
            let lock = itemCount > 0;
            $('#branch_id, #sub_warehouse_id, #ledger_id, #supplier_id, #project_id').prop('disabled', lock);
            $('[data-target="#addSupplierModal"]').prop('disabled', lock);
        }

        function esc(s) {
            return String(s ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g,
                '&gt;');
        }

        function number_format(number, decimal = 2) {
            number = Number(number);
            return Number(parseFloat(number).toFixed(decimal));
        }

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
            $('.grandtotal').text(number_format(grandtotal));
        };

        function getProductList(cat_id) {
            $('#current-stock-display').val('');
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
                    if ($('#productID').hasClass('select2-hidden-accessible')) {
                        $('#productID').select2('destroy');
                    }
                    $('#productID option').remove();
                    $('#productID').append($(data));
                    $('#productID').select2({
                        width: '100%',
                        dropdownAutoWidth: false
                    });
                    $("#productID").trigger("select2:updated");
                }
            });
        }

        function getWarehousesByBranch(branch_id) {
            if (!branch_id) return;

            $.ajax({
                url: "{{ route('inventorySetup.getWarehousesByBranch') }}",
                type: "GET",
                dataType: 'json',
                cache: false,
                data: {
                    "_token": "{{ csrf_token() }}",
                    branch_id: branch_id
                },
                success: function(response) {
                    let $warehouseSelect = $('#sub_warehouse_id');

                    $warehouseSelect.select2('destroy');

                    $warehouseSelect.empty().append(
                        '<option selected disabled value="">--Select Warehouse--</option>');

                    if (response.data.length > 0) {
                        $.each(response.data, function(index, item) {
                            $warehouseSelect.append(
                                `<option value="${item.id}">${item.text}</option>`
                            );
                        });
                    } else {
                        $warehouseSelect.append('<option disabled value="">--No Warehouse Found--</option>');
                    }

                    $('#warehouse_source').val(response.source);

                    $warehouseSelect.select2({
                        width: '100%',
                        dropdownAutoWidth: false
                    });
                },
                error: function() {
                    alertMessage.error('Failed to load warehouses for this branch.');
                }
            });
        }

        // Sale create-এর getProductStockData() — Product/Type বাছলেই stock দেখাবে
        function getProductStockData() {
            let branch_id = $('#branch_id option:selected').val();
            let warehouseId = $('#sub_warehouse_id option:selected').val();
            let purchasetype = $('.purchasetype option:selected').val();
            let productId = $('#productID option:selected').val();

            if (!productId) {
                return;
            }

            if (!warehouseId) {
                alertMessage.error('Please Select Warehouse first to check stock.');
                $('#current-stock-display').val('');
                return;
            }

            $.ajax({
                "url": "{{ route('sale.sale.getProductStock') }}",
                "type": "GET",
                cache: false,
                data: {
                    "_token": "{{ csrf_token() }}",
                    productId: productId,
                    type: purchasetype,
                    warehouseId: warehouseId,
                },
                success: function(data) {
                    $("#current-stock-display").val(data);
                }
            });
        }

        $(document).ready(function() {
            initPurchaseSelect2();

            // Warehouse বদলালেও stock refresh হবে
            $(document).on('change', '#sub_warehouse_id', getProductStockData);

            // আগে থেকে থাকা item-গুলোর total + header lock হিসাব করা
            findqtyamoun();
            findunitamount();
            findgrandtottal();
            updateHeaderLock();

            $('form.needs-validation').on('submit', function(e) {
                if (!$('#branch_id').val()) {
                    alertMessage.error('Please select a Branch.');
                    e.preventDefault();
                    return false;
                }
                if (!$('#sub_warehouse_id').val()) {
                    alertMessage.error('Please select a Warehouse.');
                    e.preventDefault();
                    return false;
                }
                if (!$('#ledger_id').val()) {
                    alertMessage.error('Please select a Ledger.');
                    e.preventDefault();
                    return false;
                }
                if ($('#show_item tbody tr[class^="new_item"]').length === 0) {
                    alertMessage.error('Please add at least one product.');
                    e.preventDefault();
                    return false;
                }

                // submit-এর আগে enable করুন, নাহলে disabled select-এর মান সার্ভারে যাবে না
                $('#branch_id, #sub_warehouse_id, #ledger_id, #supplier_id, #project_id').prop('disabled',
                    false);
            });

            // Supplier Create (modal)
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

            $(document).on('click', '#add_item', function() {
                var parent = $(this).parents('tr');

                var supid = $('#ledger_id').val();
                var purchasetypeval = $('.purchasetype').find('option:selected').val();
                var purchasetypetext = $('.purchasetype').find('option:selected').text();

                var catId = $('.catName').val();
                var catName = $(".catName").find('option:selected').text();

                var proId = $('.proName').val();
                var proName = $(".proName").find('option:selected').text();

                var qty = parseFloat(parent.find('.qty').val());
                var unitprice = parseFloat(parent.find('.unitprice').val());
                var reason = parent.find('.reasoninput').val() || '';
                var stockText = $('#current-stock-display').val();
                var currentStock = parseFloat(stockText);

                if (supid == '' || supid == null) {
                    alertMessage.error("Ledger can't be empty.");
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
                if (isNaN(qty) || qty <= 0) {
                    alertMessage.error('Quantity cannot be empty');
                    return false;
                }
                if (isNaN(unitprice)) {
                    alertMessage.error('Unit price cannot be empty');
                    return false;
                }
                if (!isNaN(currentStock) && qty > currentStock) {
                    alertMessage.error('Return quantity cannot be greater than current stock (' +
                        currentStock + ').');
                    return false;
                }

                // start check duplicate product
                if ($('tbody').find('.new_item' + proId).length > 0) {
                    alertMessage.error('This product already exist');
                    return;
                }
                // end check duplicate product

                var total = Number((qty * unitprice).toFixed(2));

                const row = `
                    <tr class="new_item${proId}">
                        <td style="padding-left:15px;">${catName}</td>
                        <td class="text-right">${proName}<input type="hidden" class="add_quantity" product-id="${proId}" name="product_id[]" value="${proId}"></td>
                        <td class="text-right">${purchasetypetext}<input type="hidden" name="purchasetype[]" value="${purchasetypeval}"></td>
                        <td class="text-right">${stockText || '—'}</td>
                        <td class="text-right">${qty}<input type="hidden" class="ttlqty" name="returned_qty[]" value="${qty}"></td>
                        <td class="text-right">${unitprice}<input type="hidden" class="ttlunitprice" name="unit_price[]" value="${unitprice}"></td>
                        <td class="text-right">${total}<input type="hidden" class="total checktotal" value="${total}"></td>
                        <td class="text-right">${esc(reason)}<input type="hidden" name="reason[]" value="${esc(reason)}"></td>
                        <td>
                            <a del_id="${proId}" class="delete_item btn form-control btn-danger" href="javascript:;" title="">
                                <i class="fa fa-times"></i>&nbsp;Remove
                            </a>
                        </td>
                    </tr>
                `;
                $("#show_item tbody").append(row);

                $('.reset_unitprice').val('');
                $('.reset_qty').val('');
                $('.reset_total').val('');
                $('.reset_reason').val('');
                $('#current-stock-display').val('');
                $(".reset").val(null).trigger("change");

                findqtyamoun();
                findunitamount();
                findgrandtottal();
                updateHeaderLock();
            });

            $(document).on('click', '.delete_item', function() {
                let deleteitem = () => {
                    $(this).parents('tr').remove();
                    findqtyamoun();
                    findunitamount();
                    findgrandtottal();
                    updateHeaderLock();
                }

                alertMessage.confirm('You want to remove this', deleteitem);
            });


            $(document).on('input', '.qty, .unitprice', function() {
                let parent = $(this).parents('tr');
                let qty = parseFloat(parent.find('.qty').val()) || 0;
                let unitPrice = parseFloat(parent.find('.unitprice').val()) || 0;
                let currentStock = parseFloat($('#current-stock-display').val());
                if ($(this).hasClass('qty') && !isNaN(currentStock) && qty > currentStock) {
                    parent.find('.qty').val('');
                    parent.find('.total').val('');
                    alertMessage.error('The desired product stock is not available.');
                    return;
                }
                parent.find('.total').val((unitPrice * qty).toFixed(2));
            });
        });
    </script>
@endsection
