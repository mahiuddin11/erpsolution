@extends('backend.layouts.master')

@section('title')
    Project - {{ $title }}
@endsection

@section('styles')
    <style>
        .section-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #6c757d;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 6px;
            margin: 18px 0 14px;
        }

        .section-title:first-of-type {
            margin-top: 0;
        }

        #actualCosting {
            background-color: #f4f6f8;
            font-weight: 600;
        }

        #actualCostingHint {
            font-size: 12px;
            display: block;
            margin-top: 4px;
        }

        .card-body {
            overflow-x: hidden;
        }

        .form-row {
            margin-left: 0;
            margin-right: 0;
        }

        .form-row>[class*="col-"] {
            padding-left: 8px;
            padding-right: 8px;
        }

        .input-group {
            flex-wrap: nowrap;
        }

        .input-group .input-group-text {
            padding: .375rem .6rem;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: calc(1.5em + .75rem + 2px);
            display: flex;
            align-items: center;
        }

        @media (max-width: 1024px) {
            .card-tools {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-tools {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-top: 10px;
                width: 100%;
            }

            .card-tools .btn {
                flex: 1 1 auto;
                text-align: center;
            }

            .card-tools .btn-tool {
                flex: 0 0 auto;
            }

            .breadcrumb.float-sm-right {
                float: none !important;
                margin-top: 8px;
                flex-wrap: wrap;
            }

            .form-row {
                flex-direction: column;
            }

            .form-row>[class*="col-"] {
                width: 100%;
                max-width: 100%;
            }

            label {
                font-size: 14px;
                margin-bottom: 4px;
            }

            .form-control,
            .select2-container {
                font-size: 16px;
            }

            button[type="submit"] {
                width: 100%;
                padding: 12px;
                font-size: 15px;
                margin-top: 8px;
            }
        }

        @media (max-width: 480px) {
            .card-title {
                font-size: 16px;
            }

            .section-title {
                font-size: 12px;
            }

            .input-group-text {
                font-size: 13px;
                padding: .375rem .5rem;
            }
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Project</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('project.project.index'))
                            <li class="breadcrumb-item"><a href="{{ route('project.project.index') }}">Project List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Project</span></li>
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
                    <h3 class="card-title">Edit Project</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('project.project.create'))
                            <a class="btn btn-default" href="{{ route('project.project.create') }}">
                                <i class="fas fa-plus"></i> Add New
                            </a>
                        @endif
                        <a class="btn btn-tool btn-default" data-card-widget="collapse"><i class="fas fa-minus"></i></a>
                        <a class="btn btn-tool btn-default" data-card-widget="remove"><i class="fas fa-times"></i></a>
                    </div>
                </div>

                <div class="card-body">
                    <form class="needs-validation" method="POST"
                        action="{{ route('project.project.update', $editInfo->id) }}" novalidate>
                        @csrf

                        <span class="badge badge-success p-2 mb-3">
                            Project Code: <strong>{{ $editInfo->projectCode }}</strong>
                        </span>

                        {{-- Basic Info --}}
                        <div class="section-title"><i class="fa fa-info-circle"></i> Basic Information</div>
                        <div class="form-row">
                            <div class="col-lg-4 col-md-6 col-12 mb-3">
                                <label for="project_name">Project Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="project_name"
                                    placeholder="Project Name" value="{{ old('name', $editInfo->name) }}" required>
                                @error('name')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-lg-4 col-md-6 col-12 mb-3">
                                <label for="party_select">Company Name <span class="text-danger">*</span></label>
                                @php
                                    $selected = '';
                                    if ($editInfo->customer_id) {
                                        $selected = 'customer_' . $editInfo->customer_id;
                                    } elseif ($editInfo->ledger_id) {
                                        $selected = 'ledger_' . $editInfo->ledger_id;
                                    }
                                @endphp
                                <select class="form-control select2" id="party_select">
                                    <option value="">--Select--</option>
                                    @foreach ($customer as $value)
                                        <option value="customer_{{ $value->id }}"
                                            {{ $selected == 'customer_' . $value->id ? 'selected' : '' }}>
                                            {{ $value->co_name }}
                                        </option>
                                    @endforeach
                                    @foreach ($ledgers as $ledger)
                                        <option value="ledger_{{ $ledger->id }}"
                                            {{ $selected == 'ledger_' . $ledger->id ? 'selected' : '' }}>
                                            {{ $ledger->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="customer_id" id="customer_id_real">
                                <input type="hidden" name="ledger_id" id="ledger_id_real">
                                @error('customer_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                                @error('ledger_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-lg-4 col-md-6 col-12 mb-3">
                                <label for="manager_id">Manager Name <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="manager_id" name="manager_id" required>
                                    <option selected disabled value="">--Select--</option>
                                    @foreach ($managers as $value)
                                        <option value="{{ $value->id }}"
                                            {{ $editInfo->manager_id == $value->id ? 'selected' : '' }}>
                                            {{ $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('manager_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Financial Details --}}
                        <div class="section-title"><i class="fa fa-coins"></i> Financial Details</div>
                        <div class="form-row">
                            <div class="col-lg-4 col-md-6 col-12 mb-3">
                                <label for="budget">Budget <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">TK.</span></div>
                                    <input type="number" step="0.01" min="0" name="budget" class="form-control"
                                        id="budget" placeholder="Project Total Budget"
                                        value="{{ old('budget', $editInfo->budget) }}" required>
                                </div>
                                @error('budget')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-lg-4 col-md-6 col-12 mb-3">
                                <label for="estimate_profit">Estimate Profit <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">TK.</span></div>
                                    <input type="number" step="0.01" name="estimate_profit" class="form-control"
                                        id="estimate_profit" placeholder="Estimate Profit"
                                        value="{{ old('estimate_profit', $editInfo->estimate_profit) }}" required>
                                </div>
                                @error('estimate_profit')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-lg-4 col-md-6 col-12 mb-3">
                                <label for="actualCosting">Actual Costing (Auto-calculated)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">TK.</span></div>
                                    <input type="number" step="0.01" name="actualCosting" class="form-control"
                                        id="actualCosting"
                                        value="{{ old('actualCosting', $editInfo->actualCosting ?? $editInfo->budget - $editInfo->estimate_profit) }}"
                                        readonly>
                                </div>
                                <span id="actualCostingHint" class="text-success">Budget − Estimate Profit থেকে অটো হিসাব
                                    হবে</span>
                                @error('actualCosting')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Timeline & Address --}}
                        <div class="section-title"><i class="fa fa-calendar-alt"></i> Timeline &amp; Address</div>
                        <div class="form-row">
                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <label>Start Date</label>
                                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                    <input type="text" name="start_date" data-toggle="datetimepicker"
                                        value="{{ old('start_date', $editInfo->start_date) }}"
                                        class="form-control datetimepicker-input" data-target="#reservationdate" />
                                    <div class="input-group-append" data-target="#reservationdate"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                @error('start_date')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <label>Complete Date</label>
                                <div class="input-group date" id="reservationdate1" data-target-input="nearest">
                                    <input type="text" name="end_date" data-toggle="datetimepicker"
                                        value="{{ old('end_date', $editInfo->end_date) }}"
                                        class="form-control datetimepicker-input" data-target="#reservationdate1" />
                                    <div class="input-group-append" data-target="#reservationdate1"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                @error('end_date')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-lg-6 col-12 mb-3">
                                <label for="address">Address <span class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control" id="address"
                                    placeholder="Address" value="{{ old('address', $editInfo->address) }}" required>
                                @error('address')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <button class="btn btn-info" type="submit"><i class="fa fa-save"></i>&nbsp;Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            // --- Select2 full width fix ---
            $('.select2').select2({
                width: '100%',
                dropdownAutoWidth: true
            });

            // --- Auto-calculate Actual Costing = Budget - Estimate Profit ---
            function calculateActualCosting() {
                var budget = parseFloat($('#budget').val()) || 0;
                var estimateProfit = parseFloat($('#estimate_profit').val()) || 0;
                var actualCosting = budget - estimateProfit;

                $('#actualCosting').val(actualCosting.toFixed(2));

                if (actualCosting < 0) {
                    $('#actualCostingHint')
                        .removeClass('text-success').addClass('text-danger')
                        .text('Warning: Estimated Profit Exceeds Budget!');
                } else {
                    $('#actualCostingHint')
                        .removeClass('text-danger').addClass('text-success')
                        .text('Auto calculated from Budget − Estimate Profit');
                }
            }
            $('#budget, #estimate_profit').on('input', calculateActualCosting);
            calculateActualCosting(); // load-e existing value diye recalc

            // --- Party select (customer/ledger) sync into hidden fields ---
            function setPartyValue(val) {
                $('#customer_id_real').val('');
                $('#ledger_id_real').val('');
                if (!val) return;
                if (val.startsWith('customer_')) {
                    $('#customer_id_real').val(val.replace('customer_', ''));
                }
                if (val.startsWith('ledger_')) {
                    $('#ledger_id_real').val(val.replace('ledger_', ''));
                }
            }
            $(document).on('change', '#party_select', function() {
                setPartyValue($(this).val());
            });
            setPartyValue($('#party_select').val()); // page load-e ekbar set

            // --- Bootstrap client-side validation ---
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
@endsection
