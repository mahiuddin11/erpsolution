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
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>

                        @if (helper::roleAccess('inventorySetup.stockAdjustment.index'))
                            <li class="breadcrumb-item">
                                <a href="{{ route('inventorySetup.stockAdjustment.index') }}">Stock Adjustment List</a>
                            </li>
                        @endif

                        <li class="breadcrumb-item active"><span>Edit Stock Adjustment</span></li>
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
                    <h3 class="card-title">Edit Stock Adjustment</h3>

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
                        action="{{ route('inventorySetup.stockAdjustment.update', $editInfo->id) }}" novalidate>
                        @csrf

                        <div class="form-row">
                            {{-- Invoice Number --}}
                            <div class="col-md-2 mb-3">
                                <label>Invoice Number :</label>
                                <input class="bg-green form-control" readonly
                                    style="padding: 5px; font-weight: bold; width: 100%"
                                    value="{{ $editInfo->invoice_no }}">
                            </div>

                            {{-- Date --}}
                            <div class="col-md-2 mb-3">
                                <label>Date * :</label>

                                @php
                                    $date = $editInfo->date
                                        ? \Carbon\Carbon::parse($editInfo->date)->format('Y-m-d')
                                        : '';
                                @endphp

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
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Branch --}}
                            <div class="col-md-3 mb-3">
                                <label>Branch * :</label>

                                <select class="form-control select2" id="branch_id" name="branch_id">
                                    <option disabled value="">--Select Branch--</option>

                                    @foreach ($branch as $value)
                                        <option value="{{ $value->id }}"
                                            {{ $editInfo->branch_id == $value->id ? 'selected' : '' }}>
                                            {{ $value->branchCode . ' - ' . $value->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('branch_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Warehouse (branch-er upor depend kore, JS diye load hoy) --}}
                            <div class="col-md-3 mb-3">
                                <label>Warehouse * :</label>

                                <select class="form-control select2" id="warehouse_id" name="warehouse_id"
                                    data-placeholder="--Select Warehouse--" data-selected="{{ $editInfo->warehouse_id }}">
                                    <option selected disabled value="">--Select Warehouse--</option>
                                </select>

                                @error('warehouse_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Adjustment Type --}}
                            <div class="col-md-2 mb-3">
                                <div class="form-group">
                                    <label>Adjustment Type * :</label>

                                    <select class="form-control select2" name="adjustment_type">
                                        <option disabled value="">--Adjustment Type--</option>
                                        <option value="Gain"
                                            {{ $editInfo->adjustment_type == 'Gain' ? 'selected' : '' }}>Gain</option>
                                        <option value="Loss"
                                            {{ in_array($editInfo->adjustment_type, ['Loss', 'Lost']) ? 'selected' : '' }}>
                                            Loss</option>
                                        <option value="Damage"
                                            {{ $editInfo->adjustment_type == 'Damage' ? 'selected' : '' }}>Damage
                                        </option>
                                    </select>

                                    @error('adjustment_type')
                                        <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Product Table --}}
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover" id="show_item">
                                    <thead>
                                        <tr>
                                            <th colspan="7">Select Product Item</th>
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
                                        {{-- Entry row --}}
                                        <tr>
                                            <td>
                                                <select onchange="getProductList(this.value)"
                                                    class="select2 form-control catName reset" id="form-field-select-3"
                                                    data-placeholder="Search Category">
                                                    <option disabled selected>---Select Category---</option>

                                                    @foreach ($category_info as $eachInfo)
                                                        <option catName="{{ $eachInfo->name }}"
                                                            value="{{ $eachInfo->id }}">
                                                            {{ $eachInfo->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td>
                                                <select class="select2 form-control proName reset" id="productID"
                                                    data-placeholder="Search Product" onchange="getUnitPrice(this.value)">
                                                    <option disabled selected>---Select Product---</option>
                                                </select>
                                            </td>

                                            {{-- create/approve blade er moto same value: local / imported --}}
                                            <td>
                                                <select class="select2 form-control purchaseType reset" id="purchaseType"
                                                    data-placeholder="Select Type">
                                                    <option disabled selected value="">--Type--</option>
                                                    <option value="local">Local</option>
                                                    <option value="imported">Imported</option>
                                                </select>
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

                                        {{-- Existing items --}}
                                        @foreach ($editInfo->details as $detail)
                                            <tr class="new_item">
                                                <td style="padding-left:15px;">
                                                    {{ $detail->product->category->name ?? '' }}
                                                    <input type="hidden" name="catName[]"
                                                        value="{{ $detail->product->category->id ?? '' }}">
                                                    <input type="hidden" name="stockDetailsId[]"
                                                        value="{{ $detail->id }}">
                                                </td>

                                                <td class="text-right">
                                                    {{ $detail->product->name ?? '' }}
                                                    <input type="hidden" class="add_quantity" name="proName[]"
                                                        value="{{ $detail->product->id ?? '' }}">
                                                </td>

                                                <td class="text-right">
                                                    {{ $detail->purchase_type ?? '' }}
                                                    <input type="hidden" name="purchaseType[]"
                                                        value="{{ $detail->purchase_type ?? '' }}">
                                                </td>

                                                <td class="text-right">
                                                    {{ $detail->quantity }}
                                                    <input type="hidden" name="qty[]"
                                                        value="{{ $detail->quantity }}">
                                                </td>

                                                <td class="text-right">
                                                    {{ $detail->unit_price }}
                                                    <input type="hidden" name="unitprice[]"
                                                        value="{{ $detail->unit_price }}">
                                                </td>

                                                <td class="text-right">
                                                    {{ $detail->total_price }}
                                                    <input type="hidden" name="total[]"
                                                        value="{{ $detail->total_price }}">
                                                </td>

                                                <td>
                                                    <a class="delete_item btn form-control btn-danger"
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
                                            <td></td>
                                            <td></td>
                                            <td class="text-right"><strong id="sum_qty">0</strong></td>
                                            <td></td>
                                            <td class="text-right"><strong class="grandtotal">0</strong></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        {{-- Narration + Total --}}
                        <div class="row mb-2">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <div class="input-group">
                                        <textarea cols="100" rows="3" class="form-control" name="narration" placeholder="Narration">{{ $editInfo->note ?? '' }}</textarea>
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
                                        <th width="35%" class="text-right"><span class="grandtotal">0</span></th>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-info" type="submit">
                                <i class="fa fa-save"></i>&nbsp;Save
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
            | Warehouse load (branch wise)
            |--------------------------------------------------------------------------
            */

            let warehouseXhr = null;

            function loadWarehouses(branchId, selectedId = null) {
                const $wh = $('#warehouse_id');
                const placeholder = '<option selected disabled value="">--Select Warehouse--</option>';

                if (warehouseXhr) {
                    warehouseXhr.abort();
                }

                if (!branchId) {
                    $wh.html(placeholder).trigger('change.select2');
                    return;
                }

                $wh.html('<option selected disabled value="">Loading Warehouse...</option>')
                    .trigger('change.select2');

                warehouseXhr = $.ajax({
                    url: "{{ route('inventorySetup.stockAdjustment.getWarehouseList') }}",
                    type: 'GET',
                    cache: false,
                    data: {
                        branch_id: branchId
                    },
                    success: function(data) {
                        $wh.html(placeholder).append(data);

                        if (selectedId) {
                            $wh.val(String(selectedId));
                        }

                        $wh.trigger('change.select2');
                    },
                    error: function(xhr, status) {
                        if (status === 'abort') {
                            return;
                        }

                        $wh.html(placeholder).trigger('change.select2');
                    }
                });
            }

            // page load: save kora branch + warehouse selected thakbe
            loadWarehouses($('#branch_id').val(), $('#warehouse_id').data('selected'));

            // branch change: notun branch er warehouse ashbe
            $(document).on('change', '#branch_id', function() {
                loadWarehouses($(this).val());
            });

            /*
            |--------------------------------------------------------------------------
            | Add Item
            |--------------------------------------------------------------------------
            */

            $(document).on('click', '#add_item', function() {
                const parent = $(this).parents('tr');
                const catId = $('.catName').val();
                const catName = $('.catName').find('option:selected').attr('catName');
                const proId = $('.proName').val();
                const proOpt = $('.proName').find('option:selected');
                const proName = proOpt.attr('proName') || proOpt.text().trim();
                const purchaseType = $('.purchaseType').val();
                const qty = number_format(parent.find('.qty').val());
                const unitprice = number_format(parent.find('.unitprice').val());

                if (!catId) {
                    alertMessage.error('Please select a Category.');
                    return false;
                }

                if (!proId) {
                    alertMessage.error('Please select a Product.');
                    return false;
                }

                if (!purchaseType) {
                    alertMessage.error('Please select Product Type (Local/Imported).');
                    return false;
                }

                if (!qty || qty <= 0) {
                    alertMessage.error('Please enter a valid quantity.');
                    return false;
                }

                // same product + same type duplicate block
                let duplicate = false;

                $('#show_item tbody tr').each(function() {
                    const p = $(this).find('input[name="proName[]"]').val();
                    const t = ($(this).find('input[name="purchaseType[]"]').val() || '')
                        .toLowerCase();

                    if (p == proId && t == purchaseType.toLowerCase()) {
                        duplicate = true;
                        return false;
                    }
                });

                if (duplicate) {
                    alertMessage.error('This product (with same type) is already added.');
                    return false;
                }

                const total = number_format(qty * unitprice);

                const row = `
                    <tr class="new_item${proId}">
                        <td style="padding-left:15px;">
                            ${catName}
                            <input type="hidden" name="catName[]" value="${catId}">
                            <input type="hidden" name="stockDetailsId[]" value="0">
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
                            <input type="hidden" name="qty[]" value="${qty}">
                        </td>
                        <td class="text-right">
                            ${unitprice}
                            <input type="hidden" name="unitprice[]" value="${unitprice}">
                        </td>
                        <td class="text-right">
                            ${total}
                            <input type="hidden" name="total[]" value="${total}">
                        </td>
                        <td>
                            <a del_id="${proId}" class="delete_item btn form-control btn-danger"
                                href="javascript:;" title="">
                                <i class="fa fa-times"></i>&nbsp;Remove
                            </a>
                        </td>
                    </tr>
                `;

                $('#show_item tbody').append(row);

                $('.reset_unitprice').val('');
                $('.reset_qty').val('');
                $('.reset_total').val('');
                $('.reset').val(null).trigger('change');

                recalcTotals();
            });

            /*
            |--------------------------------------------------------------------------
            | Delete Item
            |--------------------------------------------------------------------------
            */

            $(document).on('click', '.delete_item', function() {
                const row = $(this).parents('tr');

                alertMessage.confirm('You want to remove this', function() {
                    row.remove();
                    recalcTotals();
                });
            });

            /*
            |--------------------------------------------------------------------------
            | Qty * Unit Price (entry row)
            |--------------------------------------------------------------------------
            */

            $(document).on('input', '.qty, .unitprice', function() {
                const parent = $(this).parents('tr');
                const qty = parseFloat(parent.find('.qty').val()) || 0;
                const unitPrice = parseFloat(parent.find('.unitprice').val()) || 0;

                parent.find('.total').val(number_format(qty * unitPrice));
            });

            // page load e total hisab
            recalcTotals();
        });

        /*
        |--------------------------------------------------------------------------
        | Totals
        |--------------------------------------------------------------------------
        */

        function recalcTotals() {
            let qty = 0;
            let grand = 0;

            $('input[name="qty[]"]').each(function() {
                qty += parseFloat($(this).val()) || 0;
            });

            $('input[name="total[]"]').each(function() {
                grand += parseFloat($(this).val()) || 0;
            });

            qty = number_format(qty);
            grand = number_format(grand);

            $('#sum_qty').text(qty);
            $('.grandtotal').text(grand);

            $('.input_vat').val(0);
            $('.input_net_total').val(grand);
            $('.input_due').val(0);
        }

        function number_format(number, decimal = 2) {
            number = Number(number);
            return Number(parseFloat(number).toFixed(decimal));
        }

        /*
        |--------------------------------------------------------------------------
        | Product list / unit price
        |--------------------------------------------------------------------------
        */

        function getProductList(cat_id) {
            if (cat_id == '' || cat_id == null || cat_id == 0) {
                return false;
            }

            $.ajax({
                url: "{{ route('inventorySetup.stockAdjustment.getProductListforadjust') }}",
                type: 'GET',
                cache: false,
                data: {
                    cat_id: cat_id
                },
                success: function(data) {
                    $('#productID').select2();
                    $('#productID option').remove();
                    $('#productID').append($(data));
                    $('#productID').trigger('select2:updated');
                }
            });
        }

        function getUnitPrice(productId) {
            if (productId == '' || productId == null || productId == 0) {
                return false;
            }

            $.ajax({
                url: "{{ route('inventorySetup.stockAdjustment.unitPriceforadjust') }}",
                type: 'GET',
                cache: false,
                data: {
                    productId: productId
                },
                success: function(data) {
                    $('#unitprice').val(data);
                }
            });
        }
    </script>
@endsection
