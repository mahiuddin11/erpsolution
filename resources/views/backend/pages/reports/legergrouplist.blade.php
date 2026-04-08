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
                        {{ $title }} </h1>
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
                    <form method="GET" action="{{ route('report.group-ledger-list') }}">
                        <div class="row" id="group-select-wrapper">
                            <div class="col-md-3 col-sm-6 mb-2">
                                <label>Select Group</label>

                                <select name="account_id" id="main_group" class="form-control select2">

                                    <option value="">-- Select Group --</option>

                                    @foreach ($mainGroups as $group)
                                        <option value="{{ $group->id }}">
                                            {{ $group->accountCode }} - {{ $group->account_name }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                        </div>

                        <div class="row mt-2">

                            <div class="col-md-3 col-sm-6">

                                <label>Start Date</label>

                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}">

                            </div>

                            <div class="col-md-3 col-sm-6">

                                <label>End Date</label>

                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}">

                            </div>

                            <div class="col-md-2 mt-4">

                                <button class="btn btn-primary">Search</button>

                            </div>

                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header no-print">
                    <h3 class="card-title">Group Ledger</h3>
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
                                        {{-- <td width="70%" style="text-align: center">
                                                <h3>{{ $account->account_name ?? 'Expance' }}</h3>
                                                <h4><b>From Date: {{ $startDate ?? '00-01-2026' }}</b>, <b>To date:
                                                        {{ $endDate  ?? '00-01-2026' }} </b></h4>

                                            </td> --}}

                                        <td width="70%" style="text-align: center">
                                            <h3 id="selected-account-name">Ledger Name</h3>
                                            <h4>
                                                <b>From Date: <span
                                                        id="display-start-date">{{ request('start_date') ?? date('Y-m-d', strtotime('-30 days')) }}</span></b>,
                                                <b>To Date: <span
                                                        id="display-end-date">{{ request('end_date') ?? date('Y-m-d') }}</span></b>
                                            </h4>
                                        </td>
                                    </tr>
                                </table>

                                <table class="table table-bordered table-striped">

                                    <thead>
                                        <tr>
                                            <th>Ledger Name</th>
                                            <th>Code</th>
                                            <th>Opening Balance</th>
                                            <th>Debit (Period)</th>
                                            <th>Credit (Period)</th>
                                            <th>Closing Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ledger-table-body">
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Please select a group to
                                                view data</td>
                                        </tr>
                                    </tbody>

                                    {{-- <tfoot class="table-active">
                                        <tr>
                                            <th colspan="3">Group Total</th>
                                            <th class="text-right">{{ number_format($summary['total_debit'], 2) }}</th>
                                            <th class="text-right">{{ number_format($summary['total_credit'], 2) }}
                                            </th>
                                            <th class="text-right">
                                                {{ number_format($summary['total_debit'] - $summary['total_credit'], 2) }}
                                            </th>
                                        </tr>
                                    </tfoot> --}}
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




    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {

            // select2 initialize
            $('.select2').select2();

            // ─── Sub Group Dynamic Load ───
            $(document).on('change', '#main_group, .sub_group', 'input[name="start_date"], input[name="end_date"]',
                function() {

                    let parent_id = $(this).val();
                    let selectedText = $(this).find('option:selected').text()
                        .trim();
                    let currentCol = $(this).closest('.col-md-3');
                    currentCol.nextAll('.dynamic-group').remove();

                    if (parent_id == '') {
                        $('#selected-account-name').text('-- Select a Group --');
                        $('#ledger-table-body').html(`
            <tr><td colspan="6" class="text-center text-muted">No sub-groups or ledgers found</td></tr>
        `);
                        return;
                    }

                    $('#selected-account-name').text(selectedText);


                    //date change dinamic
                  

                    // Sub group dropdown load
                    $.ajax({
                        url: "{{ route('get.sub.groups') }}",
                        type: "GET",
                        data: {
                            parent_id: parent_id
                        },
                        success: function(data) {
                            if (data.length > 0) {
                                let html = `
                    <div class="col-md-3 col-sm-6 mb-2 dynamic-group">
                        <label>Select Sub Group</label>
                        <select class="form-control select2 sub_group" name="account_id">
                            <option value="">-- Select Sub Group --</option>`;

                                data.forEach(function(item) {
                                    html +=
                                        `<option value="${item.id}">${item.accountCode} - ${item.account_name}</option>`;
                                });

                                html += `</select></div>`;
                                $('#group-select-wrapper').append(html);
                                $('.select2').select2();
                            }
                        }
                    });

                    loadLedgerTable(parent_id);
                });


            $(document).on('change', 'input[name="start_date"], input[name="end_date"]', function() {
                  $('#display-start-date').text($('input[name="start_date"]').val());
                  $('#display-end-date').text($('input[name="end_date"]').val());
                    
                let account_id = getSelectedAccountId();
                if (account_id) loadLedgerTable(account_id);
            });


            function getSelectedAccountId() {
                let lastSelect = $('#group-select-wrapper select').last();
                return lastSelect.val();
            }

            function loadLedgerTable(account_id) {
                let start_date = $('input[name="start_date"]').val();
                let end_date = $('input[name="end_date"]').val();

                $('#ledger-table-body').html(`
            <tr><td colspan="6" class="text-center">
                <i class="fas fa-spinner fa-spin"></i> Loading...
            </td></tr>
        `);

                $.ajax({
                    url: "{{ route('group-ledger-data') }}",
                    type: "GET",
                    data: {
                        account_id: account_id,
                        start_date: start_date,
                        end_date: end_date
                    },
                    success: function(data) {
                        if (data.subLedgers.length === 0) {
                            $('#ledger-table-body').html(`
                        <tr><td colspan="6" class="text-center text-muted">No sub-groups or ledgers found</td></tr>
                    `);
                            return;
                        }

                        let rows = '';
                        data.subLedgers.forEach(function(sub) {
                            let closingClass = sub.closing_balance >= 0 ? 'text-success' :
                                'text-danger';
                            rows += `
                    <tr>
                        <td>${sub.account_name}</td>
                        <td>${sub.accountCode ?? sub.account_code ?? ''}</td>
                        <td class="text-right">${formatNumber(sub.opening_balance)}</td>
                        <td class="text-right">${formatNumber(sub.period_debit)}</td>
                        <td class="text-right">${formatNumber(sub.period_credit)}</td>
                        <td class="text-right fw-bold ${closingClass}">${formatNumber(sub.closing_balance)}</td>
                    </tr>`;
                        });

                        // Summary row
                        rows += `
                <tr class="table-active fw-bold">
                    <td colspan="3"><strong>Group Total</strong></td>
                    <td class="text-right"><strong>${formatNumber(data.summary.total_debit)}</strong></td>
                    <td class="text-right"><strong>${formatNumber(data.summary.total_credit)}</strong></td>
                    <td class="text-right"><strong>${formatNumber(data.summary.total_debit - data.summary.total_credit)}</strong></td>
                </tr>`;

                        $('#ledger-table-body').html(rows);
                    },
                    error: function() {
                        $('#ledger-table-body').html(`
                    <tr><td colspan="6" class="text-center text-danger">Something went wrong!</td></tr>
                `);
                    }
                });
            }

            // Number format helper
            function formatNumber(num) {
                num = parseFloat(num) || 0;
                return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }

        });
    </script>
    @include('backend.pages.reports.excel')
@endsection
