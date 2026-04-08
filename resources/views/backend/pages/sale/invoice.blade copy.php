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
                        <li class="breadcrumb-item active"><span>Sale List</span></li>
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
                    <h3 class="card-title">Sales Invoice</h3>

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

                            <div class="col-sm-4 invoice-col" style="text-align: center;">
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
                                <b style="text-decoration: underline"> Date : </b> {{ $invoice->date }} <br>
                                <b>Invoice : {{ $invoice->invoice_no ?? 'N/A' }} ( {{ $invoice->payment_type }} )</b><br>
                                <b>Customer:</b>
                                {{ $invoice->customer->account_name ?? 'N/A' }}
                                <br><b> Phone:</b>
                                {{ optional($invoice->customer->accountable)->phone ?? 'N/A' }}
                                <br><b> Address:</b>
                                {{ optional($invoice->customer->accountable)->address ?? 'N/A' }}
                                <br>

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
                                            {{-- <th>Cyl Size</th> --}}
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Vat*</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Unit Price</th>
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
                                                $totalQty += $detail->qty;
                                                $totalUp += $detail->rate;
                                                $totalPrice += $detail->price;

                                                $subtotal = number_format($totalPrice, 2);
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{  $detail->product->name ?? 'N/A' }}
                                                </td>
                                                {{-- <td class="text-center">{{ $detail->cty_size ?? '0' }}</td> --}}
                                                <td class="text-center">{{ $detail->qty ?? 'N/A' }}</td>
                                                <td class="text-center">{{ $detail->vat ?? 'N/A' }}</td>
                                                {{-- <td class="text-center">{{ $detail->gas_qty ?? '0' }}</td> --}}
                                                <td>
                                                    {{ $detail->purchasetype }}
                                                </td>
                                                <td class="text-right">{{ number_format($detail->rate, 2) ?? 'N/A' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($detail->price, 2) ?? 'N/A' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="1" style="text-align: center">TOTAl</th>
                                            <th class="text-right"></th>
                                            <th class="text-right">{{ number_format($totalQty, 2) }}</th>
                                            <th class="text-right"></th>
                                            <th class="text-right"></th>
                                            <th class="text-right">{{ number_format($totalUp, 2) }}</th>
                                            <th class="text-right">{{ $subtotal }}</th>
                                        </tr>
                                        @if ($invoice->discount)
                                            <tr>
                                                <td colspan="2"></td>
                                                <th class="text-right"></th>
                                                <th class="text-right"></th>
                                                <th class="text-right"></th>
                                                <th style="text-align:right;">Discount ( - ):</th>
                                                <td style="text-align:right">
                                                    @php
                                                        $discount = $invoice->discount;

                                                    @endphp
                                                    <b>
                                                        {{ number_format($discount, 2) ?? 0 }}
                                                    </b>
                                                </td>
                                            </tr>
                                        @endif
                                        @if($invoice->carrying_cost)
                                        <tr>
                                            <td colspan="2"></td>
                                            <th class="text-right"></th>
                                            <th class="text-right"></th>
                                            <th class="text-right"></th>
                                            <th style="text-align:right;">Carrying Cost ( + ):</th>
                                            <td style="text-align:right">
                                                @php
                                                    $carrying_cost = $invoice->carrying_cost;

                                                @endphp
                                                <b>
                                                    {{ number_format($carrying_cost, 2) ?? 0 }}
                                                </b>
                                            </td>
                                        </tr>
                                        @endif

                                        @if ($invoice->labor_bill)
                                        <tr>
                                            <td colspan="2"></td>
                                            <th class="text-right"></th>
                                            <th class="text-right"></th>
                                            <th class="text-right"></th>
                                            <th style="text-align:right;">Labor bill ( + ):</th>
                                            <td style="text-align:right">
                                                @php
                                                    $labor_bill = $invoice->labor_bill;
                                                @endphp
                                                <b>
                                                    {{ number_format($labor_bill, 2) ?? 0 }}
                                                </b>
                                            </td>
                                        </tr>
                                        @endif

                                        <tr>
                                            <td colspan="2"></td>
                                            <th class="text-right"></th>
                                            <th class="text-right"></th>
                                            <th class="text-right"></th>
                                            <th style="text-align:right;"> Total:</th>
                                            <td style="text-align:right">

                                                <b>
                                                    {{ number_format($invoice->net_total, 2) ?? 0 }}
                                                </b>
                                            </td>
                                        </tr>
                                        {{-- <tr>
                                            <td colspan="2"></td>
                                            <th class="text-right"></th>
                                            <th class="text-right"></th>
                                            <th class="text-right"></th>
                                            <th style="text-align:right;">Paid(-):</th>
                                            <td style="text-align:right">
                                                <b>
                                                    {{ number_format($invoice->partialPayment, 2) }}
                                                </b>
                                            </td>
                                        </tr> --}}
                                        {{-- <tr>
                                            <td colspan="2"></td>
                                            <th class="text-right"></th>
                                            <th class="text-right"></th>
                                            <th class="text-right"></th>
                                            <th style="text-align:right;">Due:</th>
                                            <td style="text-align:right">
                                                @php

                                                    $due = $invoice->net_total - $invoice->partialPayment;

                                                @endphp
                                                <b>
                                                    {{ number_format($due, 2) }}
                                                </b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="6">In words : {{ ucfirst(Terbilang::make($due)) }}</th>
                                        </tr> --}}
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
                                Thank you for choosing {{$companyInfo->company_name}} products.
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
