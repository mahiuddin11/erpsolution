@extends('backend.layouts.master')
@section('title')
    {{ $title ?? 'Approve Purchase Return' }}
@endsection
@section('styles')
    <style>
        #approve-items {
            min-width: 860px;
        }

        .decision-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        @media (max-width: 767px) {
            .card-body {
                padding: 10px;
            }

            #approve-items {
                min-width: 760px;
            }

            .decision-actions .btn {
                flex: 1 1 auto;
            }
        }
    </style>
@endsection
@section('navbar-content')
    <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('inventorySetup.purchase.return.index') }}" class="nav-link">Purchase Return</a>
    </li>
@endsection
@section('admin-content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">{{ $title ?? 'Approve Purchase Return' }}</h3>
                    <div class="card-tools">
                        <span class="badge badge-warning p-2">{{ ucfirst($purchaseReturn->status) }}</span>
                        <a class="btn btn-default" href="{{ route('inventorySetup.purchase.return.index') }}"><i
                                class="fas fa-arrow-left"></i> Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-sm-6 col-12 mb-3">
                            <label>Return No :</label>
                            <input type="text" class="form-control" value="{{ $purchaseReturn->return_no }}" readonly>
                        </div>
                        <div class="col-md-2 col-sm-6 col-12 mb-3">
                            <label>Return Date :</label>
                            <input type="text" class="form-control"
                                value="{{ $purchaseReturn->return_date ? \Carbon\Carbon::parse($purchaseReturn->return_date)->format('d-m-Y') : '' }}"
                                readonly>
                        </div>
                        <div class="col-md-2 col-sm-6 col-12 mb-3">
                            <label>Branch :</label>
                            <input type="text" class="form-control"
                                value="{{ optional($purchaseReturn->branch)->branchCode }} - {{ optional($purchaseReturn->branch)->name }}"
                                readonly>
                        </div>
                        <div class="col-md-2 col-sm-6 col-12 mb-3">
                            <label>Warehouse :</label>
                            <input type="text" class="form-control"
                                value="{{ optional($purchaseReturn->warehouse)->name ?? '—' }}" readonly>
                        </div>
                        <div class="col-md-2 col-sm-6 col-12 mb-3">
                            <label>Project :</label>
                            <input type="text" class="form-control"
                                value="{{ optional($purchaseReturn->project)->name ?? '—' }}" readonly>
                        </div>
                        <div class="col-md-2 col-sm-6 col-12 mb-3">
                            <label>Supplier :</label>
                            <input type="text" class="form-control"
                                value="{{ optional($purchaseReturn->supplier)->name ?? '—' }}" readonly>
                        </div>
                        <div class="col-md-2 col-sm-6 col-12 mb-3">
                            <label>Supplier Ledger :</label>
                            <input type="text" class="form-control"
                                value="{{ optional($purchaseReturn->ledger)->account_name ?? '—' }}" readonly>
                        </div>

                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="table-responsive return-item-table-wrapper">
                                <table class="table table-bordered table-striped" id="approve-items">
                                    <thead>
                                        <tr>
                                            <th style="width:16%">Category</th>
                                            <th style="width:20%">Product</th>
                                            <th style="width:10%" class="text-right">Return Qty</th>
                                            <th style="width:11%" class="text-right">Unit Price</th>
                                            <th style="width:11%">Type</th>
                                            <th style="width:16%">Reason</th>
                                            <th style="width:16%" class="text-right">Line Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchaseReturn->details as $detail)
                                            <tr>
                                                <td>{{ optional($detail->product->category)->name ?? '—' }}</td>
                                                <td>{{ optional($detail->product)->name ?? '—' }}</td>
                                                <td class="text-right">
                                                    {{ number_format((float) $detail->returned_qty, 2) }}</td>
                                                <td class="text-right">{{ number_format((float) $detail->unit_price, 2) }}
                                                </td>
                                                <td>{{ ucfirst($detail->purchasetype) }}</td>
                                                <td>{{ $detail->reason ?? '—' }}</td>
                                                <td class="text-right">{{ number_format((float) $detail->line_amount, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="6" class="text-right"><strong>Grand Total</strong></td>
                                            <td class="text-right">
                                                <strong>{{ number_format((float) $purchaseReturn->grand_total, 2) }}</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if ($canDecide)
                        <div class="row mt-4">
                            <div class="col-12">


                                <form action="{{ route('inventorySetup.purchase.return.reject', $purchaseReturn->id) }}"
                                    method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label>Remark * :</label>
                                        <textarea name="rejection_reason" class="form-control" rows="3" required>{{ $purchaseReturn->remarks ?? '' }}</textarea>
                                    </div>

                                    <div class="decision-actions">
                                        <button type="submit"
                                            formaction="{{ route('inventorySetup.purchase.return.approve', $purchaseReturn->id) }}"
                                            formnovalidate class="btn btn-success"
                                            onclick="return confirm('Are you sure you want to approve this return? Stock and accounting will be posted.');">
                                            <i class="fa fa-check"></i> Approve Return
                                        </button>
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to reject this return?');">
                                            <i class="fa fa-ban"></i> Reject Return
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('inventorySetup.purchase.return.index') }}" class="btn btn-default">Back to List</a>
                </div>
            </div>
        </div>
    </div>
@endsection
