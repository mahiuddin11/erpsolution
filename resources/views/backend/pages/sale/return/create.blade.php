@extends('backend.layouts.master')
@section('title')
    {{ $title }}
@endsection
@section('styles')
    <style>
        .sale-item-table-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        #return_item_table {
            min-width: 950px;
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

        .qty-exceed-error {
            color: red;
            font-size: 11px;
            display: none;
        }

        @media (max-width: 767px) {
            .form-row>[class*="col-"] {
                margin-bottom: 10px;
            }

            .card-body {
                padding: 10px;
            }

            #return_item_table {
                min-width: 850px;
            }
        }
    </style>
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Sale Return</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('sale.return.index'))
                            <li class="breadcrumb-item"><a href="{{ route('sale.sale.return.index') }}">Sale Return</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Create</span></li>
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
                    <h3 class="card-title">New Sale Return</h3>
                </div>
                <div class="card-body">
                    <form class="needs-validation" method="POST" action="{{ route('sale.return.store') }}" novalidate
                        id="saleReturnForm">
                        @csrf


                        <div class="form-row">
                            <div class="col-md-2 col-sm-6 col-12 mb-3">
                                <label>Return No :</label>
                                <input class="bg-green form-control" readonly style="padding: 5px; font-weight: bold;"
                                    value="{{ $return_no }}">
                                <input type="hidden" name="return_no" value="{{ $return_no }}">
                            </div>

                            <div class="col-md-3 col-sm-6 col-12 mb-3" style="position: relative;">
                                <label for="original_sale_search">Original Invoice * :</label>
                                <input type="text" class="form-control" id="original_sale_search"
                                    placeholder="--Search Invoice No / Customer--" autocomplete="off">
                                <input type="hidden" id="original_sale_id" name="original_sale_id" required>
                                <div id="invoice_search_results" class="list-group"
                                    style="display:none; position:absolute; z-index:1050; width:100%; max-height:220px; overflow-y:auto; box-shadow:0 2px 6px rgba(0,0,0,0.15);">
                                </div>
                                @error('original_sale_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-2 col-sm-6 col-12 mb-3">
                                <label>Return Date * :</label>
                                <div class="input-group date" id="returnDatePicker" data-target-input="nearest">
                                    <input type="text" name="return_date" data-toggle="datetimepicker"
                                        value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
                                        data-target="#returnDatePicker">
                                    <div class="input-group-append" data-target="#returnDatePicker"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                @error('return_date')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>


                        </div>

                        <div class="form-row">

                            <div class="col-md-3 col-sm-6 col-12 mb-3">
                                <label>Sales Representative :</label>
                                <input type="text" id="sales_person_display" class="form-control bg-light" readonly>
                                <input type="hidden" name="sales_person_id" id="sales_person_id">
                            </div>

                            <div class="col-md-3 col-sm-6 col-12 mb-3">
                                <label>Branch (auto) :</label>
                                <input type="text" id="branch_name_display" class="form-control bg-light" readonly>
                                <input type="hidden" name="branch_id" id="branch_id">
                                @error('branch_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-3">
                                <label>Warehouse (auto) :</label>
                                <input type="text" id="warehouse_name_display" class="form-control bg-light" readonly>
                                <input type="hidden" name="warehouse_id" id="warehouse_id">
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-3">
                                <label>Customer Ledger(auto) :</label>
                                <input type="text" id="ledger_name_display" class="form-control bg-light" readonly>
                                <input type="hidden" name="ledger_id" id="ledger_id">
                                @error('ledger_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>Invoice Items <small class="text-muted">(select an invoice above to load items)</small>
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="table-responsive sale-item-table-wrapper">
                                            <table class="table table-bordered table-hover" id="return_item_table">
                                                <thead>
                                                    <tr>
                                                        <th style="width:16%">Product</th>
                                                        <th style="width:8%" align="center">Sold Qty</th>
                                                        <th style="width:10%" align="center">Return Qty <span
                                                                style="color:red;">*</span></th>
                                                        <th style="width:8%" align="center">Remaining</th>
                                                        <th style="width:10%" align="center">Unit Price</th>
                                                        <th style="width:8%" align="center">VAT %</th>
                                                        <th style="width:12%" align="center">Condition <span
                                                                style="color:red;">*</span></th>
                                                        <th style="width:14%" align="center">Reason</th>
                                                        <th style="width:14%" align="center">Line Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="return_item_tbody">
                                                    <tr id="no_invoice_row">
                                                        <td colspan="9" class="text-center text-muted">No invoice
                                                            selected yet.</td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="8" align="right"><strong>Return Sub-Total (BDT,
                                                                incl. VAT)</strong></td>
                                                        <td align="right"><strong class="return_grandtotal"
                                                                id="return_grandtotal">0.00</strong></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 col-12 mb-3">
                                <label>Remarks:</label>
                                <textarea class="form-control" name="remarks" placeholder="Note......" rows="6"></textarea>
                            </div>
                            <div class="col-md-4 col-12 mb-3">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <tbody id="original_charges_tbody">

                                                </tbody>
                                                <tbody>
                                                    <tr>
                                                        <td nowrap align="right"><strong>Total Return Amount</strong></td>
                                                        <td align="right"><strong id="final_return_total">0.00</strong>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="clearfix"></div>
                                <div class="clearfix form-actions float-right">
                                    <div class="col-md-offset-1 col-md-10">
                                        <button class="btn btn-info float-right" id="subMitButton" type="submit">Save
                                            Return</button>
                                        &nbsp;&nbsp;&nbsp;
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function initReturnSelect2() {
            $('.select2').each(function() {
                let $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.select2({
                    width: '100%',
                    dropdownAutoWidth: false
                });
            });
        }

        function initInvoiceSearchSelect2() {
            let $searchInput = $('#original_sale_search');
            let $hiddenId = $('#original_sale_id');
            let $results = $('#invoice_search_results');
            let searchTimer = null;

            $searchInput.on('keyup', function() {
                let term = $(this).val().trim();
                clearTimeout(searchTimer);

                if (term.length < 1) {
                    $results.hide().empty();
                    return;
                }

                searchTimer = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('sale.return.searchInvoices') }}",
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term
                        },
                        success: function(data) {
                            $results.empty();
                            if (!data.results || data.results.length === 0) {
                                $results.append(
                                    '<div class="list-group-item text-muted">No invoice found.</div>'
                                );
                            } else {
                                $.each(data.results, function(i, item) {
                                    $results.append(
                                        '<a href="javascript:void(0)" class="list-group-item list-group-item-action invoice-result-item" data-id="' +
                                        item.id + '" data-text="' + item.text +
                                        '">' + item.text + '</a>'
                                    );
                                });
                            }
                            $results.show();
                        },
                        error: function(xhr) {
                            console.error('Invoice Search Error:', xhr.status, xhr
                                .responseText);
                            $results.empty().append(
                                '<div class="list-group-item text-danger">Search failed.</div>'
                            ).show();
                        }
                    });
                }, 300);
            });

            $results.on('click', '.invoice-result-item', function() {
                let id = $(this).data('id');
                let text = $(this).data('text');
                $hiddenId.val(id);
                $searchInput.val(text);
                $results.hide().empty();
                loadInvoiceDetails(id);
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#original_sale_search, #invoice_search_results').length) {
                    $results.hide();
                }
            });
        }

        $(document).ready(function() {
            initReturnSelect2();
            initInvoiceSearchSelect2();
        });


        function loadInvoiceDetails(saleId) {
            if (!saleId) return;

            $.ajax({
                url: "{{ route('sale.return.getInvoiceDetails') }}",
                type: "GET",
                dataType: "json",
                cache: false,
                data: {
                    _token: "{{ csrf_token() }}",
                    sale_id: saleId
                },
                success: function(data) {
                    $('#customer_id').val(data.customer_id);
                    $('#customer_name_display').val(data.customer_name);
                    $('#sales_person_id').val(data.sales_person_id);
                    $('#sales_person_display').val(data.sales_person_name || '—');
                    $('#branch_id').val(data.branch_id);
                    $('#branch_name_display').val(data.branch_name);
                    $('#warehouse_id').val(data.warehouse_id);
                    $('#warehouse_name_display').val(data.warehouse_name || '—');
                    $('#ledger_id').val(data.ledger_id);
                    $('#ledger_name_display').val(data.ledger_name);

                    renderOriginalCharges(data);

                    let $tbody = $('#return_item_tbody');
                    $tbody.empty();

                    if (!data.items || data.items.length === 0) {
                        $tbody.append(
                            '<tr><td colspan="8" class="text-center text-muted">No returnable items on this invoice.</td></tr>'
                        );
                        calculateReturnTotal();
                        return;
                    }

                    $.each(data.items, function(index, item) {
                        let originalQty = parseFloat(item.original_qty) || 0;
                        let alreadyReturnedQty = parseFloat(item.already_returned_qty) || 0;
                        let unitPrice = parseFloat(item.unit_price) || 0;
                        let vatPercent = parseFloat(item.vat_percent) || 0;
                        let remaining = (originalQty - alreadyReturnedQty).toFixed(2);
                        let lineTotal = (remaining * unitPrice * (1 + vatPercent / 100));

                        let row = '<tr class="return-item-row" data-sale-detail-id="' + item
                            .sale_detail_id +
                            '" data-unit-price="' + unitPrice + '" data-vat-percent="' + vatPercent +
                            '" data-remaining="' + remaining + '">' +

                            '<td>' + item.product_name +
                            '<input type="hidden" name="items[' + index + '][sale_detail_id]" value="' +
                            item.sale_detail_id + '">' +
                            '<input type="hidden" name="items[' + index + '][product_id]" value="' +
                            item.product_id + '">' +
                            '<input type="hidden" name="items[' + index + '][original_qty]" value="' +
                            originalQty + '">' +
                            '<input type="hidden" name="items[' + index +
                            '][already_returned_qty]" value="' + alreadyReturnedQty + '">' +
                            '<input type="hidden" name="items[' + index + '][unit_price]" value="' +
                            unitPrice + '">' +
                            '<input type="hidden" name="items[' + index + '][vat_percent]" value="' +
                            vatPercent + '">' +
                            '</td>' +

                            '<td align="center">' + originalQty + '</td>' +

                            '<td>' +
                            '<input type="text" class="form-control return_qty" name="items[' + index +
                            '][returned_qty]" ' +
                            'value="' + remaining + '" onkeyup="onReturnQtyChange(this)">' +
                            '<span class="qty-exceed-error">Exceeds remaining qty</span>' +
                            '</td>' +

                            '<td align="center" class="remaining_qty_cell">' + remaining + '</td>' +

                            '<td align="right">' + unitPrice.toFixed(2) + '</td>' +

                            '<td align="center">' + vatPercent.toFixed(2) + '%</td>' +

                            '<td>' +
                            '<select class="form-control line_condition" name="items[' + index +
                            '][condition]">' +
                            '<option value="good">Good</option>' +
                            '<option value="damaged">Damaged</option>' +
                            '</select>' +
                            '</td>' +

                            '<td>' +
                            '<input type="text" class="form-control" name="items[' + index +
                            '][reason]" placeholder="Reason">' +
                            '</td>' +

                            '<td align="right"><strong class="line_total">' + lineTotal.toFixed(2) +
                            '</strong></td>' +
                            '</tr>';

                        $tbody.append(row);
                    });

                    calculateReturnTotal();
                },
                error: function(xhr) {
                    console.error('Invoice Details Error:', xhr.status, xhr.responseText);
                    alertMessage.error('Failed to load invoice details.');
                }
            });
        }

        function renderOriginalCharges(data) {
            let $box = $('#original_charges_tbody');
            $box.empty();

            let rows = '';

            if (parseFloat(data.discount) > 0) {
                rows += '<tr><td nowrap align="right">Discount (original sale) :</td>' +
                    '<td align="right">' + parseFloat(data.discount).toFixed(2) + '</td></tr>';
            }
            if (parseFloat(data.carrying_cost) > 0) {
                rows += '<tr><td nowrap align="right">Carrying Cost (original sale) :</td>' +
                    '<td align="right">' + parseFloat(data.carrying_cost).toFixed(2) + '</td></tr>';
            }
            if (parseFloat(data.labor_bill) > 0) {
                rows += '<tr><td nowrap align="right">Labor Bill (original sale) :</td>' +
                    '<td align="right">' + parseFloat(data.labor_bill).toFixed(2) + '</td></tr>';
            }

            if (rows) {
                rows += '<tr><td colspan="2"><small class="text-muted">' +
                    'Note: These charges are shown as information, not added as Return Amount' +
                    '</small></td></tr>';
            }

            $box.html(rows);
        }

        function onReturnQtyChange(el) {
            let $row = $(el).closest('tr');
            let remaining = parseFloat($row.data('remaining')) || 0;
            let unitPrice = parseFloat($row.data('unit-price')) || 0;
            let vatPercent = parseFloat($row.data('vat-percent')) || 0;
            let qty = parseFloat($(el).val()) || 0;
            let $error = $row.find('.qty-exceed-error');

            if (qty > remaining) {
                $error.show();
                $(el).val(remaining);
                qty = remaining;
            } else {
                $error.hide();
            }

            let lineTotal = (qty * unitPrice * (1 + vatPercent / 100)).toFixed(2);
            $row.find('.line_total').text(lineTotal);
            calculateReturnTotal();
        }



        function calculateReturnTotal() {
            let grandTotal = 0;
            $('.line_total').each(function() {
                grandTotal += parseFloat($(this).text()) || 0;
            });
            $('#return_grandtotal').text(grandTotal.toFixed(2));
            $('#final_return_total').text(grandTotal.toFixed(2));
        }

        $('#saleReturnForm').on('submit', function(e) {
            if (!$('#original_sale_id').val()) {
                e.preventDefault();
                alertMessage.error('Please select an original invoice first.');
                return false;
            }

            let hasQty = false;
            $('.return_qty').each(function() {
                if (parseFloat($(this).val()) > 0) {
                    hasQty = true;
                }
            });

            if (!hasQty) {
                e.preventDefault();
                alertMessage.error('Please enter a return quantity for at least one item.');
                return false;
            }
        });

        function toggleRefundAccount(method) {
            if (method === 'cash_bank') {
                $('#refund_account_row').removeClass('d-none');
                $('#refund_account_id').prop('required', true);
            } else {
                $('#refund_account_row').addClass('d-none');
                $('#refund_account_id').prop('required', false).val('');
            }
        }

        $('#saleReturnForm').on('submit', function(e) {
            if (!$('#original_sale_id').val()) {
                e.preventDefault();
                alertMessage.error('Please select an original invoice first.');
                return false;
            }

            let hasQty = false;
            $('.return_qty').each(function() {
                if (!$(this).prop('disabled') && parseFloat($(this).val()) > 0) {
                    hasQty = true;
                }
            });

            if (!hasQty) {
                e.preventDefault();
                alertMessage.error('Please keep at least one item included with a return quantity.');
                return false;
            }
        });

        function onItemIncludeToggle(checkbox) {
            let $row = $(checkbox).closest('tr');
            let $qtyInput = $row.find('.return_qty');
            let $conditionSelect = $row.find('.line_condition');
            let $reasonInput = $row.find('input[name*="[reason]"]');
            let remaining = parseFloat($row.data('remaining')) || 0;

            if ($(checkbox).is(':checked')) {

                $qtyInput.prop('disabled', false).val(remaining);
                $conditionSelect.prop('disabled', false);
                $reasonInput.prop('disabled', false);
                $row.removeClass('text-muted').css('opacity', '1');
            } else {

                $qtyInput.val(0).prop('disabled', true);
                $conditionSelect.prop('disabled', true);
                $reasonInput.prop('disabled', true);
                $row.addClass('text-muted').css('opacity', '0.5');
            }

            $row.find('.line_total').text((parseFloat($qtyInput.val()) * parseFloat($row.data('unit-price'))).toFixed(2));

            calculateReturnTotal();
            updateReturnType();
        }

        function updateReturnType() {
            let anyIncluded = false;
            let anyExcluded = false;
            let allFullQty = true;

            $('.return-item-row').each(function() {
                let $row = $(this);
                let included = $row.find('.item_include').is(':checked');

                if (!included) {
                    anyExcluded = true;
                    return;
                }

                anyIncluded = true;
                let remaining = parseFloat($row.data('remaining')) || 0;
                let qty = parseFloat($row.find('.return_qty').val()) || 0;

                if (qty < remaining) {
                    allFullQty = false;
                }
            });

            let type = (anyIncluded && allFullQty && !anyExcluded) ? 'Full' : 'Partial';

            if (!anyIncluded) {
                type = '-';
            }

            $('#return_type').val(type === '-' ? '' : type);
            $('#return_type_display').val(type);
        }
    </script>
@endsection
