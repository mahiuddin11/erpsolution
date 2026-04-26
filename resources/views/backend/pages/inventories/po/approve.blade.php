@extends('backend.layouts.master')
@section('title')
    inventory - {{ $title }}
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
                        <li class="breadcrumb-item active"><span>Purchase Order</span></li>
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
                    <h3 class="card-title">Purchase Order Invoice</h3>

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
                                <b>Date : {{ $purchaseorder->order_date ?? 'N/A' }} </b><br>
                                <b>Invoice : {{ $purchaseorder->invoice_no ?? 'N/A' }} </b><br>
                                <b> Project :</b> {{ $purchaseorder->project->name ?? 'N/A' }} <br>
                                <b> Manager :</b> {{ $purchaseorder->project->manager->name ?? 'N/A' }}<br>
                            </div>
                            <!-- /.col -->
                        </div><br>
                        <!-- /.row -->
                        {{-- @php
                            $purchaseOrderId = $purchaseorder->details->pluck('id')->toArray();
                            $supplierSelected = App\Models\SupplierSelectPrice::whereIn('purchase_order_id', $purchaseOrderId);
                            $supplierSelectedId = $supplierSelected->pluck('supplier_id')->toArray();
                            $supplierSelectedPrices = $supplierSelected->get();
                            $suppliers = App\Models\Supplier::whereIn('id', $supplierSelectedId)->get();
                            
                        @endphp --}}

                        @php
                            $purchaseOrderId = $purchaseorder->details->pluck('id')->toArray();
                            $supplierSelectedPrices = App\Models\SupplierSelectPrice::whereIn(
                                'purchase_order_id',
                                $purchaseOrderId,
                            )->get(); // new add
                            $supplierIds = $supplierSelectedPrices->pluck('supplier_id')->filter()->unique(); // new add
                            $accountIds = $supplierSelectedPrices->pluck('account_id')->filter()->unique(); // new add
                            $suppliers = App\Models\Supplier::whereIn('id', $supplierIds)->get(); // updated
                            $accounts = App\Models\ChartOfAccount::whereIn('id', $accountIds)->get(); // new add
                        @endphp


                        <!-- Table row -->
                        <form action="{{ route('inventorySetup.supplierpurchaseorder.approve') }}" method="post">
                            @csrf
                            <input type="hidden" value="{{ $purchaseorder->id }}" name="purchase_order">
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Product</th>
                                                <th>Type</th>
                                                <th class="text-right">Qty</th>

                                                @if ($suppliers->count() > 0)
                                                    {{-- new add --}}
                                                    @foreach ($suppliers as $supplier)
                                                        <th class="text-right">{{ $supplier->name ?? 'N/A' }}</th>
                                                    @endforeach
                                                @endif

                                                @if ($accounts->count() > 0)
                                                    {{-- new add --}}
                                                    @foreach ($accounts as $account)
                                                        <th class="text-right">{{ $account->account_name ?? 'N/A' }}</th>
                                                    @endforeach
                                                @endif


                                                <th class="text-right">Total</th> <!-- Add Total column -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalQty = 0;
                                                $totalUp = 0;
                                                $totalPrice = 0;
                                                $totals = []; // new add: supplier/account totals
                                                $overallTotal = 0; // new add: overall total
                                            @endphp

                                            @foreach ($purchaseorder->details as $detail)
                                                @php
                                                    $totalQty += $detail->qty;
                                                    $totalUp += $detail->unit_price;
                                                    $totalPrice += $detail->total_price;
                                                    $rowTotal = 0; // total for this row
                                                @endphp

                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $detail->product->productCode ?? 'N/A' }} -
                                                        {{ $detail->product->name ?? 'N/A' }}</td>
                                                    <td class="text-right">{{ $detail->purchasetype ?? 'N/A' }}</td>
                                                    <td class="text-right">{{ $detail->qty ?? 'N/A' }}</td>

                                                    {{-- Loop through suppliers first --}}
                                                    @foreach ($suppliers as $supplier)
                                                        <td class="text-right">
                                                            @php
                                                                $found = false; // new add: to track if price found
                                                            @endphp
                                                            @foreach ($supplierSelectedPrices as $price)
                                                                @if ($price->purchase_order_id == $detail->id && $price->supplier_id == $supplier->id)
                                                                    @php
                                                                        $found = true; // mark found
                                                                        if (!isset($totals[$supplier->id])) {
                                                                            $totals[$supplier->id] = 0;
                                                                        } // init total
                                                                        if ($price->purchases_price > 0) {
                                                                            $totals[$supplier->id] +=
                                                                                $price->purchases_price;
                                                                        } // add to total
                                                                        if ($price->status == 1) {
                                                                            $rowTotal +=
                                                                                $detail->qty * $price->purchases_price;
                                                                        } // add row total
                                                                    @endphp
                                                                    <span class="mr-2">
                                                                        <input type="checkbox"
                                                                            onclick="unCheck({{ $detail->id }})"
                                                                            class="checked-input{{ $detail->id }}"
                                                                            {{ $price->status == 1 ? 'checked' : '' }}
                                                                            value="{{ $price->id }}"
                                                                            name="suplirePrice[]">
                                                                    </span>
                                                                    {{ $price->purchases_price }}
                                                                @endif
                                                            @endforeach

                                                            @if (!$found)
                                                                <span>N/A</span>
                                                            @endif
                                                        </td>
                                                    @endforeach

                                                    {{-- Loop through accounts (new add) --}}
                                                    @foreach ($accounts as $account)
                                                        {{-- new add --}}
                                                        <td class="text-right"> {{-- new add --}}
                                                            @php
                                                                $found = false; // new add
                                                            @endphp
                                                            @foreach ($supplierSelectedPrices as $price)
                                                                @if ($price->purchase_order_id == $detail->id && $price->account_id == $account->id)
                                                                    {{-- new add --}}
                                                                    @php
                                                                        $found = true; // new add
                                                                        if (!isset($totals['acc_' . $account->id])) {
                                                                            $totals['acc_' . $account->id] = 0;
                                                                        } // new add
                                                                        if ($price->purchases_price > 0) {
                                                                            $totals['acc_' . $account->id] +=
                                                                                $price->purchases_price;
                                                                        } // new add
                                                                        if ($price->status == 1) {
                                                                            $rowTotal +=
                                                                                $detail->qty * $price->purchases_price;
                                                                        } // new add
                                                                    @endphp
                                                                    <span class="mr-2">
                                                                        <input type="checkbox"
                                                                            onclick="unCheck({{ $detail->id }})"
                                                                            class="checked-input{{ $detail->id }}"
                                                                            {{ $price->status == 1 ? 'checked' : '' }}
                                                                            value="{{ $price->id }}"
                                                                            name="suplirePrice[]">
                                                                    </span>
                                                                    {{ $price->purchases_price }}
                                                                @endif
                                                            @endforeach

                                                            @if (!$found)
                                                                {{-- new add --}}
                                                                <span>N/A</span> {{-- new add --}}
                                                            @endif
                                                        </td> {{-- new add --}}
                                                    @endforeach

                                                    <td class="text-right">
                                                        <strong>{{ number_format($rowTotal, 2) }}</strong>
                                                    </td>
                                                    @php
                                                        $overallTotal += $rowTotal; // new add
                                                    @endphp
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                                <td class="text-right"><strong>{{ $totalQty }}</strong></td>

                                                {{-- Supplier totals --}}
                                                @foreach ($suppliers as $supplier)
                                                    <td class="text-right">
                                                        <strong>{{ number_format($totals[$supplier->id] ?? 0, 2) }}</strong>
                                                        {{-- new add --}}
                                                    </td>
                                                @endforeach

                                                {{-- Account totals --}}
                                                @foreach ($accounts as $account)
                                                    {{-- new add --}}
                                                    <td class="text-right">
                                                        <strong>{{ number_format($totals['acc_' . $account->id] ?? 0, 2) }}</strong>
                                                        {{-- new add --}}
                                                    </td>
                                                @endforeach

                                                {{-- Overall total --}}
                                                <td class="text-right">
                                                    <strong>{{ number_format($overallTotal, 2) }}</strong>
                                                    {{-- new add --}}
                                                </td>
                                            </tr>

                                            {{-- Narration row --}}
                                            <tr>
                                                <td colspan="{{ 5 + count($suppliers) + count($accounts) }}">
                                                    Narration: {{ $purchaseorder->note ?? 'N/A' }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                        {{-- <tbody>
                                            @php
                                                $totalQty = 0;
                                                $totalUp = 0;
                                                $totalPrice = 0;
                                                $supplierTotals = []; // Initialize supplier totals array
                                                $overallTotal = 0; // Initialize overall total for all rows
                                            @endphp

                                            @foreach ($purchaseorder->details as $detail)
                                                @php
                                                    $totalQty += $detail->qty;
                                                    $totalUp += $detail->unit_price;
                                                    $totalPrice += $detail->total_price;
                                                    $rowTotal = 0; // Initialize row total for each product
                                                @endphp

                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $detail->product->productCode ?? 'N/A' }} -
                                                        {{ $detail->product->name ?? 'N/A' }}</td>
                                                    <td class="text-right">{{ $detail->purchasetype ?? 'N/A' }}</td>
                                                    <td class="text-right">{{ $detail->qty ?? 'N/A' }}</td>

                                                    @foreach ($suppliers as $supplier)
                                                        <td class="text-right">
                                                            @php
                                                                $supplierPriceFound = false; // To check if supplier price exists
                                                            @endphp
                                                            @foreach ($supplierSelectedPrices as $supplierPrice)
                                                                @if ($supplier->id == $supplierPrice->supplier_id && $detail->id == $supplierPrice->purchase_order_id)
                                                                    @php
                                                                        $supplierPriceFound = true; // Mark that supplier price is found

                                                                        // Initialize supplier total if not already set
                                                                        if (!isset($supplierTotals[$supplier->id])) {
                                                                            $supplierTotals[$supplier->id] = 0;
                                                                        }

                                                                        // Add supplier price to the total only if checked
                                                                        if ($supplierPrice->purchases_price > 0) {
                                                                            $supplierTotals[$supplier->id] +=
                                                                                $supplierPrice->purchases_price;
                                                                        }

                                                                        // Calculate row total for checked items (Qty * Price)
                                                                        if ($supplierPrice->status == 1) {
                                                                            $rowTotal +=
                                                                                $detail->qty *
                                                                                $supplierPrice->purchases_price;
                                                                        }
                                                                    @endphp
                                                                    <span class="mr-2">
                                                                        <input type="checkbox"
                                                                            onclick="unCheck({{ $detail->id }})"
                                                                            class="checked-input{{ $detail->id }}"
                                                                            {{ $supplierPrice->status == 1 ? 'checked' : '' }}
                                                                            value="{{ $supplierPrice->id }}"
                                                                            name="suplirePrice[]">
                                                                    </span>

                                                                    {{ $supplierPrice->purchases_price }}
                                                                @endif
                                                            @endforeach

                                                            @if (!$supplierPriceFound)
                                                                <span>N/A</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                    <td class="text-right">
                                                        <strong>{{ number_format($rowTotal, 2) }}</strong>
                                                    </td>
                                                    <!-- Display row total -->
                                                    @php
                                                        $overallTotal += $rowTotal; // Add row total to the overall total
                                                    @endphp
                                                </tr>
                                            @endforeach
                                        </tbody> --}}
                                        {{-- <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                                <td class="text-right"><strong>{{ $totalQty }}</strong></td>
                                                @foreach ($suppliers as $supplier)
                                                    <td class="text-right">
                                                        <strong>{{ number_format($supplierTotals[$supplier->id] ?? 0, 2) }}</strong>
                                                    </td>
                                                @endforeach
                                                <td class="text-right">
                                                    <strong>{{ number_format($overallTotal, 2) }}</strong>
                                                </td>
                                                <!-- Total sum of all row totals -->
                                            </tr>
                                            <tr>
                                                <td colspan="{{ count($suppliers) + 5 }}">Narration:
                                                    {{ $purchaseorder->note ?? 'N/A' }}</td>
                                            </tr>
                                        </tfoot> --}}
                                    </table>



                                </div>

                                <div class="col-lg-3">
                                    <button class="btn btn-success mb-3">Submit</button>
                                </div>


                                {{-- <div class="col-md-4 text-center float-left">
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
                                </div> --}}

                                <hr>


                                <div class="col-md-12 bg-success" style="text-align: center">
                                    Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} products.
                                    We believe you will be satisfied by our services.
                                </div>
                                <!-- /.col -->
                                <!-- /.col -->
                            </div>
                        </form>
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
    <script>
        $(document).ready(function() {

        });

        function unCheck(id) {
            inputClass = '.checked-input' + id;
            var inputs = $(inputClass);
            inputs.change(function() {
                inputs.not(this).prop("checked", false);
            });
        }
    </script>
@endsection
