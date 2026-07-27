@extends('backend.layouts.master')

@section('title')
    {{ $pgTitle }} - Admin Panel
@endsection

<!-- Bootstrap Icons (safe alongside existing Bootstrap 4 / AdminLTE) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<!-- Google Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Common Dashboard CSS (shared by all department dashboards) -->
<!-- filemtime() diye cache-busting kora hoyeche, tai CSS update korle browser
     purono cached version na dekhiye notun version automatic load korbe -->
<link rel="stylesheet"
    href="{{ asset('css/dashboard-style.css') }}?v={{ filemtime(public_path('css/dashboard-style.css')) }}">

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $pgTitle }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a href="#">{{ $pgTitle }}</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="dashboard-wrap">

        @if ($user->branch_id !== null)
            <div class="row mb-3">
                <div class="col-md-6 offset-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-md-12">
                                <div class="position-relative p-3 bg-green" style="height: 150px">
                                    <div class="ribbon-wrapper ribbon-xl">
                                        <div class="ribbon bg-red">
                                            {{ $user->branch->branchCode ?? '' }} <br>
                                            {{ $user->branch->name ?? '' }}
                                        </div>
                                    </div>
                                    <h3>Today : {{ date('d-M-Y') }}</h3>
                                    <h2>Hello <br> {{ $user->name }}</h2>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Key Metrics -->
        <div class="row g-3 mb-3" id="primaryMetrics"></div>

        <!-- Secondary Metrics -->
        <div class="row g-3 mb-4" id="secondaryMetrics"></div>

        <!-- Department Distribution + Quick Actions -->
        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-building"></i> Department Distribution</div>
                    <div class="panel-body">
                        <div class="scroll-box" id="deptDistribution"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-briefcase"></i> Quick Actions</div>
                    <div class="panel-body">
                        <div class="scroll-box" id="quickActions"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Status -->
        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-calendar-event"></i> Employees on Leave</div>
                    <div class="panel-body">
                        <div class="scroll-box" id="onLeaveList"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-person-x"></i> Missing Attendance Today</div>
                    <div class="panel-body">
                        <div class="scroll-box" id="missingAttendance"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar + Recent Activity -->
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-calendar3"></i> Events &amp; Holidays Calendar</div>
                    <div class="panel-body">
                        <div class="calendar-head">
                            <button class="btn btn-sm btn-outline-secondary" id="prevMonth"><i
                                    class="bi bi-chevron-left"></i></button>
                            <div class="fw-semibold" id="calMonthLabel"></div>
                            <button class="btn btn-sm btn-outline-secondary" id="nextMonth"><i
                                    class="bi bi-chevron-right"></i></button>
                        </div>
                        <div class="cal-grid" id="calGrid"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 d-flex flex-column gap-3">
                <div class="panel">
                    <div class="panel-header"><i class="bi bi-calendar-check"></i> Recent Leave Applications</div>
                    <div class="panel-body">
                        <div class="scroll-box" style="height:230px" id="leaveApplications"></div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-header"><i class="bi bi-file-earmark-text"></i> Announcements</div>
                    <div class="panel-body">
                        <div class="scroll-box" style="height:230px" id="announcements"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        /* ---------------- Mock data (ei jaigay pore controller theke pathano real data bosbe) ---------------- */
        const stats = {
            total_employees: 248,
            present_today: 231,
            absent_today: 9,
            absent_yesterday: 13,
            on_leave: 8,
            pending_leaves: 4,
            total_branches: 6,
            total_departments: 12,
            total_promotions: 15,
            terminations: 3,
            department_distribution: [{
                    name: "Engineering",
                    value: 62
                },
                {
                    name: "Sales",
                    value: 41
                },
                {
                    name: "Marketing",
                    value: 28
                },
                {
                    name: "Customer Support",
                    value: 34
                },
                {
                    name: "Finance",
                    value: 18
                },
                {
                    name: "Human Resources",
                    value: 12
                },
                {
                    name: "Operations",
                    value: 30
                },
                {
                    name: "Design",
                    value: 23
                }
            ],
            calendar_events: [{
                    id: 1,
                    title: "Independence Day",
                    startDate: "2026-07-01",
                    type: "holiday",
                    color: "#ef4444"
                },
                {
                    id: 2,
                    title: "Team Standup",
                    startDate: "2026-07-06",
                    type: "meeting",
                    color: "#3b82f6"
                },
                {
                    id: 3,
                    title: "Payroll Run",
                    startDate: "2026-07-10",
                    type: "payroll",
                    color: "#10b981"
                },
                {
                    id: 4,
                    title: "Design Review",
                    startDate: "2026-07-15",
                    type: "meeting",
                    color: "#8b5cf6"
                },
                {
                    id: 5,
                    title: "Company Anniversary",
                    startDate: "2026-07-22",
                    type: "event",
                    color: "#f59e0b"
                }
            ],
            recent_leave_applications: [{
                    id: 1,
                    employee_name: "Rahim Uddin",
                    leave_type: "Sick Leave",
                    start_date: "2026-07-20",
                    end_date: "2026-07-21",
                    total_days: 2,
                    status: "Pending"
                },
                {
                    id: 2,
                    employee_name: "Nusrat Jahan",
                    leave_type: "Casual Leave",
                    start_date: "2026-07-18",
                    end_date: "2026-07-18",
                    total_days: 1,
                    status: "Approved"
                },
                {
                    id: 3,
                    employee_name: "Kamal Hossain",
                    leave_type: "Earned Leave",
                    start_date: "2026-07-14",
                    end_date: "2026-07-16",
                    total_days: 3,
                    status: "Rejected"
                },
                {
                    id: 4,
                    employee_name: "Farhana Akter",
                    leave_type: "Maternity Leave",
                    start_date: "2026-07-01",
                    end_date: "2026-09-01",
                    total_days: 60,
                    status: "Approved"
                }
            ],
            recent_announcements: [{
                    id: 1,
                    title: "Office closed for holiday",
                    description: "Office will remain closed on Aug 1 for a public holiday.",
                    created_at: "2026-07-24"
                },
                {
                    id: 2,
                    title: "New health policy",
                    description: "Updated health insurance policy takes effect next month.",
                    created_at: "2026-07-20"
                },
                {
                    id: 3,
                    title: "Town hall meeting",
                    description: "Quarterly town hall scheduled for next Friday at 3 PM.",
                    created_at: "2026-07-18"
                }
            ],
            employees_on_leave_today: [{
                    name: "Shahed Ali",
                    leave_type: "Sick Leave",
                    days: 2
                },
                {
                    name: "Mim Akter",
                    leave_type: "Casual Leave",
                    days: 1
                },
                {
                    name: "Tanvir Hasan",
                    leave_type: "Earned Leave",
                    days: 4
                }
            ],
            employees_without_attendance: [{
                    name: "Jahid Islam",
                    department: "Sales"
                },
                {
                    name: "Ruma Begum",
                    department: "Support"
                },
                {
                    name: "Arif Chowdhury",
                    department: "Engineering"
                }
            ]
        };

        const fmtDate = (d) => new Date(d).toLocaleDateString('en-US', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
        const initials = (name) => name.trim().charAt(0).toUpperCase();
        const avatarColors = ['#8b5cf6', '#3b82f6', '#10b981', '#f97316', '#ec4899', '#ef4444', '#f59e0b', '#0d9488'];

        function metricCard({
            title,
            value,
            sub,
            icon,
            theme
        }) {
            return `
  <div class="col-6 col-lg-3">
    <div class="metric-card card-${theme}">
      <div class="metric-top">
        <div class="metric-title">${title}</div>
        <i class="bi ${icon} metric-icon fs-5"></i>
      </div>
      <div class="metric-value">${value}</div>
      <div class="metric-sub">${sub}</div>
    </div>
  </div>`;
        }

        document.getElementById('primaryMetrics').innerHTML = [
            metricCard({
                title: 'Total Employees',
                value: stats.total_employees,
                sub: 'Active employees',
                icon: 'bi-people-fill',
                theme: 'blue'
            }),
            metricCard({
                title: 'Present Today',
                value: stats.present_today,
                sub: `${((stats.present_today/stats.total_employees)*100).toFixed(1)}% attendance rate`,
                icon: 'bi-person-check-fill',
                theme: 'green'
            }),
            metricCard({
                title: 'Absent Today',
                value: stats.absent_today,
                sub: `<i class="bi ${stats.absent_today > stats.absent_yesterday ? 'bi-arrow-up-right' : 'bi-arrow-down-right'}"></i> ${stats.absent_today - stats.absent_yesterday > 0 ? '+' : ''}${stats.absent_today - stats.absent_yesterday} from yesterday`,
                icon: 'bi-person-x-fill',
                theme: 'red'
            }),
            metricCard({
                title: 'On Leave',
                value: stats.on_leave,
                sub: `${stats.pending_leaves} pending approvals`,
                icon: 'bi-calendar-week-fill',
                theme: 'purple'
            })
        ].join('');

        document.getElementById('secondaryMetrics').innerHTML = [
            metricCard({
                title: 'Total Branch',
                value: stats.total_branches,
                sub: 'Active branches',
                icon: 'bi-building',
                theme: 'teal'
            }),
            metricCard({
                title: 'Total Department',
                value: stats.total_departments,
                sub: 'Across all branches',
                icon: 'bi-briefcase-fill',
                theme: 'indigo'
            }),
            metricCard({
                title: 'Total Promotions',
                value: stats.total_promotions,
                sub: 'This year',
                icon: 'bi-graph-up-arrow',
                theme: 'emerald'
            }),
            metricCard({
                title: 'Terminations',
                value: stats.terminations,
                sub: 'This month',
                icon: 'bi-graph-down-arrow',
                theme: 'rose'
            })
        ].join('');

        const deptColors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#06b6d4', '#f97316', '#84cc16'];
        const deptBox = document.getElementById('deptDistribution');
        if (stats.department_distribution.length) {
            const maxVal = Math.max(...stats.department_distribution.map(d => d.value));
            deptBox.innerHTML = stats.department_distribution.map((d, i) => `
    <div class="dept-row mb-3">
      <div class="dept-label">
        <span class="fw-medium">${d.name}</span>
        <span class="fw-bold">${d.value}</span>
      </div>
      <div class="progress">
        <div class="progress-bar" role="progressbar" style="width:${(d.value/maxVal)*100}%;background-color:${deptColors[i%8]}"></div>
      </div>
    </div>`).join('');
        } else {
            deptBox.innerHTML = `<div class="empty-state"><i class="bi bi-briefcase"></i><p>No departments found</p></div>`;
        }

        const actions = [{
                label: 'Add New Employee',
                icon: 'bi-person-plus'
            },
            {
                label: 'Mark Attendance',
                icon: 'bi-clock-history'
            },
            {
                label: 'Apply for Leave',
                icon: 'bi-calendar-plus'
            },
            {
                label: 'Process Payroll',
                icon: 'bi-credit-card'
            },
            {
                label: 'Create Promotion',
                icon: 'bi-graph-up-arrow'
            },
            {
                label: 'Create Resignation',
                icon: 'bi-graph-down-arrow'
            },
            {
                label: 'Create Holiday',
                icon: 'bi-calendar2-week'
            },
            {
                label: 'Create Warning',
                icon: 'bi-exclamation-triangle'
            }
        ];
        document.getElementById('quickActions').innerHTML = actions.map(a => `
  <button class="quick-btn" type="button"><i class="bi ${a.icon}"></i> ${a.label}</button>
`).join('');

        const onLeaveBox = document.getElementById('onLeaveList');
        onLeaveBox.innerHTML = stats.employees_on_leave_today.length ? stats.employees_on_leave_today.map((e, i) => `
  <div class="person-row">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar" style="background:${avatarColors[i%8]}">${initials(e.name)}</div>
      <div>
        <div class="person-name">${e.name}</div>
        <div class="person-sub">${e.leave_type}</div>
      </div>
    </div>
    <div class="text-muted small">${e.days} days</div>
  </div>`).join('') :
            `<div class="empty-state"><i class="bi bi-calendar-event"></i><p>No employees on leave today</p></div>`;

        const missingBox = document.getElementById('missingAttendance');
        missingBox.innerHTML = stats.employees_without_attendance.length ? stats.employees_without_attendance.map((e, i) => `
  <div class="person-row">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar" style="background:${avatarColors[(i+3)%8]}">${initials(e.name)}</div>
      <div>
        <div class="person-name">${e.name}</div>
        <div class="person-sub">${e.department}</div>
      </div>
    </div>
  </div>`).join('') :
            `<div class="empty-state"><i class="bi bi-person-check"></i><p>All employees marked attendance</p></div>`;

        const statusMap = {
            pending: {
                cls: 'status-pending',
                icon: '#eab308'
            },
            approved: {
                cls: 'status-approved',
                icon: '#22c55e'
            },
            rejected: {
                cls: 'status-rejected',
                icon: '#ef4444'
            }
        };
        const leaveBox = document.getElementById('leaveApplications');
        leaveBox.innerHTML = stats.recent_leave_applications.length ? stats.recent_leave_applications.map(l => {
                const s = statusMap[l.status.toLowerCase()] || {
                    cls: 'status-pending',
                    icon: '#3b82f6'
                };
                const dateStr = l.start_date === l.end_date ?
                    `${fmtDate(l.start_date)} (${l.total_days} day${l.total_days>1?'s':''})` :
                    `${fmtDate(l.start_date)} - ${fmtDate(l.end_date)} (${l.total_days} day${l.total_days>1?'s':''})`;
                return `
  <div class="person-row align-items-start">
    <div class="d-flex align-items-start gap-2">
      <div class="leave-icon" style="background:${s.icon}"><i class="bi bi-calendar-event"></i></div>
      <div>
        <div class="person-name">${l.employee_name} - ${l.leave_type}</div>
        <div class="person-sub">${dateStr}</div>
      </div>
    </div>
    <span class="status-badge ${s.cls}">${l.status}</span>
  </div>`;
            }).join('') :
            `<div class="empty-state"><i class="bi bi-calendar-event"></i><p>No recent leave applications</p></div>`;

        const annColors = ['#8b5cf6', '#3b82f6', '#10b981', '#f97316', '#ef4444', '#6366f1'];
        const annBox = document.getElementById('announcements');
        annBox.innerHTML = stats.recent_announcements.length ? stats.recent_announcements.map((a, i) => `
  <div class="person-row align-items-start">
    <div class="d-flex align-items-start gap-2">
      <div class="ann-icon" style="background:${annColors[i%6]}"><i class="bi bi-file-earmark-text"></i></div>
      <div>
        <div class="person-name">${a.title}</div>
        <div class="person-sub">${a.description}</div>
        <div class="person-sub">${fmtDate(a.created_at)}</div>
      </div>
    </div>
  </div>`).join('') :
            `<div class="empty-state"><i class="bi bi-file-earmark-text"></i><p>No active announcements</p></div>`;

        let calDate = new Date(2026, 6, 1);

        function renderCalendar() {
            const grid = document.getElementById('calGrid');
            const label = document.getElementById('calMonthLabel');
            const year = calDate.getFullYear();
            const month = calDate.getMonth();
            label.textContent = calDate.toLocaleDateString('en-US', {
                month: 'long',
                year: 'numeric'
            });

            const dows = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            let html = dows.map(d => `<div class="cal-dow">${d}</div>`).join('');

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const prevDays = new Date(year, month, 0).getDate();

            const eventsByDay = {};
            stats.calendar_events.forEach(e => {
                const ed = new Date(e.startDate);
                if (ed.getFullYear() === year && ed.getMonth() === month) {
                    const day = ed.getDate();
                    (eventsByDay[day] = eventsByDay[day] || []).push(e);
                }
            });

            for (let i = firstDay; i > 0; i--) {
                html += `<div class="cal-cell muted"><div class="cal-date">${prevDays - i + 1}</div></div>`;
            }
            for (let d = 1; d <= daysInMonth; d++) {
                const evts = eventsByDay[d] || [];
                html += `<div class="cal-cell">
      <div class="cal-date">${d}</div>
      ${evts.map(e => `<div class="cal-evt" style="background:${e.color}" title="${e.title}">${e.title}</div>`).join('')}
    </div>`;
            }
            const totalCells = firstDay + daysInMonth;
            const trailing = (7 - (totalCells % 7)) % 7;
            for (let i = 1; i <= trailing; i++) {
                html += `<div class="cal-cell muted"><div class="cal-date">${i}</div></div>`;
            }

            grid.innerHTML = html;
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            calDate.setMonth(calDate.getMonth() - 1);
            renderCalendar();
        });
        document.getElementById('nextMonth').addEventListener('click', () => {
            calDate.setMonth(calDate.getMonth() + 1);
            renderCalendar();
        });

        renderCalendar();
    </script>
@endsection
