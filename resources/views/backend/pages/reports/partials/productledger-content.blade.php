{{-- Modified: 2026-07-20 - professional styling matched to exact types from getProductLedgerData() --}}
<style>
    #simpleLedgerTable thead th {
        background: #1e293b;
        color: #fff;
        white-space: nowrap;
        font-size: 12.5px;
    }

    #simpleLedgerTable td {
        font-size: 13px;
        vertical-align: middle;
    }

    #simpleLedgerTable tbody tr:hover {
        background: #f8fafc;
    }

    .type-badge {
        font-size: 11px;
        padding: 3px 9px;
        border-radius: 999px;
        white-space: nowrap;
        font-weight: 600;
    }

    .btn-secondary {

        background-color: #ffffff;
        border-color: #506d87;
        box-shadow: none;
    }

    .badge-opening {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-purchase {
        background: #dcfce7;
        color: #166534;
    }

    .badge-consume {
        background: #ffedd5;
        color: #9a3412;
    }

    .badge-gain {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-loss {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-damage {
        background: #fecaca;
        color: #7f1d1d;
    }

    .badge-adjustment {
        background: #ede9fe;
        color: #5b21b6;
    }

    .badge-transfer-in {
        background: #cffafe;
        color: #155e75;
    }

    .badge-transfer-out {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-sale {
        background: #fee2e2;
        color: #991b1b;
    }

    .in-col {
        color: #166534;
        font-weight: 700;
    }

    .out-col {
        color: #991b1b;
        font-weight: 700;
    }

    .rem-col {
        color: #1e40af;
        font-weight: 700;
    }

    .ledger-summary-bar {
        background: #0f172a;
        border-radius: 10px;
        padding: 14px 10px;
        margin: 0 15px 14px;
    }

    .ledger-summary-bar .s-item {
        text-align: center;
        color: #fff;
    }

    .ledger-summary-bar .s-val {
        font-size: 19px;
        font-weight: 700;
    }

    .ledger-summary-bar .s-lbl {
        font-size: 10.5px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .alert-warning {
        color: #1f2d3d;
        background-color: #07d4ff14;
        border-color: #0f5143;
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>

<div class="px-3 pt-3 pb-2 d-flex justify-content-between align-items-center no-print">
    <div>
        <h6 class="mb-0 font-weight-bold">{{ $product->productCode ?? '' }} — {{ $product->name ?? '' }}</h6>
        <small class="text-muted">{{ $from_date }} to {{ $to_date }}</small>
    </div>
    <button class="btn btn-sm btn-primary" onclick="printSimpleLedger()">
        <i class="fas fa-print"></i> Print
    </button>
</div>

<div id="simpleLedgerPrintArea">
    <div style="display:none" class="d-print-block text-center mb-3 px-3">
        <h5 class="mb-0">Product Ledger</h5>
        <small>{{ $product->productCode ?? '' }} — {{ $product->name ?? '' }} | {{ $from_date }} to
            {{ $to_date }}</small>
    </div>

    @if (empty($datas))
        <div class="alert alert-warning text-center py-4 mx-3">No movement found for this product.</div>
    @else
        @php
            $totalIn = collect($datas)->sum('in');
            $totalOut = collect($datas)->sum('out');
            $closing = collect($datas)->last()['remaining'] ?? 0;
            $opening = collect($datas)->where('type', 'Opening Stock')->sum('in');
        @endphp

        {{-- Summary bar --}}
        <div class="ledger-summary-bar no-print">
            <div class="row">
                <div class="col s-item">
                    <div class="s-val">{{ number_format($opening) }}</div>
                    <div class="s-lbl">Opening</div>
                </div>
                <div class="col s-item">
                    <div class="s-val" style="color:#4ade80">+{{ number_format($totalIn - $opening) }}</div>
                    <div class="s-lbl">Total In</div>
                </div>
                <div class="col s-item">
                    <div class="s-val" style="color:#f87171">-{{ number_format($totalOut) }}</div>
                    <div class="s-lbl">Total Out</div>
                </div>
                <div class="col s-item">
                    <div class="s-val" style="color:#38bdf8">{{ number_format($closing) }}</div>
                    <div class="s-lbl">Closing</div>
                </div>
                <div class="col s-item">
                    <div class="s-val">{{ count($datas) }}</div>
                    <div class="s-lbl">Entries</div>
                </div>
            </div>
        </div>

        <div class="px-3 pb-3">
            <div class="table-responsive">
                <table id="simpleLedgerTable" class="table table-bordered table-sm mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Invoice</th>
                            <th>Branch/Project</th>
                            <th>Type</th>
                            <th class="text-right">In</th>
                            <th class="text-right">Out</th>
                            <th class="text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $row)
                            @php
                                // Modified: 2026-07-20 - exact type match অনুযায়ী badge color
                                // (getProductLedgerData() যা যা type generate করে তার সবগুলো cover করা হলো)
                                $typeClass = match (true) {
                                    $row['type'] === 'Opening Stock' => 'badge-opening',
                                    str_starts_with($row['type'], 'Purchase') => 'badge-purchase',
                                    $row['type'] === 'Project Consume (Manual)' => 'badge-consume',
                                    $row['type'] === 'Adjustment (Gain)' => 'badge-gain',
                                    $row['type'] === 'Adjustment (Loss)' => 'badge-loss',
                                    $row['type'] === 'Adjustment (Damage)' => 'badge-damage',
                                    str_starts_with($row['type'], 'Adjustment') => 'badge-adjustment',
                                    $row['type'] === 'Transfer In' => 'badge-transfer-in',
                                    $row['type'] === 'Transfer Out' => 'badge-transfer-out',
                                    $row['type'] === 'Sale' => 'badge-sale',
                                    default => 'badge-adjustment',
                                };
                            @endphp
                            <tr>
                                <td>{{ $row['sl'] }}</td>
                                <td>{{ $row['date'] }}</td>
                                <td><small>{{ $row['invoice'] }}</small></td>
                                <td>{{ $row['branch'] }}</td>
                                <td><span class="type-badge {{ $typeClass }}">{{ $row['type'] }}</span></td>
                                <td class="text-right in-col">
                                    {{ $row['in'] > 0 ? '+' . number_format($row['in']) : '—' }}</td>
                                <td class="text-right out-col">
                                    {{ $row['out'] > 0 ? '-' . number_format($row['out']) : '—' }}</td>
                                <td class="text-right rem-col">{{ number_format($row['remaining']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-dark font-weight-bold">
                            <td colspan="5" class="text-right">Total</td>
                            <td class="text-right in-col">+{{ number_format($totalIn) }}</td>
                            <td class="text-right out-col">-{{ number_format($totalOut) }}</td>
                            <td class="text-right rem-col">{{ number_format($closing) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</div>

<script>
    // Added: 2026-07-20 - DataTable + Buttons (Excel/CSV/Print/Copy), reload-safe destroy
    (function() {
        if ($.fn.dataTable.isDataTable('#simpleLedgerTable')) {
            $('#simpleLedgerTable').DataTable().destroy();
        }
        if ($('#simpleLedgerTable').length) {
            $('#simpleLedgerTable').DataTable({
                paging: false,
                info: false,
                searching: true,
                ordering: true,
                order: [],
                language: {
                    search: "Filter:"
                },
                dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rt',
                buttons: [{
                        extend: 'copyHtml5',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn btn-sm btn-outline-secondary'
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-sm btn-outline-secondary',
                        title: 'product-ledger-{{ $product->id ?? '' }}'
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-sm btn-outline-success',
                        title: 'product-ledger-{{ $product->id ?? '' }}'
                    }
                ],
                columnDefs: [{
                    targets: [5, 6, 7],
                    className: 'text-right'
                }]
            });
        }
    })();

    // Added: 2026-07-20 - print শুধু modal-এর content (badge color সহ)
    function printSimpleLedger() {
        const content = document.getElementById('simpleLedgerPrintArea').innerHTML;
        const win = window.open('', '', 'width=950,height=650');
        win.document.write(`
            <html>
            <head>
                <title>Product Ledger</title>
        
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
                <style>
                    body { padding: 20px; }
                    .d-print-block { display: block !important; }
                    .no-print { display: none !important; }
                    table th, table td { font-size: 12px; }
                    .type-badge { font-size: 10px; padding: 2px 7px; border-radius: 999px; font-weight: 600; }
                    .badge-opening    { background: #dbeafe; color: #1e40af; }
                    .badge-purchase   { background: #dcfce7; color: #166534; }
                    .badge-consume    { background: #ffedd5; color: #9a3412; }
                    .badge-gain       { background: #d1fae5; color: #065f46; }
                    .badge-loss       { background: #fee2e2; color: #991b1b; }
                    .badge-damage     { background: #fecaca; color: #7f1d1d; }
                    .badge-adjustment { background: #ede9fe; color: #5b21b6; }
                    .badge-transfer-in  { background: #cffafe; color: #155e75; }
                    .badge-transfer-out { background: #fef3c7; color: #92400e; }
                    .badge-sale       { background: #fee2e2; color: #991b1b; }
                    .in-col { color: #166534; font-weight: 700; }
                    .out-col { color: #991b1b; font-weight: 700; }
                </style>
            </head>
            <body>${content}</body>
            </html>
        `);
        win.document.close();
        win.focus();
        setTimeout(() => {
            win.print();
            win.close();
        }, 300);
    }
</script>
