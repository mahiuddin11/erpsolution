@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <style>
        #datatablexcel thead th {
            white-space: nowrap;
            background: #f4f6f9;
            vertical-align: middle;
        }

        #datatablexcel tbody td {
            vertical-align: middle;
        }

        .filter-bar label {
            font-size: 11px;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 2px;
            white-space: nowrap;
        }

        .cell-hr {
            margin: 6px 0;
            border-top: 1px solid #dee2e6;
        }

        .print-only {
            display: none;
        }

        .table-footer-wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        /* Print Specific Professional Styling */
        @page {
            size: A4 landscape;
            margin: 10mm 10mm 10mm 10mm;
        }

        @media print {

            .no-print,
            .filter-bar,
            #summaryCards,
            .dataTables_length,
            .dataTables_filter,
            .dataTables_info,
            #pageTitle,
            .dataTables_paginate,
            tfoot {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            body {
                background: #fff !important;
                color: #000 !important;
                font-size: 9pt !important;
            }

            .card,
            .card-body {
                border: none !important;
                padding: 0 !important;
                box-shadow: none !important;
            }

            .print-header-spacer {
                margin-top: 5px !important;
                /* Header shesh hobar 5px por table shuru hobe */
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .table th,
            .table td {
                padding: 4px 6px !important;
                font-size: 8.5pt !important;
                white-space: normal !important;
            }

            tr {
                page-break-inside: avoid !important;
            }

            thead {
                display: table-header-group !important;
            }

            /* Print Total Row only on Last Page */
            .print-total-row {
                display: table-row !important;
            }

            /* Signature section pinned nicely at the bottom of the last page */
            .print-signature-section {
                display: flex !important;
                justify-content: space-between;
                margin-top: 40px;
                page-break-inside: avoid;
            }

            .badge {
                border: 1px solid #999;
                color: #000 !important;
                background: none !important;
            }
        }

        .print-total-row,
        .print-signature-section {
            display: none;
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
                        <li class="breadcrumb-item active"><span>Purchase Reports</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    @php
        $rows = collect($purchaseDetails->items() ?? []);
        $selectedType = $purchasetype ?? 'all';

        $totalQty = 0;
        $totalAmount = 0;

        foreach ($purchaseDetails as $item) {
            $totalQty += $item->quantity;
            $totalAmount += $item->total_price;
        }
        $totalPurchases = $purchaseDetails->pluck('purchases_id')->unique()->count();
        $localQty = $rows->where('purchasetype', 'local')->sum('quantity');
        $importedQty = $rows->where('purchasetype', 'imported')->sum('quantity');
    @endphp

    <div class="row no-print" id="summaryCards">
        <div class="col-12 col-sm-4 mb-2">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-file-invoice"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Purchases</span>
                    <span class="info-box-number">{{ number_format($totalPurchases) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4 mb-2">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Quantity</span>
                    <span class="info-box-number">{{ number_format($totalQty, 2) }} <small>(Local: {{ $localQty }},
                            Imported: {{ $importedQty }})</small></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4 mb-2">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-coins"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Purchase Amount</span>
                    <span class="info-box-number">{{ number_format($totalAmount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between" id="pageTitle">
                    <h3 class="card-title mb-0">Purchase Reports</h3>
                    <div class="card-tools-wrap d-flex no-print" style="gap:8px">
                        @if ($rows->count() > 0)
                            <button type="button" onclick="exportPurchaseToExcel()" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                            <button type="button" onclick="window.print()" class="btn btn-default btn-sm">
                                <i class="fas fa-print"></i> Print
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="print-only text-center mb-2 print-header-spacer">
                        <h4 class="mb-0 font-weight-bold">{{ $companyInfo->company_name ?? '' }}</h4>
                        <h5 class="mb-0">Purchase Reports</h5>
                        <small>From: {{ $from_date ?? 'N/A' }} To: {{ $to_date ?? 'N/A' }} | Printed:
                            {{ date('d M Y, h:i A') }}</small>
                    </div>

                    <!-- Single Row Responsive Filter Bar -->
                    <div class="no-print filter-bar mb-4">
                        <form action="{{ route('report.purchase.purchase') }}" method="POST" id="filterForm">
                            @csrf
                            <input type="hidden" name="perPage" id="perPageInput" value="{{ $request->perPage ?? 50 }}">
                            <div class="row align-items-end">
                                <div class="col-12 col-sm-6 col-md-3 col-xl-2 mb-2">
                                    <label for="reservation">Date Range</label>
                                    <input type="text" class="form-control form-control-sm" name="dateRange"
                                        value="{{ $request->dateRange ?? '' }}" id="reservation" />
                                </div>
                                <div class="col-6 col-sm-6 col-md-2 col-xl-1 mb-2">
                                    <label for="typeSelect">Type</label>
                                    <select class="form-control form-control-sm select2" name="type" id="typeSelect"
                                        style="width:100%">
                                        <option value="all" {{ ($type ?? 'all') == 'all' ? 'selected' : '' }}>All
                                        </option>
                                        <option value="Branch" {{ ($type ?? '') == 'Branch' ? 'selected' : '' }}>Warehouse
                                        </option>
                                        <option value="Project" {{ ($type ?? '') == 'Project' ? 'selected' : '' }}>Project
                                        </option>
                                    </select>
                                </div>
                                <div class="col-6 col-sm-6 col-md-3 col-xl-1dot5 mb-2" id="branchSelectWrap">
                                    <label for="branch_id">Warehouse</label>
                                    <select class="form-control form-control-sm select2" name="branch_id" id="branch_id"
                                        style="width:100%">
                                        <option value="all">All branches</option>
                                        @foreach ($branch as $value)
                                            <option {{ ($branch_id ?? '') == $value->id ? 'selected' : '' }}
                                                value="{{ $value->id }}">
                                                {{ $value->branchCode . ' - ' . $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-sm-6 col-md-3 col-xl-1dot5 mb-2" id="projectSelectWrap"
                                    style="display:none;">
                                    <label for="project_id">Project</label>
                                    <select class="form-control form-control-sm select2" name="project_id" id="project_id"
                                        style="width:100%">
                                        <option value="all">All Project</option>
                                        @foreach ($projects as $value)
                                            <option {{ ($project_id ?? '') == $value->id ? 'selected' : '' }}
                                                value="{{ $value->id }}">
                                                {{ $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-sm-6 col-md-3 col-xl-1dot5 mb-2">
                                    <label for="product_id">Product</label>
                                    <select class="form-control form-control-sm select2" name="product_id"
                                        id="product_id_select" style="width:100%">
                                        <option value="all">All Products</option>
                                    </select>
                                </div>
                                <div class="col-6 col-sm-6 col-md-3 col-xl-1dot5 mb-2">
                                    <label for="ledger_id">Ledger</label>
                                    <select class="form-control form-control-sm select2" name="ledger_id"
                                        id="ledger_id_select" style="width:100%">
                                        <option value="all">All</option>
                                    </select>
                                </div>
                                <div class="col-6 col-sm-6 col-md-3 col-xl-1dot5 mb-2">
                                    <label for="supplier_id">Supplier</label>
                                    <select class="form-control form-control-sm select2" name="supplier_id"
                                        id="supplier_id" style="width:100%">
                                        <option value="all">All Suppliers</option>
                                        @foreach ($supplier as $value)
                                            <option {{ ($supplier_id ?? '') == $value->id ? 'selected' : '' }}
                                                value="{{ $value->id }}">
                                                {{ $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-sm-6 col-md-2 col-xl-1 mb-2">
                                    <label for="purchasetypeSelect">Purchase Type</label>
                                    <select class="form-control form-control-sm select2" name="purchasetype"
                                        id="purchasetypeSelect" style="width:100%">
                                        <option value="all" {{ ($purchasetype ?? 'all') == 'all' ? 'selected' : '' }}>
                                            All</option>
                                        <option value="local" {{ ($purchasetype ?? '') == 'local' ? 'selected' : '' }}>
                                            Local</option>
                                        <option value="imported"
                                            {{ ($purchasetype ?? '') == 'imported' ? 'selected' : '' }}>Imported</option>
                                    </select>
                                </div>

                                <!-- Search and Reset Buttons -->
                                <div class="col-12 col-sm-12 col-md-3 col-xl-2 mb-2 d-flex align-items-end"
                                    style="gap: 6px;">
                                    <button type="submit" class="btn btn-sm btn-success btn-block m-0">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                    <button type="button" id="btnResetFilters"
                                        class="btn btn-sm btn-outline-secondary btn-block m-0">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if ($rows->count() > 0)
                        <!-- Table Top Header: Per Page Selector Control -->
                        <div class="d-flex justify-content-between align-items-center mb-3 no-print flex-wrap">
                            <div class="dataTables_length">
                                <label>Show
                                    <select id="perPageSelect"
                                        class="custom-select custom-select-sm form-control form-control-sm"
                                        style="width: 80px; display: inline-block;">
                                        <option value="50" {{ $request->perPage == 50 ? 'selected' : '' }}>50
                                        </option>
                                        <option value="100" {{ $request->perPage == 100 ? 'selected' : '' }}>100
                                        </option>
                                        <option value="1000" {{ $request->perPage == 1000 ? 'selected' : '' }}>1000
                                        </option>
                                        <option value="all" {{ $request->perPage == 'all' ? 'selected' : '' }}>All
                                        </option>
                                    </select> entries
                                </label>
                            </div>
                            <div class="dataTables_info text-muted">
                                Showing {{ $purchaseDetails->firstItem() }} to {{ $purchaseDetails->lastItem() }} of
                                {{ $purchaseDetails->total() }} entries
                            </div>
                        </div>

                        @php
                            $groupedPurchases = $rows->groupBy(function ($item) {
                                return $item->purchases_id . '_' . $item->purchasetype;
                            });
                            $sl = ($purchaseDetails->currentPage() - 1) * $purchaseDetails->perPage() + 1;
                        @endphp

                        <div class="table-responsive">
                            <table id="datatablexcel" class="table table-striped table-bordered table-hover"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th>Invoice</th>
                                        <th>Branch/Project</th>
                                        <th>Warehouse</th>
                                        <th>Supplier / Ledger</th>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Unit Price</th>
                                        <th class="text-right">Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupedPurchases as $groupKey => $groupItems)
                                        @php
                                            $firstItem = $groupItems->first();
                                            $separator = '<hr class="cell-hr">';

                                            $productNames = $groupItems
                                                ->map(fn($d) => optional($d->product)->getRawOriginal('name') ?? '-')
                                                ->implode($separator);
                                            $qtys = $groupItems->map(fn($d) => $d->quantity)->implode($separator);
                                            $rates = $groupItems
                                                ->map(fn($d) => number_format($d->unit_price, 2))
                                                ->implode($separator);
                                            $prices = $groupItems
                                                ->map(fn($d) => number_format($d->total_price, 2))
                                                ->implode($separator);

                                            $groupQtySum = $groupItems->sum('quantity');
                                            $groupPriceSum = $groupItems->sum('total_price');

                                            $supplierOrLedger = 'N/A';
                                            $activeLedgerId =
                                                !empty($firstItem->ledger_id) && $firstItem->ledger_id != 0
                                                    ? $firstItem->ledger_id
                                                    : optional($firstItem->purchase)->ledger_id;

                                            $activeSupplierId =
                                                !empty($firstItem->supplier_id) && $firstItem->supplier_id != 0
                                                    ? $firstItem->supplier_id
                                                    : optional($firstItem->purchase)->supplier_id;

                                            if (!empty($activeLedgerId) && $activeLedgerId != 0) {
                                                $supplierOrLedger =
                                                    optional($firstItem->ledger)->account_name ??
                                                    (optional(optional($firstItem->purchase)->ledger)->account_name ??
                                                        'N/A');
                                            } elseif (!empty($activeSupplierId) && $activeSupplierId != 0) {
                                                $supplierOrLedger =
                                                    optional($firstItem->supplier)->name ??
                                                    (optional(optional($firstItem->purchase)->supplier)->name ?? 'N/A');
                                            }

                                            $purchaseTypeLabel = ucfirst($firstItem->purchasetype ?? '-');
                                        @endphp
                                        <tr>
                                            <td>{{ $sl++ }}</td>
                                            <td>{{ $firstItem->date }}</td>
                                            <td>{{ optional($firstItem->purchase)->invoice_no ?? '' }}</td>
                                            <td>
                                                @if (optional($firstItem->purchase)->type == 'Branch')
                                                    {{ optional($firstItem->purchase->branch)->branchCode }}
                                                    {{ optional($firstItem->purchase->branch)->name }}
                                                @else
                                                    {{ optional($firstItem->purchase->project)->name }}
                                                @endif
                                            </td>
                                            <td>{{ optional(optional($firstItem->purchase)->warehouse)->name ?? '-' }}</td>
                                            <td>{{ $supplierOrLedger }}</td>
                                            <td>{!! $productNames !!}</td>
                                            <td>
                                                @if ($firstItem->purchasetype == 'imported')
                                                    <span class="badge badge-info">Imported</span>
                                                @elseif ($firstItem->purchasetype == 'local')
                                                    <span class="badge badge-secondary">Local</span>
                                                @else
                                                    {{ $firstItem->purchasetype }}
                                                @endif
                                            </td>
                                            <td class="text-right font-weight-bold" data-order="{{ $groupQtySum }}">
                                                {!! $qtys !!}</td>
                                            <td class="text-right">{!! $rates !!}</td>
                                            <td class="text-right" data-order="{{ $groupPriceSum }}">
                                                {!! $prices !!}</td>
                                        </tr>
                                    @endforeach
                                    <!-- Print Only Total Row appearing exclusively on the Last Page -->
                                    <tr class="print-total-row">
                                        <th colspan="8" style="text-align: right;">Total:</th>
                                        <th style="text-align: right;">{{ number_format($totalQty, 2) }}</th>
                                        <th></th>
                                        <th style="text-align: right;">{{ number_format($totalAmount, 2) }}</th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="8" class="text-right">Total:</th>
                                        <th class="text-right">{{ number_format($totalQty, 2) }}</th>
                                        <th></th>
                                        <th class="text-right">{{ number_format($totalAmount, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Table Footer Wrap: Side-aligned Pagination & Info -->
                        <div class="table-footer-wrap no-print">
                            <div class="dataTables_info text-muted">
                                Showing {{ $purchaseDetails->firstItem() }} to {{ $purchaseDetails->lastItem() }} of
                                {{ $purchaseDetails->total() }} entries
                            </div>
                            <div class="dataTables_paginate paging_simple_numbers">
                                {!! $purchaseDetails->appends(request()->query())->links('pagination::bootstrap-4') !!}
                            </div>
                        </div>

                        <!-- Signatures visible on web screen & on the bottom of the last printed page -->
                        <div class="row mt-4 print-signature-section">
                            <div class="col-6 mb-3">
                                <p class="mb-0">Prepared By:_____________<br />Date:____________________</p>
                            </div>
                            <div class="col-6 text-right mb-3">
                                <p class="mb-0">Approved By:________________<br />Date:_________________</p>
                            </div>
                        </div>

                        <div class="text-center bg-success p-2 mt-3 no-print">
                            Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} products.
                            We believe you will be satisfied by our services.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        $(document).ready(function() {
            function toggleFields() {
                let selectedType = $("#typeSelect").val();
                if (selectedType === "Branch") {
                    $("#branchSelectWrap").show();
                    $("#projectSelectWrap").hide();
                } else if (selectedType === "Project") {
                    $("#branchSelectWrap").hide();
                    $("#projectSelectWrap").show();
                } else {
                    $("#branchSelectWrap").show();
                    $("#projectSelectWrap").hide();
                }
            }

            toggleFields();
            $("#typeSelect").on("change", toggleFields);

            // Per page dropdown change event submission
            $('#perPageSelect').on('change', function() {
                let val = $(this).val();
                $('#perPageInput').val(val);
                $('#filterForm').submit();
            });

            // Reset filters button action
            $('#btnResetFilters').on('click', function() {
                $('#reservation').val('');
                $('#typeSelect').val('all').trigger('change.select2');
                $('#branch_id').val('all').trigger('change.select2');
                $('#project_id').val('all').trigger('change.select2');
                $('#product_id_select').val('all').trigger('change.select2');
                $('#ledger_id_select').val('all').trigger('change.select2');
                $('#supplier_id').val('all').trigger('change.select2');
                $('#purchasetypeSelect').val('all').trigger('change.select2');
                $('#perPageInput').val('50');
                $('#filterForm').submit();
            });

            // AJAX call for filtering data
            $.ajax({
                url: "{{ route('report.purchase.filter.data') }}",
                type: "GET",
                success: function(response) {
                    let productSelect = $('#product_id_select');
                    let ledgerSelect = $('#ledger_id_select');

                    let selectedProduct = "{{ $product_id ?? 'all' }}";
                    let selectedLedger = "{{ $ledger_id ?? 'all' }}";

                    if (response.products) {
                        response.products.forEach(function(prod) {
                            let isSel = (String(prod.id) === String(selectedProduct)) ?
                                'selected' : '';
                            productSelect.append(
                                `<option value="${prod.id}" ${isSel}>${prod.productCode} - ${prod.name}</option>`
                            );
                        });
                    }

                    if (response.ledgers) {
                        response.ledgers.forEach(function(led) {
                            let isSel = (String(led.id) === String(selectedLedger)) ?
                                'selected' : '';
                            ledgerSelect.append(
                                `<option value="${led.id}" ${isSel}>${led.accountName || led.account_name}</option>`
                            );
                        });
                    }

                    productSelect.trigger('change.select2');
                    ledgerSelect.trigger('change.select2');
                }
            });
        });

        // Excel Export Function
        function exportPurchaseToExcel() {
            const table = document.getElementById('datatablexcel');
            if (!table) return;

            const heads = [];
            const numeric = [];

            $('#datatablexcel thead th').each(function() {
                heads.push($(this).text().trim());
                numeric.push($(this).hasClass('text-right'));
            });

            const data = [heads];

            $('#datatablexcel tbody tr').not('.print-total-row').each(function() {
                const row = [];
                $(this).find('td').each(function(i) {
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
            $('#datatablexcel tfoot th').each(function(i) {
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
            XLSX.utils.book_append_sheet(wb, ws, 'Purchase Reports');
            XLSX.writeFile(wb, `purchase-summary-${new Date().toISOString().slice(0, 10)}.xlsx`);
        }
    </script>
@endsection
