@extends('backend.layouts.master')
@section('title')
    inventory - {{ $title }}
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('backend/plugins/select2/css/select2.min.css') }}">
    <style>
        :root {
            --tt-primary: #007bff;
            --tt-primary-soft: #f0f7ff;
            --tt-border: #dee2e6;
            --tt-muted: #868e96;
            --tt-text: #343a40;
            --tt-radius: 10px;
        }

        .section-title {
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: .3px;
            color: #495057;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 8px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .section-title .step-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--tt-primary);
            color: #fff;
            font-size: 12px;
            flex: 0 0 auto;
        }

        .required-star {
            color: #dc3545;
        }

        .transfer-type-wrap {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .transfer-type-card {
            flex: 1 1 250px;
            border: 2px solid var(--tt-border);
            border-radius: var(--tt-radius);
            padding: 16px 15px;
            position: relative;
            background: #fff;
            margin: 0;
            min-height: 44px;
        }

        .transfer-type-card.active {
            border-color: var(--tt-primary);
            background: var(--tt-primary-soft);
            box-shadow: 0 2px 8px rgba(0, 123, 255, .15);
        }

        .transfer-type-card input[type=radio] {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 20px;
            height: 20px;
            margin: 0;
        }

        .transfer-type-card .tt-icon {
            font-size: 24px;
            margin-bottom: 8px;
            color: #6c757d;
        }

        .transfer-type-card.active .tt-icon {
            color: var(--tt-primary);
        }

        .transfer-type-card .tt-title {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 3px;
            color: var(--tt-text);
            padding-right: 28px;
        }

        .transfer-type-card .tt-sub {
            font-size: 12.5px;
            color: var(--tt-muted);
        }

        .transfer-type-card .tt-badge {
            display: inline-block;
            font-size: 10.5px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 10px;
            margin-top: 8px;
        }

        .badge-req {
            background: #fff3cd;
            color: #856404;
        }

        .badge-noreq {
            background: #d4edda;
            color: #155724;
        }

        .details-panel {
            background: #fff;
            border: 1px solid var(--tt-border);
            border-radius: var(--tt-radius);
            padding: 18px 18px 4px;
            margin-bottom: 22px;
        }

        .form-group label {
            font-weight: 500;
            font-size: 13.5px;
            color: #495057;
        }

        .route-visual {
            display: flex;
            align-items: stretch;
            gap: 14px;
            margin: 4px 0 18px;
        }

        .route-node {
            flex: 1 1 0;
            min-width: 0;
            background: #f8f9fb;
            border: 1px dashed var(--tt-border);
            border-radius: var(--tt-radius);
            padding: 14px 16px;
        }

        .route-node-from {
            background: #f4f9ff;
            border-color: #bcdcff;
        }

        .route-node-to {
            background: #f3faf4;
            border-color: #bfe6c6;
        }

        .route-node-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .5px;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .route-node-from .route-node-tag {
            color: #0d6efd;
        }

        .route-node-to .route-node-tag {
            color: #28a745;
        }

        .route-arrow {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            font-size: 20px;
            color: #adb5bd;
        }

        @media (max-width: 767.98px) {
            .route-visual {
                flex-direction: column;
            }

            .route-arrow {
                width: auto;
                height: 22px;
                transform: rotate(90deg);
            }
        }

        .form-control,
        .select2-container .select2-selection--single {
            min-height: 42px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .route-resolved-box {
            display: flex;
            align-items: center;
            min-height: 42px;
            background: #fff;
            border: 1px solid var(--tt-border);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 14px;
            color: var(--tt-text);
        }

        .products-panel {
            background: #fff;
            border: 1px solid var(--tt-border);
            border-radius: var(--tt-radius);
            padding: 16px;
        }

        .product-count-badge {
            background: #eef2f7;
            color: #495057;
            font-weight: 600;
            font-size: 12px;
            padding: 5px 11px;
            border-radius: 20px;
        }

        .products-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid #f1f1f1;
        }

        .products-summary {
            font-size: 13px;
            color: #6c757d;
        }

        .products-summary strong {
            color: var(--tt-text);
        }

        .products-empty {
            display: none;
            text-align: center;
            padding: 30px 10px;
            color: #adb5bd;
        }

        .products-empty i {
            font-size: 26px;
            display: block;
            margin-bottom: 8px;
        }

        .row-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #eef2f7;
            color: #495057;
            font-size: 12px;
            font-weight: 700;
        }

        #productTable {
            margin-bottom: 0;
        }

        #productTable thead th {
            font-size: 12.5px;
            text-transform: uppercase;
            color: #6c757d;
            white-space: nowrap;
        }

        #productTable td {
            vertical-align: middle;
        }

        .remove-row-btn {
            cursor: pointer;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
        }

        .remove-row-btn:hover {
            background: #fdeaea;
        }

        .stock-hint {
            font-size: 12px;
            margin-top: 4px;
            font-weight: 500;
        }

        .stock-hint.ok {
            color: #28a745;
        }

        .stock-hint.low {
            color: #dc3545;
        }

        .remaining-display {
            font-weight: 600;
            font-size: 14px;
        }

        .pr-add-panel {
            background: #eef7ff;
            border: 1px solid #bfe0ff;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 14px;
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .pr-add-panel .form-group {
            margin-bottom: 0;
            flex: 1 1 260px;
        }

        .pr-add-panel small {
            display: block;
            color: #0c5aa6;
            margin-top: 4px;
        }

        @media (max-width: 767.98px) {
            #productTable thead {
                display: none;
            }

            #productTable,
            #productTable tbody,
            #productTable tr,
            #productTable td {
                display: block;
                width: 100% !important;
            }

            #productTable {
                border: none;
            }

            #productTable tbody tr {
                border: 1px solid var(--tt-border);
                border-radius: var(--tt-radius);
                padding: 14px 14px 10px;
                margin-bottom: 14px;
                position: relative;
                background: #fff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
            }

            #productTable td {
                border: none !important;
                padding: 8px 0;
            }

            #productTable td[data-label]::before {
                content: attr(data-label);
                display: block;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .3px;
                color: #868e96;
                margin-bottom: 4px;
            }

            #productTable td.remove-cell {
                position: absolute;
                top: 10px;
                right: 10px;
                width: auto !important;
                padding: 0;
            }
        }

        .card-footer {
            gap: 10px;
            flex-wrap: wrap;
        }

        @media (max-width: 575.98px) {
            .card-footer {
                flex-direction: column-reverse;
            }

            .card-footer a,
            .card-footer button {
                width: 100%;
            }
        }
    </style>
@endsection
@php
    $resolveBranchWarehouse = function ($storedId) use ($branchs) {
        if (!$storedId) {
            return ['branch_id' => null, 'warehouse_id' => null];
        }
        $row = $branchs->firstWhere('id', $storedId);
        if (!$row) {
            return ['branch_id' => null, 'warehouse_id' => null];
        }
        if ((int) $row->parent_id === 0) {
            return ['branch_id' => $row->id, 'warehouse_id' => null];
        }
        return ['branch_id' => $row->parent_id, 'warehouse_id' => $row->id];
    };

    $sourceResolved = $resolveBranchWarehouse(
        $editInfo->transfer_type === 'branch_to_project' ? $editInfo->branch_id : null,
    );
    $destResolved = $resolveBranchWarehouse(
        $editInfo->transfer_type === 'project_to_branch' ? $editInfo->branch_id : null,
    );

    // FIX: initial "resolved" id for the stock-check hidden input must be the
    // WAREHOUSE id when a warehouse is set, otherwise fall back to the branch id.
    // Previously this was hardcoded to branch_id only, so the very first stock
    // check on page load ignored the warehouse entirely.
    $sourceInitialId = $sourceResolved['warehouse_id'] ?? $sourceResolved['branch_id'];
    $destInitialId = $destResolved['warehouse_id'] ?? $destResolved['branch_id'];

    $transferTypeLabels = [
        'branch_to_project' => [
            'icon' => 'fa-warehouse',
            'title' => 'Branch / Warehouse &rarr; Project',
            'sub' => 'Issue material from stock to a running project',
            'badge' => 'req',
        ],
        'project_to_project' => [
            'icon' => 'fa-people-arrows',
            'title' => 'Project &rarr; Project',
            'sub' => 'Move surplus material between two projects',
            'badge' => 'req',
        ],
        'project_to_branch' => [
            'icon' => 'fa-undo-alt',
            'title' => 'Project &rarr; Branch / Warehouse',
            'sub' => 'Return unused material back to stock',
            'badge' => 'noreq',
        ],
    ];
    $currentTypeInfo = $transferTypeLabels[$editInfo->transfer_type] ?? null;
@endphp

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $title }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('project.transferproject.index'))
                            <li class="breadcrumb-item"><a href="{{ route('project.transferproject.index') }}">Project
                                    Transfer</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exchange-alt mr-2"></i>Edit Project Transfer —
                        {{ $editInfo->invoice_no }}</h3>
                </div>

                <form action="{{ route('project.transferproject.update', $editInfo->id) }}" method="POST"
                    id="transferForm">
                    @csrf
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- ============ STEP 1: TRANSFER TYPE ============ --}}
                        {{-- FIX: only the SELECTED transfer type card is rendered now.
                             The other two are not shown at all (previously all three
                             were rendered with the other two just disabled/greyed). --}}
                        <div class="section-title"><span class="step-num">1</span> Transfer Type
                            <span class="badge badge-secondary ml-2" style="font-size:11px;"><i class="fas fa-lock"></i>
                                Locked after creation</span>
                        </div>

                        @if ($currentTypeInfo)
                            <div class="transfer-type-wrap">
                                <label class="transfer-type-card active">
                                    <div class="tt-icon"><i class="fas {{ $currentTypeInfo['icon'] }}"></i></div>
                                    <div class="tt-title">{!! $currentTypeInfo['title'] !!}</div>
                                    <div class="tt-sub">{{ $currentTypeInfo['sub'] }}</div>
                                    @if ($currentTypeInfo['badge'] === 'req')
                                        <span class="tt-badge badge-req"><i class="fas fa-file-alt"></i> Requisition
                                            required</span>
                                    @else
                                        <span class="tt-badge badge-noreq"><i class="fas fa-check"></i> No requisition
                                            needed</span>
                                    @endif
                                </label>
                            </div>
                        @endif
                        <input type="hidden" name="transfer_type" value="{{ $editInfo->transfer_type }}">

                        <hr>

                        {{-- ============ STEP 2: ROUTE DETAILS (unchanged) ============ --}}
                        <div class="section-title"><span class="step-num">2</span> Transfer Details</div>

                        <div class="details-panel">
                            <div class="form-row">
                                <div class="col-lg-2 col-md-4 col-sm-6 form-group">
                                    <label>Date <span class="required-star">*</span></label>
                                    <input type="date" name="date" class="form-control"
                                        value="{{ $editInfo->order_date }}" required>
                                </div>

                                <div class="col-lg-2 col-md-4 col-sm-6 form-group">
                                    <label>Invoice / Reference No</label>
                                    <input type="text" class="form-control" value="{{ $editInfo->invoice_no }}"
                                        readonly>
                                </div>

                                @if (in_array($editInfo->transfer_type, ['branch_to_project', 'project_to_project']))
                                    <div class="col-lg-3 col-md-4 col-sm-6 form-group">
                                        <label>
                                            Purchase Requisition <span class="required-star">*</span>
                                        </label>

                                        <select class="form-control select2" disabled>
                                            <option value="">-- Select Requisition --</option>
                                            @foreach ($purchaserequisitions as $pr)
                                                <option value="{{ $pr->id }}"
                                                    {{ $pr->id == $editInfo->purchase_requisition_id ? 'selected' : '' }}>
                                                    {{ $pr->invoice_no }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <input type="hidden" name="purchase_requisition"
                                            value="{{ $editInfo->purchase_requisition_id }}">
                                    </div>
                                @endif

                                @if ($editInfo->transfer_type === 'branch_to_project')
                                    <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                        <label>
                                            Source Branch <span class="required-star">*</span>
                                        </label>

                                        <select class="form-control select2 branch-picker" data-target="from_branch_id"
                                            data-preselect-warehouse="{{ $sourceResolved['warehouse_id'] }}" disabled>
                                            <option value="">-- Select Branch --</option>
                                            @foreach ($branchs->where('parent_id', 0) as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ $branch->id == $sourceResolved['branch_id'] ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        {{-- FIX: value is now the resolved WAREHOUSE id when one is set,
                                             otherwise the branch id — this is what stock checks read on
                                             initial page load. --}}
                                        <input type="hidden" name="from_branch_id" value="{{ $sourceInitialId }}">
                                    </div>

                                    <div class="col-lg-2 col-md-6 col-sm-6 form-group">
                                        <label>
                                            Warehouse <span class="text-muted">(optional)</span>
                                        </label>

                                        <div class="warehouse-wrap" data-target="from_branch_id"
                                            style="display:{{ $sourceResolved['warehouse_id'] ? 'block' : 'none' }}">
                                            <select class="form-control select2 warehouse-picker"
                                                data-target="from_branch_id" disabled>
                                                <option value="">-- Warehouse --</option>
                                                @foreach ($warehouses ?? [] as $warehouse)
                                                    <option value="{{ $warehouse->id }}"
                                                        {{ $warehouse->id == $sourceResolved['warehouse_id'] ? 'selected' : '' }}>
                                                        {{ $warehouse->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                @if ($editInfo->transfer_type === 'project_to_branch')
                                    <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                        <label>Destination Branch <span class="required-star">*</span></label>
                                        <select class="form-control select2 branch-picker" data-target="to_branch_id"
                                            data-preselect-warehouse="{{ $destResolved['warehouse_id'] }}">
                                            <option value="">-- Select Branch --</option>
                                            @foreach ($branchs->where('parent_id', 0) as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ $branch->id == $destResolved['branch_id'] ? 'selected' : '' }}>
                                                    {{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                        {{-- FIX: same resolved-id correction applied here --}}
                                        <input type="hidden" name="to_branch_id" value="{{ $destInitialId }}">
                                    </div>

                                    <div class="col-lg-2 col-md-6 col-sm-6 form-group">
                                        <label>Warehouse <span class="text-muted">(optional)</span></label>
                                        <div class="warehouse-wrap" data-target="to_branch_id"
                                            style="display:{{ $destResolved['warehouse_id'] ? 'block' : 'none' }}">
                                            <select class="form-control select2 warehouse-picker"
                                                data-target="to_branch_id">
                                                <option value="">-- Warehouse --</option>
                                                @foreach ($warehouses ?? [] as $warehouse)
                                                    <option value="{{ $warehouse->id }}"
                                                        {{ $warehouse->id == $destResolved['warehouse_id'] ? 'selected' : '' }}>
                                                        {{ $warehouse->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="route-visual">
                                <div class="route-node route-node-from">
                                    <span class="route-node-tag"><i class="fas fa-upload"></i> FROM</span>
                                    @if ($editInfo->transfer_type === 'branch_to_project')
                                        <div class="form-group">
                                            <label class="mb-1">Source</label>
                                            <div class="route-resolved-box">
                                                <i class="fas fa-warehouse mr-2 text-muted"></i>
                                                <span class="route-resolved-text" data-target="from_branch_id">
                                                    {{ optional($branchs->firstWhere('id', $sourceInitialId))->name ?? '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label>Project <span class="required-star">*</span></label>
                                            <select name="from_project_id" class="form-control select2" required>
                                                <option value="">-- Select Project --</option>
                                                @foreach ($projects as $project)
                                                    <option value="{{ $project->id }}"
                                                        {{ $project->id == $editInfo->project_id ? 'selected' : '' }}>
                                                        {{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>

                                <div class="route-arrow" aria-hidden="true"><i class="fas fa-long-arrow-alt-right"></i>
                                </div>

                                <div class="route-node route-node-to">
                                    <span class="route-node-tag"><i class="fas fa-flag-checkered"></i> TO</span>
                                    @if ($editInfo->transfer_type === 'branch_to_project')
                                        <div class="form-group">
                                            <label>Project <span class="required-star">*</span></label>
                                            <select name="to_project_id_a" class="form-control select2" required>
                                                <option value="">-- Select Project --</option>
                                                @foreach ($projects as $project)
                                                    <option value="{{ $project->id }}"
                                                        {{ $project->id == $editInfo->project_id ? 'selected' : '' }}>
                                                        {{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @elseif ($editInfo->transfer_type === 'project_to_project')
                                        <div class="form-group">
                                            <label>Project <span class="required-star">*</span></label>
                                            <select name="to_project_id_b" class="form-control select2" required>
                                                <option value="">-- Select Project --</option>
                                                @foreach ($projects as $project)
                                                    <option value="{{ $project->id }}"
                                                        {{ $project->id == $editInfo->to_project_id ? 'selected' : '' }}>
                                                        {{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label class="mb-1">Destination</label>
                                            <div class="route-resolved-box">
                                                <i class="fas fa-building mr-2 text-muted"></i>
                                                <span class="route-resolved-text" data-target="to_branch_id">
                                                    {{ optional($branchs->firstWhere('id', $destInitialId))->name ?? '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12 form-group">
                                    <label>Note</label>
                                    <textarea name="note" class="form-control" rows="2">{{ $editInfo->note }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- ============ STEP 3: PRODUCTS ============ --}}
                        <div class="section-title justify-content-between">
                            <span><span class="step-num">3</span> Products to Transfer</span>
                            <span class="product-count-badge" id="productCountBadge">{{ $details->count() }} items</span>
                        </div>

                        <div class="products-panel">

                            @if ($remainingPrProducts->isNotEmpty())
                                <div class="pr-add-panel">
                                    <div class="form-group">
                                        <label class="mb-1">Add another item from this Requisition</label>
                                        <select id="prRemainingSelect" class="form-control select2">
                                            <option value="">-- Select remaining item --</option>
                                            @foreach ($remainingPrProducts as $rp)
                                                <option value="{{ $rp['product_id'] }}"
                                                    data-category="{{ $rp['category_id'] }}"
                                                    data-purchasetype="{{ $rp['purchasetype'] }}"
                                                    data-remaining="{{ $rp['remaining_qty'] }}"
                                                    data-name="{{ $rp['product_name'] }}">{{ $rp['product_name'] }}
                                                    (remaining: {{ $rp['remaining_qty'] }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>Requisition-এ বাকি থাকা আইটেম — এখান থেকে সিলেক্ট করলে নতুন row auto-add হবে,
                                            remaining দিয়েই qty prefill হবে।</small>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addFromPrBtn">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-bordered" id="productTable">
                                    <thead>
                                        <tr>
                                            <th style="width:5%">#</th>
                                            <th style="width:16%">Category</th>
                                            <th style="width:20%">Product</th>
                                            <th style="width:12%">Purchase Type</th>
                                            <th style="width:12%">Available Stock</th>
                                            <th style="width:11%">Qty</th>
                                            <th style="width:12%">Remaining</th>
                                            <th style="width:6%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="productRows">
                                        @foreach ($details as $d)
                                            <tr>
                                                <td class="row-index-cell"><span
                                                        class="row-badge row-index">{{ $loop->iteration }}</span></td>
                                                <td data-label="Category">
                                                    <select name="category_nm[]"
                                                        class="form-control select2 category-select" required>
                                                        <option value="">-- Category --</option>
                                                        @foreach ($category_info as $cat)
                                                            <option value="{{ $cat->id }}"
                                                                {{ $cat->id == $d->category_id ? 'selected' : '' }}>
                                                                {{ $cat->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td data-label="Product">
                                                    <select name="product_nm[]"
                                                        class="form-control select2 product-select"
                                                        data-preselect="{{ $d->product_id }}" required>
                                                        <option value="{{ $d->product_id }}" selected>
                                                            {{ optional($d->product)->name ?? 'Product #' . $d->product_id }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td data-label="Purchase Type">
                                                    <select name="purchasetype[]" class="form-control purchasetype-select"
                                                        required>
                                                        <option value="local"
                                                            {{ $d->purchasetype == 'local' ? 'selected' : '' }}>Local
                                                        </option>
                                                        <option value="imported"
                                                            {{ $d->purchasetype == 'imported' ? 'selected' : '' }}>Imported
                                                        </option>
                                                    </select>
                                                </td>
                                                <td data-label="Available Stock">
                                                    <input type="text" class="form-control stock-display"
                                                        value="-" readonly>
                                                </td>
                                                <td data-label="Qty">
                                                    <input type="number" name="qty[]" min="0.01" step="0.01"
                                                        class="form-control qty-input" value="{{ $d->qty }}"
                                                        @if (isset($lineMax[$d->id])) max="{{ $lineMax[$d->id] }}" @endif
                                                        required>
                                                    <div class="stock-hint"></div>
                                                </td>
                                                <td data-label="Remaining" class="text-center">
                                                    <span class="remaining-display text-muted">-</span>
                                                </td>
                                                <td class="text-center remove-cell">
                                                    <i class="fas fa-trash text-danger remove-row-btn" title="Remove row"
                                                        role="button" tabindex="0"></i>
                                                </td>
                                                <input type="hidden" name="requested_qty[]"
                                                    value="{{ isset($lineMax[$d->id]) ? $lineMax[$d->id] : '' }}">
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="products-empty" id="productsEmptyHint" style="display:none">
                                <i class="fas fa-box-open"></i>
                                No products added yet. Use "Add Product Row" to start.
                            </div>

                            {{-- <div class="products-toolbar">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addRowBtn">
                                    <i class="fas fa-plus"></i> Add Product Row (manual)
                                </button>
                                <div class="products-summary">
                                    <strong id="totalRowsText">{{ $details->count() }} rows</strong> &middot;
                                    Total qty: <strong id="totalQtyText">{{ $details->sum('qty') }}</strong>
                                </div>
                            </div> --}}

                            <div class="products-toolbar">
                                @if ($editInfo->transfer_type === 'project_to_branch')
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addRowBtn">
                                        <i class="fas fa-plus"></i> Add Product Row (manual)
                                    </button>
                                @endif
                                <div class="products-summary">
                                    <strong id="totalRowsText">{{ $details->count() }} rows</strong> &middot;
                                    Total qty: <strong id="totalQtyText">{{ $details->sum('qty') }}</strong>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('project.transferproject.index') }}" class="btn btn-default"><i
                                class="fas fa-arrow-left"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update
                            Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Hidden template row (fully manual add — no requisition cap) --}}
    <table style="display:none">
        <tbody id="rowTemplate">
            <tr>
                <td class="row-index-cell"><span class="row-badge row-index"></span></td>
                <td data-label="Category">
                    <select name="category_nm[]" class="form-control select2 category-select" required>
                        <option value="">-- Category --</option>
                        @foreach ($category_info as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td data-label="Product">
                    <select name="product_nm[]" class="form-control select2 product-select" required>
                        <option value="">-- Select category first --</option>
                    </select>
                </td>
                <td data-label="Purchase Type">
                    <select name="purchasetype[]" class="form-control purchasetype-select" required>
                        <option value="local">Local</option>
                        <option value="imported">Imported</option>
                    </select>
                </td>
                <td data-label="Available Stock"><input type="text" class="form-control stock-display" value="-"
                        readonly></td>
                <td data-label="Qty">
                    <input type="number" name="qty[]" min="0.01" step="0.01" class="form-control qty-input"
                        required>
                    <div class="stock-hint"></div>
                </td>
                <td data-label="Remaining" class="text-center"><span class="remaining-display text-muted">-</span></td>
                <td class="text-center remove-cell"><i class="fas fa-trash text-danger remove-row-btn" title="Remove row"
                        role="button" tabindex="0"></i></td>
                <input type="hidden" name="requested_qty[]" value="">
            </tr>
        </tbody>
    </table>

    {{-- Hidden template for a row added from the Requisition remaining list --}}
    <table style="display:none">
        <tbody id="prRowTemplate">
            <tr>
                <td class="row-index-cell"><span class="row-badge row-index"></span></td>
                <td data-label="Category">
                    <select name="category_nm[]" class="form-control select2 category-select" required>
                        <option value="">-- Category --</option>
                        @foreach ($category_info as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td data-label="Product">
                    <select name="product_nm[]" class="form-control select2 product-select" required>
                        <option value="">-- Select category first --</option>
                    </select>
                </td>
                <td data-label="Purchase Type">
                    <select name="purchasetype[]" class="form-control purchasetype-select" required>
                        <option value="local">Local</option>
                        <option value="imported">Imported</option>
                    </select>
                </td>
                <td data-label="Available Stock"><input type="text" class="form-control stock-display" value="-"
                        readonly></td>
                <td data-label="Qty">
                    <input type="number" name="qty[]" min="0.01" step="0.01" class="form-control qty-input"
                        required>
                    <div class="stock-hint"></div>
                </td>
                <td data-label="Remaining" class="text-center"><span class="remaining-display text-muted">-</span></td>
                <td class="text-center remove-cell"><i class="fas fa-trash text-danger remove-row-btn" title="Remove row"
                        role="button" tabindex="0"></i></td>
                <input type="hidden" name="requested_qty[]" value="">
            </tr>
        </tbody>
    </table>
@endsection

@section('scripts')
    <script src="{{ asset('backend/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function() {

            function initSelect2(scope) {
                scope.find('.select2').each(function() {
                    var $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) $el.select2('destroy');
                    $el.select2({
                        width: '100%',
                        dropdownAutoWidth: true
                    });
                });
            }
            initSelect2($('body'));

            function updateRemainingDisplay($row) {
                var requested = parseFloat($row.find('input[name="requested_qty[]"]').val());
                var $display = $row.find('.remaining-display');
                if (!requested && requested !== 0) {
                    $display.text('-').removeClass('text-danger text-success').addClass('text-muted');
                    return;
                }
                var qty = parseFloat($row.find('.qty-input').val()) || 0;
                var remaining = requested - qty;
                $display.text(remaining.toFixed(2)).removeClass('text-muted');
                $display.toggleClass('text-danger', remaining < 0).toggleClass('text-success', remaining >= 0);
            }

            /* Existing rows: load correct product list per category, preselect, then trigger
               stock check via .trigger('change') (which reads the resolved from_branch_id /
               from_project_id hidden/select values). */
            $('#productRows tr').each(function() {
                var $row = $(this);
                var categoryId = $row.find('.category-select').val();
                var preselectId = $row.find('.product-select').data('preselect');

                if (categoryId) {
                    $.get("{{ route('project.transferproject.filterproduct') }}", {
                            category_id: categoryId
                        })
                        .done(function(res) {
                            var options = '<option value="">-- Select Product --</option>';
                            $.each(res, function(i, p) {
                                var sel = (p.id == preselectId) ? 'selected' : '';
                                options += '<option value="' + p.id + '" ' + sel + '>' + p
                                    .name + '</option>';
                            });
                            $row.find('.product-select').html(options).trigger('change.select2');
                            $row.find('.product-select').trigger('change');
                        });
                }
                updateRemainingDisplay($row);
            });

            /* Branch -> Warehouse preselect for header fields (initial paint only;
               subsequent changes are handled by the .branch-picker/.warehouse-picker
               handlers further below). */
            $('.branch-picker').each(function() {
                var $picker = $(this);
                var target = $picker.data('target');
                var branchId = $picker.val();
                var preselectWarehouseId = $picker.data('preselect-warehouse');
                if (!branchId) return;

                var $wrap = $('.warehouse-wrap[data-target="' + target + '"]');
                var $wh = $wrap.find('.warehouse-picker');

                $.get("{{ route('project.transferproject.getWarehouses') }}", {
                        branch_id: branchId
                    })
                    .done(function(res) {
                        if (res.length > 0) {
                            var options = '<option value="">-- Warehouse --</option>';
                            $.each(res, function(i, w) {
                                var sel = (preselectWarehouseId && w.id ==
                                    preselectWarehouseId) ? 'selected' : '';
                                options += '<option value="' + w.id + '" ' + sel + '>' + w
                                    .name + '</option>';
                            });
                            $wh.html(options).trigger('change.select2');
                            $wrap.show();
                        } else {
                            $wrap.hide();
                        }
                    });
            });

            var rowCount = {{ $details->count() }};

            function renumberRows() {
                $('#productRows tr').each(function(i) {
                    $(this).find('.row-index').text(i + 1);
                });
            }

            function updateProductsSummary() {
                var $rows = $('#productRows tr');
                var rows = $rows.length;
                var totalQty = 0;
                $rows.find('.qty-input').each(function() {
                    totalQty += parseFloat($(this).val()) || 0;
                });
                $('#productCountBadge').text(rows + (rows === 1 ? ' item' : ' items'));
                $('#totalRowsText').text(rows + (rows === 1 ? ' row' : ' rows'));
                $('#totalQtyText').text(totalQty);
                $('#productTable, .products-toolbar').toggle(rows > 0);
                $('#productsEmptyHint').toggle(rows === 0);
            }

            $('#addRowBtn').on('click', function() {
                rowCount++;
                var $row = $('#rowTemplate tr').clone();
                $row.find('.row-index').text(rowCount);
                $('#productRows').append($row);
                initSelect2($row);
                renumberRows();
                updateProductsSummary();
            });

            $('#addFromPrBtn').on('click', function() {
                var $opt = $('#prRemainingSelect option:selected');
                var productId = $opt.val();
                if (!productId) {
                    alert('একটা item সিলেক্ট করুন।');
                    return;
                }

                var categoryId = $opt.data('category');
                var purchasetype = $opt.data('purchasetype');
                var remaining = parseFloat($opt.data('remaining'));

                rowCount++;
                var $row = $('#prRowTemplate tr').clone();
                $('#productRows').append($row);
                initSelect2($row);

                $row.find('.category-select').val(categoryId).trigger('change.select2');

                $.get("{{ route('project.transferproject.filterproduct') }}", {
                        category_id: categoryId
                    })
                    .done(function(res) {
                        var options = '<option value="">-- Select Product --</option>';
                        $.each(res, function(i, p) {
                            var sel = (p.id == productId) ? 'selected' : '';
                            options += '<option value="' + p.id + '" ' + sel + '>' + p.name +
                                '</option>';
                        });
                        $row.find('.product-select').html(options).trigger('change.select2');

                        $row.find('.purchasetype-select').val(purchasetype || 'local');
                        $row.find('.qty-input').attr('max', remaining).val(remaining);
                        $row.find('input[name="requested_qty[]"]').val(remaining);
                        updateRemainingDisplay($row);

                        $row.find('.product-select').trigger('change');
                    });

                $opt.remove();
                $('#prRemainingSelect').val('').trigger('change.select2');

                renumberRows();
                updateProductsSummary();
            });

            $('#productRows').on('click keypress', '.remove-row-btn', function(e) {
                if (e.type === 'keypress' && e.which !== 13 && e.which !== 32) return;
                if ($('#productRows tr').length > 1) {
                    $(this).closest('tr').remove();
                    renumberRows();
                    updateProductsSummary();
                } else {
                    alert('At least one product row is required.');
                }
            });

            $('#productRows').on('change', '.category-select', function() {
                var $row = $(this).closest('tr');
                var categoryId = $(this).val();
                var $productSelect = $row.find('.product-select');
                $productSelect.html('<option value="">Loading...</option>');

                $.get("{{ route('project.transferproject.filterproduct') }}", {
                        category_id: categoryId
                    })
                    .done(function(res) {
                        var options = '<option value="">-- Select Product --</option>';
                        $.each(res, function(i, p) {
                            options += '<option value="' + p.id + '">' + p.name + '</option>';
                        });
                        $productSelect.html(options).trigger('change');
                    });
            });


            function currentFromKey() {
                var type = '{{ $editInfo->transfer_type }}';
                if (type === 'branch_to_project') {
                    return {
                        type: 'branch',
                        id: $('input[name=from_branch_id]').val()
                    };
                }
                return {
                    type: 'project',
                    id: $('select[name=from_project_id]').val()
                };
            }

            $('#productRows').on('change', '.product-select, .purchasetype-select', function() {
                var $row = $(this).closest('tr');
                var productId = $row.find('.product-select').val();
                var purchaseType = $row.find('.purchasetype-select').val();
                var from = currentFromKey();

                if (!productId || !from.id) {
                    $row.find('.stock-display').val('-');
                    return;
                }
                $row.find('.stock-display').val('Checking...');


                $.get("{{ route('project.transferproject.availableStock') }}", {
                    product_id: productId,
                    source_type: from.type,
                    source_id: from.id,
                    purchase_type: purchaseType
                }).done(function(res) {
                    $row.find('.stock-display').val(res.quantity + ' ' + (res.unit || ''));
                    $row.data('available', res.quantity);
                    validateQty($row);
                });
            });

            function validateQty($row) {
                var available = $row.data('available');
                var qty = parseFloat($row.find('.qty-input').val()) || 0;
                var $hint = $row.find('.stock-hint');
                if (available === undefined) {
                    $hint.text('');
                    return;
                }
                if (qty > available) {
                    $hint.removeClass('ok').addClass('low').text('No stock available (Available: ' + available +
                        ')');
                } else {
                    $hint.removeClass('low').addClass('ok').text('OK');
                }
            }

            $('#productRows').on('input', '.qty-input', function() {
                var $row = $(this).closest('tr');
                validateQty($row);
                updateRemainingDisplay($row);
                updateProductsSummary();
            });

            $(document).on('change', 'input[name=from_branch_id], select[name=from_project_id]', function() {
                $('#productRows tr').each(function() {
                    $(this).find('.product-select').trigger('change');
                });
            });

            $('#transferForm').on('submit', function(e) {
                var blocked = false,
                    overRequested = false;
                $('#productRows tr').each(function() {
                    var $row = $(this);
                    var available = $row.data('available');
                    var qty = parseFloat($row.find('.qty-input').val()) || 0;
                    var requested = parseFloat($row.find('input[name="requested_qty[]"]').val()) ||
                        0;
                    if (available !== undefined && qty > available) blocked = true;
                    if (requested > 0 && qty > requested) overRequested = true;
                });
                if (blocked) {
                    e.preventDefault();
                    alert('One or more rows exceed available stock.');
                    return;
                }
                if (overRequested) {
                    e.preventDefault();
                    alert('One or more rows exceed the remaining requisition quantity.');
                    return;
                }
                $(this).find('button[type=submit]').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Updating...');
            });

        });

        function resolveRouteNode(target) {
            return target === 'to_branch_id' ? $('.route-node-to') : $('.route-node-from');
        }

        function updateRouteLabel(target, text) {
            var $resolved = resolveRouteNode(target).find('.route-resolved-text[data-target="' + target + '"]');
            if ($resolved.length) $resolved.text(text || '-');
        }

        $(document).on('change', '.branch-picker', function() {
            var $picker = $(this);
            var target = $picker.data('target');
            var $wrap = $('.warehouse-wrap[data-target="' + target + '"]');
            var $wh = $wrap.find('.warehouse-picker');
            var branchId = $picker.val();
            var branchTxt = $picker.find('option:selected').text();

            $('input[type=hidden][name="' + target + '"]').val(branchId).trigger('change');
            updateRouteLabel(target, branchId ? branchTxt : '');

            if (!branchId) {
                $wrap.hide();
                return;
            }

            $.get("{{ route('project.transferproject.getWarehouses') }}", {
                    branch_id: branchId
                })
                .done(function(res) {
                    if (res.length > 0) {
                        var options = '<option value="">-- Warehouse --</option>';
                        $.each(res, function(i, w) {
                            options += '<option value="' + w.id + '">' + w.name + '</option>';
                        });
                        $wh.html(options).trigger('change.select2');
                        $wrap.show();
                    } else {
                        $wrap.hide();
                    }
                });
        });

        $(document).on('change', '.warehouse-picker', function() {
            var $picker = $(this);
            var target = $picker.data('target');
            var whId = $picker.val();
            var whTxt = $picker.find('option:selected').text();
            var branchTxt = $('.branch-picker[data-target="' + target + '"]').find('option:selected').text();

            if (whId) {
                $('input[type=hidden][name="' + target + '"]').val(whId).trigger('change');
                updateRouteLabel(target, whTxt);
            } else {
                var branchId = $('.branch-picker[data-target="' + target + '"]').val();
                $('input[type=hidden][name="' + target + '"]').val(branchId).trigger('change');
                updateRouteLabel(target, branchTxt);
            }
        });
    </script>
@endsection
