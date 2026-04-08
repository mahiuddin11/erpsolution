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

        @if($errors->any())
        <div class="card">
            <div class="card-body">
                <div class="text-danger">
                    <h4><i style="color:rgb(255, 0, 0)" class="fa fa-regular fa-bell"></i> {{ $errors->first()}}</h4>
                </div>
            </div>
        </div>
        @endif
        <form action="{{ route('report.stock.stocksummery') }}" method="POST" id="stocksub"
            enctype="multipart/form-data">
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

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Branch </label>
                                <select class="form-control select2 " name="branch_id">
                                    <option value="all" selected>All branches</option>
                                    @foreach($branch as $key => $value)
                                    <option {{ $branch_id==$value->id ? 'selected' : '' }} value="{{ $value->id}}">
                                        {{ $value->branchCode.' - '.$value->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                <span class="error text-red text-bold"> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Category </label>
                                <select onchange="getProductList(this.value)" class="select2 form-control catName reset"
                                    id="form-field-select-3" name="category" data-placeholder="Search Category">
                                    <option value="all" selected>---All Category---</option>
                                    <?php
                                    foreach ($category_info as $eachInfo) :
                                    ?>
                                    <option catName="{{ $eachInfo->name }}" {{ $category_id==$eachInfo->id ? 'selected'
                                        :
                                        '' }} value="{{ $eachInfo->id }}">
                                        {{ $eachInfo->name }}
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                @error('branch_id')
                                <span class="error text-red text-bold"> {{ $message }} </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Product </label>
                                <select class="select2 form-control proName" id="productID"
                                    data-placeholder="Search Product">
                                    <option value="all" selected>---All Product---</option>
                                    @if(isset($products))
                                    @foreach($products as $value)
                                    <option {{ $product_id==$value->id ? 'selected' : '' }} value="{{$value->id}}">
                                        {{$value->productCode}} {{$value->name}}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                                @error('branch_id')
                                <span class=" error text-red text-bold"> {{ $message }} </span>
                                @enderror
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
    // dd($StocksumDetails);
    @endphp
    @if (isset($StocksumDetails) && !empty($StocksumDetails))
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header no-print">
                <h3 class="card-title">Stock Summery</h3>
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
                                            <img width="200px" src="{{ asset('/backend/logo/' . $companyInfo->logo) }}"
                                                style="" alt="">
                                        </a>
                                        @endif
                                    </td>
                                    <td width="70%" style="text-align: center">
                                        <h3>Stock Details </h3>

                                    </td>
                                </tr>
                            </table>
                            <table id="datatablexcel" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Branch</th>
                                        <th>Product</th>
                                        <th style="text-align: right">Quantity</th>
                                        <th style="text-align: right">Avg Unit Price</th>
                                        <th style="text-align: right">Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i=1;
                                    $avgPrice = 0;
                                    $qty =0;
                                    $unitprc =0;
                                    $totalprc =0;
                                    $totalPrice =0;
                                    @endphp
                                    @foreach ($StocksumDetails as $item)
                                    @if ($item->productCode)


                                    @php

                                    @endphp
                                    <tr>
                                        <td>{{ $i++; }}</td>
                                        <td>{{ $item->branchCode .' - '.$item->bname }}</td>
                                        <td>{{ $item->productCode .' - '.$item->proname }}</td>
                                        <td align="right">
                                            @php
                                            $inPro = array('Purchase', 'Manual Purchase', 'Production', 'Gain',
                                            'Transfer In', 'Project In', 'Return');
                                            $outPro = array('Production Sale', 'Production Out', 'Sale', 'Damage',
                                            'Lost', 'Transfer Out', 'Project Out', 'Project Use');

                                            $stockIn =\App\Models\Stock::wherein('status',
                                            $inPro)->where('product_id',$item->product_id)->sum('quantity');
                                            $stockOut =\App\Models\Stock::wherein('status',
                                            $outPro)->where('product_id',$item->product_id)->sum('quantity');
                                            $stockqty = $stockIn - $stockOut;

                                            $qty +=$stockqty;
                                            @endphp

                                            {{ $stockqty }}
                                        </td>
                                        <td align="right">
                                            @php
                                            $avgPrice =\App\Models\PurchasesDetails::where('product_id',
                                            $item->product_id)->avg('unit_price');
                                            $totalPrice = $avgPrice * $stockqty;
                                            $totalprc += $totalPrice;
                                            @endphp


                                            <!-- if(!$avgPrice){
                                             $avgPrice =\App\Models\Production::where('product_id',
                                         $item->product_id)->avg('purchases_price');

                                            $totalPrice += $avgPrice * $stockqty;
                                            $totalPrice = $avgPrice * $stockqty;
                                             $unitprc += $avgPrice;
                                             $totalprc += $totalPrice;

                                             }
                                             @endphp -->

                                            {{ $avgPrice }}
                                        </td>
                                        <td align="right">{{ $totalPrice }}</td>
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

                                        <th style="text-align: right;">{{ $qty}}</th>
                                        <th style="text-align: right;">{{ $unitprc}}</th>
                                        <th style="text-align: right;">{{ $totalprc}}</th>
                                    </tr>
                                </tfoot>

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
                            Thank you for choosing  {{ $companyInfo->company_name ?? 'N/A' }}  products.
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