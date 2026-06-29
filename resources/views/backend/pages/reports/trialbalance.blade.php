@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .trial-balance-header th {
            background-color: #f4f4f4;
            text-align: center;
        }

        .group-header td {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .total-row th {
            background-color: #343a40;
            color: #fff;
        }

        .balanced {
            color: green;
            font-weight: bold;
        }

        .not-balanced {
            color: red;
            font-weight: bold;
        }

        @media print {
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

        {{-- Filter Form --}}
        <div class="col-md-12">
            <div class="card card-default no-print">
                <div class="card-body">
                    <form method="GET" action="{{ route('report.trialbalance.trialbalance') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ $startDate }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="{{ $endDate }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary mt-4">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Report Table --}}
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header no-print">
                    <h3 class="card-title">Trial Balance Report</h3>
                    <a onclick="window.print()" class="btn btn-default float-right my-2 no-print">
                        <i class="fas fa-print"></i> Print
                    </a>
                </div>

                <div class="card-body">
                    <div class="invoice p-3 mb-3">

                        {{-- Company Header --}}
                        <div class="row mb-3 text-center">
                            <div class="col-12">
                                @if (isset($companyInfo->logo))
                                    <img width="120px" src="{{ asset('/backend/logo/' . $companyInfo->logo) }}"
                                        alt="Logo"><br>
                                @endif
                                <h4><strong>{{ $companyInfo->company_name ?? '' }}</strong></h4>
                                <h5>Trial Balance</h5>
                                <p>From: <strong>{{ $startDate }}</strong> &nbsp; To:
                                    <strong>{{ $endDate }}</strong>
                                </p>
                            </div>
                        </div>

                        {{-- Trial Balance Table --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="trial-balance-header">
                                    <tr>
                                        <th rowspan="2" style="vertical-align: middle;">Accounts Head</th>
                                        <th colspan="2" class="text-center">Opening Balance</th>
                                        <th colspan="2" class="text-center">Transaction This Period</th>
                                        <th colspan="2" class="text-center">Closing Balance</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Dr.</th>
                                        <th class="text-center">Cr.</th>
                                        <th class="text-center">Dr.</th>
                                        <th class="text-center">Cr.</th>
                                        <th class="text-center">Dr.</th>
                                        <th class="text-center">Cr.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalOpeningDebit = 0;
                                        $totalOpeningCredit = 0;
                                        $totalTransactionDebit = 0;
                                        $totalTransactionCredit = 0;
                                        $totalClosingDebit = 0;
                                        $totalClosingCredit = 0;
                                    @endphp

                                    @foreach ($groupedTrialBalance as $accountType => $accounts)
                                        @if (count($accounts) > 0)
                                            {{-- Group Header --}}
                                            <tr class="group-header">
                                                <td colspan="7">{{ $parentNames[$accountType] }}</td>
                                            </tr>

                                            @foreach ($accounts as $entry)
                                                <tr>
                                                    <td>{{ $entry['account_name'] }}</td>


                                                    @if ($entry['opening_debit'] > 0 || $entry['opening_credit'] > 0)
                                                        <td class="text-right">
                                                            {{ number_format($entry['opening_debit'], 2) }}</td>
                                                        <td class="text-right">
                                                            {{ number_format($entry['opening_credit'], 2) }}</td>
                                                    @else
                                                        <td class="text-center text-muted" colspan="2">—</td>
                                                    @endif

                                                    @if ($entry['transaction_debit'] > 0 || $entry['transaction_credit'] > 0)
                                                        <td class="text-right">
                                                            {{ number_format($entry['transaction_debit'], 2) }}</td>
                                                        <td class="text-right">
                                                            {{ number_format($entry['transaction_credit'], 2) }}</td>
                                                    @else
                                                        <td class="text-center text-muted" colspan="2">—</td>
                                                    @endif

                                                    <td class="text-right">
                                                        {{ $entry['closing_debit'] > 0 ? number_format($entry['closing_debit'], 2) : '—' }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $entry['closing_credit'] > 0 ? number_format($entry['closing_credit'], 2) : '—' }}
                                                    </td>
                                                </tr>

                                                @php
                                                    $totalOpeningDebit += $entry['opening_debit'];
                                                    $totalOpeningCredit += $entry['opening_credit'];
                                                    $totalTransactionDebit += $entry['transaction_debit'];
                                                    $totalTransactionCredit += $entry['transaction_credit'];
                                                    $totalClosingDebit += $entry['closing_debit'];
                                                    $totalClosingCredit += $entry['closing_credit'];
                                                @endphp
                                            @endforeach
                                        @endif
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr class="total-row">
                                        <th>Total</th>
                                        <th class="text-right">{{ number_format($totalOpeningDebit, 2) }}</th>
                                        <th class="text-right">{{ number_format($totalOpeningCredit, 2) }}</th>
                                        <th class="text-right">{{ number_format($totalTransactionDebit, 2) }}</th>
                                        <th class="text-right">{{ number_format($totalTransactionCredit, 2) }}</th>
                                        <th class="text-right">{{ number_format($totalClosingDebit, 2) }}</th>
                                        <th class="text-right">{{ number_format($totalClosingCredit, 2) }}</th>
                                    </tr>


                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Balance Status:</strong></td>
                                        <td colspan="2" class="text-center">
                                            @if (round($totalClosingDebit, 2) == round($totalClosingCredit, 2))
                                                <span class="balanced">Balanced</span>
                                            @else
                                                <span class="not-balanced">
                                                    Difference:
                                                    {{ number_format(abs($totalClosingDebit - $totalClosingCredit), 2) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
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

    </div>
@endsection

@section('scripts')
    @include('backend.pages.reports.excel')
@endsection
