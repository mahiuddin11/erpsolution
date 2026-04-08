@extends('backend.layouts.master')
@section('title')
    Hrm - {{ $title }}
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap"
        rel="stylesheet" />
    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        :root {
            --bg-page: #f0f2f7;
            --bg-card: #ffffff;
            --sidebar-bg: #0f1623;
            --accent: #3b6fff;
            --accent-light: #eef2ff;
            --accent-glow: rgba(59, 111, 255, 0.15);
            --present: #10b981;
            --present-bg: #ecfdf5;
            --late: #f59e0b;
            --late-bg: #fffbeb;
            --absent: #ef4444;
            --absent-bg: #fef2f2;
            --holiday-bg: #f3e8ff;
            --holiday: #7c3aed;
            --leave-bg: #fff7ed;
            --leave: #ea580c;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --border: #e5e7eb;
            --border-light: #f3f4f6;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.07);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 12px 40px rgba(0, 0, 0, 0.12);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;

            --font-body: 'DM Sans', sans-serif;
            --transition: 0.2s ease;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background: var(--bg-page);
            color: var(--text-primary);
        }

        .profile_information a {
            font-size: 18px;
            font-weight: 600;
            color: #fff;
            text-transform: uppercase;
            text-decoration: none;
        }

        nav.main-header ul li a {
            font-size: 18px;
            color: #333 !important;
            font-weight: 500 !important;
            text-decoration: none;
        }

        .page-content {
            padding: 24px 20px;
            max-width: 1500px;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .page-title {
            font-family: var(--font-display);
            font-size: 26px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        /* KPI */
        .kpi-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }

        .kpi-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: var(--shadow-sm);
            transition: box-shadow var(--transition);
        }

        .kpi-card:hover {
            box-shadow: var(--shadow-md);
        }

        .kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            flex-shrink: 0;
        }

        .kpi-icon.total {
            background: var(--accent-light);
            color: var(--accent);
        }

        .kpi-icon.present {
            background: var(--present-bg);
            color: var(--present);
        }

        .kpi-icon.late {
            background: var(--late-bg);
            color: var(--late);
        }

        .kpi-icon.absent {
            background: var(--absent-bg);
            color: var(--absent);
        }

        .kpi-value {
            font-family: var(--font-display);
            font-size: 30px;
            font-weight: 800;
            line-height: 1;
        }

        .kpi-label {
            font-size: 11.5px;
            color: var(--text-secondary);
            margin-top: 3px;
            font-weight: 500;
        }

        .kpi-sub {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        /* Filter */
        .filter-panel {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .filter-panel-header {
            padding: 13px 22px;
            background: var(--sidebar-bg);
            color: rgba(255, 255, 255, 0.85);
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 13px;
            display: flex;
            align-items: center;
        }

        .filter-panel-body {
            padding: 18px 22px;
        }

        .filter-label {
            font-size: 10.5px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 5px;
            display: block;
        }

        .filter-input {
            font-family: var(--font-body);
            font-size: 13px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            background: var(--bg-page);
            transition: border var(--transition), box-shadow var(--transition);
        }

        .filter-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
            outline: none;
        }

        /* ── Select2 theme override — match .filter-input style ── */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1.5px solid var(--border) !important;
            border-radius: var(--radius-sm) !important;
            background: var(--bg-page) !important;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text-primary) !important;
            font-family: var(--font-body) !important;
            font-size: 13px !important;
            line-height: 36px !important;
            padding-left: 12px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: var(--text-muted) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px var(--accent-glow) !important;
            outline: none !important;
        }

        .select2-dropdown {
            border: 1.5px solid var(--accent) !important;
            border-radius: var(--radius-sm) !important;
            box-shadow: var(--shadow-md) !important;
            font-family: var(--font-body) !important;
            font-size: 13px !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1.5px solid var(--border) !important;
            border-radius: 6px !important;
            padding: 6px 10px !important;
            font-family: var(--font-body) !important;
            font-size: 13px !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--accent) !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: var(--accent-light) !important;
            color: var(--accent) !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-secondary);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-weight: 500;
            font-size: 13px;
            padding: 7px 14px;
            transition: background var(--transition);
            cursor: pointer;
        }

        .btn-ghost:hover {
            background: var(--border-light);
            color: var(--text-primary);
        }

        .btn-export-excel {
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 500;
            font-size: 13px;
            padding: 7px 13px;
            transition: opacity var(--transition);
            cursor: pointer;
        }

        .btn-export-excel:hover {
            opacity: 0.85;
            color: #fff;
            background: #14c756;
        }

        .btn-export-pdf {
            background: #dc2626;
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 500;
            font-size: 13px;
            padding: 7px 13px;
            transition: opacity var(--transition);
            cursor: pointer;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear {
            cursor: pointer;
            float: right;
            font-weight: bold;
            margin-right: 0.1px;
            !important height: 26px;
            padding-right: 0px;
        }

        .btn-export-pdf:hover {
            opacity: 0.85;
            color: #fff;
            background: #f32727;
        }

        .result-count-badge {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            padding: 5px 12px;
            background: var(--border-light);
            border-radius: 20px;
            display: none;
        }

        /* Table Card */
        .table-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .table-card-header {
            padding: 16px 22px;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .table-card-title {
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 800;
            color: var(--text-primary);
        }

        .table-card-subtitle {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 1px;
        }

        /* Table */
        #attendanceTable {
            font-size: 13px;
            border-collapse: separate;
            border-spacing: 0;
        }

        #attendanceTable thead tr {
            background: var(--border-light);
        }

        #attendanceTable thead th {
            font-family: var(--font-body);
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: var(--text-secondary);
            padding: 11px 13px;
            border: none;
            white-space: nowrap;
            cursor: pointer;
            user-select: none;
            transition: color var(--transition);
        }

        #attendanceTable thead th:hover {
            color: var(--text-primary);
        }

        #attendanceTable thead th.sort-asc::after {
            content: ' ↑';
            color: var(--accent);
        }

        #attendanceTable thead th.sort-desc::after {
            content: ' ↓';
            color: var(--accent);
        }

        #attendanceTable tbody tr {
            border-bottom: 1px solid var(--border-light);
            transition: background var(--transition);
            animation: rowFade 0.22s ease both;
        }

        #attendanceTable tbody tr:hover {
            background: #fafbff;
        }

        #attendanceTable tbody td {
            padding: 11px 13px;
            border: none;
            vertical-align: middle;
        }

        @keyframes rowFade {
            from {
                opacity: 0;
                transform: translateX(-4px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .emp-cell {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .emp-avatar {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 11px;
            color: #fff;
            flex-shrink: 0;
        }

        .emp-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 13px;
        }

        .emp-email {
            font-size: 11px;
            color: var(--text-muted);
        }

        .id-badge {
            display: inline-block;
            background: var(--accent-light);
            color: var(--accent);
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 5px;
            letter-spacing: 0.4px;
        }

        .dept-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 9px;
            border-radius: 5px;
            background: var(--border-light);
            color: var(--text-secondary);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 4px 11px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .status-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
        }

        .status-present {
            background: var(--present-bg);
            color: var(--present);
        }

        .status-late {
            background: var(--late-bg);
            color: #b45309;
        }

        .status-absent {
            background: var(--absent-bg);
            color: var(--absent);
        }

        .status-holiday {
            background: var(--holiday-bg);
            color: var(--holiday);
        }

        .status-leave {
            background: var(--leave-bg);
            color: var(--leave);
        }

        .hours-cell {
            font-family: var(--font-display);
            font-weight: 700;
            color: var(--text-primary);
            font-size: 13px;
        }

        .time-cell {
            font-size: 12.5px;
            color: var(--text-secondary);
        }

        .time-cell strong {
            color: var(--text-primary);
            font-weight: 600;
        }

        .action-btn {
            background: var(--accent-light);
            color: var(--accent);
            border: none;
            border-radius: 6px;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            cursor: pointer;
            transition: background var(--transition);
        }

        .action-btn:hover {
            background: var(--accent);
            color: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state i {
            font-size: 40px;
            color: var(--text-muted);
            display: block;
            margin-bottom: 10px;
        }

        .empty-title {
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 700;
            color: var(--text-secondary);
        }

        .empty-sub {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Pagination */
        .table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 13px 20px;
            border-top: 1px solid var(--border-light);
            background: var(--border-light);
        }

        .page-info {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .pagination-wrap {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .page-btn {
            background: var(--bg-card);
            border: 1.5px solid var(--border);
            border-radius: 7px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all var(--transition);
        }

        .page-btn:hover:not(:disabled) {
            background: var(--accent-light);
            border-color: var(--accent);
            color: var(--accent);
        }

        .page-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .page-num {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            border: 1.5px solid var(--border);
            background: var(--bg-card);
            font-size: 12.5px;
            font-weight: 500;
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition);
        }

        .page-num.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
            font-weight: 700;
        }

        .page-num:hover:not(.active) {
            background: var(--accent-light);
            border-color: var(--accent);
            color: var(--accent);
        }

        .rows-per-page {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .rows-per-page select {
            border: 1.5px solid var(--border);
            border-radius: 6px;
            padding: 4px 7px;
            font-size: 12px;
            font-family: var(--font-body);
            color: var(--text-primary);
            background: var(--bg-card);
            cursor: pointer;
            outline: none;
        }

        /* Modal */
        .custom-modal {
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .modal-header-custom {
            padding: 20px 22px 16px;
            background: var(--sidebar-bg);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .modal-title-custom {
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 800;
            color: #fff;
        }

        .modal-subtitle-text {
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.4);
            margin-top: 2px;
        }

        .modal-close-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 7px;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            transition: background var(--transition);
            flex-shrink: 0;
        }

        .modal-close-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .modal-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .modal-detail-item label {
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: var(--text-muted);
            display: block;
            margin-bottom: 3px;
        }

        .modal-detail-item .value {
            font-weight: 600;
            font-size: 13.5px;
            color: var(--text-primary);
        }

        /* Toast */
        .custom-toast {
            background: var(--sidebar-bg);
            border: none;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            min-width: 230px;
        }

        .custom-toast .toast-body {
            color: rgba(255, 255, 255, 0.85);
            font-size: 12.5px;
            font-weight: 500;
            padding: 13px 15px;
        }

        .custom-toast .btn-close {
            filter: invert(1) brightness(0.7);
        }

        /* Loading */
        #loadingOverlay {
            display: none;
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.75);
            z-index: 10;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-lg);
        }

        #loadingOverlay.active {
            display: flex;
        }

        .spinner {
            width: 36px;
            height: 36px;
            border: 3px solid var(--border);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 992px) {
            .kpi-strip {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .kpi-strip {
                grid-template-columns: repeat(2, 1fr);
            }

            .modal-detail-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ===== PRINT STYLES ===== */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .filter-panel,
            .page-header,
            .kpi-strip,
            .table-card-header,
            .table-footer,
            .action-btn,
            .toast-container,
            #detailModal,
            nav,
            footer,
            aside,
            .main-sidebar,
            .main-header,
            #loadingOverlay,
            .modal {
                display: none !important;
            }

            body {
                background: #fff !important;
                margin: 0;
                padding: 0;
                font-size: 11px;
                font-family: Arial, Helvetica, sans-serif;
            }

            .page-content {
                padding: 16px !important;
                max-width: 100% !important;
            }

            .main-wrapper {
                margin: 0 !important;
            }

            .table-card {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }

            .print-page-header {
                display: block !important;
            }

            /* ── Print header top bar ──
                           To change header border color: edit border-bottom value below */
            .print-ph-top {
                display: flex !important;
                justify-content: space-between;
                align-items: flex-start;
                padding-bottom: 8px;
                border-bottom: 2.5px solid #0f1623;
                /* ◄ HEADER BORDER COLOR — change here */
                margin-bottom: 6px;
            }

            .print-co-name {
                font-size: 20px;
                font-weight: 900;
                color: #0f1623;
                font-family: Arial, sans-serif;
                letter-spacing: -0.5px;
            }

            .print-co-sub {
                font-size: 10.5px;
                color: #555;
                margin-top: 2px;
            }

            .print-rpt-title {
                font-size: 14px;
                font-weight: 900;
                color: #0f1623;
                text-align: right;
                font-family: Arial, sans-serif;
            }

            .print-rpt-meta {
                font-size: 9.5px;
                color: #555;
                text-align: right;
                margin-top: 3px;
            }

            .print-sum-bar {
                display: flex !important;
                border: 1px solid #ddd;
                border-radius: 5px;
                overflow: hidden;
                margin-top: 10px;
                margin-bottom: 14px;
            }

            /* ── Summary line (replaces dashboard cards) ──
                           To change the header background color: edit print-ph-top border-bottom color
                           To change summary text colors: edit .ps-present, .ps-late, .ps-absent strong colors below */
            .print-summary-line {
                display: flex !important;
                align-items: center;
                gap: 10px;
                padding: 6px 0 0;
                font-size: 10px;
                color: #444;
                margin-top: 6px;
                flex-wrap: wrap;
            }

            .print-summary-line strong {
                font-weight: 800;
            }

            .ps-divider {
                color: #bbb;
            }

            /* ▼ CHANGE THESE to adjust summary text colors ▼ */
            .ps-present strong {
                color: #059669;
            }

            /* Present count — green */
            .ps-late strong {
                color: #b45309;
            }

            /* Late count    — amber */
            .ps-absent strong {
                color: #dc2626;
            }

            /* Absent count  — red   */
            .ps-working strong {
                color: #2563eb;
            }

            /* Working days  — blue  */
            /* ▲ ─────────────────────────────────────────── ▲ */

            /* ── Table header color note ──
                           To change table header background: edit the background value below */
            #attendanceTable {
                font-size: 9.5px !important;
                width: 100%;
                border-collapse: collapse;
            }

            #attendanceTable thead th {
                background: #0f1623 !important;
                /* ◄ TABLE HEADER COLOR — change here */
                color: #fff !important;
                padding: 7px 8px !important;
                font-size: 8.5px !important;
                border: none !important;
            }

            #attendanceTable tbody td {
                padding: 6px 8px !important;
                border-bottom: 1px solid #eee !important;
            }

            #attendanceTable tbody tr:nth-child(even) td {
                background: #f8f9fa !important;
            }

            .emp-avatar,
            .id-badge {
                display: none !important;
            }

            .emp-cell {
                display: block !important;
            }

            .emp-name {
                font-size: 9.5px !important;
            }

            .emp-email {
                font-size: 8.5px !important;
            }

            .status-badge {
                padding: 2px 5px !important;
                font-size: 8.5px !important;
                border-radius: 3px !important;
            }

            .status-badge::before {
                display: none;
            }

            /* ── Print footer with signature lines ── */
            .print-page-footer {
                display: block !important;
                margin-top: 30px;
                padding-top: 0;
                border-top: none;
                font-size: 9.5px;
                color: #444;
            }

            .print-footer-top {
                display: flex !important;
                justify-content: space-between;
                align-items: center;
                font-size: 8.5px;
                color: #888;
                border-top: 1px solid #ddd;
                padding-top: 6px;
                margin-bottom: 22px;
            }

            /* Signature section */
            .print-sig-row {
                display: flex !important;
                justify-content: space-between;
                align-items: flex-end;
                gap: 20px;
                margin-top: 8px;
            }

            .print-sig-box {
                flex: 1;
                text-align: center;
            }

            .print-sig-line {
                border-top: 1px solid #333;
                padding-top: 5px;
                font-size: 9px;
                color: #333;
                margin-top: 32px;
                /* space above line for actual signature */
            }

            .print-sig-title {
                font-size: 8px;
                color: #777;
                margin-top: 2px;
            }
        }

        .print-page-header {
            display: none;
        }

        .print-page-footer {
            display: none;
        }
    </style>
@endsection

@section('admin-content')
    <div class="main-wrapper" id="mainWrapper">
        <main class="page-content">

            {{-- PRINT-ONLY HEADER --}}
            <div class="print-page-header">
                <div class="print-ph-top">
                    <div>
                        <div class="print-co-name">{{ 'WATER TECHNOLOGY BD LIMITED' }}</div>
                        <div class="print-co-sub">HR &amp; Attendance Management System</div>
                    </div>
                    <div>
                        <div class="print-rpt-title">Attendance Report</div>
                        <div class="print-rpt-meta" id="printRangeMeta">—</div>
                        <div class="print-rpt-meta">Printed: <span id="printedAtLabel"></span></div>
                    </div>
                </div>
                {{-- Single-line compact summary — like a PDF sub-header, NOT dashboard cards --}}
                <div class="print-summary-line">
                    <span>Total: <strong id="ps-total">0</strong></span>
                    <span class="ps-divider">|</span>
                    <span class="ps-present">Present: <strong id="ps-present">0</strong></span>
                    <span class="ps-divider">|</span>
                    <span class="ps-late">Late: <strong id="ps-late">0</strong></span>
                    <span class="ps-divider">|</span>
                    <span class="ps-absent">Absent: <strong id="ps-absent">0</strong></span>
                    <span class="ps-divider">|</span>
                    <span class="ps-leave">Leave: <strong id="ps-leave">0</strong></span>
                    <span class="ps-divider">|</span>
                    <span class="ps-holiday">Holiday: <strong id="ps-holiday">0</strong></span>
                    <span class="ps-divider">|</span>
                    <span class="ps-working">Working Days: <strong id="ps-working">0</strong></span>
                </div>
            </div>

            {{-- PAGE HEADER --}}
            <div class="page-header">
                <div>
                    <h1 class="page-title">Attendance Log</h1>
                    <p class="page-subtitle" id="currentDateLabel">Loading…</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-outline-secondary btn-sm" onclick="doPrint()">
                        <i class="bi bi-printer-fill me-1"></i>Print
                    </button>
                    <button class="btn btn-export-excel btn-sm" onclick="exportToExcel()">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i>Excel
                    </button>
                    <button class="btn btn-export-pdf btn-sm" onclick="exportToPDF()">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i>PDF
                    </button>
                </div>
            </div>

            {{-- KPI STRIP --}}
            <div class="kpi-strip">
                <div class="kpi-card">
                    <div class="kpi-icon total"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="kpi-value" id="kpiTotalEmp">—</div>
                        <div class="kpi-label">Total Employees</div>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon present"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="kpi-value" id="kpiPresent">—</div>
                        <div class="kpi-label">Present Today</div>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon late"><i class="bi bi-clock-fill"></i></div>
                    <div>
                        <div class="kpi-value" id="kpiLate">—</div>
                        <div class="kpi-label">Late Today</div>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon absent"><i class="bi bi-x-circle-fill"></i></div>
                    <div>
                        <div class="kpi-value" id="kpiAbsent">—</div>
                        <div class="kpi-label">Absent Today</div>
                    </div>
                </div>
            </div>

            {{-- FILTER PANEL --}}
            <div class="filter-panel">
                <div class="filter-panel-header">
                    <i class="bi bi-funnel-fill me-2"></i>Search &amp; Filter Attendance
                </div>
                <div class="filter-panel-body">
                    <div class="row g-3 align-items-end">

                        {{-- Employee select (Select2) --}}
                        <div class="col-12 col-md-3">
                            <label class="filter-label">Employee</label>
                            {{-- Select2 needs a plain <select>, NOT inside search-input-wrap --}}
                            <select id="searchInput" class="filter-input" style="width:100%">
                                <option value="">All Employees</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-2">
                            <label class="filter-label">Status</label>
                            <select class="form-select filter-input" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="Present">Present</option>
                                <option value="Late">Late</option>
                                <option value="Absent">Absent</option>
                                <option value="Holiday">Holiday</option>
                                <option value="Leave">Leave</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="filter-label">Month</label>
                            <input type="month" class="form-control filter-input" id="monthFilter" />
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="filter-label">From Date</label>
                            <input type="date" class="form-control filter-input" id="startDate" />
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="filter-label">To Date</label>
                            <input type="date" class="form-control filter-input" id="endDate" />
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 d-flex gap-2 flex-wrap align-items-center">
                            <button class="btn btn-ghost" onclick="resetFilters()">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </button>
                            <span class="result-count-badge" id="resultCount"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLE CARD --}}
            <div class="table-card" style="position:relative;">
                <div id="loadingOverlay" class="active">
                    <div class="spinner"></div>
                </div>

                <div class="table-card-header">
                    <div>
                        <h2 class="table-card-title">Attendance Log</h2>
                        <p class="table-card-subtitle" id="tableSubtitle">Loading records…</p>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <button class="btn btn-sm btn-ghost" onclick="refreshData()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="attendanceTable" class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th data-col="empId">Emp ID</th>
                                <th data-col="name">Employee</th>
                                <th data-col="offictime">Office Time</th>
                                <th data-col="date">Date</th>
                                <th data-col="checkIn">Check-In</th>
                                <th data-col="checkOut">Check-Out</th>
                                <th data-col="hours">Hours</th>
                                <th data-col="overtime">Overtime</th>
                                <th data-col="status">Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>

                {{-- Print footer with signature lines (screen hidden, print visible) --}}
                <div class="print-page-footer">
                    <div class="print-footer-top">
                        <span>{{ config('app.name', 'Company') }} — Confidential</span>
                        <span id="printFooterRange"></span>
                        <span>HR Attendance System</span>
                    </div>
                    {{-- Signature row --}}
                    <div class="print-sig-row">
                        <div class="print-sig-box">
                            <div class="print-sig-line">Employee Signature</div>
                            <div class="print-sig-title">Verified by Employee</div>
                        </div>
                        <div class="print-sig-box">
                            <div class="print-sig-line">HR Manager Signature</div>
                            <div class="print-sig-title">HR Department</div>
                        </div>
                        <div class="print-sig-box">
                            <div class="print-sig-line">Authorized Signature</div>
                            <div class="print-sig-title">Management</div>
                        </div>
                    </div>
                </div>

                <div class="table-footer">
                    <div class="page-info" id="pageInfo">Showing 0 of 0 records</div>
                    <div class="pagination-wrap">
                        <button class="page-btn" id="prevPage" onclick="changePage(-1)"><i
                                class="bi bi-chevron-left"></i></button>
                        <div class="page-numbers d-flex gap-1" id="pageNumbers"></div>
                        <button class="page-btn" id="nextPage" onclick="changePage(1)"><i
                                class="bi bi-chevron-right"></i></button>
                    </div>
                    <div class="rows-per-page">
                        Rows:
                        <select id="rowsPerPage" onchange="changeRowsPerPage()">
                            <option value="30" selected>30</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

        </main>
    </div>

    {{-- DETAIL MODAL --}}
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal">
                <div class="modal-header-custom">
                    <div>
                        <h5 class="modal-title-custom">Attendance Detail</h5>
                        <p class="modal-subtitle-text">Full attendance record</p>
                    </div>
                    <button type="button" class="modal-close-btn" data-bs-dismiss="modal"><i
                            class="bi bi-x-lg"></i></button>
                </div>
                <div class="modal-body p-4" id="modalBody"></div>
            </div>
        </div>
    </div>

    {{-- TOAST --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast custom-toast align-items-center" role="alert">
            <div class="d-flex">
                <div class="toast-body" id="toastBody">Done.</div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Load order matters: jQuery → Select2 → Bootstrap → our script --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

    <script>
        /* ═══════════════════════════════════════════════════
               STATE
               ═══════════════════════════════════════════════════ */
        let allData = [];
        let filteredData = [];
        let currentPage = 1;
        let rowsPerPage = 31;
        let sortCol = 'date';
        let sortDir = 'desc';

        const ATTENDANCE_API = "{{ route('hrm.attendancelog.log') }}";

        /* ═══════════════════════════════════════════════════
           INIT  — single DOMContentLoaded, nothing nested
           ═══════════════════════════════════════════════════ */
        $(document).ready(function() {

            /* 1. Date labels */
            const today = new Date();
            const todayStr = toYMD(today);
            document.getElementById('currentDateLabel').textContent =
                today.toLocaleDateString('en-GB', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            document.getElementById('startDate').value = todayStr;
            document.getElementById('endDate').value = todayStr;

            /* 2. Init Select2 ONCE here — before any data loads */
            $('#searchInput').select2({
                placeholder: 'All Employees',
                allowClear: true,
                width: '100%',
                dropdownParent: $('body'), // fix: dropdown renders outside overflow:hidden containers
            });

            /* 3. Select2 change → client-side filter only, NO server call */
            $('#searchInput').on('change', function() {
                currentPage = 1;
                applyClientFilters();
            });

            /* 4. Status filter → client-side only */
            $('#statusFilter').on('change', function() {
                currentPage = 1;
                applyClientFilters();
            });

            /* 5. Month picker → sets date range + server fetch
                  new Date(year, monthIndex, 0) gives last day of previous month
                  monthIndex is 0-based, so for April (04) we pass 4 → last day of April  */
            $('#monthFilter').on('change', function() {
                const val = $(this).val();
                if (!val) return;
                const parts = val.split('-');
                const year = parseInt(parts[0], 10);
                const month = parseInt(parts[1], 10); // 1-based (April = 4)
                const mm = String(month).padStart(2, '0');
                const start = `${year}-${mm}-01`;
                // new Date(year, month, 0) → month is 1-based here → gives correct last day
                const lastDay = new Date(year, month, 0); // e.g. new Date(2025, 4, 0) = Apr 30
                const end = toYMD(lastDay);
                $('#startDate').val(start);
                $('#endDate').val(end);
                currentPage = 1;
                fetchData(start, end);
            });

            /* 6. Date range → server fetch when both dates filled and valid */
            $('#startDate, #endDate').on('change', function() {
                const s = $('#startDate').val();
                const e = $('#endDate').val();
                if (s && e && s <= e) {
                    $('#monthFilter').val('');
                    currentPage = 1;
                    fetchData(s, e);
                }
            });

            /* 7. Sort headers */
            setupSortHeaders();

            /* 8. Fetch today's KPI counters */
            fetchTodayKPIs(todayStr);

            /* 9. Fetch today's table data */
            fetchData(todayStr, todayStr);
        });

        /* ═══════════════════════════════════════════════════
           HELPER
           ═══════════════════════════════════════════════════ */
        function toYMD(d) {
            return d.toISOString().split('T')[0];
        }

        /* ═══════════════════════════════════════════════════
           KPI  — always fetched for TODAY, never changes with
                  the date-range filter
           ═══════════════════════════════════════════════════ */
        function fetchTodayKPIs(todayStr) {
            fetch(`${ATTENDANCE_API}?start_date=${todayStr}&end_date=${todayStr}`)
                .then(r => r.json())
                .then(res => {
                    const data = res.result ?? res; // support both {result:[]} and []
                    const totalEmployees = res.totalemployee ?? '0';
                    const present = res.presentEmploye ?? '0';
                    // const present = data.filter(r => r.status == 'Present' || r.status == 'Late').length;
                    const late = data.filter(r => r.status == 'Late').length;
                    // const absent = data.filter(r => r.status == 'Absent').length;
                    const absent = (totalEmployees - present) ?? '0';



                    $('#kpiTotalEmp').text(totalEmployees);
                    $('#kpiPresent').text(present);
                    $('#kpiLate').text(late);
                    $('#kpiAbsent').text(absent);
                })
                .catch(() => {
                    $('#kpiTotalEmp, #kpiPresent, #kpiLate, #kpiAbsent').text('—');
                });
        }

        /* ═══════════════════════════════════════════════════
           SERVER FETCH — called only when date range changes
           ═══════════════════════════════════════════════════ */
        function fetchData(start, end) {
            showLoading(true);
            fetch(`${ATTENDANCE_API}?start_date=${start}&end_date=${end}`)
                .then(r => r.json())
                .then(res => {
                    allData = res.result ?? res; // support both response shapes
                    populateEmployeeSelect(); // rebuild dropdown from fresh data
                    applyClientFilters(); // apply current filter selections
                    showLoading(false);
                })
                .catch(err => {
                    console.error(err);
                    showLoading(false);
                    showToast('Failed to load data.');
                });
        }

        function showLoading(state) {
            const el = document.getElementById('loadingOverlay');
            state ? el.classList.add('active') : el.classList.remove('active');
        }

        /* ═══════════════════════════════════════════════════
           POPULATE EMPLOYEE SELECT2 DROPDOWN
           Called after every server fetch. Remembers current
           selection and restores it if still valid.
           ═══════════════════════════════════════════════════ */
        function populateEmployeeSelect() {
            const $sel = $('#searchInput');
            const prevVal = $sel.val(); // remember what was selected

            // Destroy and re-init to avoid stale options
            $sel.empty().append('<option value="">All Employees</option>');

            // Unique employees sorted by name
            const unique = [...new Map(allData.map(e => [e.empId, e])).values()]
                .sort((a, b) => a.name.localeCompare(b.name));

            unique.forEach(emp => {
                $sel.append(new Option(`${emp.name}  (${emp.empId})`, emp.empId));
            });

            // Restore previous selection if it still exists in the new data
            if (prevVal && unique.some(e => e.empId === prevVal)) {
                $sel.val(prevVal);
            } else {
                $sel.val('');
            }

            $sel.trigger('change.select2'); // refresh Select2 display without firing our filter handler
        }

        /* ═══════════════════════════════════════════════════
           CLIENT-SIDE FILTER
           Only employee + status — no page reload, no fetch
           ═══════════════════════════════════════════════════ */
        function applyClientFilters() {
            const empId = $('#searchInput').val() || '';
            const status = $('#statusFilter').val() || '';

            filteredData = allData.filter(rec => {
                const mEmp = !empId || rec.empId === empId;
                const mStatus = !status || rec.status === status;
                return mEmp && mStatus;
            });

            currentPage = 1;
            renderTable();
            updatePrintSummary();

            const badge = document.getElementById('resultCount');
            if (filteredData.length < allData.length) {
                badge.textContent = `${filteredData.length} result${filteredData.length !== 1 ? 's' : ''}`;
                badge.style.display = 'inline-flex';
            } else {
                badge.style.display = 'none';
            }
        }

        /* ═══════════════════════════════════════════════════
           RESET — clears all filters, goes back to today
           Does NOT cause page reload
           ═══════════════════════════════════════════════════ */
        function resetFilters() {
            // Reset Select2 cleanly
            $('#searchInput').val('').trigger('change.select2');
            $('#statusFilter').val('');
            $('#monthFilter').val('');

            const today = toYMD(new Date());
            $('#startDate').val(today);
            $('#endDate').val(today);
            $('#resultCount').hide();

            currentPage = 1;
            fetchData(today, today);
        }

        function refreshData() {
            const s = $('#startDate').val();
            const e = $('#endDate').val();
            const today = toYMD(new Date());
            fetchTodayKPIs(today);
            fetchData(s, e);
            showToast('Data refreshed!');
        }

        /* ═══════════════════════════════════════════════════
           PRINT SUMMARY
           ═══════════════════════════════════════════════════ */
       

        function updatePrintSummary() {
            const total = filteredData.length;

            // Actual present = Present + Late
            const actualPresent = filteredData.filter(r => r.status === 'Present' || r.status === 'Late').length;
            const late = filteredData.filter(r => r.status === 'Late').length;
            const absent = filteredData.filter(r => r.status === 'Absent').length;
            const leave = filteredData.filter(r => r.status === 'Leave').length;
            const holiday = filteredData.filter(r => r.status === 'Holiday').length;
            // Working days (exclude absent and holiday)
            const wDays = [...new Set(
                filteredData.filter(r => r.status !== 'Absent' && r.status !== 'Holiday' ).map(r => r.date)
            )].length;

            $('#ps-total').text(total);
            $('#ps-present').text(actualPresent); // now includes Late
            $('#ps-late').text(late);
            $('#ps-absent').text(absent);
            $('#ps-leave').text(leave);
            $('#ps-holiday').text(holiday);
            $('#ps-wDays').text(wDays);

            const s = $('#startDate').val();
            const e = $('#endDate').val();
            const rangeText = s === e ? `Date: ${formatDate(s)}` : `Period: ${formatDate(s)} – ${formatDate(e)}`;
            $('#printRangeMeta').text(rangeText);
            $('#printFooterRange').text(rangeText);
            $('#printedAtLabel').text(new Date().toLocaleString());
        }

        /* ═══════════════════════════════════════════════════
           RENDER TABLE
           ═══════════════════════════════════════════════════ */
        function renderTable() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';

            const s = $('#startDate').val();
            const e = $('#endDate').val();
            document.getElementById('tableSubtitle').textContent =
                s === e ? `Records for ${formatDate(s)}` : `Records from ${formatDate(s)} to ${formatDate(e)}`;

            if (filteredData.length === 0) {
                tbody.innerHTML = `<tr><td colspan="10"><div class="empty-state">
            <i class="bi bi-search"></i>
            <div class="empty-title">No records found</div>
            <div class="empty-sub">Try adjusting your filters or date range.</div>
        </div></td></tr>`;
                updatePagination();
                updatePageInfo();
                return;
            }

            const sorted = [...filteredData].sort((a, b) => {
                let av = a[sortCol] ?? '',
                    bv = b[sortCol] ?? '';
                if (sortDir === 'asc') return av > bv ? 1 : av < bv ? -1 : 0;
                return av < bv ? 1 : av > bv ? -1 : 0;
            });

            const startIdx = (currentPage - 1) * rowsPerPage;
            sorted.slice(startIdx, startIdx + rowsPerPage).forEach((rec, idx) => {
                const tr = document.createElement('tr');
                tr.style.animationDelay = `${idx * 0.02}s`;
                tr.innerHTML = `
            <td><span class="id-badge">${esc(rec.empId)}</span></td>
            <td>
                <div class="emp-cell">
                    <div class="emp-avatar" style="background:${esc(rec.color)}">${esc(rec.initials)}</div>
                    <div>
                        <div class="emp-name">${esc(rec.name)}</div>
                        <div class="emp-email">${esc(rec.email)}</div>
                    </div>
                </div>
            </td>
            <td><span class="dept-badge">${esc(rec.offictime ?? '—')}</span></td>
            <td><span class="time-cell"><strong>${formatDate(rec.date)}</strong></span></td>
            <td><span class="time-cell">${esc(rec.checkIn)}</span></td>
            <td><span class="time-cell">${esc(rec.checkOut)}</span></td>
            <td><span class="hours-cell">${esc(rec.hours)}</span></td>
            <td><span class="hours-cell">${esc(rec.overtime)}</span></td>
            <td>${statusBadge(rec.status)}</td>
            <td>
                <button class="action-btn" title="View details"
                    onclick="viewDetail('${esc(rec.empId)}','${esc(rec.date)}')">
                    <i class="bi bi-eye-fill"></i>
                </button>
            </td>`;
                tbody.appendChild(tr);
            });

            updatePagination();
            updatePageInfo();
        }

        /* ═══════════════════════════════════════════════════
           HELPERS
           ═══════════════════════════════════════════════════ */
        function esc(str) {
            return String(str ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                '&quot;');
        }

        function statusBadge(status) {
            const map = {
                Present: 'status-present',
                Late: 'status-late',
                Absent: 'status-absent',
                Holiday: 'status-holiday',
                Leave: 'status-leave'
            };
            return `<span class="status-badge ${map[status]||'status-absent'}">${esc(status)}</span>`;
        }

        function formatDate(dateStr) {
            if (!dateStr || dateStr === '—') return '—';
            return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-GB', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
        }

        /* ═══════════════════════════════════════════════════
           SORT
           ═══════════════════════════════════════════════════ */
        function setupSortHeaders() {
            document.querySelectorAll('#attendanceTable thead th[data-col]').forEach(th => {
                th.addEventListener('click', () => {
                    const col = th.dataset.col;
                    if (sortCol === col) sortDir = sortDir === 'asc' ? 'desc' : 'asc';
                    else {
                        sortCol = col;
                        sortDir = 'asc';
                    }
                    document.querySelectorAll('#attendanceTable thead th').forEach(t => t.classList.remove(
                        'sort-asc', 'sort-desc'));
                    th.classList.add(sortDir === 'asc' ? 'sort-asc' : 'sort-desc');
                    renderTable();
                });
            });
        }

        /* ═══════════════════════════════════════════════════
           PAGINATION
           ═══════════════════════════════════════════════════ */
        function updatePageInfo() {
            const total = filteredData.length;
            const s = total === 0 ? 0 : (currentPage - 1) * rowsPerPage + 1;
            const e = Math.min(currentPage * rowsPerPage, total);
            document.getElementById('pageInfo').textContent = `Showing ${s}–${e} of ${total} records`;
        }

        function updatePagination() {
            const totalPages = Math.ceil(filteredData.length / rowsPerPage) || 1;
            const nums = document.getElementById('pageNumbers');
            nums.innerHTML = '';
            const max = 5;
            let sp = Math.max(1, currentPage - Math.floor(max / 2));
            let ep = Math.min(totalPages, sp + max - 1);
            if (ep - sp + 1 < max) sp = Math.max(1, ep - max + 1);
            for (let p = sp; p <= ep; p++) {
                const btn = document.createElement('button');
                btn.className = `page-num ${p === currentPage ? 'active' : ''}`;
                btn.textContent = p;
                btn.onclick = () => {
                    currentPage = p;
                    renderTable();
                };
                nums.appendChild(btn);
            }
            document.getElementById('prevPage').disabled = currentPage === 1;
            document.getElementById('nextPage').disabled = currentPage >= totalPages;
        }

        function changePage(dir) {
            const totalPages = Math.ceil(filteredData.length / rowsPerPage);
            currentPage = Math.max(1, Math.min(currentPage + dir, totalPages));
            renderTable();
        }

        function changeRowsPerPage() {
            rowsPerPage = parseInt(document.getElementById('rowsPerPage').value);
            currentPage = 1;
            renderTable();
        }

        /* ═══════════════════════════════════════════════════
           DETAIL MODAL
           ═══════════════════════════════════════════════════ */
        function viewDetail(empId, date) {
            const rec = allData.find(r => r.empId === empId && r.date === date);
            if (!rec) {
                showToast('Record not found.');
                return;
            }
            const initials = rec.initials || rec.name.split(' ').map(n => n[0]).join('').toUpperCase();
            document.getElementById('modalBody').innerHTML = `
        <div class="text-center mb-4">
            <div style="width:52px;height:52px;border-radius:12px;background:${rec.color};display:flex;
                align-items:center;justify-content:center;color:#fff;font-family:var(--font-display);
                font-weight:800;font-size:17px;margin:0 auto 8px">${initials}</div>
            <div style="font-family:var(--font-display);font-weight:800;font-size:16px">${esc(rec.name)}</div>
            <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px">${esc(rec.email)}</div>
            <div class="mt-2">${statusBadge(rec.status)}</div>
        </div>
        <div class="modal-detail-grid">
            <div class="modal-detail-item"><label>Employee ID</label>
                <div class="value"><span class="id-badge">${esc(rec.empId)}</span></div></div>
            <div class="modal-detail-item"><label>Office Start</label>
                <div class="value"><span class="dept-badge">${esc(rec.offictime ?? '—')}</span></div></div>
            <div class="modal-detail-item"><label>Date</label>
                <div class="value">${formatDate(rec.date)}</div></div>
            <div class="modal-detail-item"><label>Total Hours</label>
                <div class="value hours-cell">${esc(rec.hours)}</div></div>
            <div class="modal-detail-item"><label>Check-In</label>
                <div class="value">${esc(rec.checkIn)}</div></div>
            <div class="modal-detail-item"><label>Check-Out</label>
                <div class="value">${esc(rec.checkOut)}</div></div>
            <div class="modal-detail-item"><label>Overtime</label>
                <div class="value hours-cell">${esc(rec.overtime)}</div></div>
            <div class="modal-detail-item"><label>Department</label>
                <div class="value">${esc(rec.dept ?? '—')}</div></div>
        </div>`;
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        }

        /* ═══════════════════════════════════════════════════
           EXPORT: EXCEL
           ═══════════════════════════════════════════════════ */
        function exportToExcel() {
            if (!filteredData.length) {
                showToast('No data to export.');
                return;
            }
            const rows = filteredData.map(r => ({
                'Emp ID': r.empId,
                'Name': r.name,
                'Email': r.email,
                'Office Time': r.offictime ?? '',
                'Date': r.date,
                'Check-In': r.checkIn,
                'Check-Out': r.checkOut,
                'Hours': r.hours,
                'Overtime': r.overtime,
                'Status': r.status,
            }));
            const ws = XLSX.utils.json_to_sheet(rows);
            ws['!cols'] = [10, 22, 28, 13, 13, 12, 12, 10, 10, 10].map(w => ({
                wch: w
            }));
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Attendance');
            XLSX.writeFile(wb, `attendance_${toYMD(new Date())}.xlsx`);
            showToast('Excel exported!');
        }

        /* ═══════════════════════════════════════════════════
           EXPORT: PDF
           ═══════════════════════════════════════════════════ */
        function exportToPDF() {
            if (!filteredData.length) {
                showToast('No data to export.');
                return;
            }
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF({
                orientation: 'landscape',
                unit: 'mm',
                format: 'a4'
            });
            const s = $('#startDate').val();
            const e = $('#endDate').val();
            const present = filteredData.filter(r => r.status === 'Present').length;
            const late = filteredData.filter(r => r.status === 'Late').length;
            const absent = filteredData.filter(r => r.status === 'Absent').length;
            const holiday = filteredData.filter(r => r.status === 'Holiday').length;
            const leave = filteredData.filter(r => r.status === 'Leave').length;
            const company = '{{ 'WATER TECHNOLOGY BD LIMITED' }}';

            doc.setFillColor(15, 22, 35);
            doc.rect(0, 0, 297, 28, 'F');
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(16);
            doc.setTextColor(255, 255, 255);
            doc.text(company, 14, 12);
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(8.5);
            doc.setTextColor(170, 185, 210);
            doc.text('HR & Attendance Management System', 14, 18);
            doc.text(`Period: ${formatDate(s)} – ${formatDate(e)}   |   Generated: ${new Date().toLocaleString()}`, 14, 24);
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(10);
            doc.setTextColor(255, 255, 255);
            doc.text('Attendance Report', 297 - 14, 11, {
                align: 'right'
            });
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(7.5);
            doc.setTextColor(170, 185, 210);
            doc.text(
                `Total: ${filteredData.length}  Present: ${present+late}  Late: ${late}  Leave: ${leave}  Absent: ${absent}  Holiday: ${holiday}`,
                297 - 14, 19, {
                    align: 'right'
                });

            doc.autoTable({
                head: [
                    ['ID', 'Name', 'Office Time', 'Date', 'Check-In', 'Check-Out', 'Hours', 'Overtime',
                        'Status'
                    ]
                ],
                body: filteredData.map(r => [r.empId, r.name, r.offictime ?? '—', r.date, r.checkIn, r.checkOut, r
                    .hours, r.overtime, r.status
                ]),
                startY: 32,
                styles: {
                    font: 'helvetica',
                    fontSize: 8,
                    cellPadding: 3.5
                },
                headStyles: {
                    fillColor: [59, 111, 255],
                    textColor: [255, 255, 255],
                    fontStyle: 'bold',
                    fontSize: 8.5
                },
                alternateRowStyles: {
                    fillColor: [246, 248, 252]
                },
                didParseCell: (data) => {
                    if (data.section === 'body' && data.column.index === 8) {
                        const st = data.cell.raw;
                        if (st === 'Present') data.cell.styles.textColor = [5, 150, 105];
                        else if (st === 'Late') data.cell.styles.textColor = [180, 83, 9];
                        else if (st === 'Absent') data.cell.styles.textColor = [220, 38, 38];
                        else if (st === 'Holiday') data.cell.styles.textColor = [124, 58, 237];
                        else if (st === 'Leave') data.cell.styles.textColor = [234, 88, 12];
                        data.cell.styles.fontStyle = 'bold';
                    }
                },
                margin: {
                    left: 14,
                    right: 14
                },
            });

            const pages = doc.internal.getNumberOfPages();
            for (let i = 1; i <= pages; i++) {
                doc.setPage(i);

                // Confidential footer line
                doc.setFontSize(7);
                doc.setTextColor(150, 160, 180);
                doc.text(`Page ${i} of ${pages}`, 297 - 14, 207, {
                    align: 'right'
                });
                doc.text(`${company} — Confidential`, 14, 207);

                // Signature lines — only on last page
                if (i === pages) {
                    const sigY = 195; // Y position of signature lines; adjust if needed

                    // Three signature boxes
                    const sigBoxes = [{
                            x: 30,
                            label: 'Employee Signature',
                            title: 'Verified by Employee'
                        },
                        {
                            x: 123,
                            label: 'HR Manager Signature',
                            title: 'HR Department'
                        },
                        {
                            x: 216,
                            label: 'Authorized Signature',
                            title: 'Management'
                        },
                    ];

                    sigBoxes.forEach(box => {
                        // Signature line
                        doc.setDrawColor(80, 80, 80);
                        doc.setLineWidth(0.4);
                        doc.line(box.x, sigY, box.x + 60, sigY);

                        // Label
                        doc.setFontSize(7.5);
                        doc.setTextColor(50, 50, 50);
                        doc.setFont('helvetica', 'bold');
                        doc.text(box.label, box.x + 30, sigY + 4, {
                            align: 'center'
                        });

                        // Sub-title
                        doc.setFontSize(6.5);
                        doc.setFont('helvetica', 'normal');
                        doc.setTextColor(120, 120, 120);
                        doc.text(box.title, box.x + 30, sigY + 8, {
                            align: 'center'
                        });
                    });
                }
            }

            doc.save(`attendance_${toYMD(new Date())}.pdf`);
            showToast('PDF exported!');
        }

        /* ═══════════════════════════════════════════════════
           PRINT
           ═══════════════════════════════════════════════════ */
        function doPrint() {
            updatePrintSummary();

            const tbody = document.getElementById('tableBody');
            const backup = tbody.innerHTML;

            // Render all rows without pagination
            const sorted = [...filteredData].sort((a, b) => {
                let av = a[sortCol] ?? '',
                    bv = b[sortCol] ?? '';
                return sortDir === 'asc' ? (av > bv ? 1 : av < bv ? -1 : 0) : (av < bv ? 1 : av > bv ? -1 : 0);
            });

            tbody.innerHTML = '';
            sorted.forEach(rec => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
            <td>${esc(rec.empId)}</td>
            <td>
                <div class="emp-name">${esc(rec.name)}</div>
                <div class="emp-email">${esc(rec.email)}</div>
            </td>
            <td>${esc(rec.offictime ?? '—')}</td>
            <td>${formatDate(rec.date)}</td>
            <td>${esc(rec.checkIn)}</td>
            <td>${esc(rec.checkOut)}</td>
            <td>${esc(rec.hours)}</td>
            <td>${esc(rec.overtime)}</td>
            <td>${statusBadge(rec.status)}</td>`;
                tbody.appendChild(tr);
            });

            window.print();
            tbody.innerHTML = backup; // restore
        }

        /* ═══════════════════════════════════════════════════
           TOAST
           ═══════════════════════════════════════════════════ */
        function showToast(msg) {
            document.getElementById('toastBody').textContent = msg;
            new bootstrap.Toast(document.getElementById('liveToast'), {
                delay: 3000
            }).show();
        }
    </script>
@endsection
