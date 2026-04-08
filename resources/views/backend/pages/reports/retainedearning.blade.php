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
                      Retained Earning  Report </h1>
                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('report.retained_earning') }}" method="POST" enctype="multipart/form-data">
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

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Year:</label>
                                    <select name="year" class="form-control" id="yearpicker">
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
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


        @if (isset($incomes) && !empty($incomes))
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header no-print">
                    <h3 class="card-title">Retained Earnings Report</h3>
                    <a onclick="window.print()" target="_blank" class="btn btn-default float-right my-2 no-print">
                        <i class="fas fa-print"></i> Print
                    </a>
                    <div id="tableActions" class="float-right my-2 no-print"></div>
                </div>
                <div class="card-body">
                    <div class="invoice p-3 mb-3">
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <td style="text-align: center">
                                            @if (isset($companyInfo->logo))
                                                <a href="{{ route('home') }}">
                                                    <img width="200px" src="{{ asset('/backend/logo/' . $companyInfo->logo) }}" alt="">
                                                </a>
                                            @endif
                                        </td>
                                        <td width="70%" style="text-align: center">
                                            <h3>{{ $companyInfo->name ?? 'Company Name' }}</h3>
                                            <h4>Retained Earnings</h4>
                                            <h5>Year: {{ $year }}</h5>
                                        </td>
                                    </tr>
                                </table>
                                <table id="datatablexcel" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Year</th>
                                            <th>Income</th>
                                            <th>Expenses</th>
                                            <th>Retained Earnings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalRetainedEarnings = 0;
                                        @endphp
                                        @foreach($incomes as $key => $income)
                                            @php
                                                $expense = $expenses->firstWhere('year', $income->year);
                                                $incomeAmount = $income->credit;
                                                $expenseAmount = $expense->debit ?? 0;
                                                $netIncome = $incomeAmount - $expenseAmount;
                                                $totalRetainedEarnings += $netIncome;
                                            @endphp
                                            <tr class="table_data">
                                                <td align="right"><strong>{{ $income->year }}</strong></td>
                                                <td align="right">{{ number_format($incomeAmount, 2) }}</td>
                                                <td align="right">{{ number_format($expenseAmount, 2) }}</td>
                                                <td align="right">
                                                    @if ($totalRetainedEarnings < 0)
                                                        <strong>({{ number_format(abs($totalRetainedEarnings), 2) }})</strong>
                                                    @else
                                                        <strong>{{ number_format($totalRetainedEarnings, 2) }}</strong>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
        
                            <div class="col-md-4 float-left">
                                <br><br>
                                <p>Prepared By:_____________<br />
                                    Date:____________________
                                </p>
                            </div>
                            <div class="col-md-6 text-center"></div>
                            <div class="col-md-2">
                                <br><br>
                                <p>Approved By:________________<br />
                                    Date:_________________</p>
                            </div>
                            <hr>
                            <div class="col-md-12 bg-success" style="text-align: center">
                                Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} products.
                                We believe you will be satisfied with our services.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- table --}}


    </div>
@endsection
@section('scripts')
    <script>
        var startYear = 2000;
        // $('#yearpicker').append($('<option>Select Year'));
        for (i = new Date().getFullYear(); i > startYear; i--) {
            $('#yearpicker').append($('<option />').val(i).html(i));
        }
    </script>

    @include('backend.pages.reports.excel')
@endsection