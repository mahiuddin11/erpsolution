<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use App\Models\Accounts;
use Illuminate\Support\Facades\Auth;
use App\Models\Supplier;
use App\Models\Branch;
use phpDocumentor\Reflection\PseudoTypes\False_;

class SupplierRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var supplier
     */
    private $supplier;
    /**
     * CourseRepository constructor.
     * @param supplier $supplier
     */
    public function __construct(supplier $supplier)
    {
        $this->supplier = $supplier;
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
        $result = $this->supplier::latest()->get();
        // dd($result);
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
            1 => 'phone',
            1 => 'specialNumber',
        );

        $edit = Helper::roleAccess('inventorySetup.supplier.edit') ? 1 : 0;
        $delete = Helper::roleAccess('inventorySetup.supplier.destroy') ? 1 : 0;
        $view = Helper::roleAccess('inventorySetup.supplier.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->supplier::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $suppliers = $this->supplier::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->supplier::count();
        } else {
            $search = $request->input('search.value');
            $suppliers = $this->supplier::where('name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->supplier::where('name', 'like', "%{$search}%")->count();
        }



        $data = array();
        //dd($suppliers);
        if ($suppliers) {
            foreach ($suppliers as $key => $supplier) {
                $nestedData['id'] = $key + 1;
                $nestedData['name'] = $supplier->name;
                // $nestedData['branch_id'] = $supplier->getBranch->name;
                $nestedData['supplierCode'] = $supplier->supplierCode;
                $nestedData['email'] = $supplier->email;
                $nestedData['phone'] = $supplier->phone;
                $nestedData['specialNumber'] = $supplier->specialNumber;
                $nestedData['address'] = $supplier->address;
                if ($supplier->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('inventorySetup.supplier.status', [$supplier->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('inventorySetup.supplier.status', [$supplier->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('inventorySetup.supplier.edit', $supplier->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('inventorySetup.supplier.show', $supplier->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('inventorySetup.supplier.destroy', $supplier->id) . '" delete_id="' . $supplier->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $supplier->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->supplier::find($id);
        return $result;
    }

    public function store($request)
    {
        // dd($request->all());
        $supplier = new $this->supplier();
        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;
        $supplier->supplierCode = $request->supplierCode;
        $supplier->specialNumber = $request->specialNumber;
        // $supplier->branch_id = $request->branch_id;
        $supplier->status = 'Active';
        $supplier->created_by = Auth::user()->id;
        $supplier->save();

        $Accounts = new Accounts();
        $Accounts->account_name = $request->name;
        $Accounts->parent_id = 16;
        $Accounts->accountable_id = $supplier->id;
        $Accounts->accountable_type = "App\Models\Supplier";
        $Accounts->bill_by_bill = 1;
        $Accounts->status = 'Active';
        $Accounts->created_by = Auth::user()->id;
        $Accounts->save();
        return $supplier;
    }

    public function update($request, $id)
    {
        //  dd($request->all());
        $supplier = $this->supplier::findOrFail($id);
        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->specialNumber = $request->specialNumber;
        $supplier->address = $request->address;
        // $supplier->supplierCode = $request->supplierCode;
        $supplier->status = 'Active';
        $supplier->updated_by = Auth::user()->id;
        $supplier->save();

        // if ($supplier->account) {
        //     $Accounts = $supplier->account;
        //     $Accounts->account_name = $request->name;
        //     $Accounts->parent_id = 16;
        //     $Accounts->accountable_id = $supplier->id;
        //     $Accounts->accountable_type = "App\Models\Supplier";
        //     $Accounts->bill_by_bill = 1;
        //     $Accounts->status = 'Active';
        //     $Accounts->created_by = Auth::user()->id;
        //     $Accounts->save();
        // }

        return $supplier;
    }

    public function statusUpdate($id, $status)
    {
        $supplier = $this->supplier::find($id);
        $supplier->status = $status;
        $supplier->save();
        return $supplier;
    }

    public function destroy($id)
    {
        $supplier = $this->supplier::find($id);
        $supplier->delete();
        return true;
    }
}



