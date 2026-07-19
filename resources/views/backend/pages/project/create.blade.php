@extends('backend.layouts.master')

@section('title')
    Settings - {{ $title }}
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

        /* ---- Prevent horizontal scroll from Bootstrap's negative row margins ---- */
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

        /* ---- Keep input-group prefix/icon from wrapping to a new line ---- */
        .input-group {
            flex-wrap: nowrap;
        }

        .input-group .input-group-text {
            padding: .375rem .6rem;
        }

        /* ---- Select2 always full width, regardless of plugin's inline style ---- */
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: calc(1.5em + .75rem + 2px);
            display: flex;
            align-items: center;
        }

        /* ================= TABLET ================= */
        @media (max-width: 1024px) {
            .card-tools {
                flex-wrap: wrap;
                display: none
            }
        }

        /* ================= MOBILE ================= */
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-tools {
                /* display: flex; */
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

            /* 16px prevents iOS auto-zoom on focus */

            button[type="submit"] {
                width: 100%;
                padding: 12px;
                font-size: 15px;
                margin-top: 8px;
            }

            .badge.p-2 {
                display: block;
                text-align: center;
                font-size: 13px;
            }
        }

        /* ================= SMALL MOBILE ================= */
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

            textarea#address {
                min-height: 70px;
            }
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Settings</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('project.project.index'))
                            <li class="breadcrumb-item"><a href="{{ route('project.project.index') }}">Project List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Project</span></li>
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
                    <h3 class="card-title">Add New Project</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('project.project.index'))
                            <a class="btn btn-default" href="{{ route('project.project.index') }}">
                                <i class="fa fa-list"></i> Project List
                            </a>
                        @endif
                        <a class="btn btn-tool btn-default" data-card-widget="collapse"><i class="fas fa-minus"></i></a>
                        <a class="btn btn-tool btn-default" data-card-widget="remove"><i class="fas fa-times"></i></a>
                    </div>
                </div>

                <div class="card-body">
                    <form class="needs-validation" method="POST" action="{{ route('project.project.store') }}" novalidate>
                        @csrf

                        <span class="badge badge-success p-2 mb-3">
                            Project Code: <strong>{{ $projectCode }}</strong>
                        </span>
                        <input type="hidden" name="projectCode" value="{{ $projectCode }}">

                        {{-- Basic Info --}}
                        <div class="section-title"><i class="fa fa-info-circle"></i> Basic Information</div>
                        <div class="form-row">
                            <div class="col-lg-4 col-md-6 col-12 mb-3">
                                <label for="project_name">Project Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="project_name"
                                    placeholder="Project Name" value="{{ old('name') }}" required>
                                <div class="invalid-feedback">Project name is required.</div>
                                @error('name')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-lg-4 col-md-6 col-12 mb-3">
                                <label for="ledger_id">Company Name <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="ledger_id" id="ledger_id" required>
                                    <option selected disabled value="">--Select Company--</option>
                                    @foreach ($ledgers as $ledger)
                                        <option value="{{ $ledger->id }}">{{ $ledger->account_name }}</option>
                                    @endforeach
                                </select>
                                @error('ledger_id')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-lg-4 col-md-6 col-12 mb-3">
                                <label for="manager_id">Manager Name <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="manager_id" name="manager_id" required>
                                    <option selected disabled value="">--Select Manager--</option>
                                    @foreach ($managers as $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->branchCode . ' - ' . $value->name }}</option>
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
                                        id="budget" data-number-words="budgetWords" value="{{ old('budget') }}"
                                        required>
                                </div>

                                @error('budget')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                                <small id="budgetWords" class="text-muted d-block mt-1"></small>
                            </div>

                            <div class="col-lg-4 col-md-6 col-12 mb-3">
                                <label for="estimate_profit">Estimate Profit <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">TK.</span></div>
                                    <input type="number" step="0.01" min="0" name="estimate_profit"
                                        class="form-control" id="estimate_profit" data-number-words="estimateprofit"
                                        value="{{ old('estimate_profit') }}" required>
                                </div>

                                @error('estimate_profit')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                                <small id="estimateprofit" class="text-muted d-block mt-1"></small>
                            </div>

                            {{-- <div class="col-lg-4 col-md-6 col-12 mb-3">
                                <label for="estimate_profit">Estimate Profit <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">TK.</span></div>


                                    <input type="number" step="0.01" min="0" name="estimate_profit"
                                        class="form-control" id="estimate_profit" data-number-words="estimate_profit"
                                        value="{{ old('estimate_profit') }}" required>
                                </div>
                                @error('estimate_profit')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                                <small id="estimate_profit" class="text-muted d-block mt-1"></small>
                            </div> --}}

                            <div class="col-lg-4 col-md-6 col-12 mb-3">
                                <label for="actualCosting">Actual Costing Plan</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">TK.</span></div>
                                    <input type="number" step="0.01" name="actualCosting" class="form-control"
                                        id="actualCosting" ata-number-words="actualCosting"
                                        value="{{ old('actualCosting', 0) }}" readonly>
                                </div>
                                <small id="actualCosting" class="text-muted d-block mt-1"></small>
                                <span id="actualCostingHint" class="text-success">Auto calculation will be done from
                                    Budget − Estimate Profit</span>
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
                                        value="{{ old('start_date', date('Y-m-d')) }}"
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
                                        value="{{ old('end_date', date('Y-m-d')) }}"
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
                                <textarea rows="1" name="address" class="form-control" id="address" placeholder="Project Address" required>{{ old('address') }}</textarea>
                                @error('address')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <button class="btn btn-info" type="submit"><i class="fa fa-save"></i> &nbsp;Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('scripts')
    <script>
        $(function() {
            // --- Force Select2 to always take full container width (fixes mobile shrink/overflow) ---
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
                        .removeClass('text-success')
                        .addClass('text-danger')
                        .text('Warning: Estimated Profit Exceeds Budget!');
                } else {
                    $('#actualCostingHint')
                        .removeClass('text-danger')
                        .addClass('text-success')
                        .text('Auto calculated from Budget − Estimate Profit');
                }
            }

            $('#budget, #estimate_profit').on('input', calculateActualCosting);
            calculateActualCosting();

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

        document.addEventListener('DOMContentLoaded', function() {
            const budgetInput = document.getElementById('budget');
            const profitInput = document.getElementById('estimate_profit');
            const costingInput = document.getElementById('actualCosting');
            const costingWords = document.getElementById('costingWords');

            function calculateActualCosting() {
                const actual = (parseFloat(budgetInput.value) || 0) - (parseFloat(profitInput.value) || 0);
                costingInput.value = actual.toFixed(2);
                costingWords.textContent = actual > 0 ? numberToWords(actual) : '';
            }

            budgetInput.addEventListener('input', calculateActualCosting);
            profitInput.addEventListener('input', calculateActualCosting);
        });
    </script>
@endsection
