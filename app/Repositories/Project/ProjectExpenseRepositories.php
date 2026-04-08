<?php

namespace App\Repositories\Project;

use App\Helpers\Helper;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\Auth;
use App\Models\Opening;
use Illuminate\Support\Facades\DB;
use App\Models\ExpenseCategory;
use App\Models\ProjectExpense;
use App\Models\Transection;
use App\Models\Project;

use phpDocumentor\Reflection\PseudoTypes\False_;

class ProjectExpenseRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Opening
     */
    private $ProjectExpense;
    /**
     * CourseRepository constructor.
     * @param opening $opening
     */
    public function __construct(ProjectExpense $ProjectExpense)
    {
        $this->ProjectExpense = $ProjectExpense;
        //$this->middleware(function ($request, $next) {
        $this->user_id = 1; //auth()->user()->id;
        //  return $next($request);
        //});
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllOpening()
    {
        return  $this->ProjectExpense::get();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        $userid = auth()->user()->id;
        $usertype = auth()->user()->type;

        if ($usertype == 'Project') {
            $projectDetails = Project::where('manager_id', $userid)->firstOrFail();
        } else {
            $projectDetails = '';
        }

        $user = Auth::user();
        $project = Project::where('manager_id', $user->id)->pluck('condition')->first();
        $condition = $project ? $project : "Complete";

        $columns = array(
            0 => 'id',
            1 => 'amount',
        );

        $edit = Helper::roleAccess('project.projectexpense.edit') && $condition !== "Complete"  ? 1 : 0;
        $delete = Helper::roleAccess('project.projectexpense.destroy') && $condition !== "Complete" ? 1 : 0;
        $view = Helper::roleAccess('project.projectexpense.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->ProjectExpense::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if ($usertype == 'Project') {

            if (empty($request->input('search.value'))) {
                $ProjectExpense = $this->ProjectExpense::offset($start)
                    ->where('project_id', $projectDetails->id)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectExpense::count();
            } else {
                $search = $request->input('search.value');
                $ProjectExpense = $this->ProjectExpense::where('amount', 'like', "%{$search}%")
                    ->where('project_id', $projectDetails->id)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectExpense::where('amount', 'like', "%{$search}%")->count();
            }
        } else {

            if (empty($request->input('search.value'))) {
                $ProjectExpense = $this->ProjectExpense::offset($start)

                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectExpense::count();
            } else {
                $search = $request->input('search.value');
                $ProjectExpense = $this->ProjectExpense::where('amount', 'like', "%{$search}%")

                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $this->ProjectExpense::where('amount', 'like', "%{$search}%")->count();
            }
        }




        $data = array();
        if ($ProjectExpense) {
            foreach ($ProjectExpense as $key => $expens) {
                // dd($expens->projects);
                $nestedData['id'] = $key + 1;
                $nestedData['project_id'] = $expens->projects->name;
                $nestedData['categorie_id'] = $expens->expenseCategory->name ?? "N/A";
                $nestedData['subcategorie_id'] = $expens->subCategory->name ?? "N/A";
                $nestedData['amount'] = $expens->amount;
                $nestedData['note'] = $expens->note;

                if ($ced != 0) :

                    if ($edit != 0)
                        $edit_data = '<a href="' . route('project.projectexpense.edit', $expens->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('project.projectexpense.show', $expens->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('project.projectexpense.destroy', $expens->id) . '" delete_id="' . $expens->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $expens->id . '"><i class="fa fa-times"></i></a>';
                    else
                        $delete_data = '';
                    $nestedData['action'] = $edit_data . ' ' . $view_data . ' ' . $delete_data;
                else :
                    $nestedData['action'] = '';
                endif;

                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        return $json_data;
    }
    /**
     * @param $request
     * @return mixed
     */
    public function details($id)
    {
        $result = $this->ProjectExpense::find($id);
        return $result;
    }

    public function store($request)
    {
        //         dd($projectDetails->id);

        $ProjectExpense = new ProjectExpense();
        $ProjectExpense->categorie_id = $request->category_id;
        $ProjectExpense->subcategorie_id = $request->subcategory_id ?? 0;
        $ProjectExpense->project_id = $request->project_id;
        $ProjectExpense->account_id = $request->account_id;
        $ProjectExpense->date = $request->date;
        $ProjectExpense->amount = $request->amount;
        $ProjectExpense->note = $request->note;
        $ProjectExpense->save();

        $invoice = AccountTransaction::accountInvoice();
        $transaction['invoice'] = $invoice;
        $transaction['table_id'] = $ProjectExpense->id;
        $transaction['account_id'] = 10; // project
        $transaction['type'] = 11;
        $transaction['branch_id'] = 0;
        $transaction['debit'] = $request->amount;
        $transaction['remark'] = $request->note;
        $transaction['created_by'] = Auth::id();
        AccountTransaction::create($transaction);

        $transactionPay['invoice'] = $invoice;
        $transactionPay['table_id'] = $ProjectExpense->id;
        $transactionPay['account_id'] = $request->account_id; // ->
        $transactionPay['type'] = 11;
        $transactionPay['branch_id'] = 0;
        $transactionPay['credit'] =   $request->amount;
        $transactionPay['remark'] = $request->note;
        $transactionPay['created_by'] = Auth::id();
        AccountTransaction::create($transactionPay);

        return $ProjectExpense;
    }

    public function update($request, $id)
    {

        $ProjectExpense = ProjectExpense::find($id);
        $ProjectExpense->categorie_id = $request->category_id;
        $ProjectExpense->subcategorie_id = $request->subcategory_id ?? 0;
        $ProjectExpense->account_id = $request->account_id;
        $ProjectExpense->date = $request->date;
        $ProjectExpense->amount = $request->amount;
        $ProjectExpense->note = $request->note;
        $ProjectExpense->save();

        AccountTransaction::where('type', 11)->where('table_id', $ProjectExpense->id)->delete();

        $invoice = AccountTransaction::accountInvoice();
        $transaction['invoice'] = $invoice;
        $transaction['table_id'] = $ProjectExpense->id;
        $transaction['account_id'] = 10; // project
        $transaction['type'] = 11;
        $transaction['branch_id'] = 0;
        $transaction['debit'] = $request->amount;
        $transaction['remark'] = $request->note;
        $transaction['created_by'] = Auth::id();
        AccountTransaction::create($transaction);

        $transactionPay['invoice'] = $invoice;
        $transactionPay['table_id'] = $ProjectExpense->id;
        $transactionPay['account_id'] = $request->account_id; // ->
        $transactionPay['type'] = 11;
        $transactionPay['branch_id'] = 0;
        $transactionPay['credit'] =   $request->amount;
        $transactionPay['remark'] = $request->note;
        $transactionPay['created_by'] = Auth::id();
        AccountTransaction::create($transactionPay);

        return $ProjectExpense;
    }

    public function statusUpdate($id, $status)
    {
        $opening = $this->ProjectExpense::find($id);
        $opening->status = $status;
        $opening->save();
        return $opening;
    }

    public function destroy($id)
    {
        $opening = $this->ProjectExpense::find($id);
        $opening->delete();
        AccountTransaction::where('type', 11)->where('table_id', $id)->delete();
        return true;
    }
}
