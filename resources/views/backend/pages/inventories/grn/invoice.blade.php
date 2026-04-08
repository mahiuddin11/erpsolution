@extends('backend.layouts.master')
@section('title')
Purchase Requisition - {{ $title }}
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
                    Purchase Requisition </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('inventorySetup.transfer.index'))
                    <li class="breadcrumb-item"><a href="{{ route('inventorySetup.transfer.index') }}">Purchase
                            Requisition</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Purchase Requisition List</span></li>
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
                <h3 class="card-title">Purchase Requisition Invoice</h3>
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

                        <div class="col-sm-4 invoice-col" style="text-align: left">
                            <span class="btn btn-success btn-arrow-right"> From Branch</span>
                            <br>
                            @php

                            @endphp
                            <address>

                            </address>
                        </div>
                        <!-- /.col -->

                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col" style="text-align:center">
                            @if (isset($companyInfo->invoice_logo))

                            <img width="100px" src="{{ asset('/backend/invoicelogo/' . $companyInfo->invoice_logo) }}"
                                style="" alt="">

                            @endif
                            <h6>
                                <strong> {{ $companyInfo->company_name ?? 'N/A' }}</strong>
                            </h6>


                            <address>
                                <strong>{{ $companyInfo->phone ?? 'N/A' }}</strong><br>
                                <strong><em>{{ $companyInfo->address ?? 'N/A' }}</em></strong><br>
                                <strong>{{ $companyInfo->email ?? 'N/A' }}</strong>
                            </address>
                        </div>
                        <div class="col-sm-4 invoice-col" style="text-align:right">

                            <span class="btn btn-info btn-arrow-right"> To Branch</span>
                            <br>
                            @php
                            $toBranchInfo = \App\Models\Branch::where(['id' => $invoice->from_branch_id])->first();
                            @endphp
                            <address>

                            </address>
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
                                        <th>Product</th>
                                        <th class="text-right">Quantity </th>
                                        <th class="text-right">Approved Quantity</th>
                                        <th class="text-right">Unit Price</th>
                                        <th style="text-align:right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @php

                                    $totalQty = 0;
                                    $totalUp = 0;
                                    $totalPrice = 0;
                                    $totalAprQty = 0;
                                    @endphp

                                    @foreach ($invoice->details as $detail)

                                    @php
                                    $totalQty += $detail->qty;
                                    $totalAprQty += $detail->approve_qty;
                                    $totalUp += $detail->unit_price;
                                    $totalPrice += $detail->unit_price * $detail->approve_qty;

                                    $subtotal = number_format($totalPrice, 2);
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $detail->product->name ?? 'N/A' }}</td>
                                        <td class="text-right">{{ $detail->qty ?? 'N/A' }}</td>
                                        <td class="text-right">{{ $detail->approve_qty ?? 'N/A' }}</td>
                                        <td class="text-right">
                                            {{ number_format($detail->unit_price, 2) ?? 'N/A' }}
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($detail->unit_price * $detail->approve_qty, 2) ?? 'N/A' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" style="text-align: center">TOTAl</th>
                                        <th class="text-right">{{ number_format($totalQty, 2) }}</th>
                                        <th class="text-right">{{ number_format($totalAprQty, 2) }}</th>
                                        <th class="text-right">{{ number_format($totalUp, 2) }}</th>
                                        <th class="text-right">{{ number_format($totalPrice, 2) }}
                                        </th>
                                    </tr>

                                    <tr>
                                        <td colspan="4"></td>
                                        <th style="text-align:right;">Shipping ( + ):</th>
                                        <td style="text-align:right">
                                            @php
                                            $shipping = $invoice->shipping;
                                            @endphp
                                            <b>
                                                {{ number_format($shipping, 2) ?? 0 }}
                                            </b>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="4"></td>
                                        <th style="text-align:right;">Net Total:</th>
                                        <td style="text-align:right">

                                            <b>
                                                {{ number_format($totalPrice + $shipping, 2) ?? 0 }}
                                            </b>
                                        </td>
                                    </tr>


                                    <tr>
                                        <th colspan="6">In words :
                                            {{ ucfirst(Terbilang::make($totalPrice + $shipping)) }}</th>
                                    </tr>
                                    <tr>
                                        <td colspan="6"><b>Note :</b> {{ $invoice->narration ?? 'N/A' }}</td>
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