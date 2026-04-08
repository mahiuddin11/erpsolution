@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }
    </style>
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Report </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('project.project.index'))
                            <li class="breadcrumb-item"><a href="{{ route('project.project.index') }}">Project</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Project List</span></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Stock Summary</h3>
                    <a onclick="window.print()" target="_blank" class="btn btn-default float-right my-2 no-print"><i
                            class="fas fa-print"></i>
                        Print</a>

                    <form action="{{ route('inventorySetup.currentStock.index') }}" method="post">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <label for="categorysubmit">Search By Category</label>
                                <select name="category_id" id="categorysubmit" class="form-control select2">
                                    <option selected value="all">All Category</option>
                                    @foreach ($categorys as $category)
                                        <option {{ $request->category_id == $category->id ? 'selected' : '' }}
                                            value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">

                    <div class="invoice p-3 mb-3">
                        <!-- Table row -->
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Product Code</th>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th>Branch</th>
                                            <th>Type</th>
                                            <th>WearHouse Name</th>
                                            <th class="text-right">Qty</th>
                                            @if (auth()->user()->type == 'Admin')
                                                <th class="text-right">Avg Unit Price</th>
                                                <th class="text-right">Total</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                            $totalQty = 0;
                                            $totalPrice = 0; 
                                            $totalUnitPrice = 0; 
                                        @endphp

                                        @foreach ($currentSrock as $item)
                                            @if ($item->stock_qty > 0 && $item->type != 'Project')
                                                @php
                                                    // Avg price calculation (per product)
                                                    $purchasesPrices = App\Models\PurchasesDetails::where(
                                                        'product_id',
                                                        $item->product_id,
                                                    )->pluck('unit_price');

                                                    $openingStockPrices = App\Models\ProductOpeningStockDetails::where(
                                                        'product_id',
                                                        $item->product_id,
                                                    )->pluck('unit_price');

                                                    $allPrices = $purchasesPrices->merge($openingStockPrices);

                                                    $avgPrice = $allPrices->avg() ?? 0; // null হলে 0

                                                    $totalUnitPrice += round($avgPrice);
                                                    $totalQty += $item->stock_qty;
                                                    $totalPrice += round($avgPrice * $item->stock_qty, 2);
                                                @endphp

                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ optional($item->products)->getRawOriginal('productCode') }}</td>
                                                    <td>{{ optional($item->products)->getRawOriginal('name') }}
                                                        {{ $item->products->brand->name ?? '' }}</td>
                                                    <td>{{ optional(optional($item->products)->category)->name ?? 'N/A' }}
                                                    </td>
                                                    <td>{{ optional($item->branch)->branchCode . ' - ' . optional($item->branch)->name ?? 'N/A' }}
                                                    </td>
                                                    <td align="right">{{ $item->purchasetype ?? '-' }}</td>
                                                    <td align="right">{{ optional($item->branch)->name ?? 'N/A' }}</td>
                                                    
                                                    <td align="right">{{ $item->stock_qty }}</td>

                                                    @if (auth()->user()->type == 'Admin')
                                                        <td align="right">{{ number_format($avgPrice, 2) }}</td>
                                                        <td align="right">
                                                            {{ number_format($avgPrice * $item->stock_qty, 2) }}</td>
                                                    @endif
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="7" style="text-align: right">Total</th>
                                            <th style="text-align: right">{{ $totalQty }}</th>

                                            @if (auth()->user()->type == 'Admin')
                                                <th style="text-align: right">{{ number_format($totalUnitPrice,0)}}</th>
                                                
                                                <th style="text-align: right">{{ number_format($totalPrice, 0) }}</th>
                                            @endif
                                        </tr>
                                    </tfoot>


                                </table>
                            </div>


                            <div class="col-4  float-left">
                                <br>
                                <br>

                                <p>Prepared By:_____________<br />
                                    Date:____________________
                                </p>
                            </div>
                            <div class="col-6 text-center">
                            </div>
                            <div class="col-2  ">
                                <br>
                                <br>
                                <p>Approved By:________________<br />
                                    Date:_________________</p>
                            </div>

                            <hr>


                            <div class="col-md-12 bg-success" style="text-align: center">
                                Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} Company products.
                                We believe you will be satisfied by our services.
                            </div>
                            <!-- /.col -->



                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- /.col-->
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#categorysubmit').on('change', function() {
                let form = $(this).closest('form');
                form.submit();
            })
        });
    </script>
@endsection
