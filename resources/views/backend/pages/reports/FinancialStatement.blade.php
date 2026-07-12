@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .nfs-wrapper {
            background: #fff;
            padding: 20px;
            font-family: 'Times New Roman', Times, serif;
            color: #1a1a1a;
        }

        .nfs-header {
            text-align: center;
            border-bottom: 3px double #1a1a1a;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .nfs-header h4 {
            margin: 0;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nfs-header .sub {
            font-size: 12px;
            margin-top: 2px;
        }

        .note-block {
            margin-bottom: 22px;
        }

        .note-heading {
            font-size: 13.5px;
            font-weight: 700;
            background: #f2f2f2;
            padding: 5px 8px;
            border-left: 4px solid #1a1a1a;
            margin-bottom: 8px;
        }

        .note-body {
            font-size: 12.5px;
            line-height: 1.6;
            text-align: justify;
        }

        table.note-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 4px;
            font-size: 12px;
        }

        table.note-table th,
        table.note-table td {
            border: 1px solid #555;
            padding: 5px 8px;
        }

        table.note-table thead th {
            background: #f2f2f2;
            text-align: center;
            font-weight: 700;
        }

        table.note-table td.num {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        table.note-table td.label {
            text-align: left;
        }

        tr.total-row td {
            font-weight: 700;
            border-top: 2px solid #1a1a1a;
            background: #f8f9fa;
        }

        tr.negative td.num {
            color: #c0392b;
        }

        tr.suspicious {
            background: #fff8e1;
        }

        tr.suspicious td.label::after {
            content: " ⚠";
            color: #b8860b;
        }

        .config-warning {
            background: #fdecea;
            color: #c0392b;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 11.5px;
            margin-bottom: 10px;
        }

        .data-warning {
            background: #fff8e1;
            color: #8a6d00;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 11.5px;
            margin-bottom: 10px;
            border: 1px solid #f0d97a;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Report - {{ $title }}</h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    @php
        // Consistent negative-number formatting across every note table:
        // accounting convention uses parentheses, not a leading minus sign.
        // Guarded with function_exists() in case this view is ever rendered
        // more than once within the same PHP request (e.g. nested includes).
        if (!function_exists('fmt')) {
            function fmt($n, $decimals = 0)
            {
                $n = (float) $n;
                if ($n < 0) {
                    return '(' . number_format(abs($n), $decimals) . ')';
                }
                if ($n == 0) {
                    return '-';
                }
                return number_format($n, $decimals);
            }
        }
    @endphp

    <div class="row">
        <div class="col-md-12 no-print">
            <div class="card card-outline card-info">
                <div class="card-body">
                    <form action="{{ route('report.financialstatements') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From Date:</label>
                                    <input type="date" class="form-control" name="from_date"
                                        value="{{ $from_date ?? '' }}">
                                    @error('from_date')
                                        <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To Date:</label>
                                    <input type="date" class="form-control" name="to_date" value="{{ $to_date ?? '' }}">
                                    @error('to_date')
                                        <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-search"></i>
                                        Generate</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($request->method() == 'POST' && $from_date)
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header no-print">
                        <h3 class="card-title">Notes to the Financial Statements</h3>
                        <a onclick="window.print()" class="btn btn-default float-right my-2"><i class="fas fa-print"></i>
                            Print</a>
                    </div>
                    <div class="card-body">
                        <div class="nfs-wrapper">

                            <div class="nfs-header">
                                <h4>{{ $companyInfo->company_name ?? 'N/A' }}</h4>
                                <div class="sub">NOTES TO THE FINANCIAL STATEMENTS</div>
                                <div class="sub">FOR THE YEAR ENDED {{ strtoupper(date('jS F Y', strtotime($to_date))) }}
                                </div>
                                <div class="sub">(Amount in BDT)</div>
                            </div>

                            {{-- Note: Property, Plant & Equipment --}}
                            <div class="note-block">
                                <div class="note-heading">Note 1 — Property, Plant &amp; Equipment</div>
                                <div class="note-body">

                                    @if (count($fixedAssetSchedule['flaggedRows']) > 0)
                                        <div class="data-warning">
                                            ⚠ Data integrity: {{ count($fixedAssetSchedule['flaggedRows']) }}
                                            asset(s) below show a depreciation/disposal charge with <strong>zero
                                                recorded opening balance and zero addition</strong> in this system —
                                            meaning a write-off was posted against an asset whose original cost was
                                            never capitalised here. Please reconcile these against the physical
                                            Fixed Asset register before this note is finalised:
                                            <strong>{{ implode(', ', $fixedAssetSchedule['flaggedRows']) }}</strong>
                                        </div>
                                    @endif

                                    @if ($fixedAssetSchedule['totalNegative'])
                                        <div class="config-warning">
                                            ⚠ Total closing Property, Plant &amp; Equipment is
                                            <strong>negative
                                                ({{ fmt($fixedAssetSchedule['totals']['closing']) }})</strong>.
                                            This cannot be presented as-is in a statutory report — it indicates the
                                            data-integrity issue above needs to be resolved first.
                                        </div>
                                    @endif

                                    <table class="note-table">
                                        <thead>
                                            <tr>
                                                <th class="label">Category</th>
                                                <th>Opening</th>
                                                <th>Addition</th>
                                                <th>Disposal / Depreciation</th>
                                                <th>Closing</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($fixedAssetSchedule['rows'] as $row)
                                                <tr class="{{ $row['suspicious'] ? 'suspicious' : '' }}">
                                                    <td class="label">{{ $row['label'] }}</td>
                                                    <td class="num">{{ fmt($row['opening']) }}</td>
                                                    <td class="num">{{ fmt($row['addition']) }}</td>
                                                    <td class="num {{ $row['disposal'] < 0 ? 'negative' : '' }}">
                                                        {{ fmt($row['disposal']) }}
                                                    </td>
                                                    <td class="num {{ $row['negative_closing'] ? 'negative' : '' }}">
                                                        {{ fmt($row['closing']) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="total-row">
                                                <td class="label">Total</td>
                                                <td class="num">{{ fmt($fixedAssetSchedule['totals']['opening']) }}</td>
                                                <td class="num">{{ fmt($fixedAssetSchedule['totals']['addition']) }}
                                                </td>
                                                <td class="num">{{ fmt($fixedAssetSchedule['totals']['disposal']) }}
                                                </td>
                                                <td
                                                    class="num {{ $fixedAssetSchedule['totalNegative'] ? 'negative' : '' }}">
                                                    {{ fmt($fixedAssetSchedule['totals']['closing']) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p class="no-print" style="font-size:11px; color:#856404;">
                                        ⚠ Note: Depreciation and Disposal are currently combined in one column, since
                                        depreciation journal entries post directly into the Fixed Asset tree (known
                                        reconciliation item — see Cash Flow Statement notes).
                                    </p>
                                </div>
                            </div>

                            {{-- Note: Reserve and Surplus --}}
                            <div class="note-block">
                                <div class="note-heading">Note 2 — Reserve and Surplus</div>
                                <div class="note-body">

                                    @if (count($reserveMissingYears) > 0)
                                        <div class="data-warning">
                                            ⚠ No closing Profit/Loss movement found for FY
                                            {{ implode(', FY ', $reserveMissingYears) }} within this range. These
                                            fiscal years fall inside the report period but appear not to have been
                                            swept from Income/Expense into Retained Earnings yet — please confirm
                                            whether the year-end closing JV for
                                            {{ count($reserveMissingYears) > 1 ? 'these years' : 'this year' }}
                                            is still pending.
                                        </div>
                                    @endif

                                    <table class="note-table">
                                        <thead>
                                            <tr>
                                                <th class="label">Particulars</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="label">Balance as at {{ date('d F Y', strtotime($from_date)) }}
                                                </td>
                                                <td class="num">{{ fmt($reserveOpening) }}</td>
                                            </tr>
                                            @foreach ($reserveMovements as $m)
                                                <tr>
                                                    <td class="label">{{ $m['label'] }}</td>
                                                    <td class="num {{ $m['amount'] < 0 ? 'negative' : '' }}">
                                                        {{ fmt($m['amount']) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="total-row">
                                                <td class="label">Balance as at {{ date('d F Y', strtotime($to_date)) }}
                                                </td>
                                                <td class="num">{{ fmt($reserveClosing) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p style="font-size:11px; color:#555;">Refer to the Statement of Changes in Equity for
                                        full presentation.</p>
                                </div>
                            </div>

                            {{-- Note: Share Capital --}}
                            <div class="note-block">
                                <div class="note-heading">Note 3 — Share Capital</div>
                                <div class="note-body">
                                    <table class="note-table">
                                        <thead>
                                            <tr>
                                                <th class="label">Particulars</th>
                                                <th>{{ date('d M Y', strtotime($to_date)) }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="label">Paid-up Share Capital</td>
                                                <td class="num">{{ fmt($shareCapitalBalance) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Note: Revenue — year-by-year (FIX 2026-07-12: no more
                                 single overlapping "current vs prior" pair) --}}
                            <div class="note-block">
                                <div class="note-heading">Note 4 — Revenue</div>
                                <div class="note-body">
                                    <table class="note-table">
                                        <thead>
                                            <tr>
                                                <th class="label">Fiscal Year</th>
                                                <th>Total Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($revenueByYear as $year => $amount)
                                                <tr>
                                                    <td class="label">{{ $year }}</td>
                                                    <td class="num {{ $amount < 0 ? 'negative' : '' }}">
                                                        {{ fmt($amount) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="total-row">
                                                <td class="label">Total ({{ date('d M Y', strtotime($from_date)) }} –
                                                    {{ date('d M Y', strtotime($to_date)) }})</td>
                                                <td class="num">{{ fmt(array_sum($revenueByYear)) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Note: Accounts Receivable Ageing --}}
                            <div class="note-block">
                                <div class="note-heading">Note 5 — Accounts Receivable Ageing</div>
                                <div class="note-body">
                                    @if ($receivableAgeing)
                                        <table class="note-table">
                                            <thead>
                                                <tr>
                                                    <th class="label">Ageing</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($receivableAgeing as $bucket => $amount)
                                                    <tr>
                                                        <td class="label">{{ $bucket }} days</td>
                                                        <td class="num {{ $amount < 0 ? 'negative' : '' }}">
                                                            {{ fmt($amount) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="config-warning">⚠ Receivable account ID not configured — please confirm
                                            the Chart of Accounts ID in the Config section of the controller.</div>
                                    @endif
                                    <p class="no-print" style="font-size:11px; color:#856404;">
                                        ⚠ Note: This ageing is bucketed by each transaction's own posting date
                                        against today, not by open-item (invoice-vs-payment) matching. A partially
                                        paid old invoice keeps its full net amount in its original bucket while the
                                        payment lands separately — so a single bucket dominating the total (as seen
                                        here) is expected with this method and does not necessarily mean the
                                        receivable book is truly that old.
                                    </p>
                                </div>
                            </div>

                            {{-- Note: Accounts Payable --}}
                            <div class="note-block">
                                <div class="note-heading">Note 6 — Accounts Payable</div>
                                <div class="note-body">
                                    @if (!is_null($payableBalance))
                                        @if ($payableAbnormalSign)
                                            <div class="data-warning">
                                                ⚠ Accounts Payable is a liability and would normally carry a
                                                credit (positive) balance. The figure below is negative, meaning
                                                the tree nets to an overall debit position — please verify against
                                                the supplier ledger before finalising this note.
                                            </div>
                                        @endif
                                        <table class="note-table">
                                            <tbody>
                                                <tr>
                                                    <td class="label">Total Accounts Payable as at
                                                        {{ date('d M Y', strtotime($to_date)) }}</td>
                                                    <td class="num {{ $payableAbnormalSign ? 'negative' : '' }}">
                                                        {{ fmt($payableBalance) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="config-warning">⚠ Payable account ID not configured — please confirm
                                            the Chart of Accounts ID in the Config section of the controller.</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Note: Cash and Bank Balances --}}
                            <div class="note-block">
                                <div class="note-heading">Note 7 — Cash and Bank Balances</div>
                                <div class="note-body">
                                    @if ($cashBankBreakdown)
                                        @php $unexpectedCashItems = collect($cashBankBreakdown)->where('unexpected', true); @endphp
                                        @if ($unexpectedCashItems->isNotEmpty())
                                            <div class="data-warning">
                                                ⚠ The following account(s) sit directly under "Cash and Cash
                                                Equivalents" rather than under "Cash at Bank" — same misclassification
                                                pattern already known from Accounts Payable. Totals are still correct,
                                                but consider moving these under the correct parent for cleaner
                                                presentation:
                                                <strong>{{ $unexpectedCashItems->pluck('label')->implode(', ') }}</strong>
                                            </div>
                                        @endif
                                        <table class="note-table">
                                            <thead>
                                                <tr>
                                                    <th class="label">Particulars</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($cashBankBreakdown as $item)
                                                    <tr class="{{ $item['unexpected'] ? 'suspicious' : '' }}">
                                                        <td class="label">{{ $item['label'] }}</td>
                                                        <td class="num {{ $item['balance'] < 0 ? 'negative' : '' }}">
                                                            {{ fmt($item['balance']) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="config-warning">⚠ Cash/Bank account ID not configured — please confirm
                                            the Chart of Accounts ID in the Config section of the controller.</div>
                                    @endif
                                </div>
                            </div>

                            <p class="mt-3" style="font-style:italic;">These notes form an integral part of the
                                financial statements and should be read in conjunction with the Statement of Financial
                                Position, Statement of Comprehensive Income, Statement of Changes in Equity, and Statement
                                of Cash Flows.</p>

                            <div class="row mt-5">
                                <div class="col-md-5">
                                    <p>_______________________<br>Managing Director</p>
                                </div>
                                <div class="col-md-2"></div>
                                <div class="col-md-5 text-right">
                                    <p>_______________________<br>Chairman/Director</p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <p>Dated: {{ date('d F Y') }}<br>Place: Dhaka, Bangladesh</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
@endsection
