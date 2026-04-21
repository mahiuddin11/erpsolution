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

        .payment-row {
            transition: all 0.3s ease;
        }

        .payment-row:hover {
            background-color: #f8f9fa;
        }

        .bonus-type.active {
            font-weight: bold;
            color: green;
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

            .payment-row .row>div {
                margin-bottom: 0.5rem;
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

            #payment-section,
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
                        <li class="breadcrumb-item active">Pay Slip</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">

            <!-- LEFT COLUMN -->
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Payment Section -->
                <div class="card payslip-card" id="payment-section">
                    <div class="card-header bg-success text-white text-center py-3">
                        <h5 class="mb-0">Payment Information</h5>
                    </div>
                    <div class="card-body">

                        <div class="net-pay-box table-success p-3 text-center mb-3">
                            <p class="mb-1 small opacity-90">Net Payable Amount</p>
                            <h2 class="mb-0 font-weight-bold" id="net-payable-left">
                                {{ number_format($payslip->employee_payable_salary ?? 0, 2) }} Taka
                            </h2>
                        </div>

                        <form id="paymentForm" action="{{ route('hrm.salary.make.payment', $payslip->id) }}"
                            method="POST">
                            @csrf

                            <div id="bonus-form-container" style="display:none;"></div>

                            <div id="payment-container"></div>

                            <!-- Live Total -->
                            <div class="border rounded p-3 bg-light mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Total Paying Now:</strong>
                                    <span id="live-total" class="total-amount">0.00 Taka</span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success btn-block btn-lg">
                                <i class="fas fa-money-bill-wave"></i> Confirm & Pay
                            </button>
                        </form>

                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN - Salary Breakdown -->
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

                        <!-- Print Header -->
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
                                    <tr>
                                        <td>
                                            @php
                                                $bonusTypes = \App\Models\EmpPayBonus::BONUS_TYPES;
                                            @endphp

                                            Bonus Type<br>

                                            <small>
                                                @foreach ($bonusTypes as $key => $label)

                                
                                                    <a href="#" class="bonus-type" data-type="{{ $key }}">
                                                        {{ $label }}
                                                    </a>
                                                    @if (!$loop->last)
                                                        |
                                                    @endif
                                                @endforeach
                                            </small>
                                        </td>

                                        <td class="text-right">
                                            <div id="bonus-ui-container"></div>
                                        </td>
                                    </tr>


                                    <tr class="table-success">
                                        <td><strong>Total Earning</strong></td>
                                        <td class="text-right">
                                            <strong id="total-earning">
                                                {{ ($payslip->total_salary ?? 0) + ($payslip->festival_bonus ?? 0) + ($payslip->others_bonus ?? 0) }}
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
                                                — {{ $payslip->employee_absence_day ?? '' }} day
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
                                            <strong id="total-deduction">
                                                {{ ($payslip->absence_deduction ?? 0) + ($payslip->employee_deducton ?? 0) + ($payslip->loan_adjustment ?? 0) }}
                                            </strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Net Salary (Print Area) -->
                        <div class="d-none d-print-block mt-3 p-3 text-center border rounded">
                            <h5 class="mb-1">Net Payable Amount</h5>
                            <h3 class="font-weight-bold text-success mb-0" id="net-payable-print">
                                {{ number_format($payslip->employee_payable_salary ?? 0, 2) }} Taka
                            </h3>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            // ==================== Bonus Calculation ====================

            let grossSalary = parseFloat({{ $payslip->total_salary ?? 0 }});
            let totalDeduction = parseFloat(
                {{ ($payslip->absence_deduction ?? 0) + ($payslip->employee_deducton ?? 0) + ($payslip->loan_adjustment ?? 0) }}
            );

            let selectedBonus = {};

            // ADD BONUS ROW
            function addBonusRow(type, label) {

                if (selectedBonus[type]) return;

                let amount = 0;

                if (type === 'eid_ul_fitr' || type === 'eid_ul_adha') {
                    amount = grossSalary * 0.5;
                }

                selectedBonus[type] = amount;
              
                let uiRow = `
        <div class="mb-2 bonus-row" data-type="${type}">
            <div class="d-flex justify-content-between align-items-center">

                <span>${label}</span>

                <div class="d-flex align-items-center gap-2">

                    <input type="number" 
                        value="${amount.toFixed(2)}"
                        class="form-control form-control-sm bonus-amount"
                        style="width:100px;">

                    <button type="button" style="padding-right:10px;"  class="btn btn-danger btn-sm remove-bonus ">
                        ×
                    </button>

                </div>
            </div>
        </div>
    `;

                // ✅ FORM ROW (hidden submit data)
                let formRow = `
        <div class="bonus-row-form" data-type="${type}">
            <input type="hidden" name="bonus[${type}][type]" value="${type}">
            <input type="hidden" name="bonus[${type}][amount]" value="${amount.toFixed(2)}" class="bonus-hidden-amount">
        </div>
    `;

                $('#bonus-ui-container').append(uiRow);
                $('#bonus-form-container').append(formRow);

                calculateLiveTotal();
            }


            // REMOVE BONUS FIX
            $(document).on('click', '.remove-bonus', function() {

                let type = $(this).closest('.bonus-row').data('type');
                delete selectedBonus[type];
                // UI remove
                $(this).closest('.bonus-row').remove();


                $(`.bonus-row-form[data-type="${type}"]`).remove();
                $(`.bonus-type[data-type="${type}"]`).removeClass('active');
                calculateLiveTotal();
            });

            // ONUS INPUT SYNC
            $(document).on('input', '.bonus-amount', function() {

                let type = $(this).closest('.bonus-row').data('type');
                let value = parseFloat($(this).val()) || 0;

                // update hidden input
                $(`.bonus-row-form[data-type="${type}"] .bonus-hidden-amount`)
                    .val(value.toFixed(2));

                calculateLiveTotal();
            });


            // CLICK BONUS TYPE FIX
            $(document).on('click', '.bonus-type', function(e) {
                e.preventDefault();

                let type = $(this).data('type');
                let label = $(this).text();

                $(this).toggleClass('active');

                if ($(this).hasClass('active')) {
                    addBonusRow(type, label);
                } else {
                    $(`.bonus-row[data-type="${type}"]`).remove();
                    $(`.bonus-row-form[data-type="${type}"]`).remove();
                    delete selectedBonus[type];
                    calculateLiveTotal();
                }
            });

            $(document).ready(function() {
                // addBonusRow('performance', 'Performance Bonus');
                $('.bonus-type[data-type="performance"]').addClass('active');
            });

            // TOTAL CALCULATION
            function calculateLiveTotal() {

                let totalBonus = 0;

                $('.bonus-amount').each(function() {
                    totalBonus += parseFloat($(this).val()) || 0;
                });

                let totalEarning = grossSalary + totalBonus;
                let netPayable = totalEarning - totalDeduction;

                $('#total-earning').text(totalEarning.toFixed(2));
                $('#net-payable-left').text(netPayable.toFixed(2) + ' Taka');
                $('#net-payable-print').text(netPayable.toFixed(2) + ' Taka');
            }

            $(document).ready(function() {
                addBonusRow('performance', 'Performance Bonus');
            });


            // ==================== Payment Section JS ====================
            let rowCount = 0;

            function addPaymentRow() {
                rowCount++;

                let newRow = `
            <div class="payment-row border rounded p-3 mb-3" id="row-${rowCount}">
                <div class="row align-items-end">
                    <div class="col-12 col-sm-5 col-md-4 mb-2">
                        <label class="small mb-1">Payment Account</label>
                        <select name="payments[${rowCount}][account_id]" class="form-control form-control-sm select2" required>
                            <option value="">Select Account</option>
                            @foreach ($paymentAccounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->account_name }} {{ $acc->account_code ? '(' . $acc->account_code . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-5 col-md-4 mb-2">
                        <label class="small mb-1">Reference</label>
                        <input type="text" name="payments[${rowCount}][account_info]" 
                               class="form-control form-control-sm" placeholder="Account/Reference No.">
                    </div>
                    <div class="col-12 col-sm-10 col-md-3 mb-2">
                        <label class="small mb-1">Amount</label>
                        <input type="number" name="payments[${rowCount}][amount]" 
                               class="form-control form-control-sm amount-input" 
                               step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    <div class="col-12 col-sm-2 col-md-1 d-flex align-items-center mb-2">
                        <button type="button" class="btn btn-danger btn-sm remove-row mr-1">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button type="button" class="btn btn-success btn-sm add-row-btn">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>`;

                $('#payment-container').append(newRow);
                updateLiveTotal();
            }

            // Click on Plus button to add new row
            $(document).on('click', '.add-row-btn', function() {
                addPaymentRow();
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                if ($('.payment-row').length > 1) {
                    $(this).closest('.payment-row').remove();
                    updateLiveTotal();
                }
            });

            // Live Total Update
            $(document).on('input', '.amount-input', function() {
                updateLiveTotal();
            });

            function updateLiveTotal() {
                let total = 0;
                $('.amount-input').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#live-total').text(total.toFixed(2) + ' Taka');
            }

            // **Default 1 Row Show করার জন্য**
            addPaymentRow(); // ← এটি সবচেয়ে গুরুত্বপূর্ণ

            initSelect2();
        });

        function initSelect2() {
            $('.select2').select2({
                placeholder: "Select Account",
                allowClear: true,
                width: '100%'
            });
        }
    </script>

    <script>
        function printPayslip() {
            window.print();
        }
    </script>
@endsection
