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
            cursor: pointer;
            position: relative;
            transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
            background: #fff;
            margin: 0;
            min-height: 44px;
        }

        .transfer-type-card:hover {
            border-color: #adb5bd;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .06);
        }

        .transfer-type-card:focus-within {
            outline: 3px solid rgba(0, 123, 255, .35);
            outline-offset: 2px;
        }

        .transfer-type-card input[type=radio] {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 20px;
            height: 20px;
            margin: 0;
        }

        .transfer-type-card.active {
            border-color: var(--tt-primary);
            background: var(--tt-primary-soft);
            box-shadow: 0 2px 8px rgba(0, 123, 255, .15);
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

        .transfer-type-card .tt-flow {
            font-size: 13px;
            margin-top: 8px;
            color: #495057;
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

        .route-fields {
            display: none;
            margin-bottom: 1rem;
        }

        .route-fields.show {
            display: block;
        }

        .route-fields:last-child {
            margin-bottom: 0;
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

        .form-control:focus {
            border-color: var(--tt-primary);
            box-shadow: 0 0 0 .2rem rgba(0, 123, 255, .18);
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

        .route-resolved-box .route-resolved-text.text-muted {
            color: var(--tt-muted);
            font-style: italic;
        }

        .products-panel {
            background: #fff;
            border: 1px solid var(--tt-border);
            border-radius: var(--tt-radius);
            padding: 16px;
        }

        /* 10-Aug-2026: lock-warning note-এর জায়গায় তথ্যমূলক নোট — এখন আর কোনো row
                                       lock হয় না, item delete করে বা qty কমিয়ে partial transfer করা যায়। */
        .products-info-note {
            display: none;
            align-items: center;
            gap: 8px;
            background: #eef7ff;
            color: #0c5aa6;
            border: 1px solid #bfe0ff;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 14px;
        }

        .products-info-note.show {
            display: flex;
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

        @media (min-width: 768px) {
            #productTable tbody tr:hover {
                background: #fbfbfd;
            }

            #productTable td.row-index-cell {
                text-align: center;
            }
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
            transition: background .15s ease;
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


        .remaining-hint {
            font-size: 11px;
            color: #868e96;
            display: block;
            margin-top: 2px;
        }

        .product-row-loading {
            opacity: .6;
            pointer-events: none;
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

            #productTable td.row-index-cell {
                padding-bottom: 8px;
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

            .transfer-type-wrap {
                gap: 10px;
            }

            .transfer-type-card {
                flex: 1 1 100%;
            }
        }
    </style>
@endsection

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
                        <li class="breadcrumb-item active"><span>Create</span></li>
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
                    <h3 class="card-title"><i class="fas fa-exchange-alt mr-2"></i>New Project Transfer</h3>
                </div>

                <form action="{{ route('project.transferproject.store') }}" method="POST" id="transferForm">
                    @csrf
                    <div class="card-body">

                        {{-- ============ STEP 1: TRANSFER TYPE ============ --}}
                        <div class="section-title"><span class="step-num">1</span> Choose Transfer Type</div>

                        <div class="transfer-type-wrap" role="radiogroup" aria-label="Transfer type">
                            <label class="transfer-type-card" data-type="branch_to_project">
                                <input type="radio" name="transfer_type" value="branch_to_project" checked>
                                <div class="tt-icon"><i class="fas fa-warehouse"></i></div>
                                <div class="tt-title">Branch / Warehouse &rarr; Project</div>
                                <div class="tt-sub">Issue material from stock to a running project</div>
                                <div class="tt-flow"><i class="fas fa-building"></i> Branch &nbsp;<i
                                        class="fas fa-long-arrow-alt-right"></i>&nbsp; <i
                                        class="fas fa-diagram-project"></i> Project</div>
                                <span class="tt-badge badge-req"><i class="fas fa-file-alt"></i> Requisition required</span>
                            </label>

                            <label class="transfer-type-card" data-type="project_to_project">
                                <input type="radio" name="transfer_type" value="project_to_project">
                                <div class="tt-icon"><i class="fas fa-people-arrows"></i></div>
                                <div class="tt-title">Project &rarr; Project</div>
                                <div class="tt-sub">Move surplus material between two projects</div>
                                <div class="tt-flow"><i class="fas fa-diagram-project"></i> Project &nbsp;<i
                                        class="fas fa-long-arrow-alt-right"></i>&nbsp; <i
                                        class="fas fa-diagram-project"></i> Project</div>
                                <span class="tt-badge badge-noreq"><i class="fas fa-check"></i> No requisition needed</span>
                            </label>

                            <label class="transfer-type-card" data-type="project_to_branch">
                                <input type="radio" name="transfer_type" value="project_to_branch">
                                <div class="tt-icon"><i class="fas fa-undo-alt"></i></div>
                                <div class="tt-title">Project &rarr; Branch / Warehouse</div>
                                <div class="tt-sub">Return unused material back to stock</div>
                                <div class="tt-flow"><i class="fas fa-diagram-project"></i> Project &nbsp;<i
                                        class="fas fa-long-arrow-alt-right"></i>&nbsp; <i class="fas fa-building"></i>
                                    Branch</div>
                                <span class="tt-badge badge-noreq"><i class="fas fa-check"></i> No requisition needed</span>
                            </label>
                        </div>

                        <hr>

                        {{-- ============ STEP 2: ROUTE DETAILS ============ --}}
                        <div class="section-title"><span class="step-num">2</span> Transfer Details</div>

                        <div class="details-panel">
                            <div class="form-row">
                                <div class="col-lg-2 col-md-4 col-sm-6 form-group">
                                    <label>Date <span class="required-star">*</span></label>
                                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}"
                                        required>
                                </div>

                                <div class="col-lg-2 col-md-4 col-sm-6 form-group">
                                    <label>Invoice / Reference No</label>
                                    <input type="text" class="form-control" value="{{ $transferCode ?? '' }}" readonly>
                                </div>

                                <div class="col-lg-3 col-md-4 col-sm-6 form-group route-fields rf-requisition"
                                    data-rule="branch_to_project,project_to_project">
                                    <label>Purchase Requisition <span class="required-star">*</span></label>
                                    <select name="purchase_requisition" class="form-control select2 rf-input"
                                        data-rule="branch_to_project,project_to_project">
                                        <option value="">-- Select Requisition --</option>
                                        @foreach ($purchaserequisitions as $pr)
                                            <option value="{{ $pr->id }}">{{ $pr->invoice_no }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Only <b>Accepted</b> requisitions are shown.</small>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-6 form-group route-fields rf-source-branch"
                                    data-rule="branch_to_project">
                                    <label>Source Branch <span class="required-star">*</span></label>
                                    <select class="form-control select2 branch-picker" data-target="from_branch_id"
                                        data-rule="branch_to_project">
                                        <option value="">-- Select Branch --</option>
                                        @foreach ($branchs->where('parent_id', 0) as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>

                                    <input type="hidden" name="from_branch_id" class="rf-input"
                                        data-rule="branch_to_project">
                                </div>

                                <div class="col-lg-2 col-md-6 col-sm-6 form-group route-fields rf-source-warehouse"
                                    data-rule="branch_to_project">
                                    <label>Warehouse <span class="text-muted">(optional)</span></label>
                                    <div class="warehouse-wrap" data-target="from_branch_id" style="display:none">
                                        <select class="form-control select2 warehouse-picker"
                                            data-target="from_branch_id">
                                            <option value="">-- Use Branch itself --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-6 form-group route-fields rf-dest-branch"
                                    data-rule="project_to_branch">
                                    <label>Destination Branch <span class="required-star">*</span></label>
                                    <select class="form-control select2 branch-picker" data-target="to_branch_id"
                                        data-rule="project_to_branch">
                                        <option value="">-- Select Branch --</option>
                                        @foreach ($branchs->where('parent_id', 0) as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>

                                    <input type="hidden" name="to_branch_id" class="rf-input"
                                        data-rule="project_to_branch">
                                </div>

                                <div class="col-lg-2 col-md-6 col-sm-6 form-group route-fields rf-dest-warehouse"
                                    data-rule="project_to_branch">
                                    <label>Warehouse <span class="text-muted">(optional)</span></label>
                                    <div class="warehouse-wrap" data-target="to_branch_id" style="display:none">
                                        <select class="form-control select2 warehouse-picker" data-target="to_branch_id">
                                            <option value="">-- Use Branch itself --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="route-visual">
                                <div class="route-node route-node-from">
                                    <span class="route-node-tag"><i class="fas fa-upload"></i> FROM</span>

                                    <div class="form-group route-fields rf-branch-from-display"
                                        data-rule="branch_to_project">
                                        <label class="mb-1">Source</label>
                                        <div class="route-resolved-box">
                                            <i class="fas fa-warehouse mr-2 text-muted"></i>
                                            <span class="route-resolved-text text-muted" data-target="from_branch_id">--
                                                Select branch above --</span>
                                        </div>
                                    </div>

                                    <div class="form-group route-fields rf-project-from">
                                        <label>Project <span class="required-star">*</span></label>
                                        <select name="from_project_id" class="form-control select2 rf-input"
                                            data-rule="project_to_project,project_to_branch">
                                            <option value="">-- Select Project --</option>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="route-arrow" aria-hidden="true">
                                    <i class="fas fa-long-arrow-alt-right"></i>
                                </div>

                                <div class="route-node route-node-to">
                                    <span class="route-node-tag"><i class="fas fa-flag-checkered"></i> TO</span>

                                    <div class="form-group route-fields rf-project-to">
                                        <label>Project <span class="required-star">*</span></label>
                                        <select name="to_project_id_a" class="form-control select2 rf-input"
                                            data-rule="branch_to_project">
                                            <option value="">-- Select Project --</option>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group route-fields rf-project-to2">
                                        <label>Project <span class="required-star">*</span></label>
                                        <select name="to_project_id_b" class="form-control select2 rf-input"
                                            data-rule="project_to_project">
                                            <option value="">-- Select Project --</option>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group route-fields rf-branch-to-display"
                                        data-rule="project_to_branch">
                                        <label class="mb-1">Destination</label>
                                        <div class="route-resolved-box">
                                            <i class="fas fa-building mr-2 text-muted"></i>
                                            <span class="route-resolved-text text-muted" data-target="to_branch_id">--
                                                Select branch above --</span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12 form-group">
                                    <label>Note</label>
                                    <textarea name="note" class="form-control" rows="2" placeholder="Optional remarks..."></textarea>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- ============ STEP 3: PRODUCTS ============ --}}
                        <div class="section-title justify-content-between">
                            <span><span class="step-num">3</span> Products to Transfer</span>
                            <span class="product-count-badge" id="productCountBadge">1 item</span>
                        </div>

                        <div class="products-panel">
                            {{-- 10-Aug-2026: আগে এখানে "products are locked" সতর্কবার্তা ছিল।
                                 এখন row আর লক হয় না — এটা শুধু জানানোর জন্য যে remaining qty
                                 auto-fill হয়েছে, চাইলে কমানো/মুছে ফেলা যাবে। --}}
                            <div class="products-info-note" id="productsInfoNote">
                                <i class="fas fa-info-circle"></i>
                                নির্বাচিত Requisition থেকে বাকি থাকা (remaining) কোয়ান্টিটি auto-fill হয়েছে। স্টক না
                                থাকলে সেই Qty কমিয়ে দিন অথবা row মুছে দিয়ে Transfer চালিয়ে যান — বাকি অংশ পরে আবার
                                একই Requisition থেকে transfer করা যাবে।
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="productTable">
                                    <thead>
                                        <tr>
                                            <th style="width:5%">#</th>
                                            <th style="width:17%">Category</th>
                                            <th style="width:20%">Product</th>
                                            <th style="width:12%">Purchase Type</th>

                                            <th style="width:14%">Available Stock</th>
                                            <th style="width:15%">Qty</th>
                                            <th style="width:8%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="productRows">
                                        {{-- rows injected by JS --}}
                                    </tbody>
                                </table>
                            </div>

                            <div class="products-empty" id="productsEmptyHint">
                                <i class="fas fa-box-open"></i>
                                No products added yet. Use "Add Product Row" to start.
                            </div>

                            <div class="products-toolbar">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addRowBtn">
                                    <i class="fas fa-plus"></i> Add Product Row
                                </button>
                                <div class="products-summary">
                                    <strong id="totalRowsText">1 row</strong> &middot;
                                    Total qty: <strong id="totalQtyText">0</strong>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('project.transferproject.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Submit Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Hidden template row --}}
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
                <td data-label="Available Stock">
                    <input type="text" class="form-control stock-display" value="-" readonly>
                </td>
                <td data-label="Qty">
                    <input type="number" name="qty[]" min="0.01" step="0.01" class="form-control qty-input"
                        required>
                    <span class="remaining-hint"></span>
                    <div class="stock-hint"></div>
                </td>
                <td class="text-center remove-cell">
                    <i class="fas fa-trash text-danger remove-row-btn" title="Remove row" role="button"
                        tabindex="0"></i>
                </td>
                {{-- requested_qty শুধু ব্যাকগ্রাউন্ড ভ্যালিডেশনের জন্য, UI-তে দেখানো হয় না --}}
                <input type="hidden" name="requested_qty[]" value="">
            </tr>
        </tbody>
    </table>
@endsection

@section('scripts')
    <script src="{{ asset('backend/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function() {

            $('#rowTemplate .select2').each(function() {
                var $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
            });

            /* ---------------- Transfer type switching ---------------- */
            function applyTransferType(type) {
                $('.transfer-type-card').removeClass('active');
                $('.transfer-type-card[data-type="' + type + '"]').addClass('active');

                $('.route-fields').removeClass('show').hide();
                $('.rf-input').prop('required', false);

                $('.route-fields').each(function() {
                    var $field = $(this);
                    var input = $field.find('.rf-input');
                    var ruleAttr = input.length ? input.data('rule') : $field.data('rule');
                    var rules = (ruleAttr || '').toString().split(',');
                    if (rules.indexOf(type) !== -1) {
                        $field.addClass('show').show();
                        input.prop('required', true);
                    }
                });

                if (type !== 'branch_to_project' && type !== 'project_to_project') {
                    var $req = $('select[name=purchase_requisition]');
                    if ($req.val()) {
                        $req.val(null).trigger('change');
                    } else {
                        resetToSingleEmptyRow();
                    }
                }

                $('.branch-picker').each(function() {
                    var $bp = $(this);
                    var rule = ($bp.data('rule') || '').toString();
                    if (rule && rule !== type && $bp.val()) {
                        $bp.val(null).trigger('change');
                    }
                });
            }

            $('input[name=transfer_type]').on('change', function() {
                applyTransferType($(this).val());
            });
            $('.transfer-type-card').on('click', function() {
                $(this).find('input[type=radio]').prop('checked', true).trigger('change');
            });
            applyTransferType($('input[name=transfer_type]:checked').val());

            /* ---------------- Product rows ---------------- */
            var rowCount = 0;

            function initSelect2(scope) {
                scope.find('.select2').each(function() {
                    var $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }
                    $el.select2({
                        width: '100%',
                        dropdownAutoWidth: true
                    });
                });
            }

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

            function addRow() {
                rowCount++;
                var $row = $('#rowTemplate tr').clone();
                $row.find('.row-index').text(rowCount);
                $('#productRows').append($row);
                initSelect2($row);
                renumberRows();
                updateProductsSummary();
            }

            $('#addRowBtn').on('click', addRow);
            addRow();

            /* 10-Aug-2026: এখন প্রতিটা row-ই মুছে ফেলা যাবে, requisition থেকে
               auto-loaded হোক বা ম্যানুয়ালি যোগ করা হোক — আর কোনো row-locked
               চেক নেই। */
            $('#productRows').on('click keypress', '.remove-row-btn', function(e) {
                if (e.type === 'keypress' && e.which !== 13 && e.which !== 32) return;
                if ($('#productRows tr').length > 1) {
                    $(this).closest('tr').remove();
                    renumberRows();
                    updateProductsSummary();
                } else {
                    toastr && toastr.warning ? toastr.warning('At least one product row is required.') :
                        alert('At least one product row is required.');
                }
            });

            $('#productRows').on('change', '.category-select', function() {
                var $row = $(this).closest('tr');
                var categoryId = $(this).val();
                var $productSelect = $row.find('.product-select');

                $row.addClass('product-row-loading');
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
                    })
                    .always(function() {
                        $row.removeClass('product-row-loading');
                    });
            });

            function currentFromKey() {
                var type = $('input[name=transfer_type]:checked').val();
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

            $('#productRows').on('input', '.qty-input', function() {
                validateQty($(this).closest('tr'));
                updateProductsSummary();
            });

            function validateQty($row) {
                var available = $row.data('available');
                var qty = parseFloat($row.find('.qty-input').val()) || 0;
                var requested = parseFloat($row.find('input[name="requested_qty[]"]').val()) || 0;
                var $hint = $row.find('.stock-hint');
                var $remainingHint = $row.find('.remaining-hint');

                if (requested > 0 && qty > requested) {
                    $remainingHint.text('Requested qty (' + requested + ') exceeded!').css('color', '#dc3545');
                } else {
                    $remainingHint.text('');
                }

                if (available === undefined) {
                    $hint.text('');
                    return;
                }

                if (qty > available) {
                    $hint.removeClass('ok').addClass('low').text(
                        'Stock নেই (Available: ' + available + '). Qty কমান অথবা row মুছে দিন।'
                    );
                } else {
                    $hint.removeClass('low').addClass('ok').text('OK');
                }
            }

            $(document).on('change',
                'input[name=from_branch_id], select[name=from_project_id], input[name=transfer_type]',
                function() {
                    $('#productRows tr').each(function() {
                        $(this).find('.product-select').trigger('change');
                    });
                });

            /* ---------------- Purchase Requisition -> auto-fill remaining products ---------------- */

            function resetToSingleEmptyRow() {
                $('#productRows').empty();
                rowCount = 0;
                $('#productsInfoNote').removeClass('show');
                addRow();
            }

            function parsePrDetailsHtml(html) {
                var items = [];
                $('<table><tbody>' + (html || '') + '</tbody></table>').find('tr').each(function() {
                    var $tr = $(this);
                    var categoryId = $tr.find('input[name="category_nm[]"]').val();
                    var productId = $tr.find('input[name="product_nm[]"]').val();
                    var purchaseType = $tr.find('input[name="purchasetype[]"]').val();
                    var qty = $tr.find('input[name="qty[]"]').val(); // backend থেকে remaining qty আসে
                    var requestedQty = $tr.find('input[name="requested_qty[]"]').val();
                    if (categoryId && productId) {
                        items.push({
                            category_id: categoryId,
                            product_id: productId,
                            purchasetype: purchaseType || 'local',
                            qty: qty || 1,
                            requested_qty: requestedQty || null
                        });
                    }
                });
                return items;
            }


            function addRequisitionRow(item) {
                rowCount++;
                var $row = $('#rowTemplate tr').clone();
                $('#productRows').append($row);
                initSelect2($row);

                $row.find('.category-select').val(item.category_id).trigger('change.select2');

                return $.get("{{ route('project.transferproject.filterproduct') }}", {
                        category_id: item.category_id
                    })
                    .done(function(res) {
                        var options = '<option value="">-- Select Product --</option>';
                        $.each(res, function(i, p) {
                            options += '<option value="' + p.id + '">' + p.name + '</option>';
                        });
                        $row.find('.product-select').html(options);
                        $row.find('.purchasetype-select').val(item.purchasetype);

                        var $qty = $row.find('.qty-input');
                        $qty.val(item.qty);



                        $row.find('.product-select').val(item.product_id).trigger('change');
                    });
            }

            function loadRequisitionProducts(reqId) {
                $.ajax({
                        url: "{{ route('project.transferproject.searchpr') }}",
                        data: {
                            id: reqId
                        },
                        dataType: 'json'
                    })
                    .done(function(res) {
                        if (typeof res === 'string') {
                            try {
                                res = JSON.parse(res);
                            } catch (e) {
                                console.error('searchpr did not return valid JSON:', res);
                                alert('Could not load products for the selected requisition.');
                                return;
                            }
                        }

                        var items = parsePrDetailsHtml(res.prdetails);

                        $('#productRows').empty();
                        rowCount = 0;

                        if (!items.length) {
                            console.warn('searchpr returned no matching product rows.', res);
                            $('#productsInfoNote').removeClass('show');
                            alert('এই Requisition-এর সব পণ্য ইতিমধ্যে সম্পূর্ণভাবে Transfer করা হয়ে গেছে।');
                            addRow();
                            return;
                        }

                        var chain = $.Deferred().resolve();
                        $.each(items, function(i, item) {
                            chain = chain.then(function() {
                                return addRequisitionRow(item);
                            });
                        });
                        chain.then(function() {
                            renumberRows();
                            updateProductsSummary();
                        });

                        $('#productsInfoNote').addClass('show');
                    })
                    .fail(function(xhr) {
                        console.error('searchpr request failed:', xhr.status, xhr.responseText);
                        alert(
                            'Could not load products for the selected requisition (see console for details). Please add products manually.'
                        );
                    });
            }

            $(document).on('change', 'select[name=purchase_requisition]', function() {
                var reqId = $(this).val();
                if (reqId) {
                    loadRequisitionProducts(reqId);
                } else {
                    resetToSingleEmptyRow();
                }
            });

            /* ---------------- Submit guard ---------------- */
            $('#transferForm').on('submit', function(e) {
                var blocked = false;
                var overRequested = false;

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
                    alert(
                        'One or more rows exceed available stock. Please reduce qty or remove the row before submitting.'
                    );
                    return;
                }
                if (overRequested) {
                    e.preventDefault();
                    alert(
                        'One or more rows exceed the originally requested quantity. Please correct before submitting.'
                    );
                    return;
                }

                $(this).find('button[type=submit]').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
            });

        });


        /* ---------------- Branch -> Warehouse cascading ---------------- */
        function resolveRouteNode(target) {
            return target === 'to_branch_id' ? $('.route-node-to') : $('.route-node-from');
        }

        function updateRouteLabel(target, text) {
            var $node = resolveRouteNode(target);
            $node.find('.route-node-selected').text(text ? '· ' + text : '');
            var $resolved = $node.find('.route-resolved-text[data-target="' + target + '"]');
            if ($resolved.length) {
                $resolved.text(text || '-- Select branch above --')
                    .toggleClass('text-muted', !text);
            }
        }

        function branchPickerFor(target) {
            return $('.branch-picker[data-target="' + target + '"]');
        }

        function warehouseWrapFor(target) {
            return $('.warehouse-wrap[data-target="' + target + '"]');
        }

        function hiddenInputFor(target) {
            return $('input[type=hidden][name="' + target + '"]');
        }

        $(document).on('change', '.branch-picker', function() {
            var $picker = $(this);
            var target = $picker.data('target');
            var $wrap = warehouseWrapFor(target);
            var $wh = $wrap.find('.warehouse-picker');
            var $hidden = hiddenInputFor(target);
            var branchId = $picker.val();
            var branchTxt = $picker.find('option:selected').text();

            $hidden.val(branchId).trigger('change');
            updateRouteLabel(target, branchId ? branchTxt : '');

            if (!branchId) {
                $wrap.hide();
                $wh.val(null).html('<option value="">-- Use Branch itself --</option>');
                return;
            }

            $.get("{{ route('project.transferproject.getWarehouses') }}", {
                    branch_id: branchId
                })
                .done(function(res) {
                    if (res.length > 0) {
                        var options = '<option value="">-- Use Branch itself --</option>';
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
            var $hidden = hiddenInputFor(target);
            var whId = $picker.val();
            var whTxt = $picker.find('option:selected').text();
            var branchTxt = branchPickerFor(target).find('option:selected').text();

            if (whId) {
                $hidden.val(whId).trigger('change');
                updateRouteLabel(target, whTxt);
            } else {
                var branchId = branchPickerFor(target).val();
                $hidden.val(branchId).trigger('change');
                updateRouteLabel(target, branchTxt);
            }
        });
    </script>
@endsection
