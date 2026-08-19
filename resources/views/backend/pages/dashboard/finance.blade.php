@extends('backend.layouts.master')

@section('title')
    {{ $pgTitle ?? 'Financial' }} - Admin Panel
@endsection

<!-- Common Dashboard CSS (shared by all department dashboards) -->
<link rel="stylesheet"
    href="{{ asset('css/dashboard-style.css') }}?v={{ filemtime(public_path('css/dashboard-style.css')) }}">
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $pgTitle ?? 'Financial' }}</h1>
                    <p class="text-muted mb-0" style="font-size:.85rem">Manage finances, transactions, and invoices</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a href="#">Financial</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="dashboard-wrap fin-wrap">

        {{-- ============ Tabs + Export ============ --}}
        <div class="fin-tabs-row">
            <div class="fin-tabs" id="finTabs">
                <button type="button" class="fin-tab active" data-tab="overview">Overview</button>
                <button type="button" class="fin-tab" data-tab="transactions">Transactions</button>
                <button type="button" class="fin-tab" data-tab="invoices">Invoices</button>
            </div>
            <button type="button" class="fin-export-btn" id="btnExport">
                <i class="bi bi-download"></i> Export
            </button>
        </div>

        {{-- ============ TAB: Overview ============ --}}
        <div class="fin-tab-content" id="tabOverview">

            {{-- ============ Point 1: KPI Cards (clickable -> drill-down modal) ============ --}}
            <div class="row g-3 mb-3" id="finKpis"></div>

            {{-- ============ Cash Flow Analysis + Expense Breakdown ============ --}}
            <div class="row g-3 mb-3">
                <div class="col-lg-8">
                    <div class="panel h-100 fin-panel">
                        <div class="panel-header fin-panel-header">
                            <span class="fin-panel-title">Cash Flow Analysis</span>
                            <div class="fin-cf-legend" id="cfLegend"></div>
                            <select class="form-control form-control-sm fin-range-select" id="cfRange" style="width:auto">
                                <option value="this_year" selected>This Year</option>
                                <option value="last_year">Last Year</option>
                                <option value="last_6_months">Last 6 Months</option>
                            </select>
                        </div>
                        <div class="panel-body">
                            <div style="height:300px">
                                <canvas id="cashFlowChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="panel h-100 fin-panel">
                        <div class="panel-header fin-panel-header">
                            <span class="fin-panel-title">Expense Breakdown</span>
                        </div>
                        <div class="panel-body d-flex flex-column align-items-center" style="padding:20px 22px">
                            <div class="concentric-wrap">
                                <div id="concentricChart"></div>
                            </div>
                            <div class="concentric-total">
                                <div class="fin-donut-center-label">Total Expenses</div>
                                <div class="fin-donut-center-value" id="donutCenterValue">৳0</div>
                            </div>
                            <div class="fin-legend-grid" id="expenseInnerLegend" style="display:none"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ Revenue Comparison ============ --}}
            <div class="col-12">
                <div class="panel fin-panel">
                    <div class="panel-header fin-panel-header">
                        <span class="fin-panel-title">Revenue Comparison</span>
                        <div class="fin-cf-legend" id="revLegend"></div>
                    </div>
                    <div class="panel-body">
                        <div class="rev-chart-wrap" id="revChartWrap">
                            <div class="rev-y-axis" id="revYAxis"></div>
                            <div class="rev-bars-area" id="revBarsArea"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        {{-- ============ /TAB: Overview ============ --}}

        {{-- ============ TAB: Transactions ============ --}}
        <div class="fin-tab-content" id="tabTransactions" style="display:none">
            <div class="panel fin-panel">
                <div class="panel-header fin-panel-header">
                    <span class="fin-panel-title">All Transactions</span>
                </div>
                <div class="panel-body">
                    <div class="txn-filter-bar">
                        <input type="text" class="form-control form-control-sm" id="txnSearch"
                            placeholder="Search by voucher, remark...">
                        <select class="form-control form-control-sm" id="txnTypeFilter" style="max-width:160px">
                            <option value="">All Types</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div class="txn-table-header">
                        <span></span>
                        <span>Description</span>
                        <span>Voucher</span>
                        <span>Amount</span>
                        <span style="text-align:right">Date</span>
                    </div>
                    <div id="txnList"></div>
                </div>
            </div>
        </div>
        {{-- ============ /TAB: Transactions ============ --}}

        {{-- ============ TAB: Invoices ============ --}}
        <div class="fin-tab-content" id="tabInvoices" style="display:none">
            <div class="panel fin-panel">
                <div class="panel-header fin-panel-header">
                    <span class="fin-panel-title">All Invoices</span>
                </div>
                <div class="panel-body">
                    <div class="txn-filter-bar">
                        <input type="text" class="form-control form-control-sm" id="invSearch"
                            placeholder="Search by client or invoice no...">
                        <select class="form-control form-control-sm" id="invStatusFilter" style="max-width:160px">
                            <option value="">All Status</option>
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                    <div id="invoiceList"></div>
                </div>
            </div>
        </div>
        {{-- ============ /TAB: Invoices ============ --}}

    </div>

    {{-- ============ KPI Detail Modal -- KPI card click e open hoy ============ --}}
    <div class="modal fade" id="kpiDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex  ">
                    <h5 class="modal-title" id="kpiDetailModalTitle">Details</h5>
                    <div class="d-flex gap-2">

                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                id="btnKpiExcelDropdown" data-toggle="dropdown">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" id="kpiExcelMenu">
                                <a class="dropdown-item" href="#" data-scope="current">Current Page</a>
                                <a class="dropdown-item" href="#" data-scope="all">Full Data</a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-success dropdown-toggle" type="button"
                                id="btnKpiPrintDropdown" data-toggle="dropdown">
                                <i class="bi bi-printer"></i> Print
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" id="kpiPrintMenu">
                                <a class="dropdown-item" href="#" data-scope="current">Current Page</a>
                                <a class="dropdown-item" href="#" data-scope="all">Full Data</a>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="kpiDetailModalBody">
                    <div class="text-center text-muted py-3">Loading...</div>
                </div>
                <div class="modal-footer kpi-modal-footer" id="kpiDetailModalFooter" style="display:none">
                    <div class="kpi-modal-pagination" id="kpiModalPagination"></div>

                </div>
            </div>
        </div>
    </div>

    <style>
        /* ==========================================================================
                                                                                                                                                                                                                                                                                                                                                                                                                           Financial Dashboard specific styles.
                                                                                                                                                                                                                                                                                                                                                                                                                           ========================================================================== */
        .fin-wrap {
            --fin-green: #10b981;
            --fin-green-dark: #059669;
            --fin-red: #ef4444;
            --fin-amber: #f59e0b;
            --fin-purple: #7c3aed;
        }

        /* ---- Tabs row ---- */
        .fin-tabs-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
        }

        .fin-tabs {
            display: inline-flex;
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 999px;
            padding: 4px;
            gap: 2px;
        }

        .fin-tab {
            border: none;
            background: transparent;
            padding: 7px 18px;
            border-radius: 999px;
            font-size: .85rem;
            font-weight: 600;
            color: var(--gray-600);
            transition: .15s;
        }

        .fin-tab:hover {
            color: var(--gray-900);
        }

        .fin-tab.active {
            background: var(--emerald-100);
            color: var(--emerald-700);
        }

        .fin-export-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 10px;
            padding: 8px 16px;
            font-size: .85rem;
            font-weight: 600;
            color: var(--gray-700);
            transition: .15s;
        }

        .fin-export-btn:hover {
            background: var(--gray-50);
            border-color: var(--gray-500);
        }

        /* ---- KPI cards (flat white, image-style, now clickable) ---- */
        .fin-kpi-card {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 14px;
            padding: 18px 20px;
            height: 100%;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .fin-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px -10px rgba(0, 0, 0, .18);
            border-color: var(--gray-500);
        }

        .fin-kpi-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .fin-kpi-label {
            font-size: .8rem;
            font-weight: 600;
            color: var(--gray-500);
        }

        .fin-kpi-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .fin-kpi-value {
            font-size: clamp(15px, 2vw, 28px);
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
        }

        .fin-kpi-trend {
            font-size: .78rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .fin-kpi-trend.up {
            color: var(--fin-green-dark);
        }

        .fin-kpi-trend.down {
            color: var(--fin-red);
        }

        .fin-kpi-trend.neutral {
            color: var(--fin-red);
        }

        /* ---- Panel header (title + legend + dropdown in one row) ---- */
        .fin-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .fin-panel-title {
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--gray-900);
        }

        .fin-cf-legend {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: .78rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .fin-cf-legend span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .fin-cf-legend .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .fin-range-select {
            border-radius: 8px;
            font-size: .8rem;
            font-weight: 600;
        }





        /* ---- Concentric Expense Rings ---- */
        .concentric-wrap {
            position: relative;
            width: 100%;
            max-width: 260px;
            margin: 10px auto -6px;

        }

        .concentric-svg {
            width: 100%;
            height: auto;
            display: block;
            overflow: visible;
        }

        .concentric-arc {
            opacity: 0;
            animation: concentricFadeIn .55s ease forwards;
        }

        .concentric-label {
            opacity: 0;
            animation: concentricFadeIn .45s ease forwards;
            font-size: 10px;
            font-weight: 700;
            fill: var(--gray-700, #374151);
            /* dark, readable -- ar arc rong er sathe mixe jabe na */
            letter-spacing: .2px;
        }

        .concentric-inband-label {
            opacity: 0;
            animation: concentricFadeIn .5s ease forwards;
            font-size: 9.5px;
            font-weight: 700;
            fill: #ffffff;

            letter-spacing: .3px;
        }

        .concentric-badge {
            opacity: 0;
            animation: concentricFadeIn .4s ease forwards;
        }

        @keyframes concentricFadeIn {
            from {
                opacity: 0;
                transform: scale(.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .concentric-total {
            text-align: center;
            margin-top: -8px;
            margin-bottom: 4px;
        }

        /* Responsive -- choto screen e font aro chotoo hobe */
        @media (max-width: 480px) {
            .concentric-label {
                font-size: 8.5px;
            }
        }


        @media (max-width: 480px) {
            .fin-tabs-row {
                flex-direction: column;
                align-items: stretch;
            }

            .concentric-inband-label {
                font-size: 8px;
            }

            .fin-export-btn {
                justify-content: center;
            }
        }

        /* ---- Tab content fade-in ---- */
        .fin-tab-content {
            animation: finTabFade .2s ease;
        }

        @keyframes finTabFade {
            from {
                opacity: 0;
                transform: translateY(4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ---- Invoice status badge ---- */
        .status-paid {
            background: #dcfce7;
            color: #166534;
        }

        .status-overdue {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ---- KPI Detail Modal ---- */
        .kpi-detail-table th,
        .kpi-detail-table td {
            font-size: .82rem;
            vertical-align: middle;
        }

        .kpi-modal-footer {
            flex-wrap: wrap;
            gap: 10px;
        }

        .kpi-modal-pagination {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: .8rem;
        }

        /* ---- Revenue Comparison (custom bar chart) ---- */
        .rev-chart-wrap {
            position: relative;
            display: flex;
            height: 320px;
            padding-top: 10px;
        }

        .rev-y-axis {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-bottom: 28px;
            padding-right: 14px;
            font-size: .78rem;
            color: var(--gray-500, #6b7280);
            font-weight: 500;
            text-align: right;
            min-width: 48px;
        }

        .rev-bars-area {
            position: relative;
            flex: 1;
            display: flex;
            align-items: flex-end;
            gap: 10px;
            padding-bottom: 28px;
            border-bottom: 1px solid var(--gray-200, #e5e7eb);
        }

        .rev-bars-area::before {
            content: '';
            position: absolute;
            inset: 0 0 28px 0;
            background-image: repeating-linear-gradient(to top,
                    var(--gray-200, #e5e7eb) 0,
                    var(--gray-200, #e5e7eb) 1px,
                    transparent 1px,
                    transparent 25%);
            pointer-events: none;
            opacity: .6;
        }

        .rev-bar-col {
            position: relative;
            flex: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            cursor: pointer;
        }

        .rev-bar-track {
            position: relative;
            width: 62%;
            max-width: 42px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .rev-bar-ghost {
            width: 100%;
            height: 88%;
            background: var(--gray-100, #f1f3f5);
            border-radius: 8px 8px 0 0;
            border-top: 2px dashed var(--gray-300, #d1d5db);
            transition: opacity .2s ease;
        }

        .rev-bar-split {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            display: none;
            flex-direction: column;
            justify-content: flex-end;
        }

        .rev-bar-seg-current {
            width: 100%;
            background: var(--fin-green-dark, #059669);
            border-radius: 8px 8px 0 0;
            transition: height .25s ease;
        }

        .rev-bar-seg-last {
            width: 100%;
            background: #a7d9c5;
            transition: height .25s ease;
        }

        .rev-bar-col.active .rev-bar-ghost {
            opacity: 0;
        }

        .rev-bar-col.active .rev-bar-split {
            display: flex;
        }

        .rev-bar-month {
            margin-top: 8px;
            font-size: .78rem;
            color: var(--gray-500, #6b7280);
            font-weight: 500;
        }

        .rev-bar-col.active .rev-bar-month {
            color: var(--gray-900, #111827);
            font-weight: 700;
        }

        /* ---- Tooltip card ---- */
        .rev-tooltip {
            position: absolute;
            bottom: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            border: 1px solid var(--gray-200, #e5e7eb);
            border-radius: 10px;
            padding: 12px 16px;
            box-shadow: 0 10px 24px -8px rgba(0, 0, 0, .18);
            white-space: nowrap;
            z-index: 5;
            display: none;
            pointer-events: none;
        }

        .rev-bar-col.active .rev-tooltip {
            display: block;
        }

        .rev-tooltip-title {
            font-weight: 700;
            font-size: .85rem;
            color: var(--gray-900, #111827);
            margin-bottom: 8px;
        }

        .rev-tooltip-row {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: .8rem;
            color: var(--gray-600, #4b5563);
            margin-bottom: 4px;
        }

        .rev-tooltip-row:last-child {
            margin-bottom: 0;
        }

        .rev-tooltip-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .rev-tooltip-row b {
            color: var(--gray-900, #111827);
            font-weight: 700;
        }

        @media (max-width: 640px) {
            .rev-bars-area {
                gap: 4px;
            }

            .rev-bar-month {
                font-size: .68rem;
            }

            .rev-tooltip {
                padding: 9px 12px;
            }

            .rev-tooltip-title {
                font-size: .78rem;
            }

            .rev-tooltip-row {
                font-size: .72rem;
            }
        }

        /* Past month -- permanently colored, hover lagbe na */
        .rev- -col.is-past .rev-bar-ghost {
            opacity: 0;
        }

        .rev-bar-col.is-past .rev-bar-split {
            display: flex;
        }

        .rev-bar-col.is-past .rev-bar-month {
            color: var(--gray-900, #111827);
            font-weight: 700;
        }

        /* Past month-e tooltip default e hidden thakbe, shudhu hover korle dekhabe (chaile) */
        .rev-bar-col.is-past .rev-tooltip {
            display: none;
        }

        .rev-bar-col.is-past:hover .rev-tooltip {
            display: block;
        }
    </style>

    <script>
        if (typeof Chart !== 'undefined') {
            Chart.defaults.plugins.legend.display = false;
        }

        const API_BASE = '/api/financial-dashboard';

        // Charts/lists ekbar render howar por abar rebuild na kore, shudhu
        // panel show/hide kora hoy -- performance o smooth switching er jonno
        let overviewChartsRendered = false;
        let transactionsRendered = false;
        let invoicesRendered = false;

        /* ---------------- Tabs -- switches which panel is visible ---------------- */
        const tabPanels = {
            overview: document.getElementById('tabOverview'),
            transactions: document.getElementById('tabTransactions'),
            invoices: document.getElementById('tabInvoices')
        };

        function activateTab(tabKey) {
            document.querySelectorAll('.fin-tab').forEach(b => {
                b.classList.toggle('active', b.dataset.tab === tabKey);
            });

            Object.keys(tabPanels).forEach(key => {
                tabPanels[key].style.display = key === tabKey ? '' : 'none';
            });

            if (tabKey === 'overview' && !overviewChartsRendered) {
                loadOverviewCharts();
                overviewChartsRendered = true;
            } else if (tabKey === 'overview') {
                cashFlowChartInstance && cashFlowChartInstance.resize();
                donutChartInstance && donutChartInstance.resize();
                revenueChartInstance && revenueChartInstance.resize();
            }

            if (tabKey === 'transactions' && !transactionsRendered) {
                loadTransactions();
                transactionsRendered = true;
            }

            if (tabKey === 'invoices' && !invoicesRendered) {
                loadInvoices();
                invoicesRendered = true;
            }
        }

        document.querySelectorAll('.fin-tab').forEach(btn => {
            btn.addEventListener('click', function() {
                activateTab(this.dataset.tab);
            });
        });

        document.getElementById('btnExport').addEventListener('click', function() {
            alert('Export coming soon');
        });

        /* ---------------- Point 1: KPI Cards (clickable) ---------------- */
        function kpiCard({
            label,
            value,
            trend,
            trendDir,
            icon,
            iconBg,
            iconColor,
            type
        }) {
            const trendIcon = trendDir === 'up' ? 'bi-graph-up-arrow' : trendDir === 'down' ? 'bi-graph-down-arrow' :
                'bi-exclamation-circle';
            return `
  <div class="col-6 col-lg-3">
    <div class="fin-kpi-card kpi-clickable" data-kpi-type="${type}">
      <div class="fin-kpi-top">
        <div class="fin-kpi-label">${label}</div>
        <div class="fin-kpi-icon" style="background:${iconBg};color:${iconColor}"><i class="bi ${icon}"></i></div>
      </div>
      <div class="fin-kpi-value">${value}</div>
      <div class="fin-kpi-trend ${trendDir}"><i class="bi ${trendIcon}"></i> ${trend}</div>
    </div>
  </div>`;
        }

        function renderKpis(kpi) {
            document.getElementById('finKpis').innerHTML = [
                kpiCard({
                    label: 'Total Income (This Month)',
                    value: '৳' + Number(kpi.total_income).toLocaleString(),
                    trend: (kpi.income_change >= 0 ? '+' : '') + kpi.income_change + '%',
                    trendDir: kpi.income_change >= 0 ? 'up' : 'down',
                    icon: 'bi-currency-dollar',
                    iconBg: '#eff6ff',
                    iconColor: '#2563eb',
                    type: 'total_income'
                }),
                kpiCard({
                    label: 'Total Expenses (This Month)',
                    value: '৳' + Number(kpi.total_expenses).toLocaleString(),
                    trend: (kpi.expenses_change >= 0 ? '+' : '') + kpi.expenses_change + '%',
                    trendDir: kpi.expenses_change >= 0 ? 'down' : 'up',
                    icon: 'bi-graph-up-arrow',
                    iconBg: '#f3e8ff',
                    iconColor: '#9333ea',
                    type: 'total_expenses'
                }),
                kpiCard({
                    label: 'Net Profit (This Month)',
                    value: '৳' + Number(kpi.net_profit).toLocaleString(),
                    trend: (kpi.net_profit_change >= 0 ? '+' : '') + kpi.net_profit_change + '%',
                    trendDir: kpi.net_profit_change >= 0 ? 'up' : 'down',
                    icon: 'bi-piggy-bank',
                    iconBg: '#f0fdf4',
                    iconColor: '#16a34a',
                    type: 'net_profit'
                }),
                kpiCard({
                    label: 'Pending Payments',
                    value: '৳' + Number(kpi.pending_payments).toLocaleString(),
                    trend: kpi.overdue_count + ' Overdue',
                    trendDir: 'neutral',
                    icon: 'bi-hourglass-split',
                    iconBg: '#fef2f2',
                    iconColor: '#dc2626',
                    type: 'pending_payments'
                }),
            ].join('');

            bindKpiClicks();
        }

        fetch(`${API_BASE}/kpis`).then(r => r.json()).then(renderKpis).catch(() => {
            document.getElementById('finKpis').innerHTML =
                `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load KPI data</p></div>`;
        });

        /* ---------------- KPI Detail Modal -- pagination + Print/Excel (Current Page / Full Data) ---------------- */
        const kpiModalState = {
            type: null,
            title: '',
            page: 1,
            lastPage: 1,
            total: 0,
            currentRows: []
        };

        function bindKpiClicks() {
            document.querySelectorAll('.kpi-clickable').forEach(el => {
                el.addEventListener('click', () => {
                    const title = el.querySelector('.fin-kpi-label').textContent;
                    openKpiDetailModal(el.dataset.kpiType, title);
                });
            });
        }

        function openKpiDetailModal(type, title) {
            kpiModalState.type = type;
            kpiModalState.title = title;
            kpiModalState.page = 1;
            document.getElementById('kpiDetailModalTitle').textContent = title;
            document.getElementById('kpiDetailModalFooter').style.display = 'none';
            $('#kpiDetailModal').modal('show');
            loadKpiDetails(1);
        }

        function loadKpiDetails(page) {
            document.getElementById('kpiDetailModalBody').innerHTML =
                `<div class="text-center text-muted py-3">Loading...</div>`;

            fetch(`${API_BASE}/kpi-details?type=${kpiModalState.type}&page=${page}&per_page=100`)
                .then(r => r.json())
                .then(res => {
                    kpiModalState.page = res.current_page;
                    kpiModalState.lastPage = res.last_page;
                    kpiModalState.total = res.total;
                    kpiModalState.currentRows = res.data;

                    renderKpiDetailTable(res.data, res.total);
                    renderKpiPagination();
                    document.getElementById('kpiDetailModalFooter').style.display = 'flex';
                })
                .catch(() => {
                    document.getElementById('kpiDetailModalBody').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load details</p></div>`;
                    document.getElementById('kpiDetailModalFooter').style.display = 'none';
                });
        }

        function renderKpiDetailTable(rows, total) {
            const body = document.getElementById('kpiDetailModalBody');

            if (!rows.length) {
                body.innerHTML = `<div class="empty-state"><i class="bi bi-inbox"></i><p>No records found</p></div>`;
                return;
            }

            body.innerHTML = `
                <div class="text-muted small mb-2">${total.toLocaleString()} total record${total > 1 ? 's' : ''}</div>
                <table class="table table-sm kpi-detail-table">
                    <thead>
                        <tr>
                            <th>Voucher</th>
                            <th>Description</th>
                            <th class="text-right">Date</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows.map(r => `
                                                                                                                                                                                                                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                <td>${r.voucher ?? '-'}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                <td>${r.title}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="text-right">${r.date}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="text-right">৳${Number(r.amount).toLocaleString()}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>`).join('')}
                    </tbody>
                </table>`;
        }

        function renderKpiPagination() {
            const box = document.getElementById('kpiModalPagination');

            if (kpiModalState.lastPage <= 1) {
                box.innerHTML = '';
                return;
            }

            box.innerHTML = `
                <button type="button" class="btn btn-sm btn-outline-secondary" id="kpiPrevPage" ${kpiModalState.page <= 1 ? 'disabled' : ''}>
                    <i class="bi bi-chevron-left"></i>
                </button>
                <span class="mx-2">Page ${kpiModalState.page} / ${kpiModalState.lastPage}</span>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="kpiNextPage" ${kpiModalState.page >= kpiModalState.lastPage ? 'disabled' : ''}>
                    <i class="bi bi-chevron-right"></i>
                </button>`;

            document.getElementById('kpiPrevPage')?.addEventListener('click', () => {
                if (kpiModalState.page > 1) loadKpiDetails(kpiModalState.page - 1);
            });
            document.getElementById('kpiNextPage')?.addEventListener('click', () => {
                if (kpiModalState.page < kpiModalState.lastPage) loadKpiDetails(kpiModalState.page + 1);
            });
        }


        function exportKpiExcel(scope) {
            if (scope === 'current') {
                downloadKpiCsv(kpiModalState.currentRows);
                return;
            }

            document.getElementById('kpiDetailModalBody').insertAdjacentHTML('afterbegin',
                `<div class="text-muted small mb-2" id="kpiExportingNote">Preparing full data export...</div>`);

            fetch(`${API_BASE}/kpi-details?type=${kpiModalState.type}&all=1`)
                .then(r => r.json())
                .then(res => {
                    document.getElementById('kpiExportingNote')?.remove();
                    downloadKpiCsv(res.data);
                })
                .catch(() => {
                    document.getElementById('kpiExportingNote')?.remove();
                    alert('Failed to prepare full data export');
                });
        }

        function downloadKpiCsv(rows) {
            let csv = 'Description,Voucher,Amount,Date\n';
            rows.forEach(r => {
                csv += `"${(r.title ?? '').replace(/"/g, '""')}","${r.voucher ?? ''}",${r.amount},"${r.date}"\n`;
            });

            const blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `${kpiModalState.type}-details.csv`;
            link.click();
            URL.revokeObjectURL(link.href);
        }

        function printKpiDetails(scope) {
            if (scope === 'current') {
                openKpiPrintWindow(kpiModalState.currentRows);
                return;
            }

            fetch(`${API_BASE}/kpi-details?type=${kpiModalState.type}&all=1`)
                .then(r => r.json())
                .then(res => openKpiPrintWindow(res.data))
                .catch(() => alert('Failed to prepare full data print'));
        }

        function openKpiPrintWindow(rows) {
            const win = window.open('', '_blank');
            const html = `
                <html>
                <head>
                    <title>${kpiModalState.title}</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 24px; color: #111827; }
                        h3 { margin-bottom: 4px; }
                        .meta { color: #6b7280; font-size: 13px; margin-bottom: 16px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid #e5e7eb; padding: 6px 10px; font-size: 13px; text-align: left; }
                        th { background: #f3f4f6; }
                        td.amount, th.amount { text-align: right; }
                    </style>
                </head>
                <body>
                    <h3>${kpiModalState.title}</h3>
                    <div class="meta">${rows.length.toLocaleString()} record${rows.length > 1 ? 's' : ''} -- generated ${new Date().toLocaleDateString()}</div>
                    <table>
                        <thead>
                            <th>Voucher</th>
                            <tr><th>Description</th>
                                <th>Date</th></tr>
                            <th class="amount">Amount</th>
                        </thead>
                        <tbody>
                            ${rows.map(r => `
                                                                                                                                                                                                                                                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                    <td>${r.voucher ?? '-'}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                    <td>${r.title}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                    <td>${r.date}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                    <td class="amount">৳${Number(r.amount).toLocaleString()}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                </tr>`).join('')}
                        </tbody>
                    </table>
                </body>
                </html>`;

            win.document.write(html);
            win.document.close();
            win.focus();
            win.print();
        }

        document.querySelectorAll('#kpiExcelMenu .dropdown-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                exportKpiExcel(this.dataset.scope);
            });
        });

        document.querySelectorAll('#kpiPrintMenu .dropdown-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                printKpiDetails(this.dataset.scope);
            });
        });

        /* ---------------- Cash Flow Analysis (line chart) ---------------- */
        function renderCashFlowLegend() {
            document.getElementById('cfLegend').innerHTML = `
    <span><span class="dot" style="background:#10b981"></span> Income</span>
    <span><span class="dot" style="background:#ef4444"></span> Expenses</span>
    <span><span class="dot" style="background:#f59e0b"></span> Net</span>`;
        }

        let cashFlowChartInstance = null;

        function renderCashFlowChart(data) {
            const ctx = document.getElementById('cashFlowChart').getContext('2d');
            if (cashFlowChartInstance) cashFlowChartInstance.destroy();

            cashFlowChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                            label: 'Income',
                            data: data.income,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16,185,129,0.08)',
                            borderWidth: 2.5,
                            tension: 0.45,
                            fill: true,
                            pointRadius: 0,
                            pointHoverRadius: 5
                        },
                        {
                            label: 'Expenses',
                            data: data.expenses,
                            borderColor: '#ef4444',
                            borderDash: [4, 4],
                            borderWidth: 2,
                            tension: 0.45,
                            fill: false,
                            pointRadius: 0,
                            pointHoverRadius: 5
                        },
                        {
                            label: 'Net',
                            data: data.net,
                            borderColor: '#f59e0b',
                            borderDash: [4, 4],
                            borderWidth: 2,
                            tension: 0.45,
                            fill: false,
                            pointRadius: 0,
                            pointHoverRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#111827',
                            bodyColor: '#374151',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            padding: 12,
                            titleFont: {
                                weight: '700'
                            },
                            callbacks: {
                                label: (c) => `${c.dataset.label}: ৳${Number(c.parsed.y).toLocaleString()}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: (v) => '৳' + (v >= 1000 ? (v / 1000) + 'k' : v)
                            },
                            grid: {
                                color: '#f3f4f6'
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
        }



        /* ---------------- Expense Breakdown (concentric rings + curved labels) ---------------- */
        /* ---------------- Expense Breakdown (concentric rings + in-band text + numbered badges) ---------------- */
        function polarToCartesian(cx, cy, r, angleDeg) {
            const a = (angleDeg - 90) * Math.PI / 180;
            return {
                x: cx + r * Math.cos(a),
                y: cy + r * Math.sin(a)
            };
        }

        function describeArc(cx, cy, r, startAngle, endAngle) {
            const start = polarToCartesian(cx, cy, r, startAngle);
            const end = polarToCartesian(cx, cy, r, endAngle);
            const largeArcFlag = (endAngle - startAngle) <= 180 ? '0' : '1';
            return `M ${start.x} ${start.y} A ${r} ${r} 0 ${largeArcFlag} 1 ${end.x} ${end.y}`;
        }

        // pct (0-100) onujayi color felano -- kom expense = sobuj, beshi expense = lal
        function expenseColorScale(pct) {
            const clampedPct = Math.max(0, Math.min(100, pct));
            const hue = 48 - (clampedPct / 100) * 48;
            const lightness = 62 - (clampedPct / 100) * 20;
            const saturation = 88;

            return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
        }

        function renderExpenseConcentric(rawData) {
            const MAX_RINGS = 5;
            let data = [...rawData].sort((a, b) => b.amount - a.amount);

            if (data.length > MAX_RINGS) {
                const top = data.slice(0, MAX_RINGS - 1);
                const othersAmount = data.slice(MAX_RINGS - 1).reduce((s, d) => s + Number(d.amount), 0);
                data = [...top, {
                    label: 'Others',
                    amount: othersAmount,
                    color: '#9ca3af'
                }];
            }

            const total = rawData.reduce((s, d) => s + Number(d.amount), 0);
            document.getElementById('donutCenterValue').textContent = '৳' + total.toLocaleString();

            // Prottek category-r percentage age theke calculate kore rakha holo -- color scale-e lagbe
            const maxPct = total > 0 ? Math.max(...data.map(d => (d.amount / total) * 100)) : 0;

            const size = 300;
            const cx = size / 2;
            const cy = size / 2 - 4;
            const startAngle = -128;
            const endAngle = 128;
            const outerR = 122;
            const ringGap = 26;
            const strokeW = 16;
            const MIN_RADIUS_FOR_TEXT = 42;

            let defsHtml = '';
            let arcsHtml = '';
            let labelsHtml = '';
            let badgesHtml = '';
            let sideLabels = [];

            data.forEach((d, i) => {
                const r = outerR - (i * ringGap);
                const pct = total > 0 ? Math.round((d.amount / total) * 100) : 0;

                // Rong ekhon d.color theke na, expense magnitude (pct) theke ashche
                // maxPct diye normalize kora holo jate sob-cheye beshi expense-i pure lal hoy,
                // baki gula relative kome sobuj-er dike jabe
                const relativePct = maxPct > 0 ? (pct / maxPct) * 100 : 0;
                const ringColor = expenseColorScale(relativePct);

                const pathId = `arcPath${i}`;
                const pathD = describeArc(cx, cy, r, startAngle, endAngle);

                defsHtml += `<path id="${pathId}" d="${pathD}" fill="none" />`;

                arcsHtml += `<path d="${pathD}"
            fill="none" stroke="${ringColor}" stroke-width="${strokeW}"
            stroke-linecap="round" class="concentric-arc"
            style="animation-delay:${i * 0.12}s" />`;

                if (r >= MIN_RADIUS_FOR_TEXT) {
                    const labelText = `${d.label} · ${pct}%`;
                    labelsHtml += `<text class="concentric-inband-label" dy="4"
                    style="animation-delay:${i * 0.12 + 0.15}s">
                <textPath href="#${pathId}" xlink:href="#${pathId}" startOffset="50%" text-anchor="middle">
                    ${labelText}
                </textPath>
            </text>`;
                } else {
                    sideLabels.push({
                        ...d,
                        pct,
                        color: ringColor
                    });
                }

                const badgePos = polarToCartesian(cx, cy, r, startAngle);
                badgesHtml += `<g class="concentric-badge" style="animation-delay:${i * 0.12 + 0.2}s">
            <circle cx="${badgePos.x}" cy="${badgePos.y}" r="13" fill="#fff" stroke="${ringColor}" stroke-width="2.5"/>
            <text x="${badgePos.x}" y="${badgePos.y + 4.5}" text-anchor="middle"
                font-size="11" font-weight="800" fill="${ringColor}">${String(i + 1).padStart(2, '0')}</text>
        </g>`;
            });

            document.getElementById('concentricChart').innerHTML = `
        <svg viewBox="0 0 ${size} ${size}" class="concentric-svg" xmlns="http://www.w3.org/2000/svg">
            <defs>${defsHtml}</defs>
            ${arcsHtml}
            ${labelsHtml}
            ${badgesHtml}
        </svg>`;

            const legendBox = document.getElementById('expenseInnerLegend');
            if (sideLabels.length) {
                legendBox.innerHTML = sideLabels.map(d => `
            <div class="fin-legend-item">
                <span class="fin-legend-pct" style="background:${d.color}">${d.pct}%</span>
                ${d.label} : ৳${(d.amount / 1000).toFixed(1)}k
            </div>`).join('');
                legendBox.style.display = 'grid';
            } else {
                legendBox.innerHTML = '';
                legendBox.style.display = 'none';
            }
        }
        /* ---------------- Revenue Comparison (grouped bar chart) ---------------- */
        let revenueChartInstance = null;

        function renderRevenueLegend() {
            document.getElementById('revLegend').innerHTML = `
    <span><span class="dot" style="background:#059669"></span> ${new Date().getFullYear()}</span>
    <span><span class="dot" style="background:#a7d9c5"></span> ${new Date().getFullYear()-1}</span>`;
        }

        function renderRevenueChart(data) {
            const thisYearVals = data.this_year.map(Number);
            const lastYearVals = data.last_year.map(Number);
            const maxVal = Math.max(...thisYearVals, ...lastYearVals, 1);

            // Bortoman mash (0-indexed) -- ei mash porjonto shob "past", tar por gula "future"
            const currentMonthIdx = new Date().getMonth();

            const steps = 4;
            const yAxisHtml = Array.from({
                length: steps + 1
            }, (_, i) => {
                const val = Math.round((maxVal / steps) * (steps - i));
                return `<div>৳${val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val}</div>`;
            }).join('');
            document.getElementById('revYAxis').innerHTML = yAxisHtml;

            const barsHtml = data.labels.map((month, i) => {
                const curVal = thisYearVals[i];
                const lastVal = lastYearVals[i];
                const curPct = maxVal > 0 ? (curVal / maxVal) * 100 : 0;
                const lastPct = maxVal > 0 ? (lastVal / maxVal) * 100 : 0;
                const totalPct = Math.max(curPct, lastPct);
                const curSegHeight = totalPct > 0 ? (curPct / totalPct) * 100 : 0;
                const lastSegHeight = totalPct > 0 ? (lastPct / totalPct) * 100 : 0;

                // i <= currentMonthIdx mane eita already-past mash -- shada-i (permanently) color dekhabe
                const isPast = i <= currentMonthIdx;

                return `
        <div class="rev-bar-col ${isPast ? 'is-past' : ''}" data-idx="${i}">
            <div class="rev-tooltip">
                <div class="rev-tooltip-title">${month}</div>
                <div class="rev-tooltip-row">
                    <span class="rev-tooltip-dot" style="background:#059669"></span>
                    This Year : <b>৳${curVal.toLocaleString()}</b>
                </div>
                <div class="rev-tooltip-row">
                    <span class="rev-tooltip-dot" style="background:#a7d9c5"></span>
                    Last Year : <b>৳${lastVal.toLocaleString()}</b>
                </div>
            </div>
            <div class="rev-bar-track">
                <div class="rev-bar-ghost" style="height:${totalPct}%"></div>
                <div class="rev-bar-split" style="height:${totalPct}%">
                    <div class="rev-bar-seg-current" style="height:${curSegHeight}%"></div>
                    <div class="rev-bar-seg-last" style="height:${lastSegHeight}%"></div>
                </div>
            </div>
            <div class="rev-bar-month">${month}</div>
        </div>`;
            }).join('');

            document.getElementById('revBarsArea').innerHTML = barsHtml;

            // Future mash-e hover korle-o color dekhabe (tooltip soho), past mash-e already color-i ache
            document.querySelectorAll('.rev-bar-col').forEach(col => {
                col.addEventListener('mouseenter', () => col.classList.add('active'));
                col.addEventListener('mouseleave', () => col.classList.remove('active'));
            });
        }

        function loadOverviewCharts(range = 'this_year') {
            renderCashFlowLegend();
            fetch(`${API_BASE}/cash-flow?range=${range}`)
                .then(r => r.json())
                .then(renderCashFlowChart)
                .catch(() => {
                    document.getElementById('cashFlowChart').closest('.panel-body').innerHTML =
                        `<div class="empty-state"><i class="bi bi-graph-up"></i><p>Failed to load cash flow data</p></div>`;
                });

            renderRevenueLegend();
            fetch(`${API_BASE}/revenue-comparison`)
                .then(r => r.json())
                .then(renderRevenueChart)
                .catch(() => {
                    document.getElementById('revenueChart').closest('.panel-body').innerHTML =
                        `<div class="empty-state"><i class="bi bi-bar-chart"></i><p>Failed to load revenue data</p></div>`;
                });

            fetch(`${API_BASE}/expense-breakdown`)
                .then(r => r.json())
                .then(data => {
                    if (!data.length) {
                        document.getElementById('concentricChart').closest('.panel-body').innerHTML =
                            `<div class="empty-state"><i class="bi bi-pie-chart"></i><p>No expense data found</p></div>`;
                        return;
                    }
                    renderExpenseConcentric(data);
                })
                .catch(() => {
                    document.getElementById('concentricChart').closest('.panel-body').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load expense data</p></div>`;
                });
        }

        document.getElementById('cfRange').addEventListener('change', function() {
            fetch(`${API_BASE}/cash-flow?range=${this.value}`)
                .then(r => r.json())
                .then(renderCashFlowChart)
                .catch(() => {});
        });

        /* ---------------- Transactions list ---------------- */
        function renderTransactions(rows) {
            const box = document.getElementById('txnList');
            box.innerHTML = rows.length ? rows.map(t => {
                    const isIncome = t.direction === 'income';
                    const typeLabel = (t.type || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                    return `
  <div class="txn-row">
    <div class="txn-top">
      <div class="txn-icon" style="background:${isIncome ? '#10b981' : '#ef4444'}">
        <i class="bi ${isIncome ? 'bi-arrow-down-left' : 'bi-arrow-up-right'}"></i>
      </div>
      <div>
        <div class="txn-title">${t.title}</div>
        <div class="txn-sub">${t.descaption}</div>
        <div class="txn-sub">
          <span class="txn-voucher">${typeLabel}</span>
          <span class="txn-voucher">${t.voucher}</span>
        </div>
      </div>
    </div>
    <div class="txn-branch">
      <span class="txn-field-label">Branch</span>${t.branch}
    </div>
    <div class="txn-meta-grid">
      <div>
        <span class="txn-field-label">Amount</span>
        <div class="txn-qty" style="color:${isIncome ? '#16a34a' : '#dc2626'}">${isIncome ? '+' : '-'}৳${Number(t.amount).toLocaleString()}</div>
      </div>
      <div>
        <span class="txn-field-label">Date</span>
        <div class="txn-date">${t.date}</div>
      </div>
    </div>
  </div>`;
                }).join('') :
                `<div class="empty-state"><i class="bi bi-receipt"></i><p>No transactions found</p></div>`;
        }

        let txnFilterTimer;

        function loadTransactions() {
            const type = document.getElementById('txnTypeFilter').value;
            const search = document.getElementById('txnSearch').value;
            const params = new URLSearchParams({
                type,
                search
            }).toString();

            document.getElementById('txnList').innerHTML =
                `<div class="text-center text-muted py-3">Loading...</div>`;

            fetch(`${API_BASE}/transactions?${params}`)
                .then(r => r.json())
                .then(renderTransactions)
                .catch(() => {
                    document.getElementById('txnList').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load transactions</p></div>`;
                });
        }

        document.getElementById('txnSearch').addEventListener('input', function() {
            clearTimeout(txnFilterTimer);
            txnFilterTimer = setTimeout(loadTransactions, 400);
        });
        document.getElementById('txnTypeFilter').addEventListener('change', loadTransactions);

        /* ---------------- Invoices list ---------------- */
        function renderInvoices(rows) {
            const box = document.getElementById('invoiceList');
            const statusCls = {
                paid: 'status-paid',
                pending: 'status-pending',
                overdue: 'status-overdue'
            };
            box.innerHTML = rows.length ? rows.map(inv => `
  <div class="person-row">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar" style="background:${inv.color}">${inv.client.charAt(0)}</div>
      <div>
        <div class="person-name">${inv.client} <span class="text-muted fw-normal">#${inv.invoice_no}</span></div>
        <div class="person-sub">${inv.property} &middot; Due ${inv.due_date}</div>
      </div>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="fw-bold">৳${Number(inv.amount).toLocaleString()}</span>
      <span class="status-badge ${statusCls[inv.status] || 'status-pending'}">${inv.status.charAt(0).toUpperCase() + inv.status.slice(1)}</span>
    </div>
  </div>`).join('') :
                `<div class="empty-state"><i class="bi bi-file-earmark-text"></i><p>No invoices found</p></div>`;
        }

        let invFilterTimer;

        function loadInvoices() {
            const status = document.getElementById('invStatusFilter').value;
            const search = document.getElementById('invSearch').value;
            const params = new URLSearchParams({
                status,
                search
            }).toString();

            document.getElementById('invoiceList').innerHTML =
                `<div class="text-center text-muted py-3">Loading...</div>`;

            fetch(`${API_BASE}/invoices?${params}`)
                .then(r => r.json())
                .then(renderInvoices)
                .catch(() => {
                    document.getElementById('invoiceList').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load invoices</p></div>`;
                });
        }

        document.getElementById('invSearch').addEventListener('input', function() {
            clearTimeout(invFilterTimer);
            invFilterTimer = setTimeout(loadInvoices, 400);
        });
        document.getElementById('invStatusFilter').addEventListener('change', loadInvoices);

        /* ---------------- Init ---------------- */
        activateTab('overview'); // Overview tab-e KPI/charts render + default active kore dey
    </script>
@endsection
