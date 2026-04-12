@extends('backend.layouts.master')
@section('title')
    Hrm - {{ $title }}
@endsection

@section('styles')
    <style>
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
            border: 1px solid rgb(173, 173, 173)
        }

        div#ui-datepicker-div table tbody tr td a {
            color: #000
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        HRM </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        {{-- @if (helper::roleAccess('inventorySetup.adjust.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.attendance.index') }}">Hrm</a>
                            </li>
                        @endif --}}
                        <li class="breadcrumb-item active"><span>{{ $title }}</span></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Salary Pay Sheet</h3>
                    <div class="card-tools">
                        <span id="buttons"></span>
                        <a class="btn btn-tool btn-default" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </a>
                        <a class="btn btn-tool btn-default" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('hrm.paysheet.index') }}" method="get" id="paysheetForm">
                        <input type="hidden" name="action" id="formAction" value="search">

                        <div class="form-group row">
                            <div class="col-md-3">
                                <label for="employe" class="mt-2">Employee:</label>
                                <select name="employee_id" class="form-control select2" id="employe">
                                    <option value="all" selected>All</option>
                                    @foreach ($employees->all() as $employee)
                                        <option {{ $request->employee_id == $employee->id ? 'selected' : '' }}
                                            value="{{ $employee->id }}">
                                            {{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="From" class="mt-2">Select Month:</label>
                                <input type="month" id="From" value="{{ $request->month }}" class="form-control"
                                    name="month">
                            </div>

                            <div class="col-md-1" style="margin-top:38px">
                                <button type="submit" class="btn btn-success">Search</button>
                            </div>

                            <div class="col-md-2" style="margin-top:38px">
                                @php
                                    $monthName = \Carbon\Carbon::parse(request('month', now()->format('Y-m')))->format(
                                        'F Y',
                                    );
                                @endphp
                                <button type="button" class="btn btn-primary"
                                    onclick="submitGenerate('{{ $monthName }}')">
                                    <i class="fas fa-file-alt mr-1"></i> Generate Report
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
                <!-- /.card-body -->
            </div>
        </div>
        <!-- /.col-->
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <!-- /.card-header -->
                <div class="card-body">

                    <h5 class="text-center mt-3">Salary Pay Sheet History </h5>
                    <!-- Export Buttons -->
                    @if (isset($MonthlyPaySheets) || isset($tables))
                        <div class="d-flex justify-content-end mb-2 gap-1">
                            <button onclick="printSalarySheet()" class="btn btn-sm btn-outline-secondary mx-1">
                                <i class="fas fa-print md-1"></i>Print
                            </button>
                            <button onclick="exportExcel()" class="btn btn-sm btn-outline-success mx-1">
                                <i class="fas fa-file-excel mr-1"></i>Excel
                            </button>
                            <button onclick="exportPDF()" class="btn btn-sm btn-outline-danger mx-1">
                                <i class="fas fa-file-pdf mr-1"></i>PDF
                            </button>
                        </div>
                    @endif
                    <table class="table table-bordered table-responsive" id="salaryTable">
                        <thead>
                            <tr>
                                <th scope="col">SL</th>
                                <th scope="col">Name</th>
                                {{-- <th scope="col">Basic Salary</th> --}}
                                {{-- <th scope="col">House Rent</th> --}}
                                {{-- <th scope="col">Medical Allowance</th> --}}
                                {{-- <th scope="col">Conveyance Allowance</th> --}}
                                <th scope="col">Gross Salary (GS)</th>
                                <th scope="col">Daily Rate</th>
                                <th scope="col">Presence</th>
                                <th scope="col">Absence (AB)</th>
                                <th scope="col">Absent Deduction</th>
                                <th scope="col">Late (LT)</th>
                                <th scope="col-2">Late Deducton</th>
                                <th scope="col">Paid Leave (PL)</th>
                                <th scope="col">Holidays</th>
                                <th scope="col">Total Payable Days</th>
                                {{-- <th scope="col">Unpaid Leave (UL)</th> --}}
                                <th scope="col">Overtime Houre</th>
                                <th scope="col">Overtime Salary (OS)</th>
                                <th scope="col">Adjustment (Dr/Cr)</th>
                                <th scope="col">Payable Salary </th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($MonthlyPaySheets))

                           
                                @foreach ($MonthlyPaySheets as $key => $MonthlyPaySheet)

                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $MonthlyPaySheet->name }}</td>
                                        {{-- <td>{{ $MonthlyPaySheet->basic_salary }} </td> --}}
                                        {{-- <td>{{ $MonthlyPaySheet->house_rent }} </td> --}}
                                        {{-- <td>{{ $MonthlyPaySheet->medical_allowance }} </td> --}}
                                        {{-- <td>{{ $MonthlyPaySheet->travel_allowance }} </td> --}}
                                        <td>{{ $MonthlyPaySheet->total_salary }} </td>
                                        <td>{{ $MonthlyPaySheet->daily_rate ?? '0' }} </td>
                                        <td>{{ $MonthlyPaySheet->employee_presence_day }} </td>
                                        <td class="text-danger">{{ $MonthlyPaySheet->employee_absence_day }} </td>
                                        <td class="text-danger">{{ $MonthlyPaySheet->absence_deduction }} </td>
                                        <td>{{ $MonthlyPaySheet->employee_late }} </td>
                                        {{-- <td>{{ $MonthlyPaySheet->employee_deducton }} </td> --}}
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
                                        <td>{{ $MonthlyPaySheet->employee_paid_leave }} </td>
                                        {{-- <td>{{ $MonthlyPaySheet->employee_unpaid_leave }}</td> --}}


                                        <td>{{ $MonthlyPaySheet->holiday ?? '' }}</td>
                                        <td>{{  $MonthlyPaySheet->totalPayableDays ?? '' }}</td>
                                        <td>{{ $MonthlyPaySheet->overtime_houre }}h </td>
                                        <td>{{ $MonthlyPaySheet->overtime_salary }} </td>
                                        <td class="loanamount">
                                            @php
                                                $loan = DB::table('transections')
                                                    ->where('account_id', 1)
                                                    ->where('employee_id', $MonthlyPaySheet->employee_id)
                                                    ->selectRaw(
                                                        'SUM(debit) as debit
                                            ,SUM(credit) as credit',
                                                    )
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
                                        <td class="loanAdjustment d-none">{{ $loanAdjustment }} </td>
                                        <td class="payable">{{ $MonthlyPaySheet->employee_payable_salary }} </td>
                                        <td>
                                            @if ($MonthlyPaySheet->status == 'paid')
                                                <b class="text-success">Paid</b>
                                            @elseif($MonthlyPaySheet->status == 'unpaid')
                                                <b class="text-danger">Unpaid</b>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($MonthlyPaySheet->status == 'paid')
                                                <button class="btn btn-success"><i class="fas fa-check"></i></button>
                                            @elseif($MonthlyPaySheet->status == 'unpaid')
                                                <button class="paynow"
                                                    href="{{ route('hrm.paysheet.empPayDetailsStore', $MonthlyPaySheet->employee_id) }}"
                                                    data-toggle="modal" data-target="#exampleModal"><i
                                                        class="fas fa-money-bill"></i></button>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                            @elseif (isset($tables))
                                @php
                                    $total_salary = 0;
                                    $employee_payable_salary = 0;
                                @endphp
                                @foreach ($tables as $key => $table)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $table['name'] }}</td>
                                        {{-- <td>{{ $table['basic_salary'] }} </td> --}}
                                        {{-- <td>{{ $table['house_rent'] }} </td> --}}
                                        {{-- <td>{{ $table['medical_allowance'] }} </td> --}}
                                        {{-- <td>{{ $table['travel_allowance'] }} </td> --}}
                                        <td>{{ $table['total_salary'] }} </td>
                                        <td>{{ $table['daily_rate'] ?? '0' }} </td>
                                        <td>{{ $table['employee_presence_day'] }} </td>
                                        <td class="text-danger">{{ $table['employee_absence_day'] }} </td>
                                        <td class="text-danger">{{ $table['absence_deduction'] }} </td>
                                        <td>{{ $table['employee_late'] }} </td>
                                        <td class="text-danger">
                                            @php
                                                $late = $table['employee_late'] ?? 0;
                                                $days = floor($late / 3);
                                                $amount = $table['employee_deducton'] ?? 0;
                                            @endphp

                                            {{ $days == 0 ? '-' : number_format($amount, 2) . ' (' . $days . ' day' . ($days > 1 ? 's' : '') . ')' }}
                                        </td>


                                        <td>{{ $table['employee_paid_leave'] }} </td>
                                        <td>{{ $table['holiday'] }} </td>
                                        <td>{{ $table['totalPayableDays'] ?? '' }}</td>
                                        {{-- <td>{{ $table['employee_unpaid_leave'] }} </td> --}}
                                        <td>{{ $table['overtime_houre'] }} </td>
                                        <td>{{ $table['overtime_salary'] }} </td>
                                        <td class="loanamount">
                                            @php
                                                $loan = DB::table('transections')
                                                    ->where('account_id', 1)
                                                    ->where('employee_id', $table['employee_id'])
                                                    ->selectRaw(
                                                        'SUM(debit) as debit
                                            ,SUM(credit) as credit',
                                                    )
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
                                            {{ $loanBalance }}
                                        </td>
                                        <td class="loanAdjustment d-none">{{ $loanAdjustment }} </td>
                                        <td class="payable">{{ $table['employee_payable_salary'] }} </td>
                                        <td>
                                            {{-- @if ($table > status == 'paid') --}}
                                            {{-- <b class="text-success">Paid</b> --}}
                                            {{-- @elseif($table > status == 'unpaid') --}}
                                            <b class="text-danger">Unpaid</b>
                                            {{-- @endif --}}
                                        </td>
                                        <td>
                                            {{-- @if ($table > status == 'paid') --}}
                                            {{-- <button class="btn btn-success"><i class="fas fa-check"></i></button> --}}
                                            {{-- {{-- @elseif($table > status == 'unpaid') --}}
                                            {{-- <button class="paynow"
                                                href="{{ route('hrm.paysheet.empPayDetailsStore', $table['employee_id']) }}"
                                                data-toggle="modal" data-target="#exampleModal"><i
                                                    class="fas fa-money-bill"></i></button> --}}
                                            {{-- @endif --}}
                                        </td>
                                    </tr>
                                    @php
                                        $total_salary += $table['total_salary'];
                                        $employee_payable_salary += $table['employee_payable_salary'];
                                    @endphp
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="18"></td>
                                </tr>
                            @endif
                        </tbody>


                        <tfoot>
                            <tr>
                                <td colspan="7" class="text-success" class="text-right">In Word:
                                    {{ numberToWords($employee_payable_salary) }}</td>
                                {{-- <td class="text-right">{{ $total_salary }}</td> --}}
                                <td colspan="9" class="text-right">{{ $employee_payable_salary }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>


                </div>
                <!-- /.card-body -->
            </div>
        </div>
        <!-- /.col-->

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="card-header">
                        <h3 class="card-title">Salary Pay Sheet</h3>
                        <div class="card-tools">
                            <span id="buttons"></span>
                            <a class="btn btn-tool btn-default" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </a>
                            <a class="btn btn-tool btn-default" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card card-body">
                        <form class="needs-validation" action="" method="post" novalidate>
                            @csrf
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Paybale Salary</label>
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
                                        <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Payment Type <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-9 mb-1">
                                    <select name="payment_type" class="form-control">
                                        <option selected disabled>Select a Method</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('amount')
                                        <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <hr>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Loan </span></label>
                                <div class="col-md-9 mb-1">
                                    <h5 class="showloanamount">
                                    </h5>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Loan Adjustment</span></label>
                                <div class="col-md-9 mb-1">
                                    <h5 class="showloanadj">
                                    </h5>
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let from = document.getElementById("From");
            let currentMonth = new Date().toISOString().slice(0, 7);
            from.setAttribute("max", currentMonth);
        });

        $(document).on('click', '.paynow', function() {
            let url = $(this).data('url'); // ← href থেকে data-url এ পরিবর্তন
            $('#modalForm').attr('action', url); // শুধু modal form-এর action set করুন
            let payable = $(this).closest('tr').find('.payable').text().trim();
            $('.showpayable').text(payable);
            $('.payamount').val(Number(payable));
            let loanamount = $(this).closest('tr').find('.loanamount').text().trim();
            $('.showloanamount').text(loanamount);
            let loanAdjustment = $(this).closest('tr').find('.loanAdjustment').text().trim();
            $('.showloanadj').text(loanAdjustment);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <script>
        function submitGenerate(monthName) {
            if (!confirm(monthName + ' মাসের স্যালারি রিপোর্ট তৈরি করবেন?')) return;
            document.getElementById('formAction').value = 'generate';
            document.getElementById('paysheetForm').submit();
        }
    </script>
    <script>
        const MONTH_LABEL = '{{ request('month', now()->format('Y-m')) }}';

        // ── Company info (settings থেকে নিন অথবা hardcode করুন) ──
        const COMPANY = {
            name: '{{ config('app.company_name', 'Your Company Name') }}',
            address: '{{ config('app.company_address', 'Dhaka, Bangladesh') }}',
            phone: '{{ config('app.company_phone', '') }}',
            email: '{{ config('app.company_email', '') }}',
            logo: '{{ asset('images/logo.png') }}', // logo path
        };

        // ── Shared header HTML (print window) ──────────────────────
        function buildHeaderHTML() {
            return `
    <div style="display:flex;align-items:center;gap:16px;border-bottom:2px solid #333;padding-bottom:10px;margin-bottom:16px">
        <img src="${COMPANY.logo}" style="height:60px;width:auto;object-fit:contain"
             onerror="this.style.display='none'">
        <div>
            <div style="font-size:18px;font-weight:700;margin-bottom:2px">${COMPANY.name}</div>
            <div style="font-size:12px;color:#555">${COMPANY.address}</div>
            ${COMPANY.phone ? `<div style="font-size:12px;color:#555">Tel: ${COMPANY.phone}</div>` : ''}
            ${COMPANY.email ? `<div style="font-size:12px;color:#555">Email: ${COMPANY.email}</div>` : ''}
        </div>
    </div>
    <div style="text-align:center;margin-bottom:12px">
        <div style="font-size:15px;font-weight:600">Salary Pay Sheet</div>
        <div style="font-size:12px;color:#555">Month: ${MONTH_LABEL} &nbsp;|&nbsp; Generated: ${new Date().toLocaleDateString('en-BD')}</div>
    </div>`;
        }

        // ── Print ──────────────────────────────────────────────────
        function printSalarySheet() {
            const table = document.getElementById('salaryTable').cloneNode(true);
            // Action column remove করুন print-এ
            table.querySelectorAll('tr').forEach(tr => {
                const cells = tr.querySelectorAll('th, td');
                if (cells.length > 0) cells[cells.length - 1].remove();
            });

            const win = window.open('', '_blank');
            win.document.write(`<html><head>
    <title>Salary Pay Sheet - ${MONTH_LABEL}</title>
    <style>
        *{box-sizing:border-box}
        body{font-family:Arial,sans-serif;font-size:10px;margin:20px;color:#111}
        table{width:100%;border-collapse:collapse;margin-top:4px}
        th,td{border:1px solid #aaa;padding:3px 5px;text-align:left;white-space:nowrap}
        th{background:#e8e8e8;font-weight:600;font-size:10px}
        td{font-size:10px}
        .text-danger{color:#c0392b}
        .text-success{color:#27ae60}
        tfoot td{background:#f5f5f5;font-weight:600}
        @media print{
            body{margin:10px}
            @page{size:A3 landscape;margin:10mm}
        }
    </style></head><body>
    ${buildHeaderHTML()}
    ${table.outerHTML}
    <div style="margin-top:20px;font-size:10px;color:#777;text-align:right">
        Printed on: ${new Date().toLocaleString('en-BD')}
    </div>
    <script>window.onload=()=>{window.print();}<\/script>
    </body></html>`);
            win.document.close();
        }

        // ── Excel ──────────────────────────────────────────────────
        function exportExcel() {
            const tbl = document.getElementById('salaryTable');
            const wb = XLSX.utils.book_new();

            // Header rows manually
            const headerRows = [
                [COMPANY.name],
                [COMPANY.address],
                [`Salary Pay Sheet — ${MONTH_LABEL}`],
                [],
            ];
            const ws = XLSX.utils.aoa_to_sheet(headerRows);

            // Table data append করুন
            XLSX.utils.sheet_add_dom(ws, tbl, {
                origin: -1
            });

            // Style: column width auto
            const cols = [];
            for (let i = 0; i < 20; i++) cols.push({
                wch: 14
            });
            ws['!cols'] = cols;

            XLSX.utils.book_append_sheet(wb, ws, 'Salary');
            XLSX.writeFile(wb, `Salary_PaySheet_${MONTH_LABEL}.xlsx`);
        }

        // ── PDF ────────────────────────────────────────────────────
        function exportPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF({
                orientation: 'landscape',
                unit: 'pt',
                format: 'a3'
            });

            const pageW = doc.internal.pageSize.getWidth();
            let curY = 30;

            // Logo
            const logoImg = new Image();
            logoImg.crossOrigin = 'anonymous';
            logoImg.onload = function() {
                try {
                    doc.addImage(this, 'PNG', 30, curY, 50, 40);
                } catch (e) {}
                renderPDF(doc, pageW, curY);
            };
            logoImg.onerror = function() {
                renderPDF(doc, pageW, curY);
            };
            logoImg.src = COMPANY.logo;
        }

        function renderPDF(doc, pageW, startY) {
            let curY = startY;

            // Company info (right of logo)
            doc.setFontSize(14).setFont(undefined, 'bold');
            doc.text(COMPANY.name, 90, curY + 14);
            doc.setFontSize(9).setFont(undefined, 'normal');
            doc.setTextColor(80);
            doc.text(COMPANY.address, 90, curY + 28);
            if (COMPANY.phone) doc.text('Tel: ' + COMPANY.phone, 90, curY + 40);
            doc.setTextColor(0);

            // Divider line
            curY += 55;
            doc.setDrawColor(60).setLineWidth(1);
            doc.line(30, curY, pageW - 30, curY);
            curY += 12;

            // Report title
            doc.setFontSize(12).setFont(undefined, 'bold');
            doc.text('Salary Pay Sheet', pageW / 2, curY, {
                align: 'center'
            });
            curY += 16;
            doc.setFontSize(9).setFont(undefined, 'normal').setTextColor(80);
            doc.text(`Month: ${MONTH_LABEL}   |   Generated: ${new Date().toLocaleDateString('en-BD')}`,
                pageW / 2, curY, {
                    align: 'center'
                });
            doc.setTextColor(0);
            curY += 14;

            // Table
            doc.autoTable({
                html: '#salaryTable',
                startY: curY,
                styles: {
                    fontSize: 7,
                    cellPadding: 3,
                    overflow: 'linebreak'
                },
                headStyles: {
                    fillColor: [44, 62, 80],
                    textColor: 255,
                    fontStyle: 'bold',
                    fontSize: 7
                },
                alternateRowStyles: {
                    fillColor: [245, 247, 250]
                },
                columnStyles: {
                    0: {
                        cellWidth: 20
                    }
                },
                margin: {
                    left: 30,
                    right: 30
                },
                didParseCell: (data) => {
                    // Action column খালি রাখুন
                    if (data.column.index === data.table.columns.length - 1) {
                        data.cell.text = '';
                    }
                },
                didDrawPage: (data) => {
                    // Footer: page number
                    doc.setFontSize(8).setTextColor(120);
                    doc.text(`Page ${data.pageNumber}`, pageW / 2,
                        doc.internal.pageSize.getHeight() - 15, {
                            align: 'center'
                        });
                    doc.setTextColor(0);
                }
            });

            doc.save(`Salary_PaySheet_${MONTH_LABEL}.pdf`);
        }
    </script>
@endsection

{{-- @section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let from = document.getElementById("From");
            let today = new Date();
            let currentMonth = today.toISOString().slice(0, 7); // Format: YYYY-MM

            from.setAttribute("max", currentMonth);
        });

        $(document).on('click', '.paynow', function() {
            let url = $(this).attr('href');
            $('form').attr('action', url);
            let payable = $(this).closest('tr').find('.payable').text();
            $('.showpayable').text(payable)
            $('.payamount').val(Number(payable))
            let loanamount = $(this).closest('tr').find('.loanamount').text();
            $('.showloanamount').text(loanamount);
            let loanAdjustment = $(this).closest('tr').find('.loanAdjustment').text();
            $('.showloanadj').text(loanAdjustment);
        })
    </script>

    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <!-- jsPDF + AutoTable for PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

    <script>
        const MONTH_LABEL = '{{ request('month', now()->format('Y-m')) }}';

        // ── Print ──────────────────────────────────────────
        function printSalarySheet() {
            const table = document.getElementById('salaryTable').outerHTML;
            const win = window.open('', '_blank');
            win.document.write(`
        <html><head>
        <title>Salary Pay Sheet - ${MONTH_LABEL}</title>
        <style>
            body{font-family:Arial,sans-serif;font-size:11px;margin:20px}
            h3{text-align:center;margin-bottom:6px}
            table{width:100%;border-collapse:collapse}
            th,td{border:1px solid #999;padding:4px 6px;text-align:left}
            th{background:#f0f0f0;font-weight:600}
            .text-danger{color:#dc3545}
            .text-success{color:#28a745}
            @media print{button{display:none}}
        </style></head><body>
        <h3>Salary Pay Sheet — ${MONTH_LABEL}</h3>
        ${table}
        <script>window.onload=()=>window.print();</scri` + `pt>
    </body></html>`);
            win.document.close();
        }

        // ── Excel ──────────────────────────────────────────
        function exportExcel() {
            const tbl = document.getElementById('salaryTable');
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(tbl);
            XLSX.utils.book_append_sheet(wb, ws, 'Salary');
            XLSX.writeFile(wb, `Salary_PaySheet_${MONTH_LABEL}.xlsx`);
        }

        // ── PDF ────────────────────────────────────────────
        function exportPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF({
                orientation: 'landscape',
                unit: 'pt',
                format: 'a4'
            });
            doc.setFontSize(13);
            doc.text(`Salary Pay Sheet — ${MONTH_LABEL}`, 40, 30);
            doc.autoTable({
                html: '#salaryTable',
                startY: 45,
                styles: {
                    fontSize: 7,
                    cellPadding: 3
                },
                headStyles: {
                    fillColor: [52, 73, 94],
                    textColor: 255,
                    fontStyle: 'bold'
                },
                columnStyles: {
                    0: {
                        cellWidth: 22
                    }
                },
                didParseCell: (data) => {
                    // Action column লুকাও PDF-এ
                    if (data.column.index === data.table.columns.length - 1) {
                        data.cell.text = '';
                    }
                }
            });
            doc.save(`Salary_PaySheet_${MONTH_LABEL}.pdf`);
        }
    </script>
@endsection --}}
