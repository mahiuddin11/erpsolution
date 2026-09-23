{{-- Modified: 2026-07-20 - professional styling matched to exact types from getProductLedgerData() --}}
{{-- Updated: 2026-09-21 - responsive layout, sticky header, type filter, clean print, scoped CSS --}}
<div id="ledgerPartial">
    <style>
        /* Updated: 2026-09-21 - sob selector #ledgerPartial-er moddhe scope kora (age .btn-secondary / .alert-warning shara page-e leak korto) */
        #ledgerPartial #simpleLedgerTable thead th {
            background: #1e293b;
            color: #fff;
            white-space: nowrap;
            font-size: 12.5px;
            /* >>> NEW: sticky header (scroll korle heading thake) */
            position: sticky;
            top: 0;
            z-index: 2;
        }

        #ledgerPartial #simpleLedgerTable td {
            font-size: 13px;
            vertical-align: middle;
        }

        #ledgerPartial #simpleLedgerTable tbody tr:hover {
            background: #f8fafc;
        }

        #ledgerPartial #simpleLedgerTable td.ledger-date {
            white-space: nowrap;
        }

        #ledgerPartial #simpleLedgerTable td.ledger-invoice {
            word-break: break-word;
            min-width: 110px;
        }

        #ledgerPartial .type-badge {
            font-size: 11px;
            padding: 3px 9px;
            border-radius: 999px;
            white-space: nowrap;
            font-weight: 600;
            display: inline-block;
        }

        #ledgerPartial .badge-opening {
            background: #dbeafe;
            color: #1e40af;
        }

        #ledgerPartial .badge-purchase {
            background: #dcfce7;
            color: #166534;
        }

        #ledgerPartial .badge-consume {
            background: #ffedd5;
            color: #9a3412;
        }

        #ledgerPartial .badge-gain {
            background: #d1fae5;
            color: #065f46;
        }

        #ledgerPartial .badge-loss {
            background: #fee2e2;
            color: #991b1b;
        }

        #ledgerPartial .badge-damage {
            background: #fecaca;
            color: #7f1d1d;
        }

        #ledgerPartial .badge-adjustment {
            background: #ede9fe;
            color: #5b21b6;
        }

        #ledgerPartial .badge-transfer-in {
            background: #cffafe;
            color: #155e75;
        }

        #ledgerPartial .badge-transfer-out {
            background: #fef3c7;
            color: #92400e;
        }

        #ledgerPartial .badge-sale {
            background: #fee2e2;
            color: #991b1b;
        }

        #ledgerPartial .in-col {
            color: #166534;
            font-weight: 700;
        }

        #ledgerPartial .out-col {
            color: #991b1b;
            font-weight: 700;
        }

        #ledgerPartial .rem-col {
            color: #1e40af;
            font-weight: 700;
        }

        /* >>> NEW: negative balance highlight */
        #ledgerPartial .rem-col.neg {
            color: #dc2626;
            background: #fef2f2;
        }

        #ledgerPartial .alert-warning {
            color: #1f2d3d;
            background-color: #07d4ff14;
            border-color: #0f5143;
        }

        /* Header (product name + print) */
        #ledgerPartial .ledger-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 12px 15px 8px;
        }

        /* >>> NEW (2026-09-21) - KPI cards (stock summary-r info-box design-er moto, icon soho) */
        #ledgerPartial .ledger-kpis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 12px;
            margin: 0 15px 14px;
        }

        #ledgerPartial .ledger-kpis .info-box {
            margin: 0;
            min-height: 72px;
            width: 100%;
        }

        #ledgerPartial .ledger-kpis .info-box-icon {
            width: 60px;
            font-size: 1.4rem;
        }

        #ledgerPartial .ledger-kpis .info-box-content {
            padding: 6px 10px;
            min-width: 0;
        }

        #ledgerPartial .ledger-kpis .info-box-text {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .3px;
            color: #6c757d;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #ledgerPartial .ledger-kpis .info-box-number {
            font-size: 20px;
            font-weight: 700;
            margin-top: 0;
        }

        #ledgerPartial .ledger-kpis .kpi-in {
            color: #166534;
        }

        #ledgerPartial .ledger-kpis .kpi-out {
            color: #991b1b;
        }

        #ledgerPartial .ledger-kpis .kpi-neg {
            color: #dc2626;
        }

        /* <<< END NEW */

        /* Toolbar: Export buttons + Type filter + Search */
        #ledgerPartial .ledger-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        #ledgerPartial .ledger-toolbar .dt-buttons {
            display: flex;
            gap: 4px;
        }

        #ledgerPartial .ledger-toolbar .ledger-right {
            margin-left: auto;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }

        #ledgerPartial .ledger-toolbar .dataTables_filter {
            margin: 0;
        }

        #ledgerPartial .ledger-toolbar .dataTables_filter label {
            margin: 0;
        }

        #ledgerPartial .ledger-toolbar .dataTables_filter input {
            margin-left: 0;
            width: 190px;
        }

        #ledgerPartial .ledger-type-filter {
            width: auto;
            min-width: 150px;
        }

        /* Table scroll area: sticky header + horizontal scroll fallback */
        #ledgerPartial .ledger-scroll {
            max-height: 60vh;
            overflow: auto;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }

        #ledgerPartial .ledger-scroll table {
            margin-bottom: 0 !important;
        }

        #ledgerPartial .ledger-foot th {
            background: #1e293b;
            color: #fff;
            font-size: 13px;
            white-space: nowrap;
        }

        #ledgerPartial .ledger-foot .in-col {
            color: #4ade80;
        }

        #ledgerPartial .ledger-foot .out-col {
            color: #f87171;
        }

        #ledgerPartial .ledger-foot .rem-col {
            color: #38bdf8;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before {
            background-color: #007bff;
        }

        /* Mobile */
        @media (max-width: 767px) {
            #ledgerPartial .ledger-head {
                padding: 6px 4px 8px;
            }

            #ledgerPartial .ledger-kpis {
                margin: 0 0 12px;
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }

            #ledgerPartial .ledger-kpis .info-box:last-child {
                grid-column: span 2;
            }

            #ledgerPartial .ledger-kpis .info-box {
                min-height: 64px;
            }

            #ledgerPartial .ledger-kpis .info-box-icon {
                width: 46px;
                font-size: 1.1rem;
            }

            #ledgerPartial .ledger-kpis .info-box-number {
                font-size: 17px;
            }

            #ledgerPartial .ledger-toolbar .ledger-right {
                margin-left: 0;
                width: 100%;
            }

            #ledgerPartial .ledger-toolbar .dataTables_filter,
            #ledgerPartial .ledger-toolbar .ledger-type-filter {
                flex: 1 1 100%;
                width: 100%;
            }

            #ledgerPartial .ledger-toolbar .dataTables_filter input {
                width: 100%;
            }

            #ledgerPartial .ledger-scroll {
                max-height: 55vh;
            }

            #ledgerPartial .px-3 {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            #ledgerPartial #simpleLedgerTable td,
            #ledgerPartial #simpleLedgerTable th {
                padding: .35rem .4rem;
            }
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>

    <div class="ledger-head no-print">
        <div>
            <h6 class="mb-0 font-weight-bold">{{ $product->productCode ?? '' }} — {{ $product->name ?? '' }}</h6>
            <small class="text-muted"><i class="far fa-calendar-alt"></i> {{ $from_date }} to
                {{ $to_date }}</small>
        </div>
        <button type="button" class="btn btn-sm btn-primary" onclick="printSimpleLedger()">
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
            <div class="alert alert-warning text-center py-4 mx-3">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                No movement found for this product.
            </div>
        @else
            @php
                $totalIn = collect($datas)->sum('in');
                $totalOut = collect($datas)->sum('out');
                $closing = collect($datas)->last()['remaining'] ?? 0;
                $opening = collect($datas)->where('type', 'Opening Stock')->sum('in');
            @endphp

            {{-- Updated: 2026-09-21 - KPI cards (icon soho) --}}
            <div class="ledger-kpis no-print">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-box-open"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Opening</span>
                        <span class="info-box-number">{{ number_format($opening) }}</span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-arrow-circle-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total In</span>
                        <span class="info-box-number kpi-in">+{{ number_format($totalIn - $opening) }}</span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-arrow-circle-up"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Out</span>
                        <span class="info-box-number kpi-out">-{{ number_format($totalOut) }}</span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon {{ $closing < 0 ? 'bg-danger' : 'bg-primary' }}"><i
                            class="fas {{ $closing < 0 ? 'fa-exclamation-triangle' : 'fa-warehouse' }}"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Closing Stock</span>
                        <span
                            class="info-box-number {{ $closing < 0 ? 'kpi-neg' : '' }}">{{ number_format($closing) }}</span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-list-ol"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Entries</span>
                        <span class="info-box-number">{{ count($datas) }}</span>
                    </div>
                </div>
            </div>

            <div class="px-3 pb-3">
                <div class="ledger-scroll">
                    <table id="simpleLedgerTable" class="table table-bordered table-sm table-hover" style="width:100%">
                        <thead>
                            {{-- Updated: data-priority = choto screen-e ke age hide hobe (boro number = age hide) --}}
                            <tr>
                                <th class="all">SL</th>
                                <th data-priority="2">Date</th>
                                <th data-priority="8">Invoice</th>
                                <th data-priority="7">Branch/Project</th>
                                <th data-priority="6">Warehouse</th>
                                <th data-priority="5">Type</th>
                                <th data-priority="3" class="text-right">In</th>
                                <th data-priority="4" class="text-right">Out</th>
                                <th data-priority="1" class="text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $row)
                                @php
                                    // Modified: 2026-07-20 - exact type match onujayi badge color
                                    // (getProductLedgerData() ja ja type generate kore tar sobgulo cover kora holo)
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
                                    <td class="ledger-date">{{ $row['date'] }}</td>
                                    <td class="ledger-invoice"><small>{{ $row['invoice'] }}</small></td>
                                    <td>{{ $row['branch'] }}</td>
                                    {{-- >>> NEW: Warehouse (controller row-e 'warehouse' key na thakle '-' dekhabe) --}}
                                    <td>{{ $row['warehouse'] ?? '-' }}</td>
                                    <td><span class="type-badge {{ $typeClass }}">{{ $row['type'] }}</span></td>
                                    {{-- >>> NEW: data-order = number sort thik kaj korbe ("+1,200" string sort hoto) --}}
                                    <td class="text-right in-col" data-order="{{ $row['in'] }}">
                                        {{ $row['in'] > 0 ? '+' . number_format($row['in']) : '—' }}</td>
                                    <td class="text-right out-col" data-order="{{ $row['out'] }}">
                                        {{ $row['out'] > 0 ? '-' . number_format($row['out']) : '—' }}</td>
                                    <td class="text-right rem-col {{ $row['remaining'] < 0 ? 'neg' : '' }}"
                                        data-order="{{ $row['remaining'] }}">{{ number_format($row['remaining']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- Updated: 2026-09-21 - colspan bad, protita column-er alada cell (responsive-e hidden column-e footer bhenge jabe na) --}}
                        <tfoot>
                            <tr class="ledger-foot">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="text-right">Total</th>
                                <th class="text-right in-col">+{{ number_format($totalIn) }}</th>
                                <th class="text-right out-col">-{{ number_format($totalOut) }}</th>
                                <th class="text-right rem-col">{{ number_format($closing) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Added: 2026-07-20 - DataTable + Buttons (Excel/CSV/Copy), reload-safe destroy
        // Updated: 2026-09-21 - responsive, type filter, live In/Out footer, recalc on tab switch
        (function() {
            if ($.fn.dataTable.isDataTable('#simpleLedgerTable')) {
                $('#simpleLedgerTable').DataTable().destroy();
            }
            if (!$('#simpleLedgerTable').length) {
                return;
            }

            const fmt = function(n) {
                return Number(n || 0).toLocaleString('en-US', {
                    maximumFractionDigits: 2
                });
            };
            const exportCols = [0, 1, 2, 3, 4, 5, 6, 7, 8]; // hidden (responsive) column-o export hobe

            const table = $('#simpleLedgerTable').DataTable({
                responsive: true,
                autoWidth: false,
                paging: false,
                info: false,
                searching: true,
                ordering: true,
                order: [],
                language: {
                    search: "",
                    searchPlaceholder: "Search entries...",
                    zeroRecords: "No matching entries"
                },
                dom: '<"ledger-toolbar"B<"ledger-right"f>>rt',
                buttons: [{
                        extend: 'copyHtml5',
                        text: '<i class="fas fa-copy"></i><span class="d-none d-sm-inline"> Copy</span>',
                        className: 'btn btn-sm btn-outline-secondary',
                        exportOptions: {
                            columns: exportCols
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i><span class="d-none d-sm-inline"> CSV</span>',
                        className: 'btn btn-sm btn-outline-secondary',
                        title: 'product-ledger-{{ $product->id ?? '' }}',
                        exportOptions: {
                            columns: exportCols
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i><span class="d-none d-sm-inline"> Excel</span>',
                        className: 'btn btn-sm btn-outline-success',
                        title: 'product-ledger-{{ $product->id ?? '' }}',
                        exportOptions: {
                            columns: exportCols
                        }
                    }
                ],
                columnDefs: [{
                    targets: [6, 7, 8],
                    className: 'text-right'
                }],
                // filter/search korle In / Out total oi onujayi bodle jay; Balance sudhu filter na thakle dekhay
                footerCallback: function() {
                    const api = this.api();
                    const sum = function(idx) {
                        let s = 0;
                        api.column(idx, {
                            search: 'applied'
                        }).nodes().each(function(td) {
                            s += parseFloat($(td).attr('data-order')) || 0;
                        });
                        return s;
                    };
                    const filtered = api.rows({
                        search: 'applied'
                    }).count() !== api.rows().count();

                    if (!filtered) {
                        return; // original server-side total-i thakbe
                    }
                    $(api.column(6).footer()).text('+' + fmt(sum(6)));
                    $(api.column(7).footer()).text('-' + fmt(sum(7)));
                    $(api.column(8).footer()).text('—');
                },
                initComplete: function() {
                    const api = this.api();

                    // >>> NEW: Type dropdown filter (Purchase / Sale / Transfer ...)
                    const types = new Set();
                    api.column(5).data().each(function(d) {
                        const t = $('<div>').html(d).text().trim();
                        if (t) types.add(t);
                    });

                    const $sel = $(
                        '<select class="form-control form-control-sm ledger-type-filter"><option value="">All Types</option></select>'
                    );
                    Array.from(types).sort().forEach(function(t) {
                        $sel.append($('<option>').val(t).text(t));
                    });
                    $sel.on('change', function() {
                        const v = $(this).val();
                        api.column(5).search(
                            v ? '^' + $.fn.dataTable.util.escapeRegex(v) + '$' : '',
                            true,
                            false
                        ).draw();
                    });
                    $(api.table().container()).find('.dataTables_filter').before($sel);
                }
            });

            // >>> NEW: modal animation / tab switch-er por width thik kora (responsive recalc)
            const recalc = function() {
                setTimeout(function() {
                    table.columns.adjust();
                    if (table.responsive) {
                        table.responsive.recalc();
                    }
                }, 200);
            };
            recalc();
            $('#productLedgerModal').off('shown.bs.modal.ledger').on('shown.bs.modal.ledger', recalc);
            $('#btnShowLedger').off('click.ledger').on('click.ledger', recalc);
        })();

        // Added: 2026-07-20 - print shudhu modal-er content (badge color soho)
        // Updated: 2026-09-21 - DataTable-er search/buttons print-e ashbe na, responsive hidden column-o print hobe,
        //                       filter/sort kora obosthaye print hobe
        function printSimpleLedger() {
            const $src = $('#simpleLedgerTable');
            if (!$src.length) {
                return;
            }
            const dt = $src.DataTable();

            const cleanClass = function(cls) {
                return (cls || '').replace(/dtr-[\w-]+/g, '').replace(/\s+/g, ' ').trim();
            };

            const head = '<tr>' + $src.find('thead th').map(function() {
                return '<th class="' + ($(this).hasClass('text-right') ? 'text-right' : '') + '">' +
                    $(this).text().trim() + '</th>';
            }).get().join('') + '</tr>';

            let body = '';
            dt.rows({
                search: 'applied',
                order: 'applied'
            }).every(function() {
                body += '<tr>' + $(this.node()).children('td').map(function() {
                    return '<td class="' + cleanClass($(this).attr('class')) + '">' + $(this).html() +
                        '</td>';
                }).get().join('') + '</tr>';
            });

            const foot = '<tr class="ledger-foot">' + $src.find('tfoot th').map(function() {
                return '<th class="' + cleanClass($(this).attr('class')) + '">' + $(this).html() + '</th>';
            }).get().join('') + '</tr>';

            const titleBlock = $('#simpleLedgerPrintArea .d-print-block').html() || '';

            const win = window.open('', '', 'width=950,height=650');
            if (!win) {
                alert('Popup blocked. Please allow popups for this site to print.');
                return;
            }

            win.document.write(`
                <html>
                <head>
                    <title>Product Ledger</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
                    <style>
                        body { padding: 20px; }
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
                        .rem-col { color: #1e40af; font-weight: 700; }
                        .rem-col.neg { color: #dc2626; }
                        .ledger-foot th { background: #eee; }
                        thead { display: table-header-group; }
                        tr { page-break-inside: avoid; }
                    </style>
                </head>
                <body>
                    <div class="text-center mb-3">${titleBlock}</div>
                    <table class="table table-bordered table-sm">
                        <thead>${head}</thead>
                        <tbody>${body}</tbody>
                        <tfoot>${foot}</tfoot>
                    </table>
                </body>
                </html>
            `);
            win.document.close();
            win.focus();
            setTimeout(() => {
                win.print();
                win.close();
            }, 500);
        }
    </script>
</div>
