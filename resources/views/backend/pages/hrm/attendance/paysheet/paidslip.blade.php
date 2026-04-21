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

        /* ========== PRINT STYLES ========== */
        @media print {
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                padding: 20px;
            }

            .btn-print,
            .no-print {
                display: none !important;
            }

            .payslip-card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
                border-radius: 8px !important;
            }

            .card-header {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            table {
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
            <div class="col-lg-6 col-md-12 col-12 no-print">

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
                                {{ number_format($payslip->empPayDetails->amount ?? $payslip->employee_payable_salary ?? 0, 2) }} Taka
                            </h2>
                        </div>

                        @php
                            $payDetails = $payslip->empPayDetails ?? null;
                            $transactions = $payslip->accountTransactions()
                                ->where('type', 'credit')
                                ->where('table_name', 'monthly_payable_salaries')
                                ->get();
                        @endphp

                        {{-- Payment Method Breakdown --}}
                        @if($transactions && $transactions->count() > 0)
                            <h6 class="font-weight-bold mb-2 text-muted"><i class="fas fa-university mr-1"></i> Payment Methods</h6>
                            @foreach($transactions as $trx)
                                <div class="payment-detail-row d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="font-weight-bold small">{{ optional($trx->account)->account_name ?? 'Account' }}</span>
                                        @if($trx->remark)
                                            <br>
                                            <small class="text-muted">
                                                {{ Str::before($trx->remark, '#_') }}
                                            </small>
                                        @endif
                                    </div>
                                    <span class="font-weight-bold text-success">
                                        {{ number_format($trx->credit, 2) }} Tk
                                    </span>
                                </div>
                            @endforeach
                        @endif

                        {{-- Bonus Info --}}
                        @if($payDetails && $payDetails->total_bonus > 0)
                            <div class="payment-detail-row d-flex justify-content-between align-items-center mt-2">
                                <span class="font-weight-bold small text-warning"><i class="fas fa-gift mr-1"></i> Bonus Paid</span>
                                <span class="font-weight-bold text-warning">
                                    {{ number_format($payDetails->total_bonus, 2) }} Tk
                                </span>
                            </div>
                        @endif

                        {{-- Loan Adjustment Info --}}
                        @if($payDetails && $payDetails->lone > 0)
                            <div class="payment-detail-row d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold small text-danger"><i class="fas fa-hand-holding-usd mr-1"></i> Loan Adjusted</span>
                                <span class="font-weight-bold text-danger">
                                    {{ number_format($payDetails->lone, 2) }} Tk
                                </span>
                            </div>
                        @endif

                        <div class="mt-3 d-flex justify-content-end gap-2">
                            <button onclick="printPayslip()" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-print mr-1"></i> Print
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>

                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Salary Breakdown (Printable) -->
            <div class="col-lg-6 col-md-12 col-12" id="print-area">

                <div class="card payslip-card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">
                            Salary Breakdown &bull; {{ \Carbon\Carbon::parse($payslip->date ?? now())->format('F Y') }}
                        </h5>
                        <button type="button" onclick="printPayslip()" class="btn btn-light btn-sm btn-print no-print">
                            <i class="fas fa-print"></i> Print Payslip
                        </button>
                    </div>

                    <div class="card-body">

                        <!-- Print Header (visible only on print) -->
                        <div class="d-none d-print-block mb-4 text-center border-bottom pb-3">
                            <h4 class="font-weight-bold mb-1">{{ $payslip->employee->name ?? 'N/A' }}</h4>
                            <p class="mb-1">
                                {{ optional($payslip->employee->designation)->name ?? '' }}
                                @if (optional($payslip->employee->department)->name)
                                    — {{ $payslip->employee->department->name }}
                                @endif
                            </p>
                            <p class="mb-0 text-muted">
                                Pay Period: {{ \Carbon\Carbon::parse($payslip->date ?? now())->format('F Y') }}
                            </p>
                        </div>

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

                                    @if($bonusAmount > 0)
                                        <tr>
                                            <td>
                                                Bonus
                                                @if($payslip->empPayDetails && $payslip->empPayDetails->empBonus)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ \App\Models\EmpPayBonus::BONUS_TYPES[$payslip->empPayDetails->empBonus->bonus_type] ?? $payslip->empPayDetails->empBonus->bonus_type }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td class="text-right">{{ number_format($bonusAmount, 2) }}</td>
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
                                        <td class="text-right">{{ number_format($payslip->absence_deduction ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Late Deduction
                                            @if (!empty($payslip->employee_late))
                                                — {{ $payslip->employee_late }} days Late
                                            @endif
                                        </td>
                                        <td class="text-right">{{ number_format($payslip->employee_deducton ?? 0, 2) }}</td>
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

                        <!-- Net Salary (Visible always + print) -->
                        <div class="mt-3 p-3 text-center border rounded table-success">
                            <h5 class="mb-1">Paid Amount</h5>
                            <h3 class="font-weight-bold text-success mb-0">
                                {{ number_format($payslip->empPayDetails->amount ?? $payslip->employee_payable_salary ?? 0, 2) }} Taka
                            </h3>
                        </div>

                        <!-- Payment Invoice Reference -->
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                Invoice: <strong>PAY-{{ str_pad($payslip->id, 4, '0', STR_PAD_LEFT) }}</strong>
                                &bull; Paid on: <strong>{{ \Carbon\Carbon::parse($payslip->updated_at)->format('d M Y, h:i A') }}</strong>
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
            window.print();
        }
    </script>
@endsection