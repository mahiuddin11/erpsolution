@extends('backend.layouts.master')

@section('title')
    HRM – {{ $title }}
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet" />

    <style>
        :root {
            --bg-page: #f0f2f7;
            --bg-card: #ffffff;
            --sidebar-bg: #0f1623;
            --accent: #3b6fff;
            --accent-light: #eef2ff;
            --accent-glow: rgba(59, 111, 255, .15);
            --holiday-clr: #ef4444;
            --holiday-bg: #fef2f2;
            --holiday-glow: rgba(239, 68, 68, .12);
            --today-clr: #3b6fff;
            --today-bg: #eef2ff;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --border: #e5e7eb;
            --border-light: #f3f4f6;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, .07);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, .09);
            --shadow-lg: 0 12px 40px rgba(0, 0, 0, .13);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            /* --font-display: 'Syne', sans-serif; */
            --font-body: 'DM Sans', sans-serif;
            --tr: .18s ease;
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

        /* ── Page ── */
        .page-content {
            padding: 24px 20px;
            max-width: 1300px;
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

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }

        .page-title {
            font-family: var(--font-display);
            font-size: 26px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -.5px;
        }

        .page-subtitle {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        /* ── Year section ── */
        .year-section {
            margin-bottom: 28px;
        }

        .year-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 22px;
            background: var(--sidebar-bg);
            border-radius: var(--radius-md) var(--radius-md) 0 0;
            cursor: pointer;
            user-select: none;
        }

        .year-header.collapsed {
            border-radius: var(--radius-md);
        }

        .year-badge {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -1px;
        }

        .year-count {
            font-size: 12px;
            font-weight: 600;
            background: rgba(255, 255, 255, .12);
            color: rgba(255, 255, 255, .75);
            padding: 3px 10px;
            border-radius: 20px;
        }

        .year-toggle {
            margin-left: auto;
            color: rgba(255, 255, 255, .55);
            font-size: 18px;
            transition: transform var(--tr);
        }

        .year-toggle.open {
            transform: rotate(180deg);
        }

        .year-tag {
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .4);
            padding: 3px 8px;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 4px;
        }

        /* ── Month grid ── */
        .month-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
            padding: 16px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-top: none;
            border-radius: 0 0 var(--radius-md) var(--radius-md);
        }

        .month-card {
            position: relative;
            background: var(--bg-page);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-md);
            padding: 16px 18px;
            transition: box-shadow var(--tr), border-color var(--tr), transform var(--tr);
            cursor: default;
        }

        .month-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .month-card.is-current {
            border-color: var(--accent);
            background: var(--accent-light);
        }

        .month-card.is-current::before {
            content: 'Current';
            position: absolute;
            top: -1px;
            right: 10px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            background: var(--accent);
            color: #fff;
            padding: 2px 8px;
            border-radius: 0 0 5px 5px;
        }

        .month-name {
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .month-card.is-current .month-name {
            color: var(--accent);
        }

        .holiday-count {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .holiday-count.has-holidays {
            background: var(--holiday-bg);
            color: var(--holiday-clr);
        }

        .holiday-count.no-holidays {
            background: var(--border-light);
            color: var(--text-muted);
        }

        .holiday-count i {
            font-size: 11px;
        }

        .btn-setup {
            width: 100%;
            margin-top: 12px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-weight: 600;
            font-size: 12.5px;
            padding: 8px 0;
            transition: opacity var(--tr);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-setup:hover {
            opacity: .88;
        }

        .btn-setup.secondary {
            background: var(--bg-card);
            color: var(--accent);
            border: 1.5px solid var(--accent);
        }

        .btn-setup.secondary:hover {
            background: var(--accent-light);
        }

        /* ── Calendar Modal ── */
        .cal-modal .modal-content {
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .cal-modal-header {
            background: var(--sidebar-bg);
            padding: 20px 24px 16px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .cal-modal-title {
            font-family: var(--font-display);
            font-size: 17px;
            font-weight: 800;
            color: #fff;
        }

        .cal-modal-sub {
            font-size: 11.5px;
            color: rgba(255, 255, 255, .4);
            margin-top: 2px;
        }

        .modal-x {
            background: rgba(255, 255, 255, .1);
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            color: rgba(255, 255, 255, .7);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: background var(--tr);
            flex-shrink: 0;
        }

        .modal-x:hover {
            background: rgba(255, 255, 255, .2);
            color: #fff;
        }

        /* ── Calendar ── */
        .calendar-wrap {
            padding: 22px 24px;
        }

        .cal-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .cal-month-label {
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 800;
            color: var(--text-primary);
        }

        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }

        .cal-day-header {
            text-align: center;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 6px 0 10px;
        }

        .cal-day-header.weekend {
            color: var(--holiday-clr);
        }

        .cal-day {
            position: relative;
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: 1.5px solid transparent;
            transition: all var(--tr);
            user-select: none;
            gap: 2px;
        }

        .cal-day:not(.empty):not(.is-friday):hover {
            border-color: var(--accent);
            background: var(--accent-light);
            color: var(--accent);
        }

        .cal-day.empty {
            cursor: default;
            opacity: 0;
        }

        .cal-day.is-today {
            background: var(--today-bg);
            border-color: var(--today-clr);
            color: var(--today-clr);
            font-weight: 700;
        }

        .cal-day.is-holiday {
            background: var(--holiday-bg);
            border-color: var(--holiday-clr);
            color: var(--holiday-clr);
            font-weight: 700;
        }

        .cal-day.is-holiday .day-label {
            font-size: 8px;
            font-weight: 700;
            letter-spacing: .3px;
            text-transform: uppercase;
            color: var(--holiday-clr);
            line-height: 1;
        }

        /* Holiday with note — subtle dot indicator */
        .cal-day.has-note::after {
            content: '';
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--holiday-clr);
            opacity: .7;
        }

        .cal-day.is-friday {
            color: var(--holiday-clr);
            opacity: .55;
            cursor: not-allowed;
        }

        .cal-day.is-friday:hover {
            border-color: transparent;
            background: transparent;
            color: var(--holiday-clr);
        }

        .day-num {
            font-size: 14px;
            font-weight: 600;
            line-height: 1;
        }

        .day-label {
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: .3px;
            text-transform: uppercase;
            line-height: 1;
            color: transparent;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding: 0 2px;
        }

        /* Selected count badge */
        .selected-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12.5px;
            font-weight: 600;
            background: var(--holiday-bg);
            color: var(--holiday-clr);
            padding: 4px 12px;
            border-radius: 20px;
        }

        /* Save bar */
        .modal-save-bar {
            padding: 14px 24px;
            background: var(--border-light);
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-save {
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-weight: 600;
            font-size: 13px;
            padding: 9px 22px;
            transition: opacity var(--tr);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .btn-save:hover {
            opacity: .88;
        }

        .btn-save:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .btn-cancel-modal {
            background: transparent;
            color: var(--text-secondary);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-weight: 500;
            font-size: 13px;
            padding: 9px 16px;
            cursor: pointer;
            transition: background var(--tr);
        }

        .btn-cancel-modal:hover {
            background: var(--bg-card);
        }

        /* Toast */
        .custom-toast {
            background: var(--sidebar-bg);
            border: none;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            min-width: 240px;
        }

        .custom-toast .toast-body {
            color: rgba(255, 255, 255, .85);
            font-size: 12.5px;
            font-weight: 500;
            padding: 13px 16px;
        }

        /* Loading spinner */
        .spin-sm {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, .3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .6s linear infinite;
            display: none;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Right-click Context Menu ── */
        #calContextMenu {
            position: fixed;
            z-index: 9999;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            padding: 6px 0;
            min-width: 170px;
            display: none;
            animation: menuFadeIn .12s ease;
        }

        @keyframes menuFadeIn {
            from {
                opacity: 0;
                transform: scale(.96) translateY(-4px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .ctx-header {
            padding: 6px 14px 4px;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-light);
            margin-bottom: 4px;
        }

        .ctx-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-primary);
            cursor: pointer;
            transition: background var(--tr);
        }

        .ctx-item:hover {
            background: var(--border-light);
        }

        .ctx-item i {
            font-size: 14px;
            width: 16px;
            text-align: center;
        }

        .ctx-item.danger {
            color: var(--holiday-clr);
        }

        .ctx-item.danger:hover {
            background: var(--holiday-bg);
        }

        .ctx-divider {
            height: 1px;
            background: var(--border-light);
            margin: 4px 0;
        }

        /* Note Modal */
        .note-modal .modal-content {
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .note-modal-header {
            background: var(--sidebar-bg);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .note-modal-title {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
        }

        .note-textarea {
            width: 100%;
            min-height: 90px;
            resize: vertical;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px 12px;
            font-family: var(--font-body);
            font-size: 13px;
            color: var(--text-primary);
            background: var(--bg-page);
            transition: border var(--tr);
            outline: none;
        }

        .note-textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .note-char-count {
            font-size: 11px;
            color: var(--text-muted);
            text-align: right;
            margin-top: 4px;
        }

        @media(max-width:768px) {
            .month-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .cal-grid {
                gap: 2px;
            }

            .cal-day {
                font-size: 12px;
            }
        }

        @media(max-width:480px) {
            .month-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
@endsection

@section('admin-content')
    <div class="main-wrapper ">
        <main class="col-12 mt-2">

            {{-- Page Header --}}
            <div class="page-header">
                <div>
                    <h1 class="page-title">Holiday Setup</h1>
                    <p class="page-subtitle">Manage office holidays — they auto-apply in Attendance Log</p>
                </div>
            </div>

            {{-- ═══════════ CURRENT YEAR ═══════════ --}}
            <div class="year-section">
                <div class="year-header">
                    <span class="year-badge">{{ $currentYear ?? '2026' }}</span>
                    <span class="year-tag">Running Year</span>
                    <span class="year-count" id="totalHolidaysRunning">Loading…</span>
                </div>
                <div class="month-grid" id="runningMonthGrid">

                    @foreach ($runningMonths as $m)
                        @php $isCurrent = ($m['number'] == now()->month); @endphp
                        <div class="month-card {{ $isCurrent ? 'is-current' : '' }}"
                            id="card-{{ $currentYear ?? '2026' }}-{{ $m['number'] }}">
                            <div class="month-name">{{ $m['name'] }}</div>

                            {{--
                                FIX 1: holiday_count এখন শুধু database থেকে আসা manually set
                                holidays count করে। Friday গুলো count হবে না।
                                Controller এর buildMonthsForYear() method এ শুধু
                                holidays table এর row count করা হয়, Friday auto-count নয়।
                            --}}
                            <div class="holiday-count {{ $m['holiday_count'] > 0 ? 'has-holidays' : 'no-holidays' }}"
                                id="count-{{ $currentYear ?? '2026' }}-{{ $m['number'] }}">
                                <i
                                    class="bi {{ $m['holiday_count'] > 0 ? 'bi-calendar-x-fill' : 'bi-calendar-check' }}"></i>
                                @php
                                    $year = $currentYear;
                                    $month = $m['number'];

                                    $start = \Carbon\Carbon::create($year, $month, 1);
                                    $end = $start->copy()->endOfMonth();

                                    $fridays = 0;
                                    for ($date = $start->copy(); $date <= $end; $date->addDay()) {
                                        if ($date->isFriday()) {
                                            $fridays++;
                                        }
                                    }

                                    $total = $m['holiday_count'] + $fridays;
                                @endphp

                                <span>
                                    {{ $total > 0 ? $total . ' Holiday' . ($total > 1 ? 's' : '') : 'No Holidays' }}
                                </span>
                            </div>

                            <button class="btn-setup mt-2"
                                onclick="openHolidayModal({{ $currentYear ?? '2026' }}, {{ $m['number'] }}, '{{ $m['name'] }}')">
                                <i class="bi bi-pencil-square"></i> Setup
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ═══════════ PREVIOUS YEARS ═══════════ --}}
            @foreach ($previousYears as $year => $months)
                @php
                    $totalH = collect($months)->sum('holiday_count');
                @endphp
                <div class="year-section">
                    <div class="year-header collapsed" onclick="toggleYear(this, {{ $year }})"
                        style="cursor:pointer;">
                        <span class="year-badge">{{ $year }}</span>
                        <span class="year-tag">Previous Year</span>
                        <span class="year-count">{{ $totalH }} Holiday{{ $totalH != 1 ? 's' : '' }}</span>
                        <i class="bi bi-chevron-down year-toggle"></i>
                    </div>
                    <div class="month-grid" id="prev-grid-{{ $year }}"
                        style="display:none; border-radius:0 0 var(--radius-md) var(--radius-md);">
                        @foreach ($months as $m)
                            <div class="month-card" id="card-{{ $year }}-{{ $m['number'] }}">
                                <div class="month-name">{{ $m['name'] }}</div>
                                <div class="holiday-count {{ $m['holiday_count'] > 0 ? 'has-holidays' : 'no-holidays' }}"
                                    id="count-{{ $year }}-{{ $m['number'] }}">
                                    <i
                                        class="bi {{ $m['holiday_count'] > 0 ? 'bi-calendar-x-fill' : 'bi-calendar-check' }}"></i>
                                    <span>{{ $m['holiday_count'] > 0 ? $m['holiday_count'] . ' Holiday' . ($m['holiday_count'] > 1 ? 's' : '') : 'No Holidays' }}</span>
                                </div>
                                <button class="btn-setup secondary mt-2"
                                    onclick="openHolidayModal({{ $year }}, {{ $m['number'] }}, '{{ $m['name'] }}')">
                                    <i class="bi bi-pencil-square"></i> Setup
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </main>
    </div>

    {{-- ═══════════ HOLIDAY SETUP MODAL ═══════════ --}}
    <div class="modal fade cal-modal" id="holidayModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <div class="cal-modal-header">
                    <div>
                        <h5 class="cal-modal-title" id="modalTitle">Holiday Setup</h5>
                        <p class="cal-modal-sub" id="modalSub">Left-click to mark holiday · Right-click for options</p>
                    </div>
                    <button type="button" class="modal-x" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                </div>

                <div class="calendar-wrap">
                    {{-- Calendar header --}}
                    <div class="cal-nav">
                        <div class="cal-month-label" id="calMonthLabel">—</div>
                        <div class="selected-badge" id="selectedBadge">
                            <i class="bi bi-calendar-x-fill"></i>
                            <span
                                id="selectedCount">{{ $total > 0 ? $total . ' Holiday' . ($total > 1 ? 's' : '') : 'No Holidays' }}</span>
                        </div>
                    </div>

                    {{-- Day headers --}}
                    <div class="cal-grid" id="calDayHeaders">
                        <div class="cal-day-header">Sun</div>
                        <div class="cal-day-header">Mon</div>
                        <div class="cal-day-header">Tue</div>
                        <div class="cal-day-header">Wed</div>
                        <div class="cal-day-header">Thu</div>
                        <div class="cal-day-header weekend">Fri</div>
                        <div class="cal-day-header">Sat</div>
                    </div>

                    {{-- Calendar body --}}
                    <div class="cal-grid mt-1" id="calBody">
                        {{-- Injected by JS --}}
                    </div>
                </div>

                <div class="modal-save-bar">
                    <div style="font-size:12px;color:var(--text-secondary);">
                        <i class="bi bi-info-circle me-1"></i>
                        Left-click: toggle holiday &nbsp;·&nbsp; Right-click: add note / remove
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn-cancel-modal" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn-save" id="btnSave" onclick="saveHolidays()">
                            <span class="spin-sm" id="saveSpin"></span>
                            <i class="bi bi-check2-circle" id="saveIcon"></i>
                            Save Holidays
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ═══════════ RIGHT-CLICK CONTEXT MENU ═══════════ --}}
    <div id="calContextMenu">
        <div class="ctx-header" id="ctxDateLabel">12 April 2026</div>
        <div class="ctx-item" id="ctxAddNote" onclick="openNoteModal()">
            <i class="bi bi-chat-left-text"></i>
            <span id="ctxNoteText">Add Note</span>
        </div>
        <div class="ctx-divider"></div>
        <div class="ctx-item danger" id="ctxRemove" onclick="removeFromContext()">
            <i class="bi bi-x-circle"></i> Remove Holiday
        </div>
    </div>

    {{-- ═══════════ NOTE MODAL ═══════════ --}}
    <div class="modal fade note-modal" id="noteModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
            <div class="modal-content">
                <div class="note-modal-header">
                    <span class="note-modal-title"><i class="bi bi-chat-left-text me-2"></i>Holiday Note</span>
                    <button type="button" class="modal-x" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="modal-body p-4">
                    <p style="font-size:12.5px;color:var(--text-secondary);margin-bottom:10px;" id="noteDateLabel">—</p>
                    <textarea class="note-textarea" id="noteInput" maxlength="200"
                        placeholder="e.g. Independence Day, National Holiday…"
                        oninput="document.getElementById('noteCharCount').textContent=(200-this.value.length)+' chars left'"></textarea>
                    <div class="note-char-count" id="noteCharCount">200 chars left</div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn-cancel-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-save ms-2" onclick="saveNote()">
                        <i class="bi bi-check2-circle"></i> Save Note
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast custom-toast align-items-center" role="alert">
            <div class="d-flex">
                <div class="toast-body" id="toastBody">Done.</div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                    style="filter:invert(1) brightness(.7)"></button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        /* ═══════════════════════════════════════════════
                   ROUTES
                   ═══════════════════════════════════════════════ */
        const ROUTE_GET_MONTH = "{{ route('hrm.holiday.month') }}";
        const ROUTE_SAVE = "{{ route('hrm.holiday.save') }}";
        const CSRF = "{{ csrf_token() }}";
        const CURRENT_YEAR = {{ $currentYear ?? 2026 }};
        const CURRENT_MONTH = {{ now()->month }};
        const TODAY_STR = "{{ now()->format('Y-m-d') }}";

        /* ═══════════════════════════════════════════════
           STATE
           ═══════════════════════════════════════════════ */
        let modalYear = null;
        let modalMonth = null;

        /*
         * FIX 2: selectedDates এখন Map: { 'YYYY-MM-DD' => { title: 'Holiday', note: '' } }
         * Modal reopen করলে API থেকে existing dates load করে Map populate করা হয়।
         * Set এর পরিবর্তে Map ব্যবহার করলে date + note দুটোই ধরে রাখা যায়।
         */
        let selectedDates = new Map();

        // Context menu state
        let ctxDate = null;

        /* ═══════════════════════════════════════════════
           INIT
           ═══════════════════════════════════════════════ */
        $(document).ready(function() {
            updateRunningYearBadge();

            // Close context menu on any click outside
            document.addEventListener('click', closeContextMenu);
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') closeContextMenu();
            });
        });

        function updateRunningYearBadge() {
            let total = 0;
            document.querySelectorAll('[id^="count-' + CURRENT_YEAR + '-"]').forEach(el => {
                const txt = el.querySelector('span')?.textContent || '';
                const n = parseInt(txt);
                if (!isNaN(n)) total += n;
            });
            document.getElementById('totalHolidaysRunning').textContent =
                total + ' Holiday' + (total !== 1 ? 's' : '');
        }

        /* ═══════════════════════════════════════════════
           TOGGLE PREVIOUS YEAR
           ═══════════════════════════════════════════════ */
        function toggleYear(header, year) {
            const grid = document.getElementById('prev-grid-' + year);
            const toggle = header.querySelector('.year-toggle');
            const isOpen = grid.style.display !== 'none';

            if (isOpen) {
                grid.style.display = 'none';
                toggle.classList.remove('open');
                header.classList.add('collapsed');
            } else {
                grid.style.display = 'grid';
                toggle.classList.add('open');
                header.classList.remove('collapsed');
            }
        }

        /* ═══════════════════════════════════════════════
           OPEN MODAL
           FIX 2: API থেকে existing holidays load → Map এ store → calendar build
           ═══════════════════════════════════════════════ */
        function openHolidayModal(year, month, monthName) {
            modalYear = year;
            modalMonth = month;
            selectedDates.clear(); // clear previous state

            document.getElementById('modalTitle').textContent = 'Holiday Setup — ' + monthName + ' ' + year;
            document.getElementById('modalSub').textContent = 'Left-click to mark holiday · Right-click for options';
            document.getElementById('calMonthLabel').textContent = monthName + ' ' + year;
            updateSelectedBadge();
            closeContextMenu();

            // Load existing holidays from DB, then build calendar
            fetch(`${ROUTE_GET_MONTH}?year=${year}&month=${month}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success && res.holidays.length > 0) {
                        // FIX 2: Populate Map with existing dates AND their notes
                        res.holidays.forEach(h => {
                            selectedDates.set(h.date, {
                                title: h.title || 'Holiday',
                                note: h.note || ''
                            });
                        });
                    }
                    buildCalendar(year, month);
                    new bootstrap.Modal(document.getElementById('holidayModal')).show();
                })
                .catch(() => {
                    // On network error: show empty calendar anyway
                    buildCalendar(year, month);
                    new bootstrap.Modal(document.getElementById('holidayModal')).show();
                });
        }

        /* ═══════════════════════════════════════════════
           BUILD CALENDAR
           ═══════════════════════════════════════════════ */
        function buildCalendar(year, month) {
            const body = document.getElementById('calBody');
            body.innerHTML = '';

            const firstDay = new Date(year, month - 1, 1).getDay(); // 0=Sun
            const daysInMonth = new Date(year, month, 0).getDate();

            // Empty cells
            for (let i = 0; i < firstDay; i++) {
                const empty = document.createElement('div');
                empty.className = 'cal-day empty';
                body.appendChild(empty);
            }

            // Day cells
            for (let d = 1; d <= daysInMonth; d++) {
                const mm = String(month).padStart(2, '0');
                const dd = String(d).padStart(2, '0');
                const dateStr = `${year}-${mm}-${dd}`;

                const dayOfWeek = new Date(year, month - 1, d).getDay();
                const isFriday = dayOfWeek === 5;
                const isHoliday = selectedDates.has(dateStr);
                const isToday = dateStr === TODAY_STR;
                const hasNote = isHoliday && selectedDates.get(dateStr)?.note?.trim();

                const cell = document.createElement('div');
                cell.className = 'cal-day';
                cell.id = 'day-' + dateStr;
                cell.dataset.date = dateStr;

                if (isFriday) cell.classList.add('is-friday');
                if (isHoliday) cell.classList.add('is-holiday');
                if (isToday) cell.classList.add('is-today');
                if (hasNote) cell.classList.add('has-note');

                // Label text: Friday shows "Fri", holiday shows note excerpt or "Holiday"
                let labelText = '';
                if (isFriday) labelText = 'Fri';
                else if (isHoliday) labelText = hasNote ? truncate(hasNote, 8) : 'Holiday';

                cell.innerHTML = `
                    <span class="day-num">${d}</span>
                    <span class="day-label">${labelText}</span>`;

                // Left-click: toggle holiday (not on Friday)
                if (!isFriday) {
                    cell.addEventListener('click', () => toggleDay(dateStr));
                }

                // FIX 3: Right-click context menu (not on Friday, not on empty)
                if (!isFriday) {
                    cell.addEventListener('contextmenu', e => {
                        e.preventDefault();
                        openContextMenu(e, dateStr);
                    });
                }

                body.appendChild(cell);
            }

            updateSelectedBadge();
        }

        /* ═══════════════════════════════════════════════
           TOGGLE A DAY (left-click)
           ═══════════════════════════════════════════════ */
        function toggleDay(dateStr) {
            closeContextMenu();
            const cell = document.getElementById('day-' + dateStr);
            if (!cell || cell.classList.contains('is-friday')) return;

            if (selectedDates.has(dateStr)) {
                // Already holiday → remove
                selectedDates.delete(dateStr);
                cell.classList.remove('is-holiday', 'has-note');
                cell.querySelector('.day-label').textContent = '';
                cell.querySelector('.day-label').style.color = 'transparent';
            } else {
                // Not holiday → mark
                selectedDates.set(dateStr, {
                    title: 'Holiday',
                    note: ''
                });
                cell.classList.add('is-holiday');
                cell.querySelector('.day-label').textContent = 'Holiday';
                cell.querySelector('.day-label').style.color = '';
            }
            updateSelectedBadge();
        }



        // function updateSelectedBadge() {

        //     document.getElementById('selectedCount').textContent = selectedDates.size ;
        // }

        function updateSelectedBadge() {

            let manualCount = selectedDates.size;
            let fridayCount = countFridays(modalYear, modalMonth);
            let total = manualCount > 0 ?
                manualCount + fridayCount :
                fridayCount;

            let text = total > 0 ?
                total + ' Holiday' + (total > 1 ? 's' : '') :
                'No Holidays';

            document.getElementById('selectedCount').textContent = text;

        }

        function countFridays(year, month) {

            let fridays = 0;
            let daysInMonth = new Date(year, month, 0).getDate();

            for (let d = 1; d <= daysInMonth; d++) {

                let day = new Date(year, month - 1, d).getDay();
                if (day === 5) {
                    fridays++;
                }
            }

            return fridays;
        }

        /* ═══════════════════════════════════════════════
           FIX 3: RIGHT-CLICK CONTEXT MENU
           ═══════════════════════════════════════════════ */
        function openContextMenu(e, dateStr) {
            ctxDate = dateStr;
            const menu = document.getElementById('calContextMenu');
            const noteItem = document.getElementById('ctxAddNote');
            const noteText = document.getElementById('ctxNoteText');
            const removeItem = document.getElementById('ctxRemove');

            // Date label in menu header
            const d = new Date(dateStr + 'T00:00:00');
            document.getElementById('ctxDateLabel').textContent =
                d.toLocaleDateString('en-US', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });

            const isHoliday = selectedDates.has(dateStr);
            const hasNote = isHoliday && selectedDates.get(dateStr)?.note?.trim();

            // Show/hide options based on state
            if (isHoliday) {
                noteItem.style.display = '';
                noteText.textContent = hasNote ? 'Edit Note' : 'Add Note';
                removeItem.style.display = '';
            } else {
                // Not a holiday yet — only show "Mark as Holiday" via left click info
                // But still show "Add Note" so they can mark + add note in one go
                noteItem.style.display = '';
                noteText.textContent = 'Mark & Add Note';
                removeItem.style.display = 'none';
            }

            // Position menu near cursor, prevent overflow
            const vw = window.innerWidth;
            const vh = window.innerHeight;
            let x = e.clientX + 4;
            let y = e.clientY + 4;
            menu.style.display = 'block';
            const mw = menu.offsetWidth;
            const mh = menu.offsetHeight;
            if (x + mw > vw) x = vw - mw - 8;
            if (y + mh > vh) y = vh - mh - 8;
            menu.style.left = x + 'px';
            menu.style.top = y + 'px';
        }

        function closeContextMenu() {
            document.getElementById('calContextMenu').style.display = 'none';
        }

        /* Remove holiday from context menu */
        function removeFromContext() {
            closeContextMenu();
            if (!ctxDate) return;
            const cell = document.getElementById('day-' + ctxDate);
            if (!cell) return;
            selectedDates.delete(ctxDate);
            cell.classList.remove('is-holiday', 'has-note');
            const lbl = cell.querySelector('.day-label');
            lbl.textContent = '';
            lbl.style.color = 'transparent';
            updateSelectedBadge();
            ctxDate = null;
        }

        /* ═══════════════════════════════════════════════
           NOTE MODAL
           ═══════════════════════════════════════════════ */
        function openNoteModal() {
            closeContextMenu();
            if (!ctxDate) return;

            // If not yet a holiday, mark it first
            if (!selectedDates.has(ctxDate)) {
                selectedDates.set(ctxDate, {
                    title: 'Holiday',
                    note: ''
                });
                const cell = document.getElementById('day-' + ctxDate);
                if (cell) {
                    cell.classList.add('is-holiday');
                    cell.querySelector('.day-label').textContent = 'Holiday';
                    cell.querySelector('.day-label').style.color = '';
                }
                updateSelectedBadge();
            }

            const existingNote = selectedDates.get(ctxDate)?.note || '';
            const d = new Date(ctxDate + 'T00:00:00');
            document.getElementById('noteDateLabel').textContent =
                d.toLocaleDateString('en-US', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            document.getElementById('noteInput').value = existingNote;
            document.getElementById('noteCharCount').textContent = (200 - existingNote.length) + ' chars left';

            new bootstrap.Modal(document.getElementById('noteModal')).show();
        }

        function saveNote() {
            const note = document.getElementById('noteInput').value.trim();
            if (!ctxDate || !selectedDates.has(ctxDate)) return;

            // Update note in Map
            const entry = selectedDates.get(ctxDate);
            entry.note = note;
            selectedDates.set(ctxDate, entry);

            // Update cell label & dot indicator
            const cell = document.getElementById('day-' + ctxDate);
            if (cell) {
                const lbl = cell.querySelector('.day-label');
                if (note) {
                    cell.classList.add('has-note');
                    lbl.textContent = truncate(note, 8);
                    lbl.style.color = '';
                } else {
                    cell.classList.remove('has-note');
                    lbl.textContent = 'Holiday';
                    lbl.style.color = '';
                }
            }

            bootstrap.Modal.getInstance(document.getElementById('noteModal'))?.hide();
            showToast('Note saved for ' + ctxDate);
            ctxDate = null;
        }

        /* ═══════════════════════════════════════════════
           SAVE ALL HOLIDAYS TO SERVER
           ═══════════════════════════════════════════════ */
        function saveHolidays() {
            const btn = document.getElementById('btnSave');
            const spin = document.getElementById('saveSpin');
            const icon = document.getElementById('saveIcon');

            btn.disabled = true;
            spin.style.display = 'inline-block';
            icon.style.display = 'none';

            // Build payload: array of { date, note }
            const datesPayload = [];
            selectedDates.forEach((val, date) => {
                datesPayload.push({
                    date: date,
                    note: val.note || ''
                });
            });

            const payload = {
                year: modalYear,
                month: modalMonth,
                dates: datesPayload, // array of { date, note }
                _token: CSRF,
            };

            fetch(ROUTE_SAVE, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify(payload),
                })
                .then(r => r.json())
                .then(res => {
                    btn.disabled = false;
                    spin.style.display = 'none';
                    icon.style.display = '';

                    if (res.success) {
                        showToast(res.message);
                        updateMonthCard(modalYear, modalMonth, selectedDates.size);
                        setTimeout(() => {
                            bootstrap.Modal.getInstance(document.getElementById('holidayModal'))?.hide();
                        }, 700);
                    } else {
                        showToast('Error saving. Please try again.');
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    spin.style.display = 'none';
                    icon.style.display = '';
                    showToast('Network error. Please try again.');
                });
        }

        /* ═══════════════════════════════════════════════
           UPDATE CARD COUNTER AFTER SAVE
           ═══════════════════════════════════════════════ */
        function updateMonthCard(year, month, count) {
            const countEl = document.getElementById(`count-${year}-${month}`);
            if (!countEl) return;

            if (count > 0) {
                countEl.className = 'holiday-count has-holidays';
                countEl.innerHTML =
                    `<i class="bi bi-calendar-x-fill"></i><span>${count} Holiday${count > 1 ? 's' : ''}</span>`;
            } else {
                countEl.className = 'holiday-count no-holidays';
                countEl.innerHTML = `<i class="bi bi-calendar-check"></i><span>No Holidays</span>`;
            }

            if (year === CURRENT_YEAR) {
                updateRunningYearBadge();
            }
        }

        /* ═══════════════════════════════════════════════
           HELPERS
           ═══════════════════════════════════════════════ */
        function truncate(str, len) {
            return str.length > len ? str.substring(0, len) + '…' : str;
        }

        function showToast(msg) {
            document.getElementById('toastBody').textContent = msg;
            new bootstrap.Toast(document.getElementById('liveToast'), {
                delay: 3000
            }).show();
        }
    </script>
@endsection
