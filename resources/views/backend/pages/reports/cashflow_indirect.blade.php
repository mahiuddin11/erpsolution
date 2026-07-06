@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .cfi-wrapper {
            background: #fff;
            padding: 20px;
        }

        .cfi-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .cfi-header h4 {
            margin: 0;
            font-weight: 700;
        }

        .cfi-header .sub {
            font-size: 14px;
            margin-top: 2px;
        }

        table.cfi-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        table.cfi-table th,
        table.cfi-table td {
            border: 1px solid #333;
            padding: 6px 10px;
            vertical-align: top;
        }

        table.cfi-table th {
            text-align: center;
            font-weight: 700;
            background: #f2f2f2;
        }

        table.cfi-table td.particulars {
            width: 55%;
        }

        table.cfi-table td.amount-col {
            text-align: right;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
            width: 22.5%;
        }

        .cfi-section-title {
            font-weight: 700;
            text-decoration: underline;
        }

        .cfi-subtotal td {
            font-weight: 700;
            border-top: 2px solid #333;
        }

        .cfi-final td {
            font-weight: 700;
            background: #f8f9fa;
        }

        .cfi-neg {
            color: #c0392b;
        }

        .cfi-recon-warning {
            background: #fdecea;
            color: #c0392b;
            padding: 10px 14px;
            border-radius: 6px;
            margin-top: 12px;
            font-weight: 600;
        }

        .cfi-recon-ok {
            background: #eafaf1;
            color: #1e8449;
            padding: 10px 14px;
            border-radius: 6px;
            margin-top: 12px;
            font-weight: 600;
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
                    <h1 class="m-0">Statement of Cash Flow (Indirect Method)</h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12 no-print">
            <div class="card card-outline card-info">
                <div class="card-body">
                    <form action="{{ route('report.indirectcashflow') }}" method="POST">
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
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fa fa-search"></i> Generate
                                    </button>
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
                        <h3 class="card-title">Statement of Cash Flow</h3>
                        <a onclick="window.print()" class="btn btn-default float-right my-2">
                            <i class="fas fa-print"></i> Print
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="cfi-wrapper">

                            {{-- Header --}}
                            <div class="cfi-header">
                                <h4>{{ $companyInfo->company_name ?? 'N/A' }}</h4>
                                <div class="sub">STATEMENT OF CASH FLOW</div>
                                <div class="sub">
                                    FOR THE YEAR ENDED {{ strtoupper(date('jS F Y', strtotime($to_date))) }}
                                </div>
                            </div>

                            <table class="cfi-table">
                                <thead>
                                    <tr>
                                        <th class="particulars">PARTICULARS</th>
                                        <th class="amount-col">
                                            {{ date('d-M-y', strtotime($from_date)) }} <br>
                                            {{ date('d-M-y', strtotime($to_date)) }} <br>
                                            BDT
                                        </th>
                                        <th class="amount-col">
                                            {{ date('d-M-y', strtotime($prevFromDate ?? $from_date)) }} <br>
                                            {{ date('d-M-y', strtotime($prevToDate ?? $to_date)) }} <br>
                                            BDT
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    {{-- ============== A. OPERATING ============== --}}
                                    <tr>
                                        <td colspan="3" class="cfi-section-title">A. CASH FLOW FROM OPERATING
                                            ACTIVITIES :</td>
                                    </tr>
                                    <tr>
                                        <td class="particulars">Net profit (Loss)</td>
                                        <td class="amount-col {{ $netProfit < 0 ? 'cfi-neg' : '' }}">
                                            {{ number_format($netProfit, 0) }}</td>
                                        <td class="amount-col {{ ($prevNetProfit ?? 0) < 0 ? 'cfi-neg' : '' }}">
                                            {{ number_format($prevNetProfit ?? 0, 0) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="particulars">Depreciation</td>
                                        <td class="amount-col">{{ number_format($depreciation, 0) }}</td>
                                        <td class="amount-col">{{ number_format($prevDepreciation ?? 0, 0) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="particulars cfi-section-title " colspan="3">Change in Working Capital
                                            :</td>

                                    </tr>

                                    @foreach ($wcLinesData as $row)
                                        <tr>
                                            <td class="particulars">{{ $row['label'] }}</td>
                                            <td class="amount-col {{ $row['data']['current'] < 0 ? 'cfi-neg' : '' }}">
                                                {{ $row['data']['current'] == 0 ? '-' : ($row['data']['current'] < 0 ? '(' . number_format(abs($row['data']['current']), 0) . ')' : number_format($row['data']['current'], 0)) }}
                                            </td>
                                            <td class="amount-col {{ $row['data']['previous'] < 0 ? 'cfi-neg' : '' }}">
                                                {{ $row['data']['previous'] == 0 ? '-' : ($row['data']['previous'] < 0 ? '(' . number_format(abs($row['data']['previous']), 0) . ')' : number_format($row['data']['previous'], 0)) }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr class="cfi-subtotal">
                                        <td class="particulars">Net Increase/(Decrease) in Operating Activities</td>
                                        <td class="amount-col {{ $operatingTotal < 0 ? 'cfi-neg' : '' }}">
                                            {{ number_format($operatingTotal, 0) }}</td>
                                        <td class="amount-col {{ ($prevOperatingTotal ?? 0) < 0 ? 'cfi-neg' : '' }}">
                                            {{ number_format($prevOperatingTotal ?? 0, 0) }}</td>
                                    </tr>

                                    {{-- ============== B. INVESTING ============== --}}
                                    <tr>
                                        <td colspan="3" class="cfi-section-title" style="padding-top:14px;">
                                            B. CASH FLOW FROM INVESTMENT ACTIVITIES :</td>
                                    </tr>
                                    <tr>
                                        <td class="particulars">Fixed Assets Addition</td>
                                        <td class="amount-col {{ $fixedAssetsAddition < 0 ? 'cfi-neg' : '' }}">
                                            {{ $fixedAssetsAddition == 0 ? '-' : ($fixedAssetsAddition < 0 ? '(' . number_format(abs($fixedAssetsAddition), 0) . ')' : number_format($fixedAssetsAddition, 0)) }}
                                        </td>
                                        <td class="amount-col {{ ($prevFixedAssetsAddition ?? 0) < 0 ? 'cfi-neg' : '' }}">
                                            {{ ($prevFixedAssetsAddition ?? 0) == 0 ? '-' : (($prevFixedAssetsAddition ?? 0) < 0 ? '(' . number_format(abs($prevFixedAssetsAddition), 0) . ')' : number_format($prevFixedAssetsAddition ?? 0, 0)) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="particulars">Increase/(Decrease) in Last year Accounts</td>
                                        <td class="amount-col">
                                            {{ $lastYearAccountsChange == 0 ? '-' : number_format($lastYearAccountsChange, 0) }}
                                        </td>
                                        <td class="amount-col">
                                            {{ ($prevLastYearAccountsChange ?? 0) == 0 ? '-' : number_format($prevLastYearAccountsChange ?? 0, 0) }}
                                        </td>
                                    </tr>
                                    <tr class="cfi-subtotal">
                                        <td class="particulars">Net Increase/(Decrease) in investing Activities</td>
                                        <td class="amount-col {{ $investingTotal < 0 ? 'cfi-neg' : '' }}">
                                            {{ $investingTotal == 0 ? '-' : ($investingTotal < 0 ? '(' . number_format(abs($investingTotal), 0) . ')' : number_format($investingTotal, 0)) }}
                                        </td>
                                        <td class="amount-col {{ ($prevInvestingTotal ?? 0) < 0 ? 'cfi-neg' : '' }}">
                                            {{ ($prevInvestingTotal ?? 0) == 0 ? '-' : (($prevInvestingTotal ?? 0) < 0 ? '(' . number_format(abs($prevInvestingTotal), 0) . ')' : number_format($prevInvestingTotal ?? 0, 0)) }}
                                        </td>
                                    </tr>

                                    {{-- ============== C. FINANCING ============== --}}
                                    <tr>
                                        <td colspan="3" class="cfi-section-title" style="padding-top:14px;">
                                            C. CASH FLOW FROM FINANCING ACTIVITIES :</td>
                                    </tr>
                                    @foreach ($financingLinesData as $row)
                                        <tr>
                                            <td class="particulars">{{ $row['label'] }}</td>
                                            <td class="amount-col">
                                                {{ $row['data']['current'] == 0 ? '-' : number_format($row['data']['current'], 0) }}
                                            </td>
                                            <td class="amount-col">
                                                {{ $row['data']['previous'] == 0 ? '-' : number_format($row['data']['previous'], 0) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="cfi-subtotal">
                                        <td class="particulars">Net Increase/(Decrease) in fainancing Activities</td>
                                        <td class="amount-col">
                                            {{ $financingTotal == 0 ? '-' : number_format($financingTotal, 0) }}</td>
                                        <td class="amount-col">
                                            {{ ($prevFinancingTotal ?? 0) == 0 ? '-' : number_format($prevFinancingTotal ?? 0, 0) }}
                                        </td>
                                    </tr>

                                    {{-- ============== D, E, F SUMMARY ============== --}}
                                    <tr class="cfi-final">
                                        <td class="particulars">D. NET CASH &amp; BANK BALANCE
                                            INCREASE/(DECREASE)(A+B+C)</td>
                                        <td class="amount-col {{ $netChange < 0 ? 'cfi-neg' : '' }}">
                                            {{ number_format($netChange, 0) }}</td>
                                        <td class="amount-col {{ ($prevNetChange ?? 0) < 0 ? 'cfi-neg' : '' }}">
                                            {{ number_format($prevNetChange ?? 0, 0) }}</td>
                                    </tr>
                                    <tr class="cfi-final">
                                        <td class="particulars">E. OPENING CASH &amp; BANK BALANCE :</td>
                                        <td class="amount-col">{{ number_format($openingCash, 0) }}</td>
                                        <td class="amount-col">{{ number_format($prevOpeningCash ?? 0, 0) }}</td>
                                    </tr>
                                    <tr class="cfi-final">
                                        <td class="particulars">F. CLOSING CASH &amp; BANK BALANCE :</td>
                                        <td class="amount-col">{{ number_format($closingCash, 0) }}</td>
                                        <td class="amount-col">{{ number_format($prevClosingCash ?? 0, 0) }}</td>
                                    </tr>

                                </tbody>
                            </table>

                            {{-- Reconciliation check — Added: 2026-07-05 --}}
                            <div class="no-print">
                                @if (abs($reconDifference) > 1)
                                    <div class="cfi-recon-warning">
                                        ⚠ Reconciliation Difference: {{ number_format($reconDifference, 2) }} —
                                        The computed closing cash (Opening + Net Change) does not match the
                                        actual closing cash in the ledger. Please verify the account IDs
                                        in the Config section.
                                    </div>
                                @else
                                    <div class="cfi-recon-ok">
                                        ✓ Reconciled — The computed closing cash matches the actual
                                        balance in the ledger.
                                    </div>
                                @endif
                            </div>

                            <p class="mt-3" style="font-style: italic;">The annexed notes are an integral part of
                                these financial statements.</p>

                            {{-- Signature block --}}
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
