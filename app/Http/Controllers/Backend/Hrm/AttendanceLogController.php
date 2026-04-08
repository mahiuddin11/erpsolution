<?php

namespace App\Http\Controllers\Backend\Hrm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\LeaveApplication;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Rats\Zkteco\Lib\ZKTeco;

class AttendanceLogController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function index(Request $request)
    {
        $title = 'Attendance Log';
        $employees = Employee::all();
        if ($request->method() == "POST") {
            $attendances = Attendance::selectRaw('DATE(date) date,emplyee_id,sign_in,sign_out')->with('employe');

            if ($request->employee_id != 'all') {
                $attendances =  $attendances->where('emplyee_id', $request->employee_id);
            }

            if ($request->from && $request->to) {
                $attendances =  $attendances->where('date', '>=', $request->from);
                $attendances =  $attendances->where('date', '<=', $request->to);
            }

            $attendances = $attendances->get();

            $dayes = Attendance::selectRaw('DATE(date) date');
            if ($request->from && $request->to) {
                $dayes =  $dayes->where('date', '>=', $request->from);
                $dayes =  $dayes->where('date', '<=', $request->to);
            }
            $dayes = $dayes->groupBy('date')->get();
        }
        return view('backend.pages.hrm.attendance.attendance-log.index', get_defined_vars());
    }

    public function absent(Request $request)
    {
        $title = 'Attendance Absent';

        $attendances = Attendance::whereDate('date', date("Y-m-d"))->pluck("emplyee_id")->toArray();
        $employees = Employee::whereNotIn("id", $attendances)->where("employee_status", "present")->get();
        return view('backend.pages.hrm.attendance.attendance-log.absent', compact("employees", "title"));
    }

    public function newemployee(Request $request)
    {
        $title = 'New  Employee';

        $employees = Employee::whereMonth("created_at", date("m"))->whereYear("created_at", date("Y"))->get();

        return view('backend.pages.hrm.attendance.attendance-log.newemployee', compact("employees", "title"));
    }


    /* ══════════════════════════════════════════════════════════
     | JSON API — attendance log
     |
     | GET params:
     |   start_date  (Y-m-d)  default: today
     |   end_date    (Y-m-d)  default: today
     |
     | Status rules:
     |   Holiday → date is in $holidays array
     |   Leave   → employee has an approved leave on that date
     |             (requires a Leave model; see note below)
     |   Absent  → no attendance row (and not Holiday/Leave), not Friday
     |   Late    → check-in > office_start + 15 min
     |   Present → check-in exists and not Late
     |
     | Friday and $holidays are always SKIPPED (not in result at all).
     | Holidays still appear but with status = 'Holiday'.
     ══════════════════════════════════════════════════════════ */
   

    public function attandanceLog(Request $request)
    {

        $month = $request->month ?? now()->month;
        $year  = $request->year ?? now()->year;

        /* ── Date range ── */
        $start = $request->start_date ?? Carbon::create($year, $month, 1)->toDateString();
        $end   = $request->end_date   ?? Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        if ($end < $start) {
            $end = $start;
        }

        /* ── Custom Holidays ── */
        $holidays = Holiday::getHolidaySet($start, $end);

        $holidaySet = collect($holidays)
            ->mapWithKeys(fn($v, $k) => [Carbon::parse($k)->toDateString() => true])
            ->toArray();


        /* ── Approved Leaves ── */
        $approvedLeaves = [];

        $leaveApps = LeaveApplication::where('status', 'approved')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('apply_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end]);
            })
            ->get();

        foreach ($leaveApps as $leave) {

            $period = CarbonPeriod::create($leave->apply_date, $leave->end_date);

            foreach ($period as $d) {
                $approvedLeaves[$leave->employee_id . '_' . $d->toDateString()] = true;
            }
        }

        /* ── Employees ── */
        $employees = Employee::select('id', 'name', 'email', 'department', 'last_in_time')
            ->where('employee_status', 'present')
            ->get();

        /* ── Attendance rows ── */
        $attendances = Attendance::whereBetween('date', [$start, $end])
            ->get()
            ->groupBy(fn($item) => $item->emplyee_id . '_' . $item->date);

       
    

        $period = CarbonPeriod::create($start, $end);

        $result = [];

        $avatarColors = [
            '#3b6fff',
            '#10b981',
            '#f59e0b',
            '#8b5cf6',
            '#06b6d4',
            '#ec4899',
            '#14b8a6',
            '#ef4444',
        ];

        foreach ($employees as $emp) {

            $color = $avatarColors[$emp->id % count($avatarColors)];

            $initials = strtoupper(
                collect(explode(' ', trim($emp->name)))
                    ->filter()
                    ->map(fn($p) => $p[0])
                    ->take(2)
                    ->implode('')
            );

            $officeStartRaw = $emp->last_in_time ?? '09:00:00';
            $officeStart = Carbon::parse($officeStartRaw);
            $lateLimit = $officeStart->copy()->addMinutes(15);

            foreach ($period as $date) {

                $dateStr = $date->toDateString();

                $key = $emp->id . '_' . $dateStr;

                $isFriday = $date->isFriday();
                $isHoliday = isset($holidaySet[$dateStr]);
                $isLeave = isset($approvedLeaves[$key]);

                $row = $attendances[$key][0] ?? null;

                /* ── Friday = Holiday ── */
                if ($isFriday) {
                    $result[] = $this->buildRow($emp, $dateStr, null, 'Holiday', $color, $initials, $officeStart);
                    continue;
                }

                /* ── Custom Holiday ── */
                if ($isHoliday) {
                    $result[] = $this->buildRow($emp, $dateStr, null, 'Holiday', $color, $initials, $officeStart);
                    continue;
                }

                /* ── Leave ── */
                if ($isLeave) {
                    $result[] = $this->buildRow($emp, $dateStr, null, 'Leave', $color, $initials, $officeStart);
                    continue;
                }

                /* ── Absent ── */
                if (!$row) {
                    $result[] = $this->buildRow($emp, $dateStr, null, 'Absent', $color, $initials, $officeStart);
                    continue;
                }

                /* ── Present / Late ── */
                $checkInTime  = $row->sign_in ? Carbon::parse($row->sign_in) : null;
                $checkOutTime = $row->sign_out ? Carbon::parse($row->sign_out) : null;

                $status = 'Present';

                if ($checkInTime && $checkInTime->gt($lateLimit)) {
                    $status = 'Late';
                }

                $hours = '0h 0m';
                $overtime = '0h 0m';

                if ($checkInTime && $checkOutTime && $checkOutTime->gt($checkInTime)) {

                    $diffMin = $checkInTime->diffInMinutes($checkOutTime);
                    $hours = floor($diffMin / 60) . 'h ' . ($diffMin % 60) . 'm';

                    if ($diffMin > 480) {
                        $ot = $diffMin - 480;
                        $overtime = floor($ot / 60) . 'h ' . ($ot % 60) . 'm';
                    }
                }

                $result[] = [
                    'id'       => $row->id,
                    'empId'    => 'EMP' . $emp->id,
                    'name'     => $emp->name,
                    'email'    => $emp->email,
                    'dept'     => $emp->department,
                    'offictime' => $officeStart->format('h:i A'),
                    'date'     => $dateStr,
                    'checkIn'  => $checkInTime ? $checkInTime->format('h:i A') : '—',
                    'checkOut' => $checkOutTime ? $checkOutTime->format('h:i A') : '—',
                    'hours'    => $hours,
                    'overtime' => $overtime,
                    'status'   => $status,
                    'color'    => $color,
                    'initials' => $initials,
                ];
            }
        }

        usort($result, fn($a, $b) => strcmp($b['date'], $a['date']));

        $totalEmploye = $employees->count();
        $presentEmploye =  $attendances->count();

        return response()->json([
            'status' => true,
            'massage' => 'dataload succes',
            'result' =>  $result,
            'totalemployee' =>   $totalEmploye,
            'presentEmploye' =>  $presentEmploye
        ]);
    }

    /* ══════════════════════════════════════════════════════════
     | Helper: build a simple row (Holiday / Leave / Absent)
     ══════════════════════════════════════════════════════════ */
    private function buildRow(Employee $emp, string $dateStr, $row, string $status, string $color, string $initials, Carbon $officeStart): array
    {
        return [
            'id'       => $row ? $row->id : null,
            'empId'    => 'EMP' . $emp->id,
            'name'     => $emp->name,
            'email'    => $emp->email,
            'dept'     => $emp->department,
            'offictime' => $officeStart->format('h:i A'),
            'date'     => $dateStr,
            'checkIn'  => '—',
            'checkOut' => '—',
            'hours'    => '0h 0m',
            'overtime' => '0h 0m',
            'status'   => $status,
            'color'    => $status === 'Absent'  ? '#ef4444'
                : ($status === 'Holiday' ? '#7c3aed'
                    : ($status === 'Leave'   ? '#ea580c' : $color)),
            'initials' => $initials,
        ];
    }

    
}
