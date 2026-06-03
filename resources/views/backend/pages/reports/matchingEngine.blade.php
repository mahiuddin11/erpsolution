<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stock Matching Engine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .08);
        }

        .card-header {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: #fff;
        }

        thead th {
            background: #0d6efd !important;
            color: #fff;
            text-align: center;
            white-space: nowrap;
        }

        td {
            text-align: center;
            vertical-align: middle;
        }

        .matched-row {
            background: #d4edda !important;
        }

        .mismatched-row {
            background: #f8d7da !important;
        }

        /* ── Bulk bar ── */
        #bulkBar {
            display: none;
            position: sticky;
            bottom: 0;
            z-index: 100;
            background: #1e293b;
            color: #fff;
            padding: 12px 20px;
            border-radius: 10px 10px 0 0;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, .2);
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        #bulkBar.show {
            display: flex;
        }

        #bulkBar .sel-count {
            font-weight: 600;
            font-size: 15px;
        }

        #bulkBar select {
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 13px;
            border: none;
        }

        #bulkBar .btn-update {
            background: #22c55e;
            color: #fff;
            border: none;
            padding: 7px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }

        #bulkBar .btn-update:hover {
            background: #16a34a;
        }

        #bulkBar .btn-cancel {
            background: transparent;
            color: #94a3b8;
            border: 1px solid #475569;
            padding: 7px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }

        #bulkBar .btn-cancel:hover {
            color: #fff;
            border-color: #94a3b8;
        }

        /* checkbox column */
        .cb-col {
            width: 42px;
        }

        input[type=checkbox] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        /* toast */
        #toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 280px;
            display: none;
        }
    </style>
</head>

<body>

    @php
        $result = $result ?? [];
        $branchId = $branchId ?? '';
        $productId = $productId ?? '';
        $fromDate = $fromDate ?? '';
        $toDate = $toDate ?? '';
    @endphp

    {{-- Toast --}}
    <div id="toast" class="alert shadow" role="alert"></div>

    <div class="container-fluid mt-4 mb-5">

        {{-- Alerts --}}
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Product Stock Matching Engine</h4>
                @if (count($result))
                    <span class="badge bg-light text-dark fs-6">{{ count($result) }} records</span>
                @endif
            </div>

            <div class="card-body">

                {{-- Search Form --}}
                <form action="{{ url('/product-stock-matching') }}" method="POST"
                    class="row g-3 p-3 bg-light rounded mb-4">
                    @csrf
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Branch ID</label>
                        <input type="number" name="branch_id" class="form-control"
                            value="{{ old('branch_id', $branchId) }}" placeholder="optional">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Product ID <span class="text-danger">*</span></label>
                        <input type="number" name="product_id" class="form-control" required
                            value="{{ old('product_id', $productId) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control"
                            value="{{ old('from_date', $fromDate) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ old('to_date', $toDate) }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Search & Match
                        </button>
                    </div>
                </form>

                @if (count($result))

                    {{-- Toolbar --}}
                    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                        <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                            <i class="fas fa-check-square me-1"></i> Select All
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="selectNone()">
                            <i class="fas fa-square me-1"></i> Deselect All
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="selectMatched()">
                            <i class="fas fa-check me-1"></i> Select Matched
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="selectUnmatched()">
                            <i class="fas fa-times me-1"></i> Select No Match
                        </button>
                        <span class="ms-auto text-muted small" id="selInfo">0 selected</span>
                    </div>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm align-middle" id="resultTable">
                            <thead>
                                <tr>
                                    <th class="cb-col">
                                        <input type="checkbox" id="chkAll" onchange="toggleAll(this)"
                                            title="Select all">
                                    </th>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Source Table</th>
                                    <th>Stock Branch</th>
                                    <th>Source Branch</th>
                                    <th>General ID</th>
                                    <th>Source ID</th>
                                    <th>Stock Qty</th>
                                    <th>Source Qty</th>
                                    <th>Qty Match</th>
                                    <th>Current Status</th>
                                    <th>Match</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($result as $key => $item)
                                    @php
                                        $qtyMatch = $item['stock_qty'] == $item['quantity'];
                                        $isMatched = $item['match_status'] === 'Matched';
                                    @endphp
                                    <tr class="{{ $isMatched ? 'matched-row' : 'mismatched-row' }}"
                                        data-match="{{ $item['match_status'] }}"
                                        data-stock-id="{{ $item['stock_id'] }}">

                                        {{-- Checkbox --}}
                                        <td class="cb-col">
                                            <input type="checkbox" class="row-cb" value="{{ $item['stock_id'] }}"
                                                onchange="onCheckChange()">
                                        </td>

                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item['date'] ?? '-' }}</td>

                                        <td>
                                            <span class="badge {{ $isMatched ? 'bg-info' : 'bg-secondary' }}">
                                                {{ $item['table_name'] ?? '-' }}
                                            </span>
                                        </td>

                                        <td>{{ $item['stock_branch_id'] ?? 0 }}</td>
                                        <td>{{ $item['stock_out_branch_id'] ?? '-' }}</td>

                                        <td><strong>{{ $item['general_id'] ?? '-' }}</strong></td>
                                        <td>{{ $item['source_id'] ?? '-' }}</td>

                                        <td>{{ number_format($item['stock_qty'] ?? 0, 2) }}</td>
                                        <td>{{ number_format($item['quantity'] ?? 0, 2) }}</td>

                                        {{-- Qty match indicator --}}
                                        <td>
                                            @if ($isMatched)
                                                <span
                                                    class="badge {{ $qtyMatch ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ $qtyMatch ? '✓ OK' : '⚠ Diff' }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Current stock status --}}
                                        <td>
                                            @php
                                                $statusColor = match ($item['status'] ?? '') {
                                                    'Opening',
                                                    'Purchase',
                                                    'Manual Purchase',
                                                    'Production',
                                                    'Production In',
                                                    'Transfer In',
                                                    'Project In',
                                                    'Return',
                                                    'Sale Return',
                                                    'Gain'
                                                        => 'success',
                                                    'Sale',
                                                    'Production Sale',
                                                    'Production Out',
                                                    'Transfer Out',
                                                    'Project',
                                                    'Project Out',
                                                    'Project Use',
                                                    'Purchase Return'
                                                        => 'primary',
                                                    'Damage', 'Lost' => 'danger',
                                                    'Others' => 'secondary',
                                                    default => 'light',
                                                };
                                            @endphp
                                            <span
                                                class="badge bg-{{ $statusColor }} text-{{ in_array($item['status'], ['Pending']) ? 'dark' : 'white' }}"
                                                id="status-badge-{{ $item['stock_id'] }}">
                                                {{ $item['status'] ?? '-' }}
                                            </span>
                                        </td>

                                        {{-- Match status --}}
                                        <td>
                                            @if ($isMatched)
                                                <span class="badge bg-success">✓ Matched</span>
                                            @else
                                                <span class="badge bg-danger">✗ No Match</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr class="table-dark">
                                    <th colspan="8" class="text-end">Total</th>
                                    <th class="text-center">{{ number_format(collect($result)->sum('stock_qty'), 2) }}
                                    </th>
                                    <th class="text-center">{{ number_format(collect($result)->sum('quantity'), 2) }}
                                    </th>
                                    <th colspan="3" class="text-center">{{ count($result) }} records</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-info-circle fa-2x mb-3 d-block"></i>
                        <h5>Product ID দিয়ে Search করুন</h5>
                        <small class="text-muted">Branch ID optional — না দিলে সব branch দেখাবে</small>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ── Bulk Action Bar (sticky bottom) ── --}}
    <div id="bulkBar">
        <span class="sel-count"><span id="selCount">0</span> টি selected</span>

        <div class="vr opacity-25 d-none d-md-block" style="height:30px"></div>

        <label class="text-white-50 small mb-0">Status পরিবর্তন করুন:</label>
        <select id="bulkStatus">
            <option value="">— Status select করুন —</option>
            <optgroup label="── Stock In ──">
                <option value="Opening">📦 Opening</option>
                <option value="Purchase">🛒 Purchase</option>
                <option value="Manual Purchase">🖊 Manual Purchase</option>
                <option value="Production">🏭 Production</option>
                <option value="Production In">🏭 Production In</option>
                <option value="Transfer In">📥 Transfer In</option>
                <option value="Project In">📋 Project In</option>
                <option value="Return">↩ Return</option>
                <option value="Sale Return">↩ Sale Return</option>
                <option value="Gain">📈 Gain</option>
            </optgroup>
            <optgroup label="── Stock Out ──">
                <option value="Sale">🧾 Sale</option>
                <option value="Production Sale">🏭 Production Sale</option>
                <option value="Production Out">🏭 Production Out</option>
                <option value="Transfer Out">📤 Transfer Out</option>
                <option value="Project">📋 Project</option>
                <option value="Project Out">📋 Project Out</option>
                <option value="Project Use">🔧 Project Use</option>
                <option value="Purchase Return">↩ Purchase Return</option>
                <option value="Damage">💔 Damage</option>
                <option value="Lost">❌ Lost</option>
            </optgroup>
            <optgroup label="── Other ──">
                <option value="Others">🔹 Others</option>
            </optgroup>
        </select>

        <button class="btn-update" onclick="bulkUpdate()">
            <i class="fas fa-save me-1"></i> Update করুন
        </button>
        <button class="btn-cancel" onclick="selectNone()">বাতিল</button>
    </div>

    <script>
        // ── Checkbox helpers ───────────────────────────────────────────────────
        function getChecked() {
            return [...document.querySelectorAll('.row-cb:checked')];
        }

        function onCheckChange() {
            const checked = getChecked();
            const total = document.querySelectorAll('.row-cb').length;
            document.getElementById('chkAll').indeterminate = checked.length > 0 && checked.length < total;
            document.getElementById('chkAll').checked = checked.length === total;
            updateBulkBar(checked.length);
        }

        function toggleAll(el) {
            document.querySelectorAll('.row-cb').forEach(cb => cb.checked = el.checked);
            updateBulkBar(el.checked ? document.querySelectorAll('.row-cb').length : 0);
        }

        function selectAll() {
            document.querySelectorAll('.row-cb').forEach(cb => cb.checked = true);
            document.getElementById('chkAll').checked = true;
            updateBulkBar(document.querySelectorAll('.row-cb').length);
        }

        function selectNone() {
            document.querySelectorAll('.row-cb').forEach(cb => cb.checked = false);
            document.getElementById('chkAll').checked = false;
            updateBulkBar(0);
        }

        function selectMatched() {
            document.querySelectorAll('#resultTable tbody tr').forEach(tr => {
                const cb = tr.querySelector('.row-cb');
                if (cb) cb.checked = tr.dataset.match === 'Matched';
            });
            onCheckChange();
        }

        function selectUnmatched() {
            document.querySelectorAll('#resultTable tbody tr').forEach(tr => {
                const cb = tr.querySelector('.row-cb');
                if (cb) cb.checked = tr.dataset.match === 'No Match';
            });
            onCheckChange();
        }

        function updateBulkBar(count) {
            document.getElementById('selCount').textContent = count;
            document.getElementById('selInfo').textContent = count + ' selected';
            document.getElementById('bulkBar').classList.toggle('show', count > 0);
        }

        // ── Bulk Update AJAX ──────────────────────────────────────────────────
        async function bulkUpdate() {
            const stockIds = getChecked().map(cb => parseInt(cb.value));
            const status = document.getElementById('bulkStatus').value;

            if (!stockIds.length) {
                showToast('কোনো row select করা হয়নি', 'warning');
                return;
            }
            if (!status) {
                showToast('Status select করুন', 'warning');
                return;
            }

            const confirmMsg = stockIds.length + ' টি record → "' + status + '" করবেন?';
            if (!confirm(confirmMsg)) return;

            const btn = document.querySelector('#bulkBar .btn-update');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';

            try {
                const res = await fetch('{{ url('/product-stock-matching/bulk-update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        stock_ids: stockIds,
                        status: status
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    // Badge গুলো UI তে update করো
                    stockIds.forEach(id => {
                        const badge = document.getElementById('status-badge-' + id);
                        if (badge) {
                            const inStatuses = ['Opening', 'Purchase', 'Manual Purchase', 'Production',
                                'Production In', 'Transfer In', 'Project In', 'Return', 'Sale Return',
                                'Gain'
                            ];
                            const outStatuses = ['Sale', 'Production Sale', 'Production Out', 'Transfer Out',
                                'Project', 'Project Out', 'Project Use', 'Purchase Return'
                            ];
                            const badColor = ['Damage', 'Lost'];
                            let cls = 'secondary';
                            if (inStatuses.includes(status)) cls = 'success';
                            if (outStatuses.includes(status)) cls = 'primary';
                            if (badColor.includes(status)) cls = 'danger';
                            badge.className = 'badge bg-' + cls;
                            badge.textContent = status;
                        }
                    });
                    showToast('✅ ' + data.message, 'success');
                    selectNone();
                    document.getElementById('bulkStatus').value = '';
                } else {
                    showToast('❌ ' + data.message, 'danger');
                }

            } catch (e) {
                showToast('❌ Request failed: ' + e.message, 'danger');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Update করুন';
            }
        }

        // ── Toast ─────────────────────────────────────────────────────────────
        function showToast(msg, type = 'info') {
            const t = document.getElementById('toast');
            t.className = 'alert alert-' + type + ' shadow';
            t.textContent = msg;
            t.style.display = 'block';
            setTimeout(() => {
                t.style.display = 'none';
            }, 4000);
        }
    </script>

</body>

</html>
