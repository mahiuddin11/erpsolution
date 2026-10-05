@extends('backend.layouts.master')

@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
            }

            body {
                font-size: 12px;
            }
        }

        #ledgerTable thead th {
            background: #1a56db;
            color: #fff;
            white-space: nowrap;
        }

        #ledgerTable tbody tr:hover {
            background: #f0f4ff;
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

        .badge-project {
            background: #fce7f3;
            color: #9d174d;
        }

        .badge-sale-return {
            background: #cffafe;
            color: #155e75;
        }

        .badge-secondary {
            background: #e5e7eb;
            color: #374151;
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
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Product Ledger</h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    @php
        // নিরাপদ ডিফল্ট: কোনো কারণে ভ্যারিয়েবল না এলেও পেজ ভাঙবে না
        $branch_id = $branch_id ?? 'all';
        $warehouse_id = $warehouse_id ?? 'all';
        $purchase_type = $purchase_type ?? 'all';
        $product_id = $product_id ?? null;
        $from_date = $from_date ?? '2020-01-01';
        $to_date = $to_date ?? date('Y-m-t');
        $branches = $branches ?? collect();
        $warehouses = $warehouses ?? collect();
        $products = $products ?? collect();
        $datas = $datas ?? [];
    @endphp
    <div class="row">
        <div class="col-md-12">

            {{-- ── Filter Form ── --}}
            <form action="{{ route('report.stock.productledger') }}" method="POST" class="no-print">
                @csrf
                <div class="card card-outline card-info mb-3">
                    <div class="card-body">
                        <div class="row g-2 align-items-end">

                            {{-- সারি ১: Branch | Warehouse | Purchase Type | Product --}}
                            <div class="col-md-3">
                                <label class="font-weight-bold">Branch</label>
                                <select class="form-control select2" name="branch_id" id="branch_id">
                                    <option value="all" {{ $branch_id === 'all' ? 'selected' : '' }}>All Branches
                                    </option>
                                    @foreach ($branches as $b)
                                        <option value="{{ $b->id }}" {{ $branch_id == $b->id ? 'selected' : '' }}>
                                            {{ $b->branchCode }} - {{ $b->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="font-weight-bold">Warehouse</label>
                                <select class="form-control select2" name="warehouse_id" id="warehouse_id">
                                    <option value="all" {{ $warehouse_id === 'all' ? 'selected' : '' }}>All Warehouses
                                    </option>
                                    <option value="null" {{ $warehouse_id === 'null' ? 'selected' : '' }}>No Warehouse
                                    </option>
                                    @foreach ($warehouses as $w)
                                        <option value="{{ $w->id }}" data-branch="{{ $w->branch_id }}"
                                            {{ (string) $warehouse_id === (string) $w->id ? 'selected' : '' }}>
                                            {{ $w->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="font-weight-bold">Purchase Type</label>
                                <select class="form-control" name="purchase_type">
                                    <option value="all" {{ $purchase_type === 'all' ? 'selected' : '' }}>All Types
                                    </option>
                                    <option value="imported" {{ $purchase_type === 'imported' ? 'selected' : '' }}>
                                        Imported</option>
                                    <option value="local" {{ $purchase_type === 'local' ? 'selected' : '' }}>Local
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="font-weight-bold">Product <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="product_id" required>
                                    <option value="">— Select Product —</option>
                                    @foreach ($products as $p)
                                        <option value="{{ $p->id }}" {{ $product_id == $p->id ? 'selected' : '' }}>
                                            {{ $p->productCode }} - {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- সারি ২: From | To | Search --}}
                            <div class="col-md-3 mt-3">
                                <label class="font-weight-bold">From Date</label>
                                <input type="date" name="from_date" class="form-control" value="{{ $from_date }}">
                            </div>

                            <div class="col-md-3 mt-3">
                                <label class="font-weight-bold">To Date</label>
                                <input type="date" name="to_date" class="form-control" value="{{ $to_date }}">
                            </div>

                            <div class="col-md-2 mt-3">
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </form>

            {{-- ── Result Card ── --}}
            <div class="card card-default">
                <div class="card-header no-print d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Product Ledger</h3>
                    <div>
                        @if (!empty($datas))
                            <button onclick="exportCSV()" class="btn btn-sm btn-outline-success mr-2">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </button>
                        @endif
                        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>

                <div class="card-body">

                    {{-- Print Header --}}
                    <table class="table table-bordered mb-3">
                        <tr>
                            <td style="text-align:center; width:30%">
                                @if (isset($companyInfo->logo))
                                    <img width="160px" src="{{ asset('/backend/logo/' . $companyInfo->logo) }}"
                                        alt="Logo">
                                @endif
                            </td>
                            <td style="text-align:center">
                                <h3 class="mb-1">Product Ledger</h3>
                                <div class="text-muted">
                                    <strong>From:</strong> {{ $from_date }}
                                    &nbsp;&nbsp;
                                    <strong>To:</strong> {{ $to_date }}
                                </div>
                            </td>
                        </tr>
                    </table>

                    @if (!empty($datas))
                        {{-- Summary bar --}}
                        @php
                            $totalIn = collect($datas)->sum('in');
                            $totalOut = collect($datas)->sum('out');
                            $closing = collect($datas)->last()['remaining'] ?? 0;
                            // type এখন "Opening Stock (Imported)" ধরনের হতে পারে, তাই str_starts_with
                            $opening = collect($datas)
                                ->filter(fn($r) => str_starts_with($r['type'], 'Opening Stock'))
                                ->sum('in');
                        @endphp
                        <div class="summary-bar no-print">
                            <div class="row">
                                <div class="col s-item">
                                    <div class="s-val">{{ number_format($opening) }}</div>
                                    <div class="s-lbl">Opening Stock</div>
                                </div>
                                <div class="col s-item">
                                    <div class="s-val text-success">+{{ number_format($totalIn - $opening) }}</div>
                                    <div class="s-lbl">Total In (excl. opening)</div>
                                </div>
                                <div class="col s-item">
                                    <div class="s-val text-danger">-{{ number_format($totalOut) }}</div>
                                    <div class="s-lbl">Total Out</div>
                                </div>
                                <div class="col s-item">
                                    <div class="s-val" style="color:#38bdf8">{{ number_format($closing) }}</div>
                                    <div class="s-lbl">Closing Stock</div>
                                </div>
                                <div class="col s-item">
                                    <div class="s-val">{{ count($datas) }}</div>
                                    <div class="s-lbl">Total Entries</div>
                                </div>
                            </div>
                        </div>

                        {{-- Ledger Table --}}
                        <div class="table-responsive">
                            <table id="ledgerTable" class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th>Invoice</th>
                                        <th>Branch</th>
                                        <th>Warehouse/Project</th>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th class="text-right">In</th>
                                        <th class="text-right">Out</th>
                                        <th class="text-right">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $row)
                                        <tr>
                                            <td>{{ $row['sl'] }}</td>
                                            <td>{{ $row['date'] }}</td>
                                            <td><small>{{ $row['invoice'] }}</small></td>
                                            <td>{{ $row['branch'] ?? '—' }}</td>
                                            <td>{{ $row['warehouse'] ?? '—' }}</td>
                                            <td>{{ $row['product'] }}</td>
                                            <td>
                                                <span class="type-badge {{ $row['badge'] ?? 'badge-secondary' }}">
                                                    {{ $row['type'] }}
                                                </span>
                                            </td>
                                            <td class="text-right in-col">
                                                {{ $row['in'] > 0 ? '+' . number_format($row['in']) : '—' }}
                                            </td>
                                            <td class="text-right out-col">
                                                {{ $row['out'] > 0 ? '-' . number_format($row['out']) : '—' }}
                                            </td>
                                            <td class="text-right rem-col">
                                                {{ number_format($row['remaining']) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-dark font-weight-bold">
                                        <td colspan="7" class="text-right">Total</td>
                                        <td class="text-right in-col">+{{ number_format($totalIn) }}</td>
                                        <td class="text-right out-col">-{{ number_format($totalOut) }}</td>
                                        <td class="text-right rem-col">{{ number_format($closing) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Adjust Quantity ফর্ম আপাতত বন্ধ রাখা হয়েছে (qty.update যাচাই না হওয়া পর্যন্ত) --}}
                    @else
                        <div class="alert alert-info text-center py-5">
                            <i class="fas fa-info-circle fa-2x mb-3 d-block"></i>
                            <h5>Select a product and Search</h5>
                            <small class="text-muted">Branch, Warehouse and Purchase Type are optional</small>
                        </div>
                    @endif

                    {{-- Print footer --}}
                    @if (!empty($datas))
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <p>Prepared By: _____________<br>Date: ____________________</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="bg-success text-white p-2 rounded" style="font-size:12px">
                                    Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <p>Approved By: ________________<br>Date: _________________</p>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    <script>
        function exportCSV() {
            const rows = document.querySelectorAll('#ledgerTable tr');
            const lines = [];
            rows.forEach(r => {
                const cols = [...r.querySelectorAll('th,td')].map(c =>
                    '"' + c.innerText.trim().replace(/"/g, '""') + '"'
                );
                lines.push(cols.join(','));
            });
            const blob = new Blob([lines.join('\n')], {
                type: 'text/csv'
            });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'product-ledger-{{ $product_id }}-{{ $from_date }}-{{ $to_date }}.csv';
            a.click();
        }

        // Branch বাছলে Warehouse তালিকা ওই ব্রাঞ্চের warehouse-এ সীমিত হবে
        document.addEventListener('DOMContentLoaded', function() {
            const $wh = $('#warehouse_id');
            const allOpts = $wh.find('option').clone();

            function filterWarehouses() {
                const b = $('#branch_id').val();
                const current = $wh.val();
                $wh.empty();
                allOpts.each(function() {
                    const br = $(this).data('branch');
                    // all / null অপশনে data-branch নেই, তাই সবসময় থাকবে
                    if (br === undefined || b === 'all' || String(br) === String(b)) {
                        $wh.append($(this).clone());
                    }
                });
                $wh.val($wh.find('option[value="' + current + '"]').length ? current : 'all');
                $wh.trigger('change.select2');
            }

            $('#branch_id').on('change', filterWarehouses);
            filterWarehouses();
        });
    </script>
@endsection
