@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .is-section-title td {
            background-color: #e9ecef;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 8px 12px;
        }

        .is-subtotal td {
            background-color: #f8f9fa;
            font-weight: 600;
            border-top: 1.5px solid #dee2e6;
        }

        .is-profit td {
            background-color: #d4edda;
            font-weight: 700;
            border-top: 2px solid #28a745;
            font-size: 15px;
        }

        .is-loss td {
            background-color: #f8d7da;
            font-weight: 700;
            border-top: 2px solid #dc3545;
            font-size: 15px;
        }

        .is-operating td {
            background-color: #cce5ff;
            font-weight: 600;
            border-top: 1.5px solid #004085;
        }

        .amount-col {
            text-align: right;
            min-width: 130px;
            font-variant-numeric: tabular-nums;
        }

        .text-danger-amount {
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

        <div class="col-md-12 no-print">
            <div class="card card-outline card-info">
                <div class="card-body">
                    <form action="{{ route('report.incomestatement.incomestatement') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From Date:</label>
                                    <input type="date" class="form-control" name="from_date" id="from_date"
                                        value="{{ $startDate ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To Date:</label>
                                    <input type="date" class="form-control" name="to_date" id="to_date"
                                        value="{{ $endDate ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header no-print">
                    <h3 class="card-title">Income Statement Report</h3>
                    <a onclick="window.print()" class="btn btn-default float-right my-2">
                        <i class="fas fa-print"></i> Print
                    </a>
                </div>

                <div class="card-body">
                    <div class="invoice p-3 mb-3">

                        <div class="row mb-3">
                            <div class="col-12 text-center">
                                @if (isset($companyInfo->logo))
                                    <img width="100px" src="{{ asset('/backend/logo/' . $companyInfo->logo) }}"
                                        alt="Logo"><br>
                                @endif
                                <h4><strong>{{ $companyInfo->company_name ?? '' }}</strong></h4>
                                <h5>Income Statement (Profit &amp; Loss)</h5>
                                <p>From: <strong>{{ $startDate }}</strong> &nbsp;|&nbsp; To:
                                    <strong>{{ $endDate }}</strong>
                                </p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr style="background:#343a40; color:#fff;">
                                        <th>Particulars</th>
                                        <th class="amount-col">Amount (৳)</th>
                                        <th class="text-center no-print" style="width:100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr class="is-section-title">
                                        <td colspan="3">Income</td>
                                    </tr>
                                    <tr>
                                        <td class="pl-4">Sales &amp; Direct Income</td>
                                        <td class="amount-col">{{ number_format($totalRevenue, 2) }}</td>
                                        <td class="text-center no-print">
                                            <button type="button" class="btn btn-sm btn-outline-info details-btn"
                                                data-category="revenue">
                                                <i class="fa fa-eye"></i> Details
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="is-subtotal">
                                        <td>Total Income (A)</td>
                                        <td class="amount-col">{{ number_format($totalRevenue, 2) }}</td>
                                        <td class="no-print"></td>
                                    </tr>

                                    <tr class="is-section-title">
                                        <td colspan="3">Cost of Goods Sold (COGS)</td>
                                    </tr>
                                    <tr>
                                        <td class="pl-4">Direct Expenses &amp; Purchase</td>
                                        <td class="amount-col">{{ number_format($totalCOGS, 2) }}</td>
                                        <td class="text-center no-print">
                                            <button type="button" class="btn btn-sm btn-outline-info details-btn"
                                                data-category="cogs">
                                                <i class="fa fa-eye"></i> Details
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="is-subtotal">
                                        <td>Total COGS (B)</td>
                                        <td class="amount-col">{{ number_format($totalCOGS, 2) }}</td>
                                        <td class="no-print"></td>
                                    </tr>

                                    <tr class="{{ $grossProfit >= 0 ? 'is-profit' : 'is-loss' }}">
                                        <td>Gross Profit (A &minus; B)</td>
                                        <td class="amount-col {{ $grossProfit < 0 ? 'text-danger-amount' : '' }}">
                                            {{ number_format($grossProfit, 2) }}
                                        </td>
                                        <td class="no-print"></td>
                                    </tr>

                                    <tr class="is-section-title">
                                        <td colspan="3">Operating Expenses</td>
                                    </tr>
                                    <tr>
                                        <td class="pl-4">Indirect Expenses</td>
                                        <td class="amount-col">{{ number_format($totalOpex, 2) }}</td>
                                        <td class="text-center no-print">
                                            <button type="button" class="btn btn-sm btn-outline-info details-btn"
                                                data-category="opex">
                                                <i class="fa fa-eye"></i> Details
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="is-subtotal">
                                        <td>Total Operating Expenses (C)</td>
                                        <td class="amount-col">{{ number_format($totalOpex, 2) }}</td>
                                        <td class="no-print"></td>
                                    </tr>

                                    <tr class="is-operating">
                                        <td>Operating Income (A &minus; B &minus; C)</td>
                                        <td class="amount-col {{ $operatingIncome < 0 ? 'text-danger-amount' : '' }}">
                                            {{ number_format($operatingIncome, 2) }}
                                        </td>
                                        <td class="no-print"></td>
                                    </tr>

                                    <tr class="is-section-title">
                                        <td colspan="3">Non-Operating Income</td>
                                    </tr>
                                    <tr>
                                        <td class="pl-4">Indirect Income</td>
                                        <td class="amount-col">{{ number_format($totalNonOpIncome, 2) }}</td>
                                        <td class="text-center no-print">
                                            <button type="button" class="btn btn-sm btn-outline-info details-btn"
                                                data-category="nonop">
                                                <i class="fa fa-eye"></i> Details
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="is-subtotal">
                                        <td>Total Non-Operating Income (D)</td>
                                        <td class="amount-col">{{ number_format($totalNonOpIncome, 2) }}</td>
                                        <td class="no-print"></td>
                                    </tr>

                                    <tr class="{{ $netIncome >= 0 ? 'is-profit' : 'is-loss' }}">
                                        <td>
                                            Net {{ $netIncome >= 0 ? 'Profit' : 'Loss' }} (A &minus; B &minus; C + D)
                                            @if ($netIncome >= 0)
                                                <span class="badge badge-success">Profitable</span>
                                            @else
                                                <span class="badge badge-danger">Loss</span>
                                            @endif
                                        </td>
                                        <td class="amount-col {{ $netIncome < 0 ? 'text-danger-amount' : '' }}">
                                            {{ number_format($netIncome, 2) }}
                                        </td>
                                        <td class="no-print"></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

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

    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalTitle">Transaction Details</h5>
                    <button type="button" class="btn btn-default btn-sm no-print" onclick="printModal()">
                        <i class="fa fa-print"></i> Print
                    </button>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="detailsModalBody">
                    <div class="text-center py-4">
                        <i class="fa fa-spinner fa-spin"></i> Loading...
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.details-btn').on('click', function() {
                var category = $(this).data('category');
                var fromDate = $('#from_date').val();
                var toDate = $('#to_date').val();
                var rowLabel = $(this).closest('tr').find('td:first').text().trim();

                $('#detailsModalTitle').text(rowLabel + ' — Details');
                $('#detailsModalBody').html(
                    '<div class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>'
                );
                $('#detailsModal').modal('show');

                $.ajax({
                    url: '{{ route('report.incomestatement.details') }}',
                    type: 'GET',
                    data: {
                        category: category,
                        from_date: fromDate,
                        to_date: toDate
                    },
                    success: function(response) {
                        $('#detailsModalBody').html(response);
                    },
                    error: function() {
                        $('#detailsModalBody').html(
                            '<p class="text-danger">Details loading some issue</p>');
                    }
                });
            });
        });

        function printModal() {
            var printContents = document.getElementById('detailsModalBody').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
    @include('backend.pages.reports.excel')
@endsection
