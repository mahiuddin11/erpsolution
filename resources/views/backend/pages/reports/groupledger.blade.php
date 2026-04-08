@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }
        .debit-balance {
            color: #28a745;
            font-weight: bold;
        }
        .credit-balance {
            color: #dc3545;
            font-weight: bold;
        }

        /* Print styles */
        @media print {
            .print-border {
                border: 2px solid #000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .print-total-row {
                border-top: 3px solid #000 !important;
                border-bottom: 3px solid #000 !important;
                font-weight: bold !important;
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .print-summary-table {
                border: 2px solid #000 !important;
                background-color: white !important;
            }
            .print-summary-table th,
            .print-summary-table td {
                border: 1px solid #000 !important;
                font-weight: bold !important;
                padding: 8px !important;
            }
            .debit-total {
                border-left: 4px solid #28a745 !important;
            }
            .credit-total {
                border-left: 4px solid #dc3545 !important;
            }
            .net-total {
                border-left: 4px solid #007bff !important;
            }
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Group Ledger Report</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">

        @if (!empty($groupLedgerData))
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header no-print">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="card-title">Group Ledger Report</h3>
                            </div>
                            <div class="col-md-6">
                                <div class="float-right">
                                    <a onclick="window.print()" target="_blank" class="btn btn-default my-2 no-print">
                                        <i class="fas fa-print"></i> Print
                                    </a>
                                    <div class="input-group mr-2">
                                        <input type="text" id="searchTable" class="form-control" placeholder="Search accounts...">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="tableActions" class="no-print"></div>
                    </div>

                    <div class="card-body">
                        <div class="invoice p-3 mb-3">
                            <div class="row">
                                <div class="col-12">
                                    <!-- Header Table -->
                                    <table class="table table-bordered">
                                        <tr>
                                            <td style="text-align: center">
                                                @if (isset($companyInfo->logo))
                                                    <a href="{{ route('home') }}">
                                                        <img width="200px"
                                                            src="{{ asset('/backend/invoicelogo/' . $companyInfo->invoice_logo) }}"
                                                            alt="Company Logo">
                                                    </a>
                                                @endif
                                            </td>
                                            <td width="70%" style="text-align: center">
                                                <h3>{{ $companyInfo->company_name ?? 'Company Name' }}</h3>
                                                <h4>Group Ledger Report - Latest Closing Balances</h4>
                                                <h5><b>As of: {{ date('Y-m-d') }}</b></h5>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Main Report Table -->
                                    <table class="table table-bordered table-striped mt-3" id="ledgerTable">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Account Code</th>
                                                <th>Account Name</th>
                                                <th>Bank Name</th>
                                                <th>Balance Type</th>
                                                <th>Closing Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ledgerTableBody">
                                            @foreach ($groupLedgerData as $data)
                                                <tr>
                                                    <td>{{ $data['account_code'] }}</td>
                                                    <td>{{ $data['account_name'] }}</td>
                                                    <td>{{ $data['bank_name'] ?? '' }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $data['balance_type'] == 'debit' ? 'success' : 'danger' }}">
                                                            {{ ucfirst($data['balance_type']) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-right {{ $data['balance_type'] == 'debit' ? 'debit-balance' : 'credit-balance' }}">
                                                        {{ number_format($data['closing_balance'], 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-weight-bold print-total-row print-border">
                                                <td colspan="4" class="print-border"><strong>NET BALANCE</strong></td>
                                                <td class="text-right print-border"><strong>{{ number_format($totalDebitBalance - $totalCreditBalance, 2) }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <!-- Summary Section -->
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <table class="table table-bordered print-summary-table">
                                                <tr class="debit-total">
                                                    <th class="print-border">Total Debit Balances</th>
                                                    <td class="text-right print-border"><strong>{{ number_format($totalDebitBalance, 2) }}</strong></td>
                                                </tr>
                                                <tr class="credit-total">
                                                    <th class="print-border">Total Credit Balances</th>
                                                    <td class="text-right print-border"><strong>{{ number_format($totalCreditBalance, 2) }}</strong></td>
                                                </tr>
                                                <tr class="net-total">
                                                    <th class="print-border">Net Balance</th>
                                                    <td class="text-right print-border"><strong>{{ number_format($totalDebitBalance - $totalCreditBalance, 2) }}</strong></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Signature Section -->
                                <div class="col-4 float-left">
                                    <br><br>
                                    <p>Prepared By:_____________<br />
                                        Date:____________________
                                    </p>
                                </div>
                                <div class="col-4 text-center">
                                </div>
                                <div class="col-4">
                                    <br><br>
                                    <p>Approved By:________________<br />
                                        Date:_________________
                                    </p>
                                </div>

                                <hr>

                                <div class="col-md-12 bg-success text-center">
                                    Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} products.
                                    We believe you will be satisfied by our services.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-body text-center">
                        <h4>No accounts with balances found</h4>
                        <p>No accounts have transactions or balances to display.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    @include('backend.pages.reports.excel')

    <script>
        // Table search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchTable');
            const tableBody = document.getElementById('ledgerTableBody');
            const rows = tableBody.getElementsByTagName('tr');

            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let visibleRowCount = 0;

                    // Loop through all table rows
                    for (let i = 0; i < rows.length; i++) {
                        const row = rows[i];
                        const cells = row.getElementsByTagName('td');
                        let rowText = '';

                        // Get text content from all cells
                        for (let j = 0; j < cells.length; j++) {
                            rowText += cells[j].textContent.toLowerCase() + ' ';
                        }

                        // Show/hide row based on search term
                        if (rowText.includes(searchTerm)) {
                            row.style.display = '';
                            visibleRowCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    }

                    // Show/hide "no results" message
                    let noResultsRow = document.getElementById('noResultsRow');
                    if (visibleRowCount === 0 && searchTerm !== '') {
                        if (!noResultsRow) {
                            noResultsRow = document.createElement('tr');
                            noResultsRow.id = 'noResultsRow';
                            noResultsRow.innerHTML = '<td colspan="5" class="text-center text-muted"><em>No accounts found matching your search</em></td>';
                            tableBody.appendChild(noResultsRow);
                        }
                        noResultsRow.style.display = '';
                    } else if (noResultsRow) {
                        noResultsRow.style.display = 'none';
                    }
                });

                // Clear search on Escape key
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        this.value = '';
                        this.dispatchEvent(new Event('keyup'));
                        this.blur();
                    }
                });
            }
        });

        // Clear search function
        function clearSearch() {
            const searchInput = document.getElementById('searchTable');
            if (searchInput) {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('keyup'));
            }
        }
    </script>
@endsection
