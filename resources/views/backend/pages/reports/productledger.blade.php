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
            <form action="{{ route('report.stock.productledger') }}" method="POST" enctype="multipart/form-data">
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
                            {{-- <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date Range:</label>
                                    <input type="text" value="{{$request->dateRange ?? ""}}" class="form-control " name="dateRange" value=""
                                        id="reservation" />
                                    @error('dateRange')
                                        <span class="error text-red text-bold"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div> --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Branch </label>
                                    <select class="form-control select2 " name="branch_id">
                                        <option value="all" selected>All branches</option>
                                        @foreach ($branches as $key => $value)
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

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Product </label>
                                    <select class="form-control select2 " name="product_id">
                                        @foreach ($products as $key => $value)
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
                            @php

                            @endphp

                            <div class="col-md-1">
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

        @endphp

        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header no-print">
                    <h3 class="card-title">Product Ledger</h3>
                    <a onclick="window.print()" target="_blank" class="btn btn-default float-right my-2 no-print"><i
                            class="fas fa-print"></i>
                        Print</a>
                    <div id="tableActions" class=" float-right my-2 no-print"></div>
                </div>

                <div class="card-body">
                    <form action="{{route('report.stock.qty.update')}}" method="get">
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
                                                            style="" alt="" />
                                                    </a>
                                                @endif
                                            </td>
                                            <td width="70%" style="text-align: center">
                                                <h3>Product Ledger</h3>
                                                <h4><b>From Date: {{ $from_date }}</b>, <b>To date: {{ $to_date }}
                                                    </b></h4>
                                            </td>
                                        </tr>
                                    </table>
                                    <table id="datatablexcel" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Date</th>
                                                <th>Invoice</th>
                                                <th>Branch</th>
                                                <th>Product</th>
                                                <th>Option</th>
                                                <th>In</th>
                                                <th>Out</th>
                                                <th>Remaining</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($datas) && count($datas) > 0)
                                                @foreach ($datas as $data)
                                                    <tr>
                                                        <td>{{ $data['sl'] }}</td>
                                                        <td>{{ $data['date'] }}</td>
                                                        <td>{{ $data['invoice'] }}</td>
                                                        <td>{{ $data['branch'] }}</td>
                                                        <td>{{ $data['product'] }}</td>
                                                        <td>{{ $data['status'] }}</td>
                                                        <td>{{ $data['in'] }}</td>
                                                        <td>{{ $data['out'] }}</td>
                                                        <td>{{ $data['remaining'] }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="9" class="text-center">No data available for the
                                                        selected filters.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                        <tfoot>
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

                        <div class="row no-print ">
                            <div class="col-md-6 justify-content-end d-flex">
                                <select name="type" class="form-control" style="width: 200px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background-color: #f9f9f9; font-size: 14px; color: #333; cursor: pointer;">
                                    <option selected value="imported">Imported</option>
                                    <option value="local">Local</option>
                                  </select>
                            </div>
                            <div class="col-md-6 justify-content-start d-flex">
                                <input type="hidden" name="qty" value="{{ $data['remaining'] ?? 0 }}">
                                <input type="hidden" name="branch_id" value="{{$branch_id}}">
                                <input type="hidden" name="product_id" value="{{$product_id}}">
                                <button class="btn btn-success btn-sm">
                                    <i class="fas fa-box "></i> Adjust Quantity
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- /.col-->
    </div>
@endsection
