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

        .note-scope {
            font-size: 11px;
            font-weight: 400;
            font-style: italic;
            color: #555;
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

        tr.resolved-row {
            background: #eef6ff;
        }

        tr.resolved-row td.label::after {
            content: " ℹ";
            color: #31708f;
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

        .info-note {
            background: #eef6ff;
            color: #31708f;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 11.5px;
            margin-bottom: 10px;
            border: 1px solid #bce8f1;
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
                                    <label>Reporting Year-End Date:</label>
                                    <input type="date" class="form-control" name="year_end_date"
                                        value="{{ $year_end_date ?? '' }}">
                                    @error('year_end_date')
                                        <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">Current year and comparative prior year are derived
                                        automatically from this date.</small>
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

        @if ($request->method() == 'POST' && $year_end_date)
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
                                <div class="sub">FOR THE YEAR ENDED {{ strtoupper($currentYearEnd->format('jS F Y')) }}
                                </div>
                                <div class="sub">(Amount in BDT)</div>
                            </div>

                            {{-- Note: Property, Plant & Equipment --}}
                            <div class="note-block">
                                <div class="note-heading">Note 1 — Property, Plant &amp; Equipment
                                    <div class="note-scope">For the year ended {{ $currentYearEnd->format('d M Y') }}
                                        (comparative: {{ $priorYearEnd->format('d M Y') }})</div>
                                </div>
                                <div class="note-body">

                                    @if (count($fixedAssetSchedule['flaggedRows']) > 0)
                                        <div class="data-warning">
                                            ⚠ Data integrity: {{ count($fixedAssetSchedule['flaggedRows']) }}
                                            asset(s) below still carry a <strong>negative closing
                                                balance</strong> this year. Fixed asset (debit-normal) accounts should
                                            never sit negative — this means cumulative depreciation/write-offs on
                                            these assets exceed any cost ever recorded for them in this system.
                                            Please reconcile these against the physical Fixed Asset register
                                            before this note is finalised:
                                            <strong>{{ implode(', ', $fixedAssetSchedule['flaggedRows']) }}</strong>
                                        </div>
                                    @endif

                                    @if (count($fixedAssetSchedule['resolvedRows']) > 0)
                                        <div class="info-note no-print">
                                            ℹ️ Note: {{ count($fixedAssetSchedule['resolvedRows']) }}
                                            asset(s) below carried a negative <strong>opening</strong> balance
                                            earlier in the year — typically from an asset-under-construction
                                            cost transfer that didn't fully net out at the time — but have
                                            since been reconciled with an offsetting entry and now sit at a
                                            <strong>closing balance of zero</strong>. No further action needed
                                            unless the underlying capitalisation entries require review:
                                            <strong>{{ implode(', ', $fixedAssetSchedule['resolvedRows']) }}</strong>
                                        </div>
                                    @endif

                                    @if ($fixedAssetSchedule['totalNegative'])
                                        <div class="config-warning">
                                            ⚠ Total closing Property, Plant &amp; Equipment is
                                            <strong>negative
                                                ({{ fmt($fixedAssetSchedule['totals']['closing']) }})</strong>.
                                            This cannot be presented as-is in a statutory report — it indicates the
                                            data-integrity issue above needs to be resolved first.
                                            @if ($fixedAssetPriorYearClosing < 0)
                                                The comparative {{ $priorYearEnd->year }} closing balance is
                                                <strong>also negative ({{ fmt($fixedAssetPriorYearClosing) }})</strong>,
                                                so this is not a new issue introduced this year — it has been
                                                carried forward from at least the prior year.
                                            @endif
                                        </div>
                                    @endif

                                    <table class="note-table">
                                        <thead>
                                            <tr>
                                                <th class="label">Category</th>
                                                <th>Opening</th>
                                                <th>Addition</th>
                                                <th>Disposal / Depreciation</th>
                                                <th>Closing {{ $currentYearEnd->year }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($fixedAssetSchedule['rows'] as $row)
                                                <tr class="{{ $row['suspicious'] ? 'suspicious' : ($row['resolved'] ? 'resolved-row' : '') }}">
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
                                            <tr>
                                                <td class="label">Comparative — Closing {{ $priorYearEnd->year }}</td>
                                                <td class="num" colspan="3">—</td>
                                                <td class="num {{ $fixedAssetPriorYearClosing < 0 ? 'negative' : '' }}">
                                                    {{ fmt($fixedAssetPriorYearClosing) }}
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
                                <div class="note-heading">Note 2 — Reserve and Surplus
                                    <div class="note-scope">For the year ended {{ $currentYearEnd->format('d M Y') }}
                                        (comparative: year ended {{ $priorYearEnd->format('d M Y') }})</div>
                                </div>
                                <div class="note-body">

                                    @if ($reserveMissingClosingForPriorFy)
                                        <div class="data-warning">
                                            ⚠ No closing Profit/Loss entry found representing FY
                                            {{ $representedFyForCurrent }} (expected to post on
                                            {{ $currentYearStart->format('d M Y') }}). Please confirm whether that
                                            year-end closing JV is still pending.
                                        </div>
                                    @endif

                                    @if ($reserveClosingLagNote)
                                        <p class="no-print" style="font-size:11px; color:#856404;">
                                            ⚠ Note: Because closing JVs post on 1 Jan of the following year, the
                                            "Movement" figure shown under each column header does NOT represent
                                            that column's own calendar year — it represents the closing JV posted
                                            on 1 Jan of that year, which is the PRIOR fiscal year's result (see the
                                            "represents FY ..." label under each figure below). FY
                                            {{ $currentYearEnd->year }}'s
                                            own closing JV will post on
                                            {{ $currentYearEnd->copy()->addYear()->startOfYear()->format('d M Y') }}
                                            and will only appear in next year's note — this is expected, not an error.
                                        </p>
                                    @endif

                                    <table class="note-table">
                                        <thead>
                                            <tr>
                                                <th class="label">Particulars</th>
                                                <th>{{ $currentYearEnd->year }}</th>
                                                <th>{{ $priorYearEnd->year }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="label">Balance at the beginning of the year</td>
                                                <td class="num">{{ fmt($reserveCurrent['opening']) }}</td>
                                                <td class="num">{{ fmt($reservePrior['opening']) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label">Movement during the year <span
                                                        style="font-weight:400;font-style:italic;font-size:10.5px;">(closing
                                                        JV posted 1 Jan)</span></td>
                                                <td class="num {{ $reserveCurrent['movement'] < 0 ? 'negative' : '' }}">
                                                    {{ fmt($reserveCurrent['movement']) }}
                                                    <div
                                                        style="font-size:10px;font-style:italic;font-weight:400;color:#555;">
                                                        represents FY {{ $reserveCurrent['representedFy'] }}</div>
                                                </td>
                                                <td class="num {{ $reservePrior['movement'] < 0 ? 'negative' : '' }}">
                                                    {{ fmt($reservePrior['movement']) }}
                                                    <div
                                                        style="font-size:10px;font-style:italic;font-weight:400;color:#555;">
                                                        represents FY {{ $reservePrior['representedFy'] }}</div>
                                                </td>
                                            </tr>
                                            <tr class="total-row">
                                                <td class="label">Balance at the end of the year</td>
                                                <td class="num">{{ fmt($reserveCurrent['closing']) }}</td>
                                                <td class="num">{{ fmt($reservePrior['closing']) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p style="font-size:11px; color:#555;">Refer to the Statement of Changes in Equity for
                                        full presentation.</p>
                                </div>
                            </div>

                            {{-- Note: Share Capital --}}
                            <div class="note-block">
                                <div class="note-heading">Note 3 — Share Capital
                                    <div class="note-scope">As at {{ $currentYearEnd->format('d M Y') }}
                                        (comparative: {{ $priorYearEnd->format('d M Y') }})</div>
                                </div>
                                <div class="note-body">
                                    @if ($shareCapitalMissing)
                                        <div class="config-warning">
                                            ⚠ Paid-up Share Capital shows zero in both years. This is unusual
                                            for an operating company — please confirm the Share Capital account
                                            ID ({{ $shareCapitalId }}) is correct, or check whether capital was
                                            posted under a different account.
                                        </div>
                                    @endif
                                    <table class="note-table">
                                        <thead>
                                            <tr>
                                                <th class="label">Particulars</th>
                                                <th>{{ $currentYearEnd->year }}</th>
                                                <th>{{ $priorYearEnd->year }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="label">Paid-up Share Capital</td>
                                                <td class="num">{{ fmt($shareCapitalCurrent) }}</td>
                                                <td class="num">{{ fmt($shareCapitalPrior) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Note: Revenue --}}
                            <div class="note-block">
                                <div class="note-heading">Note 4 — Revenue
                                    <div class="note-scope">For the year ended {{ $currentYearEnd->format('d M Y') }}
                                        (comparative: year ended {{ $priorYearEnd->format('d M Y') }})</div>
                                </div>
                                <div class="note-body">
                                    <table class="note-table">
                                        <thead>
                                            <tr>
                                                <th class="label">Particulars</th>
                                                <th>{{ $currentYearEnd->year }}</th>
                                                <th>{{ $priorYearEnd->year }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="total-row">
                                                <td class="label">Total Revenue for the year</td>
                                                <td class="num {{ $revenueCurrent < 0 ? 'negative' : '' }}">
                                                    {{ fmt($revenueCurrent) }}
                                                </td>
                                                <td class="num {{ $revenuePrior < 0 ? 'negative' : '' }}">
                                                    {{ fmt($revenuePrior) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Note: Accounts Receivable Ageing --}}
                            <div class="note-block">
                                <div class="note-heading">Note 5 — Accounts Receivable Ageing
                                    <div class="note-scope">As at {{ $currentYearEnd->format('d M Y') }}
                                        (point-in-time snapshot — no comparative column, per standard practice)</div>
                                </div>
                                <div class="note-body">
                                    @if ($receivableAgeing)
                                        @if ($receivableAbnormalSign)
                                            <div class="data-warning">
                                                ⚠ Accounts Receivable is an asset and would normally carry a
                                                debit (positive) balance. The total below nets to a
                                                negative figure ({{ fmt($receivableTotal) }}), meaning the
                                                tree nets to an overall credit position — please verify
                                                against the customer ledger (e.g. overpayments, misapplied
                                                credit memos, or advance receipts) before finalising this note.
                                            </div>
                                        @endif
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
                                        against the reporting date, not by open-item (invoice-vs-payment) matching.
                                        A single bucket dominating the total does not necessarily mean the
                                        receivable book is truly that old — see prior discussion.
                                    </p>
                                </div>
                            </div>

                            {{-- Note: Accounts Payable --}}
                            <div class="note-block">
                                <div class="note-heading">Note 6 — Accounts Payable
                                    <div class="note-scope">As at {{ $currentYearEnd->format('d M Y') }}
                                        (comparative: {{ $priorYearEnd->format('d M Y') }})</div>
                                </div>
                                <div class="note-body">
                                    @if (!is_null($payableBalanceCurrent))
                                        @if ($payableAbnormalSignCurrent || $payableAbnormalSignPrior)
                                            <div class="data-warning">
                                                ⚠ Accounts Payable is a liability and would normally carry a
                                                credit (positive) balance. The figure below is negative in
                                                {{ $payableAbnormalSignCurrent && $payableAbnormalSignPrior ? 'both ' . $currentYearEnd->year . ' and ' . $priorYearEnd->year : ($payableAbnormalSignCurrent ? $currentYearEnd->year : $priorYearEnd->year) }},
                                                meaning the tree nets to an overall debit position — please
                                                verify against the supplier ledger before finalising this note.
                                            </div>
                                        @endif
                                        <table class="note-table">
                                            <thead>
                                                <tr>
                                                    <th class="label">Particulars</th>
                                                    <th>{{ $currentYearEnd->year }}</th>
                                                    <th>{{ $priorYearEnd->year }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="label">Total Accounts Payable</td>
                                                    <td class="num {{ $payableAbnormalSignCurrent ? 'negative' : '' }}">
                                                        {{ fmt($payableBalanceCurrent) }}
                                                    </td>
                                                    <td class="num {{ $payableAbnormalSignPrior ? 'negative' : '' }}">
                                                        {{ fmt($payableBalancePrior) }}
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
                                <div class="note-heading">Note 7 — Cash and Bank Balances
                                    <div class="note-scope">As at {{ $currentYearEnd->format('d M Y') }}
                                        (comparative: {{ $priorYearEnd->format('d M Y') }})</div>
                                </div>
                                <div class="note-body">
                                    @if ($cashBankBreakdown)
                                        @php $unexpectedCashItems = collect($cashBankBreakdown)->where('unexpected', true); @endphp

                                        @if ($cashInHandNegative)
                                            <div class="config-warning">
                                                ⚠ Cash in Hand shows a negative balance in at least one of the
                                                two years below. Physical cash on hand cannot go negative — this
                                                points to a data-entry or posting error (e.g. a payment recorded
                                                against Cash in Hand instead of Cash at Bank) and should be
                                                investigated before this note is finalised.
                                            </div>
                                        @endif

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
                                                    <th>{{ $currentYearEnd->year }}</th>
                                                    <th>{{ $priorYearEnd->year }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($cashBankBreakdown as $item)
                                                    <tr class="{{ $item['unexpected'] ? 'suspicious' : '' }}">
                                                        <td class="label">{{ $item['label'] }}</td>
                                                        <td class="num {{ $item['balance'] < 0 ? 'negative' : '' }}">
                                                            {{ fmt($item['balance']) }}
                                                        </td>
                                                        <td
                                                            class="num {{ $item['balance_prior'] < 0 ? 'negative' : '' }}">
                                                            {{ fmt($item['balance_prior']) }}
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