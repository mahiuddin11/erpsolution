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
                        Ledger  Report </h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12 no-print">
            <div class="card card-default">
                <div class="card-body">
                    <form method="GET" action="{{ route('report.ledger.ledger') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="account_id">Account</label>
                                    <select name="account_id" id="account_id" class="form-control select2">
                                        <x-account :setAccounts="$accounts" :selectVal="$selectedAccountId"/>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ request('start_date') ?? date("Y-m-d") }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ request('end_date') ?? date("Y-m-d") }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="mt-4 btn btn-primary">Search</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($account)
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header no-print">
                        <h3 class="card-title">Ledger Report</h3>
                        <a onclick="window.print()" target="_blank" class="btn btn-default float-right my-2 no-print"><i
                                class="fas fa-print"></i>
                            Print</a>
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
                                                            src="{{ asset('/backend/invoicelogo/' . $companyInfo->invoice_logo) }}"
                                                            style="" alt="">
                                                    </a>
                                                @endif
                                            </td>
                                            <td width="70%" style="text-align: center">
                                                <h3>{{ $account->account_name }}</h3>
                                                <h4><b>From Date: {{ $startDate }}</b>, <b>To date:
                                                        {{ $endDate }} </b></h4>

                                            </td>
                                        </tr>
                                    </table>
                                    <table class="table table-bordered mt-2">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Voucher No</th>
                                                <th>Account Name</th>
                                                <th>Description</th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                                <th>Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="6"><strong>Opening Balance</strong></td>
                                                <td>{{ number_format($openingBalance, 2) }}</td>
                                            </tr>
                                            @foreach ($ledgerEntries as $entry)
                                                <tr>
                                                    <td>{{ $entry['date']->format('Y-m-d') }}</td>
                                                    <td>{{ $entry['invoice'] }}</td>
                                                    <td>{{ $entry['account_name'] }}</td>
                                                    <td>{{ $entry['description'] }}</td>
                                                    <td>{{ number_format($entry['debit'], 2) }}</td>
                                                    <td>{{ number_format($entry['credit'], 2) }}</td>
                                                    <td>{{ number_format($entry['balance'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="4"><strong>Closing Balance</strong></td>
                                                <td >{{$ledgerSummary['total_debit'] ?? 0}}</td>
                                                <td >{{$ledgerSummary['total_credit'] ?? 0}}</td>
                                                <td>{{ number_format($runningBalance, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-4  float-left">
                                    <br>
                                    <br>

                                    <p>Prepared By:_____________<br />
                                        Date:____________________
                                    </p>
                                </div>
                                <div class="col-6 text-center">
                                </div>
                                <div class="col-2  ">
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
        @endif



    </div>
@endsection
@section('scripts')
    @include('backend.pages.reports.excel')
@endsection
