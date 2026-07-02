@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .bs-section-header th {
            background-color: #e9ecef;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .bs-subtotal th,
        .bs-subtotal td {
            background-color: #f8f9fa;
            font-weight: 600;
            border-top: 1.5px solid #dee2e6;
        }

        .bs-total th {
            background-color: #343a40;
            color: #fff;
            font-size: 14px;
        }

        .bs-profit td {
            background-color: #d4edda;
            font-weight: 600;
        }

        .bs-loss td {
            background-color: #f8d7da;
            font-weight: 600;
        }

        .balanced {
            color: green;
            font-weight: bold;
        }

        .not-balanced {
            color: red;
            font-weight: bold;
        }

        .amount-col {
            text-align: right;
            font-variant-numeric: tabular-nums;
            min-width: 130px;
        }

        .negative {
            color: #dc3545;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            tfoot {
                display: table-row-group;
            }
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Report</h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="row">

        {{-- Filter — Modified: 2026-07-02 GET, শুধু as_of_date --}}
        <div class="col-md-12 no-print">
            <div class="card card-outline card-info">
                <div class="card-body">
                    <form action="{{ route('report.balancesheet.balancesheet') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>As of Date:</label>
                                    <input type="date" class="form-control" name="as_of_date"
                                        value="{{ $asOfDate }}">
                                </div>
                            </div>
                            <div class="col-md-3">
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

        {{-- Report --}}
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header no-print">
                    <h3 class="card-title">Balance Sheet Report</h3>
                    <a onclick="window.print()" class="btn btn-default float-right my-2">
                        <i class="fas fa-print"></i> Print
                    </a>
                </div>
                <div class="card-body">
                    <div class="invoice p-3 mb-3">

                        {{-- Company Header --}}
                        <div class="row mb-3">
                            <div class="col-12 text-center">
                                @if (isset($companyInfo->logo))
                                    <img width="100px" src="{{ asset('/backend/logo/' . $companyInfo->logo) }}"
                                        alt="Logo"><br>
                                @endif
                                <h4><strong>{{ $companyInfo->company_name ?? '' }}</strong></h4>
                                <h5>Balance Sheet</h5>
                                <p>As of: <strong>{{ $asOfDate }}</strong></p>
                            </div>
                        </div>

                        {{-- Balance Check Row — Added: 2026-07-02 --}}
                        <div class="row mb-3 no-print">
                            <div class="col-12 text-center">
                                @if ($balanceCheck['is_balanced'])
                                    <span class="balanced">
                                        Balanced — Total Assets = Total Liabilities + Equity
                                        ({{ number_format($balanceSheet['total_assets'], 2) }})
                                    </span>
                                @else
                                    <span class="not-balanced">
                                        Not Balanced — Difference:
                                        {{ number_format(abs($balanceCheck['difference']), 2) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Two-column layout — Added: 2026-07-02 --}}
                        <div class="row">
                            {{-- LEFT: ASSETS --}}
                            <div class="col-md-6">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr style="background:#343a40; color:#fff;">
                                            <th>Assets</th>
                                            <th class="amount-col">Amount (৳)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Current Assets --}}
                                        <tr class="bs-section-header">
                                            <th colspan="2">Current Assets</th>
                                        </tr>
                                        @foreach ($balanceSheet['current_assets'] as $row)
                                            <tr>
                                                <td class="pl-4">{{ $row['name'] }}</td>
                                                <td class="amount-col {{ $row['balance'] < 0 ? 'negative' : '' }}">
                                                    {{ number_format($row['balance'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="bs-subtotal">
                                            <td>Total Current Assets</td>
                                            <td class="amount-col">
                                                {{ number_format($balanceSheet['total_current_assets'], 2) }}</td>
                                        </tr>

                                        {{-- Fixed Assets --}}
                                        <tr class="bs-section-header">
                                            <th colspan="2">Fixed Assets</th>
                                        </tr>
                                        @foreach ($balanceSheet['fixed_assets'] as $row)
                                            <tr>
                                                <td class="pl-4">{{ $row['name'] }}</td>
                                                <td class="amount-col {{ $row['balance'] < 0 ? 'negative' : '' }}">
                                                    {{ number_format($row['balance'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="bs-subtotal">
                                            <td>Total Fixed Assets</td>
                                            <td class="amount-col">
                                                {{ number_format($balanceSheet['total_fixed_assets'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bs-total">
                                            <th>Total Assets (A)</th>
                                            <th class="amount-col">{{ number_format($balanceSheet['total_assets'], 2) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            {{-- RIGHT: LIABILITIES + EQUITY --}}
                            <div class="col-md-6">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr style="background:#343a40; color:#fff;">
                                            <th>Liabilities &amp; Equity</th>
                                            <th class="amount-col">Amount (৳)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Current Liabilities --}}
                                        <tr class="bs-section-header">
                                            <th colspan="2">Current Liabilities</th>
                                        </tr>
                                        @foreach ($balanceSheet['current_liabilities'] as $row)
                                            <tr>
                                                <td class="pl-4">{{ $row['name'] }}</td>
                                                <td class="amount-col {{ $row['balance'] < 0 ? 'negative' : '' }}">
                                                    {{ number_format($row['balance'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="bs-subtotal">
                                            <td>Total Current Liabilities</td>
                                            <td class="amount-col">
                                                {{ number_format($balanceSheet['total_current_liabilities'], 2) }}</td>
                                        </tr>

                                        {{-- Long Term Liabilities --}}
                                        <tr class="bs-section-header">
                                            <th colspan="2">Long Term Liabilities</th>
                                        </tr>
                                        @foreach ($balanceSheet['long_term_liabilities'] as $row)
                                            <tr>
                                                <td class="pl-4">{{ $row['name'] }}</td>
                                                <td class="amount-col {{ $row['balance'] < 0 ? 'negative' : '' }}">
                                                    {{ number_format($row['balance'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="bs-subtotal">
                                            <td>Total Long Term Liabilities</td>
                                            <td class="amount-col">
                                                {{ number_format($balanceSheet['total_long_term_liabilities'], 2) }}</td>
                                        </tr>

                                        <tr class="bs-subtotal" style="border-top:2px solid #343a40;">
                                            <td><strong>Total Liabilities (B)</strong></td>
                                            <td class="amount-col">
                                                <strong>{{ number_format($balanceSheet['total_liabilities'], 2) }}</strong>
                                            </td>
                                        </tr>

                                        {{-- Equity --}}
                                        <tr class="bs-section-header">
                                            <th colspan="2">Equity</th>
                                        </tr>
                                        @foreach ($balanceSheet['equity'] as $row)
                                            @php
                                                $isProfit = str_contains($row['name'], 'Profit');
                                                $isLoss = str_contains($row['name'], 'Loss');
                                            @endphp
                                            <tr class="{{ $isProfit ? 'bs-profit' : ($isLoss ? 'bs-loss' : '') }}">
                                                <td class="pl-4">{{ $row['name'] }}</td>
                                                <td class="amount-col {{ $row['balance'] < 0 ? 'negative' : '' }}">
                                                    {{ number_format($row['balance'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="bs-subtotal">
                                            <td>Total Equity (C)</td>
                                            <td class="amount-col">{{ number_format($balanceSheet['total_equity'], 2) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bs-total">
                                            <th>Total Liabilities + Equity (B+C)</th>
                                            <th class="amount-col">
                                                {{ number_format($balanceSheet['total_liabilities_and_equity'], 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        {{-- Balance Status Print Row — Added: 2026-07-02 --}}
                        <div class="row mt-2">
                            <div class="col-12 text-center">
                                @if ($balanceCheck['is_balanced'])
                                    <strong class="balanced">Assets
                                        ({{ number_format($balanceSheet['total_assets'], 2) }}) = Liabilities + Equity
                                        ({{ number_format($balanceSheet['total_liabilities_and_equity'], 2) }})</strong>
                                @else
                                    <strong class="not-balanced">Not Balanced — Difference:
                                        {{ number_format(abs($balanceCheck['difference']), 2) }}</strong>
                                @endif
                            </div>
                        </div>

                        {{-- Signature --}}
                        <div class="row mt-5">
                            <div class="col-md-4">
                                <p>Prepared By: _____________<br>Date: ____________________</p>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4 text-right">
                                <p>Approved By: ________________<br>Date: _________________</p>
                            </div>
                        </div>

                        <hr>
                        <div class="col-md-12 bg-success text-center py-2">
                            Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} products.
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Unresolved Accounts Warning — Added: 2026-07-02 --}}
        @if (!empty($balanceCheck['unresolved_accounts']))
            <div class="col-md-12 no-print">
                <div class="alert alert-warning">
                    <strong>some account don't categorize </strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($balanceCheck['unresolved_accounts'] as $ua)
                            <li>{{ $ua }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

    </div>
@endsection

@section('scripts')
    @include('backend.pages.reports.excel')
@endsection
