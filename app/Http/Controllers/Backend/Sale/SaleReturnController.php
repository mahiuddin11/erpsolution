<?php

namespace App\Http\Controllers\Backend\Sale;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Employee;
use App\Models\Sale;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class SaleReturnController extends Controller
{
    //

    public function index()
    {
        $title = 'Sale Return';
        return view('backend.pages.sale.return.return', get_defined_vars());
    }


    // public function dataProcessingSale(Request $request)
    // {
    //     $json_data = $this->systemService->getList($request);
    //     return json_encode($this->systemTransformer->dataTable($json_data));
    // }

    public function create()
    {
        $title = 'Add New sale';

        $category_info = Category::where('status', 'Active')->get();
        $customer = Customer::where('status', 'Active')->get();

        $ledgers = ChartOfAccount::whereIn('id', [getAccountByUniqueID(5)->id, getAccountByUniqueID(16)->id])
            ->with([
                'parent',
                'subAccount.parent',
                'subAccount.subAccount.parent',
                'subAccount.subAccount.subAccount.parent',
            ])
            ->get();

        $user = auth()->user();
        $branch = Branch::where("parent_id", 0)->where('status', 'Active');
        if ($user->branch_id) {
            $branch = $branch->where('id', $user->branch_id);
        }
        $branch = $branch->get();
        $customerGroup = CustomerGroup::all();

        $usingNewWarehouseTable = false;
        $wearhouses = Warehouse::where('status', 'Active')->get();

        if ($wearhouses->isNotEmpty()) {
            $usingNewWarehouseTable = true;
            $wearhouses = $wearhouses->map(function ($w) {
                return (object) [
                    'id'            => $w->id,
                    'warehouseCode' => $w->warehouseCode ?? '',
                    'name'          => $w->name,
                ];
            });
        } else {
            $wearhouses = Branch::where("parent_id", "!=", 0)->where('status', 'Active')->get();
        }

        $accountEagerLoad = [
            'parent',
            'subAccount.parent',
            'subAccount.subAccount.parent',
            'subAccount.subAccount.subAccount.parent',
        ];

        if ($user->type == "Admin" || $user->branch_id) {
            $account = ChartOfAccount::whereIn('id', [16, 17])->where('status', 'Active')->with($accountEagerLoad)->get();
        } elseif ($user->type == "Admin" || !$user->branch_id) {
            $account = ChartOfAccount::whereIn('id', [16, 17])->where('status', 'Active')->where('branch_id', $user->branch_id)->with($accountEagerLoad)->get();
        }

        $saleLastData = Sale::latest('id')->first();
        if ($saleLastData) :
            $saleData = $saleLastData->id + 1;
        else :
            $saleData = 1;
        endif;
        $employees = Employee::where('status', 'Active')
            ->where('employee_status', 'present')
            ->select('id', 'name', 'id_card')
            ->get();

        $invoice_no = 'SVR' . str_pad($saleData, 5, "0", STR_PAD_LEFT);

        return view('backend.pages.sale.return.create', get_defined_vars());
    }
}
