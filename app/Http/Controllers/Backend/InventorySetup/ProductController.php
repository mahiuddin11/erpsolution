<?php

namespace App\Http\Controllers\Backend\InventorySetup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductUnit;
use App\Models\Product;
use App\Models\Navigation;
use App\Models\Supplier;
use App\Services\InventorySetup\ProductService;
use App\Services\InventorySetup\CategoryService;
use App\Services\InventorySetup\BrandService;
use App\Services\InventorySetup\UnitService;
use App\Transformers\ProductTransformer;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * @var CategoryService
     */
    private $categoryService;
    /**
     * @var BrandService
     */
    private $brandService;
    /**
     * @var UnitService
     */
    private $unitService;
    /**
     * @var ProductService
     */
    private $systemService;
    /**
     * @var ProductTransformer
     */
    private $systemTransformer;

    /**
     * CategoryController constructor.
     * @param ProductService $systemService
     * @param ProductService $systemTransformer
     */
    public function __construct(BrandService $brandService, UnitService $unitService, CategoryService $categoryService, ProductService $productService, ProductTransformer $productTransformer)
    {
        $this->categoryService = $categoryService;
        $this->brandService = $brandService;
        $this->unitService = $unitService;
        $this->systemService = $productService;
        $this->systemTransformer = $productTransformer;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Product List';
        $categorys = Category::get();
        return view('backend.pages.inventories.product.index', get_defined_vars());
    }


    public function dataProcessingProduct(Request $request)
    {
        $json_data = $this->systemService->getList($request);
        return json_encode($this->systemTransformer->dataTable($json_data));
    }

    public function quickCategoryStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable',
        ]);
    
        $category = new Category();
        $category->name = $request->name;
        $category->parent_id = $request->parent_id;
        $category->save();
    
        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }

    public function quickBrandStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->save();
    
        return response()->json([
            'success' => true,
            'brand' => $brand,
        ]);
    }


    public function quickUnitStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $unit = new ProductUnit();
        $unit->name = $request->name;
        $unit->save();
    
        return response()->json([
            'success' => true,
            'unit' => $unit,
        ]);
    }
    

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Add New Product';
        $categorys = $this->categoryService->getAllList();
        $brands = $this->brandService->getAllList();
        $units = $this->unitService->getAllList();
        $productLastData = Product::latest('id')->first();
        $category = Category::all();
        if ($productLastData) :
            $productData = $productLastData->id + 1;
        else :
            $productData = 1;
        endif;
        
        $productCode = 'PR' . str_pad($productData, 5, "0", STR_PAD_LEFT);
        $suppliers = Supplier::get();
        return view('backend.pages.inventories.product.create', get_defined_vars());
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
        return redirect()->route('inventorySetup.product.index');
    }
    /**
     * 
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        if (!is_numeric($id)) {
            session()->flash('error', 'Edit id must be numeric!!');
            return redirect()->back();
        }
        $editInfo =   $this->systemService->details($id);
        if (!$editInfo) {
            session()->flash('error', 'Edit info is invalid!!');
            return redirect()->back();
        }
        $title = 'Add New Product';
        $categorys = $this->categoryService->getAllList();
        $category = Category::all();
        $brands = $this->brandService->getAllList();
        $units = $this->unitService->getAllList();
        $suppliers = Supplier::get();
        return view('backend.pages.inventories.product.edit', get_defined_vars());
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
        return redirect()->route('inventorySetup.product.index');
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
        $detailsInfo =   $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $statusInfo =  $this->systemService->statusUpdate($id, $status);
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
        $detailsInfo =   $this->systemService->details($id);
        if (!$detailsInfo) {
            return response()->json($this->systemTransformer->notFound($detailsInfo), 200);
        }
        $deleteInfo =  $this->systemService->destroy($id);
        if ($deleteInfo) {
            return response()->json($this->systemTransformer->delete($deleteInfo), 200);
        }
    }
}
