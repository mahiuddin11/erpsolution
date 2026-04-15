@extends('backend.layouts.master')

@section('title')
    Hrm - {{ $title }}
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">

            <div class="card card-default">

                <div class="card-header">
                    <h5 class="mb-0">Salary Review & Edit</h5>
                </div>

                <div class="card-body">

                    <form action="{{ route('hrm.paysheet.update', $MonthlyPaySheet->id) }}" method="POST">
                        @csrf

                        <div class="row g-3">

                            <div class="col-md-3 col-6">
                                <label>Name</label>
                                <input type="text" class="form-control" value="{{ $MonthlyPaySheet->name }}" readonly>
                            </div>

                            <div class="col-md-3 col-6">
                                <label>Gross Salary</label>
                                <input type="number" step="any" id="gross_salary" name="total_salary"
                                    class="form-control" value="{{ $MonthlyPaySheet->total_salary }}">
                            </div>

                            <div class="col-md-3 col-6">
                                <label>Daily Rate</label>
                                <input type="number" step="any" id="daily_rate" name="daily_rate" class="form-control"
                                    value="{{ $MonthlyPaySheet->daily_rate }}">
                            </div>

                            <div class="col-md-3 col-6">
                                <label>Presence</label>
                                <input type="number" step="any" id="presence" name="employee_presence_day"
                                    class="form-control" value="{{ $MonthlyPaySheet->employee_presence_day }}">
                            </div>

                            <div class="col-md-3 col-6">
                                <label>Absence (AB)</label>
                                <input type="number" step="any" id="absence" name="employee_absence_day"
                                    class="form-control" value="{{ $MonthlyPaySheet->employee_absence_day }}">
                            </div>

                            <div class="col-md-3 col-6">
                                <label>Absent Deduction</label>
                                <input type="number" step="any" id="absence_deduction" name="absence_deduction"
                                    class="form-control" value="{{ $MonthlyPaySheet->absence_deduction }}">
                            </div>

                            <div class="col-md-3 col-6">
                                <label>Late (LT)</label>
                                <input type="number" step="any" id="late" name="employee_late"
                                    class="form-control" value="{{ $MonthlyPaySheet->employee_late }}">
                            </div>

                            <div class="col-md-3 col-6">
                                <label>Late Deduction</label>
                                <input type="number" step="any" id="late_deduction" name="employee_deducton"
                                    class="form-control" value="{{ $MonthlyPaySheet->employee_deducton }}">
                            </div>

                            <div class="col-md-3 col-6">
                                <label>Paid Leave</label>
                                <input type="number" step="any" id="paid_leave" name="employee_paid_leave"
                                    class="form-control" value="{{ $MonthlyPaySheet->employee_paid_leave }}">
                            </div>

                            <div class="col-md-3 col-6">
                                <label>Holidays</label>
                                <input type="number" step="any" id="holidays" name="holiday" class="form-control"
                                    value="{{ $MonthlyPaySheet->holiday }}">
                            </div>

                            <div class="col-md-3 col-6">
                                <label>Total Payable Days</label>
                                <input type="number" step="any" id="total_days" name="totalPayableDays"
                                    class="form-control" value="{{ $MonthlyPaySheet->totalPayableDays }}">
                            </div>

                            <div class="col-md-3 col-6">
                                <label>Overtime Hours</label>
                                <input type="number" step="any" name="overtime_houre" class="form-control"
                                    value="{{ $MonthlyPaySheet->overtime_houre }}">
                            </div>

                            <div class="col-md-3 col-6">
                                <label>Overtime Salary</label>
                                <input type="number" step="any" name="overtime_salary" class="form-control"
                                    value="{{ $MonthlyPaySheet->overtime_salary }}">
                            </div>

                            <div class="col-md-3 col-6">
                                <label>Adjustment</label>
                                <input type="number" step="any" name="adjustment" class="form-control"
                                    value="{{ $loanAdjustment ?? 0 }}">
                            </div>

                            <div class="col-md-3 col-6">
                                <label>Payable Salary</label>
                                <input type="number" step="any" id="payable_salary" name="employee_payable_salary"
                                    class="form-control" value="{{ $MonthlyPaySheet->employee_payable_salary }}">
                            </div>

                        </div>

                        <div class="mt-4 text-end">

                            @if (App\Helpers\Helper::roleAccess('hrm.paysheet.update'))
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Update
                                </button>
                            @endif

                            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                Back
                            </a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= JS ================= -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            function calculateSalary() {

                let gross = parseFloat(document.getElementById('gross_salary').value) || 0;
                let absence = parseFloat(document.getElementById('absence').value) || 0;
                let late = parseFloat(document.getElementById('late').value) || 0;
                let paidLeave = parseFloat(document.getElementById('paid_leave').value) || 0;
                let holidays = parseFloat(document.getElementById('holidays').value) || 0;

                // Daily Rate
                let dailyRate = gross / 30;
                document.getElementById('daily_rate').value = Number(dailyRate.toFixed(2));

                // Presence (FIXED LOGIC)
                let presence = 30 - (absence + paidLeave + holidays);
                if (presence < 0) presence = 0;
                document.getElementById('presence').value = presence;

                // Absence Deduction
                let absenceDeduction = absence * dailyRate;
                document.getElementById('absence_deduction').value = Number(absenceDeduction.toFixed(2));

                // Late Deduction
                let lateDays = Math.floor(late / 3);
                let lateDeduction = lateDays * dailyRate;
                document.getElementById('late_deduction').value = Number(lateDeduction.toFixed(2));

                // Total Payable Days
                let totalPayableDays = presence + paidLeave + holidays - lateDays;

                if (totalPayableDays > 30) totalPayableDays = 30;
                if (totalPayableDays < 0) totalPayableDays = 0;

                document.getElementById('total_days').value = totalPayableDays;

                // Final Salary
                let payableSalary = (totalPayableDays * dailyRate) - lateDeduction;
                if (payableSalary < 0) payableSalary = 0;

                document.getElementById('payable_salary').value = Number(payableSalary.toFixed(2));
            }

            // live update
            document.querySelectorAll("input").forEach(el => {
                el.addEventListener("input", calculateSalary);
            });

            calculateSalary();

            // submit clean fix (IMPORTANT)
            document.querySelector("form").addEventListener("submit", function() {

                function clean(id) {
                    let el = document.getElementById(id);
                    if (!el) return;

                    let val = parseFloat(el.value);
                    if (isNaN(val)) val = 0;

                    el.value = val;
                }

                clean("gross_salary");
                clean("daily_rate");
                clean("presence");
                clean("absence");
                clean("absence_deduction");
                clean("late");
                clean("late_deduction");
                clean("paid_leave");
                clean("holidays");
                clean("total_days");
                clean("payable_salary");
            });

        });
    </script>
@endsection
