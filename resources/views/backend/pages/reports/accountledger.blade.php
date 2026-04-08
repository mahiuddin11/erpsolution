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

        @if (isset($findreports) && !empty($findreports))
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header no-print">
                    <h3 class="card-title">Ledger Report</h3>
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
                                            <h3>Ledger of {{$accounts->account_name}}</h3>
                                        </td>
                                    </tr>
                                </table>
                                <table id="datatablexcel" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <td height="25" width="5%"><strong>SL.</strong></td>
                                            <td width="10%"><strong>Date Transaction</strong></td>
                                            <td width="10%"><strong>Invoice No</strong></td>
                                            <td width="12%"><strong>Head Name</strong></td>
                                            <td width="12%"><strong>Remark</strong></td>
                                            <td width="10%" align="right">
                                                <strong>Debit</strong>
                                            </td>
                                            <td width="10%" align="right">
                                                <strong>Credit</strong>
                                            </td>
                                            <td width="10%" align="right">
                                                <strong>Balance</strong>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total = $newOpeningBalance ?? 0;
                                            $debit = 0;
                                            $credit = 0;
                                            $count = 0;
                                        @endphp
                                        <tr>
                                            <td colspan="7" align="center">Opening Balance</td>
                                            <td align="right">{{ $newOpeningBalance ?? '0.00' }}
                                            </td>
                                        </tr>
                                        @if (isset($findreports))
                                            @foreach ($findreports as $findreport)
                                                @php($count++)
                                                <tr class="table_data">
                                                    <td align="right">
                                                        <strong>{{ $count }}</strong>
                                                    </td>
                                                    <td align="right">
                                                        <strong>{{ date('Y-M-d',strtotime($findreport->created_at)) }}</strong>
                                                    </td>
                                                    <td align="right">
                                                        <strong>{{ $findreport->invoice }}</strong>
                                                    </td>
                                                    <td align="right">
                                                        <?php
                                                        $accounthead = App\Models\AccountTransaction::where('invoice', $findreport->invoice)
                                                            ->where('account_id', '!=', $findreport->account_id)
                                                            ->first();
                                                        ?>
                                                        <strong>{{ account_with_name($accounthead) }}</strong>
                                                    </td>
                                                    <td align="right">
                                                        <strong>{{ $findreport->remark }}</strong>
                                                    </td>
            
                                                    <td align="right">
                                                        <strong> {{ $findreport->credit ?? 0 }}</strong>
                                                    </td>
                                                    <td align="right">
                                                        <strong> {{ $findreport->debit ?? 0 }}</strong>
                                                    </td>
                                                    <td align="right">
                                                        <strong>
                                                            <?php
                                                            
                                                            $accountid = getFirstAccount($findreport->account_id);
                                                            if ($accountid == 1 || $accountid == 6) {
                                                                $total += $findreport->debit - $findreport->credit;
                                                            } else {
                                                                $total += $findreport->credit - $findreport->debit;
                                                            }
                                                            $debit += $findreport->debit;
                                                            $credit += $findreport->credit;
                                                            ?>
                                                            <strong> {{ $total }}</strong>
                                                        </strong>
            
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="8" class="text-center">No Data Found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr class="table_data">
                                            <td colspan="5" align="right"><strong>Total</strong>
                                            </td>
                                            <td align="right">
                                                <strong> {{ $credit }} &nbsp;</strong>
                                            </td>
                                            <td align="right">
                                                <strong>{{ $debit }} &nbsp;</strong>
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



    </div>
@endsection
@section('scripts')
    @include('backend.pages.reports.excel')
@endsection
