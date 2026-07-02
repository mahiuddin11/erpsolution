<?php

/**
 * Balance Sheet Debug Script
 * ---------------------------
 * এই script টা route বা tinker দিয়ে চালাও।
 * এটা প্রতিটা root category (Asset, Liability, Equity, Income, Expense)-র
 * total opening_balance আলাদা করে বের করবে, আর orphan/uncategorized amount দেখাবে।
 *
 * ব্যবহার (routes/web.php তে সাময়িকভাবে যোগ করো):
 *
 * Route::get('/debug/balance-sheet', function () {
 *     require base_path('balance_sheet_debug.php');
 * });
 */

use Illuminate\Support\Facades\DB;

// ধাপ ১: সব account একবারে memory তে লোড করো (parent_id, opening_balance, balance_type সহ)
$allAccounts = DB::table('chart_of_accounts')->whereNull('deleted_at')->select('id', 'account_name', 'parent_id', 'opening_balance', 'balance_type')->get()->keyBy('id');

// ধাপ ২: root anchor definition (তোমার document অনুযায়ী)
$categoryAnchors = [
    1 => 'asset',
    10 => 'equity',
    14 => 'liability',
    15 => 'liability',
    17 => 'income',
    21 => 'expense',
];
$skipIds = [9]; // grouping node

// ধাপ ৩: প্রতিটা account-কে root পর্যন্ত walk করে category বের করো
function resolveCategory($accountId, $allAccounts, $categoryAnchors, $skipIds, &$visited = [])
{
    if (isset($visited[$accountId])) {
        return 'CIRCULAR_REFERENCE'; // safety net — infinite loop protection
    }
    $visited[$accountId] = true;

    if (!isset($allAccounts[$accountId])) {
        return 'ORPHAN_MISSING_ACCOUNT';
    }

    $account = $allAccounts[$accountId];

    if (isset($categoryAnchors[$accountId])) {
        return $categoryAnchors[$accountId];
    }

    if (in_array($accountId, $skipIds)) {
        return 'SKIPPED_GROUPING_NODE';
    }

    if ($account->parent_id == 0 || $account->parent_id === null) {
        return 'UNRESOLVED_ROOT_' . $accountId; // root-এ পৌঁছালো কিন্তু কোনো anchor না
    }

    return resolveCategory($account->parent_id, $allAccounts, $categoryAnchors, $skipIds, $visited);
}

// ধাপ ৪: প্রতিটা account-এর opening_balance সঠিক sign সহ বের করো এবং category-তে যোগ করো
$categoryTotals = [
    'asset' => 0,
    'liability' => 0,
    'equity' => 0,
    'income' => 0,
    'expense' => 0,
];
$unresolved = [];

foreach ($allAccounts as $id => $account) {
    if ($account->opening_balance == 0) {
        continue;
    }

    $category = resolveCategory($id, $allAccounts, $categoryAnchors, $skipIds);

    // sign convention: balance_type অনুযায়ী স্বাভাবিক দিকে ধরো
    $amount = $account->opening_balance;
    // debit balance_type হলে positive, credit হলে amount কে negative করে রাখছি
    // যাতে overall net zero হওয়া উচিত (double entry হলে)
    $signedAmount = $account->balance_type === 'debit' ? $amount : -$amount;

    if (isset($categoryTotals[$category])) {
        $categoryTotals[$category] += $amount; // category অনুযায়ী raw sum (sign convention পরে ঠিক করবে)
    } else {
        $unresolved[] = [
            'id' => $id,
            'account_name' => $account->account_name,
            'parent_id' => $account->parent_id,
            'opening_balance' => $account->opening_balance,
            'balance_type' => $account->balance_type,
            'category' => $category,
        ];
    }
}

echo '<h2>Category-wise Opening Balance Totals</h2>';
echo '<pre>';
print_r($categoryTotals);
echo '</pre>';

echo '<h2>⚠️ UNRESOLVED / UNCATEGORIZED Accounts (এখানেই bug থাকতে পারে)</h2>';
echo '<pre>';
print_r($unresolved);
echo '</pre>';

// ধাপ ৫: unresolved amount-এর সমষ্টি বের করো — এটাই gap-এর candidate
$unresolvedSum = array_sum(array_column($unresolved, 'opening_balance'));
echo "<h2>Total Unresolved Amount: {$unresolvedSum}</h2>";
echo '<p>যদি এই সংখ্যা 19,291,589.95 এর কাছাকাছি বা সমান হয়, তাহলে এখানেই আসল bug।</p>';

// ============================================================
// ধাপ ৬: PRIOR-YEAR (fiscal year শুরুর আগের) income/expense
// transaction খুঁজে বের করো — এটাই আসল সন্দেহের জায়গা
// ============================================================

$fiscalYearStart = date('Y') . '-01-01';

// Income/Expense category-র সব account_id বের করো
$incomeExpenseAccountIds = [];
foreach ($allAccounts as $id => $account) {
    $category = resolveCategory($id, $allAccounts, $categoryAnchors, $skipIds);
    if (in_array($category, ['income', 'expense'])) {
        $incomeExpenseAccountIds[$id] = $category;
    }
}

// fiscal year শুরুর *আগে* (অর্থাৎ all prior years) এই account গুলোতে কত transaction হয়েছে
$priorYearTx = DB::table('account_transactions')->whereDate('created_at', '<', $fiscalYearStart)->whereIn('account_id', array_keys($incomeExpenseAccountIds))->select('account_id', DB::raw('COALESCE(SUM(debit), 0) as total_debit'), DB::raw('COALESCE(SUM(credit), 0) as total_credit'))->groupBy('account_id')->get();

$priorYearNetProfit = 0;
foreach ($priorYearTx as $tx) {
    $category = $incomeExpenseAccountIds[$tx->account_id];
    if ($category === 'income') {
        $priorYearNetProfit += $tx->total_credit - $tx->total_debit;
    } else {
        $priorYearNetProfit -= $tx->total_debit - $tx->total_credit;
    }
}

echo '<h2>🎯 Prior-Year (fiscal year শুরুর আগের সব) Income/Expense Net Profit</h2>';
echo "<h3 style='color:red;'>{$priorYearNetProfit}</h3>";
echo '<p>এই সংখ্যা যদি 19,291,589.95 এর কাছাকাছি হয়, তাহলে এটাই confirmed root cause — ';
echo 'এই amount কোনো year-end closing entry দিয়ে কখনো Retained Earnings এ transfer হয়নি, ';
echo 'ফলে Balance Sheet-এ কোথাও যোগ হচ্ছে না।</p>';
