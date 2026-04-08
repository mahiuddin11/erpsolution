<?php

namespace App\Repositories\Project;

use App\Helpers\Helper;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Transection;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceRepositories
{

    /**
     * @var user_id
     */
    private $user_id;

    /**
     * @var Invoice
     */
    private $Invoice;

    /**
     * CourseRepository constructor.
     * @param Invoice $eInvoice
     */
    public function __construct(Invoice $Invoices)
    {
        $this->Invoice = $Invoices;
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
        $result = $this->Invoice::latest()->get();
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
            1 => 'InvoiceCode',
        );

        $edit = Helper::roleAccess('project.invoiceCreate.edit') ? 1 : 0;
        $delete = Helper::roleAccess('project.invoiceCreate.destroy') ? 1 : 0;
        $view = Helper::roleAccess('project.invoiceCreate.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->Invoice::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $Invoice = $this->Invoice::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->Invoice::count();
        } else {
            $search = $request->input('search.value');
            $Invoice = $this->Invoice::where('invoiceCode', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $this->Invoice::where('invoiceCode', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($Invoice) {
            foreach ($Invoice as $key => $eInvoice) {
                $nestedData['id'] = $key + 1;
                $nestedData['date'] = $eInvoice->date;
                $nestedData['invoiceCode'] = $eInvoice->invoiceCode;
                $nestedData['branch_id'] = $eInvoice->branch_id ?  $eInvoice->branch->branchCode . '-' . $eInvoice->branch->name : "";
                $nestedData['project_id'] = $eInvoice->project_id ? $eInvoice->project->projectCode . '-' . $eInvoice->project->name : "";
                $nestedData['customer_id'] = $eInvoice->customer_id ?  $eInvoice->customer->customerCode . '-' . $eInvoice->customer->name : "";
                $nestedData['note'] = $eInvoice->note;
                $nestedData['profit'] = $eInvoice->profit;
                $nestedData['total_value'] = $eInvoice->total_value;



                if ($eInvoice->status == 'Active') :
                    $status = '<input class="status_row" status_route="' . route('project.invoiceCreate.status', [$eInvoice->id, 'Inactive']) . '"   id="toggle-demo" type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                else :
                    $status = '<input  class="status_row" status_route="' . route('project.invoiceCreate.status', [$eInvoice->id, 'Active']) . '"  id="toggle-demo" type="checkbox" name="my-checkbox"  data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                endif;

                if ($eInvoice->condition == 'Complete') :
                    $nestedData['condition'] = '<button class="btn btn-success btn-sm"> Complete </button>';
                else :
                    $nestedData['condition'] = '<button data-toggle="modal" data-target="#Invoicecompleate" dataId="' . $eInvoice->id . '" class="btn btn-warning btn-sm complateid"> One Going </button>';
                endif;


                $nestedData['status'] = $status;

                if ($ced != 0) :
                    if ($edit != 0) {
                        $edit_data = '<a href="' . route('project.invoiceCreate.edit', $eInvoice->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    } else {
                        $edit_data = '';
                    }

                    if ($view != 0) {
                        $view_data = '<a href="' . route('project.invoiceCreate.show', $eInvoice->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    } else {
                        $view_data = '';
                    }

                    if ($delete != 0) {
                        $delete_data = '<a delete_route="' . route('project.invoiceCreate.destroy', $eInvoice->id) . '" delete_id="' . $eInvoice->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $eInvoice->id . '"><i class="fa fa-times"></i></a>';
                    } else {
                        $delete_data = '';
                    }

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
            "data" => $data,
        );

        return $json_data;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function details($id)
    {
        $result = $this->Invoice::find($id);
        return $result;
    }

    public function store($request)
    {
        //dd($request->all());

        $eInvoice = new $this->Invoice();
        $eInvoice->invoiceCode = $request->invoiceCode;
        $eInvoice->date = $request->date;
        $eInvoice->branch_id = $request->branch_id;
        $eInvoice->project_id = $request->project_id;
        $eInvoice->customer_id = $request->customer_id;
        $eInvoice->account_id = $request->account_id;
        $eInvoice->note = $request->details;
        $eInvoice->status = 'Done';
        $eInvoice->profit = $request->profit;
        $eInvoice->total_value = $request->total_value;
        $eInvoice->created_by = Auth::user()->id;
        $eInvoice->save();
        $payment_id = $eInvoice->id;

        $transection = new transection();
        $transection->account_id = $request->account_id;
        $transection->branch_id = $request->branch_id;
        $transection->debit = $request->total_value;
        $transection->date = $request->date;
        $transection->payment_id = $payment_id;
        $transection->type = 12;
        $transection->user_id = Auth::user()->id;
        $transection->created_by = Auth::user()->id;
        $transection->save();


        return $eInvoice;
    }

    public function completestore($request)
    {


        $eInvoice = $this->Invoice::find($request->Invoiceid);
        $allReturnData = Invoice::where('Invoice_id', $request->Invoiceid)
            ->where('status', 'Pending')
            ->count();

        if ($allReturnData > 0) {
            $statusHub = 1;
            return redirect()->back()->with('error', ' You Have Pending return products request.');
        }

        $eInvoice->condition = 'Complete';
        $eInvoice->closing = $request->close_date;
        $eInvoice->save();
        return $eInvoice;
    }

    public function update($request, $id)
    {
        $eInvoice = Invoice::find($id);
        $eInvoice->invoiceCode = $request->invoiceCode;
        $eInvoice->date = $request->date;
        $eInvoice->branch_id = $request->branch_id;
        $eInvoice->project_id = $request->project_id;
        $eInvoice->customer_id = $request->customer_id;
        $eInvoice->account_id = $request->account_id;
        $eInvoice->note = $request->details;
        $eInvoice->status = 'Done';
        $eInvoice->profit = $request->profit;
        $eInvoice->total_value = $request->total_value;
        $eInvoice->created_by = Auth::user()->id;
        $eInvoice->save();
        $payment_id = $eInvoice->id;

        $transection = Transection::where('payment_id', $payment_id)->first();
        $transection->account_id = $request->account_id;
        $transection->branch_id = $request->branch_id;
        $transection->debit = $request->total_value;
        $transection->date = $request->date;
        $transection->payment_id = $payment_id;
        $transection->type = 12;
        $transection->user_id = Auth::user()->id;
        $transection->created_by = Auth::user()->id;
        $transection->save();

        return $eInvoice;
    }

    public function statusUpdate($id, $status)
    {
        $eInvoice = $this->Invoice::find($id);
        $eInvoice->status = $status;
        $eInvoice->save();
        return $eInvoice;
    }

    public function destroy($id)
    {
        $eInvoice = $this->Invoice::find($id);
        if ($eInvoice->condition == "One Going") {
            $eInvoice->forceDelete();
            return true;
        } else {
            return false;
        }
    }
}
