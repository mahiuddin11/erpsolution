@extends('backend.layouts.master')
@section('title')
    Inventory - {{ $title }}
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
                        Inventory </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('inventorySetup.purchase.index'))
                            <li class="breadcrumb-item"><a href="{{ route('inventorySetup.purchase.index') }}">Purchase</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Purchase List</span></li>
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
                    <h3 class="card-title">Purchase Invoice</h3>

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
                                @if (isset($companyInfo->invoice_logo))
                                    <a href="{{ route('home') }}">
                                        <img width="200px"
                                            src="{{ asset('/backend/invoicelogo/' . $companyInfo->invoice_logo) }}"
                                            style="" alt="">
                                    </a>
                                @endif
                            </div>

                            <div class="col-sm-4 invoice-col" style="text-align: center">
                                <b style="font-size : 20px">{{ $companyInfo->company_name ?? 'N/A' }}</b>
                                <address>
                                    Phone : <strong>{{ $companyInfo->phone ?? 'N/A' }}</strong><br>
                                    Address : <strong><em>{{ $companyInfo->address ?? 'N/A' }}</em></strong><br>
                                    Email: <strong>{{ $companyInfo->email ?? 'N/A' }}</strong>
                                </address>
                            </div>
                            <!-- /.col -->
                            <!-- /.col -->
                            <div class="col-sm-4 invoice-col" style="text-align:right">
                                <b style="text-decoration: underline">Receive Invoice </b><br>
                                <b>Date : {{ $invoice->date ?? 'N/A' }}</b><br>
                                <b>Invoice : {{ $invoice->invoice_no ?? 'N/A' }} ( {{ $invoice->payment_type }}
                                    )</b><br>
                                {{-- <b> Branch :</b>{{ $invoice->branch->branchCode ?? 'N/A' }} - {{
                            $invoice->branch->branchCode ?? 'N/A' }} - {{ $invoice->branch->name ??
                            'N/A' }} - ({{ $invoice->branch->phone ??
                            'N/A'
                            }})<br> --}}
                                {{-- <b>Supplier:</b> {{$invoice->supplier->supplierCode ?? "N/A"}} - {{$invoice->supplier->name
                            ?? 'N/A'}}
                            ({{
                            $invoice->supplier->phone ??
                            'N/A' }})<br> --}}
                                {{-- @if (!empty($invoice->supplier->specialNumber))
                            <b>Supplier Bin:</b>
                            {{$invoice->supplier->specialNumber ?? "N/A"}}<br>
                            @endif --}}
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
                                            <th>SL</th>
                                            <th>Supplier</th>
                                            <th>Product</th>
                                            <th>Type</th>
                                            <th class="text-right">Qty</th>
                                            <th class="text-right">Unit Price</th>
                                            <th style="text-align:right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @php
                                            $totalQty = 0;
                                            $totalUp = 0;
                                            $totalPrice = 0;
                                        @endphp

                                        @foreach ($invoice->details as $detail)
                                            @php
                                                $totalQty += $detail->quantity;
                                                $totalUp += $detail->unit_price;
                                                $totalPrice += $detail->total_price;

                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <th>{{ $detail->supplier->name ?? '' }}</th>
                                                <td>{{ $detail->product->productCode ?? 'N/A' }} -
                                                    {{ $detail->product->name ?? 'N/A' }}
                                                </td>
                                                <td class="text-right">{{ $detail->purchasetype ?? 'N/A' }}</td>
                                                <td class="text-right">{{ $detail->quantity ?? 'N/A' }}</td>
                                                <td class="text-right">
                                                    {{ number_format($detail->unit_price, 2) ?? 'N/A' }}</td>
                                                <td class="text-right">
                                                    {{ number_format($detail->total_price, 2) ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        {{-- <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <th class="text-right">{{ number_format($totalQty, 2) }}</th>
                                        <th class="text-right">{{ number_format($totalUp, 2) }}</th>
                                        <th class="text-right">
                                            {{ number_format($invoice->grand_total, 2) ?? 'N/A' }}</th>
                                    </tr> --}}

                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <th style="text-align:right;">Discount ( - ):</th>
                                            <td style="text-align:right">

                                                <b>
                                                    {{ number_format($invoice->discount, 2) ?? 0 }}
                                                </b>
                                            </td>
                                        </tr>
                                        @if ($invoice->vat)
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <th style="text-align:right;">Vat ( + ):</th>
                                                <td style="text-align:right">
                                                    <b>
                                                        {{ number_format($invoice->vat, 2) ?? 0 }}
                                                    </b>
                                                </td>
                                            </tr>
                                        @endif

                                        @php
                                            $ttlAmountafterdiscount = $invoice->grand_total - $invoice->discount;
                                        @endphp
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <th style="text-align:right;">Net Total:</th>
                                            <td style="text-align:right">
                                                <b>
                                                    {{ number_format($ttlAmountafterdiscount, 2) ?? 0 }}
                                                </b>
                                            </td>
                                        </tr>
                                        {{-- <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <th style="text-align:right;">Paid(-):</th>
                                        <td style="text-align:right">

                                            <b>
                                                {{ number_format($invoice->paid_amount, 2) }}
                                            </b>
                                        </td>
                                    </tr> --}}
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <th style="text-align:right;">Due:</th>
                                            <td style="text-align:right">
                                                <b>
                                                    {{ number_format($invoice->due_amount, 2) }}
                                                </b>
                                            </td>
                                        </tr>
                                        
                                        <tr>

                                            <th colspan="7">
                                               <b>In Words :</b> {{ numberToWords((int) str_replace(',', '',$ttlAmountafterdiscount)) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <td colspan="7">Narration: {{ $invoice->narration ?? 'N/A' }}</td>
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
                                Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} products.
                                We believe you will be satisfied by our services.
                            </div>
                            <!-- /.col -->
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <!-- this row will not appear when printing -->

                    </div>
                </div>

            </div>
        </div>
        <!-- /.col-->
    </div>
@endsection
@section('scripts')
@endsection
