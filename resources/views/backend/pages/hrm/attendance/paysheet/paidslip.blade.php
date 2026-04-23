@extends('backend.layouts.master')

@section('title')
    Hrm - {{ $title }}
@endsection

@section('styles')
    <style>
        .bg-primary {
            background-color: #172a3e !important;
        }

        .bg-dark {
            background-color: #0c6a07 !important;
        }

        .payslip-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.09);
        }

        .net-pay-box {
            color: rgb(15, 31, 16);
            border-radius: 16px;
        }

        button.btn.btn-outline-secondary.btn-sm {
            margin-right: 10px;
        }

        .bg-success {
            background-color: #08422f !important;
        }

        .paid-badge {
            display: inline-block;
            background-color: #0c6a07;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            padding: 4px 14px;
            border-radius: 50px;
        }

        .payment-detail-row {
            border-bottom: 1px dashed #dee2e6;
            padding: 8px 0;
        }

        .payment-detail-row:last-child {
            border-bottom: none;
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0c6a07;
        }

        @media (max-width: 992px) {
            .employee-info {
                height: auto !important;
                min-height: unset !important;
            }

            .col-lg-5,
            .col-lg-7 {
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .net-pay-box h2 {
                font-size: 1.4rem;
            }
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Hrm - {{ $title }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('inventorySetup.adjust.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.employee.index') }}">Hrm</a></li>
                        @endif
                        <li class="breadcrumb-item active">Paid Slip</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">

            <!-- LEFT COLUMN: Employee Info + Payment Summary -->
            <div class="col-lg-6 col-md-12 col-12">

                <!-- Employee Information -->
                <div class="card payslip-card employee-info mb-4">
                    <div class="card-header bg-primary text-white text-center py-3 rounded-top">
                        <h5 class="mb-0">Employee Information</h5>
                    </div>
                    <div class="card-body pt-4">
                        <div class="d-flex flex-column flex-md-row align-items-center text-center text-md-left">
                            <!-- Photo -->
                            <div class="mb-3 mb-md-0 mr-md-4 flex-shrink-0">
                                @php $photo = $payslip->employee->photo ?? null; @endphp
                                <img src="{{ $photo
                                    ? asset('storage/employee/' . $photo)
                                    : 'https://ui-avatars.com/api/?name=' .
                                        urlencode($payslip->employee->name ?? 'Employee') .
                                        '&background=172a3e&color=fff' }}"
                                    class="rounded-circle shadow-lg"
                                    style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #fff;"
                                    alt="Employee Photo">
                            </div>

                            <!-- Details -->
                            <div class="flex-grow-1 w-100">
                                <h4 class="font-weight-bold mb-1">{{ $payslip->employee->name ?? 'N/A' }}</h4>
                                <p class="text-muted mb-2">ID:
                                    <strong>{{ $payslip->employee->employee_id ?? ($payslip->employee->id ?? 'N/A') }}</strong>
                                </p>

                                <div class="row small">
                                    <div class="col-6 mb-1"><strong>Designation</strong></div>
                                    <div class="col-6 mb-1 text-right">
                                        {{ optional($payslip->employee->designation)->name ?? 'N/A' }}</div>

                                    <div class="col-6 mb-1"><strong>Department</strong></div>
                                    <div class="col-6 mb-1 text-right">
                                        {{ optional($payslip->employee->department)->name ?? 'N/A' }}</div>

                                    <div class="col-6 mb-1"><strong>Month</strong></div>
                                    <div class="col-6 mb-1 text-right font-weight-bold">
                                        {{ \Carbon\Carbon::parse($payslip->date ?? now())->format('F Y') }}
                                    </div>

                                    <div class="col-6 mb-1"><strong>Status</strong></div>
                                    <div class="col-6 mb-1 text-right">
                                        <span class="paid-badge">
                                            {{ ucfirst($payslip->status ?? 'paid') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="card payslip-card mb-4">
                    <div class="card-header bg-success text-white text-center py-3">
                        <h5 class="mb-0">Payment Summary</h5>
                    </div>
                    <div class="card-body">

                        <div class="net-pay-box table-success p-3 text-center mb-3">
                            <p class="mb-1 small opacity-90">Total Paid Amount</p>
                            <h2 class="mb-0 font-weight-bold total-amount">
                                {{ number_format($payslip->empPayDetails->amount ?? ($payslip->employee_payable_salary ?? 0), 2) }}
                                Taka
                            </h2>
                        </div>

                        @php $payDetails = $payslip->empPayDetails ?? null; @endphp

                        {{-- Payment Method Breakdown --}}
                        @if ($transactions->count())
                            <h6 class="font-weight-bold mb-2 text-muted">
                                <i class="fas fa-university mr-1"></i> Payment Methods
                            </h6>

                            @foreach ($transactions as $trx)
                                <div
                                    class="payment-detail-row d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div>
                                        <span class="font-weight-bold small">
                                            {{ $trx->account->account_name ?? 'N/A' }}
                                        </span>
                                        @if ($trx->remark)
                                            <br>
                                            <small class="text-muted">
                                                {{ \Illuminate\Support\Str::before($trx->remark, '#_') }}
                                            </small>
                                        @endif
                                    </div>
                                    <span class="font-weight-bold text-success">
                                        {{ number_format($trx->credit, 2) }} Tk
                                    </span>
                                </div>
                            @endforeach
                        @endif

                        <div class="mt-3 d-flex justify-content-end gap-2">
                            <button onclick="printPayslip()" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-print mr-1"></i> Print
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-sm ">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>

                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Salary Breakdown -->
            <div class="col-lg-6 col-md-12 col-12">

                <div class="card payslip-card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">
                            Salary Breakdown &bull; {{ \Carbon\Carbon::parse($payslip->date ?? now())->format('F Y') }}
                        </h5>
                        <button type="button" onclick="printPayslip()" class="btn btn-light btn-sm">
                            <i class="fas fa-print"></i> Print Payslip
                        </button>
                    </div>

                    <div class="card-body">

                        <!-- Earnings -->
                        <h6 class="text-success mb-3"><i class="fas fa-arrow-up"></i> Earnings</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>Gross Salary</td>
                                        <td class="text-right">{{ number_format($payslip->total_salary ?? 0, 2) }}</td>
                                    </tr>

                                    @php
                                        $bonusAmount = ($payslip->festival_bonus ?? 0) + ($payslip->others_bonus ?? 0);
                                    @endphp

                                    @if ($bonusAmount > 0)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong>Bonus</strong>
                                                    @if (isset($empBonus) && $empBonus->count())
                                                        <div class="mt-1 d-flex flex-wrap">
                                                            @foreach ($empBonus as $bonus)
                                                                @php
                                                                    $color = match ($bonus->bonus_type) {
                                                                        'performance' => 'badge-success',
                                                                        'eid_ul_fitr', 'eid_ul_adha' => 'badge-info',
                                                                        default => 'badge-secondary',
                                                                    };
                                                                @endphp
                                                                <span
                                                                    class="badge {{ $color }} mr-1 mb-1 px-2 py-1">
                                                                    {{ \App\Models\EmpPayBonus::BONUS_TYPES[$bonus->bonus_type] ?? ucfirst($bonus->bonus_type) }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-right align-middle">
                                                <strong>{{ number_format($bonusAmount, 2) }}</strong>
                                            </td>
                                        </tr>
                                    @endif

                                    <tr class="table-success">
                                        <td><strong>Total Earning</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ number_format(($payslip->total_salary ?? 0) + ($payslip->festival_bonus ?? 0) + ($payslip->others_bonus ?? 0), 2) }}
                                            </strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Deductions -->
                        <h6 class="text-danger mt-4 mb-3"><i class="fas fa-arrow-down"></i> Deductions</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>Absence Deduction
                                            @if (!empty($payslip->employee_absence_day))
                                                — {{ $payslip->employee_absence_day }} day
                                            @endif
                                        </td>
                                        <td class="text-right">{{ number_format($payslip->absence_deduction ?? 0, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Late Deduction
                                            @if (!empty($payslip->employee_late))
                                                — {{ $payslip->employee_late }} days Late
                                            @endif
                                        </td>
                                        <td class="text-right">{{ number_format($payslip->employee_deducton ?? 0, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Loan Adjustment</td>
                                        <td class="text-right">{{ number_format($payslip->loan_adjustment ?? 0, 2) }}</td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td><strong>Total Deduction</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ number_format(($payslip->absence_deduction ?? 0) + ($payslip->employee_deducton ?? 0) + ($payslip->loan_adjustment ?? 0), 2) }}
                                            </strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Net Salary -->
                        <div class="mt-3 p-3 text-center border rounded table-success">
                            <h5 class="mb-1">Paid Amount</h5>
                            <h3 class="font-weight-bold text-success mb-0">
                                {{ number_format($payslip->empPayDetails->amount ?? ($payslip->employee_payable_salary ?? 0), 2) }}
                                Taka
                            </h3>
                        </div>

                        <!-- Invoice Reference -->
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                Invoice: <strong>PAY-{{ str_pad($payslip->id, 4, '0', STR_PAD_LEFT) }}</strong>
                                &bull; Paid on:
                                <strong>{{ \Carbon\Carbon::parse($payslip->updated_at)->format('d M Y, h:i A') }}</strong>
                            </small>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function printPayslip() {

            @php
                $bonusAmountJs = ($payslip->festival_bonus ?? 0) + ($payslip->others_bonus ?? 0);
                $totalEarning = ($payslip->total_salary ?? 0) + $bonusAmountJs;
                $totalDeduction = ($payslip->absence_deduction ?? 0) + ($payslip->employee_deducton ?? 0) + ($payslip->loan_adjustment ?? 0);
                $netPaid = $payslip->empPayDetails->amount ?? ($payslip->employee_payable_salary ?? 0);

                // Bonus badges HTML
                $bonusBadgesHtml = '';
                if ($bonusAmountJs > 0 && isset($empBonus) && $empBonus->count()) {
                    foreach ($empBonus as $bonus) {
                        $label = \App\Models\EmpPayBonus::BONUS_TYPES[$bonus->bonus_type] ?? ucfirst($bonus->bonus_type);
                        $bonusBadgesHtml .= '<span style="background:#d4edda;color:#0c6a07;font-size:9px;padding:1px 6px;border-radius:50px;margin-left:4px;">' . e($label) . '</span>';
                    }
                }

                // Transaction rows
                $trxRowsHtml = '';
                foreach ($transactions as $trx) {
                    $accName = e($trx->account->account_name ?? 'N/A');
                    $remark = e(\Illuminate\Support\Str::before($trx->remark ?? '', '#_'));
                    $credit = number_format($trx->credit, 2);
                    $trxRowsHtml .= "
                <div style='display:flex;justify-content:space-between;padding:7px 14px;border-bottom:1px dashed #eee;font-size:12px;'>
                    <span style='color:#172a3e;font-weight:600;'>{$accName}</span>
                    <span style='color:#888;'>{$remark}</span>
                    <span style='font-weight:700;color:#0c6a07;'>{$credit} Tk</span>
                </div>";
                }
            @endphp

            var bonusRow = '';
            @if ($bonusAmountJs > 0)
                bonusRow = `
        <tr>
            <td style="padding:7px 12px;border-bottom:1px dashed #ddd;">
                Bonus {!! addslashes($bonusBadgesHtml) !!}
            </td>
            <td style="padding:7px 12px;border-bottom:1px dashed #ddd;text-align:right;font-weight:600;">
                {{ number_format($bonusAmountJs, 2) }}
            </td>
        </tr>`;
            @endif

            var paymentSection = '';
            @if ($transactions->count())
                paymentSection = `
        <div style="font-size:13px;font-weight:700;color:#172a3e;margin:16px 0 6px;display:flex;align-items:center;gap:6px;">
            <span style="width:9px;height:9px;border-radius:50%;background:#172a3e;display:inline-block;"></span>
            Payment Information
        </div>
        <div style="border:1px solid #ddd;border-radius:5px;overflow:hidden;">
            <div style="background:#172a3e;color:#fff;padding:6px 14px;font-size:11px;font-weight:700;display:flex;justify-content:space-between;">
                <span>Account / Method</span><span>Remark</span><span>Paid Amount</span>
            </div>
            {!! addslashes($trxRowsHtml) !!}
        </div>
        <div style="text-align:right;font-size:12px;font-weight:700;color:#172a3e;padding:5px 2px 0;">
            Total Paid: {{ number_format($netPaid, 2) }} Tk
        </div>`;
            @endif

            var printContent = `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payslip - {{ $payslip->employee->name ?? 'Employee' }} - {{ \Carbon\Carbon::parse($payslip->date ?? now())->format('F Y') }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 12px; color: #222; background: #fff; }

    /* ---- LETTERHEAD ---- */
    .lh { display: flex; align-items: center; justify-content: space-between; padding: 18px 28px 14px; border-bottom: 3px solid #172a3e; }
    .lh-logo-box { width: 52px; height: 52px; background: #172a3e; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; }
    .lh-logo-box img { width: 44px; height: 44px; object-fit: contain; }
    .lh-logo-box span { color: #fff; font-size: 20px; font-weight: 900; }
    .lh-logo-info { margin-left: 10px; }
    .lh-logo-info .co-name { font-weight: 700; color: #172a3e; font-size: 13px; }
    .lh-logo-info .co-sub { font-size: 10px; color: #666; line-height: 1.6; }
    .lh-center { text-align: center; flex: 1; padding: 0 16px; }
    .lh-center h2 { font-size: 18px; font-weight: 800; color: #172a3e; margin-bottom: 2px; }
    .lh-center p { font-size: 11px; color: #0c6a07; font-style: italic; font-weight: 600; }
    .lh-right { text-align: right; font-size: 11px; color: #555; min-width: 145px; }
    .lh-right .cert-title { font-size: 12px; font-weight: 700; color: #172a3e; margin-bottom: 4px; }
    .lh-right div { line-height: 1.8; }

    /* ---- TITLE BAR ---- */
    .title-bar { background: #172a3e; color: #fff; text-align: center; padding: 8px 0; font-size: 13px; font-weight: 700; letter-spacing: 1.5px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    /* ---- EMP STRIP ---- */
    .emp-strip { display: flex; justify-content: space-between; align-items: flex-start; background: #f6f9f6; border-bottom: 1px solid #ddd; padding: 11px 28px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .ei { display: flex; flex-direction: column; gap: 2px; }
    .ei .lbl { color: #888; font-size: 10px; }
    .ei .val { font-size: 12px; font-weight: 700; color: #172a3e; }
    .paid-badge { display: inline-block; background: #0c6a07; color: #fff; font-size: 10px; padding: 2px 10px; border-radius: 50px; font-weight: 700; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    /* ---- BODY ---- */
    .body { padding: 16px 28px; }
    .sec-head { font-size: 13px; font-weight: 700; margin: 14px 0 7px; display: flex; align-items: center; gap: 7px; }
    .sec-head .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; flex-shrink: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    /* ---- TABLES ---- */
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    thead tr { background: #172a3e; color: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    thead th { padding: 8px 12px; font-weight: 600; font-size: 11px; }
    thead th:last-child { text-align: right; }
    tbody td { padding: 7px 12px; border-bottom: 1px dashed #e0e0e0; }
    tbody td:last-child { text-align: right; font-weight: 600; }
    .row-earn td { background: #edf7ed; font-weight: 700; color: #0c6a07; border-bottom: none; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .row-ded td { background: #fdf0f0; font-weight: 700; color: #b92020; border-bottom: none; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    /* ---- NET BOX ---- */
    .net-box { border: 2px solid #172a3e; border-radius: 7px; overflow: hidden; margin-top: 14px; }
    .net-head { background: #172a3e; color: #fff; padding: 7px 16px; font-size: 12px; font-weight: 700; display: flex; justify-content: space-between; align-items: center; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .net-body { background: #f6fff6; display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .net-amount { font-size: 20px; font-weight: 900; color: #0c6a07; }

    /* ---- FOOTER ---- */
    .print-footer { border-top: 2px solid #172a3e; margin: 18px 28px 20px; padding-top: 14px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .footer-grid { display: flex; justify-content: space-between; }
    .footer-col { text-align: center; width: 31%; }
    .footer-col .sig-line { width: 90%; height: 1px; background: #333; margin: 0 auto 5px; display: block; }
    .footer-col .ftitle { font-size: 12px; font-weight: 700; color: #172a3e; }
    .footer-col .fsub { font-size: 10px; color: #666; line-height: 1.7; }
    .invoice-ref { text-align: center; font-size: 10px; color: #aaa; margin-top: 10px; }

    @media print {
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        table { page-break-inside: avoid; }
        .print-footer { page-break-inside: avoid; }
    }
</style>
</head>
<body>

    <!-- LETTERHEAD -->
    <div class="lh">
        <div style="display:flex;align-items:center;">
            <div class="lh-logo-box">
                {{-- Replace <span>AB</span> with <img src="..."> for actual logo --}}
                <span>AB</span>
            </div>
            <div class="lh-logo-info">
                <div class="co-name">Water Technology DB Ltd.</div>
                <div class="co-sub">Adress : Mirpur DOHS, </div>
                <div class="co-sub"> Email :info@wtbl.com.bd</div>
                <div class="co-sub">Contact : +8801713565696</div>
                
            </div>
        </div>

        <div class="lh-center">
            <h2>Water Technology BD Ltd</h2>
            <p>"Value adding is our business"</p>
        </div>

        <div class="lh-right">
            <div class="cert-title">ISO Certification</div>
          
            <div>Month: <strong>{{ \Carbon\Carbon::parse($payslip->date ?? now())->format('F Y') }}</strong></div>
    
            <div>Status: <strong style="color:#0c6a07;">&#10003; Paid</strong></div>
        </div>
    </div>

    <!-- TITLE BAR -->
    <div class="title-bar">EMPLOYEE PAYSLIP</div>

    <!-- EMPLOYEE STRIP -->
    <div class="emp-strip">
        <div class="ei">
            <span class="lbl">Employee Name</span>
            <span class="val">{{ $payslip->employee->name ?? 'N/A' }}</span>
        </div>
        <div class="ei">
            <span class="lbl">Employee ID</span>
            <span class="val">{{ $payslip->employee->employee_id ?? ($payslip->employee->id ?? 'N/A') }}</span>
        </div>
        <div class="ei">
            <span class="lbl">Designation</span>
            <span class="val">{{ optional($payslip->employee->designation)->name ?? 'N/A' }}</span>
        </div>
        <div class="ei">
            <span class="lbl">Department</span>
            <span class="val">{{ optional($payslip->employee->department)->name ?? 'N/A' }}</span>
        </div>
        <div class="ei" style="align-items:flex-end;">
            <span class="lbl">Status</span>
            <span class="paid-badge">{{ ucfirst($payslip->status ?? 'Paid') }}</span>
        </div>
    </div>

    <!-- BODY -->
    <div class="body">

        <!-- EARNINGS -->
        <div class="sec-head" style="color:#0c6a07;">
            <span class="dot" style="background:#0c6a07;"></span> Earnings
        </div>
        <table>
            <thead>
                <tr>
                    <th style="text-align:left;">Description</th>
                    <th>Amount (Tk)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Gross Salary</td>
                    <td>{{ number_format($payslip->total_salary ?? 0, 2) }}</td>
                </tr>
                \${bonusRow}
                <tr class="row-earn">
                    <td><strong>Total Earnings</strong></td>
                    <td><strong>{{ number_format($totalEarning, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- DEDUCTIONS -->
        <div class="sec-head" style="color:#b92020;margin-top:16px;">
            <span class="dot" style="background:#b92020;"></span> Deductions
        </div>
        <table>
            <thead>
                <tr>
                    <th style="text-align:left;">Description</th>
                    <th>Amount (Tk)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Absence Deduction{{ !empty($payslip->employee_absence_day) ? ' — ' . $payslip->employee_absence_day . ' day' : '' }}</td>
                    <td>{{ number_format($payslip->absence_deduction ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Late Deduction{{ !empty($payslip->employee_late) ? ' — ' . $payslip->employee_late . ' days late' : '' }}</td>
                    <td>{{ number_format($payslip->employee_deducton ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Loan Adjustment</td>
                    <td>{{ number_format($payslip->loan_adjustment ?? 0, 2) }}</td>
                </tr>
                <tr class="row-ded">
                    <td><strong>Total Deductions</strong></td>
                    <td><strong>{{ number_format($totalDeduction, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- NET PAYABLE BOX -->
        <div class="net-box">
            <div class="net-head">
                <span>Net Payable Amount</span>
                <span style="font-weight:400;font-size:10px;opacity:0.85;">Total Earnings &minus; Total Deductions</span>
            </div>
            <div class="net-body">
                <div style="font-size:11px;color:#555;">Amount Paid</div>
                <div class="net-amount">{{ number_format($netPaid, 2) }} Tk</div>
            </div>
        </div>

        <!-- PAYMENT INFORMATION -->
        \${paymentSection}

    </div>

    <!-- FOOTER -->
    <div class="print-footer">
        <div class="footer-grid">
            <div class="footer-col">
                <span class="sig-line"></span>
                <div class="ftitle">Employee Signature</div>
                <div class="fsub">{{ $payslip->employee->name ?? '' }}</div>
                <div class="fsub">Date: ___________</div>
            </div>
            <div class="footer-col">
                <span class="sig-line"></span>
                <div class="ftitle">HR Department</div>
                <div class="fsub">Authorized Signature</div>
                <div class="fsub">Date: ___________</div>
            </div>
            <div class="footer-col">
                <span class="sig-line"></span>
                <div class="ftitle">Managing Director</div>
                <div class="fsub">Water Technology DB Ltd.</div>
                <div class="fsub">Date: ___________</div>
            </div>
        </div>
        <div class="invoice-ref">
            Invoice: PAY-{{ str_pad($payslip->id, 4, '0', STR_PAD_LEFT) }}
            &bull; Paid on: {{ \Carbon\Carbon::parse($payslip->updated_at)->format('d M Y, h:i A') }}
            &bull; This is a system generated payslip
        </div>
    </div>

</body>
</html>`;

            var printWin = window.open('', '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes');
            printWin.document.open();
            printWin.document.write(printContent);
            printWin.document.close();

            printWin.onload = function() {
                printWin.focus();
                printWin.print();
            };
        }
    </script>
@endsection
