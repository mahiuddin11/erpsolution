@extends('backend.layouts.master')
@section('title')
Delivery Challan - {{ $title }}
@endsection

@section('styles')
    <style>
        .invoice-container {
            position: relative;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
            pointer-events: none;
        }

        /* Header Styling */
        .header {
            display: flex;
            align-items: center;
            background-color: #ffffff;
            /* White background */
            border-bottom: 2px solid #000000;
            /* Black underline */
            padding: 10px;
        }

        .header-left {
            width: 30%;
            /* Left part takes 20% of the header width */
        }

        .header-left .logo {
            max-width: 100%;
            /* Ensure the image fits within the left part */
            height: auto;
        }

        .header-right {
            width: 70%;
            /* Right part takes 80% of the header width */
            text-align: left;
        }

        .header-right h1 {
            font-size: 32px;
            font-weight: 600;
            color: #000000;
            text-align: right;
            margin: 0;
        }

        .header-right p {
            font-size: 16px;
            font-weight: 600;
            text-align: right;
            color: #420297;
            margin: 5px 0 0;
        }

        /* Invoice Details Section */
        .invoice-details {
            margin-bottom: 20px;
        }

        .invoice-info {
            text-align: left;
            /* Align text to the left */
        }

        /* Button Styling */
        .invoice-button {
            display: block;
            /* Make the button a block element */
            width: 100%;
            /* Full width of the container */
            padding: 5px;
            /* Add padding for better appearance */
            background-color: #0f752c;
            /* Blue background color */
            color: #ffffff;
            /* White text color */
            /* Font size */
            font-weight: bold;
            /* Bold text */
            text-align: center;
            /* Center the text inside the button */
            border: none;
            /* Remove default border */
            border-radius: 5px;

            /* Pointer cursor on hover */
            margin-bottom: 15px;
            /* Add space below the button */
        }


        .invoice-details,
        .supplier-customer,
        .product-table,
        .totals,
        .attachments,
        .footer {
            margin-bottom: 20px;
        }

        .supplier-customer {
            display: flex;
            justify-content: space-between;
        }

        .supplier,
        .customer {
            width: 48%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f4f4f4;
        }

        .totals p,
        .attachments ul {
            margin-bottom: 10px;
        }

        .attachments ul {
            list-style-type: disc;
            padding-left: 20px;
        }

        .footer {
            display: flex;
            justify-content: space-between;
        }



        .contact-info a {
            color: #007BFF;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: 1;
            width: 729px;
            height: auto;
            user-select: none;
            pointer-events: none;
        }

        .watermark img {
            width: 100%;
            height: auto;
        }

        .custom-footer {
            padding: 20px 0;
            text-align: center;
            border-top: 3px solid #007bff;
            /* Blue border for a professional look */
        }

        .footer-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-section {
            flex: 1;
            min-width: 250px;

            text-align: left;
        }

        .footer-section h5 {
            color: #333;
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .footer-section p {
            font-size: 14px;
            color: #0c0c0c;
            margin: 5px 0;
        }

        .footer-section a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .footer-section a:hover {
            text-decoration: underline;
        }

        .prepared-received-section {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            border-top: 2px solid #ddd;
            margin-top: 20px;
        }

        .prepared-by,
        .received-by {
            flex: 1;
            padding: 10px;
        }

        .prepared-by h3,
        .received-by h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }

        .custom-table td {
            padding: 5px;
            font-size: 14px;
            color: #080808;
        }

        .custom-table td:first-child {
            font-weight: bold;
            width: 100px;
        }

        .signature-line {
            margin-top: 40px;
            font-size: 18px;
            font-weight: bold;
            text-align: left;
        }

        body {
            font-size: 20px;
            /* Adjust as needed */
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-size: 26px;
            /* Larger headers */
        }

        table {
            font-size: 18px;
            border-collapse: collapse;
            border: 2px solid black;
            /* Outer border */
        }

        p,
        td,
        th {
            font-size: 18px;

        }

        .btn {
            font-size: 20px;
        }


        .invoice-container {
            font-size: 23px;
            /* Increase font size for better readability */
        }

        h1,
        h2,
        h3 {
            font-size: 30px;
            /* Adjust header sizes */
        }

        table td,
        table th {
            border: 1px solid black;
            /* Inner cell borders */
            font-size: 18px;
            /* Increase table text size */
        }

        p {
            font-size: 20px !important;
        }

        @media print {
            .custom-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                text-align: left;
                font-size: 20px;
                /* Adjust text size for readability */
                border-top: 2px solid black;
                /* Add a separator */
                background-color: rgb(255, 255, 255);
                /* Ensure visibility */
            }

            .footer-container {
                display: flex;
                justify-content: left;
            }

            .footer-section {
                flex: 1;
                text-align: left;
            }

            /* Ensure content doesn't overlap with footer */
            body {
                margin-bottom: 100px;
                /* Give space for footer */
            }

            h1,
            h2,
            h3 {
                font-size: 36px;
            }

            p,
            td,
            th {
                font-size: 24px;
            }

            table td,
            table th {
                font-size: 20px;
                padding: 12px;
            }

            td,
            td {
                font-size: 25px;

            }

            ul,
            li {
                font-size: 25px;
            }
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
                    <h3 class="card-title">Delivery Challan</h3>

                </div>
                <div class="card-body">
                    <div class="row no-print">
                        <div class="col-12">
                            <a onclick="printInvoice()" class="btn btn-default float-right my-2">
                                <i class="fas fa-print"></i> Print
                            </a>

                        </div>
                    </div>
                </div>
            </div>
            <div class="invoice-container">
                <!-- Watermark -->

                <header class="header">
                    <div class="header-left">
                        <!-- Replace 'logo.png' with the actual path to your logo image -->

                        @if (isset($companyInfo->invoice_logo))
                            <a href="{{ route('home') }}">
                                <img width="200px" src="{{ asset('/backend/invoicelogo/' . $companyInfo->invoice_logo) }}"
                                    style="" alt="">
                            </a>
                        @endif
                    </div>
                    <div class="header-right">
                        <h1>{{ $companyInfo->company_name }}</h1>
                        <p>Value Adding is Our Business</p>
                    </div>
                </header>
                <div class="watermark">
                    @if (isset($companyInfo->invoice_logo))
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('/backend/invoicelogo/' . $companyInfo->invoice_logo) }}" style=""
                                alt="">
                        </a>
                    @endif
                </div>
                <section class="invoice-details px-3">
                    <div class="invoice-info">
                        <!-- Add the button here -->
                        <div class="pt-2" style="    padding: 0 355px;">
                            <div class="invoice-button">
                                <span style="font-size: 30px"><b> Delivery Challan </b></span>
                            </div>
                        </div>
                        <table class=" table-borderless " style="width: 100%;  border: none;">
                            <tr>
                                <td width="150px;"><strong>Date:</strong></td>
                                <td width="5px;"><strong>:</strong></td>
                                <td>{{ $invoice->date }}</td>
                            </tr>

                            <tr>
                                <td><strong>Challan No:</strong></td>
                                <td><strong> :</strong></td>
                                <td>{{ str_replace('SV', 'CN', $invoice->invoice_no ?? 'N/A') }}</td>
                            </tr>
                            <tr>
                                <td><strong>PO No:</strong></td>
                                <td><strong>:</strong></td>
                                <td>{{ $invoice->po_invoice }}</td>
                            </tr>
                            <tr>
                                <td><strong>PO Date:</strong></td>
                                <td><strong>:</strong></td>
                                <td>{{ $invoice->po_date }}</td>
                            </tr>
                        </table>
                    </div>
                </section>

                <section class="supplier-customer px-3">
                    <div class="supplier" style="    border-right: 1px solid;">
                        <h5>Supplied By:</h5>
                        <p><strong>{{ $companyInfo->company_name }}</strong></p>
                        <p><strong>Office Address:</strong> {{ $companyInfo->address }}
                        </p>
                        <p><strong>Factory & Warehouse:</strong> {{ $invoice->branch->address ?? '' }}</p>
                    </div>

                    <div class="customer">
                        <h5>Supplied For:</h5>
                        <p><strong>Customer:</strong>
                          {{ optional(optional($invoice->customer)->accountable)->co_name ?? ($invoice->customer->account_name ?? "") }}
                        </p>
                        <p><strong>Address:</strong>{{ optional(optional($invoice->customer)->accountable)->address ?? '' }}
                        </p>
                    </div>
                </section>

                <section class="product-table px-3">
                    <table>
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Product</th>
                                <th class="text-right">Unit</th>
                                <th class="text-right">Total Bag</th>
                                <th class="text-right">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalQty = 0;
                                $totalBag = 0;
                            @endphp
                            @foreach ($invoice->details as $detail)
                                @php
                                    $totalQty += $detail->qty;
                                    $totalBag += ($detail->qty ?? 0) / ($detail->product->box ?? 1);
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ optional($detail->product)->getRawOriginal("name") ?? 'N/A' }}</td>
                                    <td class="text-right">
                                        @php
                                            $unit = DB::table('products')
                                                ->select('unit_id', 'product_units.name')
                                                ->where('products.id', $detail->product_id)
                                                ->join('product_units', 'product_units.id', '=', 'products.unit_id')
                                                ->first();
                                        @endphp
                                        {{ $unit->name??"N/A" }}
                                    </td>
                                    <td class="text-right">{{ ($detail->qty ?? 0) / ($detail->product->box ?? 1) }}</td>
                                    <td class="text-right">{{ $detail->qty ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" style="text-align: right">TOTAL</th>
                                <th style="text-align: right">{{ $totalBag }}</th>
                                <th class="text-right">{{ $totalQty }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </section>

                <section class="attachments px-3">
                    <h3>Terms & Condition:</h3>
                    <ul>
                        <li>Product unload by factory.</li>
                        <li>Product Should be checked before received</li>
                        <li>Challan should be checked before received.</li>
                    </ul>
                </section>

                <div class="row table-borderless prepared-received-section">
                    <div class="col-md-6 prepared-by">
                        <h3>Prepared By:</h3>
                        <table class="custom-table" style="width: 100%; border-collapse: collapse; border: none;">
                            <tr>
                                <td style="font-size: 20px"><strong>Name</strong></td>
                                <td>:</td>
                                <td style="font-size: 20px">{{ $invoice->user->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 20px"> <strong>Mobile</strong></td>
                                <td>:</td>
                                <td style="font-size: 20px">{{ $invoice->user->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 20px"><strong>Mail</strong></td>
                                <td>:</td>
                                <td style="font-size: 20px">{{ $invoice->user->email ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6 received-by">
                        <h3>Received By:</h3>
                        <p class="signature-line"></p>
                    </div>
                </div>


                <footer class="custom-footer">
                    <div class="footer-container" >
                        <div class="footer-section" style="padding-right: 80px;">
                            <p> <b> Operational Headquarter: </b>{{ $companyInfo->address }} <b> Cell:</b>
                                {{ $companyInfo->phone }}</p>
                            <p></p>
                        </div>

                        <div class="footer-section" style="padding-right: 90px;">
                            <h5></h5>
                            <p><b>Factory Address: </b> Plot - 83/84, Nagori Bazar, Kaliganj,
                                Gazipur - 1720</p>
                            <p></p>
                        </div>

                        <div class="footer-section" style="text-align: start !important;">
                            <p>Website: <br>
                                <a href="{{ $companyInfo->website }}">{{ $companyInfo->website }}</a>
                            </p>
                            <p>Email: {{ $companyInfo->email }}</p>
                        </div>
                    </div>
                </footer>

            </div>
            <!-- /.col-->
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function printInvoice() {
            var printContents = document.querySelector('.invoice-container').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;

            location.reload(); // Reload to restore the original page
        }
    </script>
@endsection
