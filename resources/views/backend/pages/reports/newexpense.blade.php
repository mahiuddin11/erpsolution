@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }

        table.buttomTable {
            margin: 30px 20px 13px 22px;
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
            <form action="{{ route('report.expense') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card card-outline card-info no-print">
                    <div class="card-body">
                        <div class="row no-print">
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
                                    <input type="date" class="form-control" name="from_date"
                                        value="{{ old('from_date', $request->from_date ?? '') }}" />
                                    @error('from_date')
                                        <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>To Date:</label>
                                    <input type="date" class="form-control" name="to_date"
                                        value="{{ old('to_date', $request->to_date ?? '') }}" />
                                    @error('to_date')
                                        <span class="error text-red text-bold">{{ $message }}</span>
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

        @if (isset($findreports) && $findreports->isNotEmpty())
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header no-print">
                        <h3 class="card-title">Expense Report</h3>
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
                                                        <img width="200px"
                                                            src="{{ asset('/backend/logo/' . $companyInfo->logo) }}"
                                                            alt="">
                                                    </a>
                                                @endif
                                            </td>
                                            <td width="70%" style="text-align: center">
                                                <h3>Expense Book</h3>
                                                <h4><b>From Date: {{ $request->from_date }}</b>, <b>To Date:
                                                        {{ $request->to_date }}</b></h4>
                                            </td>
                                        </tr>
                                    </table>

                                    <table id="datatablexcel" class="table table-striped table-bordered">

                                        <thead>
                                            <tr>
                                                <td><strong>SL.</strong></td>
                                                <td><strong>Date</strong></td>
                                                <td><strong>Invoice No</strong></td>
                                                <td><strong>Head Name</strong></td>
                                                <td><strong>Remark</strong></td>
                                                <td align="right"><strong>Amount</strong></td>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            @php
                                                $count = 0;
                                                $total = 0;
                                            @endphp

                                            @foreach ($findreports as $findreport)
                                                @if (in_array($findreport->invoice, $getaccountInv))
                                                    @php
                                                        $count++;
                                                        $total += $findreport->debit ?? 0;
                                                    @endphp

                                                    <tr class="table_data">

                                                        <td align="right">
                                                            <strong>{{ $count }}</strong>
                                                        </td>

                                                        <td align="right">
                                                            <strong>{{ $findreport->created_at->format('Y-m-d') }}</strong>
                                                        </td>

                                                        <td align="right">
                                                            <strong>{{ $findreport->invoice }}</strong>
                                                        </td>

                                                        <td align="right">

                                                            @if (in_array($findreport->account_id, [5, 14]))
                                                                <strong>

                                                                    @if ($findreport->supplier_id)
                                                                        {{ $findreport->supplier->name ?? '' }}
                                                                    @elseif ($findreport->customer_id)
                                                                        {{ $findreport->customer->name ?? '' }}
                                                                    @elseif ($findreport->employee_id)
                                                                        {{ $findreport->employee->name ?? '' }}
                                                                    @elseif ($findreport->project_id)
                                                                        {{ $findreport->project->name ?? '' }}
                                                                    @endif

                                                                </strong>
                                                            @else
                                                                <strong>{{ account_with_name($findreport) }}</strong>
                                                            @endif

                                                        </td>

                                                        <td align="right">
                                                            <strong>{{ $findreport->remark }}</strong>
                                                        </td>

                                                        <td align="right">
                                                            <strong>{{ number_format($findreport->debit, 2) }}</strong>
                                                        </td>

                                                    </tr>
                                                @endif
                                            @endforeach

                                        </tbody>

                                        <tfoot>

                                            <tr class="table_data">

                                                <td colspan="5" align="right">
                                                    <strong>Total Expense</strong>
                                                </td>

                                                <td align="right">
                                                    <strong>{{ number_format($total, 2) }}</strong>
                                                </td>

                                            </tr>

                                        </tfoot>

                                    </table>

                                </div>
                                {{-- <div class="col-md-4 float-left">
                                    <br><br>
                                    <p>Prepared By:_____________<br>Date:____________________</p>
                                </div>
                                <div class="col-md-6 text-center"></div>
                                <div class="col-md-2">
                                    <br><br>
                                    <p>Approved By:________________<br>Date:_________________</p>
                                </div> --}}
                                <table width="95%" class="buttomTable">
                                    <tr>
                                        <td style="text-align:left">
                                            Prepared By:_____________<br>
                                            Date:____________________
                                        </td>

                                        <td style="text-align:right">
                                            Approved By:_____________<br>
                                            Date:____________________
                                        </td>
                                    </tr>
                                </table>
                                <hr>
                                <div class="col-md-12 bg-success text-center">
                                    Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} products. We believe
                                    you will be satisfied by our services.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@section('scripts')
    @include('backend.pages.reports.excel')
@endsection
