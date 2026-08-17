@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }

        .clickable-row {
            cursor: pointer;
        }

        .clickable-row:hover {
            background-color: #f8f9fa;
        }

        .modal-body .table-sm th,
        .modal-body .table-sm td {
            padding: 0.4rem;
        }

        @media print {
            .modal {
                display: none !important;
            }
        }

        @media (max-width: 767px) {

            .invoice table.table-bordered td,
            .invoice table.table-bordered th {
                font-size: 12px;
                padding: 0.4rem;
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
                        Report </h1>
                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">

        <div class="col-md-12">
            @if ($errors->any())
                <div class="card">
                    <div class="card-body">
                        <div class="text-danger">
                            <h4><i style="color:rgb(255, 0, 0)" class="fa fa-regular fa-bell"></i> {{ $errors->first() }}
                            </h4>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('report.project.project') }}" method="POST" enctype="multipart/form-data">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Project </label>
                                    <select class="form-control select2 " name="project_id">
                                        <option value="0" selected>Select A Project</option>
                                        @foreach ($project as $key => $value)
                                            <option {{ $project_id == $value->id ? 'selected' : '' }}
                                                value="{{ $value->id }}">
                                                {{ $value->projectCode . ' - ' . $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <span class="error text-red text-bold"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
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
            $productAmount = 0;
        @endphp
        @if (isset($projectDetails) && !empty($projectDetails))
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header no-print">
                        <h3 class="card-title">Project Report</h3>
                        <a onclick="window.print()" target="_blank" class="btn btn-default float-right my-2 no-print"><i
                                class="fas fa-print"></i>
                            Print</a>
                        <div id="tableActions" class=" float-right my-2 no-print"></div>
                    </div>

                    <div class="card-body">

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
                                                            style="" alt="">
                                                    </a>
                                                @endif
                                            </td>
                                            <td style="text-align: center">
                                                <h3>Project Details Report</h3>
                                                <h6><b>Project Name : </b>
                                                    {{ $projectDetails->projectCode . ' - ' . $projectDetails->pname }}<br>
                                                    <b>Project Address : </b> {{ $projectDetails->address }}<br>
                                                    <b>Manager Name : </b> {{ $projectDetails->aname }}<br>
                                                    <b>Manager Phone : </b> {{ $projectDetails->aphone }}
                                                </h6>
                                            </td>
                                            <td>
                                                <h6>
                                                    <b>Project budget : </b> TK. {{ $projectDetails->budget }}<br>
                                                    <b>Project Start :</b> {{ $projectDetails->start_date }}<br>
                                                    <b>Project End :</b> {{ $projectDetails->end_date }}
                                                    @if ($projectDetails->closing > $projectDetails->end_date)
                                                        / <b style="color: red">{{ $projectDetails->closing }}</b>
                                                    @else
                                                        / <b style="color: green">{{ $projectDetails->closing }}</b>
                                                    @endif

                                                    <br>
                                                    <b>Status :</b>
                                                    @if ($projectDetails->condition == 'Complete')
                                                        <button
                                                            class="btn-success">{{ $projectDetails->condition }}</button>
                                                    @else
                                                        <button
                                                            class="btn-warning">{{ $projectDetails->condition }}</button>
                                                    @endif

                                                    <br>
                                                </h6>

                                            </td>
                                        </tr>
                                    </table>

                                    {{-- >>> NEW: Products details - product wise grouped with modal --}}
                                    @php
                                        $productRows = collect();

                                        foreach ($productgoodreceive as $val) {
                                            foreach ($val->details as $eachuse) {
                                                $lineAmount = $eachuse->unit_price * $eachuse->qty;
                                                $productRows->push([
                                                    'product_id' => $eachuse->product_id,
                                                    'pname' =>
                                                        ($eachuse->product->productCode ?? 'N/A') .
                                                        ' - ' .
                                                        ($eachuse->product->name ?? ''),
                                                    'date' => $val->date,
                                                    'invoice_no' => $val->invoice_no,
                                                    'qty' => $eachuse->qty,
                                                    'amount' => $lineAmount,
                                                    'label' => 'GRN',
                                                    'sign' => 1,
                                                ]);
                                            }
                                        }

                                        foreach ($projectTransfer as $val) {
                                            foreach ($val->details as $eachuse) {
                                                $unitPrice = $eachuse->unit_price ?? 0;

                                                if ($unitPrice <= 0) {
                                                    $purchase = App\Models\PurchasesDetails::whereMonth(
                                                        'date',
                                                        date('m', strtotime($val->order_date)),
                                                    )
                                                        ->where('product_id', $eachuse->product_id)
                                                        ->latest('id')
                                                        ->first();

                                                    if (!$purchase) {
                                                        $purchase = App\Models\PurchasesDetails::where(
                                                            'product_id',
                                                            $eachuse->product_id,
                                                        )
                                                            ->latest('id')
                                                            ->first();
                                                    }

                                                    $unitPrice = $purchase->unit_price ?? 0;
                                                }

                                                $lineAmount = $unitPrice * $eachuse->qty;

                                                if ($val->transfer_type == 'branch_to_project') {
                                                    $sourceName =
                                                        $transferBranchNames[$val->branch_id] ??
                                                        ($transferBranchNames[$val->warehouse_id] ?? 'N/A');
                                                    $transferSign = 1;
                                                    $transferLabel = 'Transfer In from ' . $sourceName;
                                                } elseif ($val->transfer_type == 'project_to_project') {
                                                    if ((int) $val->project_id === (int) $project_id) {
                                                        $destName = $transferProjectNames[$val->to_project_id] ?? 'N/A';
                                                        $transferSign = -1;
                                                        $transferLabel = 'Transfer Out to ' . $destName;
                                                    } else {
                                                        $srcName = $transferProjectNames[$val->project_id] ?? 'N/A';
                                                        $transferSign = 1;
                                                        $transferLabel = 'Transfer In from ' . $srcName;
                                                    }
                                                } elseif ($val->transfer_type == 'project_to_branch') {
                                                    $destName =
                                                        $transferBranchNames[$val->branch_id] ??
                                                        ($transferBranchNames[$val->warehouse_id] ?? 'N/A');
                                                    $transferSign = -1;
                                                    $transferLabel = 'Transfer Out to ' . $destName;
                                                } else {
                                                    $transferSign = 1;
                                                    $transferLabel = 'Transfer';
                                                }

                                                $productRows->push([
                                                    'product_id' => $eachuse->product_id,
                                                    'pname' =>
                                                        ($eachuse->product->productCode ?? 'N/A') .
                                                        ' - ' .
                                                        ($eachuse->product->name ?? ''),
                                                    'date' => $val->order_date,
                                                    'invoice_no' => $val->invoice_no,
                                                    'qty' => $transferSign < 0 ? -$eachuse->qty : $eachuse->qty,
                                                    'amount' => $transferSign * $lineAmount,
                                                    'label' => $transferLabel,
                                                    'sign' => $transferSign,
                                                ]);
                                            }
                                        }

                                        $productAmount = $productRows->sum('amount');
                                        $groupedProducts = $productRows->groupBy('product_id');
                                    @endphp

                                    <div class="table-responsive">
                                        <table id="datatablexcel" class="table  table-striped  table-bordered">
                                            <thead>
                                                <tr>
                                                    <td colspan="5"><b>
                                                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                                                        </b></td>
                                                </tr>
                                            </thead>

                                            @if ($groupedProducts->isNotEmpty())
                                                <tbody>
                                                    <tr>
                                                        <td colspan="5">
                                                            <b>
                                                                <i class="fa fa-bullseye" aria-hidden="true"></i>
                                                                Products details
                                                            </b>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th style="text-align: right;">Total Qty</th>
                                                        <th style="text-align: right;">Amount</th>
                                                        <th class="no-print" style="width: 90px; text-align: center;">
                                                            Details</th>
                                                    </tr>

                                                    @foreach ($groupedProducts as $prodId => $group)
                                                        @php
                                                            $groupQty = $group->sum('qty');
                                                            $groupAmount = $group->sum('amount');
                                                            $prodName = $group->first()['pname'];
                                                        @endphp
                                                        <tr class="clickable-row" data-toggle="modal"
                                                            data-target="#productModal{{ $prodId }}">
                                                            <td>{{ $prodName }}</td>
                                                            <td style="text-align: right;">{{ $groupQty }}</td>
                                                            <td style="text-align: right;"
                                                                class="{{ $groupAmount < 0 ? 'text-danger' : '' }}">
                                                                {{ $groupAmount < 0 ? '(' . number_format(abs($groupAmount), 2) . ')' : number_format($groupAmount, 2) }}
                                                            </td>
                                                            <td class="no-print text-center">
                                                                <i class="fa fa-eye"></i> View
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                    <tr>
                                                        <th colspan="2" style="text-align: center;">Total Uses</th>
                                                        <th style="text-align: right;">
                                                            {{ number_format($productAmount, 2) }}</th>
                                                        <th class="no-print"></th>
                                                    </tr>
                                                </tbody>
                                            @endif
                                        </table>
                                    </div>

                                    {{-- Modals: one per product --}}
                                    @foreach ($groupedProducts as $prodId => $group)
                                        <div class="modal fade no-print" id="productModal{{ $prodId }}"
                                            tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ $group->first()['pname'] }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Date</th>
                                                                        <th>Invoice No</th>
                                                                        <th>Detail</th>
                                                                        <th style="text-align: right;">Qty</th>
                                                                        <th style="text-align: right;">Amount</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($group as $row)
                                                                        <tr>
                                                                            <td>{{ $row['date'] }}</td>
                                                                            <td>{{ $row['invoice_no'] }}</td>
                                                                            <td>
                                                                                <small
                                                                                    class="{{ $row['sign'] < 0 ? 'text-danger' : 'text-success' }}">
                                                                                    {{ $row['label'] }}
                                                                                </small>
                                                                            </td>
                                                                            <td style="text-align: right;">
                                                                                {{ $row['qty'] }}</td>
                                                                            <td style="text-align: right;"
                                                                                class="{{ $row['sign'] < 0 ? 'text-danger' : '' }}">
                                                                                {{ $row['sign'] < 0 ? '(' . number_format(abs($row['amount']), 2) . ')' : number_format($row['amount'], 2) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <th colspan="3" style="text-align: right;">
                                                                            Total</th>
                                                                        <th style="text-align: right;">
                                                                            {{ $group->sum('qty') }}</th>
                                                                        <th style="text-align: right;">
                                                                            {{ number_format($group->sum('amount'), 2) }}
                                                                        </th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default"
                                                            data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    {{-- <<< END NEW --}}

                                    {{-- >>> NEW: Direct Income - category wise grouped with modal --}}
                                    @php
                                        $directIncomeGrouped = $directIncome
                                            ? $directIncome->groupBy('account_id')
                                            : collect();
                                    @endphp
                                    @if ($directIncomeGrouped->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3">
                                                            <b><i class="fa fa-bullseye" aria-hidden="true"></i> Direct
                                                                Income details</b>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Category</th>
                                                        <th style="text-align:right;">Amount</th>
                                                        <th class="no-print" style="width:90px; text-align:center;">
                                                            Details</th>
                                                    </tr>
                                                    @php $ttlexpdirinc = 0; @endphp
                                                    @foreach ($directIncomeGrouped as $accId => $group)
                                                        @php
                                                            $catTotal = $group->sum('credit');
                                                            $ttlexpdirinc += $catTotal;
                                                            $catName =
                                                                optional($group->first()->account)->account_name ??
                                                                'N/A';
                                                        @endphp
                                                        <tr class="clickable-row" data-toggle="modal"
                                                            data-target="#dirIncModal{{ $accId }}">
                                                            <td>{{ $catName }}</td>
                                                            <td style="text-align:right;">
                                                                {{ number_format($catTotal, 2) }}</td>
                                                            <td class="no-print text-center"><i class="fa fa-eye"></i>
                                                                View</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <th colspan="2" style="text-align:center;">Total Direct Income
                                                        </th>
                                                        <th style="text-align:right;">
                                                            {{ number_format($ttlexpdirinc, 2) }}</th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        @foreach ($directIncomeGrouped as $accId => $group)
                                            <div class="modal fade no-print" id="dirIncModal{{ $accId }}"
                                                tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-scrollable"
                                                    role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                {{ optional($group->first()->account)->account_name ?? 'N/A' }}
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th style="text-align:right;">Amount</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($group as $echexp)
                                                                            <tr>
                                                                                <td>{{ $echexp->created_at }}</td>
                                                                                <td style="text-align:right;">
                                                                                    {{ number_format($echexp->credit, 2) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <th style="text-align:right;">Total</th>
                                                                            <th style="text-align:right;">
                                                                                {{ number_format($group->sum('credit'), 2) }}
                                                                            </th>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    {{-- <<< END NEW --}}

                                    {{-- >>> NEW: Indirect Income - category wise grouped with modal --}}
                                    @php
                                        $indirectIncomeGrouped = $indirectIncome
                                            ? $indirectIncome->groupBy('account_id')
                                            : collect();
                                    @endphp
                                    @if ($indirectIncomeGrouped->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3">
                                                            <b><i class="fa fa-bullseye" aria-hidden="true"></i>
                                                                Indirect Income details</b>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Category</th>
                                                        <th style="text-align:right;">Amount</th>
                                                        <th class="no-print" style="width:90px; text-align:center;">
                                                            Details</th>
                                                    </tr>
                                                    @php $ttlexpindrinc = 0; @endphp
                                                    @foreach ($indirectIncomeGrouped as $accId => $group)
                                                        @php
                                                            $catTotal = $group->sum('credit');
                                                            $ttlexpindrinc += $catTotal;
                                                            $catName =
                                                                optional($group->first()->account)->account_name ??
                                                                'N/A';
                                                        @endphp
                                                        <tr class="clickable-row" data-toggle="modal"
                                                            data-target="#indIncModal{{ $accId }}">
                                                            <td>{{ $catName }}</td>
                                                            <td style="text-align:right;">
                                                                {{ number_format($catTotal, 2) }}</td>
                                                            <td class="no-print text-center"><i class="fa fa-eye"></i>
                                                                View</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <th colspan="2" style="text-align:center;">Total Indirect
                                                            Income</th>
                                                        <th style="text-align:right;">
                                                            {{ number_format($ttlexpindrinc, 2) }}</th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        @foreach ($indirectIncomeGrouped as $accId => $group)
                                            <div class="modal fade no-print" id="indIncModal{{ $accId }}"
                                                tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-scrollable"
                                                    role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                {{ optional($group->first()->account)->account_name ?? 'N/A' }}
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th style="text-align:right;">Amount</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($group as $echexp)
                                                                            <tr>
                                                                                <td>{{ $echexp->created_at }}</td>
                                                                                <td style="text-align:right;">
                                                                                    {{ number_format($echexp->credit, 2) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <th style="text-align:right;">Total</th>
                                                                            <th style="text-align:right;">
                                                                                {{ number_format($group->sum('credit'), 2) }}
                                                                            </th>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    {{-- <<< END NEW --}}

                                    {{-- >>> NEW: Direct Expense - category wise grouped with modal --}}
                                    @php
                                        $directExpensesGrouped = $directExpenses
                                            ? $directExpenses->groupBy('account_id')
                                            : collect();
                                    @endphp
                                    @if ($directExpensesGrouped->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3">
                                                            <b><i class="fa fa-bullseye" aria-hidden="true"></i> Direct
                                                                Expense details</b>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Category</th>
                                                        <th style="text-align:right;">Amount</th>
                                                        <th class="no-print" style="width:90px; text-align:center;">
                                                            Details</th>
                                                    </tr>
                                                    @php $ttlexpdir = 0; @endphp
                                                    @foreach ($directExpensesGrouped as $accId => $group)
                                                        @php
                                                            $catTotal = $group->sum('debit');
                                                            $ttlexpdir += $catTotal;
                                                            $catName =
                                                                optional($group->first()->account)->account_name ??
                                                                'N/A';
                                                        @endphp
                                                        <tr class="clickable-row" data-toggle="modal"
                                                            data-target="#dirExpModal{{ $accId }}">
                                                            <td>{{ $catName }}</td>
                                                            <td style="text-align:right;">
                                                                {{ number_format($catTotal, 2) }}</td>
                                                            <td class="no-print text-center"><i class="fa fa-eye"></i>
                                                                View</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <th colspan="2" style="text-align:center;">Total Direct
                                                            Expense</th>
                                                        <th style="text-align:right;">
                                                            {{ number_format($ttlexpdir, 2) }}</th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        @foreach ($directExpensesGrouped as $accId => $group)
                                            <div class="modal fade no-print" id="dirExpModal{{ $accId }}"
                                                tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-scrollable"
                                                    role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                {{ optional($group->first()->account)->account_name ?? 'N/A' }}
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th style="text-align:right;">Amount</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($group as $echexp)
                                                                            <tr>
                                                                                <td>{{ $echexp->created_at }}</td>
                                                                                <td style="text-align:right;">
                                                                                    {{ number_format($echexp->debit, 2) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <th style="text-align:right;">Total</th>
                                                                            <th style="text-align:right;">
                                                                                {{ number_format($group->sum('debit'), 2) }}
                                                                            </th>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    {{-- <<< END NEW --}}

                                    {{-- >>> NEW: Indirect Expense - category wise grouped with modal --}}
                                    @php
                                        $indirectExpensesGrouped = $indirectExpenses
                                            ? $indirectExpenses->groupBy('account_id')
                                            : collect();
                                    @endphp
                                    @if ($indirectExpensesGrouped->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3">
                                                            <b><i class="fa fa-bullseye" aria-hidden="true"></i>
                                                                Indirect Expense details</b>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Category</th>
                                                        <th style="text-align:right;">Amount</th>
                                                        <th class="no-print" style="width:90px; text-align:center;">
                                                            Details</th>
                                                    </tr>
                                                    @php $ttlexpind = 0; @endphp
                                                    @foreach ($indirectExpensesGrouped as $accId => $group)
                                                        @php
                                                            $catTotal = $group->sum('debit');
                                                            $ttlexpind += $catTotal;
                                                            $catName =
                                                                optional($group->first()->account)->account_name ??
                                                                'N/A';
                                                        @endphp
                                                        <tr class="clickable-row" data-toggle="modal"
                                                            data-target="#indExpModal{{ $accId }}">
                                                            <td>{{ $catName }}</td>
                                                            <td style="text-align:right;">
                                                                {{ number_format($catTotal, 2) }}</td>
                                                            <td class="no-print text-center"><i class="fa fa-eye"></i>
                                                                View</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <th colspan="2" style="text-align:center;">Total Indirect
                                                            Expense</th>
                                                        <th style="text-align:right;">
                                                            {{ number_format($ttlexpind, 2) }}</th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        @foreach ($indirectExpensesGrouped as $accId => $group)
                                            <div class="modal fade no-print" id="indExpModal{{ $accId }}"
                                                tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-scrollable"
                                                    role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                {{ optional($group->first()->account)->account_name ?? 'N/A' }}
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th style="text-align:right;">Amount</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($group as $echexp)
                                                                            <tr>
                                                                                <td>
                                                                                    {{ date('Y-m-d', strtotime($echexp->created_at)) }}
                                                                                </td>
                                                                                <td style="text-align:right;">
                                                                                    {{ number_format($echexp->debit, 2) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <th style="text-align:right;">Total</th>
                                                                            <th style="text-align:right;">
                                                                                {{ number_format($group->sum('debit'), 2) }}
                                                                            </th>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    {{-- <<< END NEW --}}

                                    @if ($projectMoney)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3">
                                                            <b>
                                                                <i class="fa fa-bullseye" aria-hidden="true"></i>
                                                                Project Money
                                                            </b>
                                                        </td>
                                                    <tr>
                                                        <th>Total</th>
                                                        <th style="text-align: right;">{{ $projectMoney }}</th>
                                                    </tr>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-lg-6 ">
                                            <div class="card card-danger">
                                                <div class="card-header">
                                                    <h3 class="card-title">Progress Report</h3>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="pieChart"
                                                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                                </div>
                                                <div class="card-body d-none">
                                                    <canvas id="donutChart"
                                                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                                </div>
                                                <!-- /.card-body -->
                                            </div>
                                        </div>

                                        <div class="col-lg-6 ">
                                            <div class="card card-danger">
                                                <div class="card-header">
                                                    <h3 class="card-title">Profit and Loss</h3>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="myChart"></canvas>
                                                </div>
                                                <!-- /.card-body -->
                                            </div>
                                        </div>

                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td colspan="3">
                                                        <b>
                                                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                                                            Full Project Summary
                                                        </b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">
                                                        <b>
                                                            A . Income
                                                        </b>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th>Sale Value</th>
                                                    <th style="text-align: right;">{{ $projectDetails->budget ?? 0 }}
                                                    </th>
                                                </tr>

                                                <tr>
                                                    <th>Indirect Income </th>
                                                    <th style="text-align: right;">{{ $ttlexpindrinc ?? 0 }}</th>
                                                </tr>

                                                <tr>
                                                    <th>Direct Income </th>
                                                    <th style="text-align: right;">{{ $ttlexpdirinc ?? 0 }}</th>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">
                                                        <b>
                                                            B . Expenses
                                                        </b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Direct Expenses </th>
                                                    <th style="text-align: right;">{{ $ttlexpdir ?? 0 }}</th>
                                                </tr>
                                                <tr>
                                                    <th>Indirect Expenses </th>
                                                    <th style="text-align: right;">{{ $ttlexpind ?? 0 }}</th>
                                                </tr>
                                                <tr>
                                                    <th>Total Product Consumption </th>
                                                    <th style="text-align: right;">{{ $productAmount }}</th>
                                                </tr>
                                                <tr>
                                                    <th>Total Profit (A - B)</th>
                                                    <th style="text-align: right;">
                                                        {{ ($projectDetails->budget ?? 0) + ($ttlexpindrinc ?? 0) + ($ttlexpdirinc ?? 0) - (($ttlexpdir ?? 0) + ($ttlexpind ?? 0) + $productAmount) }}
                                                    </th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                @php
                                    $buject = $projectDetails->budget;
                                    $estimateprofit = $buject - $projectDetails->estimate_profit;
                                    $expense = abs(($ttlexpdir ?? 0) + ($ttlexpind ?? 0) + $productAmount);
                                    $compleate = $estimateprofit != 0 ? ($expense / $estimateprofit) * 100 : 0;
                                    $incomplate = 100 - $compleate;
                                    $curentprofit = ($projectDetails->estimate_profit * $compleate) / 100;
                                @endphp

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

                    </div>
                </div>
            </div>

        @endif
        <!-- /.col-->
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {

            //-------------
            //- DONUT CHART -
            //-------------
            var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
            var donutData = {
                labels: [
                    'Complete',
                    'InComplete',
                ],
                datasets: [{
                    data: [{{ $compleate ?? 0 }}, {{ $incomplate ?? 0 }}],
                    backgroundColor: ['#00a65a', '#f56954'],
                }]
            }
            var donutOptions = {
                maintainAspectRatio: false,
                responsive: true,
            }
            new Chart(donutChartCanvas, {
                type: 'doughnut',
                data: donutData,
                options: donutOptions
            })

            //-------------
            //- PIE CHART -
            //-------------
            var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
            var pieData = donutData;
            var pieOptions = {
                maintainAspectRatio: false,
                responsive: true,
            }
            new Chart(pieChartCanvas, {
                type: 'pie',
                data: pieData,
                options: pieOptions
            })

        })

        $(function() {
            var datasets = [{
                label: "Profit/Loss",
                data: ["{{ $curentprofit ?? 0 }}", "{{ $buject ?? 0 }}"],
                backgroundColor: ["#3F88C5"]
            }];

            for (var i = 0; i < datasets[0].data.length; i++) {
                if (datasets[0].data[i] > 0) {
                    datasets[0].backgroundColor[i] = "#3F88C5";
                } else {
                    datasets[0].backgroundColor[i] = "#FF5E5B";
                }
            }

            var ctx = document.getElementById("myChart");
            var myChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: [
                        "{{ $projectDetails->pname ?? '' }}",
                    ],
                    datasets: datasets
                }
            });

        })
    </script>
    @include('backend.pages.reports.excel')
@endsection
