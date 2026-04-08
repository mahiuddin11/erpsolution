<?php

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

function CUSTOM_ALLOWANCE_AMOUNT()
{
    $MEDICAL_ALLOWANCE = 600;
    $TRAVEL_ALLOWANCE = 350;
    $FOOD_ALLOWANCE = 900;
    return  $MEDICAL_ALLOWANCE + $TRAVEL_ALLOWANCE + $FOOD_ALLOWANCE;
}

// GET THIS MONTH WORKING DAYS
function CUSTOM_MONTH_WORKING_DAY()
{
    $THIS_MONTH = CarbonImmutable::now();
    $CURRENT_MONTH_HOLIDAY = CarbonPeriod::create($THIS_MONTH->startOfMonth(), $THIS_MONTH->endOfMonth())
        ->filter(static fn($date) => $date->is('Friday'))
        ->count();
    $TOTAL_DAT_OF_THIS_MONTH = Carbon::now()->daysInMonth;
    $TOTAL_WORKING_DAY = $TOTAL_DAT_OF_THIS_MONTH - $CURRENT_MONTH_HOLIDAY;
    return $TOTAL_WORKING_DAY;
}

//GET EMPLOYEE WORKING DAYS
function CUSTOM_EMPLOYEE_PRESENCE_DAY($EMPLOYEE_ID)
{
    $ATTENDANCE = DB::table('attendances')->where('emplyee_id', $EMPLOYEE_ID)->whereMonth('date', date('m'))->count();
    return $ATTENDANCE;
}

//GET EMPLOYEE LEAVE DAYS COUNT
function CUSTOM_EMPLOYEE_ABSENCE_DAY($EMPLOYEE_ID)
{
    $EMPLOYEE_WORKING_DAYS = CUSTOM_EMPLOYEE_PRESENCE_DAY($EMPLOYEE_ID);
    $LEAVE_COUNT = CUSTOM_MONTH_WORKING_DAY() - $EMPLOYEE_WORKING_DAYS;
    return $LEAVE_COUNT;
}

//GET EMPLOYEE MAIN SALARY
function CUSTOM_EMPLOYEE_BASIC_SALARY($EMPLOYEE_SALARY)
{
    $MAIN_SALARY = ($EMPLOYEE_SALARY - CUSTOM_ALLOWANCE_AMOUNT()) / 1.5;
    return round($MAIN_SALARY);
}

//GET EMPLOYEE HOUSE RENT MAIN SALARY
function CUSTOM_EMPLOYEE_HOUSE_RENT_SALARY($EMPLOYEE_SALARY)
{
    $HOUSE_RENT = CUSTOM_EMPLOYEE_BASIC_SALARY($EMPLOYEE_SALARY) / 2;
    return round($HOUSE_RENT);
}

function CUSTOM_OVERTIME_HOURE($EMPLOYEE, $month = null, $day = null, $loca)
{
    try {
        $ATTENDANCES = DB::table('attendances')->where('emplyee_id', $EMPLOYEE->id)->whereMonth('date', $month ?? date('m'));
        $ATTENDANCES = $ATTENDANCES->whereDay('date', $day);
        $ATTENDANCES = $ATTENDANCES->get();
        $HOURE = 0;
        foreach ($ATTENDANCES as $ATTENDANCE) {
            if (strtotime($ATTENDANCE->sign_in) < strtotime($EMPLOYEE->last_in_time)) {
                $in = Carbon::parse($EMPLOYEE->last_in_time);
                $lastin = Carbon::parse($ATTENDANCE->sign_out);
            } else {
                $in = Carbon::parse($ATTENDANCE->sign_in);
                $lastin = Carbon::parse($ATTENDANCE->sign_out);
            }
            $signOutTime = strtotime($lastin);
            $officeEndTime = strtotime($in . ' +8 hours');
            if ($signOutTime > $officeEndTime) {
                $TOTAL_TIME = $in->diff($lastin->subHour(8));
                $HOURE += $TOTAL_TIME->h;
                if ($TOTAL_TIME->i >= 50) {
                    $HOURE += 1;
                }
            }
        }
    } catch (\Throwable $th) {
        return 0;
    }
    return round($HOURE);
}

function CUSTOM_OVERTIME_SALARY($EMPLOYEE)
{
    if ($EMPLOYEE->over_time_is == "yes") {
        $ATTENDANCES = DB::table('attendances')->where('emplyee_id', $EMPLOYEE->id)->whereMonth('date', date('m'))->get();
        $EMPLOYEE_BASIC_SALARY = CUSTOM_EMPLOYEE_BASIC_SALARY($EMPLOYEE->salary);
        $ONE_DAY_SALARY = $EMPLOYEE_BASIC_SALARY / 26;
        $ONE_DAY_SALARY_DOUBLE = $ONE_DAY_SALARY * 2;
        $HOURE = 0;
        foreach ($ATTENDANCES as $ATTENDANCE) {
            if (strtotime($ATTENDANCE->sign_in) < strtotime($EMPLOYEE->last_in_time)) {
                $in = Carbon::parse($EMPLOYEE->last_in_time);
                $lastin = Carbon::parse($ATTENDANCE->sign_out);
            } else {
                $in = Carbon::parse($ATTENDANCE->sign_in);
                $lastin = Carbon::parse($ATTENDANCE->sign_out);
            }
            $TOTAL_TIME = $in->diff($lastin->subHour(8));
            $HOURE += $TOTAL_TIME->h;
            if ($TOTAL_TIME->i >= 50) {
                $HOURE += 1;
            }
        }
        $TOTAL_OVERTIME = $ONE_DAY_SALARY_DOUBLE * $HOURE;
    }

    return round($TOTAL_OVERTIME ?? 0);
}

//GET EMPLOYEE LATE DAYS
function CUSTOM_LATE_DAYS($EMPLOYEE, $month = null, $day = null)
{

    $EMPLOYEE_LAST_IN_TIME = Carbon::parse($EMPLOYEE->last_in_time ?? now())->addMinutes(15)->format("H:i:s");
    $LATE = DB::table('attendances')->where('emplyee_id', $EMPLOYEE->id)->whereMonth('date', $month ?? date('m'));
    if ($day) {
        $LATE = $LATE->whereDay('date', $day);
    }
    $LATE = $LATE->whereTime('sign_in', ">", $EMPLOYEE_LAST_IN_TIME)->count();
    return $LATE;
}

function CUSTOM_LATE_DAYS_SALARY_DEDUCT($EMPLOYEE)
{
    $EMPLOYEE_SALARY = $EMPLOYEE->salary;
    $DAYS = 26;
    $ONE_DAY_SALARY = $EMPLOYEE_SALARY / $DAYS;
    $TOTAL = 0;
    $LATE_COUNT = LATE_DAYS($EMPLOYEE);
    if ($LATE_COUNT > 3) {
        $TOTAL += $ONE_DAY_SALARY;
    }
    if ($LATE_COUNT > 6) {
        $TOTAL += $ONE_DAY_SALARY;
    }
    if ($LATE_COUNT > 9) {
        $TOTAL += $ONE_DAY_SALARY;
    }
    if ($LATE_COUNT > 12) {
        $TOTAL += $ONE_DAY_SALARY;
    }
    if ($LATE_COUNT > 15) {
        $TOTAL += $ONE_DAY_SALARY;
    }
    if ($LATE_COUNT > 18) {
        $TOTAL += $ONE_DAY_SALARY;
    }
    if ($LATE_COUNT > 21) {
        $TOTAL += $ONE_DAY_SALARY;
    }
    if ($LATE_COUNT > 24) {
        $TOTAL += $ONE_DAY_SALARY;
    }
    if ($LATE_COUNT > 27) {
        $TOTAL += $ONE_DAY_SALARY;
    }
    if ($LATE_COUNT > 30) {
        $TOTAL += $ONE_DAY_SALARY;
    }
    return round($TOTAL);
}

// Employee Unpaid Leave Salary
function CUSTOM_EMPLOYEE_UNPAID_LEAVE_SALARY($EMPLOYEE)
{
    $DAYS = 26;
    $UNPAID_LEAVE = CUSTOM_UNPAID_LEAVE_COUNT($EMPLOYEE);
    $EMPLOYEE_SALARY = $EMPLOYEE->salary;
    $ONE_DAY_SALARY = $EMPLOYEE_SALARY / $DAYS;
    $UNPAID_LEAVE_SALARY =  $ONE_DAY_SALARY * $UNPAID_LEAVE;
    return round($UNPAID_LEAVE_SALARY);
}

//GET EMPLOYEE PAYABLE SALARY
function CUSTOM_EMPLOYEE_PAYABLE_SALARY($EMPLOYEE)
{
    $LATE_DAYS_SALARY_DEDUCT = CUSTOM_LATE_DAYS_SALARY_DEDUCT($EMPLOYEE);
    $DAYS = 26;
    $OVERTIME_SALARY = CUSTOM_OVERTIME_SALARY($EMPLOYEE);
    $EMPLOYEE_SALARY = $EMPLOYEE->salary;
    $ONE_DAY_SALARY = $EMPLOYEE_SALARY / $DAYS;
    $UNPAID_LEAVE_SALARY = CUSTOM_EMPLOYEE_UNPAID_LEAVE_SALARY($EMPLOYEE);
    $DEDUCT_SALARY = $ONE_DAY_SALARY * CUSTOM_EMPLOYEE_ABSENCE_DAY($EMPLOYEE->id);
    $PAYABLE_SALARY = ($EMPLOYEE_SALARY + $OVERTIME_SALARY) - ($DEDUCT_SALARY + $LATE_DAYS_SALARY_DEDUCT + $UNPAID_LEAVE_SALARY);
    return round($PAYABLE_SALARY);
}

// Custom Paid Leave Count
function CUSTOM_PAID_LEAVE_COUNT($EMPLOYEE)
{
    $LEAVES = DB::table('leave_applications')->where('employee_id', $EMPLOYEE->id)->where('payment_status', 'paid')->where('status', 'approved')->whereMonth('apply_date', date('m'))->get();
    $DAYS = 0;
    foreach ($LEAVES as $LEAVE) {
        $START = Carbon::parse($LEAVE->apply_date);
        $END = Carbon::parse($LEAVE->end_date);
        $DAYS += $START->diffInDays($END);
        if ($DAYS != 0)
            $DAYS += 1;
    }
    return $DAYS;
}

// Custom Unpaid Leave Count
function CUSTOM_UNPAID_LEAVE_COUNT($EMPLOYEE)
{
    $LEAVES = DB::table('leave_applications')->where('employee_id', $EMPLOYEE->id)->where('payment_status', 'non-paid')->where('status', 'approved')->whereMonth('apply_date', date('m'))->get();
    $DAYS = 0;
    foreach ($LEAVES as $LEAVE) {
        $START = Carbon::parse($LEAVE->apply_date);
        $END = Carbon::parse($LEAVE->end_date);
        $DAYS += $START->diffInDays($END);
        if ($DAYS != 0)
            $DAYS += 1;
    }
    return $DAYS;
}
