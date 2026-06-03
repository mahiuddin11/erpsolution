<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product Summary Report</title>
    <style>
        :root {
            --primary: #1a56db;
            --primary-dark: #1e429f;
            --success: #057a55;
            --danger: #c81e1e;
            --warning: #9f580a;
            --bg: #f3f4f6;
            --card: #fff;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
            --muted2: #9ca3af;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
        }

        .page {
            max-width: 1500px;
            margin: 0 auto;
            padding: 20px 16px;
        }

        /* ── Top header ── */
        .page-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 18px;
            font-weight: 600;
        }

        .page-header .pill {
            font-size: 11px;
            background: #dbeafe;
            color: #1e40af;
            padding: 3px 10px;
            border-radius: 999px;
            font-weight: 500;
        }

        /* ── Filter card ── */
        .filter-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 16px;
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-end;
        }

        .fg {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .fg label {
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .fg input,
        .fg select {
            height: 38px;
            border: 1px solid var(--border);
            border-radius: 7px;
            padding: 0 12px;
            font-size: 13px;
            color: var(--text);
            background: #fff;
            min-width: 140px;
            outline: none;
            transition: .15s;
        }

        .fg input:focus,
        .fg select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px #bfdbfe55;
        }

        .btn {
            height: 38px;
            padding: 0 18px;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: .15s;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-primary:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .btn-ghost {
            background: #fff;
            border: 1px solid var(--border);
            color: var(--muted);
        }

        .btn-ghost:hover {
            background: var(--bg);
            color: var(--text);
        }

        /* ── Product info strip ── */
        #productStrip {
            display: none;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 14px;
            gap: 0;
            flex-wrap: wrap;
        }

        #productStrip.show {
            display: flex;
        }

        .pinfo {
            padding: 4px 18px 4px 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .pinfo .k {
            font-size: 10px;
            color: #3b82f6;
            font-weight: 600;
            text-transform: uppercase;
        }

        .pinfo .v {
            font-size: 14px;
            font-weight: 600;
            color: #1e3a8a;
        }

        .pinfo+.pinfo {
            border-left: 1px solid #bfdbfe;
            padding-left: 18px;
        }

        /* ── Summary cards ── */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(148px, 1fr));
            gap: 10px;
            margin-bottom: 16px;
        }

        .sc {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 16px;
        }

        .sc .lbl {
            font-size: 11px;
            color: var(--muted);
            margin-bottom: 5px;
            font-weight: 500;
        }

        .sc .val {
            font-size: 22px;
            font-weight: 700;
        }

        .sc .sub {
            font-size: 11px;
            color: var(--muted2);
            margin-top: 2px;
        }

        .sc.blue .val {
            color: var(--primary);
        }

        .sc.green .val {
            color: var(--success);
        }

        .sc.red .val {
            color: var(--danger);
        }

        .sc.amber .val {
            color: var(--warning);
        }

        .sc.slate .val {
            color: #374151;
        }

        /* ── Section tables ── */
        .section {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 14px;
        }

        .sec-head {
            padding: 10px 16px;
            background: #f9fafb;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .sec-title {
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .sec-icon {
            font-size: 16px;
        }

        .tbl-src {
            font-size: 10px;
            font-family: monospace;
            background: #e5e7eb;
            color: var(--muted);
            padding: 2px 8px;
            border-radius: 5px;
        }

        .tbl-count {
            font-size: 11px;
            background: #dbeafe;
            color: #1e40af;
            padding: 2px 9px;
            border-radius: 999px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        thead {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        th {
            padding: 8px 12px;
            text-align: left;
            font-size: 10.5px;
            font-weight: 600;
            color: var(--muted);
            background: #f9fafb;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        th.r,
        td.r {
            text-align: right;
        }

        th.c,
        td.c {
            text-align: center;
        }

        td {
            padding: 8px 12px;
            border-bottom: 1px solid #f3f4f6;
            color: var(--text);
            white-space: nowrap;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #f9fafb;
        }

        .tfoot td {
            background: #f9fafb;
            font-weight: 600;
            border-top: 1px solid var(--border);
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 500;
        }

        .b-in {
            background: #dcfce7;
            color: #166534;
        }

        .b-out {
            background: #fee2e2;
            color: #991b1b;
        }

        .b-adj {
            background: #fef9c3;
            color: #854d0e;
        }

        .b-tr {
            background: #dbeafe;
            color: #1e40af;
        }

        .b-pend {
            background: #f3f4f6;
            color: #6b7280;
        }

        .b-local {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .b-imp {
            background: #fdf4ff;
            color: #7e22ce;
            border: 1px solid #e9d5ff;
        }

        code {
            background: #f3f4f6;
            padding: 1px 5px;
            border-radius: 4px;
            font-size: 11px;
            font-family: monospace;
        }

        /* ── States ── */
        .empty {
            text-align: center;
            padding: 28px;
            color: var(--muted2);
            font-size: 13px;
        }

        #loader {
            display: none;
            text-align: center;
            padding: 40px;
            color: var(--muted);
        }

        #loader.show {
            display: block;
        }

        .spinner {
            width: 32px;
            height: 32px;
            border: 3px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin .7s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }

        #errBox {
            display: none;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 12px 16px;
            color: #991b1b;
            margin-bottom: 14px;
            font-size: 13px;
        }

        #errBox.show {
            display: block;
        }

        #initMsg {
            text-align: center;
            padding: 40px;
            color: var(--muted2);
            font-size: 13px;
            background: var(--card);
            border: 1px dashed var(--border);
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="page">

        <div class="page-header">
            <h1>📦 Product Summary Report</h1>
            <span class="pill">Developer View</span>
        </div>

        {{-- ── Filter ── --}}
        <div class="filter-card">
            <div class="filter-row">
                <div class="fg">
                    <label>Branch</label>
                    <select id="branchId">
                        <option value="">— All Branches —</option>
                        @foreach ($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Product ID</label>
                    <input type="number" id="productId" placeholder="e.g. 101" min="1" style="width:120px">
                </div>
                <div class="fg">
                    <label>From Date</label>
                    <input type="date" id="fromDate" value="{{ date('Y-01-01') }}">
                </div>
                <div class="fg">
                    <label>To Date</label>
                    <input type="date" id="toDate" value="{{ date('Y-m-d') }}">
                </div>
                <button class="btn btn-primary" id="searchBtn" onclick="loadReport()">
                    🔍 Search
                </button>
                <button class="btn btn-ghost" onclick="resetAll()">↺ Reset</button>
            </div>
        </div>

        {{-- Error --}}
        <div id="errBox"></div>

        {{-- Loader --}}
        <div id="loader">
            <div class="spinner"></div>Loading report data...
        </div>

        {{-- Init message --}}
        <div id="initMsg">Product ID দিয়ে Search করুন — সব table থেকে data load হবে</div>

        {{-- Result --}}
        <div id="resultArea" style="display:none">

            {{-- Product strip --}}
            <div id="productStrip">
                <div class="pinfo"><span class="k">Product Name</span><span class="v" id="pName">—</span>
                </div>
                <div class="pinfo"><span class="k">Code</span><span class="v" id="pCode">—</span></div>
                <div class="pinfo"><span class="k">Unit</span><span class="v" id="pUnit">—</span></div>
                <div class="pinfo"><span class="k">Category</span><span class="v" id="pCat">—</span>
                </div>
                <div class="pinfo"><span class="k">Purchase Price</span><span class="v"
                        id="pPurchPrice">—</span></div>
                <div class="pinfo"><span class="k">Sale Price</span><span class="v" id="pSalePrice">—</span>
                </div>
                <div class="pinfo"><span class="k">Product ID</span><span class="v" id="pId">—</span>
                </div>
            </div>

            {{-- Summary cards --}}
            <div class="summary-grid">
                <div class="sc blue">
                    <div class="lbl">Opening Stock</div>
                    <div class="val" id="cOpen">—</div>
                    <div class="sub">product_opening_stock_details</div>
                </div>
                <div class="sc green">
                    <div class="lbl">Purchase In (+)</div>
                    <div class="val" id="cPurch">—</div>
                    <div class="sub">purchases_details [Active]</div>
                </div>
                <div class="sc green">
                    <div class="lbl">Transfer In (+)</div>
                    <div class="val" id="cTrIn">—</div>
                    <div class="sub">transfer_details [Approved]</div>
                </div>
                <div class="sc red">
                    <div class="lbl">Transfer Out (−)</div>
                    <div class="val" id="cTrOut">—</div>
                    <div class="sub">transfer_details [Approved]</div>
                </div>
                <div class="sc amber">
                    <div class="lbl">Adjustment (+/−)</div>
                    <div class="val" id="cAdj">—</div>
                    <div class="sub">stock_ajdustment_detailsts</div>
                </div>
                <div class="sc red">
                    <div class="lbl">Sales Out (−)</div>
                    <div class="val" id="cSales">—</div>
                    <div class="sub">sales__details</div>
                </div>
                <div class="sc slate">
                    <div class="lbl">Closing Stock</div>
                    <div class="val" id="cClose">—</div>
                    <div class="sub">Opening±All transactions</div>
                </div>
            </div>

            {{-- 1. Opening Stock --}}
            <div class="section">
                <div class="sec-head">
                    <div class="sec-title"><span class="sec-icon">📂</span> Opening Stock</div>
                    <div style="display:flex;gap:6px;align-items:center">
                        <span class="tbl-count" id="cnt-open">0 rows</span>
                        <span class="tbl-src">product_opening_stock_details</span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Branch</th>
                                <th>Date</th>
                                <th>Purchase Type</th>
                                <th class="r">Qty</th>
                                <th class="r">Unit Price</th>
                                <th class="r">Total Price</th>
                            </tr>
                        </thead>
                        <tbody id="tOpen">
                            <tr>
                                <td colspan="6" class="empty">—</td>
                            </tr>
                        </tbody>
                        <tfoot id="tfOpen" style="display:none">
                            <tr class="tfoot">
                                <td colspan="3">Total</td>
                                <td class="r" id="tfOpenQty">—</td>
                                <td></td>
                                <td class="r" id="tfOpenAmt">—</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- 2. Purchases --}}
            <div class="section">
                <div class="sec-head">
                    <div class="sec-title"><span class="sec-icon">🛒</span> Purchases</div>
                    <div style="display:flex;gap:6px;align-items:center">
                        <span class="tbl-count" id="cnt-purch">0 rows</span>
                        <span class="tbl-src">purchases_details</span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Branch</th>
                                <th>Supplier</th>
                                <th>Purchase ID</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th class="r">Qty</th>
                                <th class="r">Unit Price</th>
                                <th class="r">Total Price</th>
                            </tr>
                        </thead>
                        <tbody id="tPurch">
                            <tr>
                                <td colspan="9" class="empty">—</td>
                            </tr>
                        </tbody>
                        <tfoot id="tfPurch" style="display:none">
                            <tr class="tfoot">
                                <td colspan="6">Total</td>
                                <td class="r" id="tfPurchQty">—</td>
                                <td></td>
                                <td class="r" id="tfPurchAmt">—</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- 3. Transfers --}}
            <div class="section">
                <div class="sec-head">
                    <div class="sec-title"><span class="sec-icon">🔄</span> Stock Transfers</div>
                    <div style="display:flex;gap:6px;align-items:center">
                        <span class="tbl-count" id="cnt-trans">0 rows</span>
                        <span class="tbl-src">transfer_details</span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Transfer ID</th>
                                <th>From Branch</th>
                                <th>To Branch</th>
                                <th>Status</th>
                                <th class="r">Requested Qty</th>
                                <th class="r">Approved Qty</th>
                                <th class="r">Unit Price</th>
                                <th class="r">Total Price</th>
                            </tr>
                        </thead>
                        <tbody id="tTrans">
                            <tr>
                                <td colspan="9" class="empty">—</td>
                            </tr>
                        </tbody>
                        <tfoot id="tfTrans" style="display:none">
                            <tr class="tfoot">
                                <td colspan="5">Total</td>
                                <td class="r" id="tfTransQty">—</td>
                                <td class="r" id="tfTransAQty">—</td>
                                <td></td>
                                <td class="r" id="tfTransAmt">—</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- 4. Stock Adjustments --}}
            <div class="section">
                <div class="sec-head">
                    <div class="sec-title"><span class="sec-icon">⚙️</span> Stock Adjustments</div>
                    <div style="display:flex;gap:6px;align-items:center">
                        <span class="tbl-count" id="cnt-adj">0 rows</span>
                        <span class="tbl-src">stock_ajdustment_detailsts</span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Approval Date</th>
                                <th>Branch</th>
                                <th>Ref (purchases_id)</th>
                                <th>Status</th>
                                <th class="r">Qty</th>
                                <th class="r">Unit Price</th>
                                <th class="r">Total Price</th>
                            </tr>
                        </thead>
                        <tbody id="tAdj">
                            <tr>
                                <td colspan="8" class="empty">—</td>
                            </tr>
                        </tbody>
                        <tfoot id="tfAdj" style="display:none">
                            <tr class="tfoot">
                                <td colspan="5">Total</td>
                                <td class="r" id="tfAdjQty">—</td>
                                <td></td>
                                <td class="r" id="tfAdjAmt">—</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- 5. Sales --}}
            <div class="section">
                <div class="sec-head">
                    <div class="sec-title"><span class="sec-icon">🧾</span> Sales</div>
                    <div style="display:flex;gap:6px;align-items:center">
                        <span class="tbl-count" id="cnt-sales">0 rows</span>
                        <span class="tbl-src">sales__details</span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Sale ID</th>
                                <th>Branch</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th class="r">Qty</th>
                                <th class="r">Rate</th>
                                <th class="r">Price</th>
                                <th class="r">VAT</th>
                            </tr>
                        </thead>
                        <tbody id="tSales">
                            <tr>
                                <td colspan="9" class="empty">—</td>
                            </tr>
                        </tbody>
                        <tfoot id="tfSales" style="display:none">
                            <tr class="tfoot">
                                <td colspan="5">Total</td>
                                <td class="r" id="tfSalesQty">—</td>
                                <td></td>
                                <td class="r" id="tfSalesAmt">—</td>
                                <td class="r" id="tfSalesVat">—</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>{{-- end resultArea --}}
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const BRANCHES = @json($branches->pluck('name', 'id'));

        function branchName(id) {
            return BRANCHES[id] || ('Branch #' + id);
        }

        function fmt(n) {
            return Number(n || 0).toLocaleString('en-BD');
        }

        function fmtMoney(n) {
            return '৳ ' + Number(n || 0).toLocaleString('en-BD', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function empty(cols, msg) {
            return `<tr><td colspan="${cols}" class="empty">${msg||'No data found'}</td></tr>`;
        }

        function ptype(t) {
            return t === 'local' ? '<span class="badge b-local">Local</span>' : '<span class="badge b-imp">Imported</span>';
        }

        function status(s) {
            const m = {
                Active: '<span class="badge b-in">Active</span>',
                Pending: '<span class="badge b-pend">Pending</span>',
                Cancel: '<span class="badge b-out">Cancel</span>',
                Approved: '<span class="badge b-tr">Approved</span>'
            };
            return m[s] || s;
        }

        function setCount(id, n) {
            document.getElementById(id).textContent = n + ' row' + (n !== 1 ? 's' : '');
        }

        function sumCol(arr, col) {
            return arr.reduce((a, r) => a + (Number(r[col]) || 0), 0);
        }

        async function loadReport() {
            const productId = document.getElementById('productId').value.trim();
            const branchId = document.getElementById('branchId').value;
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;

            if (!productId) {
                alert('Product ID দিন');
                return;
            }
            if (!fromDate || !toDate) {
                alert('Date range দিন');
                return;
            }

            setLoading(true);
            hideError();
            document.getElementById('resultArea').style.display = 'none';
            document.getElementById('initMsg').style.display = 'none';

            try {
                const res = await fetch('/product-report', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        branch_id: branchId || null,
                        from_date: fromDate,
                        to_date: toDate
                    })
                });
                const data = await res.json();
                if (!res.ok) {
                    showError(data.error || data.message || 'Server error (' + res.status + ')');
                    return;
                }
                renderReport(data, branchId ? parseInt(branchId) : null);
                document.getElementById('resultArea').style.display = 'block';
            } catch (e) {
                showError('Request failed: ' + e.message);
            } finally {
                setLoading(false);
            }
        }

        function renderReport(d, branchId) {
            // Product strip
            document.getElementById('pName').textContent = d.product.name || '—';
            document.getElementById('pCode').textContent = d.product.code || '—';
            document.getElementById('pUnit').textContent = d.product.unit || '—';
            document.getElementById('pCat').textContent = d.product.category || '—';
            document.getElementById('pPurchPrice').textContent = fmtMoney(d.product.purchases_price);
            document.getElementById('pSalePrice').textContent = fmtMoney(d.product.sale_price);
            document.getElementById('pId').textContent = d.product.id;
            document.getElementById('productStrip').classList.add('show');

            // Summary
            const s = d.summary;
            document.getElementById('cOpen').textContent = fmt(s.totalOpening);
            document.getElementById('cPurch').textContent = '+' + fmt(s.totalPurchase);
            document.getElementById('cTrIn').textContent = '+' + fmt(s.totalTrIn);
            document.getElementById('cTrOut').textContent = '-' + fmt(s.totalTrOut);
            document.getElementById('cAdj').textContent = (s.totalAdj >= 0 ? '+' : '') + fmt(s.totalAdj);
            document.getElementById('cSales').textContent = '-' + fmt(s.totalSales);
            document.getElementById('cClose').textContent = fmt(s.closing);

            // ── Opening Stock ──────────────────────────────────────────────
            const op = d.opening;
            setCount('cnt-open', op.length);
            document.querySelector('#tOpen tbody').innerHTML = op.length ?
                op.map(r => `<tr>
            <td>${branchName(r.branch_id)}</td>
            <td>${r.date||'—'}</td>
            <td>${r.purchasetype?ptype(r.purchasetype):'—'}</td>
            <td class="r"><span class="badge b-in">${fmt(r.quantity)}</span></td>
            <td class="r">${fmtMoney(r.unit_price)}</td>
            <td class="r">${fmtMoney(r.total_price)}</td>
        </tr>`).join('') : empty(6);
            if (op.length) {
                document.getElementById('tfOpen').style.display = '';
                document.getElementById('tfOpenQty').textContent = fmt(sumCol(op, 'quantity'));
                document.getElementById('tfOpenAmt').textContent = fmtMoney(sumCol(op, 'total_price'));
            }

            // ── Purchases ──────────────────────────────────────────────────
            const pu = d.purchases;
            setCount('cnt-purch', pu.length);
            document.querySelector('#tPurch tbody').innerHTML = pu.length ?
                pu.map(r => `<tr>
            <td>${r.date||'—'}</td>
            <td>${branchName(r.branch_id)}</td>
            <td>${r.supplier_name||'—'}</td>
            <td><code>#${r.purchases_id}</code></td>
            <td>${ptype(r.purchasetype)}</td>
            <td>${status(r.status)}</td>
            <td class="r"><span class="badge b-in">+${fmt(r.quantity)}</span></td>
            <td class="r">${fmtMoney(r.unit_price)}</td>
            <td class="r">${fmtMoney(r.total_price)}</td>
        </tr>`).join('') : empty(9);
            if (pu.length) {
                document.getElementById('tfPurch').style.display = '';
                document.getElementById('tfPurchQty').textContent = fmt(sumCol(pu, 'quantity'));
                document.getElementById('tfPurchAmt').textContent = fmtMoney(sumCol(pu, 'total_price'));
            }

            // ── Transfers ──────────────────────────────────────────────────
            const tr = d.transfers;
            setCount('cnt-trans', tr.length);
            document.querySelector('#tTrans tbody').innerHTML = tr.length ?
                tr.map(r => {
                    const isIn = !branchId || r.to_branch_id === branchId;
                    const isOut = !branchId || r.from_branch_id === branchId;
                    return `<tr>
                <td>${r.date||'—'}</td>
                <td><code>#${r.transfer_id}</code></td>
                <td>${branchName(r.from_branch_id)} ${isOut?'<span class="badge b-out">OUT</span>':''}</td>
                <td>${branchName(r.to_branch_id)} ${isIn?'<span class="badge b-in">IN</span>':''}</td>
                <td>${status(r.status)}</td>
                <td class="r">${fmt(r.qty)}</td>
                <td class="r"><strong>${fmt(r.approve_qty)}</strong></td>
                <td class="r">${fmtMoney(r.unit_price)}</td>
                <td class="r">${fmtMoney(r.total_price)}</td>
            </tr>`;
                }).join('') : empty(9);
            if (tr.length) {
                document.getElementById('tfTrans').style.display = '';
                document.getElementById('tfTransQty').textContent = fmt(sumCol(tr, 'qty'));
                document.getElementById('tfTransAQty').textContent = fmt(sumCol(tr, 'approve_qty'));
                document.getElementById('tfTransAmt').textContent = fmtMoney(sumCol(tr, 'total_price'));
            }

            // ── Adjustments ──────────────────────────────────────────────
            const adj = d.adjustments;
            setCount('cnt-adj', adj.length);
            document.querySelector('#tAdj tbody').innerHTML = adj.length ?
                adj.map(r => `<tr>
            <td>${r.date||'—'}</td>
            <td>${r.approval_date||'—'}</td>
            <td>${branchName(r.branch_id)}</td>
            <td><code>#${r.purchases_id||'—'}</code></td>
            <td>${status(r.status)}</td>
            <td class="r"><span class="badge b-adj">${(r.quantity>=0?'+':'')}${fmt(r.quantity)}</span></td>
            <td class="r">${fmtMoney(r.unit_price)}</td>
            <td class="r">${fmtMoney(r.total_price)}</td>
        </tr>`).join('') : empty(8);
            if (adj.length) {
                document.getElementById('tfAdj').style.display = '';
                document.getElementById('tfAdjQty').textContent = (sumCol(adj, 'quantity') >= 0 ? '+' : '') + fmt(sumCol(
                    adj, 'quantity'));
                document.getElementById('tfAdjAmt').textContent = fmtMoney(sumCol(adj, 'total_price'));
            }

            // ── Sales ──────────────────────────────────────────────────────
            const sl = d.sales;
            setCount('cnt-sales', sl.length);
            document.querySelector('#tSales tbody').innerHTML = sl.length ?
                sl.map(r => `<tr>
            <td>${r.date||'—'}</td>
            <td><code>#${r.sale_id}</code></td>
            <td>${branchName(r.branch_id)}</td>
            <td>${r.customer_name||'—'}</td>
            <td>${ptype(r.purchasetype)}</td>
            <td class="r"><span class="badge b-out">-${fmt(r.qty)}</span></td>
            <td class="r">${fmtMoney(r.rate)}</td>
            <td class="r">${fmtMoney(r.price)}</td>
            <td class="r">${r.vat?fmtMoney(r.vat):'—'}</td>
        </tr>`).join('') : empty(9);
            if (sl.length) {
                document.getElementById('tfSales').style.display = '';
                document.getElementById('tfSalesQty').textContent = fmt(sumCol(sl, 'qty'));
                document.getElementById('tfSalesAmt').textContent = fmtMoney(sumCol(sl, 'price'));
                document.getElementById('tfSalesVat').textContent = fmtMoney(sumCol(sl, 'vat'));
            }
        }

        function resetAll() {
            document.getElementById('productId').value = '';
            document.getElementById('branchId').value = '';
            document.getElementById('fromDate').value = '{{ date('Y-01-01') }}';
            document.getElementById('toDate').value = '{{ date('Y-m-d') }}';
            document.getElementById('resultArea').style.display = 'none';
            document.getElementById('productStrip').classList.remove('show');
            document.getElementById('initMsg').style.display = 'block';
            hideError();
            ['cnt-open', 'cnt-purch', 'cnt-trans', 'cnt-adj', 'cnt-sales'].forEach(id => document.getElementById(id)
                .textContent = '0 rows');
            ['tfOpen', 'tfPurch', 'tfTrans', 'tfAdj', 'tfSales'].forEach(id => document.getElementById(id).style.display =
                'none');
        }

        function setLoading(on) {
            document.getElementById('loader').classList.toggle('show', on);
            const btn = document.getElementById('searchBtn');
            btn.disabled = on;
            btn.textContent = on ? '⏳ Loading...' : '🔍 Search';
        }

        function showError(msg) {
            const b = document.getElementById('errBox');
            b.textContent = '❌ ' + msg;
            b.classList.add('show');
        }

        function hideError() {
            document.getElementById('errBox').classList.remove('show');
        }

        document.getElementById('productId').addEventListener('keydown', e => {
            if (e.key === 'Enter') loadReport();
        });
    </script>
</body>

</html>
