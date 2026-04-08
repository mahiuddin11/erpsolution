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
            <form action="{{ route('report.sale.sale') }}" method="POST" enctype="multipart/form-data">
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
                                    <input type="text" class="form-control " name="dateRange"
                                        value="{{ $request->dateRange }}" id="reservation" />
                                    @error('dateRange')
                                        <span class="error text-red text-bold"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Warehouse </label>
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
                                        <x-account :setAccounts="$ledgers" :selectVal="$supplier_id" />
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
                    <div id="load_data"></div>
            </form>
        </div>
        @php
            // dd($salesDetails);
        @endphp
        @if (isset($salesDetails) && !empty($salesDetails))
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header no-print">
                        <h3 class="card-title">Sales Report</h3>
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
                                                <h3>Sales Report</h3>
                                                <h4><b>From Date: {{ $from_date }}</b>, <b>To date: {{ $to_date }}
                                                    </b></h4>
                                            </td>
                                        </tr>
                                    </table>
                                    <table id="datatablexcel"
                                        class="display table-hover table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Date</th>
                                                <th>Invoice</th>
                                                <th>Branch</th>
                                                <th>Customer</th>
                                                <th>Quantity</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                                $ttlSalesPrice = 0;
                                                $ttlQty = 0;
                                                $grandTotal = 0;
                                            @endphp
                                            @foreach ($salesDetails as $key => $item)
                                                @php
                                                    $ttlSalesPrice += $item->rate;
                                                    $ttlQty += $item->qty;
                                                    $grandTotal += $item->price;
                                                @endphp
                                                <tr class="clickable-row" data-target="#details-{{ $key }}">
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $item->date }}</td>
                                                    <td>{{ $item->invoice_no }}</td>
                                                    <td>{{ $item->branch->branchCode . ' - ' . $item->branch->name }}</td>
                                                    <td>{{ $item->customer->account_name ?? '' }}</td>
                                                    <td>{{ $item->qty }}</td>
                                                    <td>{{ $item->grand_total }}</td>
                                                    @php
                                                        $ttlQty += $item->qty;
                                                        $grandTotal += $item->grand_total;
                                                    @endphp
                                                </tr>

                                                <tr id="details-{{ $key }}" class="suddetails"
                                                    style="display: none">
                                                    <td colspan="7">
                                                        <strong>Purchase Details:</strong>
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Product</th>
                                                                    <th>Quantity</th>
                                                                    <th>Price</th>
                                                                    <th>Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($item->details as $detail)
                                                                    <tr>
                                                                        <td>{{ $detail->product->getRawOriginal('name') ?? '' }}
                                                                        </td>
                                                                        <td>{{ $detail->qty }}</td>
                                                                        <td>{{ $detail->rate }}</td>
                                                                        <td>{{ $detail->price }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" style="text-align: center">Total</th>
                                                <th>{{ $ttlQty }}</th>
                                                <th>{{ $grandTotal }}</th>
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
            $(".clickable-row").click(function() {
                let target = $(this).data("target");

                if ($(target).is(":visible")) {
                    // If the clicked row's details are already visible, hide it
                    $(target).hide();
                } else {
                    // Hide all details first, then show only the clicked one
                    $(".suddetails").hide();
                    $(target).show();
                }
            });
        });
    </script>
@endsection
