@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }

        /* Updated: 2026-09-21 - table look & feel */
        #stockSummaryTable thead th {
            white-space: nowrap;
            background: #f4f6f9;
            vertical-align: middle;
        }

        #stockSummaryTable tbody td {
            vertical-align: middle;
        }

        #stockSummaryTable tfoot th {
            background: #f4f6f9;
            white-space: nowrap;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before {
            background-color: #007bff;
        }

        /* Added: 2026-07-20 - clickable row for ledger modal */
        #stockSummaryTable tbody tr.ledger-row {
            cursor: pointer;
        }

        #stockSummaryTable tbody tr.ledger-row:hover {
            background-color: #f1f7ff;
        }

        #stockSummaryTable tbody tr.ledger-row:focus {
            outline: 2px solid #007bff;
            outline-offset: -2px;
        }

        /* >>> NEW (2026-09-21) - filter bar, hint, type badge */
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

        .type-tag {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 999px;
            background: #eef2f7;
            border: 1px solid #dde3ea;
            white-space: nowrap;
        }

        .print-only {
            display: none;
        }

        /* <<< END NEW */

        /* Added: 2026-07-20 - ledger badges/summary styles needed since partial loads inside modal too */
        #ledgerTable thead th {
            background: #1a56db;
            color: #fff;
            white-space: nowrap;
        }

        .type-badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .badge-opening {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-purchase {
            background: #dcfce7;
            color: #166534;
        }

        .badge-sale {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-transfer-in {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-transfer-out {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-adjustment {
            background: #ede9fe;
            color: #5b21b6;
        }

        .in-col {
            color: #166534;
            font-weight: 600;
        }

        .out-col {
            color: #991b1b;
            font-weight: 600;
        }

        .rem-col {
            color: #1e40af;
            font-weight: 700;
        }

        .summary-bar {
            background: #1e293b;
            color: #fff;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 16px;
        }

        .summary-bar .s-item {
            text-align: center;
        }

        .summary-bar .s-val {
            font-size: 20px;
            font-weight: 700;
        }

        .summary-bar .s-lbl {
            font-size: 11px;
            color: #94a3b8;
        }

        /* Added: 2026-08-13 - modal tab buttons (Product Ledger / Stock Diagnosis) */
        #productLedgerModal .modal-header {
            flex-wrap: wrap;
            gap: 8px;
        }

        #productLedgerModal .modal-header-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
        }

        #productLedgerModal .tab-btn {
            font-size: 12px;
            font-weight: 600;
            border-radius: 999px;
            padding: 5px 14px;
        }

        #productLedgerModal .tab-btn.active {
            box-shadow: inset 0 0 0 1px rgba(0, 0, 0, .05);
        }

        /* Updated: 2026-09-21 - modal content must never overflow the screen */
        #productLedgerModal .modal-body {
            overflow-x: auto;
        }

        /* Mobile (<= 767px) */
        @media (max-width: 767px) {
            .card-header .card-tools-wrap {
                width: 100%;
                margin-top: 8px;
            }

            .card-header .card-tools-wrap .btn {
                flex: 1;
            }

            #stockSummaryTable_wrapper .row {
                margin: 0;
            }

            #stockSummaryTable_wrapper .dataTables_length,
            #stockSummaryTable_wrapper .dataTables_filter,
            #stockSummaryTable_wrapper .dataTables_info,
            #stockSummaryTable_wrapper .dataTables_paginate {
                text-align: left !important;
                margin-bottom: 6px;
            }

            #stockSummaryTable_filter label,
            #stockSummaryTable_filter input {
                width: 100%;
                margin-left: 0 !important;
            }

            #productLedgerModal .modal-dialog {
                margin: .5rem;
            }

            #productLedgerModal .modal-body {
                padding: .75rem;
            }
        }

        @media (max-width: 575px) {
            #productLedgerModal .modal-title {
                width: 100%;
            }

            #productLedgerModal .modal-header-actions {
                width: 100%;
                margin-left: 0;
                justify-content: flex-start;
            }

            #productLedgerModal .tab-btn {
                flex: 1;
                text-align: center;
            }
        }

        /* >>> NEW (2026-09-21) - print: hide controls, show only the report */
        @media print {

            .no-print,
            .filter-bar,
            #summaryCards,
            .dataTables_length,
            .dataTables_filter,
            .dataTables_info,
            .dataTables_paginate {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            #stockSummaryTable tbody tr:hover {
                background: transparent;
            }
        }

        /* <<< END NEW */
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Stock Summary</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><span>Stock Summary</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    @php

        $isAdmin = auth()->user()->type == 'Admin';

        $rows = $currentSrock
            ->filter(function ($item) {
                return $item->stock_qty > 0 && $item->type != 'Project';
            })
            ->values();

        $rows = $rows->map(function ($item) use ($avgPrices) {
            // $avgPrices controller theke ashle sheta use hobe, na hole purono query fallback
            if (isset($avgPrices)) {
                $avg = $avgPrices[$item->product_id] ?? 0;
            } else {
                $purchasesPrices = App\Models\PurchasesDetails::where('product_id', $item->product_id)->pluck(
                    'unit_price',
                );
                $openingStockPrices = App\Models\ProductOpeningStockDetails::where(
                    'product_id',
                    $item->product_id,
                )->pluck('unit_price');
                $avg = $purchasesPrices->merge($openingStockPrices)->avg() ?? 0;
            }
            $item->avg_price = $avg;
            $item->line_total = round($avg * $item->stock_qty, 2);
            return $item;
        });

        $totalQty = $rows->sum('stock_qty');
        $totalPrice = $rows->sum('line_total');
        $totalItems = $rows->count();

    @endphp


    <div class="row no-print" id="summaryCards">
        <div class="col-12 col-sm-6 {{ $isAdmin ? 'col-xl-4' : 'col-xl-6' }}">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Stock Lines</span>
                    <span class="info-box-number" id="cardItems">{{ number_format($totalItems) }}</span>
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
                        <span class="info-box-text">Total Stock Value</span>
                        <span class="info-box-number" id="cardValue">{{ number_format($totalPrice, 0) }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
    {{-- <<< END NEW --}}

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Stock Summary</h3>

                    {{-- Updated: 2026-09-21 - buttons grouped, no more float overlap on mobile --}}
                    <div class="card-tools-wrap d-flex no-print" style="gap:8px">
                        <button type="button" onclick="exportStockSummaryToExcel()" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button type="button" onclick="printStockSummary()" class="btn btn-default btn-sm">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>

                <div class="card-body">

                    {{-- >>> NEW (2026-09-21) - print header --}}
                    <div class="print-only text-center mb-3">
                        <h4 class="mb-0">{{ $companyInfo->company_name ?? '' }}</h4>
                        <h5 class="mb-0">Stock Summary</h5>
                        <small>Printed: {{ date('d M Y, h:i A') }}</small>
                    </div>
                    {{-- <<< END NEW --}}

                    {{-- Updated: 2026-09-21 - filter bar: category (server) + branch/type/warehouse (instant, client-side) --}}
                    <div class="row filter-bar no-print">
                        <form class="col-12 col-md-6 col-lg-3 mb-2"
                            action="{{ route('inventorySetup.currentStock.index') }}" method="post">
                            @csrf
                            <label for="categorysubmit">Category</label>
                            <select name="category_id" id="categorysubmit" class="form-control select2" style="width:100%">
                                <option selected value="all">All Category</option>
                                @foreach ($categorys as $category)
                                    <option {{ $request->category_id == $category->id ? 'selected' : '' }}
                                        value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </form>

                        <div class="col-6 col-md-6 col-lg-3 mb-2">
                            <label for="filterBranch">Branch</label>
                            <select id="filterBranch" class="form-control form-control-sm">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2 mb-2">
                            <label for="filterType">Type</label>
                            <select id="filterType" class="form-control form-control-sm">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2 mb-2">
                            <label for="filterWarehouse">Warehouse</label>
                            <select id="filterWarehouse" class="form-control form-control-sm">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2 mb-2 d-flex align-items-end">
                            <button type="button" id="btnResetFilters" class="btn btn-outline-secondary btn-sm btn-block">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>

                    <p class="row-hint mb-2 no-print">
                        <i class="fas fa-info-circle"></i>
                        Click / tap any row to see its Product Ledger &amp; Stock Diagnosis.
                        On small screens tap the serial number to see hidden columns.
                    </p>

                    <div class="table-responsive">
                        <table id="stockSummaryTable" class="table table-striped table-bordered table-hover"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th class="all">SL</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Branch</th>
                                    <th>Type</th>
                                    <th>Warehouse</th>
                                    <th class="text-right">Qty</th>
                                    @if ($isAdmin)
                                        <th class="text-right">Avg Unit Price</th>
                                        <th class="text-right">Total</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $item)
                                    <tr class="ledger-row" tabindex="0" data-product-id="{{ $item->product_id }}"
                                        data-branch-id="{{ $item->branch_id }}"
                                        data-warehouse-id="{{ $item->warehouse_id }}"
                                        data-purchase-type="{{ $item->purchasetype ?? '' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ optional($item->products)->getRawOriginal('productCode') }}</td>
                                        <td>{{ trim(optional($item->products)->getRawOriginal('name') . ' ' . ($item->products->brand->name ?? '')) }}
                                        </td>
                                        <td>{{ optional(optional($item->products)->category)->name ?? 'N/A' }}</td>
                                        <td>{{ $item->branch->name ?? '-' }}</td>
                                        <td>{{ $item->purchasetype ?? '-' }}</td>
                                        <td>{{ $item->warehouse->name ?? '-' }}</td>
                                        <td class="text-right font-weight-bold" data-order="{{ $item->stock_qty }}">
                                            {{ $item->stock_qty }}</td>
                                        @if ($isAdmin)
                                            <td class="text-right" data-order="{{ $item->avg_price }}">
                                                {{ number_format($item->avg_price, 2) }}</td>
                                            <td class="text-right" data-order="{{ $item->line_total }}">
                                                {{ number_format($item->line_total, 2) }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>

                            {{-- Updated: 2026-09-21 - one cell per column (responsive-safe), "Avg Unit Price" sum removed (meaningless) --}}
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="text-right">{{ $totalQty }}</th>
                                    @if ($isAdmin)
                                        <th></th>
                                        <th class="text-right">{{ number_format($totalPrice, 0) }}</th>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Updated: 2026-09-21 - signature block stacks on mobile --}}
                    <div class="row mt-4">
                        <div class="col-12 col-md-6 mb-3">
                            <p class="mb-0">Prepared By:_____________<br />Date:____________________</p>
                        </div>
                        <div class="col-12 col-md-6 text-md-right mb-3">
                            <p class="mb-0">Approved By:________________<br />Date:_________________</p>
                        </div>
                    </div>

                    <div class="text-center bg-success p-2">
                        Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} Company products.
                        We believe you will be satisfied by our services.
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Added: 2026-07-20 - Product Ledger Modal --}}
    {{-- Updated: 2026-08-13 - tab buttons (Product Ledger / Stock Diagnosis) next to close, two swappable panes --}}
    <div class="modal fade" id="productLedgerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ledgerModalTitle">Product Ledger</h5>

                    <div class="modal-header-actions">
                        <button type="button" id="btnShowLedger" class="btn btn-sm btn-primary tab-btn active"
                            onclick="showModalTab('ledger')">
                            <i class="fas fa-book"></i> Product Ledger
                        </button>
                        <button type="button" id="btnShowDiagnosis" class="btn btn-sm btn-outline-warning tab-btn">
                            <i class="fas fa-stethoscope"></i> Stock Diagnosis
                        </button>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    {{-- Pane 1: Product Ledger (default visible) --}}
                    <div id="ledgerPane">
                        <div id="ledgerLoading" class="text-center py-5">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2 mb-0">Loading...</p>
                        </div>
                    </div>

                    {{-- Pane 2: Stock Diagnosis (hidden until its tab is clicked) --}}
                    <div id="diagnosisPane" style="display:none;">
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-stethoscope fa-2x mb-2"></i>
                            <p class="mb-0">Click "Stock Diagnosis" to load this product's diagnosis.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        let modalProductId = null;
        let modalBranchId = null;
        let modalPurchaseType = null;
        let diagnosisLoadedForProductId = null;

        // >>> NEW (2026-09-21)
        const IS_ADMIN = {{ $isAdmin ? 'true' : 'false' }};
        // column index map (must match <thead> order)
        const COL = {
            branch: 4,
            type: 5,
            warehouse: 6,
            qty: 7,
            total: 9
        };
        // Bug fix: to_date was 'Y-12-d' (e.g. 2026-12-21) -> now proper year end
        const LEDGER_FROM_DATE = "{{ date('2020-01-01') }}";
        const LEDGER_TO_DATE = "{{ date('Y-12-31') }}";

        function fmtNumber(n, digits) {
            return Number(n || 0).toLocaleString('en-US', {
                maximumFractionDigits: digits
            });
        }

        function cellText(html) {
            return $('<div>').html(html).text().trim();
        }

        // dropdown-e column-er unique value gulo bosay
        function fillColumnFilter(table, colIdx, selectSel) {
            const values = new Set();
            table.column(colIdx).data().each(function(d) {
                const t = cellText(d);
                if (t !== '') values.add(t);
            });
            const $sel = $(selectSel);
            Array.from(values).sort((a, b) => a.localeCompare(b)).forEach(function(v) {
                $sel.append($('<option>').val(v).text(v));
            });
            $sel.on('change', function() {
                const val = $(this).val();
                table.column(colIdx).search(
                    val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '',
                    true,
                    false
                ).draw();
            });
        }

        function openLedgerFromRow($tr) {
            modalProductId = $tr.data('product-id');
            modalBranchId = $tr.data('branch-id');
            modalWarehouseId = $tr.data('warehouse-id');
            modalPurchaseType = $tr.data('purchase-type');
            diagnosisLoadedForProductId = null; // force diagnosis reload for the newly selected product

            let url = "{{ route('inventorySetup.productledger.modal') }}" +
                "?product_id=" + modalProductId +
                "&branch_id=" + modalBranchId +
                "&warehouse_id=" + modalWarehouseId +
                "&purchase_type=" + encodeURIComponent(modalPurchaseType || '') +
                "&from_date=" + LEDGER_FROM_DATE +
                "&to_date=" + LEDGER_TO_DATE;

            showModalTab('ledger'); // always open on the Ledger tab
            loadLedgerIntoModal(url);
        }
        // <<< END NEW

        $(document).ready(function() {
            $('#categorysubmit').on('change', function() {
                $(this).closest('form').submit();
            });

            // Updated: 2026-09-21 - paging + responsive priorities + live footer/cards + better labels
            const table = $('#stockSummaryTable').DataTable({
                responsive: true,
                paging: true,
                pageLength: 50,
                lengthMenu: [
                    [25, 50, 100, -1],
                    [25, 50, 100, 'All']
                ],
                info: true,
                ordering: true,
                order: [],
                columnDefs: [{
                        responsivePriority: 1,
                        targets: 2
                    }, // Product Name - always visible
                    {
                        responsivePriority: 2,
                        targets: COL.qty
                    }, // Qty - always visible
                    {
                        responsivePriority: 3,
                        targets: 6
                    }, // Warehouse
                    {
                        responsivePriority: 4,
                        targets: 1
                    }, // Product Code
                ],
                language: {
                    search: "Search:",
                    searchPlaceholder: "Product code / name",
                    lengthMenu: "Show _MENU_",
                    info: "Showing _START_-_END_ of _TOTAL_ stock lines",
                    infoEmpty: "No stock lines",
                    infoFiltered: "(filtered from _MAX_)",
                    zeroRecords: "No matching stock found",
                    emptyTable: "No stock available"
                },
                footerCallback: function() {
                    const api = this.api();
                    const sumCol = function(idx) {
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
                    const qty = sumCol(COL.qty);

                    $(api.column(COL.qty).footer()).text(fmtNumber(qty, 2));
                    $('#cardItems').text(fmtNumber(items, 0));
                    $('#cardQty').text(fmtNumber(qty, 2));

                    if (IS_ADMIN) {
                        const val = sumCol(COL.total);
                        $(api.column(COL.total).footer()).text(fmtNumber(val, 0));
                        $('#cardValue').text(fmtNumber(val, 0));
                    }
                },
                initComplete: function() {
                    const api = this.api();
                    fillColumnFilter(api, COL.branch, '#filterBranch');
                    fillColumnFilter(api, COL.type, '#filterType');
                    fillColumnFilter(api, COL.warehouse, '#filterWarehouse');
                }
            });

            // >>> NEW (2026-09-21) - reset all client-side filters
            $('#btnResetFilters').on('click', function() {
                $('#filterBranch, #filterType, #filterWarehouse').val('');
                table.search('').columns().search('').draw();
            });

            // Added: 2026-07-20 - row click -> load ledger inside modal
            // Updated: 2026-09-21 - mobile-e SL cell (expand icon) click korle modal khulbe na; Enter key-o kaj korbe
            $('#stockSummaryTable tbody').on('click', 'tr.ledger-row', function(e) {
                const isCollapsed = $('#stockSummaryTable').hasClass('collapsed');
                if (isCollapsed && $(e.target).closest('td.dtr-control').length) {
                    return; // let Responsive toggle the hidden-columns row
                }
                openLedgerFromRow($(this));
            });

            $('#stockSummaryTable tbody').on('keydown', 'tr.ledger-row', function(e) {
                if (e.key === 'Enter') {
                    openLedgerFromRow($(this));
                }
            });
            // <<< END NEW

            // Added: 2026-07-20 - filter form (branch/product/date) inside modal -> AJAX re-submit, page reload hobe na
            $(document).on('submit', '#ledgerFilterForm', function(e) {
                e.preventDefault();
                let url = $(this).attr('action') + '?' + $(this).serialize();
                loadLedgerIntoModal(url);
            });

            // Added: 2026-08-13 - Stock Diagnosis tab click -> fetch once per product, then just toggle panes
            $('#btnShowDiagnosis').on('click', function() {
                showModalTab('diagnosis');

                if (!modalProductId) {
                    return;
                }

                if (diagnosisLoadedForProductId === modalProductId) {
                    return; // already loaded for this product, no refetch needed
                }

                let url = "{{ route('inventorySetup.stockDiagnosis.modal') }}" +
                    "?product_id=" + modalProductId +
                    "&branch_id=" + modalBranchId +
                    "&purchase_type=" + encodeURIComponent(modalPurchaseType || '') +
                    "&from_date=" + LEDGER_FROM_DATE +
                    "&to_date=" + LEDGER_TO_DATE;

                loadDiagnosisIntoModal(url);
            });
        });

        function loadLedgerIntoModal(url) {
            $('#ledgerPane').html(
                '<div id="ledgerLoading" class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 mb-0">Loading...</p></div>'
            );
            $('#productLedgerModal').modal('show');

            $.ajax({
                url: url,
                type: 'GET',
                success: function(html) {
                    $('#ledgerPane').html(html);

                    $('#ledgerPane .select2').select2({
                        dropdownParent: $('#productLedgerModal')
                    });
                },
                error: function() {
                    $('#ledgerPane').html(
                        '<div class="alert alert-danger m-3"> Something went wrong. Please try again. </div>'
                    );
                }
            });
        }

        // Added: 2026-08-13 - loads the Stock Diagnosis partial into its own pane
        function loadDiagnosisIntoModal(url) {
            $('#diagnosisPane').html(
                '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 mb-0">Loading diagnosis...</p></div>'
            );

            $.ajax({
                url: url,
                type: 'GET',
                success: function(html) {
                    $('#diagnosisPane').html(html);
                    diagnosisLoadedForProductId = modalProductId;
                },
                error: function() {
                    $('#diagnosisPane').html(
                        '<div class="alert alert-danger m-3"> Something went wrong loading the diagnosis. Please try again. </div>'
                    );
                    diagnosisLoadedForProductId = null;
                }
            });
        }


        function showModalTab(tab) {
            const $ledgerBtn = $('#btnShowLedger');
            const $diagBtn = $('#btnShowDiagnosis');

            if (tab === 'ledger') {
                $('#ledgerPane').show();
                $('#diagnosisPane').hide();
                $('#ledgerModalTitle').text('Product Ledger');

                $ledgerBtn.addClass('btn-primary active').removeClass('btn-outline-primary');
                $diagBtn.addClass('btn-outline-warning').removeClass('btn-warning active');
            } else {
                $('#diagnosisPane').show();
                $('#ledgerPane').hide();
                $('#ledgerModalTitle').text('Stock Diagnosis');

                $diagBtn.addClass('btn-warning active').removeClass('btn-outline-warning');
                $ledgerBtn.addClass('btn-outline-primary').removeClass('btn-primary active');
            }
        }


        function printStockSummary() {
            const table = $('#stockSummaryTable').DataTable();
            const prevLen = table.page.len();

            const restore = function() {
                window.removeEventListener('afterprint', restore);
                table.page.len(prevLen).draw(false);
            };
            window.addEventListener('afterprint', restore);

            table.page.len(-1).draw(false);
            setTimeout(function() {
                window.print();
            }, 150);
        }


        function exportStockSummaryToExcel() {
            const table = $('#stockSummaryTable').DataTable();
            const heads = [];
            const numeric = [];

            $('#stockSummaryTable thead th').each(function() {
                heads.push($(this).text().trim());
                numeric.push($(this).hasClass('text-right'));
            });

            const parseCell = function(text, isNum) {
                if (!isNum) return text;
                const n = parseFloat(text.replace(/,/g, ''));
                return isNaN(n) ? text : n;
            };

            const data = [heads];

            table.rows({
                search: 'applied',
                order: 'applied'
            }).every(function() {
                const row = [];
                $(this.node()).children('td').each(function(i) {
                    row.push(parseCell($(this).text().trim(), numeric[i]));
                });
                data.push(row);
            });

            const footRow = [];
            $('#stockSummaryTable tfoot th').each(function(i) {
                footRow.push(parseCell($(this).text().trim(), numeric[i]));
            });
            data.push(footRow);

            const ws = XLSX.utils.aoa_to_sheet(data);
            ws['!cols'] = heads.map(function(h, i) {
                let max = h.length;
                data.forEach(function(r) {
                    max = Math.max(max, String(r[i] ?? '').length);
                });
                return {
                    wch: Math.min(max + 2, 40)
                };
            });

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Stock Summary');

            const today = new Date().toISOString().slice(0, 10);
            XLSX.writeFile(wb, `stock-summary-${today}.xlsx`);
        }
    </script>
@endsection
