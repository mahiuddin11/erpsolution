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
                        Leave Report </h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12 no-print">
            <div class="card card-default">
                <div class="card-body">
                    <form action="{{ route('report.leave') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="employee_id">Employee</label>
                                    <select name="employee_id" id="employee_id" class="form-control select2">
                                        <option value="All">All</option>
                                        @foreach ($employees as $employee)
                                            <option {{ $request->employee_id == $employee->id ? 'selected' : '' }}
                                                value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="employee_id">Status</label>
                                    <select name="status" id="status" class="form-control select2">
                                        <option value="All">All</option>
                                        <option {{ $request->status == 'approved' ? 'selected' : '' }} value="approved">Approved</option>
                                        <option {{ $request->status == 'pending' ? 'selected' : '' }} value="pending">Pending</option>
                                        <option {{ $request->status == 'cancel' ? 'selected' : '' }} value="cancel">Cancel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ request('start_date') ?? date('Y-m-d') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ request('end_date') ?? date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="mt-4 btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        @if (isset($reqests) && !empty($reqests))
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header no-print">
                        <h3 class="card-title">Leave Report</h3>
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
                                                <h3>Leave Report</h3>
                                                <h4><b>From Date: {{ $startDate }}</b>, <b>To date:
                                                        {{ $endDate }} </b></h4>
                                            </td>
                                        </tr>
                                    </table>

                                    <table class="table table-bordered mt-2">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Date</th>
                                                <th>Employee Name</th>
                                                <th>Apply Date</th>
                                                <th>End Date</th>
                                                <th>Leave Day</th>
                                                <th>Reason</th>
                                                <th>Leave Eligibility</th>
                                                <th>status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $amount = 0;
                                                $approve_amount = 0;
                                            @endphp
                                            @foreach ($reqests as $key => $reqest)

                                                @php
                                                    $start_date = Carbon\Carbon::parse($reqest->apply_date ?? now());
                                                    $end_date = Carbon\Carbon::parse($reqest->end_date ?? now());
                                                    $leave_days = $start_date->diffInDays($end_date) + 1;
                                                @endphp

                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($reqest->created_at)) }}</td>
                                                    <td>{{ $reqest->employee->name ?? '' }}</td>
                                                    <td>{{ $reqest->apply_date ?? '' }}</td>
                                                    <td>{{ $reqest->end_date ?? '' }}</td>
                                                    <td>{{ $leave_days ?? '' }}</td>
                                                    <td>{{ $reqest->reason ?? '' }}</td>
                                                    <td>{{ $reqest->payment_status ?? '' }}</td>
                                                    <td>{{ $reqest->status ?? '' }}</td>
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
        @endif




    </div>
@endsection
@section('scripts')
    @include('backend.pages.reports.excel')
@endsection
