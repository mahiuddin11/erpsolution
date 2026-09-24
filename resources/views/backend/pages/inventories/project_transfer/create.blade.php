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

        .remaining-display {
            font-weight: 600;
            font-size: 14px;
        }

        .badge-req {
            background: #fff3cd;
            color: #856404;
        }

        .badge-noreq {
            background: #d4edda;
            color: #155724;
        }

        .badge-wh {
            background: #e2e3f5;
            color: #383d8f;
            margin-left: 4px;
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

        /* Info note shown when remaining qty is auto-loaded from a requisition.
                   Rows are NOT locked: deleting a row or reducing qty stays possible. */
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
                        <div class="section-title"><span class="step-num">1</span> Choose Transfer Type</div>

                        <div class="transfer-type-wrap" role="radiogroup" aria-label="Transfer type">
                            <label class="transfer-type-card" data-type="branch_to_project">
                                <input type="radio" name="transfer_type" value="branch_to_project" checked>
                                <div class="tt-icon"><i class="fas fa-warehouse"></i></div>
                                <div class="tt-title">Branch / Warehouse &rarr; Project</div>
                                <div class="tt-sub">Issue material from a warehouse to a running project</div>
                                <div class="tt-flow"><i class="fas fa-building"></i> Branch/warehouse &nbsp;<i
                                        class="fas fa-long-arrow-alt-right"></i>&nbsp; <i
                                        class="fas fa-diagram-project"></i> Project</div>
                                <span class="tt-badge badge-req"><i class="fas fa-file-alt"></i> Requisition required</span>
                                <span class="tt-badge badge-wh"><i class="fas fa-warehouse"></i> Warehouse required</span>
                            </label>

                            <label class="transfer-type-card" data-type="project_to_project">
                                <input type="radio" name="transfer_type" value="project_to_project">
                                <div class="tt-icon"><i class="fas fa-people-arrows"></i></div>
                                <div class="tt-title">Project &rarr; Project</div>
                                <div class="tt-sub">Move surplus material between two projects</div>
                                <div class="tt-flow"><i class="fas fa-diagram-project"></i> Project &nbsp;<i
                                        class="fas fa-long-arrow-alt-right"></i>&nbsp; <i
                                        class="fas fa-diagram-project"></i> Project</div>
                                <span class="tt-badge badge-req"><i class="fas fa-file-alt"></i> Requisition required</span>
                            </label>

                            <label class="transfer-type-card" data-type="project_to_branch">
                                <input type="radio" name="transfer_type" value="project_to_branch">
                                <div class="tt-icon"><i class="fas fa-undo-alt"></i></div>
                                <div class="tt-title">Project &rarr; Branch / Warehouse</div>
                                <div class="tt-sub">Return unused material back to a warehouse</div>
                                <div class="tt-flow"><i class="fas fa-diagram-project"></i> Project &nbsp;<i
                                        class="fas fa-long-arrow-alt-right"></i>&nbsp; <i class="fas fa-building"></i>
                                    Branch/warehouse</div>
                                <span class="tt-badge badge-noreq"><i class="fas fa-check"></i> No requisition needed</span>
                                <span class="tt-badge badge-wh"><i class="fas fa-warehouse"></i> Warehouse required</span>
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
                                    <small class="text-muted">Final number is assigned on save.</small>
                                </div>

                                {{-- Purchase requisition (branch_to_project, project_to_project) --}}
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
                                    <small class="text-muted">Showing only <b>Accepted</b> Requisitions with remaining
                                        items.</small>
                                </div>

                                {{-- SOURCE branch + warehouse (branch_to_project) --}}
                                <div class="col-lg-3 col-md-6 col-sm-6 form-group route-fields rf-source-branch"
                                    data-rule="branch_to_project">
                                    <label>Source Branch <span class="required-star">*</span></label>
                                    <select name="from_branch_id" class="form-control select2 branch-picker rf-input"
                                        data-target="from_branch_id" data-rule="branch_to_project">
                                        <option value="">-- Select Branch --</option>
                                        @foreach ($branchs->where('parent_id', 0) as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-6 col-sm-6 form-group route-fields rf-source-warehouse"
                                    data-rule="branch_to_project">
                                    <label>Warehouse <span class="required-star">*</span></label>
                                    <select name="from_warehouse_id"
                                        class="form-control select2 warehouse-picker rf-input"
                                        data-target="from_branch_id" data-rule="branch_to_project">
                                        <option value="">-- Select branch first --</option>
                                    </select>
                                </div>

                                {{-- DESTINATION branch + warehouse (project_to_branch) --}}
                                <div class="col-lg-3 col-md-6 col-sm-6 form-group route-fields rf-dest-branch"
                                    data-rule="project_to_branch">
                                    <label>Destination Branch <span class="required-star">*</span></label>
                                    <select name="to_branch_id" class="form-control select2 branch-picker rf-input"
                                        data-target="to_branch_id" data-rule="project_to_branch">
                                        <option value="">-- Select Branch --</option>
                                        @foreach ($branchs->where('parent_id', 0) as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-6 col-sm-6 form-group route-fields rf-dest-warehouse"
                                    data-rule="project_to_branch">
                                    <label>Warehouse <span class="required-star">*</span></label>
                                    <select name="to_warehouse_id" class="form-control select2 warehouse-picker rf-input"
                                        data-target="to_branch_id" data-rule="project_to_branch">
                                        <option value="">-- Select branch first --</option>
                                    </select>
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
                            <div class="products-info-note" id="productsInfoNote">
                                <i class="fas fa-info-circle"></i>
                                The remaining quantity from the selected requisition is auto-filled. If out of stock, reduce
                                the Qty or delete the row and continue the transfer — the rest can be transferred from the
                                same requisition later.
                            </div>

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
                                        {{-- rows injected by JS --}}
                                    </tbody>
                                </table>
                            </div>

                            <div class="products-empty" id="productsEmptyHint">
                                <i class="fas fa-box-open"></i>
                                No products added yet. Use "Add Product Row" to start.
                            </div>

                            <div class="products-toolbar">
                                {{-- Manual "Add row" is only available for project -> branch (no requisition) --}}
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addRowBtn"
                                    style="display:none">
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
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i> Submit Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Hidden template row (outside the <form>, so it is never submitted) --}}
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
                    {{-- UI-only helper value. The server must re-read the remaining qty from pr_details. --}}
                    <input type="hidden" name="requested_qty[]" value="">
                    <div class="stock-hint"></div>
                    <span class="remaining-hint"></span>
                </td>
                <td data-label="Remaining" class="text-center">
                    <span class="remaining-display text-muted">-</span>
                </td>
                <td class="text-center remove-cell">
                    <i class="fas fa-trash text-danger remove-row-btn" title="Remove row" role="button"
                        tabindex="0"></i>
                </td>
            </tr>
        </tbody>
    </table>
@endsection

@section('scripts')
    <script src="{{ asset('backend/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function() {

            var routes = {
                filterProduct: "{{ route('project.transferproject.filterproduct') }}",
                availableStock: "{{ route('project.transferproject.availableStock') }}",
                searchPr: "{{ route('project.transfer.searchprvoucher') }}",
                getWarehouses: "{{ route('project.transferproject.getWarehouses') }}"
            };

            var rowCount = 0;
            var prXhr = null; // in-flight requisition request
            var reqLoadToken = 0; // invalidates an in-flight requisition load

            /* ---------------- Helpers ---------------- */
            function currentType() {
                return $('input[name=transfer_type]:checked').val();
            }

            function round2(n) {
                return Math.round((n + Number.EPSILON) * 100) / 100;
            }

            function notify(msg) {
                if (typeof toastr !== 'undefined' && toastr.warning) {
                    toastr.warning(msg);
                } else {
                    alert(msg);
                }
            }

            // new Option() escapes text, so product / warehouse names can never inject HTML.
            function fillSelect($sel, items, placeholder) {
                $sel.empty().append(new Option(placeholder, '', true, true));
                $.each(items, function(i, it) {
                    $sel.append(new Option(it.name, it.id));
                });
            }

            // The template row must not carry select2 markup (master layout may init it globally).
            $('#rowTemplate .select2').each(function() {
                var $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
            });

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

            // Top-level selects (requisition, branch, warehouse, project)
            initSelect2($('.details-panel'));

            /* ---------------- Remaining / qty validation ---------------- */
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
                if (remaining < 0) {
                    $display.removeClass('text-success').addClass('text-danger');
                } else {
                    $display.removeClass('text-danger').addClass('text-success');
                }
            }

            // Total qty entered for the same product + purchase type (duplicate rows share one stock pool).
            function groupQty(productId, ptype) {
                var total = 0;
                $('#productRows tr').each(function() {
                    var $r = $(this);
                    if ($r.find('.product-select').val() === productId &&
                        $r.find('.purchasetype-select').val() === ptype) {
                        total += parseFloat($r.find('.qty-input').val()) || 0;
                    }
                });
                return total;
            }

            function validateQty($row) {
                var available = $row.data('available');
                var qty = parseFloat($row.find('.qty-input').val()) || 0;
                var requested = parseFloat($row.find('input[name="requested_qty[]"]').val()) || 0;
                var productId = $row.find('.product-select').val();
                var ptype = $row.find('.purchasetype-select').val();
                var $hint = $row.find('.stock-hint');
                var $remainingHint = $row.find('.remaining-hint');

                if (requested > 0 && qty > requested) {
                    $remainingHint.text('Only ' + requested +
                            ' remaining in the requisition — you cannot enter more than this.')
                        .css('color', '#dc3545');
                } else {
                    $remainingHint.text('');
                }

                if (available === undefined || !productId) {
                    $hint.removeClass('ok low').text('');
                    return;
                }

                var total = groupQty(productId, ptype);
                if (total > available) {
                    var dup = total > qty ? ' [combined qty of duplicate rows: ' + round2(total) + ']' : '';
                    $hint.removeClass('ok').addClass('low').text(
                        'No stock available (Available: ' + available + ')' + dup +
                        '. Reduce the quantity or remove the row.'
                    );
                } else {
                    $hint.removeClass('low').addClass('ok').text('OK');
                }
            }

            function validateAllRows() {
                $('#productRows tr').each(function() {
                    validateQty($(this));
                });
            }

            /* ---------------- Stock lookup (branch + warehouse, or project) ---------------- */
            function currentFromKey() {
                var type = currentType();
                if (type === 'branch_to_project') {
                    var b = $('select[name=from_branch_id]').val();
                    var w = $('select[name=from_warehouse_id]').val();
                    return {
                        ok: !!(b && w),
                        params: {
                            source_type: 'branch',
                            branch_id: b,
                            warehouse_id: w
                        }
                    };
                }
                var p = $('select[name=from_project_id]').val();
                return {
                    ok: !!p,
                    params: {
                        source_type: 'project',
                        project_id: p
                    }
                };
            }

            function checkStock($row) {
                var productId = $row.find('.product-select').val();
                var from = currentFromKey();

                // always drop the previous value so a stale number can never be validated against
                $row.removeData('available');

                if (!productId || !from.ok) {
                    $row.find('.stock-hint').removeClass('ok low').text('');
                    $row.find('.stock-display').val(productId && !from.ok ? 'Select source first' : '-');
                    return;
                }

                var seq = ($row.data('stockSeq') || 0) + 1;
                $row.data('stockSeq', seq);
                $row.find('.stock-display').val('Checking...');

                $.get(routes.availableStock, $.extend({
                        product_id: productId,
                        purchase_type: $row.find('.purchasetype-select').val()
                    }, from.params))
                    .done(function(res) {
                        if ($row.data('stockSeq') !== seq) return; // outdated response
                        $row.find('.stock-display').val(res.quantity + ' ' + (res.unit || ''));
                        $row.data('available', res.quantity);
                        validateAllRows();
                    })
                    .fail(function() {
                        if ($row.data('stockSeq') !== seq) return;
                        $row.find('.stock-display').val('Error');
                    });
            }

            function checkAllStocks() {
                $('#productRows tr').each(function() {
                    checkStock($(this));
                });
            }

            /* ---------------- Product rows ---------------- */
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
                $('#totalQtyText').text(round2(totalQty));

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
                return $row;
            }

            $('#addRowBtn').on('click', addRow);

            $('#productRows').on('click keypress', '.remove-row-btn', function(e) {
                if (e.type === 'keypress') {
                    if (e.which !== 13 && e.which !== 32) return;
                    e.preventDefault();
                }
                if ($('#productRows tr').length > 1) {
                    $(this).closest('tr').remove();
                    renumberRows();
                    updateProductsSummary();
                    validateAllRows();
                } else {
                    notify('At least one product row is required.');
                }
            });

            $('#productRows').on('change', '.category-select', function() {
                var $row = $(this).closest('tr');
                var categoryId = $(this).val();
                var $productSelect = $row.find('.product-select');

                if (!categoryId) {
                    fillSelect($productSelect, [], '-- Select category first --');
                    $productSelect.trigger('change');
                    return;
                }

                $row.addClass('product-row-loading');
                fillSelect($productSelect, [], 'Loading...');

                $.get(routes.filterProduct, {
                        category_id: categoryId
                    })
                    .done(function(res) {
                        fillSelect($productSelect, res, '-- Select Product --');
                        $productSelect.trigger('change');
                    })
                    .fail(function() {
                        fillSelect($productSelect, [], 'Could not load products');
                        $productSelect.trigger('change');
                    })
                    .always(function() {
                        $row.removeClass('product-row-loading');
                    });
            });

            $('#productRows').on('change', '.product-select, .purchasetype-select', function() {
                var $row = $(this).closest('tr');
                checkStock($row);
                validateAllRows();
            });

            $('#productRows').on('input', '.qty-input', function() {
                var $row = $(this).closest('tr');
                updateRemainingDisplay($row);
                validateAllRows(); // duplicate rows share one stock pool
                updateProductsSummary();
            });

            // Source changed (branch / warehouse / project / transfer type) -> re-check every row.
            $(document).on('change',
                'select[name=from_branch_id], select[name=from_warehouse_id], select[name=from_project_id], input[name=transfer_type]',
                function() {
                    checkAllStocks();
                });

            /* ---------------- Purchase Requisition -> auto-fill remaining products ---------------- */
            function cancelRequisitionLoad() {
                reqLoadToken++;
                if (prXhr) {
                    prXhr.abort();
                    prXhr = null;
                }
            }

            function resetToSingleEmptyRow() {
                cancelRequisitionLoad();
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
                    var qty = $tr.find('input[name="qty[]"]').val(); // remaining qty (backend)
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

            // Always resolves (even when the product request fails) so one bad row can't stop the chain.
            function addRequisitionRow(item) {
                var d = $.Deferred();
                var $row = addRow();

                $row.find('.category-select').val(item.category_id).trigger('change.select2');

                $.get(routes.filterProduct, {
                        category_id: item.category_id
                    })
                    .done(function(res) {
                        fillSelect($row.find('.product-select'), res, '-- Select Product --');
                        $row.find('.purchasetype-select').val(item.purchasetype);

                        var $qty = $row.find('.qty-input');
                        $qty.val(item.qty);

                        if (item.requested_qty) {
                            $qty.attr('max', item.requested_qty);
                            $row.find('input[name="requested_qty[]"]').val(item.requested_qty);
                        }

                        updateRemainingDisplay($row);
                        $row.find('.product-select').val(item.product_id).trigger('change');
                    })
                    .fail(function() {
                        notify('Could not load products for one of the requisition rows.');
                    })
                    .always(function() {
                        d.resolve();
                    });

                return d.promise();
            }

            function loadRequisitionProducts(reqId) {
                cancelRequisitionLoad();
                var token = reqLoadToken;

                prXhr = $.ajax({
                        url: routes.searchPr,
                        data: {
                            id: reqId
                        },
                        dataType: 'json'
                    })
                    .done(function(res) {
                        if (token !== reqLoadToken) return;

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
                            alert('All products in this requisition have already been fully transferred.');
                            addRow();
                            return;
                        }

                        var chain = $.Deferred().resolve();
                        $.each(items, function(i, item) {
                            chain = chain.then(function() {
                                if (token !== reqLoadToken)
                                    return; // requisition changed meanwhile
                                return addRequisitionRow(item);
                            });
                        });
                        chain.then(function() {
                            if (token !== reqLoadToken) return;
                            renumberRows();
                            updateProductsSummary();
                            validateAllRows();
                        });

                        $('#productsInfoNote').addClass('show');
                    })
                    .fail(function(xhr) {
                        if (xhr.statusText === 'abort') return;
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

            /* ---------------- Transfer type switching ---------------- */
            function applyTransferType(type) {
                $('.transfer-type-card').removeClass('active');
                $('.transfer-type-card[data-type="' + type + '"]').addClass('active');

                // Show only the fields of this type. Fields of other types are DISABLED,
                // so a stale value from a previous type is never posted.
                $('.route-fields').each(function() {
                    var $field = $(this);
                    var $in = $field.find('.rf-input');
                    var ruleAttr = $in.length ? $in.data('rule') : $field.data('rule');
                    var on = (ruleAttr || '').toString().split(',').indexOf(type) !== -1;

                    $field.toggleClass('show', on).toggle(on);
                    $in.prop('required', on).prop('disabled', !on);
                });

                // Clear values of the fields that were just switched off
                $('select.rf-input').not('[name=purchase_requisition]').each(function() {
                    var $s = $(this);
                    if ($s.prop('disabled') && $s.val()) {
                        $s.val('').trigger('change'); // branch-picker change also resets its warehouse
                    }
                });

                $('#addRowBtn').toggle(type === 'project_to_branch');

                if (type !== 'branch_to_project' && type !== 'project_to_project') {
                    var $req = $('select[name=purchase_requisition]');
                    if ($req.val()) {
                        $req.val(null).trigger('change'); // -> resetToSingleEmptyRow()
                    } else {
                        resetToSingleEmptyRow();
                    }
                }
            }

            // The <label> already toggles the radio natively, so no extra click handler is needed.
            $('input[name=transfer_type]').on('change', function() {
                applyTransferType($(this).val());
            });

            /* ---------------- Branch -> Warehouse cascading (warehouse is mandatory) ---------------- */
            function branchPickerFor(target) {
                return $('.branch-picker[data-target="' + target + '"]');
            }

            function warehousePickerFor(target) {
                return $('.warehouse-picker[data-target="' + target + '"]');
            }

            function updateRouteLabel(target) {
                var $b = branchPickerFor(target);
                var $w = warehousePickerFor(target);
                var b = $b.val() ? $b.find('option:selected').text() : '';
                var w = $w.val() ? $w.find('option:selected').text() : '';
                var text = (b && w) ? b + ' › ' + w : b;

                $('.route-resolved-text[data-target="' + target + '"]')
                    .text(text || '-- Select branch above --')
                    .toggleClass('text-muted', !text);
            }

            $(document).on('change', '.branch-picker', function() {
                var target = $(this).data('target');
                var branchId = $(this).val();
                var $wh = warehousePickerFor(target);

                fillSelect($wh, [], '-- Select branch first --');
                $wh.trigger('change'); // clears stock + route label
                if (!branchId) return;

                fillSelect($wh, [], 'Loading...');
                $wh.trigger('change.select2');

                $.get(routes.getWarehouses, {
                        branch_id: branchId
                    })
                    .done(function(res) {
                        if (!res.length) {
                            fillSelect($wh, [], 'No warehouse under this branch');
                            $wh.trigger('change.select2');
                            alert(
                                'This branch has no warehouse. Create a warehouse first, then make the transfer.'
                                );
                            return;
                        }
                        fillSelect($wh, res, '-- Select Warehouse --');
                        $wh.trigger('change.select2');
                    })
                    .fail(function() {
                        fillSelect($wh, [], 'Could not load warehouses');
                        $wh.trigger('change.select2');
                    });
            });

            $(document).on('change', '.warehouse-picker', function() {
                updateRouteLabel($(this).data('target'));
            });

            // keep the route label in sync when the branch changes too
            $(document).on('change', '.branch-picker', function() {
                updateRouteLabel($(this).data('target'));
            });

            /* ---------------- Submit guard ---------------- */
            var $submitBtn = $('#submitBtn');
            var submitBtnHtml = $submitBtn.html();

            $('#transferForm').on('submit', function(e) {
                var type = currentType();

                function stop(msg) {
                    e.preventDefault();
                    alert(msg);
                }

                // Warehouse is mandatory for branch <-> project transfers
                if (type === 'branch_to_project' && !$('select[name=from_warehouse_id]').val()) {
                    return stop('Please select the source warehouse.');
                }
                if (type === 'project_to_branch' && !$('select[name=to_warehouse_id]').val()) {
                    return stop('Please select the destination warehouse.');
                }
                if (type === 'project_to_project' &&
                    $('select[name=from_project_id]').val() === $('select[name=to_project_id_b]').val()) {
                    return stop('Source and destination project cannot be the same.');
                }

                var unverified = false;
                var overRequested = false;
                var pools = {}; // product|purchasetype -> {sum, available}

                $('#productRows tr').each(function() {
                    var $row = $(this);
                    var available = $row.data('available');
                    var qty = parseFloat($row.find('.qty-input').val()) || 0;
                    var requested = parseFloat($row.find('input[name="requested_qty[]"]').val()) ||
                        0;
                    var key = $row.find('.product-select').val() + '|' + $row.find(
                            '.purchasetype-select')
                        .val();

                    if (available === undefined) unverified = true;
                    if (requested > 0 && qty > requested) overRequested = true;

                    pools[key] = pools[key] || {
                        sum: 0,
                        available: available
                    };
                    pools[key].sum += qty;
                });

                if (unverified) {
                    return stop(
                        'Available stock could not be verified for one or more rows. Wait for the stock check to finish, or re-select the product.'
                    );
                }

                var blocked = false;
                $.each(pools, function(k, p) {
                    if (p.available !== undefined && p.sum > p.available) blocked = true;
                });
                if (blocked) {
                    return stop(
                        'One or more rows exceed available stock. Please reduce qty or remove the row before submitting.'
                    );
                }
                if (overRequested) {
                    return stop(
                        'One or more rows exceed the remaining requisition quantity. Please correct before submitting.'
                    );
                }

                $submitBtn.prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
            });

            // Back/forward cache: never come back to a stuck "Submitting..." button
            $(window).on('pageshow', function(e) {
                if (e.originalEvent && e.originalEvent.persisted) {
                    $submitBtn.prop('disabled', false).html(submitBtnHtml);
                }
            });

            /* ---------------- Init ---------------- */
            applyTransferType(currentType());
            if (!$('#productRows tr').length) {
                addRow();
            }
        });
    </script>
@endsection
