@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <style>
        #salesSummaryTable thead th,
        #salesSummaryTable tfoot th {
            white-space: nowrap;
            background: #f4f6f9;
            vertical-align: middle;
        }

        #salesSummaryTable tbody td {
            vertical-align: middle;
        }

        #salesSummaryTable tbody tr.sale-row {
            cursor: pointer;
        }

        #salesSummaryTable tbody tr.sale-row:hover {
            background-color: #f1f7ff;
        }

        .filter-bar label {
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 2px;
        }

        .row-hint {
            font-size: 12px;
            color: #6c757d;
        }

        .print-only {
            display: none;
        }

        /* Custom separator inside cells for multiple products */
        .cell-hr {
            margin: 6px 0;
            border-top: 1px solid #dee2e6;
        }

        @media (max-width: 767px) {
            .card-header .card-tools-wrap {
                width: 100%;
                margin-top: 8px;
            }

            .card-header .card-tools-wrap .btn {
                flex: 1;
            }

            #salesSummaryTable_wrapper .dataTables_length,
            #salesSummaryTable_wrapper .dataTables_filter,
            #salesSummaryTable_wrapper .dataTables_info,
            #salesSummaryTable_wrapper .dataTables_paginate {
                text-align: left !important;
                margin-bottom: 6px;
            }

            #salesSummaryTable_filter label,
            #salesSummaryTable_filter input {
                width: 100%;
                margin-left: 0 !important;
            }
        }

        /* Print-specific styles for A4 Layout and Text Wrapping */
        @page {
            size: A4 landscape;
            margin: 10mm 10mm 10mm 5mm;
            /* Top Right Bottom Left margin */
        }

        @media print {

            /* Hide non-printable elements */
            .no-print,
            .filter-bar,
            #summaryCards,
            .dataTables_length,
            .dataTables_filter,
            .dataTables_info,
            .breadcrumb-item .active,
            #pagetitle,
            .dataTables_paginate {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            /* Remove scrollbars and fix width for printing */
            .table-responsive {
                overflow-x: visible !important;
                -webkit-overflow-scrolling: auto !important;
            }

            /* Table Layout & Text Wrapping */
            table {
                width: 100% !important;
                table-layout: auto !important;
                border-collapse: collapse !important;
            }

            .table th,
            .table td {
                padding: 5px !important;
                font-size: 9pt !important;
                white-space: normal !important;
                /* Text wrap korar jonno */
                word-wrap: break-word !important;
                /* Boro text bhenge nicher line e anbe */
                overflow-wrap: break-word !important;
            }

            /* Page Break Logic jeno kono row majhkhan theke kete na jay */
            tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }

            /* Notun page e gele jeno table header abar dekhay */
            thead {
                display: table-header-group !important;
            }

            tfoot {
                display: table-footer-group !important;
            }

            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><span>Sales Reports</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    @php
        $isAdmin = auth()->user()->type == 'Admin';
        $rows = collect($salesDetails ?? []);
        $selectedType = $purchasetype ?? 'all';

        // PHP static calculation ignoring skipped types
        $totalQty = 0;
        $totalAmount = 0;
        $actualSalesCount = 0;

        foreach ($rows as $item) {
            $validDetails = $item->details;

            // Jodi filter e nirdishto kono type thake, tahole onno gulo skip korbe
            if ($selectedType !== 'all') {
                $validDetails = $validDetails->where('purchasetype', $selectedType);
            }

            if ($validDetails->isNotEmpty()) {
                $totalQty += $validDetails->sum('qty');
                $totalAmount += $validDetails->sum('price');
                $actualSalesCount++;
            }
        }
        $totalSales = $actualSalesCount;

        // DB theke sob employee/user der niye asha hocche Sales Rep filter er jonno
        $allEmployees = class_exists('\App\Models\User') ? \App\Models\User::all() : collect();
    @endphp

    <div class="row no-print" id="summaryCards">
        <div class="col-12 col-sm-6 {{ $isAdmin ? 'col-xl-4' : 'col-xl-6' }}">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-file-invoice"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Records</span>
                    <span class="info-box-number" id="cardItems">{{ number_format($totalSales) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 {{ $isAdmin ? 'col-xl-4' : 'col-xl-6' }}">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Quantity</span>
                    <span class="info-box-number" id="cardQty">{{ number_format($totalQty, 2) }}</span>
                </div>
            </div>
        </div>
        @if ($isAdmin)
            <div class="col-12 col-sm-12 col-xl-4">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-coins"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Sales Amount</span>
                        <span class="info-box-number" id="cardValue">{{ number_format($totalAmount, 0) }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between" id="pagetitle">
                    <h3 class="card-title mb-0">Sales Reports</h3>
                    <div class="card-tools-wrap d-flex no-print" style="gap:8px">
                        <button type="button" onclick="exportSalesToExcel()" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button type="button" onclick="printSalesSummary()" class="btn btn-default btn-sm">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="print-only text-center mb-3">
                        <h4 class="mb-0">{{ $companyInfo->company_name ?? '' }}</h4>
                        <h5 class="mb-0">Sales Reports</h5>
                        <small>Printed: {{ date('d M Y, h:i A') }}</small>
                    </div>

                    <!-- Filter Area -->
                    <div class="no-print filter-bar mb-4">
                        <form id="salesFilterForm" action="{{ route('report.sale.sale') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-6 col-md-4 col-xl-2 mb-3">
                                    <label for="reservation">Date Range</label>
                                    <input type="text" class="form-control form-control-sm" name="dateRange"
                                        value="{{ $request->dateRange ?? '' }}" id="reservation" />
                                </div>
                                <div class="col-6 col-md-4 col-xl-2 mb-3">
                                    <label for="branch_id">Branch</label>
                                    <select class="form-control form-control-sm select2" name="branch_id" id="branch_id"
                                        style="width:100%">
                                        <option value="all">All Branches</option>
                                        @foreach ($branch as $b)
                                            <option {{ isset($branch_id) && $branch_id == $b->id ? 'selected' : '' }}
                                                value="{{ $b->id }}">
                                                {{ $b->branchCode . ' - ' . $b->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-md-4 col-xl-2 mb-3">
                                    <label for="warehouse_id">Warehouse</label>
                                    <select class="form-control form-control-sm select2" name="warehouse_id"
                                        id="warehouse_id" style="width:100%">
                                        <option value="all">All Warehouses</option>
                                        @foreach ($warehouses as $w)
                                            <option {{ isset($warehouse_id) && $warehouse_id == $w->id ? 'selected' : '' }}
                                                value="{{ $w->id }}" data-branch="{{ $w->branch_id }}">
                                                {{ $w->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-md-4 col-xl-2 mb-3">
                                    <label for="purchasetype">Sale Type</label>
                                    <select class="form-control form-control-sm select2" name="purchasetype"
                                        id="purchasetype" style="width:100%">
                                        <option value="all"
                                            {{ isset($purchasetype) && $purchasetype == 'all' ? 'selected' : '' }}>All
                                        </option>
                                        <option value="local"
                                            {{ isset($purchasetype) && $purchasetype == 'local' ? 'selected' : '' }}>Local
                                        </option>
                                        <option value="imported"
                                            {{ isset($purchasetype) && $purchasetype == 'imported' ? 'selected' : '' }}>
                                            Imported</option>
                                    </select>
                                </div>

                                <div class="col-6 col-md-4 col-xl-2 mb-3">
                                    <label for="filterSalesRep">Sales Representative</label>
                                    <select id="filterSalesRep" class="form-control form-control-sm select2">
                                        <option value="">All</option>
                                        @foreach ($allEmployees as $emp)
                                            <option value="{{ $emp->name }}">{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-6 col-md-4 col-xl-2 mb-3">
                                    <label for="filterCustomer">Customer </label>
                                    <select id="filterCustomer" class="form-control form-control-sm select2">
                                        <option value="">All</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-4 col-xl-2 mb-3">
                                    <label for="filterProduct">Product </label>
                                    <select id="filterProduct" class="form-control form-control-sm select2">
                                        <option value="">All</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-8 col-xl-10 mb-3 d-flex justify-content-end align-items-end"
                                    style="gap: 10px;">
                                    <button type="submit" class="btn btn-sm btn-success px-4">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                    <button type="button" id="btnResetFilters"
                                        class="btn btn-outline-secondary btn-sm px-4">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <p class="row-hint mb-2 no-print">
                        <i class="fas fa-info-circle"></i>
                        Click / tap any row to see its sold items. Horizontal scroll to see all columns.
                    </p>

                    <!-- ADDED table-responsive wrapper to handle horizontal scroll without icons -->
                    <div class="table-responsive">
                        <table id="salesSummaryTable" class="table table-striped table-bordered table-hover"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Date</th>
                                    <th>Invoice</th>
                                    <th>Branch</th>
                                    <th>Warehouse</th>
                                    <th>Sales Rep</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th class="col-qty text-right">Qty</th>
                                    <th class="text-right">Rate</th>
                                    <th class="text-right">Total Price</th>
                                    @if ($isAdmin)
                                        <th class="col-grand-total text-right">Grand Total</th>
                                    @endif
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sl = 1; @endphp
                                @foreach ($rows as $item)
                                    @php
                                        $validDetails = $item->details;

                                        if ($selectedType !== 'all') {
                                            $validDetails = $validDetails->where('purchasetype', $selectedType);
                                        }

                                        if ($validDetails->isEmpty()) {
                                            continue;
                                        }

                                        $groupedDetails = $validDetails->groupBy('purchasetype');
                                        $salesRepName =
                                            optional($item->user)->name ?? (optional($item->employee)->name ?? 'N/A');
                                    @endphp

                                    @foreach ($groupedDetails as $type => $detailsGroup)
                                        @php
                                            $separator = '<hr class="cell-hr">';

                                            $productNames = $detailsGroup
                                                ->map(fn($d) => optional($d->product)->getRawOriginal('name') ?? '-')
                                                ->implode($separator);
                                            $qtys = $detailsGroup->map(fn($d) => $d->qty)->implode($separator);
                                            $rates = $detailsGroup->map(fn($d) => $d->rate)->implode($separator);
                                            $prices = $detailsGroup->map(fn($d) => $d->price)->implode($separator);

                                            $groupQty = $detailsGroup->sum('qty');
                                            $groupTotal = $detailsGroup->sum('price');

                                            $purchaseTypeLabel = ucfirst($type ?: '-');

                                            $detailsJson = $detailsGroup->map(function ($d) {
                                                return [
                                                    'product' => optional($d->product)->getRawOriginal('name') ?? '',
                                                    'qty' => $d->qty,
                                                    'rate' => $d->rate,
                                                    'price' => $d->price,
                                                    'purchasetype' => $d->purchasetype,
                                                ];
                                            });
                                        @endphp

                                        <tr class="sale-row" tabindex="0" data-invoice="{{ $item->invoice_no }}"
                                            data-date="{{ $item->date }}"
                                            data-customer="{{ optional($item->customer)->account_name }}"
                                            data-details="{{ $detailsJson }}">
                                            <td>{{ $sl++ }}</td>
                                            <td>{{ $item->date }}</td>
                                            <td>{{ $item->invoice_no }}</td>
                                            <td>{{ optional($item->branch)->branchCode . ' - ' . optional($item->branch)->name }}
                                            </td>
                                            <td>{{ optional($item->warehouse)->name ?? '-' }}</td>
                                            <td>{{ $salesRepName }}</td>
                                            <td>{{ optional($item->customer)->account_name ?? '' }}</td>
                                            <td>{!! $productNames !!}</td>
                                            <td class="col-qty text-right font-weight-bold"
                                                data-order="{{ $groupQty }}">{!! $qtys !!}</td>
                                            <td class="text-right">{!! $rates !!}</td>
                                            <td class="text-right">{!! $prices !!}</td>
                                            @if ($isAdmin)
                                                <td class="col-grand-total text-right" data-order="{{ $groupTotal }}">
                                                    {{ number_format($groupTotal, 2) }}</td>
                                            @endif
                                            <td>{{ $purchaseTypeLabel }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="text-right">Total</th>
                                    <th class="col-qty text-right">{{ $totalQty }}</th>
                                    <th></th>
                                    <th></th>
                                    @if ($isAdmin)
                                        <th class="col-grand-total text-right">{{ number_format($totalAmount, 0) }}</th>
                                    @endif
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="saleDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saleModalTitle">Sale Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <p class="mb-2" id="saleModalMeta"></p>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th class="text-right">Qty</th>
                                    <th class="text-right">Rate</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody id="saleModalBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        const IS_ADMIN = {{ $isAdmin ? 'true' : 'false' }};
        const COL = {
            salesRep: 5,
            customer: 6,
            product: 7
        };

        let originalWarehouses;

        function fmtNumber(n, digits) {
            return Number(n || 0).toLocaleString('en-US', {
                maximumFractionDigits: digits
            });
        }

        function cellText(html) {
            let cleanHtml = html.replace(/<hr[^>]*>|<br[^>]*>/gi, ' ');
            return $('<div>').html(cleanHtml).text().trim();
        }

        function fillColumnFilter(table, colIdx, selectSel) {
            const values = new Set();
            table.column(colIdx).data().each(function(d) {
                const htmlArray = d.split(/<hr[^>]*>|<br[^>]*>/gi);
                htmlArray.forEach(function(itemHtml) {
                    const t = $('<div>').html(itemHtml).text().trim();
                    if (t !== '') values.add(t);
                });
            });
            const $sel = $(selectSel);
            Array.from(values).sort((a, b) => a.localeCompare(b)).forEach(function(v) {
                $sel.append($('<option>').val(v).text(v));
            });
            $sel.on('change', function() {
                table.column(colIdx).search($(this).val() || '', false, true).draw();
            });
        }

        function openSaleDetailsFromRow($tr) {
            const invoice = $tr.data('invoice');
            const date = $tr.data('date');
            const customer = $tr.data('customer');
            const details = $tr.data('details') || [];

            $('#saleModalTitle').text('Sale Details — Invoice ' + invoice);
            $('#saleModalMeta').text('Date: ' + date + (customer ? ' | Customer: ' + customer : ''));

            let rows = '';
            details.forEach(function(d) {
                rows += `<tr>
                    <td>${d.product || ''}</td>
                    <td>${d.purchasetype ? d.purchasetype.charAt(0).toUpperCase() + d.purchasetype.slice(1) : '-'}</td>
                    <td class="text-right">${fmtNumber(d.qty, 2)}</td>
                    <td class="text-right">${fmtNumber(d.rate, 2)}</td>
                    <td class="text-right">${fmtNumber(d.price, 2)}</td>
                </tr>`;
            });
            $('#saleModalBody').html(rows || '<tr><td colspan="5" class="text-center">No items</td></tr>');
            $('#saleDetailsModal').modal('show');
        }

        function toggleWarehouseByBranch() {
            let branchId = $("#branch_id").val();
            let selectedWarehouse = $("#warehouse_id").val();

            $("#warehouse_id").empty();

            originalWarehouses.each(function() {
                let optBranch = $(this).data('branch');
                if ($(this).val() === "all" || branchId === "all" || String(optBranch) === String(branchId)) {
                    $("#warehouse_id").append($(this).clone());
                }
            });

            if ($("#warehouse_id option[value='" + selectedWarehouse + "']").length > 0) {
                $("#warehouse_id").val(selectedWarehouse);
            } else {
                $("#warehouse_id").val("all");
            }

            if ($('#warehouse_id').hasClass("select2-hidden-accessible")) {
                $('#warehouse_id').trigger('change.select2');
            }
        }

        $(document).ready(function() {
            originalWarehouses = $("#warehouse_id option").clone();
            toggleWarehouseByBranch();

            $("#branch_id").on("change", function() {
                toggleWarehouseByBranch();
            });

            $('#filterSalesRep').on('change', function() {
                const table = $('#salesSummaryTable').DataTable();
                table.column(COL.salesRep).search($(this).val() || '', false, true).draw();
            });

            const table = $('#salesSummaryTable').DataTable({

                paging: true,
                pageLength: 50,
                lengthMenu: [
                    [25, 50, 100, -1],
                    [25, 50, 100, 'All']
                ],
                info: true,
                ordering: true,
                order: [],
                language: {
                    search: "Search:",
                    searchPlaceholder: "Invoice / customer / product"
                },
                footerCallback: function() {
                    const api = this.api();

                    const sumColByClass = function(className) {
                        const idx = api.column(className).index();
                        if (idx === undefined) return 0;
                        let s = 0;
                        api.column(idx, {
                            search: 'applied'
                        }).nodes().each(function(td) {
                            s += parseFloat($(td).attr('data-order')) || 0;
                        });
                        return s;
                    };

                    const items = api.rows({
                        search: 'applied'
                    }).count();

                    const qty = sumColByClass('.col-qty');
                    const qtyIdx = api.column('.col-qty').index();
                    $(api.column(qtyIdx).footer()).text(fmtNumber(qty, 2));
                    $('#cardItems').text(fmtNumber(items, 0));
                    $('#cardQty').text(fmtNumber(qty, 2));

                    if (IS_ADMIN) {
                        const val = sumColByClass('.col-grand-total');
                        const totalIdx = api.column('.col-grand-total').index();
                        $(api.column(totalIdx).footer()).text(fmtNumber(val, 0));
                        $('#cardValue').text(fmtNumber(val, 0));
                    }
                },
                initComplete: function() {
                    fillColumnFilter(this.api(), COL.customer, '#filterCustomer');
                    fillColumnFilter(this.api(), COL.product, '#filterProduct');
                }
            });

            $('#btnResetFilters').on('click', function() {
                $('#filterSalesRep, #filterCustomer, #filterProduct').val('').trigger('change.select2');
                table.search('').columns().search('').draw();

                $('#reservation').val('');
                $('#branch_id').val('all').trigger('change.select2');
                $('#warehouse_id').val('all').trigger('change.select2');
                $('#purchasetype').val('all').trigger('change.select2');
            });

            $('#salesSummaryTable tbody').on('click', 'tr.sale-row', function(e) {
                // Remove the check for dtr-control since we removed responsive feature
                openSaleDetailsFromRow($(this));
            });
        });

        function printSalesSummary() {
            const table = $('#salesSummaryTable').DataTable();
            const prevLen = table.page.len();
            const restore = function() {
                window.removeEventListener('afterprint', restore);
                table.page.len(prevLen).draw(false);
            };
            window.addEventListener('afterprint', restore);
            table.page.len(-1).draw(false);
            setTimeout(() => window.print(), 150);
        }

        function exportSalesToExcel() {
            const table = $('#salesSummaryTable').DataTable();
            const heads = [];
            const numeric = [];

            $('#salesSummaryTable thead th').each(function() {
                heads.push($(this).text().trim());
                numeric.push($(this).hasClass('text-right'));
            });

            const data = [heads];
            table.rows({
                search: 'applied',
                order: 'applied'
            }).every(function() {
                const row = [];
                $(this.node()).children('td').each(function(i) {
                    let html = $(this).html();
                    let text = $('<div>').html(html.replace(/<hr[^>]*>|<br[^>]*>/gi, '\n')).text().trim();

                    if (numeric[i] && !text.includes('\n')) {
                        let n = parseFloat(text.replace(/,/g, ''));
                        row.push(isNaN(n) ? text : n);
                    } else {
                        row.push(text);
                    }
                });
                data.push(row);
            });

            const footRow = [];
            $('#salesSummaryTable tfoot th').each(function(i) {
                let text = $(this).text().trim();
                if (numeric[i]) {
                    let n = parseFloat(text.replace(/,/g, ''));
                    footRow.push(isNaN(n) ? text : n);
                } else {
                    footRow.push(text);
                }
            });
            data.push(footRow);

            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();
            // XLSX.utils.book_append_sheet(wb, ws, 'Sales Reports');
            XLSX.writeFile(wb, `sales-summary-${new Date().toISOString().slice(0, 10)}.xlsx`);
        }
    </script>
@endsection
