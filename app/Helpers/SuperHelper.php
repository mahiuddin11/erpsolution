<?php

use App\Models\Accounts;
use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\Transection;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


define('MEDICAL_ALLOWANCE', 600);
define('TRAVEL_ALLOWANCE', 350);
define('FOOD_ALLOWANCE', 900);

function ALLOWANCE_AMOUNT()
{
    return MEDICAL_ALLOWANCE + TRAVEL_ALLOWANCE + FOOD_ALLOWANCE;
}

function monthPercentageChange($previous, $current)
{
    if ($previous == 0) {
        return 0;
    }

    $change = (($current - $previous) / $previous) * 100;

    return round($change, 2);
}

// GET THIS MONTH WORKING DAYS
function MONTH_WORKING_DAY($month = null)
{
    $month = $month ? $month : date("Y-m");
    $month = date("Y-m-d", strtotime($month . "-01"));

    $carbonMonth = Carbon::parse($month);

    $startOfMonth = Carbon::parse($month);
    $endOfMonth = $carbonMonth->endOfMonth();
    // Get Fridays count in the given month
    $CURRENT_MONTH_HOLIDAY = CarbonPeriod::create($startOfMonth, $endOfMonth)
        ->filter(fn ($date) => $date->isFriday()) // Corrected condition
        ->count();

    $TOTAL_DAT_OF_THIS_MONTH = $carbonMonth->daysInMonth;
    $TOTAL_WORKING_DAY = $TOTAL_DAT_OF_THIS_MONTH - $CURRENT_MONTH_HOLIDAY;
    return $TOTAL_WORKING_DAY;
}

//GET EMPLOYEE WORKING DAYS
function EMPLOYEE_PRESENCE_DAY($EMPLOYEE_ID,$month)
{
    $ATTENDANCE = DB::table('attendances')->where('emplyee_id', $EMPLOYEE_ID)->whereMonth('date', $month ? date('m',strtotime($month)) : date('m'))->count();
    return $ATTENDANCE;
}

//GET EMPLOYEE LEAVE DAYS COUNT
function EMPLOYEE_ABSENCE_DAY($EMPLOYEE_ID,$month)
{
    $EMPLOYEE_WORKING_DAYS = EMPLOYEE_PRESENCE_DAY($EMPLOYEE_ID,$month);
    $LEAVE_COUNT = MONTH_WORKING_DAY() - $EMPLOYEE_WORKING_DAYS;
    return $LEAVE_COUNT;
}

//GET EMPLOYEE MAIN SALARY
function EMPLOYEE_BASIC_SALARY($EMPLOYEE_SALARY)
{
    $MAIN_SALARY = ($EMPLOYEE_SALARY - ALLOWANCE_AMOUNT()) / 1.5;
    $HALF_SALARY = $EMPLOYEE_SALARY * 0.5; // Calculate 50% of the salary

    return [
        'main_salary' => round($MAIN_SALARY),
        'half_salary' => round($HALF_SALARY),
    ];
}

//GET EMPLOYEE HOUSE RENT MAIN SALARY
function EMPLOYEE_HOUSE_RENT_SALARY($EMPLOYEE_SALARY)
{
    $HOUSE_RENT = EMPLOYEE_BASIC_SALARY($EMPLOYEE_SALARY)['half_salary'] / 2;
    return round($HOUSE_RENT);
}

function OVERTIME_HOURE($EMPLOYEE)
{
    $ATTENDANCES = DB::table('attendances')->where('emplyee_id', $EMPLOYEE->id)->whereMonth('date', date('m'))->get();
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

        if ($officeEndTime < $signOutTime) {
            $TOTAL_TIME = $in->diff($lastin->subHour(8));
            $HOURE += $TOTAL_TIME->h;
            if ($TOTAL_TIME->i >= 50) {
                $HOURE += 1;
            }
        }
    }

    return round($HOURE);
}

function OVERTIME_SALARY($EMPLOYEE)
{
    if ($EMPLOYEE->over_time_is == "yes") {
        $ATTENDANCES = DB::table('attendances')->where('emplyee_id', $EMPLOYEE->id)->whereMonth('date', date('m'))->get();
        $EMPLOYEE_BASIC_SALARY = EMPLOYEE_BASIC_SALARY($EMPLOYEE->salary)['half_salary'];
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
        $TOTAL_OVERTIME = $ONE_DAY_SALARY_DOUBLE * $HOURE;
    }

    return round($TOTAL_OVERTIME ?? 0);
}

//GET EMPLOYEE LATE DAYS
function LATE_DAYS($EMPLOYEE)
{
    $EMPLOYEE_LAST_IN_TIME = Carbon::parse($EMPLOYEE->last_in_time)->addMinutes(15)->format("H:i:s");
    $LATE = DB::table('attendances')->where('emplyee_id', $EMPLOYEE->id)->whereMonth('date', date('m'))
        ->whereTime('sign_in', ">", $EMPLOYEE_LAST_IN_TIME)->count();
    return $LATE;
}

function LATE_DAYS_SALARY_DEDUCT($EMPLOYEE)
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


function EMPLOYEE_UNPAID_LEAVE_SALARY($EMPLOYEE)
{
    $DAYS = 26;
    $UNPAID_LEAVE = UNPAID_LEAVE_COUNT($EMPLOYEE);
    $EMPLOYEE_SALARY = $EMPLOYEE->salary;
    $ONE_DAY_SALARY = $EMPLOYEE_SALARY / $DAYS;
    $UNPAID_LEAVE_SALARY =  $ONE_DAY_SALARY * $UNPAID_LEAVE;
    return round($UNPAID_LEAVE_SALARY);
}

//GET EMPLOYEE PAYABLE SALARY
function EMPLOYEE_PAYABLE_SALARY($EMPLOYEE,$month)
{
    $LATE_DAYS_SALARY_DEDUCT = LATE_DAYS_SALARY_DEDUCT($EMPLOYEE);
    $DAYS = 26;
    $OVERTIME_SALARY = OVERTIME_SALARY($EMPLOYEE);
    $EMPLOYEE_SALARY = $EMPLOYEE->salary;
    $ONE_DAY_SALARY = $EMPLOYEE_SALARY / $DAYS;
    $UNPAID_LEAVE_SALARY = EMPLOYEE_UNPAID_LEAVE_SALARY($EMPLOYEE);
    $DEDUCT_SALARY = $ONE_DAY_SALARY * EMPLOYEE_ABSENCE_DAY($EMPLOYEE->id,$month);
    $PAYABLE_SALARY = ($EMPLOYEE_SALARY + $OVERTIME_SALARY) - ($DEDUCT_SALARY + $LATE_DAYS_SALARY_DEDUCT + $UNPAID_LEAVE_SALARY);
    return round($PAYABLE_SALARY);
}

function PAID_LEAVE_COUNT($EMPLOYEE)
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

function UNPAID_LEAVE_COUNT($EMPLOYEE)
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


function AccountBalance($id)
{
    $opening =  Transection::where('account_id', $id)->where('type', 1)->pluck('amount')->first() ?? 0;
    $debit = AccountTransaction::where('account_id', '=', $id)->sum('debit');
    $credit = AccountTransaction::where('account_id', '=', $id)->sum('credit');
    $total = ($debit - $credit) + $opening;

    $account = ChartOfAccount::findOrFail($id);

    // Calculate the opening balance as of the start date
    $debitSumBeforeStartDate = AccountTransaction::where('account_id', $id)

        ->sum('debit');

    $creditSumBeforeStartDate = AccountTransaction::where('account_id', $id)

        ->sum('credit');

    // Adjust opening balance based on the balance type
    if ($account->balance_type === 'debit') {
        $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
    } else {
        $openingBalance = -$account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
    }

    $runningBalance = $openingBalance;

    return $runningBalance;
}

function copyword($num, $syntexs)
{
    if ($num != 0) {
        $syntex = $syntexs;
        for ($i = 0; $i < $num; $i++) {
            $syntex .= $syntex;
        }
        return $syntex;
    }
    return "";
}

function getFirstAccount($id)
{
    $account_list = ChartOfAccount::find($id);
    if ($account_list) {
        $lastid = $id;
        if ($account_list->account) {
            $lastid = $account_list->account->id;
            return  getFirstAccount($lastid);
        }
        return $lastid;
    } else {
        return 0;
    }
}

// function getUnderAccount($id, $today = null, $acType = true)
// {
//     $accounts = ChartOfAccount::getaccount($id)->get();
//     $closebalance = 0;
//     $startDate = date("Y-m-d");
//     $endDate = date("Y-m-d");


//     foreach ($accounts as $account) {
//         $selectedAccountId = $account->id;
//         if ($selectedAccountId) {
//             $account = ChartOfAccount::findOrFail($selectedAccountId);

//             // Calculate the opening balance as of the start date
//             $debitSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
//                 ->whereDate('created_at', '<', $startDate)
//                 ->sum('debit');

//             $creditSumBeforeStartDate = AccountTransaction::where('account_id', $selectedAccountId)
//                 ->whereDate('created_at', '<', $startDate)
//                 ->sum('credit');

//             // Adjust opening balance based on the balance type
//             if ($account->balance_type === 'debit') {
//                 $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
//             } else {
//                 $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
//             }

//             $runningBalance = $openingBalance;

//             $transactions = AccountTransaction::where('account_id', $selectedAccountId)
//                 ->when($startDate, function ($query) use ($startDate) {
//                     return $query->whereDate('created_at', '>=', $startDate);
//                 })
//                 ->when($endDate, function ($query) use ($endDate) {
//                     return $query->whereDate('created_at', '<=', $endDate);
//                 })
//                 ->orderBy('created_at')
//                 ->get();

//             foreach ($transactions as $transaction) {
//                 $relatedAccountTransaction = AccountTransaction::where('invoice', $transaction->invoice)
//                     ->where('account_id', '!=', $selectedAccountId);
//                 if ($transaction->debit) {
//                     $relatedAccountTransaction = $relatedAccountTransaction->whereNotNull('credit');
//                 }
//                 if ($transaction->credit) {
//                     $relatedAccountTransaction = $relatedAccountTransaction->whereNotNull('debit');
//                 }
//                 $relatedAccountTransaction = $relatedAccountTransaction->first();

//                 $debit = $transaction->debit ?? 0;
//                 $credit = $transaction->credit ?? 0;

//                 if ($account->balance_type == "debit") {
//                     $runningBalance += $debit - $credit;
//                 } else {
//                     $runningBalance += $credit -  $debit;
//                 }
//             }
//             $closebalance += $runningBalance;
//         }
//     }

//     return $closebalance;
// }

function getUnderAccount($id, $today = null, $acType = true)
{
    $accounts = ChartOfAccount::getaccount($id)->get();
    

    $runningBalance = 0;

    foreach ($accounts as $account) {
        // Calculate the opening balance as of the start date
        $debitSumBeforeStartDate = AccountTransaction::where('account_id', $account->id);
        if ($today) {
            $debitSumBeforeStartDate =  $debitSumBeforeStartDate->whereDate('created_at',  $today);
        }
        $debitSumBeforeStartDate =  $debitSumBeforeStartDate->sum('debit');

        $creditSumBeforeStartDate = AccountTransaction::where('account_id', $account->id);
        if ($today) {
            $creditSumBeforeStartDate = $creditSumBeforeStartDate->whereDate('created_at', $today);
        }
        $creditSumBeforeStartDate = $creditSumBeforeStartDate->sum('credit');

        // Adjust opening balance based on the balance type
        if ($account->balance_type === 'debit') {
            $openingBalance = $account->opening_balance + $debitSumBeforeStartDate - $creditSumBeforeStartDate;
        } else {
            $openingBalance = -$account->opening_balance +  $creditSumBeforeStartDate - $debitSumBeforeStartDate;
        }
        $runningBalance += $openingBalance;
    }

    return $runningBalance;
}


function supplierDue($supplierid = 0)
{
    $supplier_id = $supplierid;
    $supplierLedger = AccountTransaction::where('account_id', "!=", 14)->selectRaw('SUM(debit) as debit,SUM(credit) as credit,account_id');

    $authcheck = auth()->user()->type;

    if ($authcheck != "Admin") {
        $supplierLedger =  $supplierLedger->where('created_by', auth()->id());
    }

    if ($supplier_id != 0) {
        $supplierLedger =  $supplierLedger->where('supplier_id', $supplier_id);
    } else {
        $supplierLedger =  $supplierLedger->whereNotNull('supplier_id');
    }
    $supplierLedger =  $supplierLedger->first();
    return $supplierLedger->debit - $supplierLedger->credit;
}

function customerDue($customer = 0)
{
    $customer_id = $customer;
    $customerLedger = AccountTransaction::where('account_id', "!=", 5)->selectRaw('SUM(debit) as debit,SUM(credit) as credit,account_id');
    if ($customer_id != 0) {
        $customerLedger =  $customerLedger->where('customer_id', $customer_id);
    } else {
        $customerLedger =  $customerLedger->whereNotNull('customer_id');
    }
    $customerLedger =  $customerLedger->first();
    return  $customerLedger->credit - $customerLedger->debit;
}



// function getSubAccount($id = [])
// {
//     $arraytoString = implode(',', $id);
//     $sql = "SELECT  id from `chart_of_accounts` where `parent_id` in ($arraytoString)";
//     $ids = DB::select($sql);
//     $data_array = array();
//     // Fetch data and store it in the array
//     foreach ($ids as $result) {
//         $data_array[] = $result->id;
//     }
//     $marge = array_merge($data_array, $id);
//     if (count($ids) > 0) {
//         return getOldAccount($marge);
//     }
//     return $id;
// }

// function getOldAccount($id = [])
// {
//     $arraytoString = getSubAccount($id);
//     $sql = "SELECT  id from `chart_of_accounts` where `parent_id` in ($arraytoString)";
//     $ids = DB::select($sql);
//     $data_array = array();
//     // Fetch data and store it in the array
//     foreach ($ids as $result) {
//         $data_array[] = $result->id;
//     }
//     $marge = array_merge($data_array, $id);
//     if (count($ids) > 0) {
//         return getOldAccount($marge);
//     }
//     return $id;
// }

function getTypeOfAccount($id = [], $oldIds = [])
{
    $ids = ChartOfAccount::whereIn('parent_id', $id)->pluck('id')->toArray();
    if ($ids) {
        $marge = array_merge($ids, $oldIds);
        return getTypeOfAccount($ids, $marge);
    }
    return $oldIds;
}

function getOldAccount($id = null , $uniqid = 0)
{
    $id = $uniqid ? $uniqid : getAccountByUniqueID($id)->id;
    $account_list =  ChartOfAccount::where('status', 'Active');
    if ($id) {
        $account_list = $account_list->whereIn('id', getTypeOfAccount([$id]))->orWhereIn("id",[$id]);
    }
    // $account_list = $account_list->where('company_id', auth()->user()->company_id);
    return $account_list;
}

function account_with_name($transaction)
{
    $name = "";

    $account =  ($transaction->account->account_name ?? "Opening Balance") . " " . $name;

    return $account;
}

function accountledger($id, $text)
{
    if ($id != 0) {
        $url = route('report.ledger.accountledger', ['account_id' => $id]);
        $a_tag = "<a href='$url'>$text</a>";
    } else {
        $a_tag = $text;
    }

    return $a_tag;
}

function rootvoucher($id, $text)
{
    if ($id != 0) {
        $url = route('report.ledger.accountledger', ['account_id' => $id]);
        $a_tag = "<a href='$url'>$text</a>";
    } else {
        $a_tag = $text;
    }

    return $a_tag;
}

function numf($number)
{
    $formate = $number;
    if (0 > $formate) {
        $abs = abs($formate);
        $formate = "(" . number_format($abs, 2) . ")";
    } else {
        $formate = number_format($formate, 2);
    }


    return $formate;
}


function getAllSubAccounts($parentId)
{
    $subAccounts = ChartOfAccount::where('parent_id', $parentId)->get();
    $allAccounts = [];

    foreach ($subAccounts as $subAccount) {
        $allAccounts[] = $subAccount;
        $allAccounts = array_merge($allAccounts, getAllSubAccounts($subAccount->id));
    }

    return $allAccounts;
}

function getAccountByUniqueID($uid)
{
    return ChartOfAccount::firstWhere('unique_identifier', $uid);
}


function getAccountIdsToArray()
{
    $args = func_get_args();

    // Initialize an array to hold account IDs
    $accountIds = [];

    // Loop through each argument
    foreach ($args as $uniqueID) {
        if (is_array($uniqueID)) {
            $accountIds =  array_merge($accountIds, $uniqueID);
        } elseif (is_int($uniqueID)) {
            $accountId = getAccountByUniqueID($uniqueID)->id;
            array_push($accountIds, $accountId);
        }
        // Retrieve the account ID based on the unique ID and add it to the array
    }
    return $accountIds;
}

// Helper function to calculate account balance
function calculateBalance($accountId, $startDate, $endDate)
{
    $openingBalance = DB::table('chart_of_accounts')
        ->where('id', $accountId)
        ->select('opening_balance', 'balance_type')
        ->first();

    $transactions = DB::table('account_transactions')
        ->where('account_id', $accountId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
        ->first();

    $balance = ($transactions->total_credit ?? 0) - ($transactions->total_debit ?? 0);
    if ($openingBalance->balance_type == 'debit') {
        $balance += $openingBalance->opening_balance;
    } else {
        $balance -= $openingBalance->opening_balance;
    }

    return $balance;
}

// function numberToWords($number) {
//     $words = [
//         0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
//         6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
//         11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
//         15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
//         19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty',
//         50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
//     ];

//     $units = ['Crore', 'Lakh', 'Thousand', 'Hundred', ''];
//     $divisors = [10000000, 100000, 1000, 100, 1];

//     $result = "";
//     $i = 0;

//     foreach ($divisors as $divisor) {
//         $quotient = floor($number / $divisor);
//         $number %= $divisor;

//         if ($quotient > 0) {
//             if ($quotient < 20) {
//                 $result .= $words[$quotient] . " ";
//             } else {
//                 $result .= $words[floor($quotient / 10) * 10] . " " . $words[$quotient % 10] . " ";
//             }

//             $result .= $units[$i] . " ";
//         }
//         $i++;
//     }

//     return trim($result .' '. 'Taka Only');
// }

function numberToWords($number)
{
    $words = [
        0 => '',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen',
        20 => 'Twenty',
        30 => 'Thirty',
        40 => 'Forty',
        50 => 'Fifty',
        60 => 'Sixty',
        70 => 'Seventy',
        80 => 'Eighty',
        90 => 'Ninety'
    ];

    $units = ['Crore', 'Lakh', 'Thousand', 'Hundred', ''];
    $divisors = [10000000, 100000, 1000, 100, 1];

    // Split Taka and Paisa
    $taka = floor($number);
    $paisa = round(($number - $taka) * 100);
   

    $result = "";
    $i = 0;

    foreach ($divisors as $divisor) {
        $quotient = floor($taka / $divisor);
        $taka %= $divisor;

        if ($quotient > 0) {
            if ($quotient < 20) {
                $result .= $words[$quotient] . " ";
            } else {
                $result .= $words[floor($quotient / 10) * 10] . " " . $words[$quotient % 10] . " ";
            }

            $result .= $units[$i] . " ";
        }
        $i++;
    }

    $result = trim($result) . " Taka";

    // Paisa convert
    if ($paisa > 0) {
        if ($paisa < 20) {
            $result .= " And " . $words[$paisa] . " Paisa";
        } else {
            $result .= " And " . $words[floor($paisa / 10) * 10] . " " . $words[$paisa % 10] . " Paisa";
        }
    }

    return $result . " Only";
}


function smartNumberFormat($num)
{
    if ($num >= 1000000000000) {
        return number_format($num / 1000000000000, 2) . 'T';
    } elseif ($num >= 1000000000) {
        return number_format($num / 1000000000, 2) . 'B';
    } elseif ($num >= 10000000) {
        return number_format($num / 10000000, 2) . 'Cr';
    } elseif ($num >= 1000000) {
        return number_format($num / 1000000, 2) . 'M';
    } elseif ($num >= 100000) {
        return number_format($num / 100000, 2) . 'L';
    } elseif ($num >= 1000) {
        return number_format($num / 1000, 2) . 'K';
    }

    return $num;
}

function checkLocation($latitude, $longitude , $type = 'check_in')
{
   if(!$latitude && !$longitude){
     return '<span class="badge badge-danger"><i class="fa fa-map-marker"></i> GPS OFF</span>';
   }

   $officeLat = config("officeLocation.latitude"); 
   $officeLog = config("officeLocation.longitude");
   if($latitude == $officeLat && $longitude == $officeLog){
        return '<span class="badge badge-success"><i class="fa fa-building"></i> Office</span>';
   }

   $text = $type == 'check_out' ? 'Check Out Location' : 'Check In Location';
   return '<a href="https://www.google.com/maps?q=' . $latitude . ',' . $longitude . '" target="_blank" class="badge badge-info"> <i class="fa fa-map-marker"></i> ' . $text . '</a>';
}

/**
 * Cross Midnight Logic - Effective Date 
 */
function getEffectiveDate($datetime)
{
    $DAY_CUTOFF_TIME = '05:00:00';

    $datetime = new \DateTime($datetime);
    $timeOnly = $datetime->format('H:i:s');

    // সকাল 5:০০ এর আগে হলে আগের দিন ধরবে
    if ($timeOnly <  $DAY_CUTOFF_TIME ) {
        $datetime->modify('-1 day');
    }

    return $datetime->format('Y-m-d');
}
