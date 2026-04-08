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
            <form action="{{ route('report.incomestatement.incomestatement') }}" method="POST" enctype="multipart/form-data">
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
                                    <input type="date" class="form-control" id="from_date" name="from_date"
                                        value="{{ $startDate ?? '' }}" />
                                    @error('from_date')
                                        <span class="error text-red text-bold"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>To Date:</label>
                                    <input type="date" class="form-control " id="to_date" name="to_date"
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
                    <h3 class="card-title">Income Statement Report</h3>
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
                                            <h3>Income & Expenses Statement</h3>
                                            <h4><b>From Date: {{ $startDate }}</b>, <b>To date: {{ $endDate }} </b>
                                            </h4>
                                        </td>
                                    </tr>
                                </table>
                                <h3 class="text-center">Income Statement </h3>

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($incomeStatement as $category => $amount)
                                            @php
                                                // Sanitize category name for use as ID
                                                $sanitizedCategory = preg_replace('/[^a-zA-Z0-9]/', '_', $category);
                                            @endphp
                                            <tr>
                                                <td>{{ $category }}</td>
                                                <td>{{ number_format($amount, 2) }}</td>
                                                <td>
                                                    @if(!in_array($category,['Gross Profit',"Operating Income","Net Income"]))
                                                    <button class="btn btn-info btn-sm toggle-details"
                                                        data-category="{{ $sanitizedCategory }}">
                                                        Show Details
                                                    </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr class="details-row" id="details-{{ $sanitizedCategory }}"
                                                style="display: none;">
                                                <td colspan="3">
                                                    <!-- Placeholder for transaction details -->
                                                    <div class="transaction-details"
                                                        id="transaction-details-{{ $sanitizedCategory }}"></div>
                                                </td>
                                            </tr>
                                        @endforeach
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

    <script>
        $(document).ready(function() {
            $('.toggle-details').on('click', function() {
                var category = $(this).data('category');

                var from_date = $("#from_date").val();
                var to_date = $("#to_date").val();

                var detailsRow = $('#details-' + category);
                var detailsContainer = $('#transaction-details-' + category);
                // Toggle the visibility of the details row
                detailsRow.toggle();
                // Fetch transaction details if not already loaded
                if (detailsRow.is(':visible') && !detailsContainer.hasClass('loaded')) {
                    $.ajax({
                        url: '{{ route('report.incomestatement.details') }}', // Adjust the route as needed
                        type: 'GET',
                        data: {
                            category: category,
                            from_date: from_date,
                            to_date: to_date
                        },
                        success: function(response) {
                            detailsContainer.html(response);
                            detailsContainer.addClass('loaded');
                        },
                        error: function() {
                            detailsContainer.html(
                                '<p>An error occurred while fetching details.</p>');
                        }
                    });
                }
            });
        });
    </script>
    @include('backend.pages.reports.excel')
@endsection
