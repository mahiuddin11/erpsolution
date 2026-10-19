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

        /* FIX: drill-down link */
        .aging-acc-link {
            color: #2563eb;
            font-weight: 600;
            cursor: pointer;
        }

        .aging-acc-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
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
                            <div style="height:300px"><canvas id="cashFlowChart"></canvas></div>
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
            <div class="aging-section-header">
                <h5 class="aging-section-title">Accounts Receivable Aging</h5>
                <div class="fin-tabs" id="arAgingRangeFilter">
                    <button type="button" class="fin-range-btn" data-arrange="7d">7 Days</button>
                    <button type="button" class="fin-range-btn" data-arrange="30d">30 Days</button>
                    <button type="button" class="fin-range-btn" data-arrange="90d">90+ Days</button>
                    <button type="button" class="fin-range-btn" data-arrange="year">1 Year</button>
                    <button type="button" class="fin-range-btn active" data-arrange="all">All Time</button>
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
                        <div class="panel-header fin-panel-header"><span class="fin-panel-title">Distribution (Top
                                10)</span></div>
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
            <div class="aging-section-header">
                <h5 class="aging-section-title">Accounts Payable Aging</h5>
                <div class="fin-tabs" id="apAgingRangeFilter">
                    <button type="button" class="fin-range-btn" data-arrange="7d">7 Days</button>
                    <button type="button" class="fin-range-btn" data-arrange="30d">30 Days</button>
                    <button type="button" class="fin-range-btn" data-arrange="90d">90+ Days</button>
                    <button type="button" class="fin-range-btn" data-arrange="year">1 Year</button>
                    <button type="button" class="fin-range-btn active" data-arrange="all">All Time</button>
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
                        <div class="panel-header fin-panel-header"><span class="fin-panel-title">Distribution (Top
                                10)</span></div>
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
                <div class="panel-header fin-panel-header"><span class="fin-panel-title">All Transactions</span></div>
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
                        <span></span><span>Description</span><span>Voucher</span><span>Amount</span><span
                            style="text-align:right">Date</span>
                    </div>
                    <div id="txnList"></div>
                </div>
            </div>
        </div>
        {{-- ============ /TAB: Transactions ============ --}}

        {{-- ============ TAB: Invoices ============ --}}
        <div class="fin-tab-content" id="tabInvoices" style="display:none">
            <div class="panel fin-panel">
                <div class="panel-header fin-panel-header"><span class="fin-panel-title">All Invoices</span></div>
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
                <div class="modal-header d-flex">
                    <h5 class="modal-title" id="kpiDetailModalTitle">Details</h5>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                data-toggle="dropdown">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" id="kpiExcelMenu">
                                <a class="dropdown-item" href="#" data-scope="current">Current Page</a>
                                <a class="dropdown-item" href="#" data-scope="all">Full Data</a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-toggle="dropdown">
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

    {{-- ============ Aging Account Drill-down Modal (NEW) ============ --}}
    <div class="modal fade" id="agingAccountModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agingAccountModalTitle">Account Details</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="agingAccountModalBody">
                    <div class="text-center text-muted py-3">Loading...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ... (আপনার existing <style> block হুবহু রাখুন — উপরের styles section-এ .aging-acc-link যোগ করা হয়েছে) ... */
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
        let overviewChartsRendered = false,
            transactionsRendered = false,
            invoicesRendered = false;

        const tabPanels = {
            overview: document.getElementById('tabOverview'),
            transactions: document.getElementById('tabTransactions'),
            invoices: document.getElementById('tabInvoices')
        };

        function activateTab(tabKey) {
            document.querySelectorAll('.fin-tab').forEach(b => b.classList.toggle('active', b.dataset.tab === tabKey));
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
                .then(r => r.json()).then(renderKpis)
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
                    <thead><tr><th>Voucher</th><th>Description</th><th class="text-right">Date</th><th class="text-right">Amount</th></tr></thead>
                    <tbody>
                        ${rows.map(r => `<tr><td>${r.voucher ?? '-'}</td><td>${r.title}</td><td class="text-right">${r.date}</td><td class="text-right">৳${Number(r.amount).toLocaleString()}</td></tr>`).join('')}
                    </tbody>
                </table>`;
        }

        function renderKpiPagination() {
            const box = document.getElementById('kpiModalPagination');
            if (kpiModalState.lastPage <= 1) {
                box.innerHTML = '';
                return;
            }
            box.innerHTML =
                `
                <button type="button" class="btn btn-sm btn-outline-secondary" id="kpiPrevPage" ${kpiModalState.page <= 1 ? 'disabled' : ''}><i class="bi bi-chevron-left"></i></button>
                <span class="mx-2">Page ${kpiModalState.page} / ${kpiModalState.lastPage}</span>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="kpiNextPage" ${kpiModalState.page >= kpiModalState.lastPage ? 'disabled' : ''}><i class="bi bi-chevron-right"></i></button>`;
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
                .then(r => r.json()).then(res => openKpiPrintWindow(res.data))
                .catch(() => alert('Failed to prepare full data print'));
        }

        function openKpiPrintWindow(rows) {
            const win = window.open('', '_blank');
            win.document.write(`<html><head><title>${kpiModalState.title}</title><style>
                body{font-family:Arial,sans-serif;padding:24px;color:#111827}h3{margin-bottom:4px}.meta{color:#6b7280;font-size:13px;margin-bottom:16px}
                table{width:100%;border-collapse:collapse}th,td{border:1px solid #e5e7eb;padding:6px 10px;font-size:13px;text-align:left}
                th{background:#f3f4f6}td.amount,th.amount{text-align:right}</style></head><body>
                <h3>${kpiModalState.title}</h3><div class="meta">${rows.length.toLocaleString()} record${rows.length > 1 ? 's' : ''} -- generated ${new Date().toLocaleDateString()}</div>
                <table><thead><tr><th>Voucher</th><th>Description</th><th>Date</th><th class="amount">Amount</th></tr></thead><tbody>
                ${rows.map(r => `<tr><td>${r.voucher ?? '-'}</td><td>${r.title}</td><td>${r.date}</td><td class="amount">৳${Number(r.amount).toLocaleString()}</td></tr>`).join('')}
                </tbody></table></body></html>`);
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

        /* ---------------- Cash Flow / Expense / Revenue (unchanged — আপনার existing code হুবহু রাখুন) ---------------- */
        // renderCashFlowLegend, renderCashFlowChart, polarToCartesian, describeArc,
        // expenseColorScale, renderExpenseConcentric, loadExpenseBreakdown,
        // renderRevenueLegend, renderRevenueChart — আগের version থেকে copy করুন

        /* ---------------- AR / AP Aging ---------------- */
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
        let arAgingRange = 'all',
            apAgingRange = 'all';
        let arBarInst = null,
            arDonutInst = null,
            apBarInst = null,
            apDonutInst = null;

        // FIX: ৫টা card সমান width-এর জন্য col-lg (auto)
        function agingKpiCard(label, value, icon, iconBg, iconColor) {
            return `
        <div class="col-6 col-lg">
            <div class="fin-kpi-card aging-kpi-card" style="cursor:default">
                <div class="aging-kpi-icon-wrap" style="background:${iconBg};color:${iconColor}"><i class="bi ${icon}"></i></div>
                <div class="fin-kpi-label mt-2">${label}</div>
                <div class="fin-kpi-value" style="font-size:20px;white-space:normal;word-break:break-word">৳${Number(value).toLocaleString()}</div>
            </div>
        </div>`;
        }

        // FIX: ৫ম card — advance amount
        function renderAgingKpis(kpiContainerId, kpis, prefix) {
            document.getElementById(kpiContainerId).innerHTML = [
                agingKpiCard(`Unpaid ${prefix} Amount`, kpis.unpaid_amount, 'bi-receipt', '#eff6ff', '#2563eb'),
                agingKpiCard('Overdue Amount', kpis.overdue_amount, 'bi-clock-history', '#fef2f2', '#dc2626'),
                agingKpiCard('Overdue 30+ Days', kpis.overdue_30_plus, 'bi-calendar-x', '#fff7ed', '#ea580c'),
                agingKpiCard('Overdue 90+ Days', kpis.overdue_90_plus, 'bi-exclamation-octagon', '#fdf2f8', '#be185d'),
                agingKpiCard(kpis.advance_label || 'Advances', kpis.advance_amount || 0, 'bi-wallet2', '#f0fdf4',
                    '#16a34a'),
            ].join('') + `<div class="col-12"><div class="aging-caption">in home currency</div></div>`;
        }

        // renderAgingCharts — আগের version হুবহু রাখুন

        // FIX: advance section + account name click → drill-down modal
        function renderAgingTable(containerId, type) {
            const state = agingState[type];
            const data = state.data;
            const searchTerm = (state.search || '').toLowerCase().trim();

            const firstCol = type === 'receivable' ? 'Customer' : 'Supplier';
            const mainTitle = type === 'receivable' ? 'Receivables' : 'Payables';
            const advTitle = type === 'receivable' ? 'Advance from Customers' : 'Advance to Suppliers';

            const filtered = (data.rows || []).filter(r => !searchTerm || r.name.toLowerCase().includes(searchTerm));
            const filteredAdv = (data.advance_rows || []).filter(r => !searchTerm || r.name.toLowerCase().includes(
                searchTerm));

            const calcGrand = rows => ({
                current: rows.reduce((s, r) => s + r.current, 0),
                d1_30: rows.reduce((s, r) => s + r.d1_30, 0),
                d31_60: rows.reduce((s, r) => s + r.d31_60, 0),
                d61_90: rows.reduce((s, r) => s + r.d61_90, 0),
                d91_plus: rows.reduce((s, r) => s + r.d91_plus, 0),
                total: rows.reduce((s, r) => s + r.total, 0),
            });

            const tableHtml = (title, rows) => {
                if (!rows.length) return '';
                const grand = calcGrand(rows);
                return `
                <div style="font-size:.95rem;margin:12px 0 6px;font-weight:700">${title}</div>
                <div class="table-responsive" style="max-height:420px;overflow-y:auto">
                    <table class="table table-sm kpi-detail-table aging-table">
                        <thead><tr><th>${firstCol}</th><th class="text-right">Current</th><th class="text-right">1-30</th><th class="text-right">31-60</th><th class="text-right">61-90</th><th class="text-right">91 and over</th><th class="text-right">Amount Due</th></tr></thead>
                        <tbody>
                            ${rows.map(r => `
                                    <tr>
                                        <td><a href="javascript:void(0)" class="aging-acc-link" data-child="${r.id}" data-isar="${type === 'receivable' ? 1 : 0}">${r.name}</a></td>
                                        <td class="text-right">${Number(r.current).toLocaleString()}</td>
                                        <td class="text-right">${Number(r.d1_30).toLocaleString()}</td>
                                        <td class="text-right">${Number(r.d31_60).toLocaleString()}</td>
                                        <td class="text-right">${Number(r.d61_90).toLocaleString()}</td>
                                        <td class="text-right">${Number(r.d91_plus).toLocaleString()}</td>
                                        <td class="text-right" style="font-weight:700">${Number(r.total).toLocaleString()}</td>
                                    </tr>`).join('')}
                        </tbody>
                        <tfoot><tr style="font-weight:700;background:#f9fafb"><td>Total</td><td class="text-right">${Number(grand.current).toLocaleString()}</td><td class="text-right">${Number(grand.d1_30).toLocaleString()}</td><td class="text-right">${Number(grand.d31_60).toLocaleString()}</td><td class="text-right">${Number(grand.d61_90).toLocaleString()}</td><td class="text-right">${Number(grand.d91_plus).toLocaleString()}</td><td class="text-right">${Number(grand.total).toLocaleString()}</td></tr></tfoot>
                    </table>
                </div>
                <div class="text-muted small text-center mt-1">${rows.length} record${rows.length > 1 ? 's' : ''}</div>`;
            };

            let html;
            if (!filtered.length && !filteredAdv.length) {
                html = `<div class="empty-state"><i class="bi bi-inbox"></i><p>No matching records</p></div>`;
            } else {
                html = tableHtml(mainTitle, filtered) + tableHtml(advTitle, filteredAdv);
            }

            const container = document.getElementById(containerId);
            container.innerHTML = html;

            // drill-down: name click → modal (axios-style fetch)
            container.querySelectorAll('.aging-acc-link').forEach(a => {
                a.addEventListener('click', () => openAgingAccountModal(a.dataset.child, a.dataset.isar, a
                    .textContent.trim(), type));
            });
        }

        /* ---------------- Aging drill-down modal (NEW) ---------------- */
        function openAgingAccountModal(childId, isAr, name, type) {
            const range = type === 'receivable' ? arAgingRange : apAgingRange;
            document.getElementById('agingAccountModalTitle').textContent = name;
            document.getElementById('agingAccountModalBody').innerHTML =
                `<div class="text-center text-muted py-3">Loading...</div>`;
            $('#agingAccountModal').modal('show');

            fetch(`${API_BASE}/aging-account-details?child_id=${childId}&is_ar=${isAr}&range=${range}`)
                .then(r => {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(res => renderAgingAccountModal(res, range))
                .catch(() => {
                    document.getElementById('agingAccountModalBody').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load account details</p></div>`;
                });
        }

        function renderAgingAccountModal(res, range) {
            const rangeLabels = {
                '7d': '7 Days',
                '30d': '30 Days',
                '90d': '90+ Days',
                'year': '1 Year',
                'all': 'All Time'
            };
            const badgeText = res.is_advance ?
                (res.is_ar ? 'Advance from Customer' : 'Advance to Supplier') :
                (res.is_ar ? 'Receivable' : 'Payable');

            let html = `
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap:8px">
                    <div>
                        <span class="badge ${res.is_advance ? 'badge-warning' : 'badge-success'}" style="font-size:.8rem">${badgeText}</span>
                        <span class="text-muted small ml-2">Range: ${rangeLabels[range] || range} &middot; ${res.txn_count} transaction(s)</span>
                    </div>
                    <div style="font-weight:700;font-size:1.05rem">Net Total: ৳${Number(res.net_total).toLocaleString()}</div>
                </div>`;

            let hasAny = false;
            res.buckets.forEach(b => {
                if (!b.transactions.length) return;
                hasAny = true;
                html += `
                <div style="font-weight:700;margin:14px 0 6px">${b.label}
                    <span class="text-muted" style="font-weight:400;font-size:.85rem">— Subtotal: ৳${Number(b.total).toLocaleString()}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm kpi-detail-table">
                        <thead><tr><th>Date</th><th>Voucher</th><th>Type</th><th>Remark</th><th class="text-right">Debit</th><th class="text-right">Credit</th><th class="text-right">Net</th></tr></thead>
                        <tbody>
                            ${b.transactions.map(t => `
                                    <tr>
                                        <td style="white-space:nowrap">${t.date}</td>
                                        <td>${t.voucher}</td>
                                        <td>${t.type}</td>
                                        <td>${t.remark}</td>
                                        <td class="text-right">${t.debit === null ? '-' : Number(t.debit).toLocaleString()}</td>
                                        <td class="text-right">${t.credit === null ? '-' : Number(t.credit).toLocaleString()}</td>
                                        <td class="text-right" style="font-weight:600;color:${t.amount < 0 ? '#dc2626' : '#111827'}">${Number(t.amount).toLocaleString()}</td>
                                    </tr>`).join('')}
                        </tbody>
                    </table>
                </div>`;
            });

            if (!hasAny) html +=
                `<div class="empty-state"><i class="bi bi-inbox"></i><p>No transactions in this range</p></div>`;

            document.getElementById('agingAccountModalBody').innerHTML = html;
        }

        document.getElementById('arAgingSearch').addEventListener('input', function() {
            agingState.receivable.search = this.value;
            renderAgingTable('arAgingBody', 'receivable');
        });
        document.getElementById('apAgingSearch').addEventListener('input', function() {
            agingState.payable.search = this.value;
            renderAgingTable('apAgingBody', 'payable');
        });

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

        function loadArApAging() {
            loadArAging(arAgingRange);
            loadApAging(apAgingRange);
        }

        document.querySelectorAll('#arAgingRangeFilter .fin-range-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#arAgingRangeFilter .fin-range-btn').forEach(b => b.classList
                    .remove('active'));
                this.classList.add('active');
                arAgingRange = this.dataset.arrange;
                loadArAging(arAgingRange);
            });
        });
        document.querySelectorAll('#apAgingRangeFilter .fin-range-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#apAgingRangeFilter .fin-range-btn').forEach(b => b.classList
                    .remove('active'));
                this.classList.add('active');
                apAgingRange = this.dataset.arrange;
                loadApAging(apAgingRange);
            });
        });

        /* ---------------- Bank/Cash, Transactions, Invoices — আপনার existing code হুবহু রাখুন ---------------- */

        /* ---------------- Init ---------------- */
        activateTab('overview');
    </script>
@endsection
