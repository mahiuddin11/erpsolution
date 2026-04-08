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
                      Cash Flow  Report </h1>
                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('report.cashflow') }}" method="POST" enctype="multipart/form-data">
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
                                    <input type="date" class="form-control" name="to_date"
                                        value="{{ $toDate ?? '' }}" />
                                    @error('to_date')
                                        <span class="error text-red text-bold"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{-- <div class="col-md-2">
                                <div class="form-group">
                                    <label>Branch </label>
                                    <select class="form-control select2 " name="branch_id">
                                        <option value="all" selected>All branches</option>
                                        @foreach ($branch as $key => $value)
                                            <option {{ $branch_id == $value->id ? 'selected' : '' }}
                                                value="{{ $value->id }}">
                                                {{ $value->branchCode . ' - ' . $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <span class="error text-red text-bold"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div> --}}
                            {{-- <div class="col-md-2">
                                <div class="form-group">
                                    <label>Accounts </label>
                                    <select class="form-control select2 " name="accounts_id">
                                        <option value="all" selected>All Accounts</option>
                                        @foreach ($accounts as $key => $value)
                                            <option value="{{ $value->id }}">
                                                {{ $value->accountCode . ' - ' . $value->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <span class="error text-red text-bold"> {{ $message }} </span>
                                    @enderror
                                </div>
                            </div> --}}

                            {{-- @php
                                
                            @endphp --}}

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

        @if (isset($accountbycroupby) && !empty($accountbycroupby))
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header no-print">
                    <h3 class="card-title">Cash Flow ledger Report</h3>
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
                                                <img width="200px" src="{{ asset('/backend/logo/' . $companyInfo->logo) }}"
                                                    style="" alt="">
                                            </a>
                                            @endif
                                        </td>
                                        <td width="70%" style="text-align: center">
                                            <h3>Cash Flow</h3>
                                            <h4><b>From Date: {{ $from_date }}</b>, <b>To date: {{ $to_date }} </b></h4>
                                        </td>
                                    </tr>
                                </table>
                                <table id="datatablexcel" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <td height="25" width="5%"><strong>SL.</strong></td>
                                            <td width="12%"><strong>Head Name</strong></td>
                                            <td width="10%" align="right">
                                                <strong>Balance</strong>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $count = 0;
                                            $total = 0;
                                        @endphp
                                        <tr>
                                            <td colspan="2" align="center">Opening Balance</td>
                                            <td align="right">{{ $newOpeningBalance ?? '0.00' }}
                                            </td>
                                        </tr>
                                        @if (isset($accountbycroupby))
                                        @php
                                            $total += $newOpeningBalance ?? 0;
                                        @endphp
                                            @foreach ($accountbycroupby as $findreport)
                                                @php($count++)
                                                    <tr class="table_data">
                                                        <td align="right">
                                                            <strong>{{ $count }}</strong>
                                                        </td>
                                                        <td align="right">
                                                            <strong>{!! accountledger($findreport->account_id ,$findreport->account->account_name ?? "") !!}</strong>
                                                        </td>
            
                                                        <td align="right">
                                                            @if (($findreport->credit - $findreport->debit) < 0)
                                                            ({{ abs($findreport->credit - $findreport->debit) }})
                                                            @else
                                                            
                                                            {{ $findreport->credit - $findreport->debit }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <?php
                                                       $total += $findreport->credit - $findreport->debit;
                                                    ?>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center">No Data Found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr class="table_data">
                                            <td colspan="2" align="right"><strong>Total</strong>
                                            </td>
                                            <td align="right">
                                                <strong>{{ $total }}</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
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
                                Thank you for choosing  {{ $companyInfo->company_name ?? 'N/A' }}  products.
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

        {{-- table --}}
    
    </div>
@endsection
@section('scripts')
    @include('backend.pages.reports.excel')
@endsection
