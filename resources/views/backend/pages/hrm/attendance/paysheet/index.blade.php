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
                                                        <a href="{{ route('hrm.paysheet.review', $MonthlyPaySheet->id) }}"
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
                                                            data-url="{{ route('hrm.paysheet.empPayDetailsStore', $MonthlyPaySheet->id) }}"
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
  <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
  <script>
      // ── Constants ────────────────────────────────────────────────────────────────
      const MONTH_LABEL = '{{ \Carbon\Carbon::parse(request('month', now()))->format('F Y') }}';

     
      let LOGO_BASE64 = null;
      let LOGO_MIMETYPE = 'image/png';

      // Preload logo on page load
      (function preloadLogo() {
          const logoUrl = '{{ asset('backend/logo/logo.png') }}';
          const img = new Image();
          img.crossOrigin = 'anonymous';
          img.onload = function() {
              try {
                  const canvas = document.createElement('canvas');
                  canvas.width = img.naturalWidth;
                  canvas.height = img.naturalHeight;
                  const ctx = canvas.getContext('2d');
                  ctx.drawImage(img, 0, 0);
                  LOGO_BASE64 = canvas.toDataURL('image/png').split(',')[1];
              } catch (e) {
                  console.warn('Logo preload canvas error:', e);
              }
          };
          img.onerror = () => console.warn('Logo not found at:', logoUrl);
          img.src = logoUrl + '?v=' + Date.now(); // cache bust
      })();

      // ── Month input max ──────────────────────────────────────────────────────────
      document.addEventListener("DOMContentLoaded", function() {
          const from = document.getElementById("From");
          if (from) from.setAttribute("max", new Date().toISOString().slice(0, 7));
      });

      // ── Pay Now modal ─────────────────────────────────────────────────────────────
      $(document).on('click', '.paynow', function() {
          const url = $(this).data('url');
        
          $('#modalForm').attr('action', url);
          const tr = $(this).closest('tr');
          $('.showpayable').text(tr.find('.payable').text().trim());
          $('.payamount').val(parseFloat(tr.find('.payable').text().trim()) || 0);
          $('.showloanamount').text(tr.find('.loanamount').text().trim());
          $('.showloanadj').text(tr.find('.loanAdjustment').text().trim());
      });

      // ── Generate Report ───────────────────────────────────────────────────────────
      function submitGenerate(monthName) {
          if (!confirm(monthName + ' মাসের স্যালারি রিপোর্ট তৈরি করবেন?')) return;
          document.getElementById('formAction').value = 'generate';
          document.getElementById('paysheetForm').submit();
      }

      // ── Helpers ───────────────────────────────────────────────────────────────────
      function getTotalPayable() {
          let total = 0;
          document.querySelectorAll('#salaryTable tbody tr .payable').forEach(c => {
              total += parseFloat(c.textContent.trim()) || 0;
          });
          return total;
      }



      // ── WTB Letterhead header for PRINT ──────────────────────────────────────────
      function buildPrintHeaderHTML(month, logoSrc) {
          return `
    <div class="page-header">
        <div class="header-left">
            <img src="${logoSrc}" class="logo-img" onerror="this.style.display='none'" crossorigin="anonymous">
            <div class="header-titles">
                <div class="co-name">Water Technology BD Limited</div>
                <div class="co-tagline">Value adding is our business</div>
            </div>
        </div>
        <div class="header-center">
            <div class="doc-title">Salary Pay Sheet</div>
            <div class="doc-month">Month: <strong>${month}</strong></div>
        </div>
        <div class="header-right">
    <img src="{{ asset('images/iso_cert.png') }}" class="iso-img" onerror="this.style.display='none'">

    <div class="header-info">
        <p class="title">ISO 9001:2015 Certified</p>
        <p>Dhaka, Bangladesh</p>
        <p>Tel: +88-02-58070365</p>
        <p>www.wtbl.com.bd</p>
    </div>
</div>
    </div>
    <div class="header-line-blue"></div>
    <div class="header-line-dark"></div>`;
      }

      // Company address footer for PRINT
      function buildPrintFooterHTML(totalPayable, generated) {
          return `
    <div class="doc-footer-bar">
        <span>Total Payable: <strong>${totalPayable.toLocaleString('en-BD',{minimumFractionDigits:2})} Tk</strong></span>
        <span class="gen-date">Generated: ${generated}</span>
    </div>
    <div class="company-address-footer">
        <div>
            <strong>Operational Headquarter</strong><br>
            Plot-No# 1248, Level # 4th Floor, Road No # 09,<br>
            Avenue # 02, Mirpur DOHS, Dhaka-1216, Bangladesh.<br>
            Tel: +88-02-58070365 &nbsp;|&nbsp; Fax: +88-02-58070365<br>
            Cell: +88017135655696
        </div>
        <div>
            <strong>Factory Address:</strong><br>
            Plot-83/84, Nagori Bazar, Kaliganj,<br>
            Gazipur-1720
        </div>
        <div>
            <strong>Website:</strong> www.wtbl.com.bd<br>
            <strong>Email:</strong> info@wtbl.com.bd;<br>
            hkfservice.inn@gmail.com
        </div>
    </div>`;
      }


      // ── PRINT ─────────────────────────────────────────────────────────────────────
      function printSalarySheet() {
          const table = document.getElementById('salaryTable').cloneNode(true);
          const logoSrc = '{{ asset('backend/logo/logo.png') }}';
          const month = MONTH_LABEL;
          const total = getTotalPayable();
          const gen = new Date().toLocaleString('en-GB');

          // Remove last (Action) column
          table.querySelectorAll('tr').forEach(tr => {
              const cells = tr.querySelectorAll('th, td');
              if (cells.length) cells[cells.length - 1].remove();
          });

          const win = window.open('', '_blank');
          win.document.write(`
<html><head>
<title>Salary Pay Sheet - ${month}</title>
<style>
* { box-sizing:border-box; margin:0; padding:0; }
body { font-family: 'Segoe UI', Arial, sans-serif; font-size:10px; color:#111; background:#fff; }

/* ── Header ── */
.page-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:12px 20px 8px; margin-bottom:0;
}.header-right {
    text-align: right;
}

.iso-img {
    width: 80px;
    margin-bottom: 5px;
}

.header-info p {
    margin: 2px 0;
    font-size: 12px;
    color: #555;
}

.header-info .title {
    font-weight: bold;
    color: #1a5ea8;
}

.header-left { display:flex; align-items:center; gap:10px; }
.logo-img    { height:52px; width:auto; }
.co-name     { font-size:16px; font-weight:700; color:#1A5EA8; }
.co-tagline  { font-size:9px; font-style:italic; color:#555; margin-top:2px; }
.header-center { text-align:center; flex:1; }
.doc-title   { font-size:18px; font-weight:700; color:#1A5EA8; }
.doc-month   { font-size:10px; color:#555; margin-top:3px; }
.header-right { text-align:right; }
.iso-img     { height:48px; width:auto; }
.header-line-blue { height:3px; background:#00AEEF; margin:4px 0 0; }
.header-line-dark { height:1px; background:#1A2E5A; margin-bottom:10px; }


.watermark-wrap {
    position: relative;
    isolation: isolate; /* নতুন stacking context তৈরি করে */
}
.watermark-img {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 440px;
    height: 400px;
    object-fit: contain;
    opacity: 0.12;        /* একটু বাড়ানো হয়েছে যাতে ভালো দেখায় */
    pointer-events: none;
    z-index: -1;          
   
}
.wrap {
    padding: 0 16px 10px;
    position: relative;
    background: transparent; /* wrap নিজে transparent */
}


table { width:100%; border-collapse:collapse; }
th, td {
    border: 1px solid #bbb;
    padding: 3px 5px;
    text-align: left;
    font-size: 8.5px;
    white-space: nowrap;
    background: transparent; /* FIX: solid white এর বদলে transparent */
}
th {
    background: rgba(26, 94, 168, 0.82) !important; /* header একটু opaque রাখা */
    color: #fff;
    text-align: center;
    font-size: 8.5px;
}
tr:nth-child(even) td {
    background: rgba(240, 246, 255, 0.55) !important; /* FIX: rgba — watermark দেখা যাবে */
}
tfoot tr td {
    background: rgba(232, 240, 251, 0.7) !important;
    font-weight: bold;
}
.text-danger  { color: #c0392b; }
.text-success { color: #1a7a3c; }

/* ── Footer ── */
.doc-footer-bar {
    display:flex; justify-content:space-between; align-items:center;
    background:#1A5EA8; color:#fff; padding:7px 16px;
    margin:10px 16px 0; border-radius:3px; font-size:10px; font-weight:bold;
}
.gen-date { font-size:8px; font-weight:normal; color:#b4c8e8; }
.company-address-footer {
    display:flex; justify-content:space-between;
    border-top:2px solid #00AEEF; margin:10px 16px 0;
    padding-top:8px; font-size:8px; color:#444; gap:20px;
}
.company-address-footer > div { flex:1; }

@media print {
    @page { size: A3 landscape; margin: 6mm; }
    .no-print { display: none !important; }
    
    /* FIX: print-এও watermark দেখানোর জন্য */
    .watermark-img {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0.12;
        z-index: -1;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    th, td, tr {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
</head><body>
${buildPrintHeaderHTML(month, logoSrc)}
<div class="watermark-wrap">
    <img src="${logoSrc}" class="watermark-img" onerror="this.style.display='none'">
    <div class="wrap">${table.outerHTML}</div>
</div>
${buildPrintFooterHTML(total, gen)}
<script>window.onload = () => window.print();<\/script>
</body></html>`);
          win.document.close();
      }



      // ── EXCEL ─────────────────────────────────────────────────────────────────────
      function exportExcel() {
          const tbl = document.getElementById('salaryTable');
          const wb = XLSX.utils.book_new();

          const headerRows = [
              ['Water Technology BD Limited'],
              ['Value adding is our business'],
              ['Salary Pay Sheet — ' + MONTH_LABEL],
              [],
          ];
          const ws = XLSX.utils.aoa_to_sheet(headerRows);
          XLSX.utils.sheet_add_dom(ws, tbl, {
              origin: -1
          });

          const cols = Array.from({
              length: 20
          }, () => ({
              wch: 15
          }));
          ws['!cols'] = cols;

          XLSX.utils.book_append_sheet(wb, ws, 'Salary');
          XLSX.writeFile(wb, `Salary_PaySheet_${MONTH_LABEL}.xlsx`);
      }

      // ── PDF ───────────────────────────────────────────────────────────────────────
      async function exportPDF() {
          const {
              jsPDF
          } = window.jspdf;
          const doc = new jsPDF({
              orientation: 'landscape',
              unit: 'pt',
              format: 'a3'
          });

          const pageW = doc.internal.pageSize.getWidth();
          const pageH = doc.internal.pageSize.getHeight();
          const month = MONTH_LABEL;
          const total = getTotalPayable();
          const gen = new Date().toLocaleString('en-GB');
          const mL = 28,
              mR = 28;

          // ── Draw header on a page ────────────────────────────────────────────
          function drawHeader(doc, startY) {
              let y = startY;

              // WTB Logo (top-left)
              if (LOGO_BASE64) {
                  try {
                      doc.addImage(LOGO_BASE64, 'PNG', mL, y, 100, 42);
                  } catch (e) {
                      console.warn('Logo addImage error', e);
                  }
              }

              // Company name (top-right of logo)
              doc.setFont('helvetica', 'bold').setFontSize(16).setTextColor(26, 94, 168);
              doc.text('Water Technology BD Limited', mL + 110, y + 18);

              doc.setFont('helvetica', 'italic').setFontSize(9).setTextColor(80, 80, 80);
              doc.text('Value adding is our business', mL + 110, y + 30);

              doc.setFont('helvetica', 'normal').setFontSize(10).setTextColor(60, 60, 60);
              doc.text('Month: ' + month, mL + 110, y + 45);

              // Center title
              doc.setFont('helvetica', 'bold').setFontSize(25).setTextColor(26, 94, 168);
              doc.text('Salary Pay Sheet', pageW / 2, y + 20, {
                  align: 'center'
              });


              // ISO / right side info
              doc.setFont('helvetica', 'bold').setFontSize(9).setTextColor(26, 94, 168);
              doc.text('ISO 9001:2015 Certified', pageW - mR, y + 14, {
                  align: 'right'
              });
              doc.setFont('helvetica', 'normal').setFontSize(8).setTextColor(80, 80, 80);
              doc.text('Dhaka, Bangladesh', pageW - mR, y + 26, {
                  align: 'right'
              });
              doc.text('Tel: +88-02-58070365', pageW - mR, y + 36, {
                  align: 'right'
              });
              doc.text('www.wtbl.com.bd', pageW - mR, y + 46, {
                  align: 'right'
              });

              // Blue line
              const lineY1 = y + 52;
              doc.setDrawColor(0, 174, 239).setLineWidth(2.5);
              doc.line(mL, lineY1, pageW - mR, lineY1);

              // Dark line
              const lineY2 = lineY1 + 3;
              doc.setDrawColor(26, 46, 90).setLineWidth(0.8);
              doc.line(mL, lineY2, pageW - mR, lineY2);

              doc.setDrawColor(180, 180, 180).setLineWidth(0.5);
              return lineY2 + 8; // content start Y
          }

          // ── Watermark on each page ───────────────────────────────────────────
          function drawWatermark(doc, pageW, pageH) {
              if (!LOGO_BASE64) return;
              try {
                  const wmW = 260,
                      wmH = 260;
                  const wmX = (pageW - wmW) / 2;
                  const wmY = (pageH - wmH) / 2;

                  // jsPDF doesn't support native opacity for images, use GState
                  doc.saveGraphicsState();
                  const gState = new doc.GState({
                      opacity: 0.07
                  });
                  doc.setGState(gState);
                  doc.addImage(LOGO_BASE64, 'PNG', wmX, wmY, wmW, wmH);
                  doc.restoreGraphicsState();
              } catch (e) {
                  console.warn('Watermark error:', e);
              }
          }

          // ── Draw first page header ───────────────────────────────────────────
          const tableStartY = drawHeader(doc, 18);

          // ── Table ────────────────────────────────────────────────────────────
          doc.autoTable({
              html: '#salaryTable',
              startY: tableStartY,
              margin: {
                  left: mL,
                  right: mR,
                  top: tableStartY + 4,
                  bottom: 36
              },
              styles: {
                  fontSize: 7,
                  cellPadding: 3,
                  overflow: 'linebreak'
              },
              headStyles: {
                  fillColor: [26, 94, 168],
                  textColor: 255,
                  fontStyle: 'bold',
                  fontSize: 7,
                  halign: 'center'
              },
              footStyles: {
                  fillColor: [220, 232, 250],
                  textColor: [26, 46, 90],
                  fontStyle: 'bold',
                  fontSize: 7
              },
              alternateRowStyles: {
                  fillColor: [240, 246, 255]
              },
              showFoot: 'never',

              didParseCell: (data) => {
                  const lastCol = data.table.columns.length - 1;

                  if (data.column.index === lastCol) data.cell.text = '';
                  if (data.section === 'body' && data.column.index === lastCol - 1) {
                      if (data.cell.raw && data.cell.raw.querySelector && data.cell.raw.querySelector(
                              'button')) {
                          data.cell.text = '';
                      }
                  }
              },

              didDrawPage: (data) => {
                  const curPage = doc.internal.getCurrentPageInfo().pageNumber;
                  const totalPgs = doc.internal.getNumberOfPages();

                  // Draw watermark behind content
                  drawWatermark(doc, pageW, pageH);

                  // Repeat header on pages > 1
                  if (curPage > 1) {
                      drawHeader(doc, 12);
                  }

                  // Page number
                  doc.setFontSize(8).setFont('helvetica', 'normal').setTextColor(150);
                  doc.text(`Page ${curPage}`, pageW / 2, pageH - 10, {
                      align: 'center'
                  });
                  doc.setTextColor(0);
              },
          });

          // ── Final page: Total footer ─────────────────────────────────────────
          const finalY = doc.lastAutoTable.finalY + 12;
          const fH = 26;

          doc.setFillColor(26, 94, 168);
          doc.rect(mL, finalY, pageW - mL - mR, fH, 'F');

          doc.setFont('helvetica', 'bold').setFontSize(10).setTextColor(255);
          doc.text(
              `Total Payable Salary: ${total.toLocaleString('en-BD', { minimumFractionDigits: 2 })} Tk`,
              pageW / 2, finalY + 16, {
                  align: 'center'
              }
          );
          doc.setFont('helvetica', 'normal').setFontSize(8).setTextColor(180, 200, 230);
          doc.text(`Generated: ${gen}`, pageW - mR, finalY + fH - 5, {
              align: 'right'
          });

          // ── Company address footer ────────────────────────────────────────────
          const addrY = finalY + fH + 10;
          doc.setDrawColor(0, 174, 239).setLineWidth(1.5);
          doc.line(mL, addrY, pageW - mR, addrY);

          doc.setFont('helvetica', 'bold').setFontSize(7.5).setTextColor(26, 46, 90);
          doc.text('Operational Headquarter', mL, addrY + 12);
          doc.setFont('helvetica', 'normal').setTextColor(60, 60, 60);
          doc.text('Plot-No# 1248, Level # 4th Floor, Road No # 09, Avenue # 02', mL, addrY + 21);
          doc.text('Mirpur DOHS, Dhaka-1216, Bangladesh.', mL, addrY + 29);
          doc.text('Tel: +88-02-58070365  |  Fax: +88-02-58070365  |  Cell: +88017135655696', mL, addrY + 37);

          const col2 = pageW / 3;
          doc.setFont('helvetica', 'bold').setTextColor(26, 46, 90);
          doc.text('Factory Address:', col2, addrY + 12);
          doc.setFont('helvetica', 'normal').setTextColor(60, 60, 60);
          doc.text('Plot-83/84, Nagori Bazar, Kaliganj, Gazipur-1720', col2, addrY + 21);

          const col3 = (pageW * 2) / 3;
          doc.setFont('helvetica', 'bold').setTextColor(26, 46, 90);
          doc.text('Website & Email:', col3, addrY + 12);
          doc.setFont('helvetica', 'normal').setTextColor(60, 60, 60);
          doc.text('www.wtbl.com.bd', col3, addrY + 21);
          doc.text('info@wtbl.com.bd  |  hkfservice.inn@gmail.com', col3, addrY + 29);

          doc.setTextColor(0);
          doc.save(`Salary_PaySheet_${month}.pdf`);
      }
  </script>

@include('backend.pages.hrm.attendance.paysheet.script')
@endsection
