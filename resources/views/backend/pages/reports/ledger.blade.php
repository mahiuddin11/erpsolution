@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }

        .ledger-table {
            table-layout: fixed;
            width: 100%;
            min-width: 900px;
        }

        .ledger-table td,
        .ledger-table th {
            word-wrap: break-word;
            white-space: normal;
        }

        .invoice-logo-img {
            max-width: 100%;
            height: auto;
        }

        .print-spacer-row {
            display: none;
        }

        @media (max-width: 575.98px) {
            .invoice-logo-img {
                width: 130px !important;
            }

            .invoice h3 {
                font-size: 1.25rem;
            }

            .invoice h4 {
                font-size: 0.95rem;
            }
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {

            .no-print,
            .modal,
            .content-header {
                display: none !important;
            }

            .main-header,
            .main-sidebar,
            .control-sidebar,
            .main-footer {
                display: none !important;
            }

            .content-wrapper {
                margin: 10mm !important;
                padding: 14mm 10mm !important;
                background: #fff !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .card-body {
                padding: 0 !important;
            }

            .invoice {
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
            }

            .invoice>.row {
                display: block !important;
            }

            .ledger-print-footer {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            .invoice h3 {
                font-size: 18px;
            }

            .invoice h4 {
                font-size: 13px;
            }

            .table-responsive {
                overflow: visible !important;
            }

            .ledger-table {
                width: 100% !important;
                min-width: 0 !important;
                font-size: 12px;
            }

            .ledger-table th,
            .ledger-table td {
                padding: 5px 5px !important;
            }

            .print-spacer-row {
                display: table-row !important;
            }

            .print-spacer-row th {
                height: 10mm !important;
                padding: 0 !important;
                border: none !important;
                background: transparent !important;
                line-height: 0 !important;
                font-size: 0 !important;
            }

            .ledger-table td:first-child {
                white-space: nowrap;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }

            a {
                color: #000 !important;
                text-decoration: none !important;
            }

            .voucher-link {
                background: none !important;
                background-color: transparent !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                color: #000 !important;
                display: inline !important;
                padding: 0 !important;
                font-size: 12px !important;
            }

            .voucher-link i {
                display: none !important;
            }

            img {
                max-width: 100% !important;
            }

            .badge,
            .bg-success {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Ledger Report </h1>
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
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="account_id">Account</label>
                                    <select name="account_id" id="account_id" class="form-control select2">
                                        <x-account :setAccounts="$accounts" :selectVal="$selectedAccountId" />
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ request('start_date') ?? date('Y-m-d') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ request('end_date') ?? date('Y-m-d') }}">
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
                                                            style="" alt="" class="invoice-logo-img">
                                                    </a>
                                                @endif
                                            </td>



                                            <td width="70%" style="text-align: center">
                                                <h3>{{ $account->account_name ?? '' }}</h3>
                                                <h4><b>From Date: {{ $startDate }}</b>, <b>To date:
                                                        {{ $endDate }} </b></h4>

                                            </td>
                                        </tr>
                                    </table>
                                    <table class="table table-bordered mt-2 ledger-table">
                                        <colgroup>
                                            <col style="width: 11%;"> {{-- Date --}}
                                            <col style="width: 9%;"> {{-- Voucher No --}}
                                            <col style="width: 10%;"> {{-- custom invoice (same as Voucher No) --}}
                                            <col style="width: 17%;"> {{-- Account Name --}}
                                            <col style="width: 16%;"> {{-- Description --}}
                                            <col style="width: 10%;"> {{-- Debit --}}
                                            <col style="width: 10%;"> {{-- Credit --}}
                                            <col style="width: 16%;"> {{-- Balance --}}
                                        </colgroup>
                                        <thead>
                                            <tr class="print-spacer-row">
                                                <th colspan="8">&nbsp;</th>
                                            </tr>
                                            <tr>
                                                <th>Date</th>
                                                <th class="voucher-no-col">Voucher No</th>
                                                <th class="custom-invoice-col">Custom Invoice</th>
                                                <th>Account Name</th>
                                                <th>Description</th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                                <th>Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="7"><strong>Opening Balance</strong></td>

                                                @php
                                                    $ob = $openingBalance;
                                                    $obAbs = number_format(abs($ob), 2);
                                                    $obLabel =
                                                        $account->balance_type === 'debit'
                                                            ? ($ob >= 0
                                                                ? 'Dr'
                                                                : 'Cr')
                                                            : ($ob >= 0
                                                                ? 'Cr'
                                                                : 'Dr');
                                                @endphp


                                                <td>{{ $obAbs }} {{ $obLabel }}</td>

                                                {{-- <td>{{ number_format($openingBalance, 2) }}</td> --}}
                                            </tr>

                                            @foreach ($ledgerEntries as $entry)
                                                <tr>
                                                    <td>{{ $entry['date']->format('Y-m-d') }}</td>
                                                    <td>
                                                        @if ($entry['voucher_url'])
                                                            <a href="javascript:void(0)"
                                                                class="voucher-link badge badge-info"
                                                                data-url="{{ $entry['voucher_url'] }}"
                                                                style="cursor: pointer; font-size: 0.85rem; padding: 5px 10px;">
                                                                {{ $entry['invoice'] }} <i class="fas fa-eye"></i>
                                                            </a>
                                                        @else
                                                            {{ $entry['invoice'] }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $entry['custom_invoice'] !== 'N/A' ? $entry['custom_invoice'] : '' }}
                                                    </td>
                                                    <td>{{ $entry['account_name'] }}</td>
                                                    <td>{{ $entry['description'] }}</td>
                                                    <td>{{ number_format($entry['debit'], 2) }}</td>
                                                    <td>{{ number_format($entry['credit'], 2) }}</td>
                                                    @php
                                                        $bal = $entry['balance'];
                                                        $balAbs = number_format(abs($bal), 2);
                                                        if ($account->balance_type === 'debit') {
                                                            $balLabel = $bal >= 0 ? 'Dr' : 'Cr';
                                                        } else {
                                                            $balLabel = $bal >= 0 ? 'Cr' : 'Dr';
                                                        }
                                                    @endphp
                                                    <td>{{ $balAbs }} {{ $balLabel }}</td>
                                                    {{-- <td>{{ number_format($entry['balance'], 2) }}</td> --}}
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="5"><strong>Closing Balance</strong></td>

                                                {{-- Closing Balance row --}}
                                                @php
                                                    $cb = $runningBalance;
                                                    $cbAbs = number_format(abs($cb), 2);
                                                    $cbLabel =
                                                        $account->balance_type === 'debit'
                                                            ? ($cb >= 0
                                                                ? 'Dr'
                                                                : 'Cr')
                                                            : ($cb >= 0
                                                                ? 'Cr'
                                                                : 'Dr');
                                                @endphp


                                                <td>{{ $ledgerSummary['total_debit'] ?? 0 }}</td>
                                                <td>{{ $ledgerSummary['total_credit'] ?? 0 }}</td>
                                                <td>{{ $cbAbs }} {{ $cbLabel }}</td>

                                                {{-- <td>{{ $ledgerSummary['total_debit'] ?? 0 }}</td>
                                               <td>{{ $ledgerSummary['total_credit'] ?? 0 }}</td> --}}
                                                {{-- <td>{{ number_format($runningBalance, 2) }}</td> --}}
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-12 ledger-print-footer">
                                    <div class="row">
                                        <div class="col-6 col-md-4  float-left">
                                            <br>
                                            <br>

                                            <p>Prepared By:_____________<br />
                                                Date:____________________
                                            </p>
                                        </div>
                                        <div class="col-md-6 d-none d-md-block text-center">
                                        </div>
                                        <div class="col-6 col-md-2  ">
                                            <br>
                                            <br>
                                            <p>Approved By:________________<br />
                                                Date:_________________</p>
                                        </div>
                                    </div>

                                    <hr>


                                    <div class="col-md-12 bg-success" style="text-align: center">
                                        Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} products.
                                        We believe you will be satisfied by our services.
                                    </div>
                                    <!-- /.col -->
                                </div>



                            </div>
                            <!-- Table row -->

                        </div>

                    </div>
                </div>
            </div>
        @endif



    </div>

    <div class="modal fade" id="voucherModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 90%;">
            <div class="modal-content" style="height: 90vh;">
                <div class="modal-header">
                    <h5 class="modal-title">Voucher Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0" style="height: 100%; position: relative;">
                    <div id="voucherLoader"
                        style="
                    position: absolute;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background: #fff;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10;
                ">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading voucher details...</p>
                        </div>
                    </div>
                    <iframe id="voucherIframe" src="" style="width:100%; height:100%; border:none;"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('backend.pages.reports.excel')
    <script>
        $(document).on('click', '.voucher-link', function() {
            const url = $(this).data('url');


            $('#voucherLoader').show();
            $('#voucherIframe').hide();
            $('#voucherIframe').attr('src', url);
            $('#voucherModal').modal('show');
        });


        $('#voucherIframe').on('load', function() {
            $('#voucherLoader').hide();
            $('#voucherIframe').show();
        });


        $('#voucherModal').on('hidden.bs.modal', function() {
            $('#voucherIframe').attr('src', '').hide();
            $('#voucherLoader').show();
        });
    </script>
@endsection
