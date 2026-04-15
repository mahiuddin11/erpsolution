@extends('backend.layouts.master')
@section('title')
    Hrm - {{ $title }}
@endsection

@section('styles')
    <style>
        /* ── General ── */
        .bootstrap-switch-large {
            width: 200px;
        }

        div#ui-datepicker-div {
            background: #fff;
            padding: 10px 20px;
            margin-top: -6px;
            border: 1px solid #e6e6e6;
        }

        div#ui-datepicker-div table tbody tr td {
            border: 1px solid rgb(173, 173, 173);
        }

        div#ui-datepicker-div table tbody tr td a {
            color: #000;
        }

        /* ── Responsive form row ── */
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            /* align-items: flex-end; */
        }

        .filter-row .f-employee {
            flex: 0 0 220px;
            min-width: 160px;
        }

        .filter-row .f-month {
            flex: 0 0 180px;
            min-width: 140px;
        }


        .filter-row .f-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        a.btn.btn-info.btn-sm {
            margin-right: 8px;
        }

        .filter-row .f-actions button {
            white-space: nowrap;
        }

        @media (max-width: 576px) {
            .filter-row {
                flex-direction: column;
            }

            .filter-row .f-employee,
            .filter-row .f-month {
                flex: 1 1 100%;
            }

            .filter-row .f-actions {
                width: 100%;
                justify-content: stretch;
            }

            .filter-row .f-actions button {
                flex: 1 1 calc(50% - 4px);
            }


        }

        /* ── Table responsive ── */
        #salaryTable {
            min-width: 900px;
        }

        .table-outer {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">HRM</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><span>{{ $title }}</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Salary Pay Sheet</h3>
                    <div class="card-tools">
                        <a class="btn btn-tool btn-default" data-card-widget="collapse"><i class="fas fa-minus"></i></a>
                        <a class="btn btn-tool btn-default" data-card-widget="remove"><i class="fas fa-times"></i></a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('hrm.paysheet.index') }}" method="get" id="paysheetForm">
                        <input type="hidden" name="action" id="formAction" value="search">

                        <div class="filter-row">
                            <div class="f-employee ">
                                <label for="employe">Employee:</label>
                                <select name="employee_id" class="form-control select2" id="employe">
                                    <option value="all" selected>All</option>
                                    @foreach ($employees->all() as $employee)
                                        <option {{ $request->employee_id == $employee->id ? 'selected' : '' }}
                                            value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="f-month">
                                <label for="From">Select Month:</label>
                                <input type="month" id="From" value="{{ $request->month }}" class="form-control"
                                    name="month">
                            </div>

                            <div class="f-actions">
                                @php
                                    $monthName = \Carbon\Carbon::parse(request('month', now()->format('Y-m')))->format(
                                        'F Y',
                                    );
                                @endphp
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-search mr-1"></i>Search
                                </button>
                                <button type="button" class="btn btn-primary"
                                    onclick="submitGenerate('{{ $monthName }}')">
                                    <i class="fas fa-file-alt mr-1"></i>Generate Payable Salary
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <h5 class="text-center mt-3">Salary Pay Sheet History</h5>

                    @if (isset($MonthlyPaySheets) || isset($tables))
                        <div class="d-flex justify-content-end mb-2 flex-wrap gap-1">
                            <button onclick="printSalarySheet()" class="btn btn-sm btn-outline-secondary mx-1">
                                <i class="fas fa-print mr-1"></i>Print
                            </button>
                            <button onclick="exportExcel()" class="btn btn-sm btn-outline-success mx-1">
                                <i class="fas fa-file-excel mr-1"></i>Excel
                            </button>
                            <button onclick="exportPDF()" class="btn btn-sm btn-outline-danger mx-1">
                                <i class="fas fa-file-pdf mr-1"></i>PDF
                            </button>
                        </div>
                    @endif

                    <div class="table-outer">
                        <table class="table table-bordered" id="salaryTable">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Gross Salary (GS)</th>
                                    <th>Daily Rate</th>
                                    <th>Presence</th>
                                    <th>Absence (AB)</th>
                                    <th>Absent Deduction</th>
                                    <th>Late (LT)</th>
                                    <th>Late Deduction</th>
                                    <th>Paid Leave (PL)</th>
                                    <th>Holidays</th>
                                    <th>Total Payable Days</th>
                                    <th>Overtime Hours</th>
                                    <th>Overtime Salary (OS)</th>
                                    <th>Adjustment (Dr/Cr)</th>
                                    <th>Payable Salary</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $employee_payable_salary = 0; @endphp

                                @if (isset($MonthlyPaySheets))
                                    @foreach ($MonthlyPaySheets as $key => $MonthlyPaySheet)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $MonthlyPaySheet->name }}</td>
                                            <td>{{ $MonthlyPaySheet->total_salary }}</td>
                                            <td>{{ $MonthlyPaySheet->daily_rate ?? '0' }}</td>
                                            <td>{{ $MonthlyPaySheet->employee_presence_day }}</td>
                                            <td class="text-danger">{{ $MonthlyPaySheet->employee_absence_day }}</td>
                                            <td class="text-danger">{{ $MonthlyPaySheet->absence_deduction }}</td>
                                            <td>{{ $MonthlyPaySheet->employee_late }}</td>
                                            <td>
                                                {{ floor($MonthlyPaySheet->employee_late / 3) == 0
                                                    ? '-'
                                                    : number_format($MonthlyPaySheet->employee_deducton, 2) .
                                                        ' (' .
                                                        floor($MonthlyPaySheet->employee_late / 3) .
                                                        ' day' .
                                                        (floor($MonthlyPaySheet->employee_late / 3) > 1 ? 's' : '') .
                                                        ')' }}
                                            </td>
                                            <td>{{ $MonthlyPaySheet->employee_paid_leave }}</td>
                                            <td>{{ $MonthlyPaySheet->holiday ?? '' }}</td>
                                            <td>{{ $MonthlyPaySheet->totalPayableDays ?? '' }}</td>
                                            <td>{{ $MonthlyPaySheet->overtime_houre }}h</td>
                                            <td>{{ $MonthlyPaySheet->overtime_salary }}</td>
                                            <td class="loanamount">
                                                @php
                                                    $loan = DB::table('transections')
                                                        ->where('account_id', 1)
                                                        ->where('employee_id', $MonthlyPaySheet->employee_id)
                                                        ->selectRaw('SUM(debit) as debit, SUM(credit) as credit')
                                                        ->first();
                                                    $loanBalance = $loan->debit - $loan->credit;
                                                    $loanAdjustment = App\Models\Lone::where(
                                                        'employee_id',
                                                        $MonthlyPaySheet->employee_id,
                                                    )
                                                        ->where('status', 'approved')
                                                        ->latest()
                                                        ->pluck('lone_adjustment')
                                                        ->first();
                                                @endphp
                                                {{ $loanBalance }}
                                            </td>
                                            <td class="loanAdjustment d-none">{{ $loanAdjustment }}</td>
                                            <td class="payable">{{ $MonthlyPaySheet->employee_payable_salary }}</td>
                                            <td>
                                                @if ($MonthlyPaySheet->status == 'paid')
                                                    <b class="text-success">Paid</b>
                                                @elseif($MonthlyPaySheet->status == 'unpaid')
                                                    <b class="text-danger">Unpaid</b>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="d-flex  gap-2 justify-content-center">
                                                    @if (App\Helpers\Helper::roleAccess('hrm.paysheet.review'))
                                                        <a href="{{ route('hrm.paysheet.review', $MonthlyPaySheet->employee_id) }}"
                                                            class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif


                                                    <!-- Status ভিত্তিক Button -->
                                                    @if ($MonthlyPaySheet->status == 'paid')
                                                        <button class="btn btn-success btn-sm">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @elseif($MonthlyPaySheet->status == 'unpaid')
                                                        <button class="paynow btn btn-warning btn-sm"
                                                            data-url="{{ route('hrm.paysheet.empPayDetailsStore', $MonthlyPaySheet->employee_id) }}"
                                                            data-toggle="modal" data-target="#exampleModal">
                                                            <i class="fas fa-money-bill"></i>
                                                        </button>
                                                    @endif

                                                </div>
                                            </td>

                                        </tr>
                                    @endforeach
                                @elseif (isset($tables))
                                    @foreach ($tables as $key => $table)
                                        @php
                                            $late = $table['employee_late'] ?? 0;
                                            $days = floor($late / 3);
                                            $amt = $table['employee_deducton'] ?? 0;
                                            $loan = DB::table('transections')
                                                ->where('account_id', 1)
                                                ->where('employee_id', $table['employee_id'])
                                                ->selectRaw('SUM(debit) as debit, SUM(credit) as credit')
                                                ->first();
                                            $loanBalance = $loan->debit - $loan->credit;
                                            $loanAdjustment = App\Models\Lone::where(
                                                'employee_id',
                                                $table['employee_id'],
                                            )
                                                ->where('status', 'approved')
                                                ->latest()
                                                ->pluck('lone_adjustment')
                                                ->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $table['name'] }}</td>
                                            <td>{{ $table['total_salary'] }}</td>
                                            <td>{{ $table['daily_rate'] ?? '0' }}</td>
                                            <td>{{ $table['employee_presence_day'] }}</td>
                                            <td class="text-danger">{{ $table['employee_absence_day'] }}</td>
                                            <td class="text-danger">{{ $table['absence_deduction'] }}</td>
                                            <td>{{ $table['employee_late'] }}</td>
                                            <td class="text-danger">
                                                {{ $days == 0 ? '-' : number_format($amt, 2) . ' (' . $days . ' day' . ($days > 1 ? 's' : '') . ')' }}
                                            </td>
                                            <td>{{ $table['employee_paid_leave'] }}</td>
                                            <td>{{ $table['holiday'] }}</td>
                                            <td>{{ $table['totalPayableDays'] ?? '' }}</td>
                                            <td>{{ $table['overtime_houre'] }}</td>
                                            <td>{{ $table['overtime_salary'] }}</td>
                                            <td class="loanamount">{{ $loanBalance }}</td>
                                            <td class="loanAdjustment d-none">{{ $loanAdjustment }}</td>
                                            <td class="payable">{{ $table['employee_payable_salary'] }}</td>
                                            <td><b class="text-danger">Unpaid</b></td>
                                            <td></td>
                                        </tr>
                                        @php $employee_payable_salary += $table['employee_payable_salary']; @endphp
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="18" class="text-center text-muted py-3">No data found.</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7" class="text-success">
                                        In Word: {{ numberToWords($employee_payable_salary) }}
                                    </td>
                                    <td colspan="9" class="text-right font-weight-bold">{{ $employee_payable_salary }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal --}}
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="card-header">
                        <h3 class="card-title">Salary Pay Sheet</h3>
                        <div class="card-tools">
                            <a class="btn btn-tool btn-default" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></a>
                            <a class="btn btn-tool btn-default" data-card-widget="remove"><i
                                    class="fas fa-times"></i></a>
                        </div>
                    </div>
                    <div class="card card-body">
                        <form id="modalForm" class="needs-validation" action="" method="post" novalidate>
                            @csrf
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Payable Salary</label>
                                <div class="col-md-9 mb-1">
                                    <h5 class="showpayable"></h5>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Amount <span class="text-danger">*</span></label>
                                <div class="col-md-9 mb-1">
                                    <input type="number" class="form-control payamount" min="1" required
                                        name="amount">
                                    @error('amount')
                                        <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Payment Type <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-9 mb-1">
                                    <select name="payment_type" class="form-control">
                                        <option selected disabled> Select a Method</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Loan</label>
                                <div class="col-md-9 mb-1">
                                    <h5 class="showloanamount"></h5>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Loan Adjustment</label>
                                <div class="col-md-9 mb-1">
                                    <h5 class="showloanadj"></h5>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@include('backend.pages.hrm.attendance.paysheet.script')
@endsection
