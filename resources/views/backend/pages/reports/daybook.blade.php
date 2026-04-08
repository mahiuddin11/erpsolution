@extends('backend.layouts.master')
@section('title')
  Day Book Report - {{ $title }}
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
                        Day Book  Report </h1>
                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('report.day.book') }}" method="POST" enctype="multipart/form-data">
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
                                    <label>Date:</label>
                                    <input type="text" class="form-control" id="reservation" name="date"
                                        value="{{ $request->date  ?? '' }}" />
                                    @error('date')
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

        @if (isset($transactions) && !empty($transactions))
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header no-print">
                    <h3 class="card-title">Day Book Report</h3>
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
                                                    <img width="200px" src="{{ asset('/backend/invoicelogo/' . $companyInfo->invoice_logo) }}" alt="">
                                                </a>
                                            @endif
                                        </td>
                                        <td width="70%" style="text-align: center">
                                            <h3>Day Book Report</h3>
                                            <h4><b>Date: {{ $request->date }}</b></h4>
                                        </td>
                                    </tr>
                                </table>
                                <table class="table table-bordered mt-2">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Voucher No</th>
                                            <th>Account Name</th>
                                            <th>Description</th>
                                            <th>Debit</th>
                                            <th>Credit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                                <td>{{ $transaction->invoice }}</td>
                                                <td>
                                                    {!! $transaction->account->account_name ?? '<span style="color: red;">Something was wrong. Account not selected</span>' !!}
                                                </td>
                                                
                                                <td>{{ $transaction->remark }}</td>
                                                <td>{{ number_format($transaction->debit, 2) }}</td>
                                                <td>{{ number_format($transaction->credit, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="4"><strong>Total</strong></td>
                                            <td>{{ number_format($totalDebit, 2) }}</td>
                                            <td>{{ number_format($totalCredit, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
    
                            <div class="col-md-4 float-left">
                                <br><br>
                                <p>Prepared By:_____________<br />Date:____________________</p>
                            </div>
                            <div class="col-md-6 text-center"></div>
                            <div class="col-md-2">
                                <br><br>
                                <p>Approved By:________________<br />Date:_________________</p>
                            </div>
                            <hr>
                            <div class="col-md-12 bg-success" style="text-align: center">
                                Thank you for choosing {{ $companyInfo->company_name ?? 'N/A' }} products. We believe you will be satisfied with our services.
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
