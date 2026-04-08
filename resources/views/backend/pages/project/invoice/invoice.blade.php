@extends('backend.layouts.master')
@section('title')
Project - {{ $title }}
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
                    @if (helper::roleAccess('project.invoiceCreate.index'))
                    <li class="breadcrumb-item"><a href="{{ route('project.invoiceCreate.index') }}">Invoice</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Invoice List</span></li>
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
                <h3 class="card-title">Project Invoice</h3>

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
                        <div class="col-sm-4 " style="">
                            @if (isset($companyInfo->invoice_logo))
                            <a>
                                <img width="200px"
                                    src="{{ asset('/backend/invoicelogo/' . $companyInfo->invoice_logo) }}" style=""
                                    alt="">
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
                            <b style="text-decoration: underline">Project Invoice </b><br>
                            <b> Invoice :</b>{{ $invoice->invoiceCode }}</b><br>
                            <b> Branch :</b> {{ $invoice->branch->branchCode ?? 'N/A' }} - {{ $invoice->branch->name ??
                            'N/A'
                            }}<br>
                            <b> Project :</b> {{ $invoice->project->projectCode ?? 'N/A' }} - {{ $invoice->project->name
                            ??
                            'N/A'
                            }}<br>
                            <b>Customer:</b> {{ $invoice->customer->customerCode ?? 'N/A' }} - {{
                            $invoice->customer->name ??
                            'N/A' }} <br>
                            <b>Customer Phone:</b> {{ $invoice->customer->phone ?? 'N/A' }}

                            @if ($invoice->customer->bin)
                            | <b>BIN : </b>
                            {{ $invoice->customer->bin }}
                            @endif


                            <br>
                        </div>
                        <!-- /.col -->
                    </div><br>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table  table-bordered">
                                <thead>
                                    <tr>
                                        <th>Invoice Details</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>

                                        <td>{{ $invoice->note }}</td>
                                        <td align="right">
                                            {{ $invoice->total_value }}
                                        </td>
                                    </tr>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5">In words :
                                            @php

                                            echo Terbilang::make($invoice->total_value);

                                            @endphp
                                        </th>
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