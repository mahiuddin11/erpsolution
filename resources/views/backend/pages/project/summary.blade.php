@extends('backend.layouts.master')
@section('title')
    Project - {{ $title }}
@endsection


@section('styles')
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet" />
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }
    </style>
    <style>
        :root {
            --bg-base: #f0f2f7;
            --bg-card: #ffffff;
            --bg-sidebar: #0f1623;
            --accent-blue: #2563eb;
            --accent-teal: #0d9488;
            --accent-amber: #d97706;
            --accent-rose: #e11d48;
            --accent-violet: #7c3aed;
            --accent-green: #16a34a;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, .06), 0 1px 2px rgba(15, 23, 42, .04);
            --shadow-md: 0 4px 16px rgba(15, 23, 42, .08);
            --shadow-lg: 0 12px 40px rgba(15, 23, 42, .12);
            --font-main: 'DM Sans', sans-serif;
            --font-mono: 'DM Mono', monospace;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-main);
            background: var(--bg-base);
            color: var(--text-primary);
            font-size: 14px;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 15px;
            color: var(--text-primary);
            text-decoration: none;
        }

        .topbar-brand .brand-dot {
            width: 28px;
            height: 28px;
            background: var(--accent-blue);
            border-radius: 8px;
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 14px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: transparent;
            display: grid;
            place-items: center;
            color: var(--text-secondary);
            cursor: pointer;
            transition: background .15s;
        }

        .topbar-btn:hover {
            background: var(--bg-base);
        }

        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-violet));
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .badge-notif {
            position: relative;
        }

        .badge-notif::after {
            content: '';
            position: absolute;
            top: 4px;
            right: 4px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--accent-rose);
            border: 2px solid var(--bg-card);
        }

        /* ── MAIN WRAPPER ── */
        .page-wrapper {
            max-width: 1320px;
            margin: 0 auto;
            padding: 28px 20px 60px;
        }

        /* ── BREADCRUMB ── */
        .breadcrumb-bar {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 22px;
        }

        .breadcrumb-bar a {
            color: var(--accent-blue);
            text-decoration: none;
        }

        .breadcrumb-bar a:hover {
            text-decoration: underline;
        }

        .breadcrumb-bar .sep {
            color: var(--border);
        }

        /* ── SECTION TITLE ── */
        .section-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        /* ── CARDS ── */
        .card-base {
            background: var(--bg-card);
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .card-padded {
            padding: 22px 24px;
        }

        /* ── PROJECT HEADER CARD ── */
        .project-header-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
            padding: 28px 32px;
            position: relative;
            overflow: hidden;
        }

        .project-header-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-teal), var(--accent-violet));
        }

        .project-title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        .project-name {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .project-code {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-badge.active {
            background: #dcfce7;
            color: #15803d;
        }

        .status-badge.pending {
            background: #fef9c3;
            color: #a16207;
        }

        .status-badge.closed {
            background: #f1f5f9;
            color: #475569;
        }

        .status-badge.at-risk {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-badge .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 16px;
        }

        .meta-item label {
            font-size: 11px;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            display: block;
            margin-bottom: 4px;
        }

        .meta-item span {
            font-size: 13.5px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .meta-item .icon-label {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .meta-item .icon-label i {
            color: var(--accent-blue);
            font-size: 14px;
        }

        /* ── KPI CARDS ── */
        .kpi-card {
            background: var(--bg-card);
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 20px 22px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: transform .2s, box-shadow .2s;
            position: relative;
            overflow: hidden;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .kpi-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 0 0 var(--radius-md) var(--radius-md);
        }

        .kpi-card.blue::after {
            background: var(--accent-blue);
        }

        .kpi-card.teal::after {
            background: var(--accent-teal);
        }

        .kpi-card.amber::after {
            background: var(--accent-amber);
        }

        .kpi-card.green::after {
            background: var(--accent-green);
        }

        .kpi-card.rose::after {
            background: var(--accent-rose);
        }

        .kpi-card.violet::after {
            background: var(--accent-violet);
        }

        .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 18px;
        }

        .kpi-icon.blue {
            background: #eff6ff;
            color: var(--accent-blue);
        }

        .kpi-icon.teal {
            background: #f0fdfa;
            color: var(--accent-teal);
        }

        .kpi-icon.amber {
            background: #fffbeb;
            color: var(--accent-amber);
        }

        .kpi-icon.green {
            background: #f0fdf4;
            color: var(--accent-green);
        }

        .kpi-icon.rose {
            background: #fff1f2;
            color: var(--accent-rose);
        }

        .kpi-icon.violet {
            background: #f5f3ff;
            color: var(--accent-violet);
        }

        .kpi-body {
            flex: 1;
        }

        .kpi-label {
            font-size: 11.5px;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .kpi-value {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
        }

        .kpi-sub {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 5px;
        }

        .kpi-trend {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 20px;
        }

        .kpi-trend.up {
            background: #dcfce7;
            color: #15803d;
        }

        .kpi-trend.down {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* ── CHARTS ── */
        .chart-card {
            background: var(--bg-card);
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 24px;
        }

        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .chart-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .chart-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .chart-legend {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
        }

        canvas {
            max-width: 100%;
        }

        /* ── TABLES ── */
        .table-card {
            background: var(--bg-card);
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .table-header {
            padding: 18px 22px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            border-bottom: 1px solid var(--border);
        }

        .table-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .table-search {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-base);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 5px 12px;
            font-size: 12.5px;
            color: var(--text-secondary);
            min-width: 180px;
        }

        .table-search input {
            border: none;
            background: transparent;
            outline: none;
            font-family: inherit;
            font-size: 12.5px;
            color: var(--text-primary);
            width: 100%;
        }

        .table-search input::placeholder {
            color: var(--text-muted);
        }

        table.erp-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.erp-table th {
            background: #f8fafc;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 10px 18px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            white-space: nowrap;
        }

        table.erp-table td {
            padding: 11px 18px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: var(--text-primary);
            vertical-align: middle;
        }

        table.erp-table tr:last-child td {
            border-bottom: none;
        }

        table.erp-table tbody tr:hover {
            background: #f8fafc;
        }

        .cat-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }

        .cat-badge.dev {
            background: #eff6ff;
            color: var(--accent-blue);
        }

        .cat-badge.infra {
            background: #f0fdf4;
            color: var(--accent-green);
        }

        .cat-badge.hr {
            background: #fdf4ff;
            color: #a21caf;
        }

        .cat-badge.ops {
            background: #fff7ed;
            color: #c2410c;
        }

        .cat-badge.travel {
            background: #f0fdfa;
            color: var(--accent-teal);
        }

        .txn-type {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 500;
        }

        .txn-type.credit {
            color: var(--accent-green);
        }

        .txn-type.debit {
            color: var(--accent-rose);
        }

        .amount-cell {
            font-family: var(--font-mono);
            font-size: 13px;
        }

        .amount-cell.credit {
            color: var(--accent-green);
        }

        .amount-cell.debit {
            color: var(--accent-rose);
        }

        /* ── PROGRESS SECTION ── */
        .progress-section-card {
            background: var(--bg-card);
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 22px 24px;
        }

        .progress-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .stat-row {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .stat-chip {
            flex: 1;
            min-width: 90px;
            background: var(--bg-base);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            text-align: center;
        }

        .stat-chip .val {
            font-size: 24px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-chip .lbl {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .stat-chip.total .val {
            color: var(--accent-blue);
        }

        .stat-chip.done .val {
            color: var(--accent-green);
        }

        .stat-chip.pend .val {
            color: var(--accent-amber);
        }

        .progress-label-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .progress-label-row strong {
            color: var(--text-primary);
        }

        .erp-progress {
            height: 10px;
            border-radius: 99px;
            background: var(--bg-base);
            overflow: hidden;
        }

        .erp-progress-bar {
            height: 100%;
            border-radius: 99px;
            transition: width 1.4s cubic-bezier(.22, 1, .36, 1);
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-teal));
        }

        .erp-progress-bar.amber-grad {
            background: linear-gradient(90deg, var(--accent-amber), #f59e0b);
        }

        .erp-progress-bar.green-grad {
            background: linear-gradient(90deg, var(--accent-green), #22c55e);
        }

        /* ── PAYMENT SECTION ── */
        .payment-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .payment-stat {
            background: var(--bg-base);
            border-radius: var(--radius-sm);
            padding: 14px 16px;
        }

        .payment-stat .p-lbl {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 5px;
        }

        .payment-stat .p-val {
            font-size: 18px;
            font-weight: 700;
            font-family: var(--font-mono);
        }

        .payment-stat.invoice .p-val {
            color: var(--accent-blue);
        }

        .payment-stat.paid .p-val {
            color: var(--accent-green);
        }

        .payment-stat.due .p-val {
            color: var(--accent-rose);
        }

        /* ── SECTION HEADINGS ── */
        .sec-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .sec-sub {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* ── EXPORT BTN ── */
        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--accent-blue);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            padding: 7px 16px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
            font-family: inherit;
        }

        .btn-export:hover {
            background: #1d4ed8;
        }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 6px 13px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s, border-color .15s;
            font-family: inherit;
        }

        .btn-outline:hover {
            background: var(--bg-base);
            border-color: #cbd5e1;
        }

        /* ── DIVIDER ── */
        .section-gap {
            margin-top: 28px;
        }

        /* ── SCROLLABLE TABLE WRAPPER ── */
        .table-scroll {
            overflow-x: auto;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .page-wrapper {
                padding: 16px 12px 50px;
            }

            .project-header-card {
                padding: 20px 16px;
            }

            .project-name {
                font-size: 18px;
            }

            .kpi-value {
                font-size: 19px;
            }

            .topbar {
                padding: 0 14px;
            }
        }

        /* ── FADE-IN ANIMATION ── */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            opacity: 0;
            animation: fadeUp .5s ease forwards;
        }

        .fade-up:nth-child(1) {
            animation-delay: .05s;
        }

        .fade-up:nth-child(2) {
            animation-delay: .10s;
        }

        .fade-up:nth-child(3) {
            animation-delay: .15s;
        }

        .fade-up:nth-child(4) {
            animation-delay: .20s;
        }

        .fade-up:nth-child(5) {
            animation-delay: .25s;
        }

        .fade-up:nth-child(6) {
            animation-delay: .30s;
        }

        /* tooltip-like */
        .info-tip {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--bg-base);
            border: 1px solid var(--border);
            display: inline-grid;
            place-items: center;
            color: var(--text-muted);
            font-size: 10px;
            cursor: default;
        }

        /* ── SMOOTH SCROLL ── */
        html {
            scroll-behavior: smooth;
        }

        /* ── IMPROVED ANIMATIONS ── */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideRight {
            from {
                opacity: 0;
                transform: translateX(-18px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.94);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulseRing {
            0% {
                box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.25);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(37, 99, 235, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(37, 99, 235, 0);
            }
        }

        .fade-up {
            opacity: 0;
            animation: fadeUp .55s cubic-bezier(.22, 1, .36, 1) forwards;
        }

        .fade-up:nth-child(1) {
            animation-delay: .04s;
        }

        .fade-up:nth-child(2) {
            animation-delay: .09s;
        }

        .fade-up:nth-child(3) {
            animation-delay: .14s;
        }

        .fade-up:nth-child(4) {
            animation-delay: .19s;
        }

        .fade-up:nth-child(5) {
            animation-delay: .24s;
        }

        .fade-up:nth-child(6) {
            animation-delay: .29s;
        }

        .fade-up:nth-child(7) {
            animation-delay: .34s;
        }

        .fade-up:nth-child(8) {
            animation-delay: .39s;
        }

        .anim-fadein {
            opacity: 0;
            animation: fadeIn .5s ease forwards;
        }

        .anim-scale {
            opacity: 0;
            animation: scaleIn .5s cubic-bezier(.22, 1, .36, 1) forwards;
        }

        /* IntersectionObserver reveal */
        .reveal {
            opacity: 0;
            transform: translateY(22px);
            transition: opacity .6s cubic-bezier(.22, 1, .36, 1), transform .6s cubic-bezier(.22, 1, .36, 1);
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-delay-1 {
            transition-delay: .08s;
        }

        .reveal-delay-2 {
            transition-delay: .16s;
        }

        .reveal-delay-3 {
            transition-delay: .24s;
        }

        .reveal-delay-4 {
            transition-delay: .32s;
        }

        .reveal-delay-5 {
            transition-delay: .40s;
        }

        .reveal-delay-6 {
            transition-delay: .48s;
        }

        /* ── KPI CARD HOVER IMPROVEMENT ── */
        .kpi-card {
            transition: transform .25s cubic-bezier(.22, 1, .36, 1),
                box-shadow .25s cubic-bezier(.22, 1, .36, 1),
                border-color .25s ease;
        }

        .kpi-card:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: var(--shadow-lg);
            border-color: #c7d7f4;
        }

        /* ── BACK TO TOP ── */
        #backToTop {
            position: fixed;
            bottom: 28px;
            right: 24px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent-blue);
            color: #fff;
            border: none;
            box-shadow: 0 4px 16px rgba(37, 99, 235, .35);
            display: grid;
            place-items: center;
            cursor: pointer;
            opacity: 0;
            pointer-events: none;
            transition: opacity .3s, transform .3s;
            z-index: 999;
            font-size: 16px;
        }

        #backToTop.show {
            opacity: 1;
            pointer-events: all;
        }

        #backToTop:hover {
            transform: translateY(-3px);
        }

        /* ── TOOLTIP ── */
        [data-tip] {
            position: relative;
            cursor: default;
        }

        [data-tip]::after {
            content: attr(data-tip);
            position: absolute;
            bottom: calc(100% + 7px);
            left: 50%;
            transform: translateX(-50%);
            background: #0f1623;
            color: #e2e8f0;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 6px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity .2s;
            z-index: 50;
        }

        [data-tip]:hover::after {
            opacity: 1;
        }

        /* ── TEAM MEMBER SECTION ── */
        .team-card {
            background: var(--bg-card);
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            transition: transform .25s cubic-bezier(.22, 1, .36, 1),
                box-shadow .25s cubic-bezier(.22, 1, .36, 1);
            cursor: default;
        }

        .team-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .team-avatar-wrap {
            position: relative;
            width: fit-content;
        }

        .team-avatar {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            letter-spacing: .5px;
            flex-shrink: 0;
        }

        .team-online-dot {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: var(--accent-green);
            border: 2.5px solid var(--bg-card);
        }

        .team-online-dot.away {
            background: var(--accent-amber);
        }

        .team-online-dot.busy {
            background: var(--accent-rose);
        }

        .team-info {
            flex: 1;
        }

        .team-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 2px;
        }

        .team-role {
            font-size: 11.5px;
            color: var(--text-muted);
        }

        .team-role-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10.5px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            margin-top: 5px;
        }

        .role-lead {
            background: #eff6ff;
            color: var(--accent-blue);
        }

        .role-dev {
            background: #f5f3ff;
            color: var(--accent-violet);
        }

        .role-qa {
            background: #f0fdf4;
            color: var(--accent-green);
        }

        .role-design {
            background: #fdf4ff;
            color: #a21caf;
        }

        .role-devops {
            background: #fff7ed;
            color: #c2410c;
        }

        .role-ba {
            background: #f0fdfa;
            color: var(--accent-teal);
        }

        .team-contact {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .contact-row {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .contact-row i {
            color: var(--accent-blue);
            font-size: 12px;
            width: 14px;
        }

        .contact-row a {
            color: var(--text-secondary);
            text-decoration: none;
        }

        .contact-row a:hover {
            color: var(--accent-blue);
        }

        .team-task-section {}

        .team-task-label {
            display: flex;
            justify-content: space-between;
            font-size: 11.5px;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .team-task-label strong {
            color: var(--text-primary);
            font-weight: 600;
        }

        .team-progress {
            height: 7px;
            border-radius: 99px;
            background: var(--bg-base);
            overflow: hidden;
            margin-bottom: 8px;
        }

        .team-progress-bar {
            height: 100%;
            border-radius: 99px;
            transition: width 1.6s cubic-bezier(.22, 1, .36, 1);
        }

        .task-chips {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .task-chip {
            font-size: 10.5px;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 500;
        }

        .task-chip.done {
            background: #dcfce7;
            color: #15803d;
        }

        .task-chip.pend {
            background: #fef9c3;
            color: #a16207;
        }

        .task-chip.total {
            background: #eff6ff;
            color: var(--accent-blue);
        }

        /* team section header */
        .team-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .team-count-badge {
            background: var(--accent-blue);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 20px;
        }

        /* ── LOADING SKELETON ── */
        @keyframes shimmer {
            0% {
                background-position: -600px 0;
            }

            100% {
                background-position: 600px 0;
            }
        }

        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 600px 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 6px;
        }
    </style>
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 13px;
        }

        .status-badge.active {
            background: #e6f7ee;
            color: #28a745;
        }

        .status-badge.inactive {
            background: #eee;
            color: #6c757d;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.cancel {
            background: #f8d7da;
            color: #dc3545;
        }

        .dot {
            height: 8px;
            width: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            background: currentColor;
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Project Summary </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('project.invoiceCreate.index'))
                            <li class="breadcrumb-item"><a href="{{ route('project.invoiceCreate.index') }}">Invoice</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Invoice List</span></li>
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
                {{-- <div class="card-header">
                    <h3 class="card-title">Project Invoice</h3>

                </div> --}}
                <div class="card-body">
                    <div class="row no-print">
                        <div class="col-12">
                            <a onclick="window.print()" target="_blank" class="btn btn-default float-right my-2"><i
                                    class="fas fa-print"></i>
                                Print</a>
                        </div>
                    </div>
                    <div class="">


                        <main class="page-wrapper">




                            <!-- ─────────────────────────────────────────
                           1. PROJECT HEADER SECTION
                      ────────────────────────────────────────── -->
                            <div class="project-header-card mb-4 reveal">
                                <div class="project-title-row">
                                    <div>
                                        <div class="project-name"> {{ $projectDetails->pname ?? '' }}</div>
                                        <div class="project-code">{{ $projectDetails->projectCode ?? '' }} &nbsp;·&nbsp;
                                            {{ $projectDetails->address ?? '' }}</div>
                                    </div>

                                    <span
                                        class="status-badge {{ $projectDetails->condition == 'Complete' ? 'active' : 'pending' }}">
                                        <span class="dot"></span> {{ $projectDetails->condition }}
                                    </span>

                                </div>
                                <div class="meta-grid">
                                    <div class="meta-item">
                                        <label>Client Name</label>
                                        <span class="icon-label"><i class="bi bi-building"></i>
                                            {{ $projectDetails->customer_name ?? $projectDetails->ledger_name }}</span>
                                    </div>
                                    {{-- @dd($projectDetails); --}}
                                    <div class="meta-item">
                                        <label>Project Manager</label>
                                        <span class="icon-label"><i class="bi bi-person-circle"></i>
                                            {{ $projectDetails->aname ?? '' }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <label>Start Date</label>
                                        <span class="icon-label"><i class="bi bi-calendar3"></i>
                                            {{ $projectDetails->start_date ?? '' }} </span>
                                    </div>
                                    <div class="meta-item">
                                        <label>End Date</label>
                                        <span class="icon-label"><i class="bi bi-calendar-check"></i>
                                            {{ $projectDetails->end_date ?? '' }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <label>Duration</label>
                                        <span class="icon-label"><i class="bi bi-clock"></i>
                                            {{ $projectDetails->duration }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- ─────────────────────────────────────────
                           2. KPI SUMMARY CARDS
                      ────────────────────────────────────────── -->
                            <p class="section-label">Financial KPIs</p>
                            <div class="kpi-section-anchor"></div>
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-2 fade-up">
                                    <div class="kpi-card blue">
                                        <div class="kpi-icon blue"><i class="bi bi-wallet2"></i></div>
                                        <div class="kpi-body">
                                            <div class="kpi-label">Total Budget</div>
                                            {{-- @dd($projectDetails->budget); --}}
                                            <div class="kpi-value" id="kpi-budget">
                                                ৳{{ smartNumberFormat($projectDetails->budget ?? 0) }}</div>
                                            <div class="kpi-sub">Approved budget</div>
                                        </div>
                                        <span class="kpi-trend up"><i class="bi bi-arrow-up-short"></i> Locked</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-2 fade-up">
                                    <div class="kpi-card rose">
                                        <div class="kpi-icon rose"><i class="bi bi-receipt"></i></div>
                                        <div class="kpi-body">
                                            <div class="kpi-label">Total Expense</div>
                                            <div class="kpi-value" id="kpi-expense">
                                                ৳{{ smartNumberFormat($totalExpense ?? 0) }}</div>
                                            <div class="kpi-sub">71.8% of budget</div>
                                        </div>
                                        <span class="kpi-trend down"><i class="bi bi-arrow-up-short"></i> +4.2%</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-2 fade-up">
                                    <div class="kpi-card teal">
                                        <div class="kpi-icon teal"><i class="bi bi-file-earmark-text"></i></div>
                                        <div class="kpi-body">
                                            <div class="kpi-label">Total Invoice</div>
                                            <div class="kpi-value" id="kpi-invoice">
                                                ৳{{ smartNumberFormat($totalInvoice ?? 0) }}</div>
                                            <div class="kpi-sub">5 invoices raised</div>
                                        </div>
                                        <span class="kpi-trend up"><i class="bi bi-arrow-up-short"></i> On track</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-2 fade-up">
                                    <div class="kpi-card green">
                                        <div class="kpi-icon green"><i class="bi bi-check2-circle"></i></div>
                                        <div class="kpi-body">
                                            <div class="kpi-label">Total Received</div>
                                            <div class="kpi-value" id="kpi-received">
                                                ৳{{ smartNumberFormat($totalReceived ?? 0) }}</div>
                                            <div class="kpi-sub">84.1% collected</div>
                                        </div>
                                        <span class="kpi-trend up"><i class="bi bi-arrow-up-short"></i> +8.5%</span>
                                    </div>
                                </div>


                                <div class="col-12 col-sm-6 col-lg-4 col-xl-2 fade-up">
                                    <div class="kpi-card amber">
                                        <div class="kpi-icon amber"><i class="bi bi-exclamation-circle"></i></div>
                                        <div class="kpi-body">
                                            <div class="kpi-label">Due Amount</div>
                                            <div class="kpi-value" id="kpi-due">
                                                ৳{{ smartNumberFormat($dueAmount ?? 0) }}</div>
                                            <div class="kpi-sub">2 invoices pending</div>
                                        </div>
                                        <span class="kpi-trend down"><i class="bi bi-arrow-down-short"></i> Overdue</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-2 fade-up">
                                    <div class="kpi-card violet">
                                        <div class="kpi-icon violet"><i class="bi bi-graph-up-arrow"></i></div>
                                        <div class="kpi-body">
                                            <div class="kpi-label">Profit / Loss</div>
                                            <div class="kpi-value" id="kpi-profit" style="color:var(--accent-violet)">
                                                ৳{{ smartNumberFormat($profit ?? 0) }}</div>
                                            <div class="kpi-sub">Margin: 23.6%</div>
                                        </div>
                                        <span class="kpi-trend up"><i class="bi bi-arrow-up-short"></i> Profit</span>
                                    </div>
                                </div>
                            </div>

                            {{-- ════════ TAB: PURCHASE ════════ --}}
                            <p class="section-label section-gap reveal">Purchase Breakdown</p>
                            <div class="table-card mb-4">
                                <div class="table-header">
                                    <div>
                                        <div class="table-title">Purchase Details</div>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <div class="table-search">
                                            <i class="bi bi-search" style="color:var(--text-muted)"></i>
                                            <input type="text" placeholder="Search expenses…" id="expenseSearch" />
                                        </div>
                                        <button class="btn-outline"><i class="bi bi-sliders2"></i> Columns</button>
                                    </div>
                                </div>
                                <div class="table-scroll">
                                    <table class="erp-table" id="expenseTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>invoice</th>
                                                <th>Date</th>
                                                <th>Supplyer</th>
                                                <th>Amount</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($productgoodreceive as $index => $detail)

                                                    <tr>
                                                        <td style="color:var(--text-muted);font-size:12px">
                                                            {{ sprintf('%03d', $index + 1) }}</td>
                                                        <td>
                                                            <span class="cat-badge dev">
                                                                <i class="bi bi-box-seam"></i>
                                                                {{ $detail->invoice_no ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($detail->date)->format('M d, Y') }}</td>
                                                        <td class="amount-cell">
                                                           {{ $detail->supplier->name ?? 'N/A' }}
                                                        </td>
                                                        <td class="amount-cell">
                                                            {{ number_format($detail->total_price, 0) }}Tk
                                                        </td>
                                                        <td>{{ $detail->note ?? 'N/A' }}</td>
                                                        <td>
                                                            <span
                                                                class="status-badge {{ $detail->status == 'Approved' ? 'active' : 'pending' }}"
                                                                style="font-size:11px;padding:3px 10px">
                                                                <span class="dot"></span>{{ $detail->status }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            <!-- ─────────────────────────────────────────
                           TEAM MEMBERS SECTION
                      ────────────────────────────────────────── -->
                            <div class="team-section-header section-gap reveal">
                                <div>
                                    <p class="section-label" style="margin-bottom:2px;">Project Team</p>
                                    <p style="font-size:12px;color:var(--text-muted);margin:0;">Members actively working on
                                        this project</p>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="team-count-badge">8 Members</span>
                                    <button class="btn-outline"><i class="bi bi-person-plus"></i> Add Member</button>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">

                                <!-- Member 1 — Lead -->
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 reveal reveal-delay-1">
                                    <div class="team-card">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="team-avatar-wrap">
                                                <div class="team-avatar"
                                                    style="background:linear-gradient(135deg,#2563eb,#7c3aed)">SK</div>
                                                <span class="team-online-dot" data-tip="Online"></span>
                                            </div>
                                            <div class="team-info">
                                                <div class="team-name">Sarah K. Thompson</div>
                                                <div class="team-role">Project Manager</div>
                                                <span class="team-role-badge role-lead"><i class="bi bi-star-fill"></i>
                                                    Lead</span>
                                            </div>
                                        </div>
                                        <div class="team-contact">
                                            <div class="contact-row"><i class="bi bi-envelope"></i><a
                                                    href="#">s.thompson@nexus.io</a></div>
                                            <div class="contact-row"><i class="bi bi-telephone"></i><span>+1 (555)
                                                    201-4432</span></div>
                                            <div class="contact-row"><i
                                                    class="bi bi-slack"></i><span>@sarah.thompson</span></div>
                                        </div>
                                        <div class="team-task-section">
                                            <div class="team-task-label">
                                                <span>Task Completion</span>
                                                <strong>18 / 20</strong>
                                            </div>
                                            <div class="team-progress">
                                                <div class="team-progress-bar"
                                                    style="width:0%;background:linear-gradient(90deg,#2563eb,#7c3aed)"
                                                    data-target="90"></div>
                                            </div>
                                            <div class="task-chips">
                                                <span class="task-chip total">20 Tasks</span>
                                                <span class="task-chip done">18 Done</span>
                                                <span class="task-chip pend">2 Pending</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Member 2 -->
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 reveal reveal-delay-2">
                                    <div class="team-card">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="team-avatar-wrap">
                                                <div class="team-avatar"
                                                    style="background:linear-gradient(135deg,#7c3aed,#a21caf)">RH</div>
                                                <span class="team-online-dot" data-tip="Online"></span>
                                            </div>
                                            <div class="team-info">
                                                <div class="team-name">Rashed Hossain</div>
                                                <div class="team-role">Lead Backend Developer</div>
                                                <span class="team-role-badge role-dev"><i class="bi bi-code-slash"></i>
                                                    Backend</span>
                                            </div>
                                        </div>
                                        <div class="team-contact">
                                            <div class="contact-row"><i class="bi bi-envelope"></i><a
                                                    href="#">r.hossain@nexus.io</a></div>
                                            <div class="contact-row"><i class="bi bi-telephone"></i><span>+880
                                                    171-234-5678</span></div>
                                            <div class="contact-row"><i class="bi bi-github"></i><span>@rashed-dev</span>
                                            </div>
                                        </div>
                                        <div class="team-task-section">
                                            <div class="team-task-label">
                                                <span>Task Completion</span>
                                                <strong>24 / 30</strong>
                                            </div>
                                            <div class="team-progress">
                                                <div class="team-progress-bar"
                                                    style="width:0%;background:linear-gradient(90deg,#7c3aed,#a21caf)"
                                                    data-target="80"></div>
                                            </div>
                                            <div class="task-chips">
                                                <span class="task-chip total">30 Tasks</span>
                                                <span class="task-chip done">24 Done</span>
                                                <span class="task-chip pend">6 Pending</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Member 3 -->
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 reveal reveal-delay-3">
                                    <div class="team-card">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="team-avatar-wrap">
                                                <div class="team-avatar"
                                                    style="background:linear-gradient(135deg,#0d9488,#2563eb)">NA</div>
                                                <span class="team-online-dot away" data-tip="Away"></span>
                                            </div>
                                            <div class="team-info">
                                                <div class="team-name">Nadia Akter</div>
                                                <div class="team-role">Frontend Developer</div>
                                                <span class="team-role-badge role-dev"><i class="bi bi-window"></i>
                                                    Frontend</span>
                                            </div>
                                        </div>
                                        <div class="team-contact">
                                            <div class="contact-row"><i class="bi bi-envelope"></i><a
                                                    href="#">n.akter@nexus.io</a></div>
                                            <div class="contact-row"><i class="bi bi-telephone"></i><span>+880
                                                    181-876-5432</span></div>
                                            <div class="contact-row"><i class="bi bi-github"></i><span>@nadia-ui</span>
                                            </div>
                                        </div>
                                        <div class="team-task-section">
                                            <div class="team-task-label">
                                                <span>Task Completion</span>
                                                <strong>16 / 22</strong>
                                            </div>
                                            <div class="team-progress">
                                                <div class="team-progress-bar"
                                                    style="width:0%;background:linear-gradient(90deg,#0d9488,#2563eb)"
                                                    data-target="73"></div>
                                            </div>
                                            <div class="task-chips">
                                                <span class="task-chip total">22 Tasks</span>
                                                <span class="task-chip done">16 Done</span>
                                                <span class="task-chip pend">6 Pending</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Member 4 -->
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 reveal reveal-delay-4">
                                    <div class="team-card">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="team-avatar-wrap">
                                                <div class="team-avatar"
                                                    style="background:linear-gradient(135deg,#16a34a,#0d9488)">MR</div>
                                                <span class="team-online-dot" data-tip="Online"></span>
                                            </div>
                                            <div class="team-info">
                                                <div class="team-name">Mahfuz Rahman</div>
                                                <div class="team-role">QA Engineer</div>
                                                <span class="team-role-badge role-qa"><i class="bi bi-shield-check"></i>
                                                    QA</span>
                                            </div>
                                        </div>
                                        <div class="team-contact">
                                            <div class="contact-row"><i class="bi bi-envelope"></i><a
                                                    href="#">m.rahman@nexus.io</a></div>
                                            <div class="contact-row"><i class="bi bi-telephone"></i><span>+880
                                                    191-543-2198</span></div>
                                            <div class="contact-row"><i class="bi bi-slack"></i><span>@mahfuz.qa</span>
                                            </div>
                                        </div>
                                        <div class="team-task-section">
                                            <div class="team-task-label">
                                                <span>Task Completion</span>
                                                <strong>14 / 18</strong>
                                            </div>
                                            <div class="team-progress">
                                                <div class="team-progress-bar"
                                                    style="width:0%;background:linear-gradient(90deg,#16a34a,#0d9488)"
                                                    data-target="78"></div>
                                            </div>
                                            <div class="task-chips">
                                                <span class="task-chip total">18 Tasks</span>
                                                <span class="task-chip done">14 Done</span>
                                                <span class="task-chip pend">4 Pending</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Member 5 -->
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 reveal reveal-delay-1">
                                    <div class="team-card">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="team-avatar-wrap">
                                                <div class="team-avatar"
                                                    style="background:linear-gradient(135deg,#a21caf,#e11d48)">FI</div>
                                                <span class="team-online-dot busy" data-tip="Busy"></span>
                                            </div>
                                            <div class="team-info">
                                                <div class="team-name">Fatema Islam</div>
                                                <div class="team-role">UI/UX Designer</div>
                                                <span class="team-role-badge role-design"><i class="bi bi-palette"></i>
                                                    Design</span>
                                            </div>
                                        </div>
                                        <div class="team-contact">
                                            <div class="contact-row"><i class="bi bi-envelope"></i><a
                                                    href="#">f.islam@nexus.io</a></div>
                                            <div class="contact-row"><i class="bi bi-telephone"></i><span>+880
                                                    172-654-3210</span></div>
                                            <div class="contact-row"><i
                                                    class="bi bi-figma"></i><span>@fatema.design</span></div>
                                        </div>
                                        <div class="team-task-section">
                                            <div class="team-task-label">
                                                <span>Task Completion</span>
                                                <strong>10 / 12</strong>
                                            </div>
                                            <div class="team-progress">
                                                <div class="team-progress-bar"
                                                    style="width:0%;background:linear-gradient(90deg,#a21caf,#e11d48)"
                                                    data-target="83"></div>
                                            </div>
                                            <div class="task-chips">
                                                <span class="task-chip total">12 Tasks</span>
                                                <span class="task-chip done">10 Done</span>
                                                <span class="task-chip pend">2 Pending</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Member 6 -->
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 reveal reveal-delay-2">
                                    <div class="team-card">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="team-avatar-wrap">
                                                <div class="team-avatar"
                                                    style="background:linear-gradient(135deg,#c2410c,#d97706)">TH</div>
                                                <span class="team-online-dot" data-tip="Online"></span>
                                            </div>
                                            <div class="team-info">
                                                <div class="team-name">Tanvir Haque</div>
                                                <div class="team-role">DevOps Engineer</div>
                                                <span class="team-role-badge role-devops"><i
                                                        class="bi bi-cloud-arrow-up"></i> DevOps</span>
                                            </div>
                                        </div>
                                        <div class="team-contact">
                                            <div class="contact-row"><i class="bi bi-envelope"></i><a
                                                    href="#">t.haque@nexus.io</a></div>
                                            <div class="contact-row"><i class="bi bi-telephone"></i><span>+880
                                                    185-432-1098</span></div>
                                            <div class="contact-row"><i
                                                    class="bi bi-slack"></i><span>@tanvir.devops</span></div>
                                        </div>
                                        <div class="team-task-section">
                                            <div class="team-task-label">
                                                <span>Task Completion</span>
                                                <strong>8 / 10</strong>
                                            </div>
                                            <div class="team-progress">
                                                <div class="team-progress-bar"
                                                    style="width:0%;background:linear-gradient(90deg,#c2410c,#d97706)"
                                                    data-target="80"></div>
                                            </div>
                                            <div class="task-chips">
                                                <span class="task-chip total">10 Tasks</span>
                                                <span class="task-chip done">8 Done</span>
                                                <span class="task-chip pend">2 Pending</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Member 7 -->
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 reveal reveal-delay-3">
                                    <div class="team-card">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="team-avatar-wrap">
                                                <div class="team-avatar"
                                                    style="background:linear-gradient(135deg,#0d9488,#16a34a)">SK2</div>
                                                <span class="team-online-dot away" data-tip="Away"></span>
                                            </div>
                                            <div class="team-info">
                                                <div class="team-name">Sumaiya Khanam</div>
                                                <div class="team-role">Business Analyst</div>
                                                <span class="team-role-badge role-ba"><i class="bi bi-graph-up"></i>
                                                    BA</span>
                                            </div>
                                        </div>
                                        <div class="team-contact">
                                            <div class="contact-row"><i class="bi bi-envelope"></i><a
                                                    href="#">s.khanam@nexus.io</a></div>
                                            <div class="contact-row"><i class="bi bi-telephone"></i><span>+880
                                                    176-321-0987</span></div>
                                            <div class="contact-row"><i class="bi bi-slack"></i><span>@sumaiya.ba</span>
                                            </div>
                                        </div>
                                        <div class="team-task-section">
                                            <div class="team-task-label">
                                                <span>Task Completion</span>
                                                <strong>6 / 8</strong>
                                            </div>
                                            <div class="team-progress">
                                                <div class="team-progress-bar"
                                                    style="width:0%;background:linear-gradient(90deg,#0d9488,#16a34a)"
                                                    data-target="75"></div>
                                            </div>
                                            <div class="task-chips">
                                                <span class="task-chip total">8 Tasks</span>
                                                <span class="task-chip done">6 Done</span>
                                                <span class="task-chip pend">2 Pending</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Member 8 -->
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 reveal reveal-delay-4">
                                    <div class="team-card">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="team-avatar-wrap">
                                                <div class="team-avatar"
                                                    style="background:linear-gradient(135deg,#2563eb,#0d9488)">AM</div>
                                                <span class="team-online-dot" data-tip="Online"></span>
                                            </div>
                                            <div class="team-info">
                                                <div class="team-name">Arif Mahmud</div>
                                                <div class="team-role">Backend Developer</div>
                                                <span class="team-role-badge role-dev"><i class="bi bi-code-slash"></i>
                                                    Backend</span>
                                            </div>
                                        </div>
                                        <div class="team-contact">
                                            <div class="contact-row"><i class="bi bi-envelope"></i><a
                                                    href="#">a.mahmud@nexus.io</a></div>
                                            <div class="contact-row"><i class="bi bi-telephone"></i><span>+880
                                                    193-210-8765</span></div>
                                            <div class="contact-row"><i
                                                    class="bi bi-github"></i><span>@arif-backend</span></div>
                                        </div>
                                        <div class="team-task-section">
                                            <div class="team-task-label">
                                                <span>Task Completion</span>
                                                <strong>3 / 5</strong>
                                            </div>
                                            <div class="team-progress">
                                                <div class="team-progress-bar"
                                                    style="width:0%;background:linear-gradient(90deg,#2563eb,#0d9488)"
                                                    data-target="60"></div>
                                            </div>
                                            <div class="task-chips">
                                                <span class="task-chip total">5 Tasks</span>
                                                <span class="task-chip done">3 Done</span>
                                                <span class="task-chip pend">2 Pending</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- ─────────────────────────────────────────
                           3. FINANCIAL OVERVIEW CHARTS
                      ────────────────────────────────────────── -->
                            <p class="section-label section-gap reveal">Financial Overview</p>
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-lg-7">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <div>
                                                <div class="chart-title">Budget vs Expense</div>
                                                <div class="chart-sub">Monthly comparison — FY 2024</div>
                                            </div>
                                            <div class="chart-legend">
                                                <span class="legend-item"><span class="legend-dot"
                                                        style="background:var(--accent-blue)"></span> Budget</span>
                                                <span class="legend-item"><span class="legend-dot"
                                                        style="background:var(--accent-rose)"></span> Expense</span>
                                            </div>
                                        </div>
                                        <canvas id="budgetExpenseChart" height="220"></canvas>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <div class="chart-card h-100">
                                        <div class="chart-header">
                                            <div>
                                                <div class="chart-title">Income vs Profit</div>
                                                <div class="chart-sub">Breakdown by quarter</div>
                                            </div>
                                            <div class="chart-legend">
                                                <span class="legend-item"><span class="legend-dot"
                                                        style="background:var(--accent-teal)"></span> Income</span>
                                                <span class="legend-item"><span class="legend-dot"
                                                        style="background:var(--accent-violet)"></span> Profit</span>
                                            </div>
                                        </div>
                                        <canvas id="incomeProfitChart" height="220"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- ─────────────────────────────────────────
                           4. EXPENSE BREAKDOWN TABLE
                      ────────────────────────────────────────── -->
                            <p class="section-label section-gap reveal">Expense Breakdown</p>
                            <div class="table-card mb-4">
                                <div class="table-header">
                                    <div>
                                        <div class="table-title">Expense Details</div>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <div class="table-search">
                                            <i class="bi bi-search" style="color:var(--text-muted)"></i>
                                            <input type="text" placeholder="Search expenses…" id="expenseSearch" />
                                        </div>
                                        <button class="btn-outline"><i class="bi bi-sliders2"></i> Columns</button>
                                    </div>
                                </div>
                                <div class="table-scroll">
                                    <table class="erp-table" id="expenseTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Category</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="color:var(--text-muted);font-size:12px">001</td>
                                                <td><span class="cat-badge dev"><i class="bi bi-code-slash"></i>
                                                        Development</span></td>
                                                <td class="amount-cell">$52,400</td>
                                                <td>Mar 15, 2024</td>
                                                <td>Frontend &amp; backend sprint Q1</td>
                                                <td><span class="status-badge active"
                                                        style="font-size:11px;padding:3px 10px"><span
                                                            class="dot"></span>Approved</span></td>
                                            </tr>
                                            <tr>
                                                <td style="color:var(--text-muted);font-size:12px">002</td>
                                                <td><span class="cat-badge infra"><i class="bi bi-server"></i>
                                                        Infrastructure</span></td>
                                                <td class="amount-cell">$18,750</td>
                                                <td>Apr 02, 2024</td>
                                                <td>Cloud hosting setup &amp; migration</td>
                                                <td><span class="status-badge active"
                                                        style="font-size:11px;padding:3px 10px"><span
                                                            class="dot"></span>Approved</span></td>
                                            </tr>
                                            <tr>
                                                <td style="color:var(--text-muted);font-size:12px">003</td>
                                                <td><span class="cat-badge hr"><i class="bi bi-people"></i> HR &amp;
                                                        Staffing</span></td>
                                                <td class="amount-cell">$34,200</td>
                                                <td>Apr 30, 2024</td>
                                                <td>Contractor fees — security &amp; QA</td>
                                                <td><span class="status-badge active"
                                                        style="font-size:11px;padding:3px 10px"><span
                                                            class="dot"></span>Approved</span></td>
                                            </tr>
                                            <tr>
                                                <td style="color:var(--text-muted);font-size:12px">004</td>
                                                <td><span class="cat-badge ops"><i class="bi bi-gear-wide"></i>
                                                        Operations</span></td>
                                                <td class="amount-cell">$9,800</td>
                                                <td>May 18, 2024</td>
                                                <td>Software licences &amp; tooling</td>
                                                <td><span class="status-badge pending"
                                                        style="font-size:11px;padding:3px 10px"><span
                                                            class="dot"></span>Pending</span></td>
                                            </tr>
                                            <tr>
                                                <td style="color:var(--text-muted);font-size:12px">005</td>
                                                <td><span class="cat-badge travel"><i class="bi bi-airplane"></i>
                                                        Travel</span></td>
                                                <td class="amount-cell">$6,350</td>
                                                <td>Jun 05, 2024</td>
                                                <td>Client site visits — 3 trips</td>
                                                <td><span class="status-badge active"
                                                        style="font-size:11px;padding:3px 10px"><span
                                                            class="dot"></span>Approved</span></td>
                                            </tr>
                                            <tr>
                                                <td style="color:var(--text-muted);font-size:12px">006</td>
                                                <td><span class="cat-badge dev"><i class="bi bi-code-slash"></i>
                                                        Development</span></td>
                                                <td class="amount-cell">$56,500</td>
                                                <td>Jul 31, 2024</td>
                                                <td>Core banking module development</td>
                                                <td><span class="status-badge active"
                                                        style="font-size:11px;padding:3px 10px"><span
                                                            class="dot"></span>Approved</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- ─────────────────────────────────────────
                           5 & 6. PROGRESS + PAYMENT (side by side)
                      ────────────────────────────────────────── -->
                            <p class="section-label section-gap reveal">Project Progress &amp; Payment Status</p>
                            <div class="row g-3 mb-4">

                                <!-- Project Progress -->
                                <div class="col-12 col-lg-6">
                                    <div class="progress-section-card h-100">
                                        <div class="progress-header">
                                            <div>
                                                <div class="sec-title">Project Progress</div>
                                                <div class="sec-sub">Task completion overview</div>
                                            </div>
                                            <span class="kpi-trend up" style="font-size:13px;padding:5px 12px"><i
                                                    class="bi bi-check2-all"></i> 74%</span>
                                        </div>
                                        <div class="stat-row">
                                            <div class="stat-chip total">
                                                <div class="val">120</div>
                                                <div class="lbl">Total Tasks</div>
                                            </div>
                                            <div class="stat-chip done">
                                                <div class="val">89</div>
                                                <div class="lbl">Completed</div>
                                            </div>
                                            <div class="stat-chip pend">
                                                <div class="val">31</div>
                                                <div class="lbl">Pending</div>
                                            </div>
                                        </div>
                                        <div class="progress-label-row">
                                            <span>Overall Completion</span>
                                            <strong id="taskPct">74%</strong>
                                        </div>
                                        <div class="erp-progress mb-4">
                                            <div class="erp-progress-bar" id="taskBar" style="width:0%"></div>
                                        </div>

                                        <div class="progress-label-row">
                                            <span>Sprint Velocity</span>
                                            <strong>88%</strong>
                                        </div>
                                        <div class="erp-progress mb-4">
                                            <div class="erp-progress-bar green-grad" style="width:0%" id="sprintBar">
                                            </div>
                                        </div>

                                        <div class="progress-label-row">
                                            <span>Bug Resolution Rate</span>
                                            <strong>63%</strong>
                                        </div>
                                        <div class="erp-progress">
                                            <div class="erp-progress-bar amber-grad" style="width:0%" id="bugBar">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Status -->
                                <div class="col-12 col-lg-6">
                                    <div class="progress-section-card h-100">
                                        <div class="progress-header">
                                            <div>
                                                <div class="sec-title">Payment Status</div>
                                                <div class="sec-sub">Invoice collection overview</div>
                                            </div>
                                            <span class="kpi-trend up" style="font-size:13px;padding:5px 12px"><i
                                                    class="bi bi-currency-dollar"></i> 84%</span>
                                        </div>
                                        <div class="payment-row">
                                            <div class="payment-stat invoice">
                                                <div class="p-lbl">Invoice Amount</div>
                                                <div class="p-val">$220K</div>
                                            </div>
                                            <div class="payment-stat paid">
                                                <div class="p-lbl">Paid Amount</div>
                                                <div class="p-val">$185K</div>
                                            </div>
                                            <div class="payment-stat due">
                                                <div class="p-lbl">Due Amount</div>
                                                <div class="p-val">$35K</div>
                                            </div>
                                        </div>

                                        <div class="progress-label-row">
                                            <span>Payment Collected</span>
                                            <strong>84.1%</strong>
                                        </div>
                                        <div class="erp-progress mb-4">
                                            <div class="erp-progress-bar green-grad" style="width:0%" id="payBar">
                                            </div>
                                        </div>

                                        <div class="progress-label-row">
                                            <span>INV-2024-004 — $18,000</span>
                                            <strong>Due Aug 30</strong>
                                        </div>
                                        <div class="erp-progress mb-4">
                                            <div class="erp-progress-bar amber-grad" style="width:0%" id="inv4Bar">
                                            </div>
                                        </div>

                                        <div class="progress-label-row">
                                            <span>INV-2024-005 — $17,000</span>
                                            <strong>Due Sep 15</strong>
                                        </div>
                                        <div class="erp-progress">
                                            <div class="erp-progress-bar" style="width:0%;background:var(--accent-rose)"
                                                id="inv5Bar"></div>
                                        </div>

                                        <!-- Invoice mini table -->
                                        <div class="mt-4 pt-3" style="border-top:1px solid var(--border)">
                                            <div
                                                style="font-size:12px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">
                                                Recent Invoices</div>
                                            <div style="font-size:12.5px;">
                                                <div class="d-flex justify-content-between py-2"
                                                    style="border-bottom:1px solid #f1f5f9">
                                                    <span style="color:var(--text-secondary)">INV-2024-001</span>
                                                    <span style="font-family:var(--font-mono)">$45,000</span>
                                                    <span class="status-badge active"
                                                        style="font-size:10px;padding:2px 8px"><span
                                                            class="dot"></span>Paid</span>
                                                </div>
                                                <div class="d-flex justify-content-between py-2"
                                                    style="border-bottom:1px solid #f1f5f9">
                                                    <span style="color:var(--text-secondary)">INV-2024-002</span>
                                                    <span style="font-family:var(--font-mono)">$60,000</span>
                                                    <span class="status-badge active"
                                                        style="font-size:10px;padding:2px 8px"><span
                                                            class="dot"></span>Paid</span>
                                                </div>
                                                <div class="d-flex justify-content-between py-2"
                                                    style="border-bottom:1px solid #f1f5f9">
                                                    <span style="color:var(--text-secondary)">INV-2024-003</span>
                                                    <span style="font-family:var(--font-mono)">$80,000</span>
                                                    <span class="status-badge active"
                                                        style="font-size:10px;padding:2px 8px"><span
                                                            class="dot"></span>Paid</span>
                                                </div>
                                                <div class="d-flex justify-content-between py-2"
                                                    style="border-bottom:1px solid #f1f5f9">
                                                    <span style="color:var(--text-secondary)">INV-2024-004</span>
                                                    <span style="font-family:var(--font-mono)">$18,000</span>
                                                    <span class="status-badge pending"
                                                        style="font-size:10px;padding:2px 8px"><span
                                                            class="dot"></span>Pending</span>
                                                </div>
                                                <div class="d-flex justify-content-between py-2">
                                                    <span style="color:var(--text-secondary)">INV-2024-005</span>
                                                    <span style="font-family:var(--font-mono)">$17,000</span>
                                                    <span class="status-badge at-risk"
                                                        style="font-size:10px;padding:2px 8px"><span
                                                            class="dot"></span>Overdue</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ─────────────────────────────────────────
                           7. RECENT TRANSACTIONS TABLE
                      ────────────────────────────────────────── -->
                            <p class="section-label section-gap reveal">Recent Transactions</p>
                            <div class="table-card mb-4">
                                <div class="table-header">
                                    <div>
                                        <div class="table-title">Transaction Ledger</div>
                                        <div style="font-size:12px;color:var(--text-muted);margin-top:2px">Last 10
                                            project-related transactions</div>
                                    </div>
                                    <button class="btn-outline"><i class="bi bi-arrow-repeat"></i> Refresh</button>
                                </div>
                                <div class="table-scroll">
                                    <table class="erp-table">
                                        <thead>
                                            <tr>
                                                <th>TXN ID</th>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Balance</th>
                                                <th>Ref</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td
                                                    style="font-family:var(--font-mono);font-size:12px;color:var(--text-muted)">
                                                    TXN-7841</td>
                                                <td>Jul 31, 2024</td>
                                                <td>INV-2024-003 — Payment received</td>
                                                <td><span class="txn-type credit"><i
                                                            class="bi bi-arrow-down-circle-fill"></i> Credit</span></td>
                                                <td class="amount-cell credit">+$80,000</td>
                                                <td class="amount-cell">$185,000</td>
                                                <td><span class="cat-badge infra" style="font-size:11px">Bank</span></td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-family:var(--font-mono);font-size:12px;color:var(--text-muted)">
                                                    TXN-7789</td>
                                                <td>Jul 31, 2024</td>
                                                <td>Sprint Q3 — Developer salaries</td>
                                                <td><span class="txn-type debit"><i
                                                            class="bi bi-arrow-up-circle-fill"></i> Debit</span></td>
                                                <td class="amount-cell debit">-$56,500</td>
                                                <td class="amount-cell">$105,000</td>
                                                <td><span class="cat-badge dev" style="font-size:11px">Payroll</span></td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-family:var(--font-mono);font-size:12px;color:var(--text-muted)">
                                                    TXN-7742</td>
                                                <td>Jun 30, 2024</td>
                                                <td>INV-2024-002 — Payment received</td>
                                                <td><span class="txn-type credit"><i
                                                            class="bi bi-arrow-down-circle-fill"></i> Credit</span></td>
                                                <td class="amount-cell credit">+$60,000</td>
                                                <td class="amount-cell">$161,500</td>
                                                <td><span class="cat-badge infra" style="font-size:11px">Bank</span></td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-family:var(--font-mono);font-size:12px;color:var(--text-muted)">
                                                    TXN-7701</td>
                                                <td>Jun 05, 2024</td>
                                                <td>Travel — Client site visits (3)</td>
                                                <td><span class="txn-type debit"><i
                                                            class="bi bi-arrow-up-circle-fill"></i> Debit</span></td>
                                                <td class="amount-cell debit">-$6,350</td>
                                                <td class="amount-cell">$101,500</td>
                                                <td><span class="cat-badge travel" style="font-size:11px">Expense</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-family:var(--font-mono);font-size:12px;color:var(--text-muted)">
                                                    TXN-7650</td>
                                                <td>May 18, 2024</td>
                                                <td>Software licences — Tooling Q2</td>
                                                <td><span class="txn-type debit"><i
                                                            class="bi bi-arrow-up-circle-fill"></i> Debit</span></td>
                                                <td class="amount-cell debit">-$9,800</td>
                                                <td class="amount-cell">$107,850</td>
                                                <td><span class="cat-badge ops" style="font-size:11px">Vendor</span></td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-family:var(--font-mono);font-size:12px;color:var(--text-muted)">
                                                    TXN-7612</td>
                                                <td>Apr 30, 2024</td>
                                                <td>Contractor fees — Security &amp; QA</td>
                                                <td><span class="txn-type debit"><i
                                                            class="bi bi-arrow-up-circle-fill"></i> Debit</span></td>
                                                <td class="amount-cell debit">-$34,200</td>
                                                <td class="amount-cell">$117,650</td>
                                                <td><span class="cat-badge hr" style="font-size:11px">Payroll</span></td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-family:var(--font-mono);font-size:12px;color:var(--text-muted)">
                                                    TXN-7580</td>
                                                <td>Apr 28, 2024</td>
                                                <td>INV-2024-001 — Payment received</td>
                                                <td><span class="txn-type credit"><i
                                                            class="bi bi-arrow-down-circle-fill"></i> Credit</span></td>
                                                <td class="amount-cell credit">+$45,000</td>
                                                <td class="amount-cell">$151,850</td>
                                                <td><span class="cat-badge infra" style="font-size:11px">Bank</span></td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-family:var(--font-mono);font-size:12px;color:var(--text-muted)">
                                                    TXN-7541</td>
                                                <td>Apr 02, 2024</td>
                                                <td>Cloud hosting &amp; migration setup</td>
                                                <td><span class="txn-type debit"><i
                                                            class="bi bi-arrow-up-circle-fill"></i> Debit</span></td>
                                                <td class="amount-cell debit">-$18,750</td>
                                                <td class="amount-cell">$106,850</td>
                                                <td><span class="cat-badge infra" style="font-size:11px">Vendor</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-family:var(--font-mono);font-size:12px;color:var(--text-muted)">
                                                    TXN-7499</td>
                                                <td>Mar 15, 2024</td>
                                                <td>Frontend &amp; backend sprint Q1</td>
                                                <td><span class="txn-type debit"><i
                                                            class="bi bi-arrow-up-circle-fill"></i> Debit</span></td>
                                                <td class="amount-cell debit">-$52,400</td>
                                                <td class="amount-cell">$125,600</td>
                                                <td><span class="cat-badge dev" style="font-size:11px">Payroll</span></td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-family:var(--font-mono);font-size:12px;color:var(--text-muted)">
                                                    TXN-7400</td>
                                                <td>Feb 01, 2024</td>
                                                <td>Project kick-off advance payment</td>
                                                <td><span class="txn-type credit"><i
                                                            class="bi bi-arrow-down-circle-fill"></i> Credit</span></td>
                                                <td class="amount-cell credit">+$178,000</td>
                                                <td class="amount-cell">$178,000</td>
                                                <td><span class="cat-badge infra" style="font-size:11px">Bank</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>



                        </main>

                    </div>
                </div>

            </div>
        </div>
        <!-- /.col-->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /* ═══════════════════════════════════════════
                       CHART.JS DEFAULTS
                    ════════════════════════════════════════════ */
        Chart.defaults.font.family = "'DM Sans', sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#64748b';

        const gridColor = '#f1f5f9';

        /* ─── Budget vs Expense Bar Chart ─── */
        const budgetCtx = document.getElementById('budgetExpenseChart').getContext('2d');
        new Chart(budgetCtx, {
            type: 'bar',
            data: {
                labels: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'],
                datasets: [{
                        label: 'Budget',
                        data: [25000, 25000, 25000, 25000, 25000, 25000, 24800, 24800, 24800, 18600],
                        backgroundColor: 'rgba(37,99,235,0.15)',
                        borderColor: '#2563eb',
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'Expense',
                        data: [8000, 52400, 62800, 72600, 78950, 135450, 148200, 162000, 172000, 178000],
                        backgroundColor: 'rgba(225,29,72,0.12)',
                        borderColor: '#e11d48',
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f1623',
                        titleColor: '#e2e8f0',
                        bodyColor: '#94a3b8',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: ctx => ` ${ctx.dataset.label}: $${ctx.parsed.y.toLocaleString()}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: '#94a3b8',
                            callback: v => '$' + (v / 1000).toFixed(0) + 'K'
                        }
                    }
                }
            }
        });

        /* ─── Income vs Profit Bar Chart ─── */
        const incomeCtx = document.getElementById('incomeProfitChart').getContext('2d');
        new Chart(incomeCtx, {
            type: 'bar',
            data: {
                labels: ['Q1', 'Q2', 'Q3', 'Q4 (est.)'],
                datasets: [{
                        label: 'Income',
                        data: [45000, 60000, 80000, 35000],
                        backgroundColor: 'rgba(13,148,136,0.18)',
                        borderColor: '#0d9488',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    },
                    {
                        label: 'Profit',
                        data: [8000, 14000, 21000, 7000],
                        backgroundColor: 'rgba(124,58,237,0.18)',
                        borderColor: '#7c3aed',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f1623',
                        titleColor: '#e2e8f0',
                        bodyColor: '#94a3b8',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: ctx => ` ${ctx.dataset.label}: $${ctx.parsed.y.toLocaleString()}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: '#94a3b8',
                            callback: v => '$' + (v / 1000).toFixed(0) + 'K'
                        }
                    }
                }
            }
        });

        /* ═══════════════════════════════════════════
           INTERSECTION OBSERVER — SCROLL REVEAL
        ════════════════════════════════════════════ */
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    // Animate progress bars when their section becomes visible
                    entry.target.querySelectorAll('.team-progress-bar[data-target]').forEach(bar => {
                        setTimeout(() => {
                            bar.style.width = bar.dataset.target + '%';
                        }, 300);
                    });
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.08,
            rootMargin: '0px 0px -40px 0px'
        });

        document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

        /* ═══════════════════════════════════════════
           ANIMATED PROGRESS BARS (existing sections)
        ════════════════════════════════════════════ */
        function animateBar(id, target, delay = 500) {
            const el = document.getElementById(id);
            if (!el) return;
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) {
                    setTimeout(() => {
                        el.style.width = target + '%';
                    }, delay);
                    observer.unobserve(el);
                }
            }, {
                threshold: 0.3
            });
            observer.observe(el);
        }

        animateBar('taskBar', 74, 300);
        animateBar('sprintBar', 88, 450);
        animateBar('bugBar', 63, 600);
        animateBar('payBar', 84, 300);
        animateBar('inv4Bar', 0, 300);
        animateBar('inv5Bar', 0, 300);

        /* ═══════════════════════════════════════════
           EXPENSE TABLE LIVE SEARCH
        ════════════════════════════════════════════ */
        document.getElementById('expenseSearch').addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#expenseTable tbody tr').forEach(row => {
                const match = row.textContent.toLowerCase().includes(q);
                row.style.transition = 'opacity .2s';
                row.style.opacity = match ? '1' : '0';
                row.style.display = match ? '' : 'none';
            });
        });

        /* ═══════════════════════════════════════════
           BACK TO TOP BUTTON
        ════════════════════════════════════════════ */
        const btt = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            btt.classList.toggle('show', window.scrollY > 320);
        }, {
            passive: true
        });
        btt.addEventListener('click', () => window.scrollTo({
            top: 0,
            behavior: 'smooth'
        }));

        /* ═══════════════════════════════════════════
           KPI COUNTER ANIMATION
        ════════════════════════════════════════════ */
        function animateCounter(el, target, prefix = '', suffix = '') {
            let start = 0;
            const duration = 1200;
            const step = timestamp => {
                if (!start) start = timestamp;
                const progress = Math.min((timestamp - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = prefix + Math.floor(eased * target).toLocaleString() + suffix;
                if (progress < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        }

        const kpiObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = [{
                            id: 'kpi-budget',
                            val: 248,
                            prefix: '$',
                            suffix: 'K'
                        },
                        {
                            id: 'kpi-expense',
                            val: 178,
                            prefix: '$',
                            suffix: 'K'
                        },
                        {
                            id: 'kpi-invoice',
                            val: 220,
                            prefix: '$',
                            suffix: 'K'
                        },
                        {
                            id: 'kpi-received',
                            val: 185,
                            prefix: '$',
                            suffix: 'K'
                        },
                        {
                            id: 'kpi-due',
                            val: 35,
                            prefix: '$',
                            suffix: 'K'
                        },
                        {
                            id: 'kpi-profit',
                            val: 42,
                            prefix: '+$',
                            suffix: 'K'
                        },
                    ];
                    counters.forEach(c => {
                        const el = document.getElementById(c.id);
                        if (el) animateCounter(el, c.val, c.prefix, c.suffix);
                    });
                    kpiObserver.disconnect();
                }
            });
        }, {
            threshold: 0.3
        });

        const kpiSection = document.querySelector('.kpi-section-anchor');
        if (kpiSection) kpiObserver.observe(kpiSection);
    </script>
@endsection
