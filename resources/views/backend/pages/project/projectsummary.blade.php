@extends('backend.layouts.master')

@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .kpi-card {
            border-radius: 10px;
            padding: 18px 20px;
            color: #fff;
        }

        .kpi-card .kpi-label {
            font-size: 13px;
            opacity: .85;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .kpi-card .kpi-value {
            font-size: 24px;
            font-weight: 700;
            margin-top: 4px;
        }

        .kpi-income {
            background: linear-gradient(135deg, #023c21, #177852);
        }


        .kpi-expense {
            background: linear-gradient(232deg, #600e02, #b92308);
        }

        .kpi-profit-pos {
            background: linear-gradient(135deg, #3f88c5, #5aa8e0);
        }

        .kpi-profit-neg {
            background: linear-gradient(98deg, #5e1017, #b10612);
        }

        .kpi-progress {
            background: linear-gradient(135deg, #6c5ce7, #8e7bf0);
        }

        .progress {
            height: 10px;
            margin-top: 8px;
            background: rgba(255, 255, 255, .25);
        }

        .progress-bar {
            background: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 30px 10px;
            color: #9aa0a6;
        }

        .empty-state i {
            font-size: 28px;
            display: block;
            margin-bottom: 8px;
        }

        .no-project-card {
            text-align: center;
            padding: 60px 20px;
            border: 2px dashed #d7dce1;
            border-radius: 10px;
            background: #fafbfc;
        }

        .no-project-card i {
            font-size: 46px;
            color: #b9c0c7;
            margin-bottom: 14px;
            display: block;
        }

        .no-project-card h5 {
            color: #495057;
            margin-bottom: 6px;
        }

        .no-project-card p {
            color: #9aa0a6;
            margin-bottom: 18px;
        }

        .amount-cell {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .select2-container {
            width: 100% !important;
        }

        .input-group {
            flex-wrap: nowrap;
        }

        @media (max-width: 1024px) {
            .kpi-card .kpi-value {
                font-size: 20px;
            }
        }

        @media (max-width: 768px) {
            .kpi-card {
                margin-bottom: 12px;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-tools {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-top: 10px;
                width: 100%;
            }

            .card-tools .btn {
                flex: 1 1 auto;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .kpi-card .kpi-value {
                font-size: 18px;
            }
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .nav-tabs .nav-link {
                display: block !important;
                opacity: 1 !important;
            }

            .tab-pane {
                display: block !important;
                opacity: 1 !important;
                page-break-inside: avoid;
            }
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0">Project Ledger Report</h1> --}}
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><span>Project Report</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">

            @if ($errors->any())
                <div class="alert alert-danger no-print">
                    <i class="fa fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif


            @if (isset($projectDetails) && $projectDetails)
                <div class="card card-default">
                    <div class="card-header no-print">
                        <h3 class="card-title">Project Summary — {{ $projectDetails->projectCode }}</h3>
                        <a onclick="window.print()" class="btn btn-default float-right"><i class="fas fa-print"></i>
                            Print</a>
                    </div>

                    <div class="card-body invoice p-3">

                        {{-- Header / project info --}}
                        <div class="row mb-3">
                            <div class="col-md-2 text-center">
                                @if (isset($companyInfo->logo))
                                    <img width="140" src="{{ asset('/backend/logo/' . $companyInfo->logo) }}"
                                        alt="">
                                @endif
                            </div>
                            <div class="col-md-5">
                                <h5>{{ $projectDetails->projectCode . ' - ' . $projectDetails->pname }}</h5>
                                <p class="mb-1"><i class="fa fa-map-marker-alt text-muted"></i>
                                    {{ $projectDetails->address }}</p>
                                <p class="mb-1"><i class="fa fa-user text-muted"></i> {{ $projectDetails->aname }} &nbsp;
                                    <i class="fa fa-phone text-muted"></i> {{ $projectDetails->aphone }}
                                </p>
                            </div>


                            <div class="col-md-5">
                                <p class="mb-1"><b>Budget:</b> TK. {{ number_format($projectDetails->budget ?? 0, 2) }}
                                </p>
                                <p class="mb-1"><b>Start:</b> {{ $projectDetails->start_date }} &nbsp; <b>End:</b>
                                    @if ($projectDetails->closing > $projectDetails->end_date)
                                        <span class="text-danger">{{ $projectDetails->closing }}</span>
                                    @else
                                        <span class="text-success">{{ $projectDetails->closing }}</span>
                                    @endif
                                </p>
                                <span
                                    class="badge badge-{{ $projectDetails->condition == 'Complete' ? 'success' : 'warning' }} p-2">
                                    {{ $projectDetails->condition }}
                                </span>
                            </div>
                        </div>

                        {{-- KPI cards --}}
                        <div class="row mb-3">

                            <div class="col-md-2 col-6">
                                <div class="kpi-card kpi-income">
                                    <div class="kpi-label">Budget</div>
                                    <div class="kpi-value">TK. {{ number_format($projectDetails->budget ?? 0, 0) }}</div>
                                </div>
                            </div>

                            <div class="col-md-2 col-6">
                                <div class="kpi-card kpi-income">
                                    <div class="kpi-label">Estimate Profit</div>
                                    <div class="kpi-value">TK. {{ number_format($summary['estimateProfit'], 0) }}</div>
                                </div>
                            </div>

                            <div class="col-md-2 col-6">
                                <div class="kpi-card kpi-income">
                                    <div class="kpi-label">Actual Cost Plan</div>
                                    <div class="kpi-value">TK. {{ number_format($summary['actualCost'], 0) }}</div>
                                </div>
                            </div>

                            <div class="col-md-2 col-6">
                                <div class="kpi-card kpi-expense">
                                    <div class="kpi-label">Current Expense</div>
                                    <div class="kpi-value">TK. {{ number_format($summary['totalExpense'], 0) }}</div>
                                </div>
                            </div>

                            <div class="col-md-2 col-6">
                                <div class="kpi-card kpi-income">
                                    <div class="kpi-label">Current Income</div>
                                    <div class="kpi-value">TK. {{ number_format($summary['currentIncome'], 0) }}</div>
                                </div>
                            </div>

                            <div class="col-md-2 col-6">
                                <div
                                    class="kpi-card {{ $summary['totalProfit'] >= 0 ? 'kpi-profit-pos' : 'kpi-profit-neg' }}">
                                    <div class="kpi-label">{{ $summary['totalProfit'] >= 0 ? 'Net Profit' : 'Net Loss' }}
                                    </div>
                                    <div class="kpi-value">TK. {{ number_format(abs($summary['totalProfit']), 0) }}</div>
                                </div>
                            </div>

                        </div>

                        {{-- Progress bar (base = Actual Cost Plan) --}}
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="kpi-card {{ $summary['isOverBudget'] ? 'kpi-profit-neg' : 'kpi-progress' }}">
                                    <div class="kpi-label">
                                        Cost Progress (Expense vs Actual Cost Plan)
                                        @if ($summary['isOverBudget'])
                                            <span class="badge badge-light text-danger ml-1">Over Plan!</span>
                                        @endif
                                    </div>
                                    <div class="kpi-value">{{ $summary['completePercent'] }}%</div>
                                    <div class="progress">
                                        <div class="progress-bar {{ $summary['isOverBudget'] ? 'bg-danger' : '' }}"
                                            style="width: {{ $summary['completePercentBar'] }}%"></div>
                                    </div>
                                    <div class="mt-1" style="font-size:12px; opacity:.9;">
                                        TK. {{ number_format($summary['totalExpense'], 0) }} spent of TK.
                                        {{ number_format($summary['actualCost'], 0) }} planned
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Charts
                        <div class="row mb-3">
                            <div class="col-lg-5">
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <h3 class="card-title">Progress</h3>
                                    </div>
                                    <div class="card-body"><canvas id="pieChart"
                                            style="min-height:230px;max-height:230px;"></canvas></div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <h3 class="card-title">Profit vs Budget</h3>
                                    </div>
                                    <div class="card-body"><canvas id="myChart"
                                            style="min-height:230px;max-height:230px;"></canvas></div>
                                </div>
                            </div>
                        </div> --}}

                        {{-- Detail tabs --}}
                        <ul class="nav nav-tabs no-print" id="detailTabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab-products">Products
                                    Used</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-dinc">Direct
                                    Income</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-iinc">Indirect
                                    Income</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-dexp">Direct
                                    Expense</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-iexp">Indirect
                                    Expense</a></li>
                        </ul>

                        <div class="tab-content border border-top-0 p-3 mb-4">

                            <div class="tab-pane fade show active" id="tab-products">
                                @if ($productgoodreceive->isNotEmpty() || $projectTransfer->isNotEmpty())
                                    <table class="table table-striped table-bordered" id="datatablexcel">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Invoice</th>
                                                <th>Details</th>
                                                <th>Qty</th>
                                                <th class="amount-cell">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($productgoodreceive as $val)
                                                @foreach ($val->details as $eachuse)
                                                    <tr>
                                                        <td>{{ $val->date }}</td>
                                                        <td>{{ $val->invoice_no }}</td>
                                                        <td>{{ ($eachuse->product->productCode ?? 'N/A') . ' - ' . ($eachuse->product->name ?? '') }}
                                                        </td>
                                                        <td>{{ $eachuse->qty }}</td>
                                                        <td class="amount-cell">
                                                            {{ number_format($eachuse->unit_price * $eachuse->qty, 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                            <tr class="font-weight-bold">
                                                <td colspan="4" class="text-center">Total Product Consumption</td>
                                                <td class="amount-cell">{{ number_format($summary['productAmount'], 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @else
                                    <div class="empty-state"><i class="fa fa-box-open"></i> No product usage recorded for
                                        this project.</div>
                                @endif
                            </div>

                            <div class="tab-pane fade" id="tab-dinc">
                                @if ($directIncome->isNotEmpty())
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Category</th>
                                                <th class="amount-cell">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($directIncome as $row)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                                    <td>{{ $row->account->account_name ?? 'N/A' }}</td>
                                                    <td class="amount-cell">{{ number_format($row->credit, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold">
                                                <td colspan="2" class="text-center">Total</td>
                                                <td class="amount-cell">{{ number_format($summary['ttlexpdirinc'], 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @else
                                    <div class="empty-state"><i class="fa fa-inbox"></i> No direct income transactions.
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane fade" id="tab-iinc">
                                @if ($indirectIncome->isNotEmpty())
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Category</th>
                                                <th class="amount-cell">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($indirectIncome as $row)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                                    <td>{{ $row->account->account_name ?? 'N/A' }}</td>
                                                    <td class="amount-cell">{{ number_format($row->credit, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold">
                                                <td colspan="2" class="text-center">Total</td>
                                                <td class="amount-cell">{{ number_format($summary['ttlexpindrinc'], 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @else
                                    <div class="empty-state"><i class="fa fa-inbox"></i> No indirect income transactions.
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane fade" id="tab-dexp">
                                @if ($directExpenses->isNotEmpty())
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Category</th>
                                                <th class="amount-cell">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($directExpenses as $row)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                                    <td>{{ $row->account->account_name ?? 'N/A' }}</td>
                                                    <td class="amount-cell">{{ number_format($row->debit, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold">
                                                <td colspan="2" class="text-center">Total</td>
                                                <td class="amount-cell">{{ number_format($summary['ttlexpdir'], 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @else
                                    <div class="empty-state"><i class="fa fa-inbox"></i> No direct expense transactions.
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane fade" id="tab-iexp">
                                @if ($indirectExpenses->isNotEmpty())
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Category</th>
                                                <th class="amount-cell">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($indirectExpenses as $row)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                                    <td>{{ $row->account->account_name ?? 'N/A' }}</td>
                                                    <td class="amount-cell">{{ number_format($row->debit, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold">
                                                <td colspan="2" class="text-center">Total</td>
                                                <td class="amount-cell">{{ number_format($summary['ttlexpind'], 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @else
                                    <div class="empty-state"><i class="fa fa-inbox"></i> No indirect expense transactions.
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($projectMoney)
                            <p><b>Project Money Received:</b> TK. {{ number_format($projectMoney, 2) }}</p>
                        @endif

                        {{-- Final summary --}}
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td colspan="2"><b><i class="fa fa-bullseye"></i> Full Project Summary</b></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><b>A. Income</b></td>
                                </tr>
                                <tr>
                                    <th>Sale Value</th>
                                    <th class="amount-cell">{{ number_format($projectDetails->budget ?? 0, 2) }}</th>
                                </tr>
                                <tr>
                                    <th>Indirect Income</th>
                                    <th class="amount-cell">{{ number_format($summary['ttlexpindrinc'], 2) }}</th>
                                </tr>
                                <tr>
                                    <th>Direct Income</th>
                                    <th class="amount-cell">{{ number_format($summary['ttlexpdirinc'], 2) }}</th>
                                </tr>
                                <tr>
                                    <td colspan="2"><b>B. Expenses</b></td>
                                </tr>
                                <tr>
                                    <th>Direct Expenses</th>
                                    <th class="amount-cell">{{ number_format($summary['ttlexpdir'], 2) }}</th>
                                </tr>
                                <tr>
                                    <th>Indirect Expenses</th>
                                    <th class="amount-cell">{{ number_format($summary['ttlexpind'], 2) }}</th>
                                </tr>
                                <tr>
                                    <th>Total Product Consumption</th>
                                    <th class="amount-cell">{{ number_format($summary['productAmount'], 2) }}</th>
                                </tr>
                                <tr class="{{ $summary['totalProfit'] >= 0 ? 'table-success' : 'table-danger' }}">
                                    <th>Total Profit (A - B)</th>
                                    <th class="amount-cell">{{ number_format($summary['totalProfit'], 2) }}</th>
                                </tr>
                            </tbody>
                        </table>

                        <div class="row mt-4">
                            <div class="col-md-4">
                                <p>Prepared By: _____________<br>Date: ____________________</p>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <p>Approved By: ________________<br>Date: _________________</p>
                            </div>
                        </div>

                        <div class="bg-success text-white text-center p-2">
                            Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} products. We believe you will
                            be satisfied by our services.
                        </div>
                    </div>
                </div>
            @else
                <div class="no-project-card no-print">
                    <i class="fa fa-folder-open"></i>
                    <h5>No Project Selected</h5>
                    <p>Report দেখতে হলে উপরের Filter থেকে একটি Project সিলেক্ট করে "Generate Report" চাপুন।</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('.select2').select2({
                width: '100%'
            });

            @if (isset($projectDetails) && $projectDetails)
                var pieCtx = $('#pieChart').get(0).getContext('2d');
                new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Complete', 'Incomplete'],
                        datasets: [{
                            data: [{{ $summary['completePercent'] ?? 0 }},
                                {{ $summary['incompletePercent'] ?? 100 }}
                            ],
                            backgroundColor: ['#00a65a', '#f56954']
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true
                    }
                });

                var barCtx = document.getElementById('myChart');
                var profit = {{ $summary['currentProfit'] ?? 0 }};
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: ["{{ $projectDetails->pname ?? '' }}"],
                        datasets: [{
                            label: 'Profit/Loss vs Budget',
                            data: [profit, {{ $projectDetails->budget ?? 0 }}],
                            backgroundColor: profit >= 0 ? ['#3F88C5', '#3F88C5'] : ['#FF5E5B',
                                '#3F88C5'
                            ]
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true
                    }
                });
            @endif
        });
    </script>
    @include('backend.pages.reports.excel')
@endsection
