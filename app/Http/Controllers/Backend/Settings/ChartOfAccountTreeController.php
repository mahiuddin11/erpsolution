<?php

namespace App\Http\Controllers\Backend\Settings;

use Illuminate\Http\Request;
use App\Models\Accounts;
use App\Models\Branch;
use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Services\Settings\AccountService;
use App\Transformers\AccountTransformer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChartOfAccountTreeController extends Controller
{

    public function index()
    {
        $title = 'COA Tree';
        return view('backend.pages.settings.account.account_tree', get_defined_vars());
    }


    public function treeData()
    {
        $accounts = ChartOfAccount::select('id', 'account_name', 'parent_id', 'balance_type', 'opening_balance')
            ->orderBy('id')
            ->get();

        $txSums = DB::table('account_transactions')
            ->select(
                'account_id',
                DB::raw('COALESCE(SUM(debit), 0) as debit'),
                DB::raw('COALESCE(SUM(credit), 0) as credit')
            )
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        $childrenByParent = $accounts->groupBy(function ($account) {
            return (is_null($account->parent_id) || (int) $account->parent_id === 0)
                ? 'root'
                : (string) $account->parent_id;
        });

        $topLevel = $childrenByParent->get('root', collect())
            ->map(fn($account) => $this->buildNode($account, $childrenByParent, $txSums, 0))
            ->values();

        $tree = [
            'id'       => 0,
            'name'     => 'Chart of Accounts',
            'type'     => 'root',
            'children' => $topLevel,
        ];

        return response()->json($tree);
    }

    private function buildNode($account, Collection $childrenByParent, Collection $txSums, int $depth)
    {

        $childAccounts = $childrenByParent->get((string) $account->id, collect());

        $children = $childAccounts
            ->map(fn($child) => $this->buildNode($child, $childrenByParent, $txSums, $depth + 1))
            ->values();

        $own = $txSums->get($account->id);
        $ownDebit = $own->debit ?? 0;
        $ownCredit = $own->credit ?? 0;

        $balanceType = $account->balance_type ?: 'debit';

        $openingDebit  = $balanceType === 'debit'  ? (float) $account->opening_balance : 0;
        $openingCredit = $balanceType === 'credit' ? (float) $account->opening_balance : 0;

        $totalDebit  = $openingDebit  + $ownDebit  + $children->sum('debit');
        $totalCredit = $openingCredit + $ownCredit + $children->sum('credit');

        return [
            'id'           => $account->id,
            'name'         => $account->account_name,
            'balance_type' => $balanceType,
            'type'         => $children->isEmpty()
                ? 'leaf'
                : ($depth === 0 ? 'cat' : 'sub'),
            'debit'        => round($totalDebit, 2),
            'credit'       => round($totalCredit, 2),
            'children'     => $children,
        ];
    }
}
