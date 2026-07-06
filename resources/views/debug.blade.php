<?php

use App\Models\ChartOfAccount;
use App\Models\AccountTransaction;

$fromDate = '2026-01-06';
$toDate = '2026-12-06';

$allAccounts = ChartOfAccount::select('id', 'parent_id', 'account_name')->whereNull('deleted_at')->get();
$childrenMap = $allAccounts->groupBy('parent_id');
$byId = $allAccounts->keyBy('id');

$collectTree = function ($rootIds) use ($childrenMap) {
    $result = [];
    $stack = (array) $rootIds;
    while (count($stack) > 0) {
        $id = array_pop($stack);
        if (in_array($id, $result)) {
            continue;
        }
        $result[] = $id;
        $children = isset($childrenMap[$id]) ? $childrenMap[$id] : collect();
        foreach ($children as $child) {
            $stack[] = $child->id;
        }
    }
    return $result;
};

$trackedIds = array_unique(array_merge($collectTree([5]), $collectTree([396]), $collectTree([2]), $collectTree([6, 7, 8]), $collectTree([16]), $collectTree([923]), $collectTree([924]), $collectTree([568, 653]), $collectTree([11]), $collectTree([451]), $collectTree([233])));

$allBsIds = $collectTree([1, 9]);
$untracked = array_diff($allBsIds, $trackedIds);

$leaks = AccountTransaction::whereIn('account_id', $untracked)->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)->selectRaw('account_id, SUM(COALESCE(debit,0)) as d, SUM(COALESCE(credit,0)) as c')->groupBy('account_id')->havingRaw('SUM(COALESCE(debit,0)) - SUM(COALESCE(credit,0)) != 0')->orderByRaw('ABS(SUM(COALESCE(debit,0)) - SUM(COALESCE(credit,0))) DESC')->get();

$totalLeak = 0;
foreach ($leaks as $l) {
    $name = isset($byId[$l->account_id]) ? $byId[$l->account_id]->account_name : 'Unknown';
    $net = $l->d - $l->c;
    $totalLeak += $net;
    echo "id={$l->account_id} | {$name} | Net: " . number_format($net, 2) . "\n";
}
echo "\nTOTAL LEAK: " . number_format($totalLeak, 2) . "\n";
