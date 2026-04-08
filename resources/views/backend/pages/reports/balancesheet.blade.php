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
            <form action="{{ route('report.balancesheet.balancesheet') }}" method="POST" enctype="multipart/form-data">
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
                                    <label>From Date:</label>
                                    <input type="date" class="form-control " name="from_date"
                                        value="{{ $startDate ?? '' }}" />
                                    @error('from_date')
                                        <span class="error text-red text-bold"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>To Date:</label>
                                    <input type="date" class="form-control " name="to_date"
                                        value="{{ $endDate ?? '' }}" />
                                    @error('to_date')
                                        <span class="error text-red text-bold"> {{ $message }}</span>
                                    @enderror
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



        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header no-print">
                    <h3 class="card-title">Balance Sheet Report</h3>
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
                                        <td width="70%" style="text-align: center">
                                            <h3>Balance Sheet</h3>
                                            <h4><b>From Date: {{ $startDate }}</b>, <b>To date: {{ $endDate }} </b>
                                            </h4>
                                        </td>
                                    </tr>
                                </table>
                                <h3 class="text-center">Balance Sheet</h3>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Account Name</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th colspan="3">Assets</th>
                                        </tr>
                                        @foreach ($balanceSheet['assets'] as $asset)
                                            <tr>
                                                <td></td>
                                                <td>{{ $asset['name'] }}</td>
                                                <td>{{ number_format($asset['balance'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th colspan="2">Total Assets</th>
                                            <th>{{ number_format($balanceSheet['total_assets'], 2) }}</th>
                                        </tr>
                            
                                        <tr>
                                            <th colspan="3">Liabilities</th>
                                        </tr>
                                        @foreach ($balanceSheet['liabilities'] as $liability)
                                            <tr>
                                                <td></td>
                                                <td>{{ $liability['name'] }}</td>
                                                <td>{{ number_format($liability['balance'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th colspan="2">Total Liabilities</th>
                                            <th>{{ number_format($balanceSheet['total_liabilities'], 2) }}</th>
                                        </tr>
                            
                                        <tr>
                                            <th colspan="3">Equity</th>
                                        </tr>
                                        @foreach ($balanceSheet['equity'] as $equity)
                                            <tr>
                                                <td></td>
                                                <td>{{ $equity['name'] }}</td>
                                                <td>{{ number_format($equity['balance'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th colspan="2">Total Equity</th>
                                            <th>{{ number_format($balanceSheet['total_equity'], 2) }}</th>
                                        </tr>
                            
                                        <tr>
                                            <th colspan="2">Total Liabilities and Equity</th>
                                            <th>{{ number_format($balanceSheet['total_liabilities_and_equity'], 2) }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
