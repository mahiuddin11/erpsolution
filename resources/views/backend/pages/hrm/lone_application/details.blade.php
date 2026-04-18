@extends('backend.layouts.master')
@section('title')
    Hrm - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }

        .bg-primary {
            background-color: #0f2032 !important;
        }

        .bg-info {
            background-color: #005a34 !important;
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Hrm </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('hrm.lone.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.lone.index') }}">Hrm</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Loan Application Details</span></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">

        <!-- 🔹 Loan Info (Bigger) -->
        <div class="col-lg-6 col-md-12">
            <div class="card shadow-sm">

                <!-- Header -->
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Loan Information</h4>
                </div>

                <!-- Body -->
                <div class="card-body">

                    <!-- 👤 Employee Profile -->
                    <div class="text-center mb-3">
                        <img src="{{ $lone->employee->photo
                            ? asset('storage/employee/' . $lone->employee->photo)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($lone->employee->name) . '&background=0D8ABC&color=fff' }}"
                            class="rounded-circle shadow" style="width: 110px; height: 110px; object-fit: cover;"
                            alt="Employee Image">

                        <h5 class="mt-2 mb-0">{{ $lone->employee->name ?? '' }}</h5>
                        <small class="text-muted">{{ $lone->branch->name ?? '' }}</small>
                    </div>

                    <hr>

                    <!-- 📊 Info Table -->
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Loan Amount:</th>
                            <td><strong>{{ number_format($lone->amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Monthly Adjustment:</th>
                            <td>{{ number_format($lone->lone_adjustment, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Start Month:</th>
                            <td>
                                {{ $lone->adjustment_start ? \Carbon\Carbon::parse($lone->adjustment_start)->format('M Y') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Reason:</th>
                            <td>{{ $lone->reason }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span
                                    class="badge 
                                {{ $lone->status == 'approved' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($lone->status) }}
                                </span>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>

        <!-- 🔥 Loan Schedule (unchanged) -->
       

        @if ($lone->status == 'approved')
             <div class="col-lg-6 col-md-12 mt-3 mt-lg-0">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white text-center">
                        <h5 class="mb-0">Loan Adjustment Schedule</h5>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>Month</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loanDetails as $key => $detail)
                                        <tr class="text-center">
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($detail->month)->format('M Y') }}</td>
                                            <td><strong>{{ number_format($detail->amount, 2) }}</strong></td>
                                            <td>
                                                <span
                                                    class="badge 
                                        {{ $detail->status == 'paid' ? 'badge-success' : 'badge-danger' }}">
                                                    {{ ucfirst($detail->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-light">

                        @php
                            $paid = $loanDetails->where('status', 'paid')->sum('amount');
                            $due = $loanDetails->where('status', 'unpaid')->sum('amount');
                            $total = $loanDetails->sum('amount');
                        @endphp

                        <div class="row text-center">

                             <div class="col-12 border-bottom">
                                    <h6 class=" mb-2 font-weight-bold">Total Amount : {{ number_format($total, 0) }} </h6>
                            </div>

                            <div class="col-6 border-right">
                                <h6 class="text-success mb-0">Paid Amount</h6>
                                <h5 class="font-weight-bold">{{ number_format($paid, 0) }}</h5>
                            </div>

                            <div class="col-6">
                                <h6 class="text-danger mb-0">Due Amount</h6>
                                <h5 class="font-weight-bold">{{ number_format($due, 0) }}</h5>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        @else
            <div class="col-lg-6 col-md-12 mt-3 mt-lg-0">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white text-center">
                        <h5 class="mb-0">Loan Adjustment Schedule</h5>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>Month</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <tr class="text-center">
                                            <td colspan="4">{{ 'Loan Status Panding ... Schedule empty'  }}</td>
                                        </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-light">

                        @php
                            $paid = $loanDetails->where('status', 'paid')->sum('amount') ?? 0;
                            $due = $loanDetails->where('status', 'unpaid')->sum('amount') ?? 0;
                            $total = $loanDetails->sum('amount') ?? 0;
                        @endphp

                        <div class="row text-center">

                             <div class="col-12 border-bottom">
                                    <h6 class=" mb-2 font-weight-bold">Total Amount : {{ number_format($total, 0) }} </h6>
                            </div>

                            <div class="col-6 border-right">
                                <h6 class="text-success mb-0">Paid Amount</h6>
                                <h5 class="font-weight-bold">{{ number_format($paid, 0) }}</h5>
                            </div>

                            <div class="col-6">
                                <h6 class="text-danger mb-0">Due Amount</h6>
                                <h5 class="font-weight-bold">{{ number_format($due, 0) }}</h5>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
