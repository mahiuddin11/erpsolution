@extends('backend.layouts.master')

@section('title')
    {{ $pgTitle ?? 'Financial' }} - Admin Panel
@endsection

<!-- Common Dashboard CSS (shared by all department dashboards) -->
<link rel="stylesheet"
    href="{{ asset('css/dashboard-style.css') }}?v={{ filemtime(public_path('css/dashboard-style.css')) }}">
@section('styles')
    <style>
        .bc-stat-card {
            cursor: default;
        }

        .bc-stat-card:hover {
            transform: none;
            box-shadow: none;
        }

        .bc-stat-sub {
            font-size: .78rem;
            color: var(--gray-500, #6b7280);
            margin-top: 4px;
        }

        .bc-stat-bar {
            height: 3px;
            width: 32px;
            border-radius: 2px;
            margin-top: 8px;
        }

        .bc-acc-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            margin-right: 8px;
            font-size: .8rem;
        }

        .bc-type-badge {
            display: inline-block;
            background: var(--emerald-100, #d1fae5);
            color: var(--emerald-700, #047857);
            font-size: .72rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 999px;
        }

        .aging-kpi-card {
            cursor: default;
        }

        .aging-kpi-card:hover {
            transform: none;
            box-shadow: none;
        }

        .aging-kpi-icon-wrap {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .aging-caption {
            font-size: .75rem;
            color: var(--gray-500, #6b7280);
            margin-top: -8px;
            margin-bottom: 4px;
        }

        .aging-table thead tr {
            background: #1e293b;
        }

        .aging-table thead th {
            color: #fff;
            font-weight: 600;
            border: none;
            padding: 10px 12px;
        }

        .aging-table tbody td {
            padding: 8px 12px;
        }

        .aging-table tfoot td {
            border-top: 2px solid #e5e7eb;
            padding: 10px 12px;
        }

        .fin-panel-subtitle {
            font-size: .78rem;
            font-weight: 400;
            color: var(--gray-500, #9ca3af);
            margin-left: 4px;
        }

        /* add new: AR/AP Aging Summary search input + section header with range filter */
        .aging-search-input {
            max-width: 220px;
        }

        .aging-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .aging-section-title {
            font-weight: 700;
            font-size: 1.05rem;
            margin: 0;
        }
    </style>
@endsection
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

        <div class="fin-tabs-row">
            <div class="fin-tabs" id="finTabs">
                <button type="button" class="fin-tab active" data-tab="overview">Overview</button>
                <button type="button" class="fin-tab" data-tab="transactions">Transactions</button>
                <button type="button" class="fin-tab" data-tab="invoices">Invoices</button>
            </div>
            <div class="fin-tabs" id="finRangeFilter">
                <button type="button" class="fin-range-btn" data-range="today">Today</button>
                <button type="button" class="fin-range-btn active" data-range="7d">7 Days</button>
                <button type="button" class="fin-range-btn" data-range="month">Month</button>
                <button type="button" class="fin-range-btn" data-range="year">Year</button>
                <button type="button" class="fin-range-btn" data-range="all">All Time</button>
            </div>
        </div>

        {{-- ============ TAB: Overview ============ --}}
        <div class="fin-tab-content" id="tabOverview">

            {{-- ============ KPI Cards ============ --}}
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

            {{-- ============ Bank / Cash Balance ============ --}}
            <div class="row g-3 mb-3 bank-cash-blance">
                <div class="col-12">
                    <div class="panel fin-panel">
                        <div class="panel-header fin-panel-header">
                            <span class="fin-panel-title">Bank &amp; Cash Balance</span>
                        </div>
                        <div class="panel-body" id="bankCashBody">
                            <div class="text-center text-muted py-3">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ AR Aging ============ --}}
            {{-- add new: section header with range filter (7d/30d/90d/year) --}}
            <div class="aging-section-header">
                <h5 class="aging-section-title">Accounts Receivable Aging</h5>
                <div class="fin-tabs" id="arAgingRangeFilter">
                    <button type="button" class="fin-range-btn" data-arrange="7d">7 Days</button>
                    <button type="button" class="fin-range-btn" data-arrange="30d">30 Days</button>
                    <button type="button" class="fin-range-btn" data-arrange="90d">90+ Days</button>
                    <button type="button" class="fin-range-btn active" data-arrange="year">1 Year</button>
                </div>
            </div>
            <div class="row g-3 mb-3" id="arAgingKpis"></div>
            <div class="row g-3 mb-3">
                <div class="col-lg-6">
                    <div class="panel fin-panel">
                        <div class="panel-header fin-panel-header">
                            <span class="fin-panel-title">Unpaid Invoices Amount by Customer (Top 10) <span
                                    class="fin-panel-subtitle">in home currency</span></span>
                        </div>
                        <div class="panel-body">
                            <div style="height:280px"><canvas id="arBarChart"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="panel fin-panel">
                        <div class="panel-header fin-panel-header">
                            <span class="fin-panel-title">Distribution (Top 10)</span>
                        </div>
                        <div class="panel-body">
                            <div style="height:280px"><canvas id="arDonutChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <div class="panel fin-panel">
                        <div class="panel-header fin-panel-header">
                            <span class="fin-panel-title">Accounts Receivable Aging -- Summary</span>
                            {{-- add new: customer name search --}}
                            <input type="text" class="form-control form-control-sm aging-search-input"
                                id="arAgingSearch" placeholder="Search customer...">
                        </div>
                        <div class="panel-body" id="arAgingBody">
                            <div class="text-center text-muted py-3">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ AP Aging ============ --}}
            {{-- add new: section header with range filter (7d/30d/90d/year) --}}
            <div class="aging-section-header">
                <h5 class="aging-section-title">Accounts Payable Aging</h5>
                <div class="fin-tabs" id="apAgingRangeFilter">
                    <button type="button" class="fin-range-btn" data-arrange="7d">7 Days</button>
                    <button type="button" class="fin-range-btn" data-arrange="30d">30 Days</button>
                    <button type="button" class="fin-range-btn" data-arrange="90d">90+ Days</button>
                    <button type="button" class="fin-range-btn active" data-arrange="year">1 Year</button>
                </div>
            </div>
            <div class="row g-3 mb-3" id="apAgingKpis"></div>
            <div class="row g-3 mb-3">
                <div class="col-lg-6">
                    <div class="panel fin-panel">
                        <div class="panel-header fin-panel-header">
                            <span class="fin-panel-title">Unpaid Bills Amount by Supplier (Top 10) <span
                                    class="fin-panel-subtitle">in home currency</span></span>
                        </div>
                        <div class="panel-body">
                            <div style="height:280px"><canvas id="apBarChart"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="panel fin-panel">
                        <div class="panel-header fin-panel-header">
                            <span class="fin-panel-title">Distribution (Top 10)</span>
                        </div>
                        <div class="panel-body">
                            <div style="height:280px"><canvas id="apDonutChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <div class="panel fin-panel">
                        <div class="panel-header fin-panel-header">
                            <span class="fin-panel-title">Accounts Payable Aging -- Summary</span>
                            {{-- add new: supplier name search --}}
                            <input type="text" class="form-control form-control-sm aging-search-input"
                                id="apAgingSearch" placeholder="Search supplier...">
                        </div>
                        <div class="panel-body" id="apAgingBody">
                            <div class="text-center text-muted py-3">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        {{-- ============ /TAB: Overview ============ --}}

        {{-- ============ TAB: Transactions ============ --}}
        <div class="fin-tab-content mb-3" id="tabTransactions" style="display:none">
            <div class="panel fin-panel ">
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

    {{-- ============ KPI Detail Modal ============ --}}
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
        .fin-wrap {
            --fin-green: #10b981;
            --fin-green-dark: #059669;
            --fin-red: #ef4444;
            --fin-amber: #f59e0b;
            --fin-purple: #7c3aed;
        }

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

        .fin-range-btn {
            border: none;
            background: transparent;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            color: var(--gray-600);
            transition: .15s;
        }

        .fin-range-btn:hover {
            color: var(--gray-900);
        }

        .fin-range-btn.active {
            background: var(--emerald-100);
            color: var(--emerald-700);
        }

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

        @media (max-width: 480px) {
            .concentric-label {
                font-size: 8.5px;
            }

            .fin-tabs-row {
                flex-direction: column;
                align-items: stretch;
            }

            .concentric-inband-label {
                font-size: 8px;
            }
        }

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

        .status-paid {
            background: #dcfce7;
            color: #166534;
        }

        .status-overdue {
            background: #fee2e2;
            color: #991b1b;
        }

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
            background-image: repeating-linear-gradient(to top, var(--gray-200, #e5e7eb) 0, var(--gray-200, #e5e7eb) 1px, transparent 1px, transparent 25%);
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

        .rev-bar-col.is-past .rev-bar-ghost {
            opacity: 0;
        }

        .rev-bar-col.is-past .rev-bar-split {
            display: flex;
        }

        .rev-bar-col.is-past .rev-bar-month {
            color: var(--gray-900, #111827);
            font-weight: 700;
        }

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

        const FIN_RANGE_LABELS = {
            today: 'Today',
            '7d': 'Last 7 Days',
            month: 'This Month',
            year: 'This Year',
            all: 'All Time'
        };
        let finRange = '7d';

        let overviewChartsRendered = false;
        let transactionsRendered = false;
        let invoicesRendered = false;

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

        document.querySelectorAll('#finRangeFilter .fin-range-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#finRangeFilter .fin-range-btn').forEach(b => b.classList.remove(
                    'active'));
                this.classList.add('active');
                finRange = this.dataset.range;
                loadKpis();
                loadExpenseBreakdown();
            });
        });

        /* ---------------- KPI Cards ---------------- */
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
            const rangeLabel = FIN_RANGE_LABELS[finRange] || 'Last 7 Days';
            document.getElementById('finKpis').innerHTML = [
                kpiCard({
                    label: `Total Income (${rangeLabel})`,
                    value: '৳' + Number(kpi.total_income).toLocaleString(),
                    trend: (kpi.income_change >= 0 ? '+' : '') + kpi.income_change + '%',
                    trendDir: kpi.income_change >= 0 ? 'up' : 'down',
                    icon: 'bi-currency-dollar',
                    iconBg: '#eff6ff',
                    iconColor: '#2563eb',
                    type: 'total_income'
                }),
                kpiCard({
                    label: `Total Expenses (${rangeLabel})`,
                    value: '৳' + Number(kpi.total_expenses).toLocaleString(),
                    trend: (kpi.expenses_change >= 0 ? '+' : '') + kpi.expenses_change + '%',
                    trendDir: kpi.expenses_change >= 0 ? 'down' : 'up',
                    icon: 'bi-graph-up-arrow',
                    iconBg: '#f3e8ff',
                    iconColor: '#9333ea',
                    type: 'total_expenses'
                }),
                kpiCard({
                    label: `Net Profit (${rangeLabel})`,
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

        function loadKpis() {
            fetch(`${API_BASE}/kpis?range=${finRange}`)
                .then(r => r.json())
                .then(renderKpis)
                .catch(() => {
                    document.getElementById('finKpis').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load KPI data</p></div>`;
                });
        }
        loadKpis();

        /* ---------------- KPI Detail Modal ---------------- */
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
            fetch(`${API_BASE}/kpi-details?type=${kpiModalState.type}&range=${finRange}&page=${page}&per_page=100`)
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
                        <tr><th>Voucher</th><th>Description</th><th class="text-right">Date</th><th class="text-right">Amount</th></tr>
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
            fetch(`${API_BASE}/kpi-details?type=${kpiModalState.type}&range=${finRange}&all=1`)
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
            fetch(`${API_BASE}/kpi-details?type=${kpiModalState.type}&range=${finRange}&all=1`)
                .then(r => r.json())
                .then(res => openKpiPrintWindow(res.data))
                .catch(() => alert('Failed to prepare full data print'));
        }

        function openKpiPrintWindow(rows) {
            const win = window.open('', '_blank');
            const html = `
                <html><head><title>${kpiModalState.title}</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 24px; color: #111827; }
                    h3 { margin-bottom: 4px; }
                    .meta { color: #6b7280; font-size: 13px; margin-bottom: 16px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #e5e7eb; padding: 6px 10px; font-size: 13px; text-align: left; }
                    th { background: #f3f4f6; }
                    td.amount, th.amount { text-align: right; }
                </style></head>
                <body>
                    <h3>${kpiModalState.title}</h3>
                    <div class="meta">${rows.length.toLocaleString()} record${rows.length > 1 ? 's' : ''} -- generated ${new Date().toLocaleDateString()}</div>
                    <table>
                        <thead><tr><th>Voucher</th><th>Description</th><th>Date</th><th class="amount">Amount</th></tr></thead>
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
                </body></html>`;
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

        /* ---------------- Cash Flow Analysis ---------------- */
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

        /* ---------------- Expense Breakdown ---------------- */
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
            const maxPct = total > 0 ? Math.max(...data.map(d => (d.amount / total) * 100)) : 0;
            const size = 300,
                cx = size / 2,
                cy = size / 2 - 4,
                startAngle = -128,
                endAngle = 128;
            const outerR = 122,
                ringGap = 26,
                strokeW = 16,
                MIN_RADIUS_FOR_TEXT = 42;
            let defsHtml = '',
                arcsHtml = '',
                labelsHtml = '',
                badgesHtml = '',
                sideLabels = [];

            data.forEach((d, i) => {
                const r = outerR - (i * ringGap);
                const pct = total > 0 ? Math.round((d.amount / total) * 100) : 0;
                const relativePct = maxPct > 0 ? (pct / maxPct) * 100 : 0;
                const ringColor = expenseColorScale(relativePct);
                const pathId = `arcPath${i}`;
                const pathD = describeArc(cx, cy, r, startAngle, endAngle);
                defsHtml += `<path id="${pathId}" d="${pathD}" fill="none" />`;
                arcsHtml +=
                    `<path d="${pathD}" fill="none" stroke="${ringColor}" stroke-width="${strokeW}" stroke-linecap="round" class="concentric-arc" style="animation-delay:${i * 0.12}s" />`;
                if (r >= MIN_RADIUS_FOR_TEXT) {
                    const labelText = `${d.label} · ${pct}%`;
                    labelsHtml += `<text class="concentric-inband-label" dy="4" style="animation-delay:${i * 0.12 + 0.15}s">
                        <textPath href="#${pathId}" xlink:href="#${pathId}" startOffset="50%" text-anchor="middle">${labelText}</textPath>
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
                    <text x="${badgePos.x}" y="${badgePos.y + 4.5}" text-anchor="middle" font-size="11" font-weight="800" fill="${ringColor}">${String(i + 1).padStart(2, '0')}</text>
                </g>`;
            });

            document.getElementById('concentricChart').innerHTML = `
        <svg viewBox="0 0 ${size} ${size}" class="concentric-svg" xmlns="http://www.w3.org/2000/svg">
            <defs>${defsHtml}</defs>${arcsHtml}${labelsHtml}${badgesHtml}
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

        function loadExpenseBreakdown() {
            fetch(`${API_BASE}/expense-breakdown?range=${finRange}`)
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

        /* ---------------- Revenue Comparison ---------------- */
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

            document.querySelectorAll('.rev-bar-col').forEach(col => {
                col.addEventListener('mouseenter', () => col.classList.add('active'));
                col.addEventListener('mouseleave', () => col.classList.remove('active'));
            });
        }

        /* ---------------- AR / AP Aging ---------------- */
        // add new: agingState-e search field add kora holo (client-side filter-er jonno)
        const agingState = {
            receivable: {
                data: null,
                search: ''
            },
            payable: {
                data: null,
                search: ''
            }
        };
        // add new: প্রতিটা section-er নিজস্ব range state
        let arAgingRange = 'year',
            apAgingRange = 'year';
        let arBarInst = null,
            arDonutInst = null,
            apBarInst = null,
            apDonutInst = null;

        function agingKpiCard(label, value, icon, iconBg, iconColor) {
            return `
        <div class="col-6 col-lg-3">
            <div class="fin-kpi-card aging-kpi-card" style="cursor:default">
                <div class="aging-kpi-icon-wrap" style="background:${iconBg};color:${iconColor}">
                    <i class="bi ${icon}"></i>
                </div>
                <div class="fin-kpi-label mt-2">${label}</div>
                <div class="fin-kpi-value" style="font-size:20px;white-space:normal;word-break:break-word">৳${Number(value).toLocaleString()}</div>
            </div>
        </div>`;
        }

        function renderAgingKpis(kpiContainerId, kpis, prefix) {
            document.getElementById(kpiContainerId).innerHTML = [
                agingKpiCard(`Unpaid ${prefix} Amount`, kpis.unpaid_amount, 'bi-receipt', '#eff6ff', '#2563eb'),
                agingKpiCard('Overdue Amount', kpis.overdue_amount, 'bi-clock-history', '#fef2f2', '#dc2626'),
                agingKpiCard('Overdue 30+ Days', kpis.overdue_30_plus, 'bi-calendar-x', '#fff7ed', '#ea580c'),
                agingKpiCard('Overdue 90+ Days', kpis.overdue_90_plus, 'bi-exclamation-octagon', '#fdf2f8', '#be185d'),
            ].join('') + `<div class="col-12"><div class="aging-caption">in home currency</div></div>`;
        }

        function renderAgingCharts(barCanvasId, donutCanvasId, top10, instRefSetter) {
            const labels = top10.map(t => t.label);
            const values = top10.map(t => Math.abs(t.amount));
            const colors = ['#1e3a5f', '#2c5282', '#2b6cb0', '#3182ce', '#4299e1', '#63b3ed', '#90cdf4', '#a0d3ec',
                '#bee3f8', '#e2e8f0'
            ];

            const barCtx = document.getElementById(barCanvasId).getContext('2d');
            const barChart = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Amount',
                        data: values,
                        backgroundColor: '#f2795f',
                        borderRadius: 3,
                        barPercentage: 0.55,
                        categoryPercentage: 0.7
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        x: {
                            position: 'top',
                            grid: {
                                color: '#eef0f2',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#9ca3af',
                                font: {
                                    size: 10
                                },
                                callback: v => (v >= 1000 ? (v / 1000) + 'K' : v)
                            }
                        },
                        y: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: '#4b5563',
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });

            const donutCtx = document.getElementById(donutCanvasId).getContext('2d');
            const donutChart = new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        label: 'Amount',
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '55%',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',
                            labels: {
                                boxWidth: 10,
                                font: {
                                    size: 10
                                },
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    }
                }
            });

            instRefSetter(barChart, donutChart);
        }

        // change: pagination logic sorano holo -- ekhon shudhu search-diye filter kore
        // shob matching row ekbari dekhabe (table-responsive scroll diye)
        function renderAgingTable(containerId, type) {
            const state = agingState[type];
            const data = state.data;
            const searchTerm = (state.search || '').toLowerCase().trim();

            // add new: customer/supplier name diye client-side filter
            const filteredRows = searchTerm ?
                data.rows.filter(r => r.name.toLowerCase().includes(searchTerm)) :
                data.rows;

            // add new: grand total filtered rows theke live-calculate, jate search
            // korle footer-o thik matching total dekhay
            const grand = {
                current: filteredRows.reduce((s, r) => s + r.current, 0),
                d1_30: filteredRows.reduce((s, r) => s + r.d1_30, 0),
                d31_60: filteredRows.reduce((s, r) => s + r.d31_60, 0),
                d61_90: filteredRows.reduce((s, r) => s + r.d61_90, 0),
                d91_plus: filteredRows.reduce((s, r) => s + r.d91_plus, 0),
                total: filteredRows.reduce((s, r) => s + r.total, 0),
            };

            let html;
            if (!filteredRows.length) {
                html = `<div class="empty-state"><i class="bi bi-inbox"></i><p>No matching records</p></div>`;
            } else {
                html =
                    `
            <div class="table-responsive" style="max-height:420px;overflow-y:auto">
                <table class="table table-sm kpi-detail-table aging-table">
                    <thead>
                        <tr>
                            <th>${type === 'receivable' ? 'Customer' : 'Supplier'}</th>
                            <th class="text-right">Current</th>
                            <th class="text-right">1-30</th>
                            <th class="text-right">31-60</th>
                            <th class="text-right">61-90</th>
                            <th class="text-right">91 and over</th>
                            <th class="text-right">Amount Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filteredRows.map(r => `
                                <tr>
                                    <td>${r.name}</td>
                                    <td class="text-right">${Number(r.current).toLocaleString()}</td>
                                    <td class="text-right">${Number(r.d1_30).toLocaleString()}</td>
                                    <td class="text-right">${Number(r.d31_60).toLocaleString()}</td>
                                    <td class="text-right">${Number(r.d61_90).toLocaleString()}</td>
                                    <td class="text-right">${Number(r.d91_plus).toLocaleString()}</td>
                                    <td class="text-right" style="font-weight:700">${Number(r.total).toLocaleString()}</td>
                                </tr>`).join('')}
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:700;background:#f9fafb">
                            <td>Grand Total</td>
                            <td class="text-right">${Number(grand.current).toLocaleString()}</td>
                            <td class="text-right">${Number(grand.d1_30).toLocaleString()}</td>
                            <td class="text-right">${Number(grand.d31_60).toLocaleString()}</td>
                            <td class="text-right">${Number(grand.d61_90).toLocaleString()}</td>
                            <td class="text-right">${Number(grand.d91_plus).toLocaleString()}</td>
                            <td class="text-right">${Number(grand.total).toLocaleString()}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="text-muted small text-center mt-1">${filteredRows.length} record${filteredRows.length > 1 ? 's' : ''}</div>`;
            }

            document.getElementById(containerId).innerHTML = html;
        }

        // add new: search input listener -- customer/supplier name diye filter
        document.getElementById('arAgingSearch').addEventListener('input', function() {
            agingState.receivable.search = this.value;
            renderAgingTable('arAgingBody', 'receivable');
        });
        document.getElementById('apAgingSearch').addEventListener('input', function() {
            agingState.payable.search = this.value;
            renderAgingTable('apAgingBody', 'payable');
        });

        // change: range parameter add kora holo, ar function-e range pass kora hocche
        function loadArAging(range) {
            fetch(`${API_BASE}/ar-ap-aging?range=${range}`)
                .then(r => r.json())
                .then(res => {
                    agingState.receivable.data = res.receivable;
                    renderAgingKpis('arAgingKpis', res.receivable.kpis, 'Invoices');
                    if (arBarInst) arBarInst.destroy();
                    if (arDonutInst) arDonutInst.destroy();
                    renderAgingCharts('arBarChart', 'arDonutChart', res.receivable.top10, (b, d) => {
                        arBarInst = b;
                        arDonutInst = d;
                    });
                    renderAgingTable('arAgingBody', 'receivable');
                })
                .catch(() => {
                    document.getElementById('arAgingBody').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load AR aging</p></div>`;
                });
        }

        // add new: AP aging-er jonno alada loader (age eta ekta combined loadArApAging()-e chhilo)
        function loadApAging(range) {
            fetch(`${API_BASE}/ar-ap-aging?range=${range}`)
                .then(r => r.json())
                .then(res => {
                    agingState.payable.data = res.payable;
                    renderAgingKpis('apAgingKpis', res.payable.kpis, 'Bills');
                    if (apBarInst) apBarInst.destroy();
                    if (apDonutInst) apDonutInst.destroy();
                    renderAgingCharts('apBarChart', 'apDonutChart', res.payable.top10, (b, d) => {
                        apBarInst = b;
                        apDonutInst = d;
                    });
                    renderAgingTable('apAgingBody', 'payable');
                })
                .catch(() => {
                    document.getElementById('apAgingBody').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load AP aging</p></div>`;
                });
        }

        // change: age ekta call-e AR+AP dutoi loading hoto, ekhon dutoi nijer nijer range diye chole
        function loadArApAging() {
            loadArAging(arAgingRange);
            loadApAging(apAgingRange);
        }

        // add new: AR Aging section-er range filter click handler
        document.querySelectorAll('#arAgingRangeFilter .fin-range-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#arAgingRangeFilter .fin-range-btn').forEach(b => b.classList
                    .remove('active'));
                this.classList.add('active');
                arAgingRange = this.dataset.arrange;
                loadArAging(arAgingRange);
            });
        });
        // add new: AP Aging section-er range filter click handler
        document.querySelectorAll('#apAgingRangeFilter .fin-range-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#apAgingRangeFilter .fin-range-btn').forEach(b => b.classList
                    .remove('active'));
                this.classList.add('active');
                apAgingRange = this.dataset.arrange;
                loadApAging(apAgingRange);
            });
        });

        /* ---------------- Bank / Cash Balance ---------------- */
        let bcAccountsData = [];
        let bcCurrentPage = 1;
        const BC_PER_PAGE = 8;

        function bcStatCard(icon, iconBg, iconColor, label, value, sub) {
            return `
        <div class="col-6 col-lg-3">
            <div class="fin-kpi-card bc-stat-card">
                <div class="fin-kpi-top">
                    <div class="fin-kpi-label">${label}</div>
                    <div class="fin-kpi-icon" style="background:${iconBg};color:${iconColor}"><i class="bi ${icon}"></i></div>
                </div>
                <div class="fin-kpi-value" style="color:${iconColor};white-space:normal;word-break:break-word">৳${Number(value).toLocaleString()}</div>
                <div class="bc-stat-sub">${sub}</div>
                <div class="bc-stat-bar" style="background:${iconColor}"></div>
            </div>
        </div>`;
        }

        function renderBankCashPanel(data) {
            bcAccountsData = data.accounts;
            bcCurrentPage = 1;

            let html = `<div class="row g-3 mb-3">`;
            html += bcStatCard('bi-bank', '#eff6ff', '#2563eb', 'Total Bank Balance', data.total_bank,
                `${data.bank_accounts_count} Accounts`);
            html += bcStatCard('bi-cash-coin', '#f0fdf4', '#16a34a', 'Total Cash Balance', data.total_cash,
                `${data.cash_accounts_count} Accounts`);
            html += bcStatCard('bi-pie-chart-fill', '#f5f3ff', '#7c3aed', 'Total Balance', data.grand_total,
                `${data.bank_accounts_count + data.cash_accounts_count} Accounts`);
            html += bcStatCard('bi-stack', '#fff7ed', '#ea580c', "Today's Transactions", data.today_transactions_amount,
                `${data.today_transactions_count} Transactions`);
            html += `</div>`;

            html += `
        <div class="txn-filter-bar">
            <input type="text" class="form-control form-control-sm" id="bcSearch" placeholder="Search account name or number...">
            <select class="form-control form-control-sm" id="bcTypeFilter" style="max-width:140px">
                <option value="">All Types</option>
                <option value="Bank">Bank</option>
                <option value="Cash">Cash</option>
            </select>
            <select class="form-control form-control-sm" id="bcStatusFilter" style="max-width:140px">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>
        <div class="table-responsive">
            <table class="table table-sm kpi-detail-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Account Name</th>
                        <th>Account Number</th>
                        <th>Type</th>
                        <th class="text-right">Current Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="bcAccountBody"></tbody>
            </table>
        </div>
        <div id="bcPagination" class="d-flex justify-content-between align-items-center mt-2"></div>`;

            document.getElementById('bankCashBody').innerHTML = html;

            document.getElementById('bcSearch').addEventListener('input', () => {
                bcCurrentPage = 1;
                renderBankCashTable();
            });
            document.getElementById('bcTypeFilter').addEventListener('change', () => {
                bcCurrentPage = 1;
                renderBankCashTable();
            });
            document.getElementById('bcStatusFilter').addEventListener('change', () => {
                bcCurrentPage = 1;
                renderBankCashTable();
            });

            renderBankCashTable();
        }

        function renderBankCashTable() {
            const search = document.getElementById('bcSearch').value.toLowerCase();
            const typeFilter = document.getElementById('bcTypeFilter').value;
            const statusFilter = document.getElementById('bcStatusFilter').value;

            const filtered = bcAccountsData.filter(a => {
                const matchesSearch = !search ||
                    a.name.toLowerCase().includes(search) ||
                    (a.account_number || '').toLowerCase().includes(search);
                const matchesType = !typeFilter || a.type === typeFilter;
                const matchesStatus = !statusFilter || a.status === statusFilter;
                return matchesSearch && matchesType && matchesStatus;
            });

            const total = filtered.length;
            const lastPage = Math.max(1, Math.ceil(total / BC_PER_PAGE));
            if (bcCurrentPage > lastPage) bcCurrentPage = lastPage;
            const start = (bcCurrentPage - 1) * BC_PER_PAGE;
            const pageRows = filtered.slice(start, start + BC_PER_PAGE);

            const tbody = document.getElementById('bcAccountBody');
            tbody.innerHTML = pageRows.length ? pageRows.map((a, i) => {
                    const isBank = a.type === 'Bank';
                    const icon = isBank ? 'bi-bank' : 'bi-cash-stack';
                    const iconColor = isBank ? '#2563eb' : '#16a34a';
                    const iconBg = isBank ? '#eff6ff' : '#f0fdf4';
                    const balColor = a.balance < 0 ? '#dc2626' : '#16a34a';
                    const statusCls = a.status === 'Active' ? 'status-paid' : 'status-overdue';

                    return `
            <tr>
                <td>${start + i + 1}</td>
                <td>
                    <span class="bc-acc-icon" style="background:${iconBg};color:${iconColor}"><i class="bi ${icon}"></i></span>
                    ${a.name}
                </td>
                <td>${a.account_number || '-'}</td>
                <td><span class="bc-type-badge">${a.type}</span></td>
                <td class="text-right" style="color:${balColor};font-weight:700">৳${Number(a.balance).toLocaleString()}</td>
                <td><span class="status-badge ${statusCls}">${a.status}</span></td>
            </tr>`;
                }).join('') :
                `<tr><td colspan="6"><div class="empty-state"><i class="bi bi-inbox"></i><p>No accounts found</p></div></td></tr>`;

            document.getElementById('bcPagination').innerHTML = `
        <span class="text-muted small">Showing ${total ? start + 1 : 0} to ${Math.min(start + BC_PER_PAGE, total)} of ${total} entries</span>
        <div class="kpi-modal-pagination">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="bcPrevPage" ${bcCurrentPage <= 1 ? 'disabled' : ''}><i class="bi bi-chevron-left"></i></button>
            <span class="mx-2">Page ${bcCurrentPage} / ${lastPage}</span>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="bcNextPage" ${bcCurrentPage >= lastPage ? 'disabled' : ''}><i class="bi bi-chevron-right"></i></button>
        </div>`;

            document.getElementById('bcPrevPage')?.addEventListener('click', () => {
                bcCurrentPage--;
                renderBankCashTable();
            });
            document.getElementById('bcNextPage')?.addEventListener('click', () => {
                bcCurrentPage++;
                renderBankCashTable();
            });
        }

        function loadBankCashBalance() {
            fetch(`${API_BASE}/bank-cash-balance`)
                .then(r => r.json())
                .then(renderBankCashPanel)
                .catch(() => {
                    document.getElementById('bankCashBody').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load bank/cash data</p></div>`;
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
                    document.getElementById('revChartWrap').innerHTML =
                        `<div class="empty-state"><i class="bi bi-bar-chart"></i><p>Failed to load revenue data</p></div>`;
                });

            loadExpenseBreakdown();
            loadArApAging();
            loadBankCashBalance();
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
            document.getElementById('txnList').innerHTML = `<div class="text-center text-muted py-3">Loading...</div>`;
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
            document.getElementById('invoiceList').innerHTML = `<div class="text-center text-muted py-3">Loading...</div>`;
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
        activateTab('overview');
    </script>
@endsection
