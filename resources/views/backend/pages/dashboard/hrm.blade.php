@extends('backend.layouts.master')

@section('title')
    {{ $pgTitle }} - Admin Panel
@endsection

<!-- Common Dashboard CSS (shared by all department dashboards) -->
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

        {{-- @if ($user->branch_id !== null)
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
        @endif --}}

        {{-- Point 1: KPI cards -- Admin ar Employee duijoner jonno alada set --}}
        <div class="row g-3 mb-3" id="primaryMetrics"></div>

        @if ($isAdmin)
            <div class="row g-3 mb-4" id="secondaryMetrics"></div>
        @endif

        <!-- Department Distribution + Quick Actions -->
        <div class="row g-3 mb-4">

            @if ($isAdmin)
                <div class="col-lg-6">
                    <div class="panel h-100">
                        <div class="panel-header"><i class="bi bi-building"></i> Position Distribution</div>
                        <div class="panel-body">
                            <div class="scroll-box" id="deptDistribution"></div>
                        </div>
                    </div>
                </div>
            @endif


            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header"><i class="bi bi-briefcase"></i> Quick Actions</div>
                    <div class="panel-body">
                        <div class="scroll-box" id="quickActions"></div>
                    </div>
                </div>
            </div>

            @if ($isAdmin)
                <div class="col-lg-6">
                    <div class="panel h-100">
                        <div class="panel-header"><i class="bi bi-calendar-event"></i> Employees on Leave</div>
                        <div class="panel-body">
                            <div class="scroll-box" id="onLeaveList"></div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-header">
                        <i class="bi bi-person-check"></i>
                        @if ($isAdmin)
                            Attendance Today
                        @else
                            Attendance (Last 30 Days)
                        @endif
                    </div>
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
                    <div class="panel-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-file-earmark-text"></i> Announcements</span>
                        @if ($isAdmin)
                            <button class="btn btn-xs btn-success" type="button" id="btnNewAnnouncement">
                                <i class="bi bi-plus"></i> New
                            </button>
                        @endif
                    </div>
                    <div class="panel-body">
                        <div class="scroll-box" style="height:230px" id="announcements"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ============ Generic KPI Detail Modal (Point 1) ============ --}}
    <div class="modal fade" id="kpiDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kpiDetailModalTitle">Details</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="kpiDetailModalBody">
                    <div class="text-center text-muted py-3">Loading...</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ Quick Action iframe Modal (Point 3) ============ --}}
    <div class="modal fade" id="quickActionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content" style="height: 85vh;">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickActionModalTitle">Action</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-0 position-relative">
                    {{-- Processing overlay -- iframe full load na hoya porjonto dekhabe --}}
                    <div id="quickActionLoader" class="d-none flex-column align-items-center justify-content-center"
                        style="position:absolute; inset:0; background:#fff; z-index:5;">
                        <div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;">
                        </div>
                        <div class="text-muted small mt-2">Loading, please wait...</div>
                    </div>
                    <iframe id="quickActionFrame" src="" style="width:100%; height:100%; border:0;"></iframe>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ Announcement Create Modal (Point 3) ============ --}}
    @if ($isAdmin)
        <div class="modal fade" id="announcementCreateModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="announcementCreateForm">
                        <div class="modal-header">
                            <h5 class="modal-title">New Announcement</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div id="announcementFormError" class="alert alert-danger d-none"></div>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <select name="type" id="announcementType" class="form-control" required>
                                    <option value="public">Public (Everyone)</option>
                                    <option value="department">Department Only</option>
                                </select>
                            </div>
                            <div class="form-group d-none" id="announcementDepartmentWrap">
                                <label>Department</label>
                                <select name="department" id="announcementDepartment" class="form-control">
                                    <option value="">Select Department</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Expire Date (optional)</label>
                                <input type="date" name="expire_date" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Publish</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ============ Point 7: Leave Application Detail/Approve Modal ============ --}}
    <div class="modal fade" id="leaveDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Leave Application Details</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="leaveDetailModalBody">
                    <div class="text-center text-muted py-3">Loading...</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ Point 8: Announcement Detail/Update Modal ============ --}}
    <div class="modal fade" id="announcementDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Announcement Details</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="announcementDetailModalBody">
                    <div class="text-center text-muted py-3">Loading...</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const isAdmin = @json($isAdmin);
        const API_BASE = '/api/hrm-dashboard';
        const CSRF_TOKEN = '{{ csrf_token() }}';
        // Office coordinates -- existing checkLocation() logic-er sathe consistent rakha
        const OFFICE_LAT = "{{ config('officeLocation.latitude') }}";
        const OFFICE_LNG = "{{ config('officeLocation.longitude') }}";

        // Location badge generate -- office match hole "Office" (non-clickable),
        // GPS na thakle "GPS OFF" (non-clickable), onnothay clickable "Location" badge
        function locationBadge(lat, lng) {
            if (!lat || !lng) {
                return `<span class="badge badge-danger"><i class="bi bi-geo-alt"></i> GPS OFF</span>`;
            }
            if (String(lat) === OFFICE_LAT && String(lng) === OFFICE_LNG) {
                return `<span class="badge badge-success"><i class="bi bi-building"></i> Office</span>`;
            }
            return `<span class="badge badge-info loc-btn" style="cursor:pointer" data-lat="${lat}" data-lng="${lng}"><i class="bi bi-geo-alt-fill"></i> Location</span>`;
        }

        // Location badge e click korle -- direct 1 click e notun tab e Google Maps khule jabe,
        // kono modal-er dorkar nei
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.loc-btn');
            if (!btn) return;
            window.open(`https://www.google.com/maps?q=${btn.dataset.lat},${btn.dataset.lng}`, '_blank');
        });

        /* ---------------- Shared helpers ---------------- */
        const fmtDate = (d) => new Date(d).toLocaleDateString('en-US', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
        const initials = (name) => name.trim().charAt(0).toUpperCase();
        const avatarColors = ['#8b5cf6', '#3b82f6', '#10b981', '#f97316', '#ec4899', '#ef4444', '#f59e0b', '#0d9488'];
        const deptColors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#06b6d4', '#f97316', '#84cc16'];
        const annColors = ['#8b5cf6', '#3b82f6', '#10b981', '#f97316', '#ef4444', '#6366f1'];
        // Point 7: 'cancel' key jog kora holo -- DB enum e 'rejected' nei,
        // ache 'approved', 'pending', 'cancel'
        const statusMap = {
            pending: {
                cls: 'status-pending',
                icon: '#eab308'
            },
            approved: {
                cls: 'status-approved',
                icon: '#22c55e'
            },
            cancel: {
                cls: 'status-rejected',
                icon: '#ef4444'
            }
        };

        function metricCard({
            title,
            value,
            sub,
            icon,
            theme,
            type
        }) {
            return `
  <div class="col-6 col-lg-3">
    <div class="metric-card card-${theme} kpi-clickable" style="cursor:pointer" data-kpi-type="${type}">
      <div class="metric-top">
        <div class="metric-title">${title}</div>
        <i class="bi ${icon} metric-icon fs-5"></i>
      </div>
      <div class="metric-value">${value}</div>
      <div class="metric-sub">${sub}</div>
    </div>
  </div>`;
        }

        function bindKpiClicks() {
            document.querySelectorAll('.kpi-clickable').forEach(el => {
                el.addEventListener('click', () => openKpiDetailModal(el.dataset.kpiType, el.querySelector(
                    '.metric-title').textContent));
            });
        }

        function openKpiDetailModal(type, title) {
            document.getElementById('kpiDetailModalTitle').textContent = title;
            document.getElementById('kpiDetailModalBody').innerHTML =
                `<div class="text-center text-muted py-3">Loading...</div>`;
            $('#kpiDetailModal').modal('show');

            fetch(`${API_BASE}/kpi-details?type=${type}`)
                .then(r => r.json())
                .then(rows => {
                    const body = document.getElementById('kpiDetailModalBody');

                    if (!rows.length) {
                        body.innerHTML =
                            `<div class="empty-state"><i class="bi bi-inbox"></i><p>No records found</p></div>`;
                        return;
                    }

                    if (type === 'present_today') {
                        // Point: Check-in, Check-in Location, Check-out, Check-out Location -- structured
                        body.innerHTML =
                            `<ul class="list-group">${rows.map(r => `
                                                                                                                                                                                                                        <li class="list-group-item">
                                                                                                                                                                                                                            <div class="fw-bold mb-2">${r.title}</div>
                                                                                                                                                                                                                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                                                                                                                                                                                                                <span class="text-muted">Check-in: ${r.sign_in ?? '-'}</span>
                                                                                                                                                                                                                                ${locationBadge(r.in_lat, r.in_lng)}
                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                            <div class="d-flex justify-content-between align-items-center small">
                                                                                                                                                                                                                                <span class="text-muted">Check-out: ${r.sign_out ?? 'Not yet'}</span>
                                                                                                                                                                                                                                ${r.sign_out ? locationBadge(r.out_lat, r.out_lng) : '<span class="badge badge-secondary">Pending</span>'}
                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                        </li>`).join('')}</ul>`;
                    } else {
                        body.innerHTML =
                            `<ul class="list-group">${rows.map(r => `
                                                                                                                                                                                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                                                                                                                                                            <span>${r.title}</span>
                                                                                                                                                                                                                            <span class="text-muted small">${r.subtitle}</span>
                                                                                                                                                                                                                        </li>`).join('')}</ul>`;
                    }
                })
                .catch(() => {
                    document.getElementById('kpiDetailModalBody').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load details</p></div>`;
                });
        }

        /* ---------------- Point 1: KPI Cards -- Admin ebong Employee duijoner jonno ---------------- */
        fetch(`${API_BASE}/kpis`)
            .then(r => r.json())
            .then(kpi => {
                if (!kpi.visible) return;

                if (kpi.is_admin) {
                    document.getElementById('primaryMetrics').innerHTML = [
                        metricCard({
                            title: 'Total Employees',
                            value: kpi.total_employees,
                            sub: 'Active employees',
                            icon: 'bi-people-fill',
                            theme: 'blue',
                            type: 'total_employees'
                        }),
                        metricCard({
                            title: 'Present Today',
                            value: kpi.present_today,
                            sub: kpi.total_employees ?
                                `${((kpi.present_today / kpi.total_employees) * 100).toFixed(1)}% attendance rate` :
                                '',
                            icon: 'bi-person-check-fill',
                            theme: 'green',
                            type: 'present_today'
                        }),
                        metricCard({
                            title: 'Absent Today',
                            value: kpi.absent_today,
                            sub: '',
                            icon: 'bi-person-x-fill',
                            theme: 'red',
                            type: 'absent_today'
                        }),
                        metricCard({
                            title: 'On Leave',
                            value: kpi.on_leave,
                            sub: '',
                            icon: 'bi-calendar-week-fill',
                            theme: 'purple',
                            type: 'on_leave'
                        })
                    ].join('');

                    document.getElementById('secondaryMetrics').innerHTML = [
                        // metricCard({
                        //     title: 'Total Branch',
                        //     value: kpi.total_branches,
                        //     sub: 'Active branches',
                        //     icon: 'bi-building',
                        //     theme: 'teal',
                        //     type: 'total_branches'
                        // }),

                        metricCard({
                            title: 'New Employee',
                            value: kpi.new_employee_this_month,
                            sub: 'This month',
                            icon: 'bi-graph-up-arrow',
                            theme: 'emerald',
                            type: 'new_employee_this_month'
                        }),
                        metricCard({
                            title: 'Left Employee',
                            value: kpi.left_employee_this_month,
                            sub: 'This month',
                            icon: 'bi-graph-down-arrow',
                            theme: 'rose',
                            type: 'left_employee_this_month'
                        })
                    ].join('');

                    bindKpiClicks();
                } else {
                    // Employee KPI -- click/drill-down nei, shudhu summary card
                    document.getElementById('primaryMetrics').innerHTML = [
                        metricCard({
                            title: 'This Month Attendance',
                            value: kpi.this_month_attendance,
                            sub: 'Days present',
                            icon: 'bi-person-check-fill',
                            theme: 'green',
                            type: ''
                        }),
                        metricCard({
                            title: 'Absent',
                            value: kpi.absent_this_month,
                            sub: 'This month',
                            icon: 'bi-person-x-fill',
                            theme: 'red',
                            type: ''
                        }),
                        metricCard({
                            title: 'Late',
                            value: kpi.late_this_month,
                            sub: 'This month',
                            icon: 'bi-clock-history',
                            theme: 'purple',
                            type: ''
                        }),
                        metricCard({
                            title: 'Leave',
                            value: kpi.leave_this_month,
                            sub: 'This month',
                            icon: 'bi-calendar-week-fill',
                            theme: 'blue',
                            type: ''
                        })
                    ].join('');

                    // Employee card e click-drill-down nei, tai cursor:pointer soriye deya hocche
                    document.querySelectorAll('#primaryMetrics .kpi-clickable').forEach(el => {
                        el.style.cursor = 'default';
                    });
                }
            })
            .catch(() => {
                document.getElementById('primaryMetrics').innerHTML =
                    `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load KPI data</p></div>`;
            });

        /* ---------------- Point 2: Department Distribution -- circular card design ---------------- */
        fetch(`${API_BASE}/department-distribution`)
            .then(r => r.json())
            .then(data => {
                const deptBox = document.getElementById('deptDistribution');
                if (data.length) {
                    deptBox.innerHTML = `<div class="dept-grid">${data.map((d, i) => {
                        // Percent onujayi color: high present = green, medium = amber, kom = red
                        const ringColor = d.present_percent >= 75 ? '#22c55e' : d.present_percent >= 40 ? '#f59e0b' : '#ef4444';
                        return `
                                                                                                                                                                                                <div class="dept-card" title="${d.name}: ${d.present}/${d.total} present">
                                                                                                                                                                                                  <div class="dept-circle" style="background: conic-gradient(${ringColor} ${d.present_percent}%, #e5e7eb ${d.present_percent}% 100%);">
                                                                                                                                                                                                    <div class="dept-circle-inner">${d.present_percent}%</div>
                                                                                                                                                                                                  </div>
                                                                                                                                                                                                  <div class="dept-card-name">${d.name}</div>
                                                                                                                                                                                                  <div class="dept-card-sub">${d.present}/${d.total} present</div>
                                                                                                                                                                                                </div>`;
                    }).join('')}</div>`;
                } else {
                    deptBox.innerHTML =
                        `<div class="empty-state"><i class="bi bi-briefcase"></i><p>No departments found</p></div>`;
                }
            })
            .catch(() => {
                document.getElementById('deptDistribution').innerHTML =
                    `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load department data</p></div>`;
            });


        /* ---------------- Point 2: Department Distribution -- circular card design ---------------- */
        fetch(`${API_BASE}/department-distribution`)
            .then(r => r.json())
            .then(data => {
                const deptBox = document.getElementById('deptDistribution');
                if (data.length) {
                    deptBox.innerHTML = `<div class="dept-grid">${data.map((d, i) => {
                const ringColor = d.present_percent >= 75 ? '#22c55e' : d.present_percent >= 40 ? '#f59e0b' : '#ef4444';
               
                return `
                                                                                                                                                                        <div class="dept-card dept-card-clickable" style="cursor:pointer" data-position-id="${d.position_id}" title="${d.name}: ${d.present}/${d.total} present">
                                                                                                                                                                          <div class="dept-circle" style="background: conic-gradient(${ringColor} ${d.present_percent}%, #e5e7eb ${d.present_percent}% 100%);">
                                                                                                                                                                            <div class="dept-circle-inner">${d.present_percent}%</div>
                                                                                                                                                                          </div>
                                                                                                                                                                          <div class="dept-card-name">${d.name}</div>
                                                                                                                                                                          <div class="dept-card-sub">${d.present}/${d.total} present</div>
                                                                                                                                                                        </div>`;
            }).join('')}</div>`;

                    // Added: 2026-08-02 -- render hobar por click bind kora hocche
                    bindPositionCardClicks();
                } else {
                    deptBox.innerHTML =
                        `<div class="empty-state"><i class="bi bi-briefcase"></i><p>No departments found</p></div>`;
                }
            })
            .catch(() => {
                document.getElementById('deptDistribution').innerHTML =
                    `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load department data</p></div>`;
            });


        function bindPositionCardClicks() {
            document.querySelectorAll('.dept-card-clickable').forEach(el => {
                el.addEventListener('click', () => {
                    const positionId = el.dataset.positionId;
                    const positionName = el.querySelector('.dept-card-name').textContent;
                    openPositionDetailModal(positionId, positionName);
                });
            });
        }

        function openPositionDetailModal(positionId, title) {
            document.getElementById('kpiDetailModalTitle').textContent = title;
            document.getElementById('kpiDetailModalBody').innerHTML =
                `<div class="text-center text-muted py-3">Loading...</div>`;
            $('#kpiDetailModal').modal('show');

            fetch(`${API_BASE}/position-details?position_id=${positionId}`)
                .then(r => r.json())
                .then(rows => {
                    const body = document.getElementById('kpiDetailModalBody');

                    if (!rows.length) {
                        body.innerHTML =
                            `<div class="empty-state"><i class="bi bi-inbox"></i><p>No employees found</p></div>`;
                        return;
                    }

                    // Added: 2026-08-02 -- present/absent onujayi badge color
                    body.innerHTML =
                        `<ul class="list-group">${rows.map(r => `
                                                                                                                                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                                                                                                            <span>${r.title}</span>
                                                                                                                                                                            <span class="badge ${r.status === 'present' ? 'badge-success' : 'badge-danger'}">${r.subtitle}</span>
                                                                                                                                                                        </li>`).join('')}</ul>`;
                })
                .catch(() => {
                    document.getElementById('kpiDetailModalBody').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load details</p></div>`;
                });
        }

        /* ---------------- Point 3: Quick Actions -- click korle iframe modal e page open ---------------- */
        fetch(`${API_BASE}/quick-actions`)
            .then(r => r.json())
            .then(actions => {
                document.getElementById('quickActions').innerHTML = actions.length ?
                    actions.map(a => `
  <button class="quick-btn" type="button" data-url="${a.url}" data-label="${a.label}"><i class="bi ${a.icon}"></i> ${a.label}</button>
`).join('') :
                    `<div class="empty-state"><i class="bi bi-briefcase"></i><p>No actions available</p></div>`;

                document.querySelectorAll('#quickActions .quick-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const loader = document.getElementById('quickActionLoader');
                        const frame = document.getElementById('quickActionFrame');

                        document.getElementById('quickActionModalTitle').textContent = btn.dataset
                            .label;

                        // Loader dekhano -- d-none soriye d-flex jog kora hocche
                        // (Bootstrap class-er sathe conflict na kore)
                        loader.classList.remove('d-none');
                        loader.classList.add('d-flex');

                        frame.src = btn.dataset.url;
                        $('#quickActionModal').modal('show');

                        // Safety net: kono karone 'load' event fire na korle (page-er
                        // nijer JS crash, slow script, ba onno karone) loader jate
                        // chirokal atke na thake -- 6 second por force hide
                        clearTimeout(window._quickActionLoaderTimeout);
                        window._quickActionLoaderTimeout = setTimeout(() => {
                            loader.classList.add('d-none');
                            loader.classList.remove('d-flex');
                        }, 6000);
                    });
                });
            })
            .catch(() => {
                document.getElementById('quickActions').innerHTML =
                    `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load actions</p></div>`;
            });

        // Iframe full load hoye gele (ba error hoile) loader hide
        function hideQuickActionLoader() {
            clearTimeout(window._quickActionLoaderTimeout);
            const loader = document.getElementById('quickActionLoader');
            loader.classList.add('d-none');
            loader.classList.remove('d-flex');
        }
        document.getElementById('quickActionFrame').addEventListener('load', hideQuickActionLoader);
        document.getElementById('quickActionFrame').addEventListener('error', hideQuickActionLoader);

        $('#quickActionModal').on('hidden.bs.modal', function() {
            document.getElementById('quickActionFrame').src = '';
            hideQuickActionLoader();
        });

        /* ---------------- Point 4: Employees on Leave (unchanged) ---------------- */
        fetch(`${API_BASE}/employees-on-leave`)
            .then(r => r.json())
            .then(data => {
                const onLeaveBox = document.getElementById('onLeaveList');
                onLeaveBox.innerHTML = data.length ? data.map((e, i) => `
  <div class="person-row">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar" style="background:${avatarColors[i % 8]}">${initials(e.name)}</div>
      <div>
        <div class="person-name">${e.name}</div>
        <div class="person-sub">${e.leave_type}</div>
      </div>
    </div>
    <div class="text-muted small">${e.days} days</div>
  </div>`).join('') :
                    `<div class="empty-state"><i class="bi bi-calendar-event"></i><p>No employees on leave today</p></div>`;
            })
            .catch(() => {
                document.getElementById('onLeaveList').innerHTML =
                    `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load leave data</p></div>`;
            });

        /* ---------------- Point 5: "Attendance Today" ---------------- */
        function loadAttendanceList(params = {}) {
            const query = new URLSearchParams(params).toString();
            fetch(`${API_BASE}/attendance-list` + (query ? '?' + query : ''))
                .then(r => r.json())
                .then(data => {
                    const box = document.getElementById('missingAttendance');
                    box.innerHTML = data.length ? data.map(a => `
  <div class="person-row align-items-start">
    <div class="d-flex align-items-start gap-2 w-100">
      <div class="avatar" style="background:#22c55e">${a.employee_name.charAt(0)}</div>
      <div class="w-100">
        <div class="d-flex justify-content-between align-items-center">
    <div class="person-name mb-0">${a.employee_name}</div>
    <span class="text-muted small">${a.date}</span>
  </div>
        <div class="d-flex justify-content-between align-items-center small mt-1">
          <span class="person-sub mb-0">Check-in: ${a.sign_in ?? '-'}</span>
          ${locationBadge(a.in_lat, a.in_lng)}
        </div>
        <div class="d-flex justify-content-between align-items-center small mt-1">
          <span class="person-sub mb-0">Check-out: ${a.sign_out ?? 'Not yet'}</span>
          ${a.sign_out ? locationBadge(a.out_lat, a.out_lng) : '<span class="badge badge-secondary">Pending</span>'}
        </div>
       
      </div>
    </div>
  </div>`).join('') : `<div class="empty-state"><i class="bi bi-person-x"></i><p>No attendance recorded</p></div>`;
                })
                .catch(() => {
                    document.getElementById('missingAttendance').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load attendance data</p></div>`;
                });
        }
        loadAttendanceList();

        if (isAdmin) {
            fetch(`${API_BASE}/employee-options`)
                .then(r => r.json())
                .then(employees => {
                    const filterHtml = `
    <div class="d-flex gap-2 mb-2">
      <select id="attFilterEmployee" class="form-control form-control-sm">
        <option value="">All Employees</option>
        ${employees.map(e => `<option value="${e.id}">${e.name}</option>`).join('')}
      </select>
      <input type="text" id="attFilterSearch" class="form-control form-control-sm" placeholder="Search name...">
    </div>`;
                    document.getElementById('missingAttendance').insertAdjacentHTML('beforebegin', filterHtml);

                    const applyFilter = () => loadAttendanceList({
                        employee_id: document.getElementById('attFilterEmployee').value,
                        search: document.getElementById('attFilterSearch').value
                    });
                    document.getElementById('attFilterEmployee').addEventListener('change', applyFilter);
                    document.getElementById('attFilterSearch').addEventListener('input', applyFilter);
                })
                .catch(() => {});
        }

        /* ---------------- Point 6: Calendar (unchanged) ---------------- */
        let calDate = new Date();

        function fetchAndRenderCalendar() {
            const year = calDate.getFullYear();
            const month = calDate.getMonth() + 1;
            fetch(`${API_BASE}/calendar-events?year=${year}&month=${month}`)
                .then(r => r.json())
                .then(events => renderCalendar(events))
                .catch(() => renderCalendar([]));
        }

        function renderCalendar(events) {
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
            events.forEach(e => {
                const ed = new Date(e.startDate);
                if (ed.getFullYear() === year && ed.getMonth() === month) {
                    const day = ed.getDate();
                    (eventsByDay[day] = eventsByDay[day] || []).push(e);
                }
            });

            for (let i = firstDay; i > 0; i--) html +=
                `<div class="cal-cell muted"><div class="cal-date">${prevDays - i + 1}</div></div>`;
            for (let d = 1; d <= daysInMonth; d++) {
                const evts = eventsByDay[d] || [];
                html += `<div class="cal-cell"><div class="cal-date">${d}</div>
      ${evts.map(e => {
          // Note thakle note dekhabe, na thakle default title ("Holiday" ba "Weekend (Friday)")
          const displayText = (e.note && e.note.trim()) ? e.note : e.title;
          return `<div class="cal-evt" style="background:${e.color}" title="${e.title}">${displayText}</div>`;
      }).join('')}</div>`;
            }
            const totalCells = firstDay + daysInMonth;
            const trailing = (7 - (totalCells % 7)) % 7;
            for (let i = 1; i <= trailing; i++) html +=
                `<div class="cal-cell muted"><div class="cal-date">${i}</div></div>`;

            grid.innerHTML = html;
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            calDate.setMonth(calDate.getMonth() - 1);
            fetchAndRenderCalendar();
        });
        document.getElementById('nextMonth').addEventListener('click', () => {
            calDate.setMonth(calDate.getMonth() + 1);
            fetchAndRenderCalendar();
        });
        fetchAndRenderCalendar();

        /* ---------------- Point 7: Recent Leave Applications -- click e detail/approve modal ---------------- */
        function loadRecentLeaveApplications() {
            fetch(`${API_BASE}/recent-leave-applications`)
                .then(r => r.json())
                .then(data => {
                    const leaveBox = document.getElementById('leaveApplications');
                    leaveBox.innerHTML = data.length ? data.map(l => {
                            const s = statusMap[l.status.toLowerCase()] || {
                                cls: 'status-pending',
                                icon: '#3b82f6'
                            };
                            const dateStr = l.start_date === l.end_date ?
                                `${fmtDate(l.start_date)} (${l.total_days} day${l.total_days > 1 ? 's' : ''})` :
                                `${fmtDate(l.start_date)} - ${fmtDate(l.end_date)} (${l.total_days} day${l.total_days > 1 ? 's' : ''})`;
                            return `
  <div class="person-row align-items-start leave-clickable" style="cursor:pointer" data-leave-id="${l.id}">
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

                    bindLeaveClicks();
                })
                .catch(() => {
                    document.getElementById('leaveApplications').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load leave applications</p></div>`;
                });
        }
        loadRecentLeaveApplications();

        function bindLeaveClicks() {
            document.querySelectorAll('.leave-clickable').forEach(el => {
                el.addEventListener('click', () => openLeaveDetailModal(el.dataset.leaveId));
            });
        }

        function openLeaveDetailModal(id) {
            document.getElementById('leaveDetailModalBody').innerHTML =
                `<div class="text-center text-muted py-3">Loading...</div>`;
            $('#leaveDetailModal').modal('show');

            fetch(`${API_BASE}/leave-applications/${id}`)
                .then(r => r.json())
                .then(l => renderLeaveDetail(l))
                .catch(() => {
                    document.getElementById('leaveDetailModalBody').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load details</p></div>`;
                });
        }

        function renderLeaveDetail(l) {
            const body = document.getElementById('leaveDetailModalBody');

            // Non-admin -- shudhu read-only view
            if (!l.is_admin) {
                body.innerHTML = `
                    <table class="table table-sm">
                        <tr><th>Employee</th><td>${l.employee_name}</td></tr>
                        <tr><th>From</th><td>${fmtDate(l.apply_date)}</td></tr>
                        <tr><th>To</th><td>${fmtDate(l.end_date)}</td></tr>
                        <tr><th>Reason</th><td>${l.reason ?? '-'}</td></tr>
                        <tr><th>Payment</th><td>${l.payment_status ?? '-'}</td></tr>
                        <tr><th>Status</th><td><span class="badge badge-info">${l.status}</span></td></tr>
                        ${l.file ? `<tr><th>Attachment</th><td><a href="${l.file}" target="_blank">View File</a></td></tr>` : ''}
                    </table>`;
                return;
            }

            // Admin -- edit + approve form
            body.innerHTML = `
                <div id="leaveEditError" class="alert alert-danger d-none"></div>
                <form id="leaveEditForm">
                    <div class="form-group">
                        <label>Employee</label>
                        <input type="text" class="form-control" value="${l.employee_name}" disabled>
                    </div>
                    <div class="form-row">
                        <div class="col form-group">
                            <label>From</label>
                            <input type="date" name="apply_date" class="form-control" value="${l.apply_date}" required>
                        </div>
                        <div class="col form-group">
                            <label>To</label>
                            <input type="date" name="end_date" class="form-control" value="${l.end_date}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <textarea name="reason" class="form-control" rows="2">${l.reason ?? ''}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Payment Status</label>
                        <select name="payment_status" class="form-control">
                            <option value="paid" ${l.payment_status === 'paid' ? 'selected' : ''}>Paid</option>
                            <option value="non-paid" ${l.payment_status === 'non-paid' ? 'selected' : ''}>Non-paid</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="pending" ${l.status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="approved" ${l.status === 'approved' ? 'selected' : ''}>Approved</option>
                            <option value="cancel" ${l.status === 'cancel' ? 'selected' : ''}>Cancel</option>
                        </select>
                    </div>
                    ${l.file ? `<a href="${l.file}" target="_blank">View Attached File</a>` : ''}
                    <div class="text-right mt-3">
                        <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
                    </div>
                </form>`;

            document.getElementById('leaveEditForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const payload = Object.fromEntries(new FormData(this).entries());

                fetch(`${API_BASE}/leave-applications/${l.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(async r => {
                        if (!r.ok) throw await r.json();
                        return r.json();
                    })
                    .then(() => {
                        $('#leaveDetailModal').modal('hide');
                        loadRecentLeaveApplications();
                    })
                    .catch(err => {
                        const box = document.getElementById('leaveEditError');
                        box.textContent = err.message || 'Failed to update';
                        box.classList.remove('d-none');
                    });
            });
        }

        /* ---------------- Point 3/8: Announcements -- real DB theke, create + detail/update modal ---------------- */
        function loadAnnouncements() {
            fetch(`${API_BASE}/announcements`)
                .then(r => r.json())
                .then(data => {
                    const annBox = document.getElementById('announcements');
                    annBox.innerHTML = data.length ? data.map((a, i) => `
  <div class="person-row align-items-start ann-clickable" style="cursor:pointer" data-ann-id="${a.id}">
    <div class="d-flex align-items-start gap-2">
      <div class="ann-icon" style="background:${annColors[i % 6]}"><i class="bi bi-file-earmark-text"></i></div>
      <div>
        <div class="person-name">${a.title} ${a.type === 'department' ? `<span class="badge badge-secondary">${a.department}</span>` : '<span class="badge badge-info">Public</span>'}</div>
        <div class="person-sub">${a.description}</div>
        <div class="person-sub">${fmtDate(a.created_at)}${a.is_expired ? ' <span class="text-danger">(Expired)</span>' : ''}</div>
      </div>
    </div>
  </div>`).join('') :
                        `<div class="empty-state"><i class="bi bi-file-earmark-text"></i><p>No active announcements</p></div>`;

                    bindAnnouncementClicks();
                })
                .catch(() => {
                    document.getElementById('announcements').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load announcements</p></div>`;
                });
        }
        loadAnnouncements();

        function bindAnnouncementClicks() {
            document.querySelectorAll('.ann-clickable').forEach(el => {
                el.addEventListener('click', () => openAnnouncementDetailModal(el.dataset.annId));
            });
        }

        function openAnnouncementDetailModal(id) {
            document.getElementById('announcementDetailModalBody').innerHTML =
                `<div class="text-center text-muted py-3">Loading...</div>`;
            $('#announcementDetailModal').modal('show');

            fetch(`${API_BASE}/announcements/${id}`)
                .then(r => r.json())
                .then(a => renderAnnouncementDetail(a))
                .catch(() => {
                    document.getElementById('announcementDetailModalBody').innerHTML =
                        `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load details</p></div>`;
                });
        }

        function renderAnnouncementDetail(a) {
            const body = document.getElementById('announcementDetailModalBody');

            if (!a.is_admin || a.is_expired) {
                body.innerHTML =
                    `
                    <h6>${a.title} ${a.is_expired ? '<span class="badge badge-danger">Expired</span>' : ''}</h6>
                    <p>${a.description}</p>
                    <table class="table table-sm">
                        <tr><th>Type</th><td>${a.type === 'public' ? 'Public' : 'Department: ' + a.department}</td></tr>
                        <tr><th>Posted</th><td>${fmtDate(a.created_at)}</td></tr>
                        <tr><th>Expires</th><td>${a.expire_date ? fmtDate(a.expire_date) : 'No expiry'}</td></tr>
                    </table>
                    ${a.is_expired && a.is_admin ? '<div class="alert alert-warning mb-0">This announcement has expired and can no longer be updated.</div>' : ''}`;
                return;
            }

            body.innerHTML = `
                <div id="announcementEditError" class="alert alert-danger d-none"></div>
                <form id="announcementEditForm">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" value="${a.title}" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4" required>${a.description}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" id="editAnnouncementType" class="form-control" required>
                            <option value="public" ${a.type === 'public' ? 'selected' : ''}>Public (Everyone)</option>
                            <option value="department" ${a.type === 'department' ? 'selected' : ''}>Department Only</option>
                        </select>
                    </div>
                    <div class="form-group ${a.type !== 'department' ? 'd-none' : ''}" id="editAnnouncementDeptWrap">
                        <label>Department</label>
                        <input type="text" name="department" class="form-control" value="${a.department ?? ''}">
                    </div>
                    <div class="form-group">
                        <label>Expire Date</label>
                        <input type="date" name="expire_date" class="form-control" value="${a.expire_date ?? ''}">
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
                    </div>
                </form>`;

            document.getElementById('editAnnouncementType').addEventListener('change', function() {
                document.getElementById('editAnnouncementDeptWrap').classList.toggle('d-none', this.value !==
                    'department');
            });

            document.getElementById('announcementEditForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const payload = Object.fromEntries(new FormData(this).entries());

                fetch(`${API_BASE}/announcements/${a.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(async r => {
                        if (!r.ok) throw await r.json();
                        return r.json();
                    })
                    .then(() => {
                        $('#announcementDetailModal').modal('hide');
                        loadAnnouncements();
                    })
                    .catch(err => {
                        const box = document.getElementById('announcementEditError');
                        box.textContent = err.message || 'Failed to update';
                        box.classList.remove('d-none');
                    });
            });
        }

        // Point 3: Announcement create modal -- shudhu Admin
        if (isAdmin) {
            const btnNew = document.getElementById('btnNewAnnouncement');
            if (btnNew) {
                btnNew.addEventListener('click', () => {
                    document.getElementById('announcementCreateForm').reset();
                    document.getElementById('announcementFormError').classList.add('d-none');
                    document.getElementById('announcementDepartmentWrap').classList.add('d-none');
                    $('#announcementCreateModal').modal('show');
                });
            }

            let deptOptionsLoaded = false;
            document.getElementById('announcementType')?.addEventListener('change', function() {
                document.getElementById('announcementDepartmentWrap').classList.toggle('d-none', this.value !==
                    'department');
                if (this.value === 'department' && !deptOptionsLoaded) {
                    fetch(`${API_BASE}/department-options`)
                        .then(r => r.json())
                        .then(depts => {
                            const select = document.getElementById('announcementDepartment');
                            select.innerHTML = '<option value="">Select Department</option>' +
                                depts.map(d => `<option value="${d}">${d}</option>`).join('');
                            deptOptionsLoaded = true;
                        });
                }
            });

            document.getElementById('announcementCreateForm')?.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const payload = Object.fromEntries(formData.entries());

                fetch(`${API_BASE}/announcements`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(async r => {
                        if (!r.ok) throw await r.json();
                        return r.json();
                    })
                    .then(() => {
                        $('#announcementCreateModal').modal('hide');
                        loadAnnouncements();
                    })
                    .catch(err => {
                        const errBox = document.getElementById('announcementFormError');
                        errBox.textContent = err.message || 'Something went wrong. Please check the form.';
                        errBox.classList.remove('d-none');
                    });
            });
        }
    </script>
@endsection
