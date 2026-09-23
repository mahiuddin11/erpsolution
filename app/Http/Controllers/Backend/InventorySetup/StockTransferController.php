<?php

namespace App\Http\Controllers\Backend\InventorySetup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\Navigation;
use App\Models\StockSummary;
use App\Models\Company;
use App\Models\PurchasesDetails;
use App\Models\StockTransfer;
use App\Models\Transfer;
use App\Models\TransferDetails;

use App\Services\InventorySetup\StockTransferService;
use App\Transformers\StockTransferTransformer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\ValifdationException;
use Illuminate\Support\Facades\Validator;

class StockTransferController extends Controller
{

    /**
     * @var StockTransferService
     */
    private $systemService;

    /**
     * @var StockTransferTransformer
     */
    private $systemTransformer;

    /**
     * StockTransferController constructor.
     * @param StockTransferService $systemService
     * @param StockTransferService $systemTransformer
     */
    public function __construct(StockTransferService $saleService, StockTransferTransformer $saleTransformer)
    {
        $this->systemService = $saleService;
        $this->systemTransformer = $saleTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $title = 'Transfer List';
        return view('backend.pages.inventories.transfer.index', get_defined_vars());
    }

    public function dataProcessingTransfer(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    // public function create()
    // {
    //     $title = 'Add New Transfer';
    //     $tobranch = Branch::get()->where('status', 'Active');
    //     $user = auth()->user();
    //     $branch = Branch::where('status', 'Active');
    //     if ($user->branch_id !== null) {
    //         $branch = $branch->where('id', $user->branch_id);
    //     }
    //     $branch = $branch->get();
    //     $category_info = Category::get()->where('status', 'Active');

    //     $stockTransferData = Transfer::latest('id')->first();
    //     if ($stockTransferData) :
    //         $stockTransfer = $stockTransferData->id + 1;
    //     else :
    //         $stockTransfer = 1;
    //     endif;
    //     $invoice_no = 'TV' . str_pad($stockTransfer, 5, "0", STR_PAD_LEFT);
    //     return view('backend.pages.inventories.transfer.create', get_defined_vars());
    // }

    // public function create()
    // {
    //     $title = 'Add New Transfer';
    //     $user = auth()->user();

    //     $category_info = Category::where('status', 'Active')->get();
    //     $branchQuery = Branch::where('status', 'Active');
    //     if ($user->branch_id !== null) {
    //         $branchQuery = $branchQuery->where('id', $user->branch_id);
    //     }
    //     $branches = $branchQuery->orderBy('parent_id')->orderBy('name')->get();

    //     $toBranches = Branch::where('status', 'Active')
    //         ->orderBy('parent_id')
    //         ->orderBy('name')
    //         ->get();


    //     $formattedBranches = $branches->map(function ($branch) use ($branches) {
    //         $displayName = $branch->branchCode . ' - ' . $branch->name;

    //         if (!empty($branch->parent_id) && $branch->parent_id > 0) {
    //             $parent = $branches->where('id', $branch->parent_id)->first();
    //             if ($parent) {
    //                 $displayName .= " (" . $parent->name . ")";
    //             }
    //         }
    //         $branch->display_name = $displayName;
    //         return $branch;
    //     });

    //     // Display Name (To Branch)
    //     $formattedToBranches = $toBranches->map(function ($branch) use ($toBranches) {
    //         $displayName = $branch->branchCode . ' - ' . $branch->name;

    //         if (!empty($branch->parent_id) && $branch->parent_id > 0) {
    //             $parent = $toBranches->where('id', $branch->parent_id)->first();
    //             if ($parent) {
    //                 $displayName .= " (" . $parent->name . ")";
    //             }
    //         }
    //         $branch->display_name = $displayName;
    //         return $branch;
    //     });

    //     // Invoice Number
    //     $stockTransferData = Transfer::latest('id')->first();
    //     $stockTransfer = $stockTransferData ? $stockTransferData->id + 1 : 1;
    //     $invoice_no = 'TV' . str_pad($stockTransfer, 5, "0", STR_PAD_LEFT);

    //     return view('backend.pages.inventories.transfer.create', get_defined_vars());
    // }

    public function create()
    {
        $title = 'Add New Transfer';
        $user = auth()->user();

        $category_info = Category::where('status', 'Active')->get();

        $realBranchQuery = Branch::where('status', 'Active')
            ->where(function ($q) {
                $q->whereNull('parent_id')->orWhere('parent_id', 0);
            });

        $branchQuery = clone $realBranchQuery;
        $lockedWarehouseId = null;

        if ($user->branch_id !== null) {
            $userBranch = Branch::find($user->branch_id);

            if ($userBranch && !empty($userBranch->parent_id) && $userBranch->parent_id > 0) {

                $realBranchId = $userBranch->parent_id;
                $lockedWarehouseId = $userBranch->warehouse_id;
            } else {
                $realBranchId = $user->branch_id;
            }

            $branchQuery = $branchQuery->where('id', $realBranchId);
        }

        $branches = $branchQuery->orderBy('name')->get();
        $toBranches = (clone $realBranchQuery)->orderBy('name')->get();


        $formattedBranches = $branches->map(function ($branch) {
            $branch->display_name = $branch->branchCode . ' - ' . $branch->name;
            return $branch;
        });

        $formattedToBranches = $toBranches->map(function ($branch) {
            $branch->display_name = $branch->branchCode . ' - ' . $branch->name;
            return $branch;
        });

        $warehouseRows = DB::table('branches as b')
            ->join('warehouses as w', 'w.id', '=', 'b.warehouse_id')
            ->where('b.status', 'Active')
            ->select(
                'w.id as warehouse_id',
                'w.name as warehouse_name',
                DB::raw('IF(COALESCE(b.parent_id, 0) != 0, b.parent_id, b.id) as real_branch_id')
            )
            ->orderBy('w.name')
            ->get();

        $toWarehousesByBranch = $warehouseRows
            ->groupBy('real_branch_id')
            ->map(function ($rows) {
                return $rows->unique('warehouse_id')
                    ->map(function ($r) {
                        return ['id' => $r->warehouse_id, 'name' => $r->warehouse_name];
                    })
                    ->values();
            });

        $fromWarehousesByBranch = $toWarehousesByBranch;
        if ($lockedWarehouseId) {
            $fromWarehousesByBranch = $toWarehousesByBranch->map(function ($list) use ($lockedWarehouseId) {
                return $list->where('id', $lockedWarehouseId)->values();
            });
        }

        $stockTransferData = Transfer::latest('id')->first();
        $stockTransfer = $stockTransferData ? $stockTransferData->id + 1 : 1;
        $invoice_no = 'TV' . str_pad($stockTransfer, 5, "0", STR_PAD_LEFT);

        return view('backend.pages.inventories.transfer.create', get_defined_vars());
    }

    public function show(Request $request, $id)
    {
        $title = 'Transfer Invoice';
        $invoice = Transfer::with(['details.product.category', 'branch'])->findOrFail($id);
        $companyInfo = Company::latest('id')->first();
        return view('backend.pages.inventories.transfer.invoice', get_defined_vars());
    }

    // public function approval($id)
    // {
    //     if (!is_numeric($id)) {
    //         session()->flash('error', 'Edit id must be numeric!!');
    //         return redirect()->back();
    //     }
    //     $editInfo = $this->systemService->details($id);
    //     if (!$editInfo) {
    //         session()->flash('error', 'Edit info is invalid!!');
    //         return redirect()->back();
    //     }
    //     $transfer = $this->systemService->getAllList();
    //     $category_info = Category::get()->where('status', 'Active');
    //     $branch = Branch::get()->where('status', 'Active');
    //     $title = 'Approved Edit';
    //     return view('backend.pages.inventories.transfer.approval', get_defined_vars());
    // }
    public function approval($id)
    {
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }
        $editInfo = $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        $transfer = $this->systemService->getAllList();
        $category_info = Category::get()->where('status', 'Active');
        $branch = Branch::get()->where('status', 'Active');
        $title = 'Approved Edit';


        $warehouseIds = array_filter([$editInfo->from_warehouse_id, $editInfo->to_warehouse_id]);
        $warehouseNames = empty($warehouseIds)
            ? collect()
            : DB::table('warehouses')->whereIn('id', $warehouseIds)->pluck('name', 'id');


        return view('backend.pages.inventories.transfer.approval', get_defined_vars());
    }


    public function getProductListTransfer(Request $request)
    {
        // dd($request->all());
        $cat_id = $request->cat_id;
        $productList = Product::get()->where('category_id', $cat_id);

        $add = '';
        if (!empty($productList)) :
            $add .= "<option value=''>Select Product</option>";

            foreach ($productList as $key => $value) :
                $stocksummerylst = StockSummary::where('branch_id', $request->branch_id)->where('product_id', $value->id)->first();
                // dd($stocksummerylst);
                if (!empty($stocksummerylst) && $stocksummerylst->quantity > 0) {

                    $add .= "<option proName='" . $value->name . "'   value='" . $value->id . "'>$value->productCode - $value->name</option>";
                }
            endforeach;
            echo $add;
            die;
        else :
            echo "<option value='' selected disabled>No Product Available</option>";
            die;
        endif;
    }

    public function approval_store(Request $request)
    {

        if ($request->approvalstatus == 'Approved') {
            session()->flash('error', ' Already Approved!!');
            return redirect()->route('inventorySetup.transfer.index');
        }

        try {
            $this->validate($request, $this->systemService->storeValidation_approval($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->approval($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('inventorySetup.transfer.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, $this->systemService->storeValidation($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->store($request);
        session()->flash('success', 'Data successfully save!!');
        return redirect()->route('inventorySetup.transfer.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function edit($id)
    // {
    //     if (!is_numeric($id)) {
    //         session()->flash('error', 'Edit id must be numeric!!');
    //         return redirect()->back();
    //     }
    //     $editInfo = $this->systemService->details($id);
    //     if (!$editInfo) {
    //         session()->flash('error', 'Edit info is invalid!!');
    //         return redirect()->back();
    //     }
    //     $transfer = $this->systemService->getAllList();

    //     $title = 'Approved Edit';
    //     $tobranch = Branch::get()->where('status', 'Active');
    //     $user = auth()->user();
    //     $branch = Branch::where('status', 'Active');
    //     if ($user->branch_id !== null) {
    //         $branch = $branch->where('id', $user->branch_id);
    //     }
    //     $branch = $branch->get();
    //     $category_info = Category::get()->where('status', 'Active');
    //     $transfe = Transfer::find($id);
    //     $transfeDetails = TransferDetails::where('transfer_id', $id)->get();
    //     return view('backend.pages.inventories.transfer.edit', get_defined_vars());
    // }

    public function edit($id)
    {
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }

        $editInfo = $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }

        $transfe = Transfer::find($id);
        if (!$transfe) {
            session()->flash('error', 'Transfer not found!!');
            return redirect()->back();
        }

        $title = 'Approved Edit';
        $user = auth()->user();

        $category_info = Category::where('status', 'Active')->get();
        $transfeDetails = TransferDetails::where('transfer_id', $id)->get();

      
        $realBranchQuery = Branch::where('status', 'Active')
            ->where(function ($q) {
                $q->whereNull('parent_id')->orWhere('parent_id', 0);
            });

        $branchQuery = clone $realBranchQuery;
        $lockedWarehouseId = null;

        if ($user->branch_id !== null) {
            $userBranch = Branch::find($user->branch_id);

            if ($userBranch && !empty($userBranch->parent_id) && $userBranch->parent_id > 0) {
                $realBranchId = $userBranch->parent_id;
                $lockedWarehouseId = $userBranch->warehouse_id;
            } else {
                $realBranchId = $user->branch_id;
            }

            $branchQuery = $branchQuery->where('id', $realBranchId);
        }

        $branches = $branchQuery->orderBy('name')->get();
        $toBranches = (clone $realBranchQuery)->orderBy('name')->get();

        $formattedBranches = $branches->map(function ($branch) {
            $branch->display_name = $branch->branchCode . ' - ' . $branch->name;
            return $branch;
        });

        $formattedToBranches = $toBranches->map(function ($branch) {
            $branch->display_name = $branch->branchCode . ' - ' . $branch->name;
            return $branch;
        });

        // ---------- branch-wise warehouse map ----------
        $warehouseRows = DB::table('branches as b')
            ->join('warehouses as w', 'w.id', '=', 'b.warehouse_id')
            ->where('b.status', 'Active')
            ->select(
                'w.id as warehouse_id',
                'w.name as warehouse_name',
                DB::raw('IF(COALESCE(b.parent_id, 0) != 0, b.parent_id, b.id) as real_branch_id')
            )
            ->orderBy('w.name')
            ->get();

        $toWarehousesByBranch = $warehouseRows
            ->groupBy('real_branch_id')
            ->map(function ($rows) {
                return $rows->unique('warehouse_id')
                    ->map(function ($r) {
                        return ['id' => $r->warehouse_id, 'name' => $r->warehouse_name];
                    })
                    ->values();
            });

        $fromWarehousesByBranch = $toWarehousesByBranch;
        if ($lockedWarehouseId) {
            $fromWarehousesByBranch = $toWarehousesByBranch->map(function ($list) use ($lockedWarehouseId) {
                return $list->where('id', $lockedWarehouseId)->values();
            });
        }

        return view('backend.pages.inventories.transfer.edit', get_defined_vars());
    }

    public function editapproval($id)
    {
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }
        $editInfo = $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        $transfer = $this->systemService->getAllList();

        $title = 'Approved Edit';
        $branch = Branch::get()->where('status', 'Active');
        $category_info = Category::get()->where('status', 'Active');
        $transfe = Transfer::find($id);
        $transfeDetails = TransferDetails::where('transfer_id', $id)->get();
        return view('backend.pages.inventories.transfer.editapprove', get_defined_vars());
    }
    public function updateapprove(Request $request)
    {
        if ($request->approvalstatus == 'Approved') {
            session()->flash('error', ' Already Approved!!');
            return redirect()->route('inventorySetup.transfer.index');
        }
        try {
            $this->validate($request, $this->systemService->transferApprove($request));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->stotransferStore($request);

        return redirect()->route('inventorySetup.transfer.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }
        $editInfo = $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        try {
            $this->validate($request, $this->systemService->updateValidation($request, $id));
        } catch (ValidationException $e) {
            session()->flash('error', 'Validation error !!');
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        $this->systemService->update($request, $id);
        session()->flash('success', 'Data successfully updated!!');
        return redirect()->route('inventorySetup.transfer.index');
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function statusUpdate($id, $status)
    {
        if (!is_numeric($id)) {
            return response()->json($this->systemTransformer->invalidId($id), 200);
        }
        $detailsInfo = $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $statusInfo = $this->systemService->statusUpdate($id, $status);
        if ($statusInfo) {
            return response()->json($this->systemTransformer->statusUpdate($statusInfo), 200);
        }
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            return response()->json($this->systemTransformer->invalidId($id), 200);
        }
        $detailsInfo = $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $deleteInfo = $this->systemService->destroy($id);
        if ($deleteInfo) {
            return response()->json($this->systemTransformer->delete($deleteInfo), 200);
        }
    }

    public function getProductListForSale(Request $request)
    {
        $cat_id = $request->cat_id;
        $productList = Product::get()->where('category_id', $cat_id);
        //   dd($productList);
        $add = '';
        if (!empty($productList)) :
            $add .= "<option value=''>Select Product</option>";
            foreach ($productList as $key => $value) :
                $add .= "<option proName='" . $value->name . "'   value='" . $value->id . "'>$value->productCode - $value->name</option>";
            endforeach;
            echo $add;
            die;
        else :
            echo "<option value='' selected disabled>No Product Available</option>";
            die;
        endif;
    }

    // public function unitPiceForSale(Request $request)
    // {
    //     $proid = $request->productId;
    //     $productPrice = Product::where('id', $proid)->first();
    //     echo json_encode(array('purchases_price' => $productPrice->purchases_price, 'sale_price' => $productPrice->sale_price));
    // }

    public function unitPiceForSale(Request $request)
    {
        $proid = $request->productId;
        $branchId = $request->branch_id;
        $purchaseType = $request->purchase_type; // Added: 2026-08-27
        $product = Product::where('id', $proid)->first();
        $purchasesPriceQuery = PurchasesDetails::where('product_id', $proid);

        if (!empty($branchId)) {
            $purchasesPriceQuery->where('branch_id', $branchId);
        }
        if (!empty($purchaseType)) {
            $purchasesPriceQuery->where('purchasetype', $purchaseType);
        }

        $avgPurchasePrice = $purchasesPriceQuery->avg('unit_price');

        $purchasesPrice = $avgPurchasePrice ?: ($product->purchases_price ?? 0);

        echo json_encode([
            'purchases_price' => round($purchasesPrice, 2),
            'sale_price'       => $product->sale_price ?? 0,
        ]);
    }

    // function getProductStock(Request $request)
    // {

    //     $productStock = StockSummary::where('product_id',  $request->productId)->where('branch_id', $request->branch_id)->first();
    //     if (!empty($productStock->quantity) && $productStock->quantity > 0) :
    //         echo $productStock->quantity;
    //     endif;
    // }

    function getProductStock(Request $request)
    {
        $query = StockSummary::where('product_id', $request->productId)
            ->where('branch_id', $request->branch_id);
        if (!empty($request->purchase_type)) {
            $query->where('purchasetype', $request->purchase_type);
        }
        $productStock = $query->first();
        if (!empty($productStock->quantity) && $productStock->quantity > 0) {
            echo $productStock->quantity;
        } else {
            echo 0;
        }
    }
}
