<?php

namespace App\Repositories\Settings;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Opening;
use Illuminate\Support\Facades\DB;
use App\Models\Expense;
use App\Models\Transection;
use phpDocumentor\Reflection\PseudoTypes\False_;

class ExpenseRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Opening
     */
    private $Expense;
    /**
     * CourseRepository constructor.
     * @param opening $opening
     */
    public function __construct(Expense $Expense)
    {
        $this->Expense = $Expense;
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
        return  $this->Expense::get();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request)
    {
        $columns = array(
            0 => 'id',
            1 => 'amount',
        );

        $edit = Helper::roleAccess('settings.expense.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.expense.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.expense.show') ? 0 : 0;
        $ced = $edit + $delete + $view;
        $totalData = $this->Expense::count();
        // dd($totalData);
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $auth = Auth::user();
        if (empty($request->input('search.value'))) {
            $Expense = $this->Expense::offset($start);
            if ($auth->branch_id !== null) {
                $Expense = $Expense->where('branch_id', $auth->branch_id);
            }
            $Expense = $Expense->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->Expense::count();
        } else {
            $search = $request->input('search.value');
            $Expense = $this->Expense::where('amount', 'like', "%{$search}%");
            if ($auth->branch_id !== null) {
                $Expense = $Expense->where('branch_id', $auth->branch_id);
            }
            $Expense = $Expense->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->Expense::where('amount', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($Expense) {
            foreach ($Expense as $key => $expens) {
                $nestedData['id'] = $key + 1;
                $nestedData['date'] = $expens->date;
                $nestedData['chartofaccount_id'] = $expens->chartOfaccount->accountCode . '-' . $expens->chartOfaccount->account_name;
                $nestedData['branch_id'] = $expens->branch->name;
                $nestedData['expensecategorie_id'] = $expens->category->name;
                $nestedData['expensesubcategorie_id'] = $expens->subcategory->name ?? "N/A";
                $nestedData['amount'] = $expens->amount;
                $nestedData['note'] = $expens->note;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('settings.expense.edit', $expens->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('settings.expense.show', $expens->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('settings.expense.destroy', $expens->id) . '" delete_id="' . $expens->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $expens->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->Expense::find($id);
        return $result;
    }

    public function store($request)
    {
        // dd($request->all());
        try {
            for ($i = 0; $i < count($request->category_id); $i++) {
                $expense = Expense::create([
                    'expensecategorie_id' => $request->category_id[$i],
                    'expensesubcategorie_id' => $request->subcategory_id[$i] ?? null,
                    'branch_id' => $request->branch_id[$i],
                    'chartofaccount_id' => $request->account_id[$i],
                    'date' => $request->date[$i],
                    'amount' => $request->amount[$i],
                    'note' => $request->note[$i],
                ]);


                $transection = new transection();
                $transection->account_id = $request->account_id[$i];
                $transection->branch_id = $request->branch_id[$i];
                $transection->credit = $request->amount[$i];
                $transection->amount = $request->amount[$i];
                $transection->note = $request->note[$i];
                $transection->date = $request->date[$i];
                $transection->payment_id = $expense->id;
                $transection->type = 4;
                $transection->user_id = Auth::user()->id;
                $transection->created_by = Auth::user()->id;
                $transection->save();
            }
            return $transection;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
        }
    }

    public function update($request, $id)
    {
        $expense = [
            'expensecategorie_id' => $request->category_id,
            'expensesubcategorie_id' => $request->subcategory_id,
            'branch_id' => $request->branch_id,
            'chartofaccount_id' => $request->account_id,
            'date' => $request->date,
            'amount' => $request->amount,
            'note' => $request->note,
        ];
        DB::table('expenses')->where('id', $id)->update($expense);

        $transection['account_id'] = $request->account_id;
        $transection['branch_id'] = $request->branch_id;
        $transection['credit'] = $request->amount;
        $transection['amount'] = $request->amount;
        $transection['note'] = $request->note;
        $transection['date'] = $request->date;
        $transection['updated_by'] = Auth::user()->id;
        // Transection::where('payment_id', $id)->orWhere('type', 4)->update($transection);
        Transection::where('payment_id', $id)->update($transection);

        return $expense;
    }

    public function statusUpdate($id, $status)
    {
        $opening = $this->Expense::find($id);
        $opening->status = $status;
        $opening->save();
        return $opening;
    }

    public function destroy($id)
    {
        $opening = $this->Expense::find($id);
        $opening->delete();
        Transection::where('type', 4)->where('payment_id', $id)->delete();
        return true;
    }
}
