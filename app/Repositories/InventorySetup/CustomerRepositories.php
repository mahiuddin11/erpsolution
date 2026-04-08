<?php

namespace App\Repositories\InventorySetup;

use App\Helpers\Helper;
use App\Models\Accounts;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\PseudoTypes\False_;

class CustomerRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var customer
     */
    private $customer;
    /**
     * CourseRepository constructor.
     * @param customer $customer
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
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
        $result = $this->customer::latest()->get();
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
        );

        $edit = Helper::roleAccess('inventorySetup.customer.edit') ? 1 : 0;
        $delete = Helper::roleAccess('inventorySetup.customer.destroy') ? 1 : 0;
        $view = Helper::roleAccess('inventorySetup.customer.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->customer::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $customers = $this->customer::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->customer::count();
        } else {
            $search = $request->input('search.value');
            $customers = $this->customer::where('name', 'like', "%{$search}%")->orWhere('co_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->customer::where('name', 'like', "%{$search}%")->count();
        }

        $data = array();
        //dd($customers);
        if ($customers) {
            foreach ($customers as $key => $customer) {
                $nestedData['id'] = $key + 1;
                $nestedData['name'] = $customer->name;
                $nestedData['co_name'] = $customer->co_name;
                $nestedData['customergroup_id'] = $customer->customerGroup->name ?? "N/A";
                // $nestedData['branch_id'] = $customer->branch->name;
                $nestedData['customerCode'] = $customer->customerCode;
                $nestedData['email'] = $customer->email;
                $nestedData['phone'] = $customer->phone;
                $nestedData['address'] = $customer->address;
                $nestedData['bin'] = $customer->bin;
                if ($customer->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('inventorySetup.customer.status', [$customer->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('inventorySetup.customer.status', [$customer->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;
                $nestedData['status'] = $status;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('inventorySetup.customer.edit', $customer->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('inventorySetup.customer.show', $customer->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('inventorySetup.customer.destroy', $customer->id) . '" delete_id="' . $customer->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $customer->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->customer::find($id);
        return $result;
    }

    public function store($request)
    {
        // dd('repositories', $request->all());
        try {
            \DB::beginTransaction();
            //code...
            $customer = new $this->customer();
            $customer->customergroup_id = $request->customergroup_id;
            $customer->name = $request->name;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->address = $request->address;
            $customer->bin = $request->bin;
            $customer->co_name = $request->co_name;
            $customer->customerCode = $request->customerCode;
            $customer->status = 'Active';
            $customer->created_by = Auth::user()->id;
            $customer->save();

            $Accounts = new Accounts();
            $Accounts->account_name = $request->co_name;
            $Accounts->parent_id = 5;
            $Accounts->accountable_id = $customer->id;
            $Accounts->accountable_type = "App\Models\Customer";
            $Accounts->bill_by_bill = 1;
            $Accounts->status = 'Active';
            $Accounts->created_by = Auth::user()->id;
            $Accounts->save();
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            dd($th->getMessage());
            return $th->getMessage();
        }
       
        return $customer;
    }

    public function update($request, $id)
    {
        try {
            \DB::beginTransaction();
            //code...
            $customer = $this->customer::findOrFail($id);
            $customer->customergroup_id = $request->customergroup_id;
            $customer->name = $request->name;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->bin = $request->bin;
            $customer->address = $request->address;
            // $customer->branch_id = $request->branch_id;
            // $customer->customerCode = $request->customerCode;
            $customer->co_name = $request->co_name;
            $customer->status = 'Active';
            $customer->updated_by = Auth::user()->id;
            $customer->save();
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            return $th->getMessage();
        }
        return $customer;
    }

    public function statusUpdate($id, $status)
    {
        $customer = $this->customer::find($id);
        $customer->status = $status;
        $customer->save();
        return $customer;
    }

    public function destroy($id)
    {
        $customer = $this->customer::find($id);
        $customer->delete();
        return true;
    }
}
