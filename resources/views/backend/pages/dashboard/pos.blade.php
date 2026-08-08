{{-- resources/views/backend/pages/dashboard/pos.blade.php --}}
{{-- Modified: 2025-08-08 -- Paid/Due KPI, payment status badge, top due
     customers panel jog kora holo (account_transactions accounting flow onujayi) --}}

@extends('backend.layouts.master')

@section('title')
    {{ $pgTitle }} - Admin Panel
@endsection

<link rel="stylesheet"
    href="{{ asset('css/dashboard-style.css') }}?v={{ filemtime(public_path('css/dashboard-style.css')) }}">

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
    <div class="dashboard-wrap">

        {{-- Global Date Filter --}}
        <div class="d-flex justify-content-end mb-3">
            <div class="btn-group" role="group" id="posGlobalFilter">
                <button type="button" class="btn btn-sm btn-outline-primary filter-btn active"
                    data-filter="today">Today</button>
                <button type="button" class="btn btn-sm btn-outline-primary filter-btn" data-filter="week">This
                    Week</button>
                <button type="button" class="btn btn-sm btn-outline-primary filter-btn" data-filter="month">This
                    Month</button>
                <button type="button" class="btn btn-sm btn-outline-primary filter-btn" data-filter="year">This
                    Year</button>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="row g-3 mb-4" id="posMetrics"></div>

        <div class="row g-3 mb-4">
            <div class="col-lg-8">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-graph-up"></i> Sales Trend</div>
                    <div class="panel-body">
                        <canvas id="salesTrendChart" height="110"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-pie-chart"></i> Paid vs Due</div>
                    <div class="panel-body">
                        <canvas id="paymentChart" height="220"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-box-seam"></i> Top Selling Products</div>
                    <div class="panel-body">
                        <div class="scroll-box" id="topProductsList"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-receipt"></i> Recent Transactions</div>
                    <div class="panel-body">
                        <div class="scroll-box" id="recentTransactionsList"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-person-badge"></i> Cashier Performance</div>
                    <div class="panel-body">
                        <div class="scroll-box" id="cashierPerformanceList"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-person-exclamation"></i> Top Due Customers</div>
                    <div class="panel-body">
                        <div class="scroll-box" id="topDueCustomersList"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        /* =========================================================================
                   HRM Dashboard er moto -- API theke shob data fetch kora hocche.
                   Global filter (today/week/month/year) shob API call e query param
                   hishebe pass hocche.
                   ========================================================================= */

        const API_BASE = '/api/pos-dashboard';
        let currentFilter = 'today'; // default global filter

        let salesTrendChart = null;
        let paymentChart = null;

        /* ---------------- Shared helpers ---------------- */
        const fmtMoney = (n) => Number(n || 0).toLocaleString('en-US', {
            minimumFractionDigits: 0
        });
        const fmtDateTime = (d) => new Date(d).toLocaleString('en-US', {
            day: '2-digit',
            month: 'short',
            hour: '2-digit',
            minute: '2-digit'
        });

        function metricCard({
            title,
            value,
            sub,
            icon,
            theme
        }) {
            return `
  <div class="col-6 col-lg-3">
    <div class="metric-card card-${theme}">
      <div class="metric-top">
        <div class="metric-title">${title}</div>
        <i class="bi ${icon} metric-icon fs-5"></i>
      </div>
      <div class="metric-value">${value}</div>
      <div class="metric-sub">${sub}</div>
    </div>
  </div>`;
        }

        /* ---------------- KPI Cards ---------------- */
        function loadKpis() {
            fetch(`${API_BASE}/kpis?filter=${currentFilter}`)
                .then(r => r.json())
                .then(kpi => {
                    document.getElementById('posMetrics').innerHTML = [
                        metricCard({
                            title: 'Total Sales',
                            value: fmtMoney(kpi.total_sales_amount),
                            sub: `${kpi.total_transactions} transactions`,
                            icon: 'bi-cash-stack',
                            theme: 'blue'
                        }),
                        metricCard({
                            title: 'Avg. Order Value',
                            value: fmtMoney(kpi.average_order_value),
                            sub: '',
                            icon: 'bi-graph-up-arrow',
                            theme: 'teal'
                        }),
                        metricCard({
                            title: 'Paid',
                            value: fmtMoney(kpi.paid_amount),
                            sub: `${kpi.collection_rate}% collection rate`,
                            icon: 'bi-check-circle',
                            theme: 'green'
                        }),
                        metricCard({
                            title: 'Due',
                            value: fmtMoney(kpi.due_amount),
                            sub: '',
                            icon: 'bi-exclamation-circle',
                            theme: 'red'
                        })
                    ].join('');
                })
                .catch(() => {
                    document.getElementById('posMetrics').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load KPI data</p></div>`;
                });
        }

        /* ---------------- Sales Trend Chart ---------------- */
        function loadSalesTrend() {
            fetch(`${API_BASE}/sales-trend?filter=${currentFilter}`)
                .then(r => r.json())
                .then(res => {
                    const ctx = document.getElementById('salesTrendChart').getContext('2d');
                    if (salesTrendChart) salesTrendChart.destroy();

                    salesTrendChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: res.labels,
                            datasets: [{
                                label: 'Sales',
                                data: res.values,
                                borderColor: '#4e73df',
                                backgroundColor: 'rgba(78,115,223,0.1)',
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                })
                .catch(() => {});
        }

        /* ---------------- Paid vs Due Chart ---------------- */
        function loadPaymentBreakdown() {
            fetch(`${API_BASE}/payment-breakdown?filter=${currentFilter}`)
                .then(r => r.json())
                .then(data => {
                    const labels = data.map(item => item.method);
                    const values = data.map(item => parseFloat(item.amount));

                    const ctx = document.getElementById('paymentChart').getContext('2d');
                    if (paymentChart) paymentChart.destroy();

                    paymentChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                backgroundColor: ['#1cc88a', '#e74a3b']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                })
                .catch(() => {});
        }

        /* ---------------- Top Products ---------------- */
        function loadTopProducts() {
            fetch(`${API_BASE}/top-products?filter=${currentFilter}`)
                .then(r => r.json())
                .then(data => {
                    const box = document.getElementById('topProductsList');
                    box.innerHTML = data.length ? data.map(p => `
  <div class="person-row">
    <div>
      <div class="person-name">${p.name}</div>
      <div class="person-sub">Qty: ${p.total_qty}</div>
    </div>
    <div class="text-muted small">${fmtMoney(p.total_amount)}</div>
  </div>`).join('') :
                        `<div class="empty-state"><i class="bi bi-box-seam"></i><p>No sales in this period</p></div>`;
                })
                .catch(() => {
                    document.getElementById('topProductsList').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load products</p></div>`;
                });
        }

        /* ---------------- Recent Transactions -- Paid/Due badge shoho ---------------- */
        function loadRecentTransactions() {
            fetch(`${API_BASE}/recent-transactions?filter=${currentFilter}`)
                .then(r => r.json())
                .then(data => {
                    const box = document.getElementById('recentTransactionsList');
                    box.innerHTML = data.length ? data.map(t => `
  <div class="person-row">
    <div>
      <div class="person-name">${t.invoice_no}</div>
      <div class="person-sub">${t.customer_name} &middot; ${fmtDateTime(t.created_at)}</div>
    </div>
    <div class="text-right">
      <div class="text-muted small mb-1">${fmtMoney(t.grand_total)}</div>
      <span class="badge ${t.payment_status === 'Paid' ? 'badge-success' : 'badge-danger'}">${t.payment_status}</span>
    </div>
  </div>`).join('') :
                        `<div class="empty-state"><i class="bi bi-receipt"></i><p>No transactions in this period</p></div>`;
                })
                .catch(() => {
                    document.getElementById('recentTransactionsList').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load transactions</p></div>`;
                });
        }

        /* ---------------- Cashier Performance ---------------- */
        function loadCashierPerformance() {
            fetch(`${API_BASE}/cashier-performance?filter=${currentFilter}`)
                .then(r => r.json())
                .then(data => {
                    const box = document.getElementById('cashierPerformanceList');
                    box.innerHTML = data.length ? `
  <table class="table table-sm mb-0">
    <thead><tr><th>Cashier</th><th>Transactions</th><th>Total Sales</th></tr></thead>
    <tbody>
      ${data.map(c => `<tr><td>${c.name}</td><td>${c.txn_count}</td><td>${fmtMoney(c.total_sales)}</td></tr>`).join('')}
    </tbody>
  </table>` :
                        `<div class="empty-state"><i class="bi bi-person-badge"></i><p>No cashier data found</p></div>`;
                })
                .catch(() => {
                    document.getElementById('cashierPerformanceList').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load cashier data</p></div>`;
                });
        }

        /* ---------------- Top Due Customers ---------------- */
        function loadTopDueCustomers() {
            fetch(`${API_BASE}/top-due-customers?filter=${currentFilter}`)
                .then(r => r.json())
                .then(data => {
                    const box = document.getElementById('topDueCustomersList');
                    box.innerHTML = data.length ? data.map(c => `
  <div class="person-row">
    <div class="person-name">${c.name}</div>
    <div class="text-danger small">${fmtMoney(c.due_amount)}</div>
  </div>`).join('') :
                        `<div class="empty-state"><i class="bi bi-check-circle"></i><p>No dues found in this period</p></div>`;
                })
                .catch(() => {
                    document.getElementById('topDueCustomersList').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load due customers</p></div>`;
                });
        }

        /* ---------------- Load All Sections ---------------- */
        function loadAllSections() {
            loadKpis();
            loadSalesTrend();
            loadPaymentBreakdown();
            loadTopProducts();
            loadRecentTransactions();
            loadCashierPerformance();
            loadTopDueCustomers();
        }

        /* ---------------- Global Filter Button Click ---------------- */
        document.querySelectorAll('#posGlobalFilter .filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#posGlobalFilter .filter-btn').forEach(b => b.classList
                    .remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.filter;
                loadAllSections();
            });
        });

        // Initial load -- default: today
        loadAllSections();
    </script>
@endsection
