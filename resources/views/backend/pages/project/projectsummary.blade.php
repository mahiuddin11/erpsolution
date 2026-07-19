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
            position: relative;
            transition: transform .15s ease, box-shadow .15s ease;
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

        .kpi-card.clickable {
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .12);
        }

        .kpi-card.clickable:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .22);
        }

        .kpi-card.clickable:active {
            transform: translateY(-1px);
        }

        .kpi-card .kpi-hint {
            position: absolute;
            top: 10px;
            right: 12px;
            font-size: 11px;
            opacity: .75;
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

        .modal-title small {
            display: block;
            font-size: 12px;
            opacity: .7;
            font-weight: 400;
            margin-top: 2px;
        }

        .kpi-mini-table th,
        .kpi-mini-table td {
            padding: 8px 10px;
        }

        @media (max-width: 1024px) {
            .kpi-card .kpi-value {
                font-size: 20px;
            }
        }

        @media (max-width: 768px) {
            .kpi-card {
                margin-bottom: 4px;
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

            .modal-dialog {
                margin: 8px;
            }

            .modal-footer .btn {
                flex: 1 1 auto;
            }
        }

        @media (max-width: 480px) {
            .kpi-card .kpi-value {
                font-size: 18px;
            }

            .kpi-card .kpi-label {
                font-size: 11px;
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
                    <h1 class="m-0">Project Ledger Report</h1>
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
                                    <i class="fa fa-phone text-muted"></i> {{ $projectDetails->aphone }}</p>
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

                        {{-- Financial KPI cards — all clickable --}}
                        <div class="row mb-2">
                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="kpi-card kpi-income clickable" data-toggle="modal" data-target="#modalBudget">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label">Budget</div>
                                    <div class="kpi-value">TK. {{ number_format($projectDetails->budget ?? 0, 0) }}</div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="kpi-card kpi-income clickable" data-toggle="modal"
                                    data-target="#modalEstimateProfit">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label">Estimate Profit</div>
                                    <div class="kpi-value">TK. {{ number_format($summary['estimateProfit'], 0) }}</div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="kpi-card kpi-income clickable" data-toggle="modal"
                                    data-target="#modalActualCost">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label">Actual Cost Plan</div>
                                    <div class="kpi-value">TK. {{ number_format($summary['actualCost'], 0) }}</div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="kpi-card kpi-expense clickable" data-toggle="modal"
                                    data-target="#modalCurrentExpense">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label">Current Expense</div>
                                    <div class="kpi-value">TK. {{ number_format($summary['totalExpense'], 0) }}</div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="kpi-card kpi-income clickable" data-toggle="modal"
                                    data-target="#modalCurrentIncome">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label">Current Income</div>
                                    <div class="kpi-value">TK. {{ number_format($summary['currentIncome'], 0) }}</div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="kpi-card clickable {{ $summary['totalProfit'] >= 0 ? 'kpi-profit-pos' : 'kpi-profit-neg' }}"
                                    data-toggle="modal" data-target="#modalNetProfit">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label">{{ $summary['totalProfit'] >= 0 ? 'Net Profit' : 'Net Loss' }}
                                    </div>
                                    <div class="kpi-value">TK. {{ number_format(abs($summary['totalProfit']), 0) }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Progress bar --}}
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

                        {{-- Document / Process KPI cards --}}
                        <div class="row mb-3 no-print">
                            <div class="col-lg-2 col-md-4 col-6 mb-2">
                                <div class="kpi-card kpi-progress clickable" data-toggle="modal"
                                    data-target="#modalRequisition">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label"><i class="fa fa-file-alt"></i> Purchase Requisition</div>
                                    <div class="kpi-value">{{ $summary['countRequisition'] }}</div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6 mb-2">
                                <div class="kpi-card kpi-progress clickable" data-toggle="modal"
                                    data-target="#modalOrder">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label"><i class="fa fa-file-invoice"></i> Purchase Order</div>
                                    <div class="kpi-value">{{ $summary['countOrder'] }}</div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6 mb-2">
                                <div class="kpi-card kpi-progress clickable" data-toggle="modal"
                                    data-target="#modalVoucher">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label"><i class="fa fa-receipt"></i> Purchase Voucher</div>
                                    <div class="kpi-value">{{ $summary['countVoucher'] }}</div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6 mb-2">
                                <div class="kpi-card kpi-income clickable" data-toggle="modal" data-target="#modalGrn">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label"><i class="fa fa-box"></i> Good Receive</div>
                                    <div class="kpi-value">{{ $summary['countGrn'] }}</div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6 mb-2">
                                <div class="kpi-card kpi-income clickable" data-toggle="modal"
                                    data-target="#modalTransfer">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label"><i class="fa fa-exchange-alt"></i> Transfer</div>
                                    <div class="kpi-value">{{ $summary['countTransfer'] }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Detail tabs --}}
                        <ul class="nav nav-tabs no-print" id="detailTabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab"
                                    href="#tab-products">Products Used</a></li>
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

    @if (isset($projectDetails) && $projectDetails)

        {{-- ============ Budget Modal ============ --}}
        <div class="modal fade" id="modalBudget" tabindex="-1">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Budget Breakdown <small>{{ $projectDetails->projectCode }}</small></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered kpi-mini-table" id="tblBudget">
                            <tbody>
                                <tr>
                                    <th>Project Budget (Sale Value)</th>
                                    <td class="amount-cell">{{ number_format($projectDetails->budget ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Direct Income (extra)</th>
                                    <td class="amount-cell">{{ number_format($summary['ttlexpdirinc'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Indirect Income (extra)</th>
                                    <td class="amount-cell">{{ number_format($summary['ttlexpindrinc'], 2) }}</td>
                                </tr>
                                <tr class="font-weight-bold table-success">
                                    <th>Total Income</th>
                                    <td class="amount-cell">{{ number_format($summary['totalIncome'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" onclick="printModalTable('tblBudget','Budget Breakdown')"><i
                                class="fa fa-print"></i> Print</button>
                        <button class="btn btn-success"
                            onclick="exportModalTableToExcel('tblBudget','budget_breakdown')"><i
                                class="fa fa-file-excel"></i> Excel</button>
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Estimate Profit Modal ============ --}}
        <div class="modal fade" id="modalEstimateProfit" tabindex="-1">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Estimate Profit <small>{{ $projectDetails->projectCode }}</small></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered kpi-mini-table" id="tblEstimateProfit">
                            <tbody>
                                <tr>
                                    <th>Project Budget</th>
                                    <td class="amount-cell">{{ number_format($projectDetails->budget ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Estimate Profit (planned)</th>
                                    <td class="amount-cell">{{ number_format($summary['estimateProfit'], 2) }}</td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <th>Actual Cost Plan (Budget − Estimate Profit)</th>
                                    <td class="amount-cell">{{ number_format($summary['actualCost'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Current Profit (at {{ $summary['completePercentBar'] }}% progress)</th>
                                    <td class="amount-cell">{{ number_format($summary['currentProfit'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default"
                            onclick="printModalTable('tblEstimateProfit','Estimate Profit')"><i class="fa fa-print"></i>
                            Print</button>
                        <button class="btn btn-success"
                            onclick="exportModalTableToExcel('tblEstimateProfit','estimate_profit')"><i
                                class="fa fa-file-excel"></i> Excel</button>
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Actual Cost Plan Modal ============ --}}
        <div class="modal fade" id="modalActualCost" tabindex="-1">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Actual Cost Plan <small>{{ $projectDetails->projectCode }}</small></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered kpi-mini-table" id="tblActualCost">
                            <tbody>
                                <tr>
                                    <th>Actual Cost Plan</th>
                                    <td class="amount-cell">{{ number_format($summary['actualCost'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Current Expense (spent so far)</th>
                                    <td class="amount-cell">{{ number_format($summary['totalExpense'], 2) }}</td>
                                </tr>
                                <tr
                                    class="font-weight-bold {{ $summary['isOverBudget'] ? 'table-danger' : 'table-success' }}">
                                    <th>{{ $summary['isOverBudget'] ? 'Over Plan By' : 'Remaining Plan' }}</th>
                                    <td class="amount-cell">
                                        {{ number_format(abs($summary['actualCost'] - $summary['totalExpense']), 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Progress</th>
                                    <td class="amount-cell">{{ $summary['completePercent'] }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" onclick="printModalTable('tblActualCost','Actual Cost Plan')"><i
                                class="fa fa-print"></i> Print</button>
                        <button class="btn btn-success"
                            onclick="exportModalTableToExcel('tblActualCost','actual_cost_plan')"><i
                                class="fa fa-file-excel"></i> Excel</button>
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Current Expense Modal (Direct+Indirect+Product) ============ --}}
        <div class="modal fade" id="modalCurrentExpense" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Current Expense — All Transactions
                            <small>{{ $projectDetails->projectCode }}</small></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @if ($directExpenses->isNotEmpty() || $indirectExpenses->isNotEmpty() || $summary['productAmount'] > 0)
                            <table class="table table-bordered table-sm" id="tblCurrentExpense">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th class="amount-cell">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($directExpenses as $row)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                            <td>Direct</td>
                                            <td>{{ $row->account->account_name ?? 'N/A' }}</td>
                                            <td class="amount-cell">{{ number_format($row->debit, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    @foreach ($indirectExpenses as $row)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                            <td>Indirect</td>
                                            <td>{{ $row->account->account_name ?? 'N/A' }}</td>
                                            <td class="amount-cell">{{ number_format($row->debit, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3">Product Consumption (GRN + Transfer)</td>
                                        <td class="amount-cell">{{ number_format($summary['productAmount'], 2) }}</td>
                                    </tr>
                                    <tr class="font-weight-bold table-danger">
                                        <td colspan="3" class="text-center">Grand Total Expense</td>
                                        <td class="amount-cell">{{ number_format($summary['totalExpense'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state"><i class="fa fa-inbox"></i> No expense recorded for this project.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default"
                            onclick="printModalTable('tblCurrentExpense','Current Expense')"><i class="fa fa-print"></i>
                            Print</button>
                        <button class="btn btn-success"
                            onclick="exportModalTableToExcel('tblCurrentExpense','current_expense')"><i
                                class="fa fa-file-excel"></i> Excel</button>
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Current Income Modal (Direct+Indirect+Project Money) ============ --}}
        <div class="modal fade" id="modalCurrentIncome" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Current Income — All Transactions
                            <small>{{ $projectDetails->projectCode }}</small></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @if ($directIncome->isNotEmpty() || $indirectIncome->isNotEmpty() || $projectMoney > 0)
                            <table class="table table-bordered table-sm" id="tblCurrentIncome">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th class="amount-cell">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($directIncome as $row)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                            <td>Direct</td>
                                            <td>{{ $row->account->account_name ?? 'N/A' }}</td>
                                            <td class="amount-cell">{{ number_format($row->credit, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    @foreach ($indirectIncome as $row)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                            <td>Indirect</td>
                                            <td>{{ $row->account->account_name ?? 'N/A' }}</td>
                                            <td class="amount-cell">{{ number_format($row->credit, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3">Project Money Received</td>
                                        <td class="amount-cell">{{ number_format($projectMoney, 2) }}</td>
                                    </tr>
                                    <tr class="font-weight-bold table-success">
                                        <td colspan="3" class="text-center">Grand Total Income</td>
                                        <td class="amount-cell">{{ number_format($summary['currentIncome'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state"><i class="fa fa-inbox"></i> No income recorded for this project.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" onclick="printModalTable('tblCurrentIncome','Current Income')"><i
                                class="fa fa-print"></i> Print</button>
                        <button class="btn btn-success"
                            onclick="exportModalTableToExcel('tblCurrentIncome','current_income')"><i
                                class="fa fa-file-excel"></i> Excel</button>
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Net Profit/Loss Modal (Full Summary) ============ --}}
        <div class="modal fade" id="modalNetProfit" tabindex="-1">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $summary['totalProfit'] >= 0 ? 'Net Profit' : 'Net Loss' }} — Full
                            Summary <small>{{ $projectDetails->projectCode }}</small></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered kpi-mini-table" id="tblNetProfit">
                            <tbody>
                                <tr>
                                    <td colspan="2"><b>A. Income</b></td>
                                </tr>
                                <tr>
                                    <th>Sale Value</th>
                                    <td class="amount-cell">{{ number_format($projectDetails->budget ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Direct Income</th>
                                    <td class="amount-cell">{{ number_format($summary['ttlexpdirinc'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Indirect Income</th>
                                    <td class="amount-cell">{{ number_format($summary['ttlexpindrinc'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><b>B. Expenses</b></td>
                                </tr>
                                <tr>
                                    <th>Direct Expenses</th>
                                    <td class="amount-cell">{{ number_format($summary['ttlexpdir'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Indirect Expenses</th>
                                    <td class="amount-cell">{{ number_format($summary['ttlexpind'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Product Consumption</th>
                                    <td class="amount-cell">{{ number_format($summary['productAmount'], 2) }}</td>
                                </tr>
                                <tr
                                    class="font-weight-bold {{ $summary['totalProfit'] >= 0 ? 'table-success' : 'table-danger' }}">
                                    <th>{{ $summary['totalProfit'] >= 0 ? 'Net Profit' : 'Net Loss' }} (A − B)</th>
                                    <td class="amount-cell">{{ number_format(abs($summary['totalProfit']), 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default"
                            onclick="printModalTable('tblNetProfit','Net Profit Loss Summary')"><i
                                class="fa fa-print"></i> Print</button>
                        <button class="btn btn-success"
                            onclick="exportModalTableToExcel('tblNetProfit','net_profit_loss')"><i
                                class="fa fa-file-excel"></i> Excel</button>
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Purchase Requisition Modal ============ --}}
        <div class="modal fade" id="modalRequisition" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Purchase Requisition <small>{{ $projectDetails->projectCode }}</small>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @if ($purchaseRequisitions->isNotEmpty())
                            <table class="table table-bordered table-sm" id="tblRequisition">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Reference No</th>
                                        <th>Status</th>
                                        <th class="amount-cell">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseRequisitions as $row)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                            <td>{{ $row->reference_no ?? ($row->requisition_no ?? 'N/A') }}</td>
                                            <td>{{ $row->status ?? '-' }}</td>
                                            <td class="amount-cell">
                                                {{ number_format($row->details->sum(fn($d) => ($d->qty ?? 0) * ($d->unit_price ?? 0)), 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state"><i class="fa fa-inbox"></i> No purchase requisition found.</div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default"
                            onclick="printModalTable('tblRequisition','Purchase Requisition')"><i class="fa fa-print"></i>
                            Print</button>
                        <button class="btn btn-success"
                            onclick="exportModalTableToExcel('tblRequisition','purchase_requisition')"><i
                                class="fa fa-file-excel"></i> Excel</button>
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Purchase Order Modal ============ --}}
        <div class="modal fade" id="modalOrder" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Purchase Order <small>{{ $projectDetails->projectCode }}</small></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @if ($purchaseOrders->isNotEmpty())
                            <table class="table table-bordered table-sm" id="tblOrder">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Order No</th>
                                        <th>Status</th>
                                        <th class="amount-cell">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseOrders as $row)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                            <td>{{ $row->order_no ?? ($row->invoice_no ?? 'N/A') }}</td>
                                            <td>{{ $row->status ?? '-' }}</td>
                                            <td class="amount-cell">
                                                {{ number_format($row->details->sum(fn($d) => ($d->qty ?? 0) * ($d->unit_price ?? 0)), 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state"><i class="fa fa-inbox"></i> No purchase order found.</div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" onclick="printModalTable('tblOrder','Purchase Order')"><i
                                class="fa fa-print"></i> Print</button>
                        <button class="btn btn-success" onclick="exportModalTableToExcel('tblOrder','purchase_order')"><i
                                class="fa fa-file-excel"></i> Excel</button>
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Purchase Voucher Modal ============ --}}
        <div class="modal fade" id="modalVoucher" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Purchase Voucher <small>{{ $projectDetails->projectCode }}</small></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @if ($purchaseVouchers->isNotEmpty())
                            <table class="table table-bordered table-sm" id="tblVoucher">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Voucher No</th>
                                        <th>Status</th>
                                        <th class="amount-cell">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseVouchers as $row)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                            <td>{{ $row->voucher_no ?? ($row->invoice_no ?? 'N/A') }}</td>
                                            <td>{{ $row->status ?? '-' }}</td>
                                            <td class="amount-cell">
                                                {{ number_format($row->details->sum(fn($d) => ($d->qty ?? 0) * ($d->unit_price ?? 0)), 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state"><i class="fa fa-inbox"></i> No purchase voucher found.</div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" onclick="printModalTable('tblVoucher','Purchase Voucher')"><i
                                class="fa fa-print"></i> Print</button>
                        <button class="btn btn-success"
                            onclick="exportModalTableToExcel('tblVoucher','purchase_voucher')"><i
                                class="fa fa-file-excel"></i> Excel</button>
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Good Receive Modal ============ --}}
        <div class="modal fade" id="modalGrn" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Good Receive <small>{{ $projectDetails->projectCode }}</small></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @if ($productgoodreceive->isNotEmpty())
                            <table class="table table-bordered table-sm" id="tblGrn">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice</th>
                                        <th>Items</th>
                                        <th class="amount-cell">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($productgoodreceive as $val)
                                        <tr>
                                            <td>{{ $val->date }}</td>
                                            <td>{{ $val->invoice_no }}</td>
                                            <td>{{ $val->details->count() }} item(s)</td>
                                            <td class="amount-cell">
                                                {{ number_format($val->details->sum(fn($d) => $d->qty * $d->unit_price), 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state"><i class="fa fa-inbox"></i> No good receive found.</div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" onclick="printModalTable('tblGrn','Good Receive')"><i
                                class="fa fa-print"></i> Print</button>
                        <button class="btn btn-success" onclick="exportModalTableToExcel('tblGrn','good_receive')"><i
                                class="fa fa-file-excel"></i> Excel</button>
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Transfer Modal ============ --}}
        <div class="modal fade" id="modalTransfer" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Transfer <small>{{ $projectDetails->projectCode }}</small></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @if ($projectTransfer->isNotEmpty())
                            <table class="table table-bordered table-sm" id="tblTransfer">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice</th>
                                        <th>Items</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projectTransfer as $val)
                                        <tr>
                                            <td>{{ $val->order_date }}</td>
                                            <td>{{ $val->invoice_no }}</td>
                                            <td>{{ $val->details->count() }} item(s)</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state"><i class="fa fa-inbox"></i> No transfer found.</div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" onclick="printModalTable('tblTransfer','Transfer')"><i
                                class="fa fa-print"></i> Print</button>
                        <button class="btn btn-success" onclick="exportModalTableToExcel('tblTransfer','transfer')"><i
                                class="fa fa-file-excel"></i> Excel</button>
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    @endif
@endsection

@section('scripts')
    <script>
        $(function() {
            $('.select2').select2({
                width: '100%'
            });

            // Guarded chart init — charts are currently disabled (canvas removed from view),
            // this prevents a console error if/when charts are re-enabled without matching canvas.
            var pieCanvas = document.getElementById('pieChart');
            var barCanvas = document.getElementById('myChart');

            @if (isset($projectDetails) && $projectDetails)
                if (pieCanvas) {
                    new Chart(pieCanvas.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Complete', 'Incomplete'],
                            datasets: [{
                                data: [{{ $summary['completePercentBar'] ?? 0 }},
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
                }
                if (barCanvas) {
                    var profit = {{ $summary['currentProfit'] ?? 0 }};
                    new Chart(barCanvas, {
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
                }
            @endif
        });

        // Opens a clean print-window containing only the modal's table
        function printModalTable(tableId, title) {
            var tableHtml = document.getElementById(tableId).outerHTML;
            var win = window.open('', '_blank', 'width=900,height=650');
            win.document.write(
                '<html><head><title>' + title + '</title>' +
                '<style>' +
                'body{font-family:Arial,sans-serif;padding:16px;}' +
                'table{width:100%;border-collapse:collapse;font-size:13px;}' +
                'th,td{border:1px solid #333;padding:6px 8px;}' +
                'th{background:#f1f1f1;text-align:left;}' +
                '.amount-cell{text-align:right;}' +
                '</style></head><body>' +
                '<h3>' + title + '</h3>' + tableHtml +
                '</body></html>'
            );
            win.document.close();
            win.focus();
            setTimeout(function() {
                win.print();
                win.close();
            }, 300);
        }

        // Exports the modal's table as an Excel-readable .xls file (no external library needed)
        function exportModalTableToExcel(tableId, filename) {
            var tableHtml = document.getElementById(tableId).outerHTML;
            var template =
                '<html xmlns:o="urn:schemas-microsoft-com:office:office" ' +
                'xmlns:x="urn:schemas-microsoft-com:office:excel" ' +
                'xmlns="http://www.w3.org/TR/REC-html40">' +
                '<head><meta charset="UTF-8"></head><body>' + tableHtml + '</body></html>';

            var blob = new Blob(['\ufeff', template], {
                type: 'application/vnd.ms-excel'
            });
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = (filename || 'export') + '_' + new Date().toISOString().slice(0, 10) + '.xls';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
    @include('backend.pages.reports.excel')
@endsection
