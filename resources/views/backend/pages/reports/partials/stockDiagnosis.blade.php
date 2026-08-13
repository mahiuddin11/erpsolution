{{-- resources/views/backend/inventory/stock_diagnosis_modal.blade.php --}}
{{-- Loaded via AJAX into #diagnosisPane inside the Product Ledger modal. --}}

<div class="diagnosis-wrap">

    {{-- ===== Header: product + range ===== --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
        <div>
            <h6 class="mb-0 font-weight-bold">{{ $product->name }}</h6>
            <small class="text-muted">{{ $product->productCode }} &middot; {{ $branchLabel }} &middot;
                {{ $from }} to {{ $to }}</small>
        </div>
        <span class="type-badge {{ $hasMismatch ? 'badge-sale' : 'badge-purchase' }}">
            {{ $hasMismatch ? 'Mismatch Found' : 'Matched' }}
        </span>
    </div>

    {{-- ===== Financial ledger-style summary bar ===== --}}
    <div class="ledger-summary-bar">
        <div class="row no-gutters">
            @foreach ($summary as $row)
                <div class="col-6 col-md-4 s-item">
                    <div class="s-lbl">{{ $row['label'] }}</div>
                    <div class="s-val {{ $row['net'] < 0 ? 'text-danger' : '' }}">
                        {{ number_format($row['net'], 2) }}
                    </div>
                    <div class="s-sub">
                        <span class="s-in">IN {{ number_format($row['in'], 2) }}</span>
                        <span class="s-out">OUT {{ number_format($row['out'], 2) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ===== Difference strip ===== --}}
    <div class="row mb-3">
        <div class="col-md-6 mb-2 mb-md-0">
            <div class="diff-card {{ abs($diffAsIs['net']) > 0.0001 ? 'diff-bad' : 'diff-ok' }}">
                <div class="diff-title">AS-IS `stocks` vs Source — difference</div>
                <div class="diff-row">
                    <span>IN <b>{{ number_format($diffAsIs['in'], 2) }}</b></span>
                    <span>OUT <b>{{ number_format($diffAsIs['out'], 2) }}</b></span>
                    <span>NET <b>{{ number_format($diffAsIs['net'], 2) }}</b></span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="diff-card {{ abs($diffFixed['net']) > 0.0001 ? 'diff-bad' : 'diff-ok' }}">
                <div class="diff-title">With "Project In" excluded — difference</div>
                <div class="diff-row">
                    <span>IN <b>{{ number_format($diffFixed['in'], 2) }}</b></span>
                    <span>OUT <b>{{ number_format($diffFixed['out'], 2) }}</b></span>
                    <span>NET <b>{{ number_format($diffFixed['net'], 2) }}</b></span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Unmapped status alert ===== --}}
    @if (!empty($unmappedStatuses))
        <div class="alert alert-warning py-2 px-3 mb-3" style="font-size: 13px;">
            <strong>Unmapped statuses:</strong> {{ implode(', ', $unmappedStatuses) }}
            — invisible to the summary report.
        </div>
    @endif

    {{-- ===== Source breakdown table ===== --}}
    <div class="table-responsive mb-3">
        <table class="table table-sm table-bordered mb-0" id="diagnosisSourceTable">
            <thead>
                <tr>
                    <th>Source Category</th>
                    <th class="text-right">In</th>
                    <th class="text-right">Out</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sourceBreakdown as $row)
                    <tr>
                        <td>{{ $row['label'] }}</td>
                        <td class="text-right in-col">{{ number_format($row['in'], 2) }}</td>
                        <td class="text-right out-col">{{ number_format($row['out'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ===== Raw status breakdown table ===== --}}
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0" id="diagnosisStatusTable">
            <thead>
                <tr>
                    <th>Status (raw)</th>
                    <th class="text-right">Rows</th>
                    <th class="text-right">Qty</th>
                    <th class="text-center">stocksummery()</th>
                    <th class="text-center">stock()</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($statusBreakdown as $row)
                    <tr>
                        <td>{{ $row['status'] }}</td>
                        <td class="text-right">{{ $row['count'] }}</td>
                        <td class="text-right">{{ number_format($row['qty'], 2) }}</td>
                        <td class="text-center">
                            <span
                                class="type-badge {{ $row['stocksummery'] === 'IN' ? 'badge-purchase' : ($row['stocksummery'] === 'OUT' ? 'badge-sale' : 'badge-adjustment') }}">
                                {{ $row['stocksummery'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span
                                class="type-badge {{ $row['stockController'] === 'IN' ? 'badge-purchase' : 'badge-sale' }}">
                                {{ $row['stockController'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Scoped to the diagnosis pane — reuses the ledger's colour language */
    .diagnosis-wrap .ledger-summary-bar {
        background: #1e293b;
        color: #fff;
        border-radius: 8px;
        padding: 14px 18px;
        margin-bottom: 14px;
    }

    .diagnosis-wrap .ledger-summary-bar .s-item {
        text-align: center;
        padding: 6px 4px;
        border-right: 1px solid rgba(255, 255, 255, .08);
    }

    .diagnosis-wrap .ledger-summary-bar .s-item:last-child {
        border-right: none;
    }

    .diagnosis-wrap .ledger-summary-bar .s-lbl {
        font-size: 11px;
        color: #94a3b8;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .diagnosis-wrap .ledger-summary-bar .s-val {
        font-size: 20px;
        font-weight: 700;
        color: #fff;
    }

    .diagnosis-wrap .ledger-summary-bar .s-sub {
        font-size: 11px;
        margin-top: 2px;
    }

    .diagnosis-wrap .ledger-summary-bar .s-in {
        color: #34d399;
        margin-right: 8px;
    }

    .diagnosis-wrap .ledger-summary-bar .s-out {
        color: #f87171;
    }

    .diagnosis-wrap .diff-card {
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 13px;
        border: 1px solid #e2e8f0;
    }

    .diagnosis-wrap .diff-card.diff-ok {
        background: #ecfdf5;
        border-color: #a7f3d0;
    }

    .diagnosis-wrap .diff-card.diff-bad {
        background: #fef2f2;
        border-color: #fecaca;
    }

    .diagnosis-wrap .diff-title {
        font-weight: 600;
        margin-bottom: 6px;
        color: #334155;
    }

    .diagnosis-wrap .diff-row {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    @media (max-width: 575px) {
        .diagnosis-wrap .ledger-summary-bar .s-item {
            border-right: none;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            margin-bottom: 6px;
            padding-bottom: 8px;
        }
    }
</style>
