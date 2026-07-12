@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .soce-wrapper {
            background: #fff;
            padding: 20px;
        }

        .soce-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .soce-header h4 {
            margin: 0;
            font-weight: 700;
        }

        .soce-header .sub {
            font-size: 14px;
            margin-top: 2px;
        }

        table.soce-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        table.soce-table th,
        table.soce-table td {
            border: 1px solid #333;
            padding: 6px 10px;
            vertical-align: top;
        }

        table.soce-table th {
            text-align: center;
            font-weight: 700;
            background: #f2f2f2;
        }

        table.soce-table td.particulars {
            width: 28%;
        }

        table.soce-table td.amount-col {
            text-align: right;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .soce-opening td,
        .soce-closing td {
            font-weight: 700;
            background: #f8f9fa;
            border-top: 2px solid #333;
        }

        .soce-neg {
            color: #c0392b;
        }

        .soce-recon-warning {
            background: #fdecea;
            color: #c0392b;
            padding: 10px 14px;
            border-radius: 6px;
            margin-top: 12px;
            font-weight: 600;
        }

        .soce-recon-ok {
            background: #eafaf1;
            color: #1e8449;
            padding: 10px 14px;
            border-radius: 6px;
            margin-top: 12px;
            font-weight: 600;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Statement of Changes in Equity</h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12 no-print">
            <div class="card card-outline card-info">
                <div class="card-body">
                    <form action="{{ route('report.changesinequity') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From Date:</label>
                                    <input type="date" class="form-control" name="from_date"
                                        value="{{ $from_date ?? '' }}">
                                    @error('from_date')
                                        <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To Date:</label>
                                    <input type="date" class="form-control" name="to_date" value="{{ $to_date ?? '' }}">
                                    @error('to_date')
                                        <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fa fa-search"></i> Generate
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($request->method() == 'POST' && $from_date)
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header no-print">
                        <h3 class="card-title">Statement of Changes in Equity</h3>
                        <a onclick="window.print()" class="btn btn-default float-right my-2">
                            <i class="fas fa-print"></i> Print
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="soce-wrapper">

                            {{-- Header --}}
                            <div class="soce-header">
                                <h4>{{ $companyInfo->company_name ?? 'N/A' }}</h4>
                                <div class="sub">STATEMENT OF CHANGES IN EQUITY</div>
                                <div class="sub">
                                    FOR THE YEAR ENDED {{ strtoupper(date('jS F Y', strtotime($to_date))) }}
                                </div>
                                <div class="sub">(Amount in BDT)</div>
                            </div>

                            <table class="soce-table">
                                <thead>
                                    <tr>
                                        <th class="particulars">PARTICULARS</th>
                                        @foreach ($components as $key => $comp)
                                            <th>{{ strtoupper($comp['label']) }}</th>
                                        @endforeach
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    {{-- Opening Balance --}}
                                    <tr class="soce-opening">
                                        <td class="particulars">Balance as at {{ date('d F Y', strtotime($from_date)) }}
                                        </td>
                                        @foreach ($components as $key => $comp)
                                            <td class="amount-col">{{ number_format($comp['opening'], 0) }}</td>
                                        @endforeach
                                        <td class="amount-col">
                                            {{ number_format(array_sum(array_column($components, 'opening')), 0) }}
                                        </td>
                                    </tr>

                                    {{-- Movement Rows --}}
                                    @foreach ($movementRows as $row)
                                        <tr>
                                            <td class="particulars">{{ $row['label'] }}</td>
                                            @foreach ($components as $key => $comp)
                                                @php $val = $row[$key] ?? 0; @endphp
                                                <td class="amount-col {{ $val < 0 ? 'soce-neg' : '' }}">
                                                    {{ $val == 0 ? '-' : ($val < 0 ? '(' . number_format(abs($val), 0) . ')' : number_format($val, 0)) }}
                                                </td>
                                            @endforeach
                                            @php $rowTotal = array_sum(array_intersect_key($row, $components)); @endphp
                                            <td class="amount-col {{ $rowTotal < 0 ? 'soce-neg' : '' }}">
                                                {{ $rowTotal == 0 ? '-' : ($rowTotal < 0 ? '(' . number_format(abs($rowTotal), 0) . ')' : number_format($rowTotal, 0)) }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    {{-- Closing Balance --}}
                                    <tr class="soce-closing">
                                        <td class="particulars">Balance as at {{ date('d F Y', strtotime($to_date)) }}
                                        </td>
                                        @foreach ($components as $key => $comp)
                                            <td class="amount-col">{{ number_format($comp['closing'], 0) }}</td>
                                        @endforeach
                                        <td class="amount-col">
                                            {{ number_format(array_sum(array_column($components, 'closing')), 0) }}
                                        </td>
                                    </tr>

                                </tbody>
                            </table>

                            {{-- Reconciliation check --}}
                            <div class="no-print">
                                @if (abs($reconDifference) > 1)
                                    <div class="soce-recon-warning">
                                        ⚠ Reconciliation Difference: {{ number_format($reconDifference, 2) }} —
                                        Closing Total Equity does not match the Equity section of the
                                        Balance Sheet. Please verify the account IDs in the Config section.
                                    </div>
                                @else
                                    <div class="soce-recon-ok">
                                        ✓ Reconciled — Closing Total Equity matches the Balance Sheet.
                                    </div>
                                @endif
                            </div>

                            <p class="mt-3" style="font-style: italic;">The annexed notes are an integral part of
                                these financial statements.</p>

                            {{-- Signature block --}}
                            <div class="row mt-5">
                                <div class="col-md-5">
                                    <p>_______________________<br>Managing Director</p>
                                </div>
                                <div class="col-md-2"></div>
                                <div class="col-md-5 text-right">
                                    <p>_______________________<br>Chairman/Director</p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <p>Dated: {{ date('d F Y') }}<br>Place: Dhaka, Bangladesh</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
