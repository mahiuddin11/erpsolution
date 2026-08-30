<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\LeaveApplication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HrmDashboardApiController extends Controller
{
    // ---------------------------------------------------------------
    // Point 1: KPI Cards summary — Admin only
    // ---------------------------------------------------------------
    public function kpis()
    {
        $user    = Auth::user();
        $isAdmin = $user->type == 'Admin';

        if (!$isAdmin) {
            return $this->employeeKpis($user);
        }


        $today      = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        $totalEmployee = Employee::where('employee_status', 'present')
            ->count();

        $todayPresent = Attendance::whereDate('date', $today)
            ->distinct('emplyee_id')
            ->count('emplyee_id');

        $todayLeave = LeaveApplication::where('status', 'approved')
            ->whereDate('apply_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        $todayAbsent = max($totalEmployee - ($todayPresent + $todayLeave), 0);

        $newEmployeeThisMonth = Employee::where('status', 'Active')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        $leftEmployeeThisMonth = Employee::where('employee_status', 'left')
            ->whereBetween('updated_at', [$monthStart, $monthEnd])
            ->count();

        $totalBranch = Branch::count();

        $totalDepartment = Employee::where('status', 'Active')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct('department')
            ->count('department');

        return response()->json([
            'visible'                  => true,
            'is_admin'                 => true,
            'total_employees'          => $totalEmployee,
            'present_today'            => $todayPresent,
            'absent_today'             => $todayAbsent,
            'on_leave'                 => $todayLeave,
            'new_employee_this_month'  => $newEmployeeThisMonth,
            'left_employee_this_month' => $leftEmployeeThisMonth,
            'total_branches'           => $totalBranch,
            'total_departments'        => $totalDepartment,
        ]);
    }


    // ---------------------------------------------------------------
    // Non-admin (Employee) KPI: This Month Attendance, Absent, Late, Leave
    // Keno alada method: Admin-er KPI logic theke shompurno bhinno calculation,
    // tai porishkar rakhar jonno alada method e likha holo
    // ---------------------------------------------------------------
    private function employeeKpis($user)
    {
        $employeeId = $user->employee_id;
        $monthStart = Carbon::now()->startOfMonth();
        $today      = Carbon::today(); // future date bad dewar jonno "end" ekhane e capped

        if (!$employeeId) {
            return response()->json(['visible' => false]);
        }

        // Ei mash-e present thaka date gulo (distinct date set)
        $presentDatesSet = Attendance::where('emplyee_id', $employeeId)
            ->whereBetween('date', [$monthStart, $today])
            ->pluck('date')
            ->map(fn($d) => $d instanceof Carbon ? $d->format('Y-m-d') : (string) $d)
            ->flip();

        $presentCount = $presentDatesSet->count();

        // Late count: employee-er nijer 'last_in_time' column ke expected office
        // time dhore, 15 min grace diye sign_in tar por hole "late" gonno
        $lateCount = Attendance::where('emplyee_id', $employeeId)
            ->whereBetween('date', [$monthStart, $today])
            ->get()
            ->filter(function ($a) use ($user) {
                $employee = Employee::find($user->employee_id);
                if (empty($a->sign_in) || empty($employee->last_in_time)) {
                    return false;
                }
                $officeTime = Carbon::parse($employee->last_in_time)->addMinutes(15);
                $checkIn    = Carbon::parse($a->sign_in);
                return $checkIn->gt($officeTime);
            })->count();

        // Ei mash-er shob approved leave date -- future leave dinও dhora hobe,
        // karon approved leave already jana/planned, "future absence" na
        $monthEnd = Carbon::now()->endOfMonth();
        $leaveDaysThisMonth = 0;
        $leaveDatesSetUptoToday = collect(); // shudhu ajke porjonto -- absent calculation-e lagbe

        $leaves = LeaveApplication::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where('apply_date', '<=', $monthEnd)
            ->where('end_date', '>=', $monthStart)
            ->get();

        foreach ($leaves as $l) {
            $start = Carbon::parse($l->apply_date)->max($monthStart);
            $end   = Carbon::parse($l->end_date)->min($monthEnd);
            $leaveDaysThisMonth += $start->diffInDays($end) + 1;

            // Absent calculation-er jonno shudhu ajke porjonto leave date gulo
            $cursor = $start->copy();
            $cappedEnd = $end->min($today);
            while ($cursor <= $cappedEnd) {
                $leaveDatesSetUptoToday->put($cursor->format('Y-m-d'), true);
                $cursor->addDay();
            }
        }

        // Holiday set (manually added holidays), ajke porjonto
        $holidaySet = Holiday::whereBetween('date', [$monthStart, $today])
            ->pluck('date')
            ->map(fn($d) => $d instanceof Carbon ? $d->format('Y-m-d') : (string) $d)
            ->flip();

        // Absent calculation: month-start theke aj porjonto protita dine loop kore
        // dekha hocche -- Friday, holiday, leave, ba present thakle "absent" na
        $absentCount = 0;
        $cursor = $monthStart->copy();
        while ($cursor <= $today) {
            $dateStr = $cursor->format('Y-m-d');

            $isFriday   = $cursor->isFriday();
            $isHoliday  = $holidaySet->has($dateStr);
            $isPresent  = $presentDatesSet->has($dateStr);
            $isOnLeave  = $leaveDatesSetUptoToday->has($dateStr);

            if (!$isFriday && !$isHoliday && !$isPresent && !$isOnLeave) {
                $absentCount++;
            }
            $cursor->addDay();
        }

        return response()->json([
            'visible'                => true,
            'is_admin'                => false,
            'this_month_attendance'  => $presentCount,
            'absent_this_month'      => $absentCount,
            'late_this_month'        => $lateCount,
            'leave_this_month'       => $leaveDaysThisMonth,
        ]);
    }

    // ---------------------------------------------------------------
    // Point 1 (NEW): KPI click korle modal e drill-down list
    // ?type= total_employees | present_today | absent_today | on_leave |
    //        new_employee_this_month | left_employee_this_month |
    //        total_branches | total_departments
    // Keno generic {title, subtitle} shape: Blade e ekta shared table
    // renderer diye shob type render kora jabe, alada UI lagbe na
    // ---------------------------------------------------------------
    public function kpiDetails(Request $request)
    {
        $type       = $request->input('type');
        $today      = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        $rows = [];

        switch ($type) {
            case 'total_employees':
                $rows = Employee::where('status', 'Active')
                    ->where('employee_status', 'present')
                    ->orderBy('name')->take(50)->get()
                    ->map(fn($e) => ['title' => $e->name, 'subtitle' => $e->department ?? 'N/A']);
                break;

            // case 'present_today':
            //     $rows = Attendance::with('employe')
            //         ->whereDate('date', $today)
            //         ->take(50)->get()
            //         ->map(fn($a) => ['title' => $a->employe->name ?? 'N/A', 'subtitle' => 'In: ' . ($a->sign_in ?? '-')]);
            //     break;

            case 'present_today':
                $rows = Attendance::with('employe')
                    ->whereDate('date', $today)
                    ->take(50)->get()
                    ->map(function ($a) {
                        $hasOut = !empty($a->sign_out) && $a->sign_out != '00:00:00';
                        return [
                            'title'    => $a->employe->name ?? 'N/A',
                            'sign_in'  => $a->sign_in,
                            'sign_out' => $hasOut ? $a->sign_out : null,
                            'in_lat'   => $a->latitude,
                            'in_lng'   => $a->longitude,
                            'out_lat'  => $hasOut ? $a->latitude_out : null,
                            'out_lng'  => $hasOut ? $a->longitude_out : null,
                        ];
                    });
                break;

            case 'on_leave':
                $rows = LeaveApplication::with('employee')
                    ->where('status', 'approved')
                    ->whereDate('apply_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today)
                    ->take(50)->get()
                    ->map(fn($l) => ['title' => $l->employee->name ?? 'N/A', 'subtitle' => 'Return: ' . $l->end_date]);
                break;

            case 'absent_today':
                // Active employee list theke present + on-leave baad diye absent বের করা
                $presentIds = Attendance::whereDate('date', $today)->pluck('emplyee_id')->unique();
                $onLeaveIds = LeaveApplication::where('status', 'approved')
                    ->whereDate('apply_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today)
                    ->pluck('employee_id')->unique();

                $rows = Employee::where('status', 'Active')
                    ->where('employee_status', 'present')
                    ->whereNotIn('id', $presentIds)
                    ->whereNotIn('id', $onLeaveIds)
                    ->take(50)->get()
                    ->map(fn($e) => ['title' => $e->name, 'subtitle' => $e->department ?? 'N/A']);
                break;

            case 'new_employee_this_month':
                $rows = Employee::where('status', 'Active')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->orderByDesc('created_at')->take(50)->get()
                    ->map(fn($e) => ['title' => $e->name, 'subtitle' => 'Joined: ' . $e->created_at->format('d-M-Y')]);
                break;

            case 'left_employee_this_month':
                $rows = Employee::where('employee_status', 'left')
                    ->whereBetween('updated_at', [$monthStart, $monthEnd])
                    ->orderByDesc('updated_at')->take(50)->get()
                    ->map(fn($e) => ['title' => $e->name, 'subtitle' => 'Left: ' . $e->updated_at->format('d-M-Y')]);
                break;

            case 'total_branches':
                $rows = Branch::take(50)->get()
                    ->map(fn($b) => ['title' => $b->name ?? 'N/A', 'subtitle' => $b->branchCode ?? '']);
                break;

            case 'total_departments':
                $rows = Employee::select('department', DB::raw('count(*) as total'))
                    ->where('status', 'Active')
                    ->whereNotNull('department')->where('department', '!=', '')
                    ->groupBy('department')->orderByDesc('total')->take(50)->get()
                    ->map(fn($d) => ['title' => $d->department, 'subtitle' => $d->total . ' employees']);
                break;

            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }

        return response()->json($rows);
    }

    // ---------------------------------------------------------------
    // Point 2: Department Distribution -- ekhon Position (position_id) onujayi
    // group kora hoy. Employee::position() relation (belongsTo Position) use
    // kore prottek position-e koto employee ache ar ajke koto jon present ta
    // compare kore dekhano hocche.
    // ---------------------------------------------------------------

    public function departmentDistribution()
    {
        $today = Carbon::today();

        $presentEmployeeIds = Attendance::whereDate('date', $today)
            ->pluck('emplyee_id')->unique();

        $positions = Employee::select('position_id', DB::raw('count(*) as total'))
            ->where('status', 'Active')
            ->where('employee_status', 'present')
            ->whereNotNull('position_id')
            ->groupBy('position_id')
            ->orderByDesc('total')
            ->with('position')
            ->get();

        $data = $positions->map(function ($pos) use ($presentEmployeeIds) {
            $presentCount = Employee::where('position_id', $pos->position_id)
                ->where('status', 'Active')
                ->where('employee_status', 'present')
                ->whereIn('id', $presentEmployeeIds)
                ->count();

            $percent = $pos->total > 0 ? round(($presentCount / $pos->total) * 100, 1) : 0;

            return [
                'position_id'     => $pos->position_id,
                'name'            => optional($pos->position)->name ?? 'Unassigned',
                'total'           => $pos->total,
                'present'         => $presentCount,
                'present_percent' => $percent,
            ];
        });

        return response()->json($data);
    }

    public function positionDetails(Request $request)
    {
        $positionId = $request->input('position_id');
        $today      = Carbon::today();

        $presentIds = Attendance::whereDate('date', $today)
            ->pluck('emplyee_id')->unique();

        $rows = Employee::where('position_id', $positionId)
            ->where('status', 'Active')
            ->where('employee_status', 'present')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(function ($e) use ($presentIds) {
                $isPresent = $presentIds->contains($e->id);
                return [
                    'title'    => $e->name,
                    'subtitle' => $isPresent ? 'Present today' : 'Absent today',
                    'status'   => $isPresent ? 'present' : 'absent',
                ];
            });

        return response()->json($rows);
    }
    // ---------------------------------------------------------------
    // Point 3: Quick Actions — role permission onujayi filtered button list
    // ---------------------------------------------------------------
    public function quickActions()
    {
        $actions = [
            ['key' => 'add_employee',     'label' => 'Add New Employee',    'icon' => 'bi-person-plus',       'route' => 'hrm.employee.create'],
            ['key' => 'mark_attendance',  'label' => 'Mark Attendance',     'icon' => 'bi-clock-history',     'route' => 'hrm.attendance.create'],
            ['key' => 'apply_leave',      'label' => 'Apply for Leave',     'icon' => 'bi-calendar-plus',     'route' => 'hrm.leave.create'],
            ['key' => 'process_payroll',  'label' => 'Process Payroll',     'icon' => 'bi-credit-card',       'route' => 'hrm.payroll.index'],
            ['key' => 'create_promotion', 'label' => 'Create Promotion',    'icon' => 'bi-graph-up-arrow',    'route' => 'hrm.promotion.create'],
            ['key' => 'create_resign',    'label' => 'Create Resignation',  'icon' => 'bi-graph-down-arrow',  'route' => 'hrm.resignation.create'],
            ['key' => 'create_holiday',   'label' => 'Create Holiday',      'icon' => 'bi-calendar2-week',    'route' => 'hrm.holiday.index'],
            ['key' => 'create_warning',   'label' => 'Create Warning',      'icon' => 'bi-exclamation-triangle', 'route' => 'hrm.warning.create'],
        ];

        $allowed = collect($actions)->filter(function ($action) {
            return \Route::has($action['route']) && Helper::roleAccess($action['route']);
        })->map(function ($action) {
            $action['url'] = route($action['route']);
            return $action;
        })->values();

        return response()->json($allowed);
    }

    // ---------------------------------------------------------------
    // Point 4: Employees on Leave (unchanged)
    // ---------------------------------------------------------------
    public function employeesOnLeave()
    {
        $user    = Auth::user();
        $isAdmin = $user->type == 'Admin';
        $today   = Carbon::today();

        $query = LeaveApplication::with('employee')
            ->where('status', 'approved')
            ->whereDate('apply_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);

        if (!$isAdmin) {
            $query->where('employee_id', $user->employee_id);
        }

        $data = $query->latest('apply_date')->take(10)->get()->map(fn($l) => [
            'name'       => $l->employee->name ?? 'N/A',
            'leave_type' => $l->reason ?? 'Leave',
            'days'       => Carbon::parse($l->apply_date)->diffInDays(Carbon::parse($l->end_date)) + 1,
        ]);

        return response()->json($data);
    }


    public function attendanceList(Request $request)
    {
        $user    = Auth::user();
        $isAdmin = $user->type == 'Admin';
        $today   = Carbon::today();

        $query = Attendance::with('employe');

        if ($isAdmin) {

            $query->whereDate('date', $today);
            if ($request->filled('employee_id')) {
                $query->where('emplyee_id', $request->employee_id);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('employe', fn($q) => $q->where('name', 'like', "%{$search}%"));
            }
        } else {

            $last30Start = Carbon::now()->subDays(30)->startOfDay();

            $query->where('emplyee_id', $user->employee_id)
                ->whereBetween('date', [$last30Start, $today]);
        }

        // $data = $query->latest('date')->get()->map(fn($a) => [
        //     'employee_name' => $a->employe->name ?? 'N/A',
        //     'date'          => $a->date instanceof Carbon ? $a->date->format('Y-m-d') : $a->date,
        //     'sign_in'       => $a->sign_in,
        //     'sign_out'      => $a->sign_out,
        // ]);

        $data = $query->latest('date')->take(50)->get()->map(fn($a) => [
            'employee_name' => $a->employe->name ?? 'N/A',
            'date'          => $a->date instanceof Carbon ? $a->date->format('Y-m-d') : $a->date,
            'sign_in'       => $a->sign_in,
            'sign_out'      => $a->sign_out,
            'in_lat'        => $a->latitude,
            'in_lng'        => $a->longitude,
            'out_lat'       => $a->latitude_out,
            'out_lng'       => $a->longitude_out,
            'has_out'       => !empty($a->sign_out) && $a->sign_out != '00:00:00',
        ]);


        return response()->json($data);
    }

    public function employeeOptions()
    {
        $employees = Employee::where('status', 'Active')
            ->select('id', 'name')->orderBy('name')->get();

        return response()->json($employees);
    }

    // ---------------------------------------------------------------
    // Point 6: Calendar — Holiday table + auto Friday detect (unchanged)
    // ---------------------------------------------------------------
    public function calendarEvents(Request $request)
    {
        $calYear  = (int) $request->input('year', now()->year);
        $calMonth = (int) $request->input('month', now()->month);

        $holidayEvents = Holiday::whereYear('date', $calYear)
            ->whereMonth('date', $calMonth)
            ->get()
            ->map(fn($h) => [
                'id'        => 'h-' . $h->id,
                'title'     => $h->title,
                'note'      => $h->note, // NEW -- calendar cell e note thakle seta prioritize kore dekhabe
                'startDate' => $h->date instanceof Carbon ? $h->date->format('Y-m-d') : $h->date,
                'type'      => 'holiday',
                'color'     => '#ef4444',
            ]);

        $fridayEvents = collect();
        $cursor = Carbon::create($calYear, $calMonth, 1);
        while ($cursor->month == $calMonth) {
            if ($cursor->isFriday()) {
                $fridayEvents->push([
                    'id'        => 'fri-' . $cursor->format('Y-m-d'),
                    'title'     => 'Weekend (Friday)',
                    'note'      => null, // NEW -- Friday e kono note thake na
                    'startDate' => $cursor->format('Y-m-d'),
                    'type'      => 'holiday',
                    'color'     => '#f59e0b',
                ]);
            }
            $cursor->addDay();
        }

        return response()->json($holidayEvents->concat($fridayEvents)->values());
    }

    // ---------------------------------------------------------------
    // Point 7: Recent Leave Applications (unchanged in this phase —
    // approve/edit modal Phase 3 e asbe)
    // ---------------------------------------------------------------
    public function recentLeaveApplications()
    {
        $user       = Auth::user();
        $isAdmin    = $user->type == 'Admin';
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        $query = LeaveApplication::with('employee')
            ->whereBetween('apply_date', [$monthStart, $monthEnd]);

        if (!$isAdmin) {
            $query->where('employee_id', $user->employee_id);
        }

        $data = $query->latest('apply_date')->take(10)->get()->map(fn($l) => [
            'id'            => $l->id,
            'employee_name' => $l->employee->name ?? 'N/A',
            'leave_type'    => $l->reason ?? 'Leave',
            'start_date'    => $l->apply_date,
            'end_date'      => $l->end_date,
            'total_days'    => Carbon::parse($l->apply_date)->diffInDays(Carbon::parse($l->end_date)) + 1,
            'status'        => ucfirst($l->status),
        ]);

        return response()->json($data);
    }

    // ---------------------------------------------------------------
    // Point 3 (Announcements list) + Point 8 (details/update Phase 3 e)
    // Ekhon real DB theke — public shobai dekhbe, department type hole
    // shudhu oi department-er employee-ra dekhbe
    // ---------------------------------------------------------------
    public function announcements()
    {
        $user = Auth::user();

        $query = Announcement::query()->latest();

        if ($user->type != 'Admin') {
            $userDepartment = optional(Employee::find($user->employee_id))->department;

            $query->where(function ($q) use ($userDepartment) {
                $q->where('type', 'public')
                    ->orWhere(function ($q2) use ($userDepartment) {
                        $q2->where('type', 'department')->where('department', $userDepartment);
                    });
            });
        }

        $data = $query->take(10)->get()->map(fn($a) => [
            'id'          => $a->id,
            'title'       => $a->title,
            'description' => $a->description,
            'type'        => $a->type,
            'department'  => $a->department,
            'expire_date' => $a->expire_date,
            'is_expired'  => $a->isExpired(),
            'created_at'  => $a->created_at->format('Y-m-d'),
        ]);

        return response()->json($data);
    }

    // Announcement create — shudhu Admin
    public function storeAnnouncement(Request $request)
    {
        $user = Auth::user();
        if ($user->type != 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'type'        => 'required|in:public,department',
            'department'  => 'required_if:type,department|nullable|string',
            'expire_date' => 'nullable|date',
        ]);

        $validated['created_by'] = $user->id;

        Announcement::create($validated);

        return response()->json(['message' => 'Announcement created successfully']);
    }

    // Announcement create form-er department dropdown-er jonno
    public function departmentOptions()
    {
        $departments = Employee::whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()->pluck('department');

        return response()->json($departments);
    }

    // ---------------------------------------------------------------
    // Point 7: Leave Application details (modal) + Admin approve/edit
    // ---------------------------------------------------------------
    public function leaveApplicationDetail($id)
    {
        $leave   = LeaveApplication::with('employee')->findOrFail($id);
        $user    = Auth::user();
        $isAdmin = $user->type == 'Admin';

        // Non-admin shudhu nijer leave dekhte parbe
        if (!$isAdmin && $leave->employee_id != $user->employee_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id'             => $leave->id,
            'employee_name'  => $leave->employee->name ?? 'N/A',
            'apply_date'     => $leave->apply_date,
            'end_date'       => $leave->end_date,
            'reason'         => $leave->reason,
            'file'           => $leave->file ? asset('storage/' . $leave->file) : null,
            'payment_status' => $leave->payment_status,
            'status'         => $leave->status,
            'is_admin'       => $isAdmin,
        ]);
    }

    // Point 7: Admin approve/update -- non-admin ei route e access pabe na
    public function updateLeaveApplication(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->type != 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $leave = LeaveApplication::findOrFail($id);

        $validated = $request->validate([
            'apply_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:apply_date',
            'reason'         => 'nullable|string',
            'payment_status' => 'nullable|in:paid,non-paid',
            // Note: DB enum e 'rejected' nei, ache 'approved','pending','cancel'
            'status'         => 'required|in:approved,pending,cancel',
        ]);

        $leave->update($validated);

        return response()->json(['message' => 'Leave application updated successfully']);
    }

    // ---------------------------------------------------------------
    // Point 8: Announcement details (modal) + Admin update (expire hole lock)
    // ---------------------------------------------------------------
    public function announcementDetail($id)
    {
        $announcement = Announcement::findOrFail($id);
        $user         = Auth::user();

        if ($user->type != 'Admin') {
            $userDepartment = optional(Employee::find($user->employee_id))->department;
            if ($announcement->type == 'department' && $announcement->department != $userDepartment) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        return response()->json([
            'id'          => $announcement->id,
            'title'       => $announcement->title,
            'description' => $announcement->description,
            'type'        => $announcement->type,
            'department'  => $announcement->department,
            'expire_date' => $announcement->expire_date,
            'is_expired'  => $announcement->isExpired(),
            'created_at'  => $announcement->created_at->format('Y-m-d'),
            'is_admin'    => $user->type == 'Admin',
        ]);
    }

    // Point 8: Admin update -- expire hoye gele kaji korbe na
    public function updateAnnouncement(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->type != 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $announcement = Announcement::findOrFail($id);

        if ($announcement->isExpired()) {
            return response()->json(['message' => 'This announcement has expired and cannot be updated'], 422);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'type'        => 'required|in:public,department',
            'department'  => 'required_if:type,department|nullable|string',
            'expire_date' => 'nullable|date',
        ]);

        $announcement->update($validated);

        return response()->json(['message' => 'Announcement updated successfully']);
    }
}
