@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }
    </style>
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Report </h1>
                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header no-print">
                    <h3 class="card-title">Bank Balance Report</h3>
                    <a onclick="window.print()" target="_blank" class="btn btn-default float-right my-2 no-print"><i class="fas fa-print"></i>Print</a>
                    <div id="tableActions" class=" float-right my-2 no-print"></div>
                </div>
                <div class="card-body">
                    <div class="invoice p-3 mb-3">
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table  table-bordered">
                                    <tr>
                                        <td style="text-align: center">
                                            @if (isset($companyInfo->logo))
                                                <a href="{{ route('home') }}">
                                                    <img width="200px"
                                                        src="{{ asset('/backend/logo/' . $companyInfo->logo) }}"
                                                        style="" alt="">
                                                </a>
                                            @endif
                                        </td>
                                        <td width="70%" style="text-align: center">
                                            <h3>Bank Balance</h3>
                                            {{-- <h4><b>From Date: {{ $request->from_date }}</b>, <b>To date:
                                                    {{ $request->to_date }} </b></h4> --}}
                                        </td>
                                    </tr>
                                </table>
                                <table class="table table-bordered mt-2">
                                    <thead>
                                        <tr>
                                            <th>Accounts Head</th>
                                            <th colspan="2">Closing Balance</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            {{-- <th>Dr.</th>
                                            <th>Cr.</th> --}}
                                            {{-- <th>Dr.</th>
                                            <th>Cr.</th> --}}
                                            <th>Dr.</th>
                                            <th>Cr.</th>
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
                                            {{-- <tr>
                                                <td colspan="3"><strong>{{ $parentNames[$accountType] }}</strong></td>
                                            </tr> --}}
                                            @foreach ($accounts as $entry)
                                                <tr>
                                                    <td>{{ $entry['account_name'] }}</td>

                                                    {{-- <td>{{ number_format($entry['transaction_debit'], 2) }}</td>
                                                    <td>{{ number_format($entry['transaction_credit'], 2) }}</td> --}}
                                                    <td>{{ number_format($entry['closing_debit'], 2) }}</td>
                                                    <td>{{ number_format($entry['closing_credit'], 2) }}</td>
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
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            {{-- <th>{{ number_format($totalOpeningDebit, 2) }}</th>
                                            <th>{{ number_format($totalOpeningCredit, 2) }}</th>
                                            <th>{{ number_format($totalTransactionDebit, 2) }}</th>
                                            <th>{{ number_format($totalTransactionCredit, 2) }}</th> --}}
                                            <th>{{ number_format($totalClosingDebit, 2) }}</th>
                                            <th>{{ number_format($totalClosingCredit, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="col-md-4  float-left">
                                <br>
                                <br>

                                <p>Prepared By:_____________<br />
                                    Date:____________________
                                </p>
                            </div>
                            <div class="col-md-6 text-center">
                            </div>
                            <div class="col-md-2  ">
                                <br>
                                <br>
                                <p>Approved By:________________<br />
                                    Date:_________________</p>
                            </div>

                            <hr>


                            <div class="col-md-12 bg-success" style="text-align: center">
                                Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} products.
                                We believe you will be satisfied by our services.
                            </div>
                            <!-- /.col -->

                        </div>
                        <!-- Table row -->

                    </div>

                </div>
            </div>
        </div>



    </div>
@endsection
@section('scripts')
    @include('backend.pages.reports.excel')
@endsection
