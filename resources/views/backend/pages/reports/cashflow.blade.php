@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }

        /* ===== Cash Flow Dashboard Style - Added: 2026-07-04 ===== */
        .cf-summary-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .cf-summary-card {
            flex: 1;
            min-width: 200px;
            background: #fff;
            border-radius: 8px;
            padding: 16px 18px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border-top: 3px solid #ccc;
        }

        .cf-summary-card.operating {
            border-top-color: #2980b9;
        }

        .cf-summary-card.investing {
            border-top-color: #c0392b;
        }

        .cf-summary-card.financing {
            border-top-color: #27ae60;
        }

        .cf-summary-card.net {
            border-top-color: #8e44ad;
        }

        .cf-summary-card .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #95a5a6;
            font-weight: 600;
        }

        .cf-summary-card .value {
            font-size: 20px;
            font-weight: 700;
            margin-top: 4px;
            font-family: 'Consolas', monospace;
        }

        .cf-summary-card .value.neg {
            color: #c0392b;
        }

        .cf-panel {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            margin-bottom: 18px;
            overflow: hidden;
        }

        .cf-panel-head {
            padding: 12px 18px;
            font-weight: 700;
            font-size: 14px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cf-panel-head.operating {
            background: #2980b9;
        }

        .cf-panel-head.investing {
            background: #c0392b;
            cursor: pointer;
        }

        .cf-panel-head.financing {
            background: #27ae60;
            cursor: pointer;
        }

        table.cf-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
            margin-bottom: 0;
        }

        table.cf-table td {
            padding: 9px 18px;
            border-bottom: 1px solid #f1f2f6;
        }

        table.cf-table tr:last-child td {
            border-bottom: none;
        }

        table.cf-table td:last-child {
            text-align: right;
            font-family: 'Consolas', monospace;
            white-space: nowrap;
            width: 180px;
        }

        .cf-neg {
            color: #c0392b;
        }

        .cf-panel-total {
            display: flex;
            justify-content: space-between;
            padding: 10px 18px;
            background: #f8f9fa;
            font-weight: 700;
            font-size: 14px;
            border-top: 1px solid #eee;
        }

        .cf-reconcile {
            background: #1c2833;
            color: #fff;
            border-radius: 8px;
            padding: 18px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .cf-reconcile .item {
            text-align: center;
        }

        .cf-reconcile .item .k {
            font-size: 11px;
            color: #95a5a6;
            text-transform: uppercase;
        }

        .cf-reconcile .item .v {
            font-size: 17px;
            font-weight: 700;
            font-family: 'Consolas', monospace;
        }

        .cf-reconcile .arrow {
            font-size: 20px;
            color: #576574;
        }

        /* ===== Collapsible Details - Added: 2026-07-05 ===== */
        .cf-details-btn {
            background: rgba(28, 214, 3, 0.267);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #fff;
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }

        .cf-details-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .cf-subgroup-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 18px;
            background: #eaf2f8;
            color: #2980b9;
            font-weight: 600;
            cursor: pointer;
        }

        .cf-subgroup-row .cf-toggle-icon {
            transition: transform 0.2s;
            margin-right: 6px;
            display: inline-block;
        }

        .cf-subgroup-row.collapsed .cf-toggle-icon {
            transform: rotate(-90deg);
        }

        .cf-panel-head .cf-toggle-icon {
            transition: transform 0.2s;
            margin-right: 8px;
            display: inline-block;
        }

        .cf-panel-head.collapsed .cf-toggle-icon {
            transform: rotate(-90deg);
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .cf-summary-card,
            .cf-panel,
            .cf-reconcile {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            /* Print করার সময় সব details expanded দেখাবে */
            .collapse {
                display: block !important;
                height: auto !important;
            }

            .cf-details-btn {
                display: none;
            }
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Cash Flow Report</h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('report.cashflow') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card card-outline card-info no-print">
                    <div class="card-body">
                        <div class="row no-print">
                            <div class="box-header with-border" style="cursor: pointer;">
                                <h6 class="box-title"><i class="fa fa-filter" aria-hidden="true"></i> Filters</h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>From Date:</label>
                                    <input type="date" class="form-control" name="from_date"
                                        value="{{ $startDate ?? '' }}" />
                                    @error('from_date')
                                        <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>To Date:</label>
                                    <input type="date" class="form-control" name="to_date" value="{{ $toDate ?? '' }}" />
                                    @error('to_date')
                                        <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-search"></i>
                                        Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="load_data"></div>
            </form>
        </div>

        @if ($request->method() == 'POST' && $from_date)
            <div class="col-md-12">

                <div class="d-flex justify-content-between align-items-center mb-2 no-print">
                    <h5 class="m-0">{{ $companyInfo->company_name ?? 'N/A' }} &nbsp;•&nbsp; {{ $from_date }} to
                        {{ $to_date }}</h5>
                    <a onclick="window.print()" class="btn btn-default btn-sm"><i class="fas fa-print"></i> Print</a>
                </div>

                {{-- ===== SUMMARY CARDS ===== --}}
                <div class="cf-summary-row">
                    <div class="cf-summary-card operating">
                        <div class="label">Operating Activities</div>
                        <div class="value {{ $operatingTotal < 0 ? 'neg' : '' }}">
                            {{ number_format($operatingTotal, 2) }}
                        </div>
                    </div>
                    <div class="cf-summary-card investing">
                        <div class="label">Investing Activities</div>
                        <div class="value {{ $investingTotal < 0 ? 'neg' : '' }}">
                            {{ number_format($investingTotal, 2) }}
                        </div>
                    </div>
                    <div class="cf-summary-card financing">
                        <div class="label">Financing Activities</div>
                        <div class="value {{ $financingTotal < 0 ? 'neg' : '' }}">
                            {{ number_format($financingTotal, 2) }}
                        </div>
                    </div>
                    <div class="cf-summary-card net">
                        <div class="label">Net Change in Cash</div>
                        <div class="value {{ $netChange < 0 ? 'neg' : '' }}">
                            {{ number_format($netChange, 2) }}
                        </div>
                    </div>
                </div>

                {{-- ===== OPERATING PANEL ===== --}}
                <div class="cf-panel">
                    <div class="cf-panel-head operating">
                        <span>Cash Flow from Operating Activities</span>
                        <span>{{ number_format($operatingTotal, 2) }}</span>
                    </div>

                    @forelse ($operatingGroups as $index => $group)
                        {{-- sub-group summary row - click to expand/collapse details --}}
                        <div class="cf-subgroup-row collapsed" data-toggle="collapse"
                            data-target="#op-group-{{ $index }}" aria-expanded="false">
                            <span><span class="cf-toggle-icon">▼</span>{{ $group['label'] }}</span>
                            <span>
                                {{ number_format($group['total'], 2) }}
                                <button type="button" class="cf-details-btn">Details</button>
                            </span>
                        </div>

                        {{-- collapsible details table --}}
                        <div class="collapse" id="op-group-{{ $index }}">
                            <table class="cf-table">
                                @foreach ($group['rows'] as $row)
                                    <tr>
                                        <td>
                                            {!! $row->account_id == 0
                                                ? '<span style="color:#c0392b">Uncategorized (Suspense)</span>'
                                                : accountledger($row->account_id, $row->account->account_name ?? '') !!}
                                        </td>
                                        @php($net = $row->cash_effect)
                                        <td class="{{ $net < 0 ? 'cf-neg' : '' }}">
                                            {{ $net < 0 ? '(' . number_format(abs($net), 2) . ')' : number_format($net, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @empty
                        <table class="cf-table">
                            <tr>
                                <td colspan="2" class="text-center text-muted">No operating activity in this period</td>
                            </tr>
                        </table>
                    @endforelse

                    <div class="cf-panel-total">
                        <span>Net Cash from Operating Activities</span>
                        <span>{{ number_format($operatingTotal, 2) }}</span>
                    </div>
                </div>

                {{-- ===== INVESTING PANEL ===== --}}
                <div class="cf-panel">
                    <div class="cf-panel-head investing collapsed" data-toggle="collapse" data-target="#investing-details"
                        aria-expanded="false">
                        <span><span class="cf-toggle-icon">▼</span>Cash Flow from Investing Activities</span>
                        <span>
                            {{ number_format($investingTotal, 2) }}
                            <button type="button" class="cf-details-btn">Details</button>
                        </span>
                    </div>

                    <div class="collapse" id="investing-details">
                        <table class="cf-table">
                            @forelse ($investing as $row)
                                <tr>
                                    <td>{!! accountledger($row->account_id, $row->account->account_name ?? '') !!}</td>
                                    @php($net = $row->credit - $row->debit)
                                    <td class="{{ $net < 0 ? 'cf-neg' : '' }}">
                                        {{ $net < 0 ? '(' . number_format(abs($net), 2) . ')' : number_format($net, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No investing activity in this period
                                    </td>
                                </tr>
                            @endforelse
                        </table>
                    </div>

                    <div class="cf-panel-total">
                        <span>Net Cash Used in Investing Activities</span>
                        <span>{{ number_format($investingTotal, 2) }}</span>
                    </div>
                </div>

                {{-- ===== FINANCING PANEL ===== --}}
                <div class="cf-panel">
                    <div class="cf-panel-head financing collapsed" data-toggle="collapse" data-target="#financing-details"
                        aria-expanded="false">
                        <span><span class="cf-toggle-icon">▼</span>Cash Flow from Financing Activities</span>
                        <span>
                            {{ number_format($financingTotal, 2) }}
                            <button type="button" class="cf-details-btn">Details</button>
                        </span>
                    </div>

                    <div class="collapse" id="financing-details">
                        <table class="cf-table">
                            @forelse ($financing as $row)
                                <tr>
                                    <td>{!! accountledger($row->account_id, $row->account->account_name ?? '') !!}</td>
                                    @php($net = $row->credit - $row->debit)
                                    <td class="{{ $net < 0 ? 'cf-neg' : '' }}">
                                        {{ $net < 0 ? '(' . number_format(abs($net), 2) . ')' : number_format($net, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No financing activity in this period
                                    </td>
                                </tr>
                            @endforelse
                        </table>
                    </div>

                    <div class="cf-panel-total">
                        <span>Net Cash from Financing Activities</span>
                        <span>{{ number_format($financingTotal, 2) }}</span>
                    </div>
                </div>

                {{-- ===== RECONCILIATION BAR ===== --}}
                <div class="cf-reconcile mb-3">
                    <div class="item">
                        <div class="k">Opening Cash</div>
                        <div class="v">{{ number_format($newOpeningBalance, 2) }}</div>
                    </div>
                    <div class="arrow">+</div>
                    <div class="item">
                        <div class="k">Net Change</div>
                        <div class="v {{ $netChange < 0 ? 'cf-neg' : '' }}">{{ number_format($netChange, 2) }}</div>
                    </div>
                    <div class="arrow">=</div>
                    <div class="item">
                        <div class="k">Closing Cash</div>
                        <div class="v">{{ number_format($closingBalance, 2) }}</div>
                    </div>

                    {{-- Verification: computed closing vs actual ledger closing --}}
                    @isset($actualClosing)
                        <div class="arrow">✓</div>
                        <div class="item">
                            <div class="k">Ledger Closing</div>
                            <div class="v {{ abs($reconDifference) > 0.01 ? 'cf-neg' : '' }}">
                                {{ number_format($actualClosing, 2) }}
                            </div>
                        </div>
                        @if (abs($reconDifference) > 0.01)
                            <div class="item">
                                <div class="k">Difference</div>
                                <div class="v cf-neg">{{ number_format($reconDifference, 2) }}</div>
                            </div>
                        @endif
                    @endisset
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <p>Prepared By:_____________<br>Date:____________________</p>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-2">
                        <p>Approved By:________________<br>Date:_________________</p>
                    </div>
                </div>

                <div class="bg-success text-center p-2">
                    Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} products. We believe you will be
                    satisfied by our services.
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    @include('backend.pages.reports.excel')


    <script>
        $(document).ready(function() {
            $('.cf-subgroup-row, .cf-panel-head[data-toggle="collapse"]').on('click', function() {
                $(this).toggleClass('collapsed');
            });

            $('.collapse').on('show.bs.collapse', function() {
                var target = $(this).attr('id');
                $('[data-target="#' + target + '"]').removeClass('collapsed');
            });

            $('.collapse').on('hide.bs.collapse', function() {
                var target = $(this).attr('id');
                $('[data-target="#' + target + '"]').addClass('collapsed');
            });
        });
    </script>
@endsection
