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
        <form action="{{ route('report.purchase.po') }}" method="POST" id="stocksub" enctype="multipart/form-data">
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
                                <input type="text" class="form-control " name="dateRange" value="" id="reservation" />
                                @error('dateRange')
                                <span class="error text-red text-bold"> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
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
                                        {{ $eachInfo->name }}
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                @error('branch_id')
                                <span class="error text-red text-bold"> {{ $message }} </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
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
    // dd($orderDetails);
    @endphp
    @if (isset($orderDetails) && !empty($orderDetails))
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header no-print">
                <h3 class="card-title">Purchase Order Report</h3>
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
                                        <h3>Purchase Order Report</h3>
                                        <h4><b>From Date: {{ $from_date }}</b>, <b>To date: {{ $to_date }} </b></h4>
                                    </td>
                                </tr>
                            </table>
                            <table id="datatablexcel" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th>Branch</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i=1;

                                    $qty =0;
                                    $unitprc =0;
                                    $totalprc =0;

                                    @endphp
                                    @foreach ($orderDetails as $item)

                                    @php

                                    $qty += $item->qty;
                                    $unitprc += $item->unit_price;
                                    $totalprc += $item->total_price;

                                    @endphp
                                    <tr>
                                        <td>{{ $i++; }}</td>
                                        <td>{{ $item->order_date }}</td>
                                        <td>{{ $item->brcode .' - '.$item->brname }}</td>
                                        <td>{{ $item->prname .' - '.$item->productCode }}</td>
                                        <td align="right">{{ $item->qty }}</td>
                                        <td align="right">{{ $item->unit_price }}</td>
                                        <td align="right">{{ $item->total_price }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" style="text-align: right">Total</th>

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
            success: function (data) {
                $('#productID').select2();
                $('#productID option').remove();
                $('#productID').append($(data));
                $("#productID").trigger("select2:updated");
            }
        });
    }

    $(document).ready(function () {
        $('#stocksub').on('submit', function () {
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