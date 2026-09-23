@extends('backend.layouts.master')
@section('title')
    {{ $title }}
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Sale Return Aprove</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sale.sale.return') }}">Sale Return</a></li>
                        <li class="breadcrumb-item active"><span>{{ $saleReturn->return_no }}</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')

    {{-- NEW: print styling — hides layout chrome and action buttons, shows a clean invoice-style header only when printing --}}
    <style media="print">
        .main-header,
        .main-sidebar,
        .content-header,
        .no-print,
        .card-tools,
        footer.main-footer {
            display: none !important;
        }

        .content-wrapper {
            margin-left: 0 !important;
        }

        .card {
            box-shadow: none !important;
            border: none !important;
        }

        body {
            background: #fff !important;
        }

        #printable-invoice-header {
            display: block !important;
        }
    </style>

    {{-- NEW: only visible when printing — a simple invoice-style letterhead --}}
    <div id="printable-invoice-header" style="display:none; text-align:center; margin-bottom:20px;">
        <h2 style="margin-bottom:0;">Water Technology BD Limited</h2>
        <p style="margin-top:2px;">Sale Return Voucher</p>
        <hr>
    </div>

    @if (session('success'))
        <div class="alert alert-success no-print">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger no-print">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Return #{{ $saleReturn->return_no }}</h3>
                    <div class="card-tools">
                        @php
                            $statusColors = [
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'completed' => 'info',
                            ];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$saleReturn->status] ?? 'secondary' }} p-2">
                            {{ ucfirst($saleReturn->status) }}
                        </span>

                        {{-- NEW: Print button — triggers window.print(), hidden itself when printing via .no-print --}}
                        <button type="button" class="btn btn-sm btn-default no-print" onclick="window.print()"
                            style="margin-left:8px;">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">

                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 col-12 mb-2">
                            <strong>Original Invoice:</strong><br>
                            {{ optional($saleReturn->sale)->invoice_no ?? '—' }}
                        </div>
                        <div class="col-md-2 col-sm-6 col-12 mb-2">
                            <strong>Return Date:</strong><br>
                            {{ $saleReturn->return_date ? \Carbon\Carbon::parse($saleReturn->return_date)->format('d-m-Y') : '—' }}
                        </div>
                        <div class="col-md-2 col-sm-6 col-12 mb-2">
                            <strong>Branch:</strong><br>
                            {{ optional($saleReturn->branch)->name ?? '—' }}
                        </div>
                        <div class="col-md-2 col-sm-6 col-12 mb-2">
                            <strong>Warehouse:</strong><br>
                            {{ optional($saleReturn->warehouse)->name ?? '—' }}
                        </div>
                        <div class="col-md-3 col-sm-6 col-12 mb-2">
                            <strong>Customer / Ledger:</strong><br>
                            {{ optional($saleReturn->ledger)->account_name ?? '—' }}
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 col-12 mb-2">
                            <strong>Sales Representative:</strong><br>
                            {{ optional($saleReturn->salesPerson)->name ?? 'Not Assigned' }}
                        </div>
                        <div class="col-md-2 col-sm-6 col-12 mb-2">
                            <strong>Return Type:</strong><br>
                            {{ ucfirst($saleReturn->return_type ?? '—') }}
                        </div>
                        @if ($saleReturn->status !== 'pending')
                            <div class="col-md-3 col-sm-6 col-12 mb-2">
                                <strong>{{ $saleReturn->status === 'rejected' ? 'Rejected' : 'Approved' }} By:</strong><br>
                                {{ optional($saleReturn->approvedBy)->name ?? '—' }}
                                @if ($saleReturn->approved_at)
                                    <br><small
                                        class="text-muted">{{ \Carbon\Carbon::parse($saleReturn->approved_at)->format('d-m-Y h:i A') }}</small>
                                @endif
                            </div>
                        @endif
                    </div>

                    <h5>Returned Items</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th align="center">Return Qty</th>
                                    <th align="center">Unit Price</th>
                                    <th align="center">VAT %</th>
                                    <th align="center">Line Amount</th>
                                    <th align="center">Condition</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($saleReturn->details as $detail)
                                    <tr>
                                        <td>{{ optional($detail->product)->name ?? '—' }}</td>
                                        <td align="center">{{ $detail->returned_qty }}</td>
                                        <td align="right">{{ number_format((float) $detail->unit_price, 2) }}</td>
                                        <td align="center">{{ $detail->vat_percent ?? 0 }}%</td>
                                        <td align="right">{{ number_format((float) $detail->line_amount, 2) }}</td>
                                        <td align="center">
                                            @if ($detail->condition === 'damaged')
                                                <span class="badge badge-danger">Damaged</span>
                                            @else
                                                <span class="badge badge-success">Good</span>
                                            @endif
                                        </td>
                                        <td>{{ $detail->reason ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No line items found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" align="right"><strong>Grand Total</strong></td>
                                    <td align="right">
                                        <strong>{{ number_format((float) $saleReturn->grand_total, 2) }}</strong>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if ($saleReturn->remarks)
                        <div class="mb-4">
                            <strong>Remarks:</strong>
                            <p class="text-muted">{{ $saleReturn->remarks }}</p>
                        </div>
                    @endif

                    @if ($canDecide)
                        <div class="row no-print">
                            <div class="col-12">
                                <hr>
                                <h5>Decision</h5>
                                <p class="text-muted">Review the items above carefully before approving or rejecting this
                                    return.</p>

                                <form id="approveForm" method="POST"
                                    action="{{ route('sale.return.approve', $saleReturn->id) }}" class="d-inline">
                                    @csrf
                                    <button type="button"
                                        onclick="confirmDecision('approveForm', 'approve this return', 'Yes, Approve it!', '#28a745')"
                                        class="btn btn-success">
                                        <i class="fa fa-check"></i> Approve Return
                                    </button>
                                </form>

                                <form id="rejectForm" method="POST"
                                    action="{{ route('sale.return.reject', $saleReturn->id) }}" class="d-inline">
                                    @csrf
                                    <button type="button"
                                        onclick="confirmDecision('rejectForm', 'reject this return', 'Yes, Reject it!', '#ffc107')"
                                        class="btn btn-warning">
                                        <i class="fa fa-ban"></i> Reject Return
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDecision(formId, actionLabel, confirmText, confirmColor) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to ' + actionLabel + '.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: 'Cancel',
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
@endsection
