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

        @media (max-width: 767px) {
            .card-header .btn.no-print {
                margin-top: 8px;
            }

            #stockSummaryTable_wrapper .row {
                margin: 0;
            }

            #stockSummaryTable_filter input {
                width: 100%;
            }
        }

        #stockSummaryTable thead th {
            white-space: nowrap;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before {
            background-color: #007bff;
        }

        /* Added: 2026-07-20 - clickable row for ledger modal */
        #stockSummaryTable tbody tr {
            cursor: pointer;
        }

        #stockSummaryTable tbody tr:hover {
            background-color: #f1f7ff;
        }

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
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('project.project.index'))
                            <li class="breadcrumb-item"><a href="{{ route('project.project.index') }}">Project</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Project List</span></li>
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
                    <h3 class="card-title">Stock Summary</h3>
                    <a onclick="window.print()" target="_blank" class="btn btn-default float-right my-2 no-print"><i
                            class="fas fa-print"></i> Print</a>

                    {{-- Added: 2026-07-20 - Excel export button --}}
                    <a onclick="exportStockSummaryToExcel()" class="btn btn-success float-right my-2 mr-2 no-print"><i
                            class="fas fa-file-excel"></i> Excel</a>

                    <form action="{{ route('inventorySetup.currentStock.index') }}" method="post">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <label for="categorysubmit">Search By Category</label>
                                <select name="category_id" id="categorysubmit" class="form-control select2">
                                    <option selected value="all">All Category</option>
                                    @foreach ($categorys as $category)
                                        <option {{ $request->category_id == $category->id ? 'selected' : '' }}
                                            value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">

                    <div class="invoice p-3 mb-3">
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table id="stockSummaryTable" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Product Code</th>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th>Branch</th>
                                            <th>Type</th>
                                            <th>WearHouse Name</th>
                                            <th class="text-right">Qty</th>
                                            @if (auth()->user()->type == 'Admin')
                                                <th class="text-right">Avg Unit Price</th>
                                                <th class="text-right">Total</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                            $totalQty = 0;
                                            $totalPrice = 0;
                                            $totalUnitPrice = 0;
                                        @endphp

                                        @foreach ($currentSrock as $item)
                                            @if ($item->stock_qty > 0 && $item->type != 'Project')
                                                @php
                                                    $purchasesPrices = App\Models\PurchasesDetails::where(
                                                        'product_id',
                                                        $item->product_id,
                                                    )->pluck('unit_price');

                                                    $openingStockPrices = App\Models\ProductOpeningStockDetails::where(
                                                        'product_id',
                                                        $item->product_id,
                                                    )->pluck('unit_price');

                                                    $allPrices = $purchasesPrices->merge($openingStockPrices);
                                                    $avgPrice = $allPrices->avg() ?? 0;

                                                    $totalUnitPrice += round($avgPrice);
                                                    $totalQty += $item->stock_qty;
                                                    $totalPrice += round($avgPrice * $item->stock_qty, 2);
                                                @endphp

                                                {{-- Modified: 2026-07-20 - whole row click opens ledger modal --}}
                                                <tr class="ledger-row" data-product-id="{{ $item->product_id }}"
                                                    data-branch-id="{{ $item->branch_id }}"
                                                    data-purchase-type="{{ $item->purchasetype ?? '' }}">
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ optional($item->products)->getRawOriginal('productCode') }}</td>
                                                    <td>{{ optional($item->products)->getRawOriginal('name') }}
                                                        {{ $item->products->brand->name ?? '' }}</td>
                                                    <td>{{ optional(optional($item->products)->category)->name ?? 'N/A' }}
                                                    </td>
                                                    <td>{{ optional($item->branch)->branchCode . ' - ' . optional($item->branch)->name ?? 'N/A' }}
                                                    </td>
                                                    <td align="right">{{ $item->purchasetype ?? '-' }}</td>
                                                    <td align="right">{{ optional($item->branch)->name ?? 'N/A' }}</td>
                                                    <td align="right">{{ $item->stock_qty }}</td>
                                                    @if (auth()->user()->type == 'Admin')
                                                        <td align="right">{{ number_format($avgPrice, 2) }}</td>
                                                        <td align="right">
                                                            {{ number_format($avgPrice * $item->stock_qty, 2) }}</td>
                                                    @endif
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="7" style="text-align: right">Total</th>
                                            <th style="text-align: right">{{ $totalQty }}</th>
                                            @if (auth()->user()->type == 'Admin')
                                                <th style="text-align: right">{{ number_format($totalUnitPrice, 0) }}</th>
                                                <th style="text-align: right">{{ number_format($totalPrice, 0) }}</th>
                                            @endif
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="col-4 float-left">
                                <br><br>
                                <p>Prepared By:_____________<br />Date:____________________</p>
                            </div>
                            <div class="col-6 text-center"></div>
                            <div class="col-2">
                                <br><br>
                                <p>Approved By:________________<br />Date:_________________</p>
                            </div>

                            <hr>

                            <div class="col-md-12 bg-success" style="text-align: center">
                                Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} Company products.
                                We believe you will be satisfied by our services.
                            </div>
                        </div>
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
                        <button type="button" id="btnShowDiagnosis" class="btn btn-sm btn-outline-warning tab-btn"
                            onclick="showModalTab('diagnosis')">
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

        $(document).ready(function() {
            $('#categorysubmit').on('change', function() {
                $(this).closest('form').submit();
            });

            $('#stockSummaryTable').DataTable({
                responsive: true,
                paging: false,
                info: false,
                ordering: true,
                order: [],
                language: {
                    search: "Search (Code / Name):"
                }
            });

            // Added: 2026-07-20 - row click -> load ledger inside modal
            // Updated: 2026-08-13 - remembers product/branch, resets to Ledger tab each time a new row is opened
            $('#stockSummaryTable tbody').on('click', 'tr.ledger-row', function() {
                modalProductId = $(this).data('product-id');
                modalBranchId = $(this).data('branch-id');
                modalPurchaseType = $(this).data('purchase-type');
                diagnosisLoadedForProductId = null; // force diagnosis reload for the newly selected product

                let url = "{{ route('inventorySetup.productledger.modal') }}" +
                    "?product_id=" + modalProductId +
                    "&branch_id=" + modalBranchId +
                    "&purchase_type=" + encodeURIComponent(modalPurchaseType || '') +
                    "&from_date={{ date('2020-01-01') }}" +
                    "&to_date={{ date('Y-12-d') }}";

                showModalTab('ledger'); // always open on the Ledger tab
                loadLedgerIntoModal(url);
            });

            // Added: 2026-07-20 - filter form (branch/product/date) inside modal -> AJAX re-submit, page reload হবে না
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
                    "&from_date={{ date('2020-01-01') }}" +
                    "&to_date={{ date('Y-12-d') }}";

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

        // Added: 2026-08-13 - swap panes + active tab styling without closing the modal
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

        function exportStockSummaryToExcel() {
            const table = document.getElementById('stockSummaryTable');

            const wb = XLSX.utils.table_to_book(table, {
                sheet: 'Stock Summary',
                raw: false
            });

            const today = new Date().toISOString().slice(0, 10);
            XLSX.writeFile(wb, `stock-summary-${today}.xlsx`);
        }
    </script>
@endsection
