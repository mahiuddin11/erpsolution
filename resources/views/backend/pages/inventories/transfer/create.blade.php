@extends('backend.layouts.master')
@section('title')
    Stock Transfer - {{ $title }}
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
                    <h1 class="m-0">Stock Transfer</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('inventorySetup.transfer.index'))
                            <li class="breadcrumb-item">
                                <a href="{{ route('inventorySetup.transfer.index') }}">Transfer List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Stock Transfer Create</span></li>
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
                    <h3 class="card-title">Stock Transfer</h3>
                </div>
                <div class="card-body">
                    <form class="needs-validation" method="POST" action="{{ route('inventorySetup.transfer.store') }}"
                        novalidate>
                        @csrf

                        <div class="form-row">

                            {{-- Invoice Number --}}
                            <div class="col-md-3 mb-3">
                                <label>Invoice Number :</label>
                                <input class="bg-green form-control" readonly
                                    style="padding: 5px; font-weight: bold; width: 100%" value="{{ $invoice_no }}">
                                <input type="hidden" name="invoice_no" value="{{ $invoice_no }}">
                            </div>

                            {{-- ✅ FIX: Date format Y-m-d ব্যবহার করা হয়েছে (আগে 'YYYY-mm-dd' ছিল যা PHP এ কাজ করে না) --}}
                            <div class="col-md-3 mb-3">
                                <label>Date:</label>
                                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                    <input type="text" name="date" data-toggle="datetimepicker"
                                        value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
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

                            {{-- From Branch --}}
                            <div class="col-md-3 mb-3">
                                <label>From Branch * :</label>
                                <select class="form-control select2 from_branch" id="from_branch_id" name="from_branch_id"
                                    onchange="duplicateBranchCheck()">
                                    <option selected disabled value="">--Select Branch--</option>
                                    @foreach ($formattedBranches as $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('from_branch_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- To Branch --}}
                            <div class="col-md-3 mb-3">
                                <label>To Branch * :</label>
                                <select class="form-control select2" id="to_branch_id" name="to_branch_id"
                                    onchange="duplicateBranchCheck()">
                                    <option selected disabled value="">--Select Branch--</option>
                                    @foreach ($formattedToBranches as $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_branch_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Product Table --}}
                            <table class="table-responsive table table-bordered w-100">
                                <tr>
                                    <td>
                                        <div class="col-md-9 float-left">Sales Item</div>
                                        <div class="col-md-3 float-right"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px !important;">
                                        <div class="col-md-12">
                                            <div class="col-md-12 float-left">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <table class="table table-bordered table-hover tableAddItem"
                                                            id="show_item">
                                                            <thead>
                                                                <tr>
                                                                    <th nowrap style="width:20%" align="center">
                                                                        <strong>Product Category <span
                                                                                style="color:red;">*</span></strong>
                                                                    </th>
                                                                    <th nowrap style="width:25%" align="center">
                                                                        <strong>Product <span
                                                                                style="color:red;">*</span></strong>
                                                                    </th>
                                                                    <th nowrap style="width:10%" align="center">
                                                                        <strong>Quantity <span
                                                                                style="color:red;">*</span></strong>
                                                                    </th>
                                                                    <th nowrap style="width:12%" align="center">
                                                                        <strong>Unit Price(BDT) <span
                                                                                style="color:red;">*</span></strong>
                                                                    </th>
                                                                    <th nowrap style="width:13%" align="center">
                                                                        <strong>Total Price(BDT) <span
                                                                                style="color:red;">*</span></strong>
                                                                    </th>
                                                                    <th align="center" style="width:5%">
                                                                        <strong>Action</strong>
                                                                    </th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <tr id="input_row">
                                                                    <td>
                                                                        <select onchange="getProductList(this.value)"
                                                                            class="select2 form-control catName"
                                                                            id="form-field-select-3"
                                                                            data-placeholder="Search Category">
                                                                            <option disabled selected>--- Select
                                                                                Category ---</option>
                                                                            @foreach ($category_info as $eachInfo)
                                                                                <option catName="{{ $eachInfo->name }}"
                                                                                    value="{{ $eachInfo->id }}">
                                                                                    {{ $eachInfo->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select class="select2 form-control proName"
                                                                            id="productID" data-placeholder="Search Product"
                                                                            onchange="getUnitPrice(this.value)">
                                                                            <option disabled selected>---Select
                                                                                Product---</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>

                                                                        <small class="text-muted">Available</small>
                                                                        <input type="text" readonly
                                                                            class="form-control mb-1"
                                                                            style="height:16px; background:#f0f4ff; font-weight:bold; color:#1a56db;"
                                                                            id="currentStock" placeholder="Stock: 0">

                                                                        <small class="text-muted">Transfer Qty</small>
                                                                        <input type="number" min="1"
                                                                            style="height:28px;"
                                                                            class="form-control input-qty" id="qty"
                                                                            onkeyup="qtyPriceCal(this.value);"
                                                                            oninput="qtyPriceCal(this.value);"
                                                                            placeholder="0">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" readonly
                                                                            class="form-control text-right input-unitprice"
                                                                            id="unitpice" placeholder="0.00">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            class="form-control text-right input-total"
                                                                            id="total" placeholder="0.00" readonly>
                                                                    </td>
                                                                    <td>
                                                                        <a id="add_item"
                                                                            class="btn btn-info form-control"
                                                                            href="javascript:;" title="Add Item">
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td align="right" colspan="2">
                                                                        <strong>Sub-Total (BDT)</strong>
                                                                    </td>
                                                                    <td align="right">
                                                                        {{-- ✅ tfoot এ আলাদা id ব্যবহার — class loop conflict নেই --}}
                                                                        <strong id="foot-ttlqty">0.00</strong>
                                                                    </td>
                                                                    <td align="right">
                                                                        <strong id="foot-ttlunitprice">0.00</strong>
                                                                    </td>
                                                                    <td align="right">
                                                                        <strong id="foot-grandtotal">0.00</strong>
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <table>
                                                        <tr>
                                                            <td>
                                                                <textarea cols="157" class="form-control" name="narration" placeholder="Note......" style="border:none;"></textarea>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-4 float-right">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <table class="table table-bordered table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td nowrap align="right">
                                                                            <strong>Total</strong>
                                                                        </td>
                                                                        <td align="right">
                                                                            {{-- ✅ FIX: grandtotal class সরানো হয়েছে — loop conflict ছিল --}}
                                                                            <strong id="gtoal"></strong>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td nowrap align="right">
                                                                            <strong>Shipping Charge ( + )</strong>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" autocomplete="off"
                                                                                onkeyup="shipingCalculation(this.value)"
                                                                                id="disCount" style="text-align: right"
                                                                                name="shipping" value=""
                                                                                class="form-control" placeholder="0.00"
                                                                                oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" />
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td nowrap align="right">
                                                                            <strong>Net Total</strong>
                                                                        </td>
                                                                        <td align="right">
                                                                            {{-- ✅ FIX: grandtotal class সরানো হয়েছে, loop conflict ছিল --}}
                                                                            <strong id="ntotal"></strong>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2" style="height: 102px;">
                                                                            <div class="clearfix"></div>
                                                                            <div class="clearfix form-actions float-right">
                                                                                <div class="col-md-offset-1 col-md-10">
                                                                                    <button class="btn btn-info"
                                                                                        id="subMitButton" type="submit">
                                                                                        Save
                                                                                    </button>
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
    </div>

    <script type="text/javascript">
        $(document).ready(function() {

            /*
             * =====================================================
             * ✅ FIX: Calculation functions
             * শুধু item-row class এর hidden input থেকে value নেওয়া হচ্ছে।
             * tfoot strong (#foot-*), #gtoal, #ntotal — এগুলোতে
             * কোনো shared class নেই, তাই double-count সম্পূর্ণ দূর।
             * =====================================================
             */

            // Added rows এর qty sum → tfoot update
            var findqtyamount = function() {
                var ttlqty = 0;
                $('#show_item tbody tr.item-row input.row-qty').each(function() {
                    ttlqty += parseFloat($(this).val()) || 0;
                });
                $('#foot-ttlqty').text(parseFloat(ttlqty).toFixed(2));
            };

            // Added rows এর unit price sum → tfoot update
            var findunitamount = function() {
                var ttlunitprice = 0;
                $('#show_item tbody tr.item-row input.row-unitprice').each(function() {
                    ttlunitprice += parseFloat($(this).val()) || 0;
                });
                $('#foot-ttlunitprice').text(parseFloat(ttlunitprice).toFixed(2));
            };

            // Added rows এর total sum → grand total + net total update
            var findgrandtottal = function() {
                var grandtotal = 0;
                $('#show_item tbody tr.item-row input.row-total').each(function() {
                    grandtotal += parseFloat($(this).val()) || 0;
                });
                $('#foot-grandtotal').text(parseFloat(grandtotal).toFixed(2));
                $('#gtoal').text(parseFloat(grandtotal).toFixed(2));
                var shipping = parseFloat($('#disCount').val()) || 0;
                $('#ntotal').text(parseFloat(grandtotal + shipping).toFixed(2));
            };

            // =====================================================
            // ✅ Add item button
            // =====================================================
            $("#add_item").click(function() {
                var catId = $('#form-field-select-3').val();
                var catName = $('#form-field-select-3').find('option:selected').attr('catName');
                var proId = $('#productID').val();
                var proName = $('#productID').find('option:selected').attr('proName');
                var qty = parseFloat($('#qty').val()) || 0;
                var unitprice = parseFloat($('#unitpice').val()) || 0;
                var total = parseFloat($('#total').val()) || 0;
                var stock = parseFloat($('#currentStock').val()) || 0;

                // Validation
                if (!catId) {
                    alertMessage.error("Category can't be empty.");
                    return false;
                }
                if (!proId) {
                    alertMessage.error("Product can't be empty.");
                    return false;
                }
                if (qty <= 0) {
                    alertMessage.error("Quantity can't be empty or zero.");
                    return false;
                }
                if (qty > stock) {
                    alertMessage.error('Transfer quantity exceeds available stock. Available: ' + stock);
                    return false;
                }

                // ✅ Duplicate product check — data-proid attribute দিয়ে
                if ($('#show_item tbody tr.item-row[data-proid="' + proId + '"]').length > 0) {
                    alertMessage.error(
                        'This product is already added. Remove it first to change quantity.');
                    return false;
                }

                /*
                 * ✅ FIX: Added row এ আলাদা class ব্যবহার:
                 *   - tr.item-row          → calculation loop এর জন্য
                 *   - data-proid           → duplicate check এর জন্য
                 *   - input.row-qty        → qty loop
                 *   - input.row-unitprice  → unit price loop
                 *   - input.row-total      → grand total loop
                 * input row এর id গুলোর (qty, unitpice, total) সাথে কোনো conflict নেই।
                 */
                $('#show_item tbody').append(
                    '<tr class="item-row" data-proid="' + proId + '">' +
                    '<td style="padding-left:12px;">' + catName +
                    '<input type="hidden" name="catName[]" value="' + catId + '"></td>' +
                    '<td>' + proName +
                    '<input type="hidden" name="proName[]" value="' + proId + '"></td>' +
                    '<td align="right">' + qty +
                    '<input type="hidden" class="row-qty" name="qty[]" value="' + qty + '"></td>' +
                    '<td align="right">' + parseFloat(unitprice).toFixed(2) +
                    '<input type="hidden" class="row-unitprice" name="unitprice[]" value="' +
                    unitprice + '"></td>' +
                    '<td align="right">' + parseFloat(total).toFixed(2) +
                    '<input type="hidden" class="row-total" name="total[]" value="' + total +
                    '"></td>' +
                    '<td align="center">' +
                    '<a del_id="' + proId +
                    '" class="delete_item btn btn-sm btn-danger" href="javascript:;" title="Remove">' +
                    '<i class="fa fa-times"></i>' +
                    '</a>' +
                    '</td>' +
                    '</tr>'
                );

                // ✅ Input row reset — id দিয়ে সরাসরি target, class conflict নেই
                $('#qty').val('');
                $('#total').val('');
                $('#unitpice').val('');
                $('#currentStock').val('');
                $('#form-field-select-3').val(null).trigger('change');
                $('#productID').empty()
                    .append('<option disabled selected>---Select Product---</option>')
                    .trigger('change');

                findqtyamount();
                findunitamount();
                findgrandtottal();
            });

            // =====================================================
            // ✅ Delete item — data-proid দিয়ে সঠিক row remove
            // =====================================================
            $(document).on('click', '.delete_item', function() {
                var $this = $(this);
                var deleteitem = function() {
                    var proid = $this.attr('del_id');
                    $('#show_item tbody tr.item-row[data-proid="' + proid + '"]').remove();
                    findqtyamount();
                    findunitamount();
                    findgrandtottal();
                };
                alertMessage.confirm('You want to remove this item?', deleteitem);
            });

            // =====================================================
            // ✅ From Branch change → input row + added rows সব reset
            // =====================================================
            $("#from_branch_id").on("change", function() {
                $('#form-field-select-3').val(null).trigger('change');
                $('#productID').empty()
                    .append('<option disabled selected>---Select Product---</option>')
                    .trigger('change');
                $('#currentStock').val('');
                $('#unitpice').val('');
                $('#qty').val('');
                $('#total').val('');
                // Branch change হলে আগে add করা সব row সরানো
                $('#show_item tbody tr.item-row').remove();
                findqtyamount();
                findunitamount();
                findgrandtottal();
            });

        });

        // =====================================================
        // ✅ Shipping charge calculation
        // =====================================================
        function shipingCalculation(amount) {
            var gtoal = parseFloat($('#gtoal').text()) || 0;
            var shipping = parseFloat(amount) || 0;
            $('#ntotal').text(parseFloat(gtoal + shipping).toFixed(2));
        }

        // =====================================================
        // ✅ Qty input → total price calculate + stock check
        // =====================================================
        function qtyPriceCal(qty) {
            var unitpice = parseFloat($('#unitpice').val()) || 0;
            var currentStock = parseFloat($('#currentStock').val()) || 0;
            var inputQty = parseFloat(qty) || 0;

            if (inputQty > currentStock && currentStock > 0) {
                $('#total').val('');
                $('#qty').val('');
                alertMessage.error('Transfer quantity exceeds available stock. Available: ' + currentStock);
            } else if (inputQty > 0 && unitpice > 0) {
                $('#total').val(parseFloat(unitpice * inputQty).toFixed(2));
            } else {
                $('#total').val('');
            }
        }

        // =====================================================
        // ✅ Category change → branch check করে product list আনা
        // =====================================================
        function getProductList(cat_id) {
            var from_branch_id = $('#from_branch_id').val();
            if (!from_branch_id) {
                alertMessage.error('Please select From Branch first.');
                $('#form-field-select-3').val(null).trigger('change');
                return;
            }
            // Product dropdown reset while loading
            $('#productID').empty().append('<option disabled selected>Loading...</option>');
            $('#currentStock').val('');
            $('#unitpice').val('');
            $('#qty').val('');
            $('#total').val('');

            $.ajax({
                url: "{{ route('inventorySetup.transfer.getProductListTransfer') }}",
                type: "GET",
                cache: false,
                data: {
                    "_token": "{{ csrf_token() }}",
                    cat_id: cat_id,
                    branch_id: from_branch_id,
                },
                success: function(data) {
                    $('#productID').empty();
                    $('#productID').append($(data));
                    $('#productID').trigger('change');
                }
            });
        }

        // =====================================================
        // ✅ Product change → Unit Price + Branch-wise Stock
        // =====================================================
        function getUnitPrice(productId) {
            if (!productId) return;
            var from_branch_id = $('#from_branch_id').val();

            // Unit Price fetch
            $.ajax({
                url: "{{ route('InventorySetup.unitPiceForSale') }}",
                type: "GET",
                cache: false,
                data: {
                    "_token": "{{ csrf_token() }}",
                    productId: productId
                },
                success: function(data) {
                    var parsed = (typeof data === 'string') ? JSON.parse(data) : data;
                    $('#unitpice').val(parsed.purchases_price);
                    // price আসার পর qty থাকলে total recalculate
                    var qty = parseFloat($('#qty').val()) || 0;
                    if (qty > 0) {
                        qtyPriceCal(qty);
                    }
                }
            });

            // Branch-wise Available Stock fetch
            $.ajax({
                url: "{{ route('InventorySetup.getProductStock') }}",
                type: "GET",
                cache: false,
                data: {
                    "_token": "{{ csrf_token() }}",
                    productId: productId,
                    branch_id: from_branch_id
                },
                success: function(data) {
                    $('#currentStock').val(data || 0);
                }
            });
        }

        // =====================================================
        // ✅ From/To Branch duplicate check
        // =====================================================
        function duplicateBranchCheck() {
            var fromBranch = $('#from_branch_id').val();
            var toBranch = $('#to_branch_id').val();
            if (fromBranch && toBranch && fromBranch === toBranch) {
                alertMessage.error('From Branch and To Branch cannot be the same.');
                $('#from_branch_id').val('').trigger('change');
                $('#to_branch_id').val('').trigger('change');
            }
        }
    </script>
@endsection

@section('scripts')
@endsection
