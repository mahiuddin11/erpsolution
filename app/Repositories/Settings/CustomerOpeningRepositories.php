<?php

namespace App\Repositories\Settings;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerOpening;
use App\Models\Accounts;
use App\Models\Customer;
use App\Models\Transection;
use phpDocumentor\Reflection\PseudoTypes\False_;

class CustomerOpeningRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var Opening
     */
   private $Transection;
    /**
     * CourseRepository constructor.
     * @param opening $customerOpening
     */
    public function __construct(Transection $Transection)
    {
        $this->Transection = $Transection;
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
        return  $this->Customer::get();
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
            2 => 'phone',
            3 => 'email',
        );


        $edit = Helper::roleAccess('settings.customerOpening.edit') ? 1 : 0;
        $delete = Helper::roleAccess('settings.customerOpening.destroy') ? 1 : 0;
        $view = Helper::roleAccess('settings.customerOpening.show') ? 1 : 0;
        $ced = $edit + $delete + $view;



        $totalData = $this->Transection::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $customerOpenings = $this->Transection::offset($start)
                ->limit($limit)
                ->where('type', 5)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->Transection::count();
        } else {
            $search = $request->input('search.value');
            $customerOpenings = $this->Transection::where('account_id', 'like', "%{$search}%")->orWhere('branch_id','like',"%{$search}%")->orWhere('amount', 'like', "%{$search}%")->orWhere('date', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
               ->where('type', 5)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->Transection::where('account_id', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($customerOpenings) {
            foreach ($customerOpenings as $key => $customerOpening) {
                $nestedData['id'] = $key + 1;
                $nestedData['account_id'] = $customerOpening->account_id;
                $nestedData['branch_id'] = $customerOpening->branch_id;
                $nestedData['date'] = $customerOpening->date;
                $nestedData['amount'] = $customerOpening->amount;
                $nestedData['note'] = $customerOpening->note;
            

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('settings.customerOpening.edit', $customerOpening->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view =! 0)
                        $view_data = '<a href="' . route('settings.customerOpening.show', $customerOpening->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('settings.customerOpening.destroy', $customerOpening->id) . '" delete_id="' . $customerOpening->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $customerOpening->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->opening::find($id);
        return $result;
    }

    public function store($request)
    {
        
     //   dd($request->all());
        
        $transection = new transection();
        $transection->account_id = $request->customer_id;
        $transection->branch_id = $request->branch_id;
        $transection->debit = $request->amount;
        $transection->amount = $request->amount;
        $transection->note = $request->note;
        $transection->date = $request->date;
        $transection->type = 5;
        $transection->created_by = Auth::user()->id;
        $transection->save();
        
        return $customerOpening;
    }

    public function update($request, $id)
    {
        $customerOpening = $this->opening::findOrFail($id);
        $customerOpening->name = $request->name;
        $customerOpening->email = $request->email;
        $customerOpening->phone = $request->phone;
        $customerOpening->address = $request->address;
        $customerOpening->status = 'Active';
        $customerOpening->updated_by = Auth::user()->id;
        $customerOpening->save();
        return $customerOpening;
    }

    public function statusUpdate($id, $status)
    {
        $customerOpening = $this->opening::find($id);
        $customerOpening->status = $status;
        $customerOpening->save();
        return $customerOpening;
    }

    public function destroy($id)
    {
        $customerOpening = $this->opening::find($id);
        $customerOpening->delete();
        return true;
    }
}