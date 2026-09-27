@extends('backend.layouts.master')
@section('title')
    Hrm - {{ $title }}
@endsection

@section('styles')
    <style>
        :root {
            --bg-page: #f0f2f7;
            --bg-card: #ffffff;
            --sidebar-bg: #0f1623;
            --accent: #3b6fff;
            --accent-light: #eef2ff;
            --accent-glow: rgba(59, 111, 255, .15);
            --present: #059669;
            --present-bg: #ecfdf5;
            --absent: #dc2626;
            --absent-bg: #fef2f2;
            --leave: #ea580c;
            --leave-bg: #fff7ed;
            --holiday: #7c3aed;
            --holiday-bg: #f3e8ff;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --border: #e5e7eb;
            --border-light: #f3f4f6;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, .07);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, .08);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --font: 'DM Sans', sans-serif;
        }

        body {
            font-family: var(--font);
        }

        .page-content {
            padding: 22px 18px;
            /* max-width: 1400px; */
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -.3px;
        }

        .page-subtitle {
            font-size: 12.5px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .btn-soft {
            border: 1.5px solid var(--border);
            background: #fff;
            color: var(--text-secondary);
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            font-weight: 600;
            padding: 7px 14px;
            transition: .15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-soft:hover {
            background: var(--border-light);
            color: var(--text-primary);
        }

        .btn-excel {
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            font-weight: 600;
            padding: 7px 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: .15s ease;
        }

        .btn-excel:hover {
            background: #128a3e;
            color: #fff;
        }

        /* KPI STRIP */
        .kpi-strip {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .kpi-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 14px 16px;
            box-shadow: var(--shadow-sm);
        }

        .kpi-label {
            font-size: 10.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: var(--text-muted);
        }

        .kpi-value {
            font-size: 22px;
            font-weight: 800;
            margin-top: 3px;
        }

        .kpi-value.present {
            color: var(--present);
        }

        .kpi-value.absent {
            color: var(--absent);
        }

        .kpi-value.leave {
            color: var(--leave);
        }

        .kpi-value.holiday {
            color: var(--holiday);
        }

        .kpi-value.duty {
            color: var(--accent);
        }

        /* FILTER PANEL */
        .filter-panel {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 18px;
        }

        .filter-panel-header {
            padding: 12px 20px;
            background: var(--sidebar-bg);
            color: rgba(255, 255, 255, .85);
            font-weight: 700;
            font-size: 12.5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        .filter-panel-body {
            padding: 16px 20px;
        }

        .filter-label {
            font-size: 10.5px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 5px;
            display: block;
        }

        .filter-input {
            font-size: 13px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--bg-page);
            width: 100%;
        }

        .filter-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
            outline: none;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1.5px solid var(--border) !important;
            border-radius: var(--radius-sm) !important;
            background: var(--bg-page) !important;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-size: 13px !important;
            line-height: 36px !important;
            padding-left: 12px !important;
            color: var(--text-primary) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        .select2-container {
            width: 100% !important;
        }

        /* TABLE CARD */
        .table-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            position: relative;
        }

        .table-card-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        .report-title {
            font-size: 15.5px;
            font-weight: 800;
            color: var(--text-primary);
        }

        .report-sub {
            font-size: 11.5px;
            color: var(--text-secondary);
            margin-top: 1px;
        }

        .table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        #reportTable {
            font-size: 12.5px;
            min-width: 1100px;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        #reportTable thead th {
            background: var(--sidebar-bg);
            color: #fff;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .4px;
            padding: 10px 12px;
            border: none;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        #reportTable tbody td {
            padding: 9px 12px;
            border-bottom: 1px solid var(--border-light);
            white-space: nowrap;
        }

        #reportTable tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        #reportTable tbody tr:hover {
            background: #f4f7ff;
        }

        .emp-name-cell .name {
            font-weight: 600;
            color: var(--text-primary);
        }

        .emp-name-cell .id {
            font-size: 10.5px;
            color: var(--text-muted);
        }

        .num-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 26px;
            padding: 3px 8px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
        }

        .num-present {
            background: var(--present-bg);
            color: var(--present);
        }

        .num-absent {
            background: var(--absent-bg);
            color: var(--absent);
        }

        .num-leave {
            background: var(--leave-bg);
            color: var(--leave);
        }

        .num-holiday {
            background: var(--holiday-bg);
            color: var(--holiday);
        }

        .num-duty {
            background: var(--accent-light);
            color: var(--accent);
        }

        .hours-cell {
            font-weight: 700;
            color: var(--text-primary);
        }

        .diff-pos {
            color: var(--present);
            font-weight: 700;
        }

        .diff-neg {
            color: var(--absent);
            font-weight: 700;
        }

        .incomplete-tag {
            font-size: 10.5px;
            color: var(--absent);
            background: var(--absent-bg);
            padding: 2px 7px;
            border-radius: 6px;
            cursor: help;
        }

        .no-tag {
            color: var(--text-muted);
            font-size: 12px;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state i {
            font-size: 38px;
            color: var(--text-muted);
            display: block;
            margin-bottom: 10px;
        }

        .empty-title {
            font-size: 14.5px;
            font-weight: 700;
            color: var(--text-secondary);
        }

        .empty-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        #loadingOverlay {
            display: none;
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, .75);
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        #loadingOverlay.active {
            display: flex;
        }

        .spinner {
            width: 34px;
            height: 34px;
            border: 3px solid var(--border);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .kpi-strip {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 576px) {
            .page-header {
                flex-direction: column;
            }

            .kpi-strip {
                grid-template-columns: repeat(2, 1fr);
            }

            .page-content {
                padding: 16px 10px;
            }
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: #fff;
            }

            .table-card {
                box-shadow: none !important;
                border: none !important;
            }

            #reportTable {
                min-width: 100% !important;
                font-size: 10.5px;
            }
        }
    </style>
@endsection

@section('admin-content')
    <div class="main-wrapper">
        <main class="page-content">

            {{-- HEADER --}}
            <div class="page-header no-print">
                <div>
                    <div class="page-title">Presence &amp; Absence Report</div>
                    <div class="page-subtitle" id="reportSub">Attendance summary with working hours analysis</div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn-soft" onclick="window.print()">
                        <i class="bi bi-printer-fill"></i>Print
                    </button>
                    <button class="btn-excel" id="btnExcel">
                        <i class="bi bi-file-earmark-excel-fill"></i>Excel
                    </button>
                </div>
            </div>

            {{-- KPI STRIP --}}
            {{-- <div class="kpi-strip no-print">
                <div class="kpi-card">
                    <div class="kpi-label">Employees</div>
                    <div class="kpi-value" id="kpiTotal">—</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Total Presence</div>
                    <div class="kpi-value present" id="kpiPresence">—</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Total Absence</div>
                    <div class="kpi-value absent" id="kpiAbsence">—</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Total Leave</div>
                    <div class="kpi-value leave" id="kpiLeave">—</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Holiday Days</div>
                    <div class="kpi-value holiday" id="kpiHoliday">—</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Holiday Duty</div>
                    <div class="kpi-value duty" id="kpiDuty">—</div>
                </div>
            </div> --}}

            {{-- FILTER PANEL --}}
            <div class="filter-panel no-print">
                <div class="filter-panel-header">
                    <span><i class="bi bi-funnel-fill me-2"></i>Filter Report</span>
                </div>
                <div class="filter-panel-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-6 col-md-3">
                            <label class="filter-label">Employee</label>
                            <select id="employeeFilter" class="filter-input" style="width:100%">
                                <option value="all">All Employees</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->id_card }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="filter-label">From Date</label>
                            <input type="date" id="startDate" class="form-control filter-input"
                                value="{{ $start }}">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="filter-label">To Date</label>
                            <input type="date" id="endDate" class="form-control filter-input"
                                value="{{ $end }}">
                        </div>
                        <div class="col-6 col-md-3 d-flex gap-2">
                            <button class="btn btn-primary flex-fill" id="btnFilter"
                                style="border-radius:8px; font-size:13px; font-weight:600;">
                                <i class="bi bi-search me-1"></i>Generate
                            </button>
                            <button class="btn-soft" id="btnReset" title="Reset filters">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLE CARD --}}
            <div class="table-card">
                <div id="loadingOverlay" class="active">
                    <div class="spinner"></div>
                </div>

                <div class="table-card-header">
                    <div>
                        <div class="report-title" id="reportTitle">Loading…</div>
                        <div class="report-sub" id="reportRangeSub"></div>
                    </div>
                </div>

                <div class="table-scroll">
                    <table id="reportTable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Employee</th>
                                <th class="text-center">Presence</th>
                                <th class="text-center">Absence</th>
                                <th class="text-center">Leave</th>
                                <th class="text-center">Holidays</th>
                                <th class="text-center">Holiday Duty</th>
                                <th class="text-center">Expected Hrs</th>
                                <th class="text-center">Worked Hrs</th>
                                <th class="text-center">Diff</th>
                                <th>Incomplete Days</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody id="reportBody"></tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        const REPORT_API = "{{ route('hrm.attendancelog.presence-absence-report-data') }}";
        let currentData = [];
        let currentMeta = {};

        $(document).ready(function() {

            $('#employeeFilter').select2({
                placeholder: 'All Employees',
                width: '100%',
                dropdownParent: $('body'),
            });

            fetchReport();

            $('#btnFilter').on('click', fetchReport);

            $('#btnReset').on('click', function() {
                $('#employeeFilter').val('all').trigger('change.select2');
                $('#startDate').val("{{ $start }}");
                $('#endDate').val("{{ $end }}");
                fetchReport();
            });

            $('#btnExcel').on('click', exportToExcel);
        });

        function fetchReport() {
            const s = $('#startDate').val();
            const e = $('#endDate').val();
            const empId = $('#employeeFilter').val() || 'all';

            if (!s || !e) return;

            $('#loadingOverlay').addClass('active');
            $('#reportTitle').text('Loading…');
            $('#reportBody').empty();

            fetch(`${REPORT_API}?start_date=${s}&end_date=${e}&employee_id=${empId}`)
                .then(r => r.json())
                .then(res => {
                    currentData = res.result ?? [];
                    currentMeta = res;
                    renderReport(res);
                    $('#loadingOverlay').removeClass('active');
                })
                .catch(() => {
                    $('#loadingOverlay').removeClass('active');
                    $('#reportBody').html(
                        `<tr><td colspan="12"><div class="empty-state">
                            <i class="bi bi-exclamation-triangle"></i>
                            <div class="empty-title">Failed to load data</div>
                            <div class="empty-sub">Please try again.</div>
                        </div></td></tr>`
                    );
                });
        }

        function renderReport(res) {
            const data = res.result ?? [];

            const rangeText = `${formatDate(res.start)} to ${formatDate(res.end)}`;
            $('#reportTitle').text('Presence and Absence Report');
            $('#reportRangeSub').text(`For the period of ${rangeText} · ${res.totalDays ?? 0} day(s)`);
            $('#reportSub').text(`Showing report for ${rangeText}`);

            updateKPIs(data);

            const tbody = $('#reportBody');
            tbody.empty();

            if (!data.length) {
                tbody.html(`<tr><td colspan="12"><div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <div class="empty-title">No records found</div>
                    <div class="empty-sub">Try a different date range or employee.</div>
                </div></td></tr>`);
                return;
            }

            data.forEach(row => {
                const diffClass = row.hoursDiff.startsWith('-') ? 'diff-neg' : 'diff-pos';
                const incompleteBadge = row.incompleteCount > 0 ?
                    `<span class="incomplete-tag" title="${esc(row.incompleteDays)}">
                         <i class="bi bi-exclamation-circle"></i> ${row.incompleteCount} day(s)
                       </span>` :
                    '<span class="no-tag">—</span>';

                tbody.append(`
                    <tr>
                        <td>${row.sl}</td>
                        <td>
                            <div class="emp-name-cell">
                                <div class="name">${esc(row.name)}</div>
                                <div class="id">EMP: ${esc(row.empId)}</div>
                            </div>
                        </td>
                        <td class="text-center"><span class="num-badge num-present">${row.presence}</span></td>
                        <td class="text-center"><span class="num-badge num-absent">${row.absence}</span></td>
                        <td class="text-center"><span class="num-badge num-leave">${row.leave}</span></td>
                        <td class="text-center"><span class="num-badge num-holiday">${row.holidays}</span></td>
                        <td class="text-center"><span class="num-badge num-duty">${row.holidayDuty}</span></td>
                        <td class="text-center hours-cell">${row.expectedHours}</td>
                        <td class="text-center hours-cell">${row.workedHours}</td>
                        <td class="text-center ${diffClass}">${row.hoursDiff}</td>
                        <td>${incompleteBadge}</td>
                        <td style="white-space:normal; font-size:11.5px; color:var(--text-secondary)">${esc(row.note) || '—'}</td>
                    </tr>
                `);
            });
        }

        function updateKPIs(data) {
            const total = data.length;
            const sum = key => data.reduce((a, r) => a + (r[key] || 0), 0);

            $('#kpiTotal').text(total);
            $('#kpiPresence').text(sum('presence'));
            $('#kpiAbsence').text(sum('absence'));
            $('#kpiLeave').text(sum('leave'));
            $('#kpiHoliday').text(total ? data[0].holidays : 0); // holiday count same for all (date-range based)
            $('#kpiDuty').text(sum('holidayDuty'));
        }

        function exportToExcel() {
            if (!currentData.length) {
                alert('No data to export.');
                return;
            }

            const rows = currentData.map(r => ({
                'SL': r.sl,
                'Employee ID': r.empId,
                'Name': r.name,
                'Presence': r.presence,
                'Absence (AB)': r.absence,
                'Leave': r.leave,
                'Holidays': r.holidays,
                'Holiday Duty': r.holidayDuty,
                'Expected Hours': r.expectedHours,
                'Worked Hours': r.workedHours,
                'Diff': r.hoursDiff,
                'Incomplete Days': r.incompleteDays || '—',
                'Note': r.note || '',
            }));

            const ws = XLSX.utils.json_to_sheet(rows);
            ws['!cols'] = [5, 12, 22, 10, 12, 8, 10, 12, 14, 13, 10, 22, 26].map(w => ({
                wch: w
            }));

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Presence-Absence Report');

            const s = $('#startDate').val();
            const e = $('#endDate').val();
            XLSX.writeFile(wb, `presence_absence_report_${s}_to_${e}.xlsx`);
        }

        function esc(str) {
            return String(str ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                '&quot;');
        }

        function formatDate(dateStr) {
            if (!dateStr) return '—';
            return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }
    </script>
@endsection
