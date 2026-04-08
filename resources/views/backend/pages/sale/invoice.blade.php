@extends('backend.layouts.master')
@section('title')
    Sale - {{ $title }}
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Main content area that will push footer down */
        .invoice-content {
            flex: 1;
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
            border-bottom: 2px solid #000000;
            padding: 10px;
        }

        .header-left {
            width: 30%;
        }

        .header-left .logo {
            max-width: 100%;
            height: auto;
        }

        .header-right {
            width: 70%;
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
        }

        /* Button Styling */
        .invoice-button {
            display: block;
            width: 100%;
            padding: 5px;
            background-color: #0f752c;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            border: none;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .invoice-details,
        .supplier-customer,
        .product-table,
        .totals,
        .attachments {
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

        /* Default - no borders for most tables */
        table th,
        table td {
            border: none;
            padding: 8px;
            text-align: left;
        }

        /* Only product table gets borders */
        .product-table table th,
        .product-table table td {
            border: 1px solid black;
        }

        table th {
            background-color: #f4f4f4;
        }

        /* Remove borders from specific tables */
        .table-borderless,
        .table-borderless th,
        .table-borderless td {
            border: none !important;
        }

        .totals p,
        .attachments ul {
            margin-bottom: 10px;
        }

        .attachments ul {
            list-style-type: disc;
            padding-left: 20px;
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

        /* Footer - This is the key fix */
        .custom-footer {
            margin-top: auto;
            padding: 20px 0;
            text-align: center;
            border-top: 3px solid #007bff;
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
            font-size: 24px;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-size: 32px;
        }

        /* General table styles - no borders for info tables */
        table {
            font-size: 22px;
            border-collapse: collapse;
            border: none;
        }

        /* Product table specific borders */
        .product-table table {
            border: 2px solid black !important;
        }

        .product-table table td,
        .product-table table th {
            border: 1px solid black !important;
            font-size: 22px;
        }

        /* Remove borders from other tables */
        .table-borderless,
        .table-borderless td,
        .table-borderless th,
        .custom-table,
        .custom-table td,
        .custom-table th {
            border: none !important;
        }

        p,
        td,
        th {
            font-size: 22px;
        }

        .btn {
            font-size: 24px;
        }

        .invoice-container {
            font-size: 26px;
        }

        h1,
        h2,
        h3 {
            font-size: 34px;
        }

        p {
            font-size: 24px !important;
        }

        /* IMPROVED PRINT STYLES - Updated with no margins */
        @media print {

            /* Remove default margins and padding */
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            html,
            body {
                height: 100%;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 14px;
            }

            /* UPDATED: Set all margins to 0 for full page printing */
            @page {
                size: A4;
                margin: 0 !important;
                /* This removes all margins */
                /* Alternative options:
               margin: 0mm !important;
               margin-top: 0 !important;
               margin-bottom: 0 !important;
               margin-left: 0 !important;
               margin-right: 0 !important;
            */
            }

            /* Main container with proper flexbox layout */
            .invoice-container {
                min-height: 100vh;
                display: flex !important;
                flex-direction: column !important;
                margin: 0 !important;
                padding: 10px !important;
                /* Add small padding since we removed page margins */
                box-shadow: none !important;
                border: none !important;
                position: relative;
            }

            /* Content area that grows to fill space */
            .invoice-content {
                flex: 1 !important;
                display: flex;
                flex-direction: column;
            }

            /* Footer positioning - this ensures it stays at bottom */
            .custom-footer {
                margin-top: auto !important;
                page-break-inside: avoid !important;
                border-top: 2px solid black !important;
                padding: 15px 0 !important;
                text-align: center !important;
                background-color: white !important;
                position: relative !important;
                flex-shrink: 0 !important;
            }

            /* Alternative: Fixed footer at bottom of every page */
            .custom-footer.fixed-bottom {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                height: auto !important;
                background: white !important;
                border-top: 2px solid black !important;
                padding: 10px 15px !important;
                z-index: 1000 !important;
            }

            .footer-container {
                display: flex !important;
                justify-content: space-between !important;
                flex-wrap: wrap !important;
                max-width: 100% !important;
            }

            .footer-section {
                flex: 1 !important;
                text-align: center !important;
                min-width: 150px !important;
            }

            /* Prevent content from being cut off */
            .invoice-details,
            .supplier-customer,
            .product-table,
            .prepared-received-section,
            .attachments {
                page-break-inside: avoid !important;
                margin-bottom: 15px !important;
            }

            /* Table improvements for printing */
            table {
                page-break-inside: auto !important;
                border-collapse: collapse !important;
                width: 100% !important;
            }

            /* Only product table gets borders */
            .product-table table {
                border: 2px solid black !important;
            }

            /* Remove borders from all other tables */
            .table-borderless,
            .table-borderless td,
            .table-borderless th,
            .custom-table,
            .custom-table td,
            .custom-table th {
                border: none !important;
            }

            table tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }

            /* Font size adjustments for better print readability */
            h1,
            h2,
            h3 {
                font-size: 26px !important;
                margin-bottom: 8px !important;
            }

            p,
            td,
            th {
                font-size: 18px !important;
                line-height: 1.3 !important;
            }

            /* Product table with borders */
            .product-table table td,
            .product-table table th {
                font-size: 16px !important;
                padding: 6px !important;
                border: 1px solid black !important;
            }

            /* All other tables without borders */
            .table-borderless td,
            .table-borderless th,
            .custom-table td,
            .custom-table th {
                border: none !important;
                font-size: 16px !important;
                padding: 6px !important;
            }

            ul,
            li {
                font-size: 16px !important;
            }

            /* Hide print button and other non-essential elements */
            .no-print,
            .btn,
            .card-header,
            .card {
                display: none !important;
            }

            /* Watermark adjustments for print */
            .watermark {
                opacity: 0.03 !important;
                z-index: -1 !important;
            }

            /* Header adjustments */
            .header {
                margin-bottom: 15px !important;
                page-break-after: avoid !important;
            }

            .header-right h1 {
                font-size: 24px !important;
            }

            .header-right p {
                font-size: 14px !important;
            }

            /* Invoice button styling for print */
            .invoice-button {
                background-color: #0f752c !important;
                color: white !important;
                padding: 6px !important;
                margin: 8px auto !important;
                border-radius: 3px !important;
                width: 150px !important;
                text-align: center !important;
                font-size: 16px !important;
            }

            /* Prepared/Received section */
            .prepared-received-section {
                margin-top: 20px !important;
                page-break-inside: avoid !important;
                border-top: 2px solid #ddd !important;
                padding-top: 10px !important;
            }

            /* Attachments section */
            .attachments {
                page-break-inside: avoid !important;
                margin-bottom: 15px !important;
            }

            .attachments ul {
                margin-bottom: 10px !important;
            }

            /* Custom table styles */
            .custom-table td {
                font-size: 12px !important;
                padding: 3px !important;
            }
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Sale</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('sale.sale.index'))
                            <li class="breadcrumb-item"><a href="{{ route('sale.sale.index') }}">Sale</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Sale List</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default no-print">
                <div class="card-header">
                    <h3 class="card-title">Sales Invoice</h3>
                </div>
                <div class="card-body">
                    <div class="row">
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
                <div class="watermark">
                    @if (isset($companyInfo->invoice_logo))
                        <img src="{{ asset('/backend/invoicelogo/' . $companyInfo->invoice_logo) }}" alt="">
                    @endif
                </div>

                <!-- Main content wrapper -->
                <div class="invoice-content">
                    <header class="header">
                        <div class="header-left">
                            @if (isset($companyInfo->invoice_logo))
                                <a href="{{ route('home') }}">
                                    <img width="200px"
                                        src="{{ asset('/backend/invoicelogo/' . $companyInfo->invoice_logo) }}"
                                        alt="">
                                </a>
                            @endif
                        </div>
                        <div class="header-right">
                            <h1>{{ $companyInfo->company_name }}</h1>
                            <p>Value Adding is Our Business</p>
                        </div>
                    </header>

                    <section class="invoice-details px-3">
                        <div class="invoice-info">
                            <div class="pt-2" style="padding: 0 433px;">
                                <div class="invoice-button">
                                    <span style="font-size: 30px"><b>INVOICE</b></span>
                                </div>
                            </div>
                            <table class="table-borderless" style="width: 100%; border-collapse: collapse; border: none;">
                                <tr>
                                    <td width="150px;"><strong>Date:</strong></td>
                                    <td width="5px;"><strong>:</strong></td>
                                    <td>{{ $invoice->date }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Invoice No:</strong></td>
                                    <td><strong>:</strong></td>
                                    <td>{{ $invoice->invoice_no ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Challan No:</strong></td>
                                    <td><strong>:</strong></td>
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
                        <div class="supplier" style="border-right: 1px solid;">
                            <h5>Supplied By:</h5>
                            <p><strong>{{ $companyInfo->company_name }}</strong></p>
                            <p><strong>Office Address:</strong> {{ $companyInfo->address }}</p>
                            <p><strong>Factory & Warehouse:</strong> {{ $invoice->branch->address ?? '' }}</p>
                        </div>

                        <div class="customer">
                            <h5>Supplied For:</h5>
                            <p><strong>Customer:</strong>
                                {{ optional(optional($invoice->customer)->accountable)->co_name ?? ($invoice->customer->account_name ?? '') }}
                            </p>
                            <p><strong>Address:</strong>
                                {{ optional(optional($invoice->customer)->accountable)->address ?? '' }}</p>
                        </div>
                    </section>

                    <section class="product-table no-page-break px-3">
                        <table>
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Vat</th>
                                    <th>Unit Price (Tk)</th>
                                    <th>Total Price (Tk)</th>
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
                                        <td>{{ $detail->product->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $detail->qty ?? 'N/A' }}
                                            {{ optional(optional(optional($detail)->product)->unit)->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $detail->vat ?? '0' }}</td>
                                        <td class="text-right">{{ number_format($detail->rate, 2) ?? '0' }}</td>
                                        <td class="text-right">{{ number_format($detail->price, 2) ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="1" style="text-align: center">TOTAL</th>
                                    <th class="text-right"></th>
                                    <th class="text-center">{{ number_format($totalQty, 2) }}</th>
                                    <th class="text-right"></th>
                                    <th class="text-right"></th>
                                    <!--{{ number_format($totalUp, 2) }}-->
                                    <th class="text-right"></th>
                                    <!--{{ $subtotal }}-->
                                </tr>
                                @if ($invoice->discount)
                                    <tr>
                                        <td colspan="2"></td>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th style="text-align:right;">Discount ( - ):</th>
                                        <td style="text-align:right">
                                            <b>{{ number_format($invoice->discount, 2) ?? 0 }}</b>
                                        </td>
                                    </tr>
                                @endif
                                @if ($invoice->carrying_cost)
                                    <tr>
                                        <td colspan="2"></td>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th style="text-align:right;">Carrying Cost ( + ):</th>
                                        <td style="text-align:right">
                                            <b>{{ number_format($invoice->carrying_cost, 2) ?? 0 }}</b>
                                        </td>
                                    </tr>
                                @endif
                                @if ($invoice->labor_bill)
                                    <tr>
                                        <td colspan="2"></td>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th style="text-align:right;">Labor bill ( + ):</th>
                                        <td style="text-align:right">
                                            <b>{{ number_format($invoice->labor_bill, 2) ?? 0 }}</b>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="2"></td>
                                    <th class="text-right"></th>
                                    <th class="text-right"></th>
                                    <th style="text-align:right;">Total Payable Amount:</th>
                                    <td style="text-align:right">
                                        <b>{{ number_format($invoice->net_total, 2) ?? 0 }}</b>
                                    </td>
                                </tr>
                                <tr>


                                    <th colspan="6">
                                        <b>In Words :</b>
                                        {{ numberToWords((int) str_replace(',', '', $invoice->net_total)) }}
                                    </th>

                                </tr>
                                <tr>
                                    <td colspan="6"><b>Note :</b> {{ $invoice->narration ?? 'N/A' }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </section>

                    <section class="attachments px-3">
                        <h3>Attachments:</h3>
                        <ul>
                            <li>Purchase Order</li>
                            <li>Received Challan</li>
                            <li>Beneficiary's Bank Information as Following: {{ $companyInfo->company_name }}</li>
                        </ul>
                    </section>

                    <div class="row table-borderless prepared-received-section no-page-break">
                        <div class="col-md-6 prepared-by">
                            <h3>Prepared By:</h3>
                            <table class="custom-table" style="width: 100%; border-collapse: collapse; border: none;">
                                <tr>
                                    <td><strong>Name</strong></td>
                                    <td>:</td>
                                    <td>{{ $invoice->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mobile</strong></td>
                                    <td>:</td>
                                    <td>{{ $invoice->user->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mail</strong></td>
                                    <td>:</td>
                                    <td>{{ $invoice->user->email ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6 received-by">
                            <h3>Received By:</h3>
                            <p class="signature-line"></p>
                        </div>
                    </div>
                </div>

                <!-- Footer - This will stick to bottom -->
                <footer class="custom-footer">
                    <div class="footer-container">
                        <div class="footer-section" style="padding-right: 80px;">
                            <p><b>Operational Headquarter:</b> {{ $companyInfo->address }} <b>Cell:</b>
                                {{ $companyInfo->phone }}</p>
                        </div>

                        <div class="footer-section" style="padding-right: 90px;">
                            <p><b>Factory Address:</b> Plot - 83/84, Nagori Bazar, Kaliganj, Gazipur - 1720</p>
                        </div>

                        <div class="footer-section" style="text-align: start !important;">
                            <p>Website: <br><a href="{{ $companyInfo->website }}">{{ $companyInfo->website }}</a></p>
                            <p>Email: {{ $companyInfo->email }}</p>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function printInvoice() {
            // Hide non-print elements
            const noPrintElements = document.querySelectorAll('.no-print');
            noPrintElements.forEach(element => {
                element.style.display = 'none';
            });

            // Trigger print
            window.print();

            // Restore hidden elements after print
            setTimeout(() => {
                noPrintElements.forEach(element => {
                    element.style.display = '';
                });
            }, 100);
        }

        // Alternative print function that isolates the invoice content
        function printInvoiceIsolated() {
            const printWindow = window.open('', '_blank');
            const invoiceContent = document.querySelector('.invoice-container').outerHTML;
            const styles = document.querySelector('style').outerHTML;

            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Invoice Print</title>
                    ${styles}
                </head>
                <body>
                    ${invoiceContent}
                </body>
                </html>
            `);

            printWindow.document.close();
            printWindow.focus();

            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }
    </script>
@endsection
