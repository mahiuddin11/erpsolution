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

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">

        <div class="col-md-12">

            @if ($errors->any())
                <div class="card">
                    <div class="card-body">
                        <div class="text-danger">
                            <h4><i style="color:rgb(255, 0, 0)" class="fa fa-regular fa-bell"></i> {{ $errors->first() }}
                            </h4>
                        </div>
                    </div>
                </div>
            @endif
            <form action="{{ route('report.stock.stock') }}" method="POST" id="stocksub" enctype="multipart/form-data">
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

                        <div class="row no-print ">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date Range:</label>
                                    <input type="text" class="form-control " name="dateRange" value=""
                                        id="reservation" />
                                    @error('dateRange')
                                        <span class="error text-red text-bold"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Branch </label>
                                    <select class="form-control select2 " name="branch_id">
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Category </label>
                                    <select onchange="getProductList(this.value)" class="select2 form-control catName reset"
                                        id="form-field-select-3" data-placeholder="Search Category">
                                        <option disabled selected>---Select Category---</option>
                                        <?php
                                                                                        foreach ($category_info as $eachInfo) :
                                                                                            ?>
                                        <option catName="{{ $eachInfo->name }}" value="{{ $eachInfo->id }}">
                                            {{ $eachInfo->name }}</option>
                                        <?php endforeach; ?>
                                    </select>
                                    @error('branch_id')
                                        <span class="error text-red text-bold"> {{ $message }} </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Product </label>
                                    <select class="select2 form-control proName" id="productID"
                                        data-placeholder="Search Product">
                                        <option disabled selected>---Select Product---</option>
                                    </select>
                                    @error('branch_id')
                                        <span class="error text-red text-bold"> {{ $message }} </span>
                                    @enderror
                                </div>
                            </div>

                            @php

                            @endphp

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
            // dd($StockDetails);
        @endphp
        @if (isset($StockDetails) && !empty($StockDetails))
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header no-print">
                        <h3 class="card-title">Stock Detail Report</h3>
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
                                                <h3>Stock Detail Report</h3>
                                                <h4><b>From Date: {{ $from_date }}</b>, <b>To date: {{ $to_date }}
                                                    </b></h4>
                                            </td>
                                        </tr>
                                    </table>
                                    {{-- <table id="datatablexcel" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Date</th>
                                                <th>Product</th>
                                                <th style="text-align: right">Quantity</th>
                                                <th style="text-align: right">Avg Unit Price</th>
                                                <th style="text-align: right">Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;

                                                $qty = 0;
                                                $unitprc = 0;
                                                $totalprc = 0;

                                            @endphp
                                            @foreach ($StockDetails as $item)
                                                @if ($item->productCode)
                                                    @php

                                                        $qty += $item->quantity;
                                                        $unitprc += $item->avgPrice;
                                                        $totalprc += $item->total_price;

                                                    @endphp
                                                    <tr>
                                                        <td>{{ $i++ }}</td>
                                                        <td>{{ $item->date }}</td>
                                                        <td>{{ $item->productCode . ' - ' . $item->name }}</td>
                                                        <td align="right">{{ $item->quantity }}</td>
                                                        <td align="right">{{ $item->avgPrice }}</td>
                                                        <td align="right">{{ $item->total_price }}</td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td colspan="6" style="color:red">NO DATA RECORDS</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align: right">Total</th>

                                                <th style="text-align: right;">{{ $qty }}</th>
                                                <th style="text-align: right;">{{ $unitprc }}</th>
                                                <th style="text-align: right;">{{ $totalprc }}</th>
                                            </tr>
                                        </tfoot>

                                    </table> --}}

                                    <table id="datatablexcel" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Product Code</th>
                                                <th>Product Name</th>
                                                <th>Date</th>
                                                <th>Particulars</th>
                                                <th>Invoice / Ref</th>
                                                <th>Branch</th>
                                                <th style="text-align: right">In Qty</th>
                                                <th style="text-align: right">Out Qty</th>
                                                <th style="text-align: right">Current Stock</th>
                                                <th style="text-align: right">Unit Price</th>
                                                <th style="text-align: right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                                $runningBalance = 0;
                                            @endphp

                                            @foreach ($StockDetails as $item)
                                                @php
                                                    // Stock In / Out Logic
                                                    $positiveStatuses = [
                                                        'Opening',
                                                        'Purchase',
                                                        'Manual Purchase',
                                                        'Production',
                                                        'Gain',
                                                        'Transfer In',
                                                        'Project In',
                                                        'Return',
                                                        'Purchase Return',
                                                    ];

                                                    $isIn = in_array($item->status, $positiveStatuses);

                                                    if ($isIn) {
                                                        $runningBalance += $item->quantity ?? 0;
                                                        $inQty = $item->quantity ?? 0;
                                                        $outQty = 0;
                                                    } else {
                                                        $runningBalance -= $item->quantity ?? 0;
                                                        $inQty = 0;
                                                        $outQty = $item->quantity ?? 0;
                                                    }

                                                    // Smart Invoice / Reference Logic
                                                    $invoiceRef = '-';
                                                    if (!empty($item->invoice_no)) {
                                                        $invoiceRef = $item->invoice_no;
                                                    } elseif (!empty($item->general_id)) {
                                                        $invoiceRef = $item->general_id;
                                                    } elseif ($item->status == 'Opening') {
                                                        $invoiceRef = 'Opening Stock';
                                                    } elseif (in_array($item->status, ['Gain', 'Damage', 'Lost'])) {
                                                        $invoiceRef =
                                                            $item->general_id ??
                                                            'ADJ-' . str_pad($item->id ?? '', 5, '0', STR_PAD_LEFT);
                                                    } else {
                                                        $invoiceRef = $item->general_id ?? '-';
                                                    }
                                                @endphp

                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td><strong>{{ $item->productCode ?? 'N/A' }}</strong></td>
                                                    <td>{{ $item->product_name ?? ($item->name ?? 'N/A') }}</td>
                                                    <td>{{ $item->date ? date('d-m-Y', strtotime($item->date)) : '' }}</td>

                                                    <td><strong>{{ ucwords(str_replace(['_', '-'], ' ', $item->status)) }}</strong>
                                                    </td>

                                                    <td><strong>{{ $invoiceRef }}</strong></td>

                                                    <td>{{ $item->branchCode ?? '' }} -
                                                        {{ $item->branch_name ?? ($item->bname ?? '') }}</td>

                                                    <td align="right" style="color: #28a745; font-weight: bold">
                                                        {{ $inQty > 0 ? number_format($inQty) : '' }}
                                                    </td>

                                                    <td align="right" style="color: #dc3545; font-weight: bold">
                                                        {{ $outQty > 0 ? number_format($outQty) : '' }}
                                                    </td>

                                                    <td align="right" style="font-weight: bold; color: navy">
                                                        {{ number_format($runningBalance) }}
                                                    </td>

                                                    <td align="right">{{ number_format($item->unit_price ?? 0, 2) }}</td>
                                                    <td align="right">{{ number_format($item->total_price ?? 0, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

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

    <script>
        function getProductList(cat_id) {
            if (cat_id == '' || cat_id == null || cat_id == 0) {
                return false;
            }
            $.ajax({
                "url": "{{ route('inventorySetup.purchase.getProductList') }}",
                "type": "GET",
                cache: false,
                data: {
                    "_token": "{{ csrf_token() }}",
                    cat_id: cat_id
                },
                success: function(data) {
                    $('#productID').select2();
                    $('#productID option').remove();
                    $('#productID').append($(data));
                    $("#productID").trigger("select2:updated");
                }
            });
        }

        $(document).ready(function() {
            $('#stocksub').on('submit', function() {
                var product_id = $(".proName").find('option:selected').val();
                var prhtml = '<input type="hidden" name="product_id" value="' + product_id + '" />';
                $(this).append(prhtml);
            })
        })
    </script>
@endsection
@section('scripts')
    @include('backend.pages.reports.excel')
@endsection
