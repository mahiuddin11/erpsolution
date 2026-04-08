<?php

namespace App\Repositories\AssetsManagement;

use App\Helpers\Helper;
use App\Models\AccountTransaction;
use App\Models\AssetsList;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductUnit;
use phpDocumentor\Reflection\PseudoTypes\False_;

class AssetsListRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var AssetsList
     */
    private $assetsList;
    /**
     * CourseRepository constructor.
     * @param assetsList $productUnit
     */
    public function __construct(AssetsList $assetsList)
    {
        $this->assetsList = $assetsList;
        //$this->middleware(function ($request, $next) {
        $this->user_id = 1; //auth()->user()->id;
        //  return $next($request);
        //});
    }


    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        $result = $this->assetsList::latest()->get();
        return $result;
    }
    /**
     * @param $request
     * @return mixed
     */

    public function getList($request)
    {

        $columns = array(
            0 => 'id',
            1 => 'name',
        );

        $edit = Helper::roleAccess('assets.list.edit') ? 1 : 0;
        $delete = Helper::roleAccess('assets.list.destroy') ? 1 : 0;
        $view = Helper::roleAccess('assets.list.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->assetsList::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $productUnits = $this->assetsList::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->assetsList::count();
        } else {
            $search = $request->input('search.value');
            $productUnits = $this->assetsList::where('name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->assetsList::where('name', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($productUnits) {
            foreach ($productUnits as $key => $productUnit) {
                $nestedData['id'] = $key + 1;
                $nestedData['name'] = $productUnit->name;
                $nestedData['category_asset_id'] = $productUnit->assetCategory->category_name;
                $nestedData['_date'] = $productUnit->_date;
                $nestedData['qty'] = $productUnit->qty;
                $nestedData['amount'] = $productUnit->amount;
                if ($productUnit->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('assets.list.status', [$productUnit->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('assets.list.status', [$productUnit->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('assets.list.edit', $productUnit->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('assets.list.show', $productUnit->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('assets.list.destroy', $productUnit->id) . '" delete_id="' . $productUnit->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $productUnit->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->assetsList::find($id);
        return $result;
    }

    public function store($request)
    {

        $productUnit = new $this->assetsList();
        $productUnit->name = $request->name;
        $productUnit->account_id = $request->account_id;
        $productUnit->payment_account = $request->payment_account;
        $productUnit->category_asset_id = $request->category_asset_id;
        $productUnit->qty = $request->qty;
        $productUnit->_date = $request->_date;
        $productUnit->amount = $request->amount;
        $productUnit->save();

        $invoice = AccountTransaction::accountInvoice();
        $transactionPay['payment_invoice'] = $request->invoice_no;
        $transactionPay['invoice'] = $invoice;
        $transactionPay['table_id'] = $productUnit->id;
        $transactionPay['account_id'] = $request->account_id; // ->acces
        $transactionPay['type'] = 13;
        $transactionPay['branch_id'] = $request->branch_id ?? 0;
        $transactionPay['debit'] =  $request->amount;
        $transactionPay['remark'] = $request->narration ?? "";
        $transactionPay['created_by'] = Auth::id();
        AccountTransaction::create($transactionPay);

        $transaction['payment_invoice'] = $request->invoice_no;
        $transaction['invoice'] = $invoice;
        $transaction['table_id'] = $productUnit->id;
        $transaction['account_id'] = $request->payment_account;
        $transaction['type'] = 13;
        $transaction['branch_id'] = $request->branch_id ?? 0;
        $transaction['credit'] = $request->amount;
        $transaction['remark'] = $request->narration ?? "";
        $transaction['created_by'] = Auth::id();
        AccountTransaction::create($transaction);

        return $productUnit;
    }

    public function update($request, $id)
    {
        $productUnit = $this->assetsList::findOrFail($id);
        $productUnit->name = $request->name;
        $productUnit->account_id = $request->account_id;
        $productUnit->payment_account = $request->payment_account;
        $productUnit->category_asset_id = $request->category_asset_id;
        $productUnit->_date = $request->_date;
        $productUnit->qty = $request->qty;
        $productUnit->amount = $request->amount;
        $productUnit->save();

        AccountTransaction::where('type',13)->where('table_id',$productUnit->id)->delete();
        $invoice = AccountTransaction::accountInvoice();
        $transactionPay['payment_invoice'] = $request->invoice_no;
        $transactionPay['invoice'] = $invoice;
        $transactionPay['table_id'] = $productUnit->id;
        $transactionPay['account_id'] = $request->account_id; // ->acces
        $transactionPay['type'] = 13;
        $transactionPay['branch_id'] = $request->branch_id ?? 0;
        $transactionPay['debit'] =  $request->amount;
        $transactionPay['remark'] = $request->narration ?? "";
        $transactionPay['created_by'] = Auth::id();
        AccountTransaction::create($transactionPay);

        $transaction['payment_invoice'] = $request->invoice_no;
        $transaction['invoice'] = $invoice;
        $transaction['table_id'] = $productUnit->id;
        $transaction['account_id'] = $request->payment_account;
        $transaction['type'] = 13;
        $transaction['branch_id'] = $request->branch_id ?? 0;
        $transaction['credit'] = $request->amount;
        $transaction['remark'] = $request->narration ?? "";
        $transaction['created_by'] = Auth::id();
        AccountTransaction::create($transaction);
        return $productUnit;
    }

    public function statusUpdate($id, $status)
    {
        $productUnit = $this->assetsList::find($id);
        $productUnit->status = $status;
        $productUnit->save();
        return $productUnit;
    }

    public function destroy($id)
    {
        $productUnit = $this->assetsList::find($id);
        $productUnit->delete();
        return true;
    }
}
