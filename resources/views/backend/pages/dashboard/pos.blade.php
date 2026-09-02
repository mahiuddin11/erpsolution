{{-- resources/views/backend/pages/dashboard/pos.blade.php --}}
{{-- Rebuilt: 2025-08-08 -- quick action fix, generic paginated print/export
     modal, sale invoice modal, advanced product search, top seller panel --}}

@extends('backend.layouts.master')

@section('title')
    {{ $pgTitle }} - Admin Panel
@endsection

@section('styles')
    <link rel="stylesheet"
        href="{{ asset('css/dashboard-style.css') }}?v={{ filemtime(public_path('css/dashboard-style.css')) }}">

    <style>
        .pos-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .pos-quick-actions {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .pos-quick-actions .btn {
            border-radius: 8px;
            font-weight: 500;
            font-size: .85rem;
        }

        .chart-wrap {
            position: relative;
            height: 300px;
        }

        @media (max-width: 768px) {
            .txn-invoice-modal-dialog {
                max-width: 95vw;
                width: 95vw;
                height: 90vh;
                margin: 5vh auto;
            }

            .chart-wrap {
                height: 220px;
            }
        }

        .paid-due-wrap {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            flex-wrap: wrap;
        }

        .paid-due-ring {
            position: relative;
            width: 130px;
            height: 130px;
            flex-shrink: 0;
            margin: 0 auto;
        }

        .paid-due-ring canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .paid-due-ring-label {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .paid-due-ring-label .pct {
            font-size: 1.3rem;
            font-weight: 700;
            color: #22c55e;
        }

        .paid-due-ring-label .txt {
            font-size: .7rem;
            color: #94a3b8;
        }

        .paid-due-stats {
            flex: 1;
            min-width: 140px;
            display: flex;
            flex-direction: column;
            gap: .6rem;
        }

        .paid-due-stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .5rem .75rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, .03);
        }

        .paid-due-stat-row .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .clickable-row {
            cursor: pointer;
            transition: background .15s;
        }

        .clickable-row:hover {
            background: rgba(255, 255, 255, .05);
        }

        .filter-inline {
            display: flex;
            gap: .5rem;
            margin-bottom: .75rem;
            flex-wrap: wrap;
        }

        .filter-inline .flex-1 {
            flex: 1;
            min-width: 150px;
        }

        .filter-inline select,
        .filter-inline input {
            font-size: .8rem;
            border-radius: 6px;
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: .85rem;
        }

        .search-box input {
            padding-left: 32px;
        }

        .rank-badge {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .75rem;
            color: #fff;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .rank-1 {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }

        .rank-2 {
            background: linear-gradient(135deg, #cbd5e1, #94a3b8);
        }

        .rank-3 {
            background: linear-gradient(135deg, #f97316, #c2410c);
        }

        .rank-other {
            background: #334155;
        }

        .coming-soon-note {
            margin-top: .75rem;
            padding: .6rem .8rem;
            border-radius: 8px;
            background: rgba(99, 102, 241, .08);
            border: 1px dashed rgba(99, 102, 241, .35);
            font-size: .78rem;
            color: #a5b4fc;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Detail Modal (product/receivable/supplier) */
        .detail-modal-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            justify-content: space-between;
            align-items: center;
            margin-bottom: .75rem;
        }

        .detail-modal-actions {
            display: flex;
            gap: .5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .detail-modal-pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: .75rem;
            font-size: .82rem;
        }

        .detail-modal-pagination .btn {
            padding: .25rem .6rem;
            font-size: .78rem;
        }


        .pos-wrap {
            --fin-green: #10b981;
            --fin-green-dark: #059669;
            --fin-red: #ef4444;
            --fin-amber: #f59e0b;
            --fin-purple: #7c3aed;
        }

        /* ---- Toolbar controls, pill style (matches Financial dashboard tabs) ---- */
        .pos-tabs {
            display: inline-flex;
            background: #fff;
            border: 1px solid var(--gray-200, #e5e7eb);
            border-radius: 999px;
            padding: 4px;
            gap: 2px;
        }

        .pos-tab {
            border: none;
            background: transparent;
            padding: 7px 18px;
            border-radius: 999px;
            font-size: .85rem;
            font-weight: 600;
            color: var(--gray-600, #4b5563);
            transition: .15s;
        }

        .pos-tab:hover {
            color: var(--gray-900, #111827);
        }

        .pos-tab.active {
            background: var(--emerald-100, #d1fae5);
            color: var(--emerald-700, #047857);
        }

        .pos-quick-actions .btn {
            border-radius: 999px;
            padding: 8px 16px;
            font-weight: 600;
        }

        /* ---- KPI cards -- flat white card, colored icon badge, hover lift ---- */
        .metric-card {
            background: #fff;
            border: 1px solid var(--gray-200, #e5e7eb);
            border-radius: 14px;
            padding: 18px 20px;
            height: 100%;
            box-shadow: none;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px -10px rgba(0, 0, 0, .18);
            border-color: var(--gray-500, #9ca3af);
        }

        .metric-card.card-blue,
        .metric-card.card-purple,
        .metric-card.card-green,
        .metric-card.card-red {
            border-left: none;
            background: #fff;
        }

        .metric-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .metric-title {
            font-size: .8rem;
            font-weight: 600;
            color: var(--gray-500, #6b7280);
        }

        .metric-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .metric-value {
            font-size: clamp(15px, 2vw, 28px);
            font-weight: 700;
            line-height: 1.2;
            color: var(--gray-900, #111827) !important;
            white-space: nowrap;
        }

        .metric-sub {
            font-size: .78rem;
            font-weight: 600;
            color: var(--gray-500, #6b7280);
            margin-top: 4px;
        }

        .card-blue .metric-icon {
            background: #eff6ff;
            color: #2563eb;
        }

        .card-purple .metric-icon {
            background: #f3e8ff;
            color: #9333ea;
        }

        .card-green .metric-icon {
            background: #f0fdf4;
            color: #16a34a;
        }

        .card-red .metric-icon {
            background: #fef2f2;
            color: #dc2626;
        }

        .txn-invoice-modal-dialog {
            max-width: 90vw;
            width: 90vw;
            height: 90vh;
            margin: 5vh auto;
        }

        .txn-invoice-modal-dialog .modal-content {
            height: 100%;
        }

        .txn-invoice-modal-dialog .modal-body {
            height: calc(100% - 56px);
            /* header height baad diye */
        }


        /* ---- Panel header -- title left, helper text/legend right ---- */
        .panel-header.fin-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .fin-panel-title {
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--gray-900, #111827);
        }

        /* ---- Paid/Due stat rows -- light chips instead of dark translucent ---- */
        .paid-due-stat-row {
            background: var(--gray-50, #f9fafb);
        }

        /* ---- Coming soon note -- soft emerald tint to match fin palette ---- */
        .coming-soon-note {
            background: #ecfdf5;
            border: 1px dashed #a7f3d0;
            color: var(--fin-green-dark, #059669);
        }



        @media (max-width: 480px) {
            .pos-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .pos-quick-actions .btn {
                justify-content: center;
            }
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $pgTitle }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a href="#">{{ $pgTitle }}</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="dashboard-wrap pos-wrap">

        {{-- Toolbar: Dropdown Filter + Quick Actions (same row) --}}
        <div class="pos-toolbar">
            <div class="pos-tabs" id="posFilterTabs">
                <button type="button" class="pos-tab active" data-filter="today">Today</button>
                <button type="button" class="pos-tab" data-filter="week">This Week</button>
                <button type="button" class="pos-tab" data-filter="month">This Month</button>
                <button type="button" class="pos-tab" data-filter="year">This Year</button>
            </div>
            <div class="pos-quick-actions" id="posQuickActions"></div>
        </div>

        {{-- KPI Cards --}}
        <div class="row g-3 mb-4" id="posMetrics"></div>

        {{-- Sales vs Purchase Trend + Paid/Due --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-8">
                <div class="panel h-100">
                    <div class="panel-header fin-panel-header">
                        <span class="fin-panel-title"><i class="bi bi-bar-chart-line"></i> Sales vs Purchase (12
                            Month)</span>
                    </div>
                    <div class="panel-body">
                        <div class="chart-wrap"><canvas id="salesPurchaseChart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel h-100">
                    <div class="panel-header fin-panel-header">
                        <span class="fin-panel-title"><i class="bi bi-pie-chart"></i> Paid vs Due</span>
                    </div>
                    <div class="panel-body">
                        <div class="paid-due-wrap">
                            <div class="paid-due-ring">
                                <canvas id="paidDueRing"></canvas>
                                <div class="paid-due-ring-label">
                                    <div class="pct" id="paidPercentLabel">0%</div>
                                    <div class="txt">Paid</div>
                                </div>
                            </div>
                            <div class="paid-due-stats">
                                <div class="paid-due-stat-row"><span><span class="dot"
                                            style="background:#22c55e"></span>Paid</span><strong
                                        id="paidAmountLabel">0</strong></div>
                                <div class="paid-due-stat-row"><span><span class="dot"
                                            style="background:#ef4444"></span>Due</span><strong
                                        id="dueAmountLabel">0</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Products + Recent Transactions --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header fin-panel-header">
                        <span class="fin-panel-title"><i class="bi bi-box-seam"></i> Top Selling Products</span>
                        <span class="text-muted small">Click for details</span>
                    </div>
                    <div class="panel-body">
                        <div class="scroll-box" id="topProductsList"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header fin-panel-header">
                        <span class="fin-panel-title"><i class="bi bi-receipt"></i> Recent Purchases & Sales</span>
                        <div class="search-box" style="min-width:190px">
                            <i class="bi bi-search"></i>
                            <input type="text" id="recentTxnSearch" class="form-control form-control-sm"
                                placeholder="Search invoice, party...">
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="scroll-box" id="recentTransactionsList"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Consumption --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-12">
                <div class="panel h-100">
                    <div class="panel-header fin-panel-header">
                        <span class="fin-panel-title"><i class="bi bi-boxes"></i> Product Consumption (Project-wise)</span>
                    </div>
                    <div class="panel-body">
                        <div class="filter-inline">
                            <div class="search-box flex-1">
                                <i class="bi bi-search"></i>
                                <input type="text" id="consumptionSearch" class="form-control form-control-sm"
                                    placeholder="Search product, code, project or invoice...">
                            </div>
                            <select id="consumptionProjectFilter" class="form-control form-control-sm"
                                style="max-width:200px">
                                <option value="">All Projects</option>
                            </select>
                            <select id="consumptionProductFilter" class="form-control form-control-sm"
                                style="max-width:220px">
                                <option value="">All Products</option>
                            </select>
                        </div>
                        <div class="scroll-box" id="productConsumptionList" style="height:300px"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Seller + Top Receivables + Supplier Due --}}
        <div class="row g-3">
            <div class="col-lg-4">
                <div class="panel h-100">
                    <div class="panel-header fin-panel-header">
                        <span class="fin-panel-title"><i class="bi bi-trophy"></i> Top Seller</span>
                    </div>
                    <div class="panel-body">
                        <div class="scroll-box" id="salesPerformanceList"></div>
                        <div class="coming-soon-note"><i class="bi bi-rocket-takeoff"></i> Target, commission & incentive
                            analytics coming soon</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel h-100">
                    <div class="panel-header fin-panel-header">
                        <span class="fin-panel-title"><i class="bi bi-person-exclamation"></i> Top Receivables</span>
                        <span class="text-muted small">Click for details</span>
                    </div>
                    <div class="panel-body">
                        <div class="scroll-box" id="topReceivablesList"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel h-100">
                    <div class="panel-header fin-panel-header">
                        <span class="fin-panel-title"><i class="bi bi-truck"></i> Supplier Due</span>
                        <span class="text-muted small">Click for details</span>
                    </div>
                    <div class="panel-body">
                        <div class="scroll-box" id="supplierDueList"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Generic Detail Modal (Products / Receivables / Supplier Due) --}}
    <div class="modal fade" id="detailListModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailListModalTitle">Details</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="detail-modal-toolbar">
                        <select id="detailExportScope" class="form-control form-control-sm" style="max-width:160px">
                            <option value="page">Current Page</option>
                            <option value="all">All Data</option>
                        </select>
                        <div class="detail-modal-actions">
                            <button class="btn btn-sm btn-outline-secondary" id="detailPrintBtn"><i
                                    class="bi bi-printer"></i> Print</button>
                            <button class="btn btn-sm btn-outline-success" id="detailExportBtn"><i
                                    class="bi bi-file-earmark-excel"></i> Excel</button>
                        </div>
                    </div>
                    <div id="detailListModalBody">
                        <div class="text-center text-muted py-3">Loading...</div>
                    </div>
                    <div class="detail-modal-pagination" id="detailPagination"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="quickActionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog txn-invoice-modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickActionModalTitle">Action</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-0">
                    <div id="quickActionLoading" class="text-center text-muted py-5">Loading...</div>
                    <iframe id="quickActionIframe" style="width:100%;height:100%;border:0;display:none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    {{-- Single Sale Invoice Modal --}}
    <div class="modal fade" id="txnInvoiceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog txn-invoice-modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="txnInvoiceModalTitle">Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-0">
                    <div id="txnInvoiceLoading" class="text-center text-muted py-5">Loading...</div>
                    <iframe id="txnInvoiceIframe" style="width:100%;height:100%;border:0;display:none;"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        const API_BASE = '/api/pos-dashboard';
        let currentFilter = 'today';
        let salesPurchaseChart = null;
        let paidDueRingChart = null;

        /* ---------------- Helpers ---------------- */
        const fmtMoney = (n) => Number(n || 0).toLocaleString('en-US', {
            minimumFractionDigits: 0
        });
        const fmtDateTime = (d) => new Date(d).toLocaleString('en-US', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        const debounce = (fn, delay = 400) => {
            let t;
            return (...a) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...a), delay);
            };
        };

        function metricCard({
            title,
            value,
            sub,
            icon,
            theme
        }) {
            return `
  <div class="col-6 col-lg-4">
    <div class="metric-card card-${theme}">
      <div class="metric-top"><div class="metric-title">${title}</div><i class="bi ${icon} metric-icon fs-5"></i></div>
      <div class="metric-value">${value}</div>
      <div class="metric-sub">${sub}</div>
    </div>
  </div>`;
        }

        /* ---------------- Quick Actions ---------------- */
        function loadQuickActions() {
            fetch(`${API_BASE}/quick-actions`).then(r => r.json()).then(actions => {
                document.getElementById('posQuickActions').innerHTML = actions.length ? actions.map(a =>
                    `<button type="button" class="btn btn-sm btn-success" data-url="${a.url}" data-label="${a.label}">
               <i class="bi ${a.icon}"></i> ${a.label}
             </button>`
                ).join('') : '';

                // >>> NEW: quick action button click -> modal e iframe open
                document.querySelectorAll('#posQuickActions button[data-url]').forEach(btn => {
                    btn.addEventListener('click', () => openQuickActionModal(btn.dataset.url, btn.dataset
                        .label));
                });
            }).catch(() => {});
        }

        function openQuickActionModal(url, label) {
            const iframe = document.getElementById('quickActionIframe');
            const loading = document.getElementById('quickActionLoading');

            document.getElementById('quickActionModalTitle').textContent = label || 'Action';

            iframe.style.display = 'none';
            loading.style.display = 'block';
            loading.textContent = 'Loading...';

            iframe.src = url;
            $('#quickActionModal').modal('show');
        }

        document.getElementById('quickActionIframe').addEventListener('load', function() {
            document.getElementById('quickActionLoading').style.display = 'none';
            this.style.display = 'block';
        });


        $('#quickActionModal').on('hidden.bs.modal', function() {
            document.getElementById('quickActionIframe').src = 'about:blank';
            loadAllSections();
        });
        /* ---------------- KPI Cards ---------------- */
        function loadKpis() {
            fetch(`${API_BASE}/kpis?filter=${currentFilter}`).then(r => r.json()).then(kpi => {
                document.getElementById('posMetrics').innerHTML = [
                    metricCard({
                        title: 'Total Sales',
                        value: kpi.total_sales_count,
                        sub: `Amount: ${fmtMoney(kpi.total_sales_amount)}`,
                        icon: 'bi-cart-check-fill',
                        theme: 'blue'
                    }),

                    metricCard({
                        title: 'Paid',
                        value: fmtMoney(kpi.received_amount),
                        sub: 'Against sales',
                        icon: 'bi-check-circle-fill',
                        theme: 'green'
                    }),
                    metricCard({
                        title: 'Customer Due',
                        value: fmtMoney(kpi.receivable_amount),
                        sub: 'Outstanding due',
                        icon: 'bi-exclamation-circle-fill',
                        theme: 'red'
                    }),
                    metricCard({
                        title: 'Total Purchase',
                        value: kpi.total_purchase_count,
                        sub: `Amount: ${fmtMoney(kpi.total_purchase_amount)}`,
                        icon: 'bi-bag-check-fill',
                        theme: 'purple'
                    }),
                    metricCard({
                        title: 'Supplier Paid',
                        value: fmtMoney(kpi.supplier_paid_amount),
                        sub: 'Paid to suppliers',
                        icon: 'bi-cash-stack',
                        theme: 'green'
                    }),

                    metricCard({
                        title: 'Supplier Due',
                        value: fmtMoney(kpi.supplier_due_amount),
                        sub: 'Payable to suppliers',
                        icon: 'bi-truck',
                        theme: 'red'
                    }),


                ].join('');
            }).catch(() => {
                document.getElementById('posMetrics').innerHTML =
                    `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load KPI data</p></div>`;
            });
        }

        /* ---------------- Sales vs Purchase Trend ---------------- */
        function loadSalesPurchaseTrend() {
            fetch(`${API_BASE}/sales-purchase-trend`).then(r => r.json()).then(res => {
                const ctx = document.getElementById('salesPurchaseChart').getContext('2d');
                if (salesPurchaseChart) salesPurchaseChart.destroy();
                salesPurchaseChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: res.labels,
                        datasets: [{
                                label: 'Sales',
                                data: res.sales,
                                backgroundColor: '#b5ebc6',
                                borderRadius: 6,
                                maxBarThickness: 26
                            },
                            {
                                label: 'Purchase',
                                data: res.purchases,
                                backgroundColor: '#ceb0ef',
                                borderRadius: 6,
                                maxBarThickness: 26
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (c) => `${c.dataset.label}: ${fmtMoney(c.raw)}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (v) => fmtMoney(v)
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }).catch(() => {});
        }

        /* ---------------- Paid vs Due ---------------- */
        function loadPaymentBreakdown() {
            fetch(`${API_BASE}/payment-breakdown?filter=${currentFilter}`).then(r => r.json()).then(res => {
                document.getElementById('paidPercentLabel').textContent = res.paid_percent + '%';
                document.getElementById('paidAmountLabel').textContent = fmtMoney(res.paid);
                document.getElementById('dueAmountLabel').textContent = fmtMoney(res.due);
                const ctx = document.getElementById('paidDueRing').getContext('2d');
                if (paidDueRingChart) paidDueRingChart.destroy();
                paidDueRingChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [res.paid, res.due],
                            backgroundColor: ['#22c55e', '#ef4444'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }).catch(() => {});
        }

        /* ---------------- Generic Detail Modal (Product / Receivable / Supplier) ---------------- */
        const detailModalState = {
            endpoint: null,
            columns: [],
            title: '',
            page: 1,
            perPage: 100,
            total: 0,
            currentPageData: []
        };

        function openDetailModal(endpoint, columns, title) {
            detailModalState.endpoint = endpoint;
            detailModalState.columns = columns;
            detailModalState.title = title;
            detailModalState.page = 1;
            document.getElementById('detailListModalTitle').textContent = title;
            document.getElementById('detailExportScope').value = 'page';
            $('#detailListModal').modal('show');
            fetchDetailPage(1);
        }

        function fetchDetailPage(page) {
            document.getElementById('detailListModalBody').innerHTML =
                `<div class="text-center text-muted py-3">Loading...</div>`;
            fetch(`${detailModalState.endpoint}&page=${page}&per_page=100`).then(r => r.json()).then(res => {
                detailModalState.page = res.page;
                detailModalState.perPage = res.per_page;
                detailModalState.total = res.total;
                detailModalState.currentPageData = res.data;
                renderDetailTable(res.data);
                renderDetailPagination();
            }).catch(() => {
                document.getElementById('detailListModalBody').innerHTML =
                    `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load details</p></div>`;
            });
        }

        function renderDetailTable(rows) {
            const cols = detailModalState.columns;
            const body = document.getElementById('detailListModalBody');
            if (!rows.length) {
                body.innerHTML = `<div class="empty-state"><i class="bi bi-inbox"></i><p>No records found</p></div>`;
                return;
            }
            body.innerHTML = `
  <div class="table-responsive">
    <table class="table table-sm" id="detailTable">
      <thead><tr>${cols.map(c => `<th>${c.label}</th>`).join('')}</tr></thead>
      <tbody>${rows.map(r => `<tr>${cols.map(c => `<td>${c.format ? c.format(r[c.key], r) : (r[c.key] ?? '-')}</td>`).join('')}</tr>`).join('')}</tbody>
    </table>
  </div>`;
        }

        function renderDetailPagination() {
            const totalPages = Math.max(1, Math.ceil(detailModalState.total / detailModalState.perPage));
            const box = document.getElementById('detailPagination');
            if (totalPages <= 1) {
                box.innerHTML = `<span class="text-muted">${detailModalState.total} record(s)</span>`;
                return;
            }
            box.innerHTML = `
  <span class="text-muted">Page ${detailModalState.page} of ${totalPages} &middot; ${detailModalState.total} record(s)</span>
  <div>
    <button class="btn btn-sm btn-outline-secondary" id="detailPrevBtn" ${detailModalState.page <= 1 ? 'disabled' : ''}>Prev</button>
    <button class="btn btn-sm btn-outline-secondary" id="detailNextBtn" ${detailModalState.page >= totalPages ? 'disabled' : ''}>Next</button>
  </div>`;
            document.getElementById('detailPrevBtn')?.addEventListener('click', () => fetchDetailPage(detailModalState
                .page - 1));
            document.getElementById('detailNextBtn')?.addEventListener('click', () => fetchDetailPage(detailModalState
                .page + 1));
        }

        async function getExportRows() {
            const scope = document.getElementById('detailExportScope').value;
            if (scope === 'page') return detailModalState.currentPageData;
            const res = await fetch(`${detailModalState.endpoint}&per_page=all`).then(r => r.json());
            return res.data;
        }

        document.getElementById('detailExportBtn').addEventListener('click', async () => {
            const rows = await getExportRows();
            if (!rows.length) return;
            const cols = detailModalState.columns;
            const sheetData = rows.map(r => {
                const o = {};
                cols.forEach(c => {
                    o[c.label] = c.format ? c.format(r[c.key], r).toString().replace(/<[^>]+>/g,
                        '') : (r[c.key] ?? '');
                });
                return o;
            });
            const ws = XLSX.utils.json_to_sheet(sheetData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Data');
            XLSX.writeFile(wb, `${detailModalState.title.replace(/\s+/g, '_')}.xlsx`);
        });

        document.getElementById('detailPrintBtn').addEventListener('click', async () => {
            const rows = await getExportRows();
            const cols = detailModalState.columns;
            const html = `
  <html><head><title>${detailModalState.title}</title>
  <style>body{font-family:Arial,sans-serif;padding:20px} table{width:100%;border-collapse:collapse} th,td{border:1px solid #ccc;padding:6px 8px;font-size:12px;text-align:left} th{background:#f1f5f9}</style>
  </head><body>
  <h3>${detailModalState.title}</h3>
  <table><thead><tr>${cols.map(c => `<th>${c.label}</th>`).join('')}</tr></thead>
  <tbody>${rows.map(r => `<tr>${cols.map(c => `<td>${c.format ? c.format(r[c.key], r) : (r[c.key] ?? '-')}</td>`).join('')}</tr>`).join('')}</tbody>
  </table></body></html>`;
            const w = window.open('', '_blank');
            w.document.write(html);
            w.document.close();
            w.focus();
            setTimeout(() => w.print(), 300);
        });

        /* ---------------- Top Products ---------------- */
        function loadTopProducts() {
            fetch(`${API_BASE}/top-products?filter=${currentFilter}`).then(r => r.json()).then(data => {
                const box = document.getElementById('topProductsList');
                box.innerHTML = data.length ? data.map(p => `
  <div class="person-row clickable-row" data-product-id="${p.id}" data-product-name="${p.name}">
    <div><div class="person-name">${p.name}</div><div class="person-sub">Qty: ${p.total_qty}</div></div>
    <div class="text-muted small">${fmtMoney(p.total_amount)}</div>
  </div>`).join('') : `<div class="empty-state"><i class="bi bi-box-seam"></i><p>No sales in this period</p></div>`;

                document.querySelectorAll('#topProductsList .clickable-row').forEach(row => {
                    row.addEventListener('click', () => {
                        const endpoint =
                            `${API_BASE}/product-invoices/${row.dataset.productId}?filter=${currentFilter}`;
                        openDetailModal(endpoint, [{
                                key: 'invoice_no',
                                label: 'Invoice No'
                            },
                            {
                                key: 'created_at',
                                label: 'Date',
                                format: (v) => fmtDateTime(v)
                            },
                            {
                                key: 'qty',
                                label: 'Qty'
                            },
                            {
                                key: 'price',
                                label: 'Rate',
                                format: (v) => fmtMoney(v)
                            },
                            {
                                key: 'line_total',
                                label: 'Total',
                                format: (v) => fmtMoney(v)
                            },
                            {
                                key: 'print_url',
                                label: '',
                                format: (v) => v ?
                                    `<a href="${v}" target="_blank"><i class="bi bi-printer"></i></a>` :
                                    ''
                            }
                        ], `${row.dataset.productName} -- Sale Invoices`);
                    });
                });
            }).catch(() => {
                document.getElementById('topProductsList').innerHTML =
                    `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load products</p></div>`;
            });
        }


        /* ---------------- Recent Purchases & Sales (search + infinite scroll + iframe modal) ---------------- */
        const recentTxnState = {
            page: 1,
            perPage: 15,
            hasMore: true,
            loading: false,
            search: ''
        };

        function resetRecentTxnState() {
            recentTxnState.page = 1;
            recentTxnState.hasMore = true;
            recentTxnState.loading = false;
        }

        function renderTxnRow(t) {
            return `
  <div class="person-row clickable-row" data-print-url="${t.print_url ?? ''}" data-invoice-no="${t.invoice_no}" data-type="${t.type}">
    <div>
      <div class="person-name">
        <span class="badge ${t.type === 'purchase' ? 'badge-warning' : 'badge-primary'}" style="margin-right:6px">${t.type === 'purchase' ? 'Purchase' : 'Sale'}</span>
        ${t.invoice_no}
      </div>
      <div class="person-sub">${t.party_name} &middot; ${fmtDateTime(t.created_at)}</div>
    </div>
    <div class="text-right"><div class="text-muted small mb-1">${fmtMoney(t.grand_total)}</div><span class="badge ${t.payment_status === 'Paid' ? 'badge-success' : 'badge-danger'}">${t.payment_status}</span></div>
  </div>`;
        }

        function bindTxnRowClicks(container) {
            container.querySelectorAll('.clickable-row').forEach(row => {
                row.addEventListener('click', () => openTxnInvoiceModal(row.dataset.printUrl, row.dataset.invoiceNo,
                    row.dataset.type));
            });
        }

        function loadRecentPurchaseSales(append = false) {
            if (recentTxnState.loading) return;
            if (append && !recentTxnState.hasMore) return;
            recentTxnState.loading = true;

            const box = document.getElementById('recentTransactionsList');

            if (!append) {
                box.innerHTML = `<div class="text-center text-muted py-3">Loading...</div>`;
            } else {
                box.insertAdjacentHTML('beforeend',
                    `<div class="text-center text-muted py-2 recent-txn-loading-more">Loading more...</div>`);
            }

            const params = new URLSearchParams({
                filter: currentFilter,
                page: recentTxnState.page,
                per_page: recentTxnState.perPage
            });
            if (recentTxnState.search) params.append('search', recentTxnState.search);

            fetch(`${API_BASE}/recent-purchase-sales?${params.toString()}`).then(r => r.json()).then(res => {
                document.querySelector('.recent-txn-loading-more')?.remove();
                if (!append) box.innerHTML = '';

                if (!res.data.length && !append) {
                    box.innerHTML =
                        `<div class="empty-state"><i class="bi bi-receipt"></i><p>No transactions found</p></div>`;
                } else {
                    box.insertAdjacentHTML('beforeend', res.data.map(renderTxnRow).join(''));
                    bindTxnRowClicks(box);
                }

                recentTxnState.hasMore = res.has_more;
                recentTxnState.page += 1;
                recentTxnState.loading = false;
            }).catch(() => {
                document.querySelector('.recent-txn-loading-more')?.remove();
                if (!append) {
                    box.innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load transactions</p></div>`;
                }
                recentTxnState.loading = false;
            });
        }


        document.getElementById('recentTransactionsList').addEventListener('scroll', function() {
            const nearBottom = this.scrollTop + this.clientHeight >= this.scrollHeight - 40;
            if (nearBottom && recentTxnState.hasMore && !recentTxnState.loading) {
                loadRecentPurchaseSales(true);
            }
        });


        document.getElementById('recentTxnSearch').addEventListener('input', debounce((e) => {
            recentTxnState.search = e.target.value.trim();
            resetRecentTxnState();
            loadRecentPurchaseSales(false);
        }, 400));

        function openTxnInvoiceModal(printUrl, invoiceNo, type) {
            const iframe = document.getElementById('txnInvoiceIframe');
            const loading = document.getElementById('txnInvoiceLoading');

            document.getElementById('txnInvoiceModalTitle').textContent =
                `${type === 'purchase' ? 'Purchase' : 'Sale'} Invoice -- ${invoiceNo || ''}`;

            iframe.style.display = 'none';
            loading.style.display = 'block';

            if (!printUrl) {
                loading.textContent = 'Invoice print link not available';
                iframe.src = 'about:blank';
                $('#txnInvoiceModal').modal('show');
                return;
            }

            loading.textContent = 'Loading...';
            iframe.src = printUrl;
            $('#txnInvoiceModal').modal('show');
        }

        document.getElementById('txnInvoiceIframe').addEventListener('load', function() {
            document.getElementById('txnInvoiceLoading').style.display = 'none';
            this.style.display = 'block';
        });

        $('#txnInvoiceModal').on('hidden.bs.modal', function() {
            document.getElementById('txnInvoiceIframe').src = 'about:blank';
        });


        function openSaleInvoiceModal(saleId) {
            document.getElementById('saleInvoiceModalBody').innerHTML =
                `<div class="text-center text-muted py-3">Loading...</div>`;
            document.getElementById('saleInvoiceModalFooter').innerHTML = '';
            $('#saleInvoiceModal').modal('show');

            fetch(`${API_BASE}/sale-invoice/${saleId}`).then(r => r.json()).then(res => {
                const s = res.sale;
                document.getElementById('saleInvoiceModalBody').innerHTML = `
  <div class="d-flex justify-content-between mb-3">
    <div><strong>${s.invoice_no}</strong><div class="text-muted small">${fmtDateTime(s.created_at)}</div></div>
    <div class="text-right">
      <div>${res.customer_name}</div>
      <span class="badge ${res.payment_status === 'Paid' ? 'badge-success' : 'badge-danger'}">${res.payment_status}</span>
    </div>
  </div>
  <table class="table table-sm">
    <thead><tr><th>Product</th><th>Qty</th><th>Rate</th><th>Total</th></tr></thead>
    <tbody>
      ${res.items.map(i => `<tr><td>${i.product_name ?? '-'}</td><td>${i.qty}</td><td>${fmtMoney(i.price)}</td><td>${fmtMoney(i.line_total)}</td></tr>`).join('')}
    </tbody>
    <tfoot><tr><th colspan="3" class="text-right">Grand Total</th><th>${fmtMoney(s.grand_total)}</th></tr></tfoot>
  </table>`;

                document.getElementById('saleInvoiceModalFooter').innerHTML = res.print_url ?
                    `
  <a href="${res.print_url}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer"></i> Print</a>
  <a href="${res.print_url}" target="_blank" class="btn btn-sm btn-primary"><i class="bi bi-download"></i> Download</a>` :
                    '';
            }).catch(() => {
                document.getElementById('saleInvoiceModalBody').innerHTML =
                    `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load invoice</p></div>`;
            });
        }

        /* ---------------- Product Consumption (search + code) ---------------- */
        function loadProductConsumption() {
            const projectId = document.getElementById('consumptionProjectFilter').value;
            const productId = document.getElementById('consumptionProductFilter').value;
            const search = document.getElementById('consumptionSearch').value.trim();
            const params = new URLSearchParams({
                filter: currentFilter
            });
            if (projectId) params.append('project_id', projectId);
            if (productId) params.append('product_id', productId);
            if (search) params.append('search', search);

            fetch(`${API_BASE}/product-consumption?${params.toString()}`).then(r => r.json()).then(data => {
                const box = document.getElementById('productConsumptionList');
                box.innerHTML = data.length ? `
                    <div class="table-responsive">
                 <table class="table table-sm mb-0">
                      <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Project</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Amount</th>
                         </tr>
                    </thead>
                <tbody>
                        ${data.map(r => `
                                                                                                                                                                                                                                 <tr>
                                                                                                                                                                                                                                <td>${r.invoice_no ?? '-'}</td>
                                                                                                                                                                                                                                  <td>${r.project_name ?? '-'}</td>
                                                                                                                                                                                                                                  <td>${r.product_code ?? ''} - ${r.product_name ?? '-'}</td>
                                                                                                                                                                                                                                  <td>${r.total_qty}</td>
                                                                                                                                                                                                                                  <td>${fmtMoney(r.total_amount)}</td>
                                                                                                                                                                                                                            </tr>`
                  ).join('')}
    </tbody>
  </table>
  </div>` : `<div class="empty-state"><i class="bi bi-boxes"></i><p>No consumption data found</p></div>`;
            }).catch(() => {
                document.getElementById('productConsumptionList').innerHTML =
                    `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load consumption data</p></div>`;
            });
        }

        function loadConsumptionFilters() {
            fetch(`${API_BASE}/project-options`).then(r => r.json()).then(projects => {
                const select = document.getElementById('consumptionProjectFilter');
                projects.forEach(p => select.insertAdjacentHTML('beforeend',
                    `<option value="${p.id}">${p.name}</option>`));
            }).catch(() => {});

            fetch(`${API_BASE}/product-options`).then(r => r.json()).then(products => {
                const select = document.getElementById('consumptionProductFilter');
                products.forEach(p => select.insertAdjacentHTML('beforeend',
                    `<option value="${p.id}">${p.name}${p.code ? ' (' + p.code + ')' : ''}</option>`));
            }).catch(() => {});
        }

        document.getElementById('consumptionProjectFilter').addEventListener('change', loadProductConsumption);
        document.getElementById('consumptionProductFilter').addEventListener('change', loadProductConsumption);
        document.getElementById('consumptionSearch').addEventListener('input', debounce(loadProductConsumption, 400));

        /* ---------------- Top Seller (Sales Performance) ---------------- */
        function loadSalesPerformance() {
            fetch(`${API_BASE}/sales-performance?filter=${currentFilter}`).then(r => r.json()).then(data => {
                const box = document.getElementById('salesPerformanceList');
                box.innerHTML = data.length ? data.map((c, i) => `
  <div class="person-row">
    <div class="d-flex align-items-center">
      <span class="rank-badge ${i === 0 ? 'rank-1' : i === 1 ? 'rank-2' : i === 2 ? 'rank-3' : 'rank-other'}">${i + 1}</span>
      <div class="person-name">${c.name}</div>
    </div>
    <div class="text-right"><div class="small text-muted">${c.txn_count} sales</div><div class="small">${fmtMoney(c.total_sales)}</div></div>
  </div>`).join('') : `<div class="empty-state"><i class="bi bi-trophy"></i><p>No performance data yet</p></div>`;
            }).catch(() => {
                document.getElementById('salesPerformanceList').innerHTML =
                    `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load data</p></div>`;
            });
        }

        /* ---------------- Top Receivables (click -> modal) ---------------- */
        function loadTopReceivables() {
            fetch(`${API_BASE}/top-receivables?filter=${currentFilter}`).then(r => r.json()).then(data => {
                const box = document.getElementById('topReceivablesList');
                box.innerHTML = data.length ? data.map(c => `
  <div class="person-row clickable-row" data-account-id="${c.id}" data-account-name="${c.name}">
    <div class="person-name">${c.name}</div>
    <div class="text-danger small">${fmtMoney(c.due_amount)}</div>
  </div>`).join('') : `<div class="empty-state"><i class="bi bi-check-circle"></i><p>No receivables found</p></div>`;

                document.querySelectorAll('#topReceivablesList .clickable-row').forEach(row => {
                    row.addEventListener('click', () => {
                        const endpoint =
                            `${API_BASE}/receivable-invoices/${row.dataset.accountId}?filter=${currentFilter}`;
                        openDetailModal(endpoint, [{
                                key: 'invoice',
                                label: 'Invoice No'
                            },
                            {
                                key: 'created_at',
                                label: 'Date',
                                format: (v) => fmtDateTime(v)
                            },
                            {
                                key: 'debit',
                                label: 'Due Amount',
                                format: (v) => fmtMoney(v)
                            }
                        ], `${row.dataset.accountName} -- Due Invoices`);
                    });
                });
            }).catch(() => {
                document.getElementById('topReceivablesList').innerHTML =
                    `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load data</p></div>`;
            });
        }

        /* ---------------- Supplier Due (click -> modal) ---------------- */
        function loadSupplierDue() {
            fetch(`${API_BASE}/supplier-due?filter=${currentFilter}`).then(r => r.json()).then(data => {
                const box = document.getElementById('supplierDueList');
                box.innerHTML = data.length ? data.map(s => `
  <div class="person-row clickable-row" data-supplier-id="${s.id}" data-supplier-name="${s.name}">
    <div class="person-name">${s.name}</div>
    <div class="text-warning small">${fmtMoney(s.due_amount)}</div>
  </div>`).join('') : `<div class="empty-state"><i class="bi bi-check-circle"></i><p>No supplier dues found</p></div>`;

                document.querySelectorAll('#supplierDueList .clickable-row').forEach(row => {
                    row.addEventListener('click', () => {
                        const endpoint =
                            `${API_BASE}/supplier-due-invoices/${row.dataset.supplierId}?filter=${currentFilter}`;
                        openDetailModal(endpoint, [{
                                key: 'invoice',
                                label: 'Voucher No'
                            },
                            {
                                key: 'created_at',
                                label: 'Date',
                                format: (v) => fmtDateTime(v)
                            },
                            {
                                key: 'credit',
                                label: 'Due Amount',
                                format: (v) => fmtMoney(v)
                            }
                        ], `${row.dataset.supplierName} -- Payable Vouchers`);
                    });
                });
            }).catch(() => {
                document.getElementById('supplierDueList').innerHTML =
                    `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load data</p></div>`;
            });
        }

        /* ---------------- Load All ---------------- */
        function loadAllSections() {
            loadKpis();
            loadPaymentBreakdown();
            loadTopProducts();
            resetRecentTxnState();
            loadRecentPurchaseSales(false);
            loadProductConsumption();
            loadSalesPerformance();
            loadTopReceivables();
            loadSupplierDue();
        }

        document.querySelectorAll('.pos-tab').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.pos-tab').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.filter;
                loadAllSections();
            });
        });

        loadQuickActions();
        loadSalesPurchaseTrend();
        loadConsumptionFilters();
        loadAllSections();
    </script>
@endsection
