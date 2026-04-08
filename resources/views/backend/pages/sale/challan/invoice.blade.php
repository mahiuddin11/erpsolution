@extends('backend.layouts.master')
@section('title')
Sale - {{ $title }}
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
                    Sale </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('sale.sale.index'))
                    <li class="breadcrumb-item"><a href="{{ route('sale.sale.index') }}">Sale</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Challan Invoice</span></li>
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
                <h3 class="card-title">Delivery Challan Invoice</h3>

            </div>
            <div class="card-body">
                <div class="row no-print">
                    <div class="col-12">
                        <a onclick="window.print()" target="_blank" class="btn btn-default float-right my-2"><i
                                class="fas fa-print"></i>
                            Print</a>
                    </div>
                </div>
                <div class="invoice p-3 mb-3">
                    <!-- title row -->

                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 ">
                            @if(isset($companyInfo->invoice_logo))
                            <a href="{{ route('home') }}">
                                <img width="200px" src="{{asset('/backend/invoicelogo/' . $companyInfo->invoice_logo)}}"
                                    style="" alt="">
                            </a>
                            @endif
                        </div>

                        <div class="col-sm-4 invoice-col" style="text-align: center; ">
                            <b style="font-size : 20px">{{ $companyInfo->company_name ?? 'N/A' }}</b>
                            <address>
                                Phone : <strong>{{ $companyInfo->phone ?? 'N/A' }}</strong><br>
                                Address : <strong><em>{{ $companyInfo->address ?? 'N/A' }}</em></strong><br>
                                Email: <strong>{{ $companyInfo->email ?? 'N/A' }}</strong>
                            </address>
                        </div>

                        <div class="col-sm-4 invoice-col" style="text-align:right">
                            <b style="text-decoration: underline">Receive Invoice </b><br>
                            <b>Invoice : {{ $sale->invoice_no ?? 'N/A' }} - ({{ $sale->payment_type ?? 'N/A' }})</b><br>

                            <b>Customer:</b> {{ $invoice->customer->name ?? 'N/A' }}<br>
                            <b>Phone:</b> {{ $invoice->customer->phone ?? 'N/A' }}<br>
                            <b>Challan No : {{ $invoice->chalan_no ?? 'N/A' }} </b><br>
                        </div>
                        <!-- /.col -->
                    </div><br>
                    <!-- /.row -->
                    <!-- Table row -->
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 10%;">SL.NO.</th>
                                        <th>Product Name</th>
                                        <th style="width: 10%;" class="text-right">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @php
                                    $totalQty = 0;

                                    @endphp

                                    @foreach ($deliverydetails as $detail)

                                    @php
                                    $totalQty += $detail->delivary_qty;

                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $detail->product->productCode .' - '. $detail->product->name ?? 'N/A'
                                            }}</td>
                                        <td class="text-right">{{ $detail->delivary_qty ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td align="right" colspan="2"><b>Total :</b></td>

                                        <td align="right">{{$totalQty}} </td>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>





                        <div class="col-md-4 text-center float-left">
                            <br>
                            <br>

                            <p>Received by:_____________<br />
                                Date:____________________
                            </p>
                        </div>
                        <div class="col-md-4 text-center">
                        </div>
                        <div class="col-md-4 text-center float-right">
                            <br>
                            <br>
                            <p>Authorized by:________________<br />
                                Date:_________________</p>
                        </div>

                        <hr>


                        <div class="col-md-12 bg-success" style="text-align: center">
                            Thank you for choosing  {{ $companyInfo->company_name ?? 'N/A' }}  products.
                            We believe you will be satisfied by our services.
                        </div>
                        <!-- /.col -->



                    </div>
                </div>

            </div>
        </div>
        <!-- /.col-->
    </div>
    @endsection
    @section('scripts')
    @endsection