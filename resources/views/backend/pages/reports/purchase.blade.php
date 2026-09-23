@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }

        {{-- >>> NEW: print/PDF responsive fix --}} @media print {
            @page {
                size: landscape;
                margin: 8mm;
            }

            #datatablexcel {
                font-size: 10px;
                width: 100% !important;
            }

            .table-responsive {
                overflow-x: visible !important;
            }

            .badge {
                border: 1px solid #999;
                color: #000 !important;
                background: none !important;
            }
        }

        {{-- <<< END NEW --}}
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

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">

        <div class="col-md-12">
            <form action="{{ route('report.purchase.purchase') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card card-outline card-info no-print">
                    <div class="card-body">
                        <div class="row  no-print">
                            <div class="box-header with-border" style="cursor: pointer;">
                                <h6 class="box-title">
                                    <i class="fa fa-filter" aria-hidden="true"></i> Filters
                                </h6>
                            </div>
                        </div>

                        <div class="row no-print">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date Range:</label>
                                    <input type="text" class="form-control" value="{{ $request->dateRange }}"
                                        name="dateRange" value="" id="reservation" />
                                    @error('dateRange')
                                        <span class="error text-red text-bold"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Type </label>
                                    <select class="form-control select2" name="type" id="typeSelect">
                                        <option value="all"
                                            {{ !isset($type) || $type == '' || $type == 'all' ? 'selected' : '' }}>
                                            All</option>
                                        <option value="Branch" {{ $type == 'Branch' ? 'selected' : '' }}>Warehouse
                                        </option>
                                        <option value="Project" {{ $type == 'Project' ? 'selected' : '' }}>Project
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2" id="branchSelect">
                                <div class="form-group">
                                    <label>Warehouse </label>
                                    <select class="form-control select2" name="branch_id">
                                        <option value="all" selected>All branches</option>
                                        @foreach ($branch as $key => $value)
                                            <option {{ $branch_id == $value->id ? 'selected' : '' }}
                                                value="{{ $value->id }}">
                                                {{ $value->branchCode . ' - ' . $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <span class="error text-red text-bold"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2" id="projectSelect">
                                <div class="form-group">
                                    <label>Project </label>
                                    <select class="form-control select2" name="project_id">
                                        <option value="all" selected>All Project</option>
                                        @foreach ($projects as $key => $value)
                                            <option {{ $project_id == $value->id ? 'selected' : '' }}
                                                value="{{ $value->id }}">
                                                {{ $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <span class="error text-red text-bold"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Product </label>
                                    <select class="form-control select2 " name="product_id">
                                        <option value="all" selected>All Products</option>
                                        @foreach ($product as $key => $value)
                                            <option {{ $product_id == $value->id ? 'selected' : '' }}
                                                value="{{ $value->id }}">
                                                {{ $value->productCode . ' - ' . $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <span class="error text-red text-bold"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Ledger </label>
                                    <select class="form-control select2 supid" name="ledger_id" id="ledger_id">
                                        <option value="all">All</option>
                                        <x-account :setAccounts="$ledgers" :selectVal="$ledger_id" />
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Supplier </label>
                                    <select class="form-control select2" name="supplier_id" id="supplier_id">
                                        <option value="all" selected>All Suppliers</option>
                                        @foreach ($supplier as $key => $value)
                                            <option {{ $supplier_id == $value->id ? 'selected' : '' }}
                                                value="{{ $value->id }}">

                                                {{ $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Purchase Type </label>
                                    <select class="form-control select2" name="purchasetype" id="purchasetypeSelect">
                                        <option value="all"
                                            {{ !isset($purchasetype) || $purchasetype == 'all' || $purchasetype == '' ? 'selected' : '' }}>
                                            All</option>
                                        <option value="local"
                                            {{ isset($purchasetype) && $purchasetype == 'local' ? 'selected' : '' }}>
                                            Local</option>
                                        <option value="imported"
                                            {{ isset($purchasetype) && $purchasetype == 'imported' ? 'selected' : '' }}>
                                            Imported</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-search"></i>
                                        Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="load_data"></div>
            </form>
        </div>
        @php
            // dd($purchaseDetails);
        @endphp
        @if (isset($purchaseDetails) && !empty($purchaseDetails) && $purchaseDetails->count() > 0)
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header no-print">
                        <h3 class="card-title">Purchse Report</h3>
                        <a onclick="window.print()" target="_blank" class="btn btn-default float-right my-2 no-print"><i
                                class="fas fa-print"></i>
                            Print</a>
                        <div id="tableActions" class=" float-right my-2 no-print"></div>
                    </div>
                    <div class="card-body">

                        <div class="invoice p-3 mb-3">
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table  table-bordered">
                                        <tr>
                                            <td style="text-align: center">
                                                @if (isset($companyInfo->logo))
                                                    <a href="{{ route('home') }}">
                                                        <img width="200px"
                                                            src="{{ asset('/backend/logo/' . $companyInfo->logo) }}"
                                                            style="" alt="">
                                                    </a>
                                                @endif
                                            </td>
                                            <td width="70%" style="text-align: center">
                                                <h3>Purchase Report</h3>
                                                <h4><b>From Date: {{ $from_date }}</b>, <b>To date: {{ $to_date }}
                                                    </b></h4>
                                            </td>
                                        </tr>
                                    </table>

                                    @php
                                        $totalPurchases = $purchaseDetails->pluck('purchases_id')->unique()->count();
                                        $totalQty = $purchaseDetails->sum('quantity');
                                        $totalAmount = $purchaseDetails->sum('total_price');
                                        $localQty = $purchaseDetails->where('purchasetype', 'local')->sum('quantity');
                                        $importedQty = $purchaseDetails
                                            ->where('purchasetype', 'imported')
                                            ->sum('quantity');
                                    @endphp
                                    <div class="row no-print mb-3">
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info"><i
                                                        class="fa fa-file-invoice"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Purchases</span>
                                                    <span class="info-box-number">{{ $totalPurchases }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i class="fa fa-boxes"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Quantity</span>
                                                    <span class="info-box-number">{{ $totalQty }}
                                                        <small>(Local: {{ $localQty }}, Imported:
                                                            {{ $importedQty }})</small>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning"><i
                                                        class="fa fa-money-bill"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Purchase Amount</span>
                                                    <span
                                                        class="info-box-number">{{ number_format($totalAmount, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-secondary"><i
                                                        class="fa fa-truck"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Suppliers Involved</span>
                                                    <span
                                                        class="info-box-number">{{ $purchaseDetails->pluck('supplier_id')->unique()->count() }}</span>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>

                                    <div class="table-responsive">
                                        <table id="datatablexcel"
                                            class="display table-hover table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>SL</th>
                                                    <th>Date</th>
                                                    <th>Invoice</th>
                                                    <th>Warehouse/project</th>
                                                    <th>Supplier</th>
                                                    <th>Product</th>
                                                    <th>Type</th>
                                                    <th>Quantity</th>
                                                    <th>Unit Price</th>
                                                    <th>Total Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $ttlQty = 0;
                                                    $grandTotal = 0;
                                                @endphp
                                                @foreach ($purchaseDetails as $key => $detail)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $detail->date }}</td>
                                                        <td>{{ $detail->purchase->invoice_no ?? '' }}</td>
                                                        <td>
                                                            @if (optional($detail->purchase)->type == 'Branch')
                                                                {{ optional($detail->purchase->branch)->branchCode }}
                                                                {{ optional($detail->purchase->branch)->name }}
                                                            @else
                                                                {{ optional($detail->purchase->project)->name }}
                                                            @endif
                                                        </td>

                                                        <td>
                                                            @if (!empty($detail->ledger_id) && $detail->ledger_id != 0)
                                                                {{ $detail->ledger->account_name ?? 'N/A' }}
                                                            @elseif (!empty($detail->supplier_id) && $detail->supplier_id != 0)
                                                                {{ $detail->supplier->name ?? 'N/A' }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>

                                                        <td>{{ $detail->product->getRawOriginal('name') ?? '' }}</td>
                                                        <td>
                                                            @if ($detail->purchasetype == 'imported')
                                                                <span class="badge badge-info">Imported</span>
                                                            @elseif ($detail->purchasetype == 'local')
                                                                <span class="badge badge-secondary">Local</span>
                                                            @else
                                                                {{ $detail->purchasetype }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $detail->quantity }}</td>
                                                        <td>{{ number_format($detail->unit_price, 2) }}</td>
                                                        <td>{{ number_format($detail->total_price, 2) }}</td>
                                                        @php
                                                            $ttlQty += $detail->quantity;
                                                            $grandTotal += $detail->total_price;
                                                        @endphp
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="7" style="text-align: right">Total:</th>
                                                    <th style="text-align: left;">{{ $ttlQty ?? '' }}</th>
                                                    <th></th>
                                                    <th style="text-align: left;">{{ number_format($grandTotal ?? 0, 2) }}
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>

                                    </div>
                                </div>




                                <div class="col-md-4  float-left">
                                    <br>
                                    <br>

                                    <p>Prepared By:_____________<br />
                                        Date:____________________
                                    </p>
                                </div>
                                <div class="col-md-6 text-center">
                                </div>
                                <div class="col-md-2  ">
                                    <br>
                                    <br>
                                    <p>Approved By:________________<br />
                                        Date:_________________</p>
                                </div>

                                <hr>


                                <div class="col-md-12 bg-success" style="text-align: center">
                                    Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} products.
                                    We believe you will be satisfied by our services.
                                </div>
                                <!-- /.col -->



                            </div>
                            <!-- Table row -->

                        </div>

                    </div>
                </div>
            </div>
        @endif
        <!-- /.col-->
    </div>
@endsection
@section('scripts')
    @include('backend.pages.reports.excel')
    <script>
        $(document).ready(function() {
            function toggleFields() {
                let selectedType = $("#typeSelect").val();
                if (selectedType === "Branch") {
                    $("#branchSelect").show();
                    $("#projectSelect").hide();
                } else if (selectedType === "Project") {
                    $("#branchSelect").hide();
                    $("#projectSelect").show();
                } else {
                    $("#branchSelect").hide();
                    $("#projectSelect").hide();
                }
            }

            toggleFields();

            $("#typeSelect").on("change", function() {
                toggleFields();
            });
        });
    </script>
@endsection
