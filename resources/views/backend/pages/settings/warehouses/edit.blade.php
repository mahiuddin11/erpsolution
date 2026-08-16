@extends('backend.layouts.master')

@section('title')
    Settings - {{ $title }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard-style.css') }}">
@endpush

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Settings </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('settings.warehouses.index'))
                            <li class="breadcrumb-item"><a href="{{ route('settings.warehouses.index') }}">Warehouses
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Warehouses</span></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Edit Warehouses</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('settings.warehouses.index'))
                            <a class="btn btn-default" href="{{ route('settings.warehouses.index') }}"><i
                                    class="fa fa-list"></i> <span class="d-none d-sm-inline">Warehouses List</span></a>
                        @endif
                        @if (helper::roleAccess('settings.warehouses.create'))
                            <a class="btn btn-default" href="{{ route('settings.warehouses.create') }}"><i
                                    class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add New</span></a>
                        @endif
                        <span id="buttons"></span>
                        <a class="btn btn-tool btn-default" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </a>
                        <a class="btn btn-tool btn-default" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong><i class="fa fa-exclamation-triangle"></i> There is something wrong with the form
                                —</strong>
                            Correct the highlighted fields below and save again.
                        </div>
                    @endif

                    <div class="alert alert-light border d-flex align-items-center" style="font-size:.85rem;">
                        <i class="fa fa-info-circle text-primary mr-2"></i>
                        <span> Red <span class="text-danger">*</span> Marked fields are required.</span>
                    </div>

                    <form class="needs-validation" id="warehouseEditForm" method="POST"
                        action="{{ route('settings.warehouses.update', $editInfo->id) }}" novalidate>
                        @csrf

                        {{-- ================= BASIC INFO ================= --}}
                        <div class="card ecf-section">
                            <div class="card-header">
                                <span class="ecf-icon"><i class="fa fa-warehouse"></i></span>
                                <div>
                                    <h4>Basic Information</h4>
                                    <small>Warehouse name and branch</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Branch Name <span class="text-danger">*</span></label>
                                        <select name="parent_id"
                                            class="form-control select2 @error('parent_id') is-invalid @enderror"
                                            data-placeholder="Select Branch Name">

                                            Not Applicable</option>
                                            @foreach ($parents as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('parent_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Warehouses Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            name="name" placeholder="Example: Dhaka Central Warehouse"
                                            value="{{ old('name', $editInfo->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================= CONTACT INFO ================= --}}
                        <div class="card ecf-section">
                            <div class="card-header">
                                <span class="ecf-icon"><i class="fa fa-address-book"></i></span>
                                <div>
                                    <h4>Contact Information</h4>
                                    <small>Email, phone and address</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>E-mail <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            name="email" placeholder="name@example.com"
                                            value="{{ old('email', $editInfo->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Phone <span class="text-danger">*</span></label>
                                        <input type="tel" pattern="[0-9]{10,14}" maxlength="14"
                                            class="form-control @error('phone') is-invalid @enderror" name="phone"
                                            placeholder="01XXXXXXXXX" value="{{ old('phone', $editInfo->phone) }}"
                                            required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-lg-4 ecf-field">
                                        <label>Address <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror"
                                            name="address" placeholder="Warehouse address"
                                            value="{{ old('address', $editInfo->address) }}" required>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ecf-action-bar">
                            @if (helper::roleAccess('settings.warehouses.index'))
                                <a href="{{ route('settings.warehouses.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                            @endif
                            <button class="btn btn-info" type="submit" id="ecfSubmitBtn">
                                <i class="fa fa-save"></i> &nbsp;Update
                            </button>
                        </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
        <!-- /.col-->
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // select2 init
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });


            var currentBranchId = "{{ optional($warehouse)->branch_id }}";
            if (currentBranchId) {
                $('select[name="parent_id"]').val(currentBranchId).trigger('change');
            }

            // ---------- Simple client-side validation feedback ----------
            var form = document.getElementById('warehouseEditForm');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    var $firstInvalid = $(form).find(':invalid').first();
                    if ($firstInvalid.length) {
                        $('html, body').animate({
                            scrollTop: $firstInvalid.offset().top - 120
                        }, 300);
                        $firstInvalid.focus();
                    }
                } else {
                    $('#ecfSubmitBtn').prop('disabled', true)
                        .html('<i class="fa fa-spinner fa-spin"></i> Updating...');
                }
                form.classList.add('was-validated');
            });
        });
    </script>
@endpush
