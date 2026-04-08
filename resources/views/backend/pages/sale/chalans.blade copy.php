@extends('backend.layouts.master')
@section('title')
    Sale - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }

        /* Watermark Styles */
        .invoice {
            position: relative;
        }

        .watermark-background {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            /* Set to 50% opacity */
            z-index: 0;
            /* Layer it behind all other content */
            width: 60%;
            /* Adjust width to control watermark size */
            pointer-events: none;
            /* Prevent interference with other elements */
        }

        /* Ensure content is above the watermark */
        .invoice-content {
            position: relative;
            z-index: 1;
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Delivery Chalans</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('sale.sale.index'))
                            <li class="breadcrumb-item"><a href="{{ route('sale.sale.index') }}">Sale</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Delivery Chalans List</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Delivery Chalans Invoice</h3>
                </div>
                <div class="card-body">
                    <div class="row no-print">
                        <div class="col-12">
                            <a onclick="window.print()" target="_blank" class="btn btn-default float-right my-2">
                                <i class="fas fa-print"></i> Print
                            </a>
                        </div>
                    </div>

                    <div class="invoice p-3 mb-3">
                        <!-- Watermark Image -->
                        <img src="{{ asset('/backend/invoicelogo/' . $companyInfo->invoice_logo) }}"
                            class="watermark-background" alt="Watermark">

                        <!-- Invoice Content -->
                        <div class="invoice-content">
                            <div class="row invoice-info">
                                <div class="col-sm-12 text-center" style="border-bottom: 1px solid #000">
                                    @if (isset($companyInfo->invoice_logo))
                                        <a href="{{ route('home') }}">
                                            <img src="{{ asset('/backend/invoicelogo/' . $companyInfo->invoice_logo) }}"
                                                width="200px" alt="Company Logo">
                                        </a>
                                    @endif
                                </div>

                                <div class="col-sm-4 invoice-col" style="text-align:left">
                                    <strong>
                                        <b style="text-decoration: underline">Date:</b> {{ $invoice->date }}<br>
                                        <b>Invoice:</b> {{ $invoice->invoice_no ?? 'N/A' }} ({{ $invoice->payment_type }})<br>
                                        <b>PO No:</b> {{ $invoice->po_invoice ?? 'N/A' }}<br>
                                    </strong>
                                </div>
                                
                                <div class="col-sm-4 invoice-col" style="text-align: center;">
                                    <strong>
                                        <b style="font-size: 20px">{{ $companyInfo->company_name ?? 'N/A' }}</b> <br>
                                        <b style="text-align:left;color:#000">OFFICE ADDRESS:</b>
                                           <p>House # 1248, Road # 09, Level# 04
                                                Mirpur DOHS, Dhaka-1216, Bangladesh.</p>
                                        <b style="text-align:left;color:#000">Factory And Ware House:</b>
                                           <p>83-84 Bagdi, Nagori Bazar,
                                            Kaliganj, Gazipur-1720.</p>

                                    </strong>
                                </div>
                                
                                <div class="col-sm-4 invoice-col" style="text-align:right">
                                    <strong>
                                        <b style="text-decoration: underline">Receive Invoice</b><br>
                                        <b>Customer:</b> {{ optional($invoice->customer)->account_name ?? 'N/A' }}<br>
                                        <b>Phone:</b> {{ optional(optional($invoice->customer)->accountable)->phone ?? 'N/A' }}<br>
                                        <b>Address:</b> {{ optional(optional($invoice->customer)->accountable)->address ?? 'N/A' }}<br>
                                    </strong>
                                </div>
                                
                            </div>
                            <br>

                            <!-- Table row -->
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Product</th>
                                                <th class="text-right">Unit</th>
                                                <th class="text-right">Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $totalQty = 0; @endphp
                                            @foreach ($invoice->details as $detail)
                                                @php $totalQty += $detail->qty; @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $detail->product->name ?? 'N/A' }}</td>
                                                    <td class="text-right">
                                                        @php
                                                            $unit = DB::table('products')
                                                                ->select('unit_id', 'product_units.name')
                                                                ->where('products.id', $detail->product_id)
                                                                ->join(
                                                                    'product_units',
                                                                    'product_units.id',
                                                                    '=',
                                                                    'products.unit_id',
                                                                )
                                                                ->first();
                                                        @endphp
                                                        {{ $unit->name }}
                                                    </td>
                                                    <td class="text-right">{{ $detail->qty ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align: right">TOTAL</th>
                                                <th class="text-right">{{ $totalQty }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <!-- Terms and Conditions -->
                                <div class="col-12 mt-3">
                                    <h5 style="font-weight: bold;">Terms and Conditions:</h5>
                                    <ul>
                                        <li style="font-weight: bold;">Product unload by factory.</li>
                                        <li style="font-weight: bold;">Product should be checked before received.</li>
                                        <li style="font-weight: bold;">Challan should be checked before received.</li>
                                    </ul>
                                </div>
                                
                                <div class="col-md-4 col-sm-4 text-center float-left">
                                    <br><br>
                                    <p style="font-weight: bold; text-align: left">PREPARED BY : <br> {{$invoice->user->name ?? ""}}<br /></p>
                                </div>
                                <div class="col-md-4 col-sm-4 text-center"></div>
                                <div class="col-md-4 col-sm-4 text-center float-right">
                                    <br><br>
                                    <p style="font-weight: bold; text-align: left">RECEVIED BY : <br> Name: <br> SIGNATURE:</p>
                                </div>
                                <hr>
                                <!-- Thank You Note -->
                                <div class="col-md-12 bg-success" style="text-align: center">
                                    Thank you for choosing {{$companyInfo->company_name}} products.
                                    We believe you will be satisfied with our services.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
