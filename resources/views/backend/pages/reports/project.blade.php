@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
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
                            {{-- @dd('ff'); --}}
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

                            @php

                            @endphp

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
            // dd($projectDetails);
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
                                    {{-- <table id="datatablexcel" class="table  table-striped  table-bordered">
                                        <thead>
                                            <tr>
                                                <td colspan="5"><b>
                                                        <i class="fa fa-bullseye" aria-hidden="true"></i>

                                                    </b></td>
                                            </tr>
                                            <tr>
                                                <th>Date</th>
                                                <th>Invoice</th>
                                                <th>Details</th>
                                                <th>Quantity</th>
                                            </tr>
                                        </thead>
                                        @if ($productIssue)
                                            <tbody>
                                                @php
                                                    $ttlpurchase = 0;
                                                @endphp
                                                @foreach ($productgoodreceive as $element)
                                                 @foreach ($element->details as $eachissue)
                                                 @php
                                                     $ttlpurchase +=$eachissue->qty;
                                                 @endphp
                                                    <tr>
                                                        <td>{{ $eachissue->date }}</td>
                                                        <td>{{ $element->invoice_no }}</td>
                                                        <td>{{ ($eachissue->product->productCode ?? "N/A") . ' - ' . $eachissue->product->name ?? ""}}</td>
                                                        <td>{{ $eachissue->qty }}</td>

                                                    </tr>
                                                 @endforeach
                                                @endforeach
                                                <tr>
                                                    <th colspan="3" style="text-align: center;">Total Issue</th>
                                                    <th style="text-align: center;">{{ $ttlpurchase }}</th>
                                                </tr>
                                            </tbody>
                                            @endif
                                    </table> --}}
                                    <table id="datatablexcel" class="table  table-striped  table-bordered">
                                        <thead>
                                            <tr>
                                                <td colspan="5"><b>
                                                        <i class="fa fa-bullseye" aria-hidden="true"></i>

                                                    </b></td>
                                            </tr>
                                            <tr>
                                                <th>Date</th>
                                                <th>Invoice</th>
                                                <th>Details</th>
                                                <th>Quantity</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        @if (!$productgoodreceive->isEmpty())
                                            <tbody>
                                                <tr>
                                                    <td colspan="5">
                                                        <b>
                                                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                                                            Products details
                                                        </b>
                                                    </td>
                                                </tr>
                                                @php
                                                    $productAmount = 0;
                                                @endphp

                                                @foreach ($productgoodreceive as $val)
                                                    @foreach ($val->details as $eachuse)
                                                        @php
                                                            $productAmount += $eachuse->unit_price * $eachuse->qty;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $val->date }}</td>
                                                            <td>{{ $val->invoice_no }}</td>
                                                            <td>{{ ($eachuse->product->productCode ?? 'N/A') . ' - ' . $eachuse->product->name ?? '' }}
                                                            </td>
                                                            <td>{{ $eachuse->qty }}</td>
                                                            <td style="text-align: right;">
                                                                {{ $eachuse->unit_price * $eachuse->qty }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach

                                                @foreach ($projectTransfer as $val)
                                                    @foreach ($val->details as $eachuse)
                                                        @php
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
                                                            $productAmount += $purchase->unit_price * $eachuse->qty;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $val->order_date }}</td>
                                                            <td>{{ $val->invoice_no }}</td>
                                                            <td>{{ ($eachuse->product->productCode ?? 'N/A') . ' - ' . $eachuse->product->name ?? '' }}
                                                            </td>
                                                            <td>{{ $eachuse->qty }}</td>
                                                            <td style="text-align: right;">
                                                                {{ $purchase->unit_price * $eachuse->qty }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach

                                                <tr>
                                                    <th colspan="4" style="text-align: center;">Total Uses</th>
                                                    <th style="text-align: right;">{{ $productAmount }}</th>
                                                </tr>
                                            </tbody>
                                        @endif
                                        {{--
                                        @if ($productReturn)
                                            <tbody>
                                                <tr>
                                                    <td colspan="5">
                                                        <b>
                                                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                                                            Products Return details
                                                        </b>
                                                    </td>
                                                </tr>
                                                @php
                                                    $ttlreturn = 0;
                                                @endphp
                                                @foreach ($productReturn as $eachreturn)
                                                    @php
                                                        $requisitions = App\Models\ProjectRequisition::where('project_id', $project_id)->first();
                                                        $unitprice = $requisitions->ProjectRequisitionDetails
                                                            ->where('product_id', $eachuse->productId)
                                                            ->pluck('unit_price')
                                                            ->first();
                                                        $ttlreturn += $unitprice * $eachreturn->return_qty;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $eachreturn->created }}</td>
                                                        <td>{{ $eachreturn->in_no }}</td>
                                                        <td>{{ $eachreturn->pcode . ' - ' . $eachreturn->pname }}</td>
                                                        <td>{{ $eachreturn->return_qty }}</td>
                                                        <td style="text-align: right;">
                                                            {{ $unitprice * $eachreturn->return_qty }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th colspan="4" style="text-align: center;">Total Uses</th>
                                                    <th style="text-align: right;">{{ $ttlreturn }}</th>
                                                </tr>
                                            </tbody>
                                        @endif --}}



                                    </table>
                                    
                                    @if ($directIncome)
                                        <table class="table table-bordered">


                                            <tbody>
                                                <tr>
                                                    <td colspan="3">
                                                        <b>
                                                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                                                            Direct Income details
                                                        </b>
                                                    </td>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Category</th>
                                                    <th>Amount</th>
                                                </tr>
                                                </tr>
                                                @php
                                                    $ttlexpdirinc = 0;
                                                @endphp
                                                @foreach ($directIncome as $echexp)
                                                    @php
                                                        $ttlexpdirinc += $echexp->debit;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $echexp->created_at }}</td>
                                                        <td>{{ $echexp->account->account_name }}</td>
                                                        <td style="text-align: right;">{{ $echexp->credit }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th colspan="2" style="text-align: center;">Total Direct Income</th>
                                                    <th style="text-align: right;">{{ $ttlexpdirinc }}</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endif
                                    @if ($indirectIncome)
                                        <table class="table table-bordered">


                                            <tbody>
                                                <tr>
                                                    <td colspan="3">
                                                        <b>
                                                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                                                            Indirect Income details
                                                        </b>
                                                    </td>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Category</th>
                                                    <th>Amount</th>
                                                </tr>
                                                </tr>
                                                @php
                                                    $ttlexpindrinc = 0;
                                                @endphp
                                                @foreach ($indirectIncome as $echexp)
                                                    @php
                                                        $ttlexpindrinc += $echexp->debit;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $echexp->created_at }}</td>
                                                        <td>{{ $echexp->account->account_name }}</td>
                                                        <td style="text-align: right;">{{ $echexp->credit }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th colspan="2" style="text-align: center;">Total Indirect Expense
                                                    </th>
                                                    <th style="text-align: right;">{{ $ttlexpindrinc }}</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endif
                                    @if ($directExpenses)
                                        <table class="table table-bordered">


                                            <tbody>
                                                <tr>
                                                    <td colspan="3">
                                                        <b>
                                                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                                                            Direct Expense details
                                                        </b>
                                                    </td>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Category</th>
                                                    <th>Amount</th>
                                                </tr>
                                                </tr>
                                                @php
                                                    $ttlexpdir = 0;
                                                @endphp
                                                @foreach ($directExpenses as $echexp)
                                                    @php
                                                        $ttlexpdir += $echexp->debit;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $echexp->created_at }}</td>
                                                        <td>{{ $echexp->account->account_name }}</td>
                                                        <td style="text-align: right;">{{ $echexp->debit }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th colspan="2" style="text-align: center;">Total Direct Expense</th>
                                                    <th style="text-align: right;">{{ $ttlexpdir }}</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endif
                                    @if ($indirectExpenses)
                                        <table class="table table-bordered">

                                            <tbody>
                                                <tr>
                                                    <td colspan="3">
                                                        <b>
                                                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                                                            Indirect Expense details
                                                        </b>
                                                    </td>
                                                  <tr>
                                                      <th>Date</th>
                                                      <th>Category</th>
                                                      <th>Amount</th>
                                                  </tr>
                                                </tr>
                                                @php
                                                    $ttlexpind = 0;
                                                @endphp
                                                @foreach ($indirectExpenses as $echexp)
                                                    @php
                                                        $ttlexpind += $echexp->debit;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ date('Y-m-d', strtotime($echexp->created_at)) }}</td>
                                                        <td>{{ $echexp->account->account_name }}</td>
                                                        <td style="text-align: right;">{{ $echexp->debit }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th colspan="2" style="text-align: center;">Total Indirect Expense
                                                    </th>
                                                    <th style="text-align: right;">{{ $ttlexpind }}</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endif

                                    @if ($projectMoney)
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
                                                <th style="text-align: right;">{{ $projectDetails->budget ?? 0 }}</th>
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
                                                <th style="text-align: right;">{{ $ttlexpind }}</th>
                                            </tr>
                                            <tr>
                                                <th>Total Product Consumption </th>
                                                <th style="text-align: right;">{{ $productAmount }}</th>
                                            </tr>
                                            <tr>
                                                <th>Total Profit (A - B)</th>
                                                <th style="text-align: right;">
                                                    {{ ($projectDetails->budget ?? 0) + $ttlexpindrinc + $ttlexpdirinc - ($ttlexpdir + $ttlexpind + $productAmount) }}
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>



                                </div>
                                @php
                                    $buject = $projectDetails->budget;
                                    $estimateprofit = $buject - $projectDetails->estimate_profit;
                                    $expense = abs($ttlexpdir + $ttlexpind + $productAmount);
                                    $compleate = ($expense / $estimateprofit) * 100;
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
            // Get context with jQuery - using jQuery's .get() method.
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
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            new Chart(donutChartCanvas, {
                type: 'doughnut',
                data: donutData,
                options: donutOptions
            })
            //-------------
            //- PIE CHART -
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
            var pieData = donutData;
            var pieOptions = {
                maintainAspectRatio: false,
                responsive: true,
            }
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
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

            /* ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
             
            ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー */

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
