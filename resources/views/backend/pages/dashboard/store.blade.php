@extends('backend.layouts.master')

@section('title')
    {{ $pgTitle }} - Admin Panel
@endsection

<link rel="stylesheet"
    href="{{ asset('css/dashboard-style.css') }}?v={{ filemtime(public_path('css/dashboard-style.css')) }}">

<style>
    .dashboard-wrap .scroll-box {
        height: auto !important;
        overflow-y: auto;
        padding-right: 6px;
    }
</style>
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

        <div class="row g-3 mb-4" id="primaryMetrics"></div>

        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-building"></i> Warehouse-wise Stock Value</div>
                    <div class="panel-body">
                        <div class="scroll-box" id="warehouseDistribution"></div>
                    </div>
                    <hr class="my-2">
                    <div class="panel-header"><i class="bi bi-diagram-3"></i> Branch-wise Stock Value</div>
                    <div class="panel-body">
                        <div class="scroll-box" id="branchWiseDistribution"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-briefcase"></i> Quick Actions</div>
                    <div class="panel-body">
                        <div class="scroll-box" id="quickActions"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-exclamation-triangle"></i> Low Stock Items</div>
                    <div class="panel-body">
                        <div class="scroll-box" id="lowStockList"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header">
                        <i class="bi bi-arrow-left-right"></i>
                        @if ($isAdmin)
                            Stock Movement Today
                        @else
                            Stock Movement (Last 30 Days)
                        @endif
                    </div>
                    <div class="panel-body">
                        <div class="scroll-box" id="stockMovement"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-12">
                <div class="panel h-100" id="recentTxnPanel">
                    <div class="panel-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-clock-history"></i> Recent Stock Transactions</span>
                        <button type="button" class="panel-expand-btn" id="recentTxnExpandBtn" title="Open full page">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </button>
                    </div>
                    <div class="panel-body">
                        <div class="scroll-box" id="recentTransactions"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="kpiDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kpiDetailModalTitle">Details</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="kpiDetailModalBody">
                    <div class="text-center text-muted py-3">Loading...</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Branch Stock Breakdown Modal, Print + Excel export shoho --}}
    <div class="modal fade" id="branchStockModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="branchStockModalTitle">Stock Breakdown</h5>
                    <div>
                        <button type="button" id="branchStockPrintBtn" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-printer"></i> Print
                        </button>
                        <button type="button" id="branchStockExcelBtn" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </button>
                        <button type="button" class="close" data-dismiss="modal"
                            style="margin-left:10px;">&times;</button>
                    </div>
                </div>
                <div class="modal-body" id="branchStockModalBody">
                    <div class="text-center text-muted py-3">Loading...</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        const isAdmin = @json($isAdmin);
        const API_BASE = '/api/store-dashboard';

        function metricCard({
            title,
            value,
            sub,
            icon,
            theme,
            type
        }) {
            return `<div class="col-6 col-lg-3">
      <div class="metric-card card-${theme} kpi-clickable" style="cursor:pointer" data-kpi-type="${type}">
        <div class="metric-top"><div class="metric-title">${title}</div><i class="bi ${icon} metric-icon fs-5"></i></div>
        <div class="metric-value">${value}</div>
        <div class="metric-sub">${sub}</div>
      </div></div>`;
        }

        function bindKpiClicks() {
            document.querySelectorAll('.kpi-clickable').forEach(el => {
                el.addEventListener('click', () => {
                    const type = el.dataset.kpiType;
                    if (!type) return;
                    openKpiDetailModal(type, el.querySelector('.metric-title').textContent);
                });
            });
        }

        function openKpiDetailModal(type, title) {
            document.getElementById('kpiDetailModalTitle').textContent = title;
            document.getElementById('kpiDetailModalBody').innerHTML =
                `<div class="text-center text-muted py-3">Loading...</div>`;
            $('#kpiDetailModal').modal('show');

            fetch(`${API_BASE}/kpi-details?type=${type}`)
                .then(r => r.json())
                .then(rows => {
                    const body = document.getElementById('kpiDetailModalBody');
                    body.innerHTML = rows.length ?
                        `<ul class="list-group">${rows.map(r => `
                                                                                                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                                                                        <span>${r.title}</span><span class="text-muted small">${r.subtitle}</span>
                                                                                                                                    </li>`).join('')}</ul>` :
                        `<div class="empty-state"><i class="bi bi-inbox"></i><p>No records found</p></div>`;
                })
                .catch(() => {
                    document.getElementById('kpiDetailModalBody').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load details</p></div>`;
                });
        }

        /* ---- Point 1: KPI Cards ---- */


        fetch(`${API_BASE}/kpis`).then(r => r.json()).then(k => {
            document.getElementById('primaryMetrics').innerHTML = [
                metricCard({
                    title: 'Total SKU',
                    value: k.total_sku,
                    sub: '৳ ' + Number(k.total_stock_value).toLocaleString(),
                    icon: 'bi-box-seam',
                    theme: 'blue',
                    type: ''
                }),
                metricCard({
                    title: 'Stock In Today',
                    value: k.stock_in_today,
                    sub: 'Units received',
                    icon: 'bi-box-arrow-in-down',
                    theme: 'green',
                    type: 'stock_in_today'
                }),
                metricCard({
                    title: 'Stock Out Today',
                    value: k.stock_out_today,
                    sub: 'Units issued',
                    icon: 'bi-box-arrow-up',
                    theme: 'teal',
                    type: 'stock_out_today'
                }),
                metricCard({
                    title: 'Low Stock',
                    value: k.low_stock_count,
                    sub: 'Need reorder',
                    icon: 'bi-exclamation-triangle',
                    theme: 'purple',
                    type: 'low_stock'
                }),
                metricCard({
                    title: 'Out of Stock',
                    value: k.out_of_stock_count,
                    sub: '',
                    icon: 'bi-x-circle',
                    theme: 'red',
                    type: 'out_of_stock'
                }),
                metricCard({
                    title: 'Branches',
                    value: k.branchs,
                    icon: 'bi-diagram-3',
                    theme: 'indigo',
                    type: 'branches'
                }),
                metricCard({
                    title: 'Warehouses',
                    value: k.warehouses,
                    icon: 'bi-building',
                    theme: 'indigo',
                    type: 'warehouses'
                }),
            ].join('');
            bindKpiClicks();
        }).catch(() => {
            document.getElementById('primaryMetrics').innerHTML =
                `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load KPI data</p></div>`;
        });

        /* ---- Point 2: Warehouse Distribution ---- */
        /* ---- Point 2: Warehouse & Branch Distribution ---- */
        function renderDistribution(containerId, apiPath) {
            fetch(`${API_BASE}/${apiPath}`).then(r => r.json()).then(data => {
                const box = document.getElementById(containerId);
                box.innerHTML = data.length ? `<div class="dept-grid">${data.map(d => {
            const color = d.present_percent >= 40 ? '#16a34a' : d.present_percent >= 15 ? '#f59e0b' : '#94a3b8';
            return `<div class="dept-card dept-card-clickable" style="cursor:pointer" data-branch-id="${d.id}" title="${d.name}: ৳${Number(d.total).toLocaleString()}">
                                    <div class="dept-circle" style="background:conic-gradient(${color} ${d.present_percent}%, #e5e7eb ${d.present_percent}% 100%);">
                                        <div class="dept-circle-inner">${d.present_percent}%</div>
                                    </div>
                                    <div class="dept-card-name">${d.name}</div>
                                    <div class="dept-card-sub">৳${Number(d.total).toLocaleString()}</div>
                                </div>`;
        }).join('')}</div>` :
                    `<div class="empty-state"><i class="bi bi-building"></i><p>No data found</p></div>`;

                bindBranchCardClicks(containerId);
            }).catch(() => {
                document.getElementById(containerId).innerHTML =
                    `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load</p></div>`;
            });
        }

        function bindBranchCardClicks(containerId) {
            document.querySelectorAll(`#${containerId} .dept-card-clickable`).forEach(el => {
                el.addEventListener('click', () => {
                    openBranchStockModal(el.dataset.branchId);
                });
            });
        }

        renderDistribution('warehouseDistribution', 'warehouse-distribution');
        renderDistribution('branchWiseDistribution', 'branch-distribution');

        function bindBranchCardClicks() {
            document.querySelectorAll('.dept-card-clickable').forEach(el => {
                el.addEventListener('click', () => {
                    openBranchStockModal(el.dataset.branchId);
                });
            });
        }

        let currentBranchRows = [];
        let filteredBranchRows = [];
        let currentBranchTitle = '';
        let currentPage = 1;
        const PAGE_SIZE = 100;

        function openBranchStockModal(warehouseId) {
            document.getElementById('branchStockModalBody').innerHTML =
                `<div class="text-center text-muted py-3">Loading...</div>`;
            $('#branchStockModal').modal('show');

            fetch(`${API_BASE}/warehouse-stock-details?warehouse_id=${warehouseId}`)
                .then(r => r.json())
                .then(data => {
                    currentBranchRows = data.rows;
                    filteredBranchRows = data.rows;
                    currentBranchTitle = data.warehouse_name + ' - Stock Breakdown';
                    currentPage = 1;

                    document.getElementById('branchStockModalTitle').textContent = currentBranchTitle;

                    if (!data.rows.length) {
                        document.getElementById('branchStockModalBody').innerHTML =
                            `<div class="empty-state"><i class="bi bi-inbox"></i><p>No stock found</p></div>`;
                        return;
                    }

                    renderBranchStockPage(data.grand_total);
                })
                .catch(() => {
                    document.getElementById('branchStockModalBody').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load details</p></div>`;
                });
        }

        function applyBranchStockSearch(query, grandTotal) {
            const q = query.trim().toLowerCase();
            filteredBranchRows = !q ? currentBranchRows : currentBranchRows.filter(r =>
                (r.product_code || '').toLowerCase().includes(q) ||
                (r.product || '').toLowerCase().includes(q) ||
                (r.category || '').toLowerCase().includes(q)
            );
            currentPage = 1;
            renderBranchStockTable(grandTotal);
        }

        function renderBranchStockPage(grandTotal) {
            const body = document.getElementById('branchStockModalBody');
            body.innerHTML = `
                <div class="mb-2">
                    <input type="text" id="branchStockSearch" class="form-control form-control-sm"
                        placeholder="Search by Product Code, Name, or Category...">
                </div>
                <div id="branchStockTableWrap"></div>
            `;

            document.getElementById('branchStockSearch').addEventListener('input', function() {
                applyBranchStockSearch(this.value, grandTotal);
            });

            renderBranchStockTable(grandTotal);
        }

        function renderBranchStockTable(grandTotal) {
            const totalPages = Math.ceil(filteredBranchRows.length / PAGE_SIZE) || 1;
            const start = (currentPage - 1) * PAGE_SIZE;
            const pageRows = filteredBranchRows.slice(start, start + PAGE_SIZE);

            const wrap = document.getElementById('branchStockTableWrap');

            if (!filteredBranchRows.length) {
                wrap.innerHTML =
                    `<div class="empty-state"><i class="bi bi-search"></i><p>No matching products found</p></div>`;
                return;
            }

            wrap.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Showing ${start + 1}-${start + pageRows.length} of ${filteredBranchRows.length}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="branchStockTable">
                        <thead>
                            <tr>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Avg Unit Price</th>
                                <th class="text-right">Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${pageRows.map(r => `
                                                                                                                                        <tr>
                                                                                                                                            <td>${r.product_code}</td>
                                                                                                                                            <td>${r.product}</td>
                                                                                                                                            <td>${r.category}</td>
                                                                                                                                            <td class="text-right">${r.qty}</td>
                                                                                                                                            <td class="text-right">${Number(r.avg_price).toLocaleString()}</td>
                                                                                                                                            <td class="text-right">${Number(r.total).toLocaleString()}</td>
                                                                                                                                        </tr>`).join('')}
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-right">Grand Total (All Pages)</th>
                                <th class="text-right">৳ ${Number(grandTotal).toLocaleString()}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                ${totalPages > 1 ? renderPaginationControls(totalPages) : ''}
            `;

            if (totalPages > 1) {
                document.querySelectorAll('.branch-page-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        currentPage = parseInt(btn.dataset.page);
                        renderBranchStockTable(grandTotal);
                    });
                });
            }
        }

        function renderPaginationControls(totalPages) {
            let buttons = '';
            for (let p = 1; p <= totalPages; p++) {
                buttons +=
                    `<button type="button" class="btn btn-sm ${p === currentPage ? 'btn-primary' : 'btn-outline-secondary'} branch-page-btn" data-page="${p}">${p}</button>`;
            }
            return `<div class="d-flex gap-1 flex-wrap justify-content-center mt-2">${buttons}</div>`;
        }

        function askExportScope(callback) {
            const totalPages = Math.ceil(currentBranchRows.length / PAGE_SIZE);
            if (totalPages <= 1) {
                callback('all');
                return;
            }

            const choiceHtml = `
                <div class="modal fade" id="exportScopeModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Export Range</h6>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body text-center">
                                <button type="button" class="btn btn-outline-primary btn-block mb-2" id="exportScopeAll">
                                    All Data (${currentBranchRows.length} rows)
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-block" id="exportScopeThisPage">
                                    This Page Only (Page ${currentPage})
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;

            document.getElementById('exportScopeModal')?.remove();
            document.body.insertAdjacentHTML('beforeend', choiceHtml);

            $('#exportScopeModal').modal('show');

            document.getElementById('exportScopeAll').addEventListener('click', () => {
                $('#exportScopeModal').modal('hide');
                callback('all');
            });
            document.getElementById('exportScopeThisPage').addEventListener('click', () => {
                $('#exportScopeModal').modal('hide');
                callback('page');
            });
        }

        function getExportRows(scope) {
            if (scope === 'all') return currentBranchRows;
            const start = (currentPage - 1) * PAGE_SIZE;
            return currentBranchRows.slice(start, start + PAGE_SIZE);
        }

        document.getElementById('branchStockPrintBtn').addEventListener('click', function() {
            if (!currentBranchRows.length) return;

            askExportScope(function(scope) {
                const rows = getExportRows(scope);
                const total = rows.reduce((sum, r) => sum + Number(r.total), 0);

                const tableHtml = `
                    <table>
                        <thead>
                            <tr><th>Product Code</th><th>Product Name</th><th>Category</th><th>Qty</th><th>Avg Unit Price</th><th>Total Value</th></tr>
                        </thead>
                        <tbody>
                            ${rows.map(r => `
                                                                                                                                        <tr>
                                                                                                                                            <td>${r.product_code}</td>
                                                                                                                                            <td>${r.product}</td>
                                                                                                                                            <td>${r.category}</td>
                                                                                                                                            <td class="text-right">${r.qty}</td>
                                                                                                                                            <td class="text-right">${Number(r.avg_price).toLocaleString()}</td>
                                                                                                                                            <td class="text-right">${Number(r.total).toLocaleString()}</td>
                                                                                                                                        </tr>`).join('')}
                        </tbody>
                        <tfoot>
                            <tr><th colspan="5" class="text-right">Total</th><th class="text-right">৳ ${total.toLocaleString()}</th></tr>
                        </tfoot>
                    </table>`;

                const printWin = window.open('', '_blank');
                printWin.document.write(`
                    <html><head><title>${currentBranchTitle}</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid #ccc; padding: 6px 10px; font-size: 13px; }
                        th { background: #f1f5f9; }
                        .text-right { text-align: right; }
                    </style>
                    </head><body>
                    <h3>${currentBranchTitle}</h3>
                    <p style="color:#666;font-size:12px;">${scope === 'all' ? `All ${rows.length} items` : `Page ${currentPage} (${rows.length} items)`}</p>
                    ${tableHtml}
                    </body></html>`);
                printWin.document.close();
                printWin.focus();
                printWin.print();
            });
        });

        document.getElementById('branchStockExcelBtn').addEventListener('click', function() {
            if (!currentBranchRows.length) return;

            askExportScope(function(scope) {
                const rows = getExportRows(scope);

                const exportData = rows.map(r => ({
                    'Product Code': r.product_code,
                    'Product Name': r.product,
                    'Category': r.category,
                    'Qty': r.qty,
                    'Avg Unit Price': r.avg_price,
                    'Total Value': r.total,
                }));

                const ws = XLSX.utils.json_to_sheet(exportData);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Stock');
                const suffix = scope === 'all' ? 'all' : `page${currentPage}`;
                XLSX.writeFile(wb, `${currentBranchTitle.replace(/[^a-z0-9]/gi, '_')}_${suffix}.xlsx`);
            });
        });

        /* ---- Point 3: Quick Actions ---- */
        fetch(`${API_BASE}/quick-actions`).then(r => r.json()).then(actions => {
            document.getElementById('quickActions').innerHTML = actions.length ?
                actions.map(a =>
                    `<button class="quick-btn" type="button" onclick="location.href='${a.url}'"><i class="bi ${a.icon}"></i> ${a.label}</button>`
                ).join('') :
                `<div class="empty-state"><i class="bi bi-briefcase"></i><p>No actions available</p></div>`;
        }).catch(() => {
            document.getElementById('quickActions').innerHTML =
                `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load</p></div>`;
        });

        /* ---- Point 4: Low Stock Items ---- */
        fetch(`${API_BASE}/low-stock-items`).then(r => r.json()).then(data => {
            const box = document.getElementById('lowStockList');
            box.innerHTML = data.length ? data.map((i) => `
                <div class="person-row">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar" style="background:${i.severity === 'out' ? '#ef4444' : '#f59e0b'}"><i class="bi bi-box-seam"></i></div>
                        <div><div class="person-name">${i.name}</div><div class="person-sub">${i.sub}</div></div>
                    </div>
                </div>`).join('') :
                `<div class="empty-state"><i class="bi bi-check-circle"></i><p>No low stock items</p></div>`;
        }).catch(() => {
            document.getElementById('lowStockList').innerHTML =
                `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load</p></div>`;
        });

        /* ---- Point 5: Stock Movement ---- */
        function loadStockMovement(params = {}) {
            const query = new URLSearchParams(params).toString();
            fetch(`${API_BASE}/stock-movement` + (query ? '?' + query : ''))
                .then(r => r.json())
                .then(data => {
                    const box = document.getElementById('stockMovement');
                    box.innerHTML = data.length ? data.map(s => {
                            const color = s.direction === 'in' ? '#16a34a' : s.direction === 'out' ? '#dc2626' :
                                '#94a3b8';
                            const icon = s.direction === 'in' ? 'bi-arrow-down' : s.direction === 'out' ?
                                'bi-arrow-up' : 'bi-arrow-left-right';
                            return `<div class="person-row">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar" style="background:${color}"><i class="bi ${icon}"></i></div>
                                    <div><div class="person-name">${s.product}</div><div class="person-sub">${s.status} - ${s.branch} - Qty: ${s.quantity}</div></div>
                                </div>
                                <div class="text-muted small">${s.date}</div>
                            </div>`;
                        }).join('') :
                        `<div class="empty-state"><i class="bi bi-inbox"></i><p>No movement found</p></div>`;
                })
                .catch(() => {
                    document.getElementById('stockMovement').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load</p></div>`;
                });
        }
        loadStockMovement();

        if (isAdmin) {
            fetch(`${API_BASE}/warehouse-options`).then(r => r.json()).then(branches => {
                const filterHtml = `<div class="d-flex gap-2 mb-2">
                    <select id="movBranch" class="form-control form-control-sm">
                        <option value="">All Branches</option>
                        ${branches.map(b => `<option value="${b.id}">${b.name}</option>`).join('')}
                    </select>
                    <input type="text" id="movSearch" class="form-control form-control-sm" placeholder="Search product...">
                </div>`;
                document.getElementById('stockMovement').insertAdjacentHTML('beforebegin', filterHtml);

                const applyFilter = () => loadStockMovement({
                    warehouse_id: document.getElementById('movBranch').value,
                    search: document.getElementById('movSearch').value
                });
                document.getElementById('movBranch').addEventListener('change', applyFilter);
                document.getElementById('movSearch').addEventListener('input', applyFilter);
            }).catch(() => {});
        }

        /* ---- Point 6: Recent Transactions -- filter bar + fullscreen + responsive rows ---- */
        let txnFilters = {
            from_date: '{{ Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}',
            to_date: '{{ Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}',
            type: '',
            search: ''
        };

        function renderTxnFilterBar() {
            return `
                <div class="txn-filter-bar">
                    <input type="date" id="txnFrom" class="form-control form-control-sm" value="${txnFilters.from_date}">
                    <input type="date" id="txnTo" class="form-control form-control-sm" value="${txnFilters.to_date}">
                    <select id="txnType" class="form-control form-control-sm">
                        <option value="">All Types</option>
                        <option value="in">Stock In</option>
                        <option value="out">Stock Out</option>
                    </select>
                    <input type="text" id="txnSearch" class="form-control form-control-sm" placeholder="Search product or voucher no...">
                </div>`;
        }

        function loadRecentTransactions() {
            const box = document.getElementById('recentTransactions');
            box.innerHTML = renderTxnFilterBar() +
                `<div id="txnListWrap"><div class="text-center text-muted py-3">Loading...</div></div>`;

            document.getElementById('txnFrom').addEventListener('change', e => {
                txnFilters.from_date = e.target.value;
                fetchTxnList();
            });
            document.getElementById('txnTo').addEventListener('change', e => {
                txnFilters.to_date = e.target.value;
                fetchTxnList();
            });
            document.getElementById('txnType').addEventListener('change', e => {
                txnFilters.type = e.target.value;
                fetchTxnList();
            });
            document.getElementById('txnSearch').addEventListener('input', debounce(e => {
                txnFilters.search = e.target.value;
                fetchTxnList();
            }, 400));

            fetchTxnList();
        }

        function debounce(fn, delay) {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => fn(...args), delay);
            };
        }

        function fetchTxnList() {
            const query = new URLSearchParams(txnFilters).toString();
            const wrap = document.getElementById('txnListWrap');
            wrap.innerHTML = `<div class="text-center text-muted py-3">Loading...</div>`;

            fetch(`${API_BASE}/recent-transactions?${query}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.length) {
                        wrap.innerHTML =
                            `<div class="empty-state"><i class="bi bi-clock-history"></i><p>No transactions found</p></div>`;
                        return;
                    }

                    const header = `
                        <div class="txn-table-header">
                            <div></div>
                            <div>Product</div>
                            <div>Branch</div>
                            <div style="text-align:right">Qty</div>
                            <div style="text-align:right">Date</div>
                        </div>`;

                    const rows = data.map(t => {
                        const color = t.direction === 'in' ? '#16a34a' : t.direction === 'out' ? '#dc2626' :
                            '#94a3b8';
                        const icon = t.direction === 'in' ? 'bi-arrow-down' : t.direction === 'out' ?
                            'bi-arrow-up' : 'bi-arrow-left-right';
                        const qtySign = t.direction === 'in' ? '+' : t.direction === 'out' ? '-' : '';

                        return `
                            <div class="txn-row">
                                <div class="txn-top">
                                    <div class="txn-icon" style="background:${color}"><i class="bi ${icon}"></i></div>
                                    <div style="min-width:0; flex:1">
                                        <div class="txn-title">${t.title}</div>
                                        <div class="txn-sub">
                                            <span>${t.status}</span>
                                            <span class="txn-voucher"><i class="bi bi-receipt"></i> ${t.voucher}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="txn-branch">
                                    <span class="txn-field-label">Branch</span>${t.branch}
                                </div>
                                <div class="txn-qty" style="color:${color}">
                                    <span class="txn-field-label">Quantity</span>${qtySign}${t.quantity}
                                </div>
                                <div class="txn-date">
                                    <span class="txn-field-label">Date</span>${t.date}
                                </div>
                            </div>`;
                    }).join('');

                    wrap.innerHTML = header + rows;
                })
                .catch(() => {
                    wrap.innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load</p></div>`;
                });
        }

        loadRecentTransactions();

        // Generic fullscreen toggle (reusable pattern for any panel)
        let txnFullscreenBackdrop = null;

        document.getElementById('recentTxnExpandBtn').addEventListener('click', function() {
            const panel = document.getElementById('recentTxnPanel');
            const isFullscreen = panel.classList.contains('panel-fullscreen');

            if (isFullscreen) {
                panel.classList.remove('panel-fullscreen');
                txnFullscreenBackdrop?.remove();
                txnFullscreenBackdrop = null;
                this.querySelector('i').className = 'bi bi-arrows-fullscreen';
                this.title = 'Open full page';
            } else {
                panel.classList.add('panel-fullscreen');
                txnFullscreenBackdrop = document.createElement('div');
                txnFullscreenBackdrop.className = 'panel-fullscreen-backdrop';
                document.body.appendChild(txnFullscreenBackdrop);
                this.querySelector('i').className = 'bi bi-fullscreen-exit';
                this.title = 'Exit full page';

                txnFullscreenBackdrop.addEventListener('click', () => this.click());
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const panel = document.getElementById('recentTxnPanel');
                if (panel.classList.contains('panel-fullscreen')) {
                    document.getElementById('recentTxnExpandBtn').click();
                }
            }
        });
    </script>
@endsection
