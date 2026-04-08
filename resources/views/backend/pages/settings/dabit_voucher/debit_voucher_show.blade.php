@extends('backend.layouts.master')
@section('title')
    {{ $title }}
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
                        Voucher </h1>
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
                    <h3 class="card-title">Debit Voucher Report</h3>
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
                                                        src="{{ asset('/backend/logo/' . $companyInfo->logo) }}"
                                                        style="" alt="">
                                                </a>
                                            @endif

                                        </td>

                                        <td width="70%" style="text-align: center">
                                            <b style="font-size : 20px">{{ $companyInfo->company_name ?? 'N/A' }}</b>
                                            <address>
                                                Phone : <strong>{{ $companyInfo->phone ?? 'N/A' }}</strong><br>
                                                Address : <strong><em>{{ $companyInfo->address ?? 'N/A' }}</em></strong><br>
                                            </address>
                                            <h4 style="margin-top: -20px">Payment Voucher</h4>
                                            <div class="row">
                                                <div class="col-md-6 text-left">No:
                                                    {{ $account_transactions->first()->debitVoucher->voucher_no ?? '' }}
                                                </div>
                                                <div class="col-md-6 text-right">Date:
                                                    {{ date('d-M-Y', strtotime($debitVoucher->date)) }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <table id="datatablexcel" class="table table-striped table-bordered">
                                    <?php
                                    $count = 0;
                                    ?>
                                    <thead>
                                        <tr>
                                            <td height="25" width="5%"><strong>SL.</strong></td>
                                            <td width="12%"><strong>Account</strong></td>
                                            @php
                                                $voucher = $account_transactions->first();
                                            @endphp
                                            @if (!empty($voucher->debitVoucher->project_id))
                                                @php
                                                    $count += 1;
                                                @endphp
                                                <td width="12%"><strong>Project</strong></td>
                                            @endif
                                            @if (!empty($voucher->debitVoucher->supplier_id))
                                                @php
                                                    $count += 1;
                                                @endphp
                                                <td width="12%"><strong>Supplier</strong></td>
                                            @endif
                                            @if (!empty($voucher->debitVoucher->customer_id))
                                                @php
                                                    $count += 1;
                                                @endphp
                                                <td width="12%"><strong>Customer</strong></td>
                                            @endif
                                            @if (!empty($voucher->debitVoucher->employee_id))
                                                @php
                                                    $count += 1;
                                                @endphp
                                                <td width="12%"><strong>Employee</strong></td>
                                            @endif

                                            <td width="10%" align="right">
                                                <strong>Debit</strong>
                                            </td>
                                            <td width="10%" align="right">
                                                <strong>Credit</strong>
                                            </td>
                                            {{-- <td width="10%" align="right">
                                                <strong>Balance</strong>
                                            </td> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalCredit = 0;
                                            $totalDebit = 0;
                                        @endphp

                                        @if (count($account_transactions) != 0)
                                            @foreach ($account_transactions as $key => $transaction)
                                                <tr class="table_data">
                                                    <td align="right">
                                                        <strong>{{ $key + 1 }}</strong>
                                                    </td>

                                                    <td align="right">
                                                        <strong>{{ $transaction->account->account_name ??" " }}</strong>
                                                    </td>

                                                    @if (!empty($voucher->debitVoucher->project_id))
                                                        <td align="right">
                                                            <strong>{{ $transaction->debitVoucher->project->name }}</strong>
                                                        </td>
                                                    @endif

                                                    @if (!empty($voucher->debitVoucher->supplier_id))
                                                        <td align="right">
                                                            <strong>{{ $transaction->debitVoucher->supplier->name }}</strong>
                                                        </td>
                                                    @endif

                                                    @if (!empty($voucher->debitVoucher->customer_id))
                                                        <td align="right">
                                                            <strong>{{ $transaction->debitVoucher->customer->name }}</strong>
                                                        </td>
                                                    @endif

                                                    @if (!empty($voucher->debitVoucher->employee_id))
                                                        <td align="right">
                                                            <strong>{{ $transaction->debitVoucher->employee->name }}</strong>
                                                        </td>
                                                    @endif

                                                    <td align="right">
                                                        <?php
                                                        $totalDebit += $transaction->debit == null ? 0 : $transaction->debit;
                                                        ?>
                                                        <strong>
                                                            {{ $transaction->debit == null ? 0 : $transaction->debit }}</strong>

                                                    </td>
                                                    <td align="right">
                                                        <?php
                                                        $totalCredit += $transaction->credit == null ? 0 : $transaction->credit;
                                                        ?>
                                                        <strong>
                                                            {{ $transaction->credit == null ? 0 : $transaction->credit }}</strong>

                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center">No Data Found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr class="table_data">
                                            <td colspan="{{ 1 + $count }}"><strong>Note:
                                                    {{ $debitVoucher->note }}</strong>
                                            </td>
                                            <td align="right"><strong>Total</strong>
                                            </td>
                                            <td align="right">
                                                <strong>{{ $totalDebit }}</strong>
                                            </td>
                                            <td align="right">
                                                <strong>{{ $totalCredit }}</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="col-4  text-left">
                                <br>
                                <br>
                                <p>Prepared By: {{$debitVoucher->createBy->name ?? ""}}<br />
                                    Date:____________________
                                </p>
                            </div>
                            <div class="col-4 text-center">
                                <br>
                                <br>
                                <p>Received By:________________<br />
                                    Date:_________________</p>
                            </div>
                            <div class="col-4 text-right">
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
