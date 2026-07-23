@extends('backend.layouts.master')

@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .kpi-card {
            border-radius: 10px;
            padding: 16px 18px;
            color: #fff;
            position: relative;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .kpi-card .kpi-label {
            font-size: 12px;
            opacity: .85;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .kpi-card .kpi-value {
            font-size: 22px;
            font-weight: 700;
            margin-top: 4px;
        }

        .kpi-card.clickable {
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .12);
        }

        .kpi-card.clickable:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, .2);
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

        .section-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #9aa0a6;
            margin: 4px 0 8px;
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

        .project-header-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-left: 4px solid #17a2b8;
            border-radius: 6px;
            padding: 18px 20px;
        }

        .project-header-logo {
            width: 100%;
            max-width: 110px;
        }

        .project-code-chip {
            font-size: 12px;
            font-weight: 600;
            background: #eef6f8;
            color: #17a2b8;
            padding: 2px 8px;
            border-radius: 4px;
            letter-spacing: .3px;
        }

        .project-meta {
            font-size: 13px;
            color: #6c757d;
        }

        .project-meta i {
            width: 16px;
            color: #adb5bd;
        }

        .project-header-stats {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
        }

        .project-budget-box {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            line-height: 1.2;
        }

        .project-budget-box .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #adb5bd;
        }

        .project-budget-box .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #343a40;
        }

        .project-status-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .project-dates {
            font-size: 12px;
            color: #6c757d;
        }

        /* ============ Unified Transaction Feed (Demo 2 style) ============ */
        .txn-panel {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .txn-tabs {
            display: flex;
            gap: 2px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            overflow-x: auto;
            padding: 4px;
        }

        .txn-tab-btn {
            border: none;
            background: none;
            padding: 8px 14px;
            font-size: 13px;
            cursor: pointer;
            color: #6c757d;
            border-radius: 6px;
            white-space: nowrap;
        }

        .txn-tab-btn.active {
            background: #fff;
            color: #17a2b8;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
        }

        .txn-list {
            max-height: 480px;
            overflow-y: auto;
        }

        .txn-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-bottom: 1px solid #f1f1f1;
        }

        .txn-row:last-child {
            border-bottom: none;
        }

        .txn-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            position: relative;
        }

        .txn-cash-in {
            color: #00c853 !important;
            transform: rotate(35deg);
        }

        .txn-cash-out {
            color: #f44336 !important;
            transform: rotate(35deg);
        }


        .txn-row:hover .txn-cash-in {
            transform: rotate(40deg) scale(1.1);
        }

        .txn-row:hover .txn-cash-out {
            transform: rotate(40deg) scale(1.1);
        }

        .txn-main {
            flex: 1;
            min-width: 0;
        }

        .txn-desc {
            font-size: 13px;
            font-weight: 600;
            color: #343a40;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .txn-meta {
            font-size: 11px;
            color: #9aa0a6;
            margin-top: 2px;
        }

        .txn-amount {
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        .txn-amount.income {
            color: #177852;
        }

        .txn-status {
            font-size: 10px;
            color: #adb5bd;
        }

        .sidebar-sticky {
            position: sticky;
            top: 15px;
        }

        .sidebar-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 12px;
        }

        .sidebar-card .sc-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #9aa0a6;
            margin-bottom: 10px;
        }

        .sidebar-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .sidebar-row:last-child {
            margin-bottom: 0;
        }

        .sidebar-row .label {
            color: #6c757d;
        }

        .sidebar-row .value {
            font-weight: 600;
        }

        .sidebar-row.total {
            border-top: 1px solid #f1f1f1;
            padding-top: 6px;
            margin-top: 4px;
        }

        @media (max-width: 1024px) {
            .kpi-card .kpi-value {
                font-size: 18px;
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

            .project-header-stats,
            .project-budget-box,
            .project-status-row {
                align-items: flex-start;
                justify-content: flex-start;
            }

            .sidebar-sticky {
                position: static;
            }
        }

        @media (max-width: 480px) {
            .kpi-card .kpi-value {
                font-size: 16px;
            }

            .kpi-card .kpi-label {
                font-size: 11px;
            }
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .txn-list {
                max-height: none;
                overflow: visible;
            }
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
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
                        <div class="project-header-card mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-2 col-4 text-center">
                                    @if (isset($companyInfo->logo))
                                        <img class="project-header-logo"
                                            src="{{ asset('/backend/logo/' . $companyInfo->logo) }}" alt="">
                                    @endif
                                </div>

                                <div class="col-md-6 col-8">
                                    <div class="d-flex align-items-center flex-wrap mb-1">
                                        <h5 class="mb-0 mr-2">{{ $projectDetails->pname }}</h5>
                                        <span class="project-code-chip">{{ $projectDetails->projectCode }}</span>
                                    </div>

                                    <p class="mb-1 project-meta"><i class="fa fa-map-marker-alt"></i>Address :
                                        {{ $projectDetails->address }}</p>

                                    <p class="mb-1 project-meta">
                                        <i class="fa fa-building"></i>Company :
                                        {{ $projectDetails->client_name ?? 'N/A' }}
                                    </p>

                                    <p class="mb-0 project-meta">
                                        <i class="fa fa-user"></i> Project Manager: {{ $projectDetails->aname }}
                                    </p>
                                </div>

                                <div class="col-md-4 col-12 mt-3 mt-md-0">
                                    <div class="project-header-stats">
                                        <div class="project-budget-box">
                                            <span class="stat-label">Contract Value</span>
                                            <span class="stat-value">TK.
                                                {{ number_format($summary['budget'], 2) }}</span>
                                        </div>

                                        <div class="project-status-row">
                                            <span
                                                class="badge badge-{{ $projectDetails->condition == 'Complete' ? 'success' : 'warning' }} p-2">
                                                {{ $projectDetails->condition }}
                                            </span>
                                            <span class="project-dates">
                                                <i class="fa fa-calendar-alt"></i> {{ $projectDetails->start_date }}
                                                <i class="fa fa-arrow-right mx-1"></i>
                                                @if ($projectDetails->closing > $projectDetails->end_date)
                                                    <span class="text-danger">{{ $projectDetails->closing }}</span>
                                                @else
                                                    <span class="text-success">{{ $projectDetails->end_date }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- ============ Document / Process KPI cards ============ --}}
                        <div class="row mb-3 no-print">
                            <div class="col-lg-2 col-md-4 col-6 mb-2">
                                <div class="kpi-card kpi-progress clickable" data-toggle="modal"
                                    data-target="#modalRequisition">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label"><i class="fa fa-file-alt"></i> Requisition</div>
                                    <div class="kpi-value">{{ $summary['countRequisition'] }}</div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6 mb-2">
                                <div class="kpi-card kpi-progress clickable" data-toggle="modal" data-target="#modalOrder">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label"><i class="fa fa-file-invoice"></i> Order</div>
                                    <div class="kpi-value">{{ $summary['countOrder'] }}</div>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-6 mb-2">
                                <div class="kpi-card kpi-progress clickable" data-toggle="modal"
                                    data-target="#modalVoucher">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label"><i class="fa fa-receipt"></i> Voucher</div>
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
                                <div class="kpi-card kpi-income clickable" data-toggle="modal" data-target="#modalTransfer">
                                    <i class="fa fa-chevron-right kpi-hint"></i>
                                    <div class="kpi-label"><i class="fa fa-exchange-alt"></i> Transfer</div>
                                    <div class="kpi-value">{{ $summary['countTransfer'] }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- ============ MAIN: Transaction feed (left) + Sidebar summary (right) ============ --}}
                        <div class="row">
                            <div class="col-md-8">
                                <div class="txn-panel">
                                    <div class="txn-tabs no-print" id="txnTabs">
                                        <button class="txn-tab-btn active" data-tab="all">All</button>
                                        <button class="txn-tab-btn" data-tab="requisition">Requisition</button>
                                        <button class="txn-tab-btn" data-tab="order">Order</button>
                                        <button class="txn-tab-btn" data-tab="voucher">Purchase</button>
                                        <button class="txn-tab-btn" data-tab="grn">GRN</button>
                                        <button class="txn-tab-btn" data-tab="transfer">Transfer</button>
                                        <button class="txn-tab-btn" data-tab="expense">Expense</button>
                                        <button class="txn-tab-btn" data-tab="income">Income</button>
                                        @if ($projectMoney)
                                            <button class="txn-tab-btn" data-tab="money">Money</button>
                                        @endif
                                    </div>

                                    <div class="txn-list" id="txn-list">
                                        @forelse ($firstPageTransactions as $row)
                                            <div class="txn-row" data-type="{{ $row['type'] }}">
                                                <div class="txn-icon">
                                                    @switch($row['type'])
                                                        @case('requisition')
                                                            <i class="fa fa-file-alt text-primary"></i>
                                                        @break

                                                        @case('order')
                                                            <i class="fa fa-file-invoice text-info"></i>
                                                        @break

                                                        @case('voucher')
                                                            <i class="fa fa-file-invoice text-info"></i>
                                                        @break

                                                        @case('grn')
                                                            <i class="fa fa-box text-success"></i>
                                                        @break

                                                        @case('transfer')
                                                            <i class="fa fa-exchange-alt text-warning"></i>
                                                        @break

                                                        @case('income')
                                                        @case('money')
                                                            <i class="fa fa-arrow-down txn-cash-in"></i>
                                                        @break

                                                        @default
                                                            <i class="fa fa-arrow-up txn-cash-out"></i>
                                                    @endswitch
                                                </div>

                                                <div class="txn-main">
                                                    <div class="txn-desc">{{ $row['desc'] }}</div>
                                                    <div class="txn-meta">{{ $row['date'] }} &middot;
                                                        {{ $row['invoice'] }}</div>
                                                </div>

                                                <div class="text-right">
                                                    @if ($row['type'] === 'requisition')
                                                        <div class="txn-amount">{{ number_format($row['amount'], 0) }}
                                                            <small style="font-weight:400;">Qty</small>
                                                        </div>
                                                    @else
                                                        <div
                                                            class="txn-amount {{ in_array($row['type'], ['income', 'money']) ? 'income' : '' }}">
                                                            {{ in_array($row['type'], ['income', 'money']) ? '+' : '' }}{{ number_format($row['amount'], 2) }}
                                                        </div>
                                                    @endif
                                                    <div class="txn-status">{{ $row['status'] }}</div>
                                                </div>
                                                {{-- <div class="text-right">
                                                    <div
                                                        class="txn-amount {{ in_array($row['type'], ['income', 'money']) ? 'income' : '' }}">
                                                        {{ in_array($row['type'], ['income', 'money']) ? '+' : '' }}{{ number_format($row['amount'], 2) }}
                                                    </div>
                                                    <div class="txn-status">{{ $row['status'] }}</div>
                                                </div> --}}
                                            </div>
                                            @empty
                                                <div class="empty-state"><i class="fa fa-inbox"></i> No transactions
                                                    recorded for this project.</div>
                                            @endforelse
                                        </div>

                                        <div id="txn-loading" style="text-align:center; padding: 14px; display:none;">
                                            <i class="fa fa-spinner fa-spin"></i> Loading more...
                                        </div>
                                        <div id="txn-sentinel" style="height: 1px;"></div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="sidebar-sticky">
                                        <div class="sidebar-card">
                                            <div class="sc-title">Estimate</div>
                                            <div class="sidebar-row">
                                                <span class="label">Contract value</span>
                                                <span class="value">{{ number_format($summary['budget'], 2) }}</span>
                                            </div>
                                            <div class="sidebar-row">
                                                <span class="label">Estimated cost</span>
                                                <span class="value">{{ number_format($summary['estimateCost'], 2) }}</span>
                                            </div>
                                            <div class="sidebar-row total">
                                                <span class="label">Estimated profit</span>
                                                <span
                                                    class="value {{ $summary['estimateProfit'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($summary['estimateProfit'], 2) }}</span>
                                            </div>
                                        </div>

                                        <div class="sidebar-card">
                                            <div class="sc-title">Progress</div>
                                            <div class="d-flex justify-content-between align-items-baseline mb-1">
                                                <span style="font-size:12px; color:#6c757d;">Cost progress</span>
                                                <span
                                                    style="font-size:18px; font-weight:700;">{{ $summary['completePercent'] }}%</span>
                                            </div>
                                            <div class="progress" style="background:#f1f3f5;">
                                                <div class="progress-bar {{ $summary['isOverEstimate'] ? 'bg-danger' : 'bg-info' }}"
                                                    style="width: {{ $summary['completePercentBar'] }}%"></div>
                                            </div>
                                        </div>

                                        <div class="sidebar-card">
                                            <div class="sc-title">Recognized (POC)</div>
                                            <div class="sidebar-row">
                                                <span class="label">Revenue</span>
                                                <span
                                                    class="value">{{ number_format($summary['recognizedRevenue'], 2) }}</span>
                                            </div>
                                            <div class="sidebar-row">
                                                <span class="label">Profit</span>
                                                <span
                                                    class="value {{ $summary['recognizedProfit'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($summary['recognizedProfit'], 2) }}</span>
                                            </div>
                                        </div>

                                        <div class="sidebar-card">
                                            <div class="sc-title">Actual (cash)</div>
                                            <div class="sidebar-row">
                                                <span class="label">Income</span>
                                                <span class="value">{{ number_format($summary['actualIncome'], 2) }}</span>
                                            </div>
                                            <div class="sidebar-row">
                                                <span class="label">Cost</span>
                                                <span class="value">{{ number_format($summary['actualCost'], 2) }}</span>
                                            </div>
                                            <div class="sidebar-row total">
                                                <span class="label">Profit / loss</span>
                                                <span
                                                    class="value {{ $summary['actualProfit'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($summary['actualProfit'], 2) }}</span>
                                            </div>
                                        </div>

                                        @if ($summary['isOverEstimate'] || $summary['isExpectedLoss'])
                                            <div class="sidebar-card" style="background:#fdecec; border-color:#f5c2c2;">
                                                <div class="d-flex" style="gap:8px;">
                                                    <i class="fa fa-exclamation-triangle text-danger mt-1"></i>
                                                    <div style="font-size:12px; color:#a32d2d;">
                                                        @if ($summary['isOverEstimate'])
                                                            Over estimate by TK.
                                                            {{ number_format($summary['overEstimateAmount'], 2) }}.
                                                        @endif
                                                        @if ($summary['isExpectedLoss'])
                                                            Expected loss project — full loss recognized.
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if ($projectMoney)
                                <p class="mt-3"><b>Project Money (net):</b> TK. {{ number_format($projectMoney, 2) }}
                                </p>
                            @endif

                            {{-- ============ Full Summary Table (Print) ============
                            <table class="table table-bordered mt-4">
                                <tbody>
                                    <tr>
                                        <td colspan="2"><b><i class="fa fa-bullseye"></i> Full Project Summary</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>A. Estimate</b></td>
                                    </tr>
                                    <tr>
                                        <th>Contract Value</th>
                                        <th class="amount-cell">{{ number_format($summary['budget'], 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th>Estimated Cost</th>
                                        <th class="amount-cell">{{ number_format($summary['estimateCost'], 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th>Estimated Profit</th>
                                        <th class="amount-cell">{{ number_format($summary['estimateProfit'], 2) }}</th>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>B. Recognized (POC)</b></td>
                                    </tr>
                                    <tr>
                                        <th>Recognized Revenue</th>
                                        <th class="amount-cell">{{ number_format($summary['recognizedRevenue'], 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th>Recognized Profit</th>
                                        <th class="amount-cell">{{ number_format($summary['recognizedProfit'], 2) }}</th>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>C. Actual (Cash Basis)</b></td>
                                    </tr>
                                    <tr>
                                        <th>Direct Income</th>
                                        <th class="amount-cell">{{ number_format($summary['ttlexpdirinc'], 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th>Indirect Income</th>
                                        <th class="amount-cell">{{ number_format($summary['ttlexpindrinc'], 2) }}</th>
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
                                        <th>Product Consumption</th>
                                        <th class="amount-cell">{{ number_format($summary['productAmount'], 2) }}</th>
                                    </tr>
                                    <tr class="{{ $summary['actualProfit'] >= 0 ? 'table-success' : 'table-danger' }}">
                                        <th>Actual Profit / Loss (Income − Cost)</th>
                                        <th class="amount-cell">{{ number_format($summary['actualProfit'], 2) }}</th>
                                    </tr>
                                </tbody>
                            </table> --}}

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
                                Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} products. We believe
                                you will be satisfied by our services.
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
                            <h5 class="modal-title">Contract Value Breakdown
                                <small>{{ $projectDetails->projectCode }}</small>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered kpi-mini-table" id="tblBudget">
                                <tbody>
                                    <tr>
                                        <th>Contract Value (Budget)</th>
                                        <td class="amount-cell">{{ number_format($summary['budget'], 2) }}</td>
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

            {{-- ============ Estimate Cost / Profit Modal ============ --}}
            <div class="modal fade" id="modalEstimateProfit" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Estimate Breakdown <small>{{ $projectDetails->projectCode }}</small>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered kpi-mini-table" id="tblEstimateProfit">
                                <tbody>
                                    <tr>
                                        <th>Contract Value (Budget)</th>
                                        <td class="amount-cell">{{ number_format($summary['budget'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Estimated Cost (input)</th>
                                        <td class="amount-cell">{{ number_format($summary['estimateCost'], 2) }}</td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <th>Estimated Profit (Budget − Estimated Cost)</th>
                                        <td class="amount-cell">{{ number_format($summary['estimateProfit'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Recognized Profit (at {{ $summary['completePercentBar'] }}% progress)</th>
                                        <td class="amount-cell">{{ number_format($summary['recognizedProfit'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-default"
                                onclick="printModalTable('tblEstimateProfit','Estimate Breakdown')"><i
                                    class="fa fa-print"></i>
                                Print</button>
                            <button class="btn btn-success"
                                onclick="exportModalTableToExcel('tblEstimateProfit','estimate_breakdown')"><i
                                    class="fa fa-file-excel"></i> Excel</button>
                            <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ Actual Cost / POC Modal ============ --}}
            <div class="modal fade" id="modalActualCost" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Progress & Recognized Revenue
                                <small>{{ $projectDetails->projectCode }}</small>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered kpi-mini-table" id="tblActualCost">
                                <tbody>
                                    <tr>
                                        <th>Estimated Cost</th>
                                        <td class="amount-cell">{{ number_format($summary['estimateCost'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Actual Cost (spent so far)</th>
                                        <td class="amount-cell">{{ number_format($summary['actualCost'], 2) }}</td>
                                    </tr>
                                    <tr
                                        class="font-weight-bold {{ $summary['isOverEstimate'] ? 'table-danger' : 'table-success' }}">
                                        <th>{{ $summary['isOverEstimate'] ? 'Over Estimate By' : 'Remaining Estimate' }}
                                        </th>
                                        <td class="amount-cell">
                                            {{ number_format(abs($summary['estimateCost'] - $summary['actualCost']), 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Progress</th>
                                        <td class="amount-cell">{{ $summary['completePercent'] }}%</td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <th>Recognized Revenue (Budget × Progress)</th>
                                        <td class="amount-cell">
                                            {{ number_format($summary['recognizedRevenue'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-default"
                                onclick="printModalTable('tblActualCost','Progress and Recognized Revenue')"><i
                                    class="fa fa-print"></i> Print</button>
                            <button class="btn btn-success"
                                onclick="exportModalTableToExcel('tblActualCost','progress_recognized_revenue')"><i
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
                            <h5 class="modal-title">Actual Cost — All Transactions
                                <small>{{ $projectDetails->projectCode }}</small>
                            </h5>
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
                                            <td class="amount-cell">{{ number_format($summary['productAmount'], 2) }}
                                            </td>
                                        </tr>
                                        <tr class="font-weight-bold table-danger">
                                            <td colspan="3" class="text-center">Grand Total Actual Cost</td>
                                            <td class="amount-cell">{{ number_format($summary['actualCost'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            @else
                                <div class="empty-state"><i class="fa fa-inbox"></i> No expense recorded for this
                                    project.
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-default" onclick="printModalTable('tblCurrentExpense','Actual Cost')"><i
                                    class="fa fa-print"></i>
                                Print</button>
                            <button class="btn btn-success"
                                onclick="exportModalTableToExcel('tblCurrentExpense','actual_cost')"><i
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
                            <h5 class="modal-title">Actual Income — All Transactions
                                <small>{{ $projectDetails->projectCode }}</small>
                            </h5>
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
                                            <td colspan="3">Project Money (net)</td>
                                            <td class="amount-cell">{{ number_format($projectMoney, 2) }}</td>
                                        </tr>
                                        <tr class="font-weight-bold table-success">
                                            <td colspan="3" class="text-center">Grand Total Actual Income</td>
                                            <td class="amount-cell">{{ number_format($summary['actualIncome'], 2) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            @else
                                <div class="empty-state"><i class="fa fa-inbox"></i> No income recorded for this
                                    project.
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-default" onclick="printModalTable('tblCurrentIncome','Actual Income')"><i
                                    class="fa fa-print"></i> Print</button>
                            <button class="btn btn-success"
                                onclick="exportModalTableToExcel('tblCurrentIncome','actual_income')"><i
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
                            <h5 class="modal-title">
                                {{ $summary['actualProfit'] >= 0 ? 'Actual Profit' : 'Actual Loss' }} — Full Summary
                                <small>{{ $projectDetails->projectCode }}</small>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered kpi-mini-table" id="tblNetProfit">
                                <tbody>
                                    <tr>
                                        <td colspan="2"><b>A. Actual Income</b></td>
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
                                        <th>Project Money (net)</th>
                                        <td class="amount-cell">{{ number_format($projectMoney, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>B. Actual Cost</b></td>
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
                                        class="font-weight-bold {{ $summary['actualProfit'] >= 0 ? 'table-success' : 'table-danger' }}">
                                        <th>{{ $summary['actualProfit'] >= 0 ? 'Actual Profit' : 'Actual Loss' }} (A −
                                            B)</th>
                                        <td class="amount-cell">
                                            {{ number_format(abs($summary['actualProfit']), 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-default"
                                onclick="printModalTable('tblNetProfit','Actual Profit Loss Summary')"><i
                                    class="fa fa-print"></i> Print</button>
                            <button class="btn btn-success"
                                onclick="exportModalTableToExcel('tblNetProfit','actual_profit_loss')"><i
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
                            <h5 class="modal-title">Purchase Requisition
                                <small>{{ $projectDetails->projectCode }}</small>
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
                                <div class="empty-state"><i class="fa fa-inbox"></i> No purchase requisition found.
                                </div>
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
                            <h5 class="modal-title">Purchase Order <small>{{ $projectDetails->projectCode }}</small>
                            </h5>
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
                            <h5 class="modal-title">Purchase Voucher <small>{{ $projectDetails->projectCode }}</small>
                            </h5>
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
            });

            // ============ Unified Transaction Feed — lazy load / infinite scroll ============
            @if (isset($projectDetails) && $projectDetails)
                (function() {
                    const projectId = {{ $projectDetails->id }};
                    const listEl = document.getElementById('txn-list');
                    const loadingEl = document.getElementById('txn-loading');
                    const sentinel = document.getElementById('txn-sentinel');
                    const hasFirstPageMore = {{ $firstPageHasMore ? 'true' : 'false' }};

                    let currentPage = 1;
                    let hasMore = hasFirstPageMore;
                    let isLoading = false;
                    let activeType = 'all';

                    const iconMap = {
                        requisition: {
                            icon: 'fa-file-alt',
                            cls: 'text-primary'
                        },
                        order: {
                            icon: 'fa-file-invoice',
                            cls: 'text-info'
                        },
                        purchase: {
                            icon: 'fa-file-invoice',
                            cls: 'text-info'
                        },
                        voucher: {
                            icon: 'fa-file-invoice',
                            cls: 'text-info'
                        },
                        grn: {
                            icon: 'fa-box',
                            cls: 'text-success'
                        },
                        transfer: {
                            icon: 'fa-exchange-alt',
                            cls: 'text-warning'
                        },
                        income: {
                            icon: 'fa-arrow-down',
                            cls: 'txn-cash-in'
                        },
                        money: {
                            icon: 'fa-arrow-down',
                            cls: 'txn-cash-in'
                        },
                        expense: {
                            icon: 'fa-arrow-up',
                            cls: 'txn-cash-out'
                        },
                    };

                    function renderRow(row) {
                        const div = document.createElement('div');
                        div.className = 'txn-row';
                        div.dataset.type = row.type;

                        const isIncomeLike = row.type === 'income' || row.type === 'money';
                        const isRequisition = row.type === 'requisition';
                        const meta = iconMap[row.type] || {
                            icon: 'fa-file',
                            cls: ''
                        };

                        let amountHtml;
                        if (isRequisition) {
                            amountHtml =
                                `<div class="txn-amount">${Number(row.amount).toLocaleString(undefined, {maximumFractionDigits: 0})} <small style="font-weight:400;">QTY</small></div>`;
                        } else {
                            amountHtml =
                                `<div class="txn-amount ${isIncomeLike ? 'income' : ''}">${isIncomeLike ? '+' : ''}${Number(row.amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</div>`;
                        }

                        div.innerHTML = `
        <div class="txn-icon"><i class="fa ${meta.icon} ${meta.cls}"></i></div>
        <div class="txn-main">
            <div class="txn-desc">${row.desc}</div>
            <div class="txn-meta">${row.date} &middot; ${row.invoice}</div>
        </div>
        <div class="text-right">
            ${amountHtml}
            <div class="txn-status">${row.status}</div>
        </div>
    `;
                        return div;
                    }


                    const baseUrl = "{{ route('project.transactions.feed', ['id' => $projectDetails->id]) }}";


                    function loadNextPage() {
                        if (isLoading || !hasMore) return;
                        isLoading = true;
                        loadingEl.style.display = 'block';

                        const nextPage = currentPage + 1;

                        const url = `${baseUrl}?page=${nextPage}&type=${activeType}`;

                        fetch(url)
                            .then(res => res.json())
                            .then(json => {
                                json.data.forEach(row => listEl.appendChild(renderRow(row)));
                                hasMore = json.has_more;
                                currentPage = nextPage;
                                if (json.data.length === 0 && currentPage === 1) {
                                    listEl.innerHTML =
                                        '<div class="empty-state"><i class="fa fa-inbox"></i> No transactions found.</div>';
                                }
                            })
                            .catch(err => console.error('Transaction load failed:', err))
                            .finally(() => {
                                isLoading = false;
                                loadingEl.style.display = 'none';
                            });
                    }

                    const observer = new IntersectionObserver((entries) => {
                        if (entries[0].isIntersecting) loadNextPage();
                    }, {
                        rootMargin: '200px'
                    });
                    observer.observe(sentinel);

                    document.querySelectorAll('.txn-tab-btn').forEach(btn => {
                        btn.addEventListener('click', () => {
                            document.querySelectorAll('.txn-tab-btn').forEach(b => b.classList.remove(
                                'active'));
                            btn.classList.add('active');
                            activeType = btn.dataset.tab;
                            currentPage = 0;
                            hasMore = true;
                            listEl.innerHTML = '';
                            loadNextPage();
                        });
                    });
                })();
            @endif

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
