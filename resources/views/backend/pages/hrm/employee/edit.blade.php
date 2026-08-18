@extends('backend.layouts.master')

@section('title')
    Hrm - {{ $title }}
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Hrm </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('hrm.employee.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.employee.index') }}">Employee List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Employee</span></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <style>
        /* ---------- Section headers ---------- */
        .ecf-section {
            border: 1px solid #e3e6ea;
            border-radius: .5rem;
            margin-bottom: 1.25rem;
            overflow: hidden;
        }

        .ecf-section .card-header {
            background: #f8f9fb;
            border-bottom: 1px solid #e3e6ea;
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .85rem 1.1rem;
        }

        .ecf-section .card-header .ecf-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #eef2ff;
            color: #3b5bdb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            font-size: 13px;
        }

        .ecf-section .card-header h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .ecf-section .card-header small {
            display: block;
            color: #8a92a6;
            font-size: .78rem;
        }

        .ecf-section .card-body {
            padding: 1.1rem;
        }

        /* ---------- Form fields ---------- */
        .ecf-field {
            margin-bottom: 1rem;
        }

        .ecf-field label {
            font-weight: 500;
            font-size: .86rem;
            margin-bottom: .3rem;
            color: #384158;
        }

        .ecf-field .text-danger {
            font-weight: 600;
        }

        .ecf-field .form-text {
            font-size: .76rem;
            color: #9aa1b1;
        }

        .form-control:focus,
        .custom-file-input:focus~.custom-file-label,
        .select2-container--default.select2-container--focus .select2-selection {
            border-color: #3b5bdb !important;
            box-shadow: 0 0 0 .15rem rgba(59, 91, 219, .18) !important;
        }

        .select2-container {
            width: 100% !important;
        }

        /* ---------- Select2 manual validation styling ---------- */
        .select2-container .select2-selection.is-invalid-select2 {
            border: 1px solid #dc3545 !important;
        }

        .select2-error-msg {
            font-size: .82rem;
            color: #dc3545;
            margin-top: .3rem;
        }

        /* ---------- File upload with preview ---------- */
        .ecf-upload {
            border: 1px dashed #c6cbd8;
            border-radius: .4rem;
            padding: .6rem .75rem;
            background: #fbfbfd;
        }

        .ecf-upload-preview {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-top: .55rem;
        }

        .ecf-upload-preview img {
            width: 46px;
            height: 46px;
            object-fit: cover;
            border-radius: .35rem;
            border: 1px solid #dfe2ea;
            background: #fff;
        }

        .ecf-upload-preview span {
            font-size: .78rem;
            color: #5c6579;
            word-break: break-all;
        }

        .ecf-upload-current {
            font-size: .74rem;
            color: #6b7280;
            margin-top: .4rem;
        }

        .ecf-upload-current i {
            color: #2f9e44;
        }

        /* ---------- Sticky save bar ---------- */
        .ecf-action-bar {
            position: sticky;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid #e3e6ea;
            padding: .85rem 1rem;
            margin: 1.25rem -1.25rem -1.25rem -1.25rem;
            display: flex;
            justify-content: flex-end;
            gap: .6rem;
            z-index: 5;
        }

        @media (max-width: 575.98px) {
            .ecf-action-bar {
                flex-direction: column-reverse;
            }

            .ecf-action-bar .btn {
                width: 100%;
            }

            .ecf-section .card-header {
                align-items: flex-start;
            }
        }

        /* Keyboard accessibility */
        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible {
            outline: 2px solid #3b5bdb;
            outline-offset: 1px;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Employee Edit</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('hrm.employee.create'))
                            <a class="btn btn-default" href="{{ route('hrm.employee.create') }}"><i class="fas fa-plus"></i>
                                <span class="d-none d-sm-inline">Add New</span></a>
                        @endif
                        @if (helper::roleAccess('hrm.employee.index'))
                            <a class="btn btn-default" href="{{ route('hrm.employee.index') }}"><i class="fa fa-list"></i>
                                <span class="d-none d-sm-inline">Employee List</span></a>
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
                            <strong><i class="fa fa-exclamation-triangle"></i> There are some errors in the form —</strong>
                            Please correct the highlighted fields below and update again.
                        </div>
                    @endif

                    <div class="alert alert-light border d-flex align-items-center" style="font-size:.85rem;">
                        <i class="fa fa-info-circle text-primary mr-2"></i>
                        <span> Red <span class="text-danger">*</span> Marked fields are required. If you don't want to
                            change the images/files, leave them blank — the previous ones will remain.</span>
                    </div>

                    <form class="needs-validation" id="employeeEditForm" method="POST"
                        action="{{ route('hrm.employee.update', $editInfo->id) }}" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')


                        {{-- ================= PERSONAL INFO ================= --}}
                        <div class="card ecf-section">
                            <div class="card-header">
                                <span class="ecf-icon"><i class="fa fa-user"></i></span>
                                <div>
                                    <h4>Personal Details</h4>
                                    <small>Name, contact and identification information</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $editInfo->name) }}" name="name" id="emp_name"
                                            required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Attendance Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('am_name') is-invalid @enderror"
                                            value="{{ old('am_name', $editInfo->am_name) }}" name="am_name" required>
                                        <small class="form-text">Use the same name as configured in the ZKTeco
                                            device.</small>
                                        @error('am_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Employee ID <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('id_card') is-invalid @enderror"
                                            value="{{ old('id_card', $editInfo->id_card) }}" name="id_card" required>
                                        @error('id_card')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $editInfo->email) }}" name="email" id="emp_email">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Personal Number <span class="text-danger">*</span></label>
                                        <input type="tel" pattern="[0-9]{10,14}" maxlength="14"
                                            class="form-control @error('personal_phone') is-invalid @enderror"
                                            value="{{ old('personal_phone', $editInfo->personal_phone) }}"
                                            name="personal_phone" required>
                                        @error('personal_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Office Number</label>
                                        <input type="tel" maxlength="14"
                                            class="form-control @error('office_phone') is-invalid @enderror"
                                            value="{{ old('office_phone', $editInfo->office_phone) }}" name="office_phone">
                                        @error('office_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-control @error('gender') is-invalid @enderror"
                                            required>

                                            <option value="male"
                                                {{ old('gender', $editInfo->gender) == 'male' ? 'selected' : '' }}>
                                                Male
                                            </option>

                                            <option value="female"
                                                {{ old('gender', $editInfo->gender) == 'female' ? 'selected' : '' }}>
                                                Female
                                            </option>

                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Marital Status</label>
                                        <select name="marital_status"
                                            class="form-control @error('marital_status') is-invalid @enderror">

                                            <option value="married"
                                                {{ old('marital_status', $editInfo->marital_status) == 'married' ? 'selected' : '' }}>
                                                Married
                                            </option>

                                            <option value="unmarried"
                                                {{ old('marital_status', $editInfo->marital_status) == 'unmarried' ? 'selected' : '' }}>
                                                Unmarried
                                            </option>

                                        </select>
                                        @error('marital_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Date Of Birth</label>
                                        <input type="date" class="form-control @error('dob') is-invalid @enderror"
                                            value="{{ old('dob', $editInfo->dob) }}" onfocus="this.showPicker()"
                                            name="dob">
                                        @error('dob')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>NID</label>
                                        <input type="text" class="form-control @error('nid') is-invalid @enderror"
                                            value="{{ old('nid', $editInfo->nid) }}" name="nid">
                                        @error('nid')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Blood Group</label>
                                        <select name="blood_group"
                                            class="form-control @error('blood_group') is-invalid @enderror">
                                            <option value="">Select</option>
                                            @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                                <option value="{{ $bg }}"
                                                    {{ old('blood_group', $editInfo->blood_group) == $bg ? 'selected' : '' }}>
                                                    {{ $bg }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('blood_group')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Reference</label>
                                        <input type="text"
                                            class="form-control @error('reference') is-invalid @enderror"
                                            value="{{ old('reference', $editInfo->reference) }}" name="reference">
                                        @error('reference')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Guardian Number</label>
                                        <input type="tel" maxlength="14"
                                            class="form-control @error('guardian_number') is-invalid @enderror"
                                            value="{{ old('guardian_number', $editInfo->guardian_number) }}"
                                            name="guardian_number">
                                        @error('guardian_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================= ADDRESS & EXPERIENCE ================= --}}
                        <div class="card ecf-section">
                            <div class="card-header">
                                <span class="ecf-icon"><i class="fa fa-map-marker-alt"></i></span>
                                <div>
                                    <h4>Address & Experience</h4>
                                    <small>Optional information</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-4 ecf-field">
                                        <label>Present Address</label>
                                        <textarea name="present_address" rows="3" class="form-control @error('present_address') is-invalid @enderror">{{ old('present_address', $editInfo->present_address) }}</textarea>
                                        @error('present_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-lg-4 ecf-field">
                                        <label>Permanent Address</label>
                                        <textarea name="permanent_address" rows="3"
                                            class="form-control @error('permanent_address') is-invalid @enderror">{{ old('permanent_address', $editInfo->permanent_address) }}</textarea>
                                        @error('permanent_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-lg-4 ecf-field">
                                        <label>Experience</label>
                                        <textarea name="experience" rows="3" class="form-control @error('experience') is-invalid @enderror">{{ old('experience', $editInfo->experience) }}</textarea>
                                        @error('experience')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================= DOCUMENTS / UPLOADS ================= --}}
                        <div class="card ecf-section">
                            <div class="card-header">
                                <span class="ecf-icon"><i class="fa fa-file-upload"></i></span>
                                <div>
                                    <h4>Photo & Documents</h4>
                                    <small>If you don't give a new file, the old one will remain</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Profile Photo</label>
                                        <div class="ecf-upload">
                                            <div class="custom-file">
                                                <input type="file" accept="image/*"
                                                    class="custom-file-input ecf-file-input @error('image') is-invalid @enderror"
                                                    name="image" id="image">
                                                <label class="custom-file-label" for="image">Select New File</label>
                                            </div>
                                            @if ($editInfo->image)
                                                <div class="ecf-upload-current">
                                                    <i class="fa fa-check-circle"></i> Current:
                                                    <img src="{{ asset('storage/photo/' . $editInfo->image) }}"
                                                        alt="current" width="30" height="30"
                                                        style="object-fit:cover;border-radius:4px;vertical-align:middle;margin-left:4px;">
                                                </div>
                                            @endif
                                            <div class="ecf-upload-preview" id="preview-image" style="display:none;">
                                                <img src="" alt="preview">
                                                <span></span>
                                            </div>
                                        </div>
                                        @error('image')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Signature <span class="text-muted">(PNG only)</span></label>
                                        <div class="ecf-upload">
                                            <div class="custom-file">
                                                <input type="file" accept="image/png"
                                                    class="custom-file-input ecf-file-input @error('emp_signature') is-invalid @enderror"
                                                    name="emp_signature" id="emp_signature">
                                                <label class="custom-file-label" for="emp_signature">Select New
                                                    File</label>
                                            </div>
                                            @if ($editInfo->emp_signature)
                                                <div class="ecf-upload-current">
                                                    <i class="fa fa-check-circle"></i> Current:
                                                    <img src="{{ asset('storage/photo/' . $editInfo->emp_signature) }}"
                                                        alt="current" width="30" height="30"
                                                        style="object-fit:cover;border-radius:4px;vertical-align:middle;margin-left:4px;">
                                                </div>
                                            @endif
                                            <div class="ecf-upload-preview" id="preview-emp_signature"
                                                style="display:none;">
                                                <img src="" alt="preview">
                                                <span></span>
                                            </div>
                                        </div>
                                        @error('emp_signature')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Guardian NID Photo</label>
                                        <div class="ecf-upload">
                                            <div class="custom-file">
                                                <input type="file" accept="image/*"
                                                    class="custom-file-input ecf-file-input @error('guardian_nid') is-invalid @enderror"
                                                    name="guardian_nid" id="guardian_nid">
                                                <label class="custom-file-label" for="guardian_nid">Select New
                                                    File</label>
                                            </div>
                                            @if ($editInfo->guardian_nid)
                                                <div class="ecf-upload-current">
                                                    <i class="fa fa-check-circle"></i> Current:
                                                    <img src="{{ asset('storage/photo/' . $editInfo->guardian_nid) }}"
                                                        alt="current" width="30" height="30"
                                                        style="object-fit:cover;border-radius:4px;vertical-align:middle;margin-left:4px;">
                                                </div>
                                            @endif
                                            <div class="ecf-upload-preview" id="preview-guardian_nid"
                                                style="display:none;">
                                                <img src="" alt="preview">
                                                <span></span>
                                            </div>
                                        </div>
                                        @error('guardian_nid')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================= QUALIFICATION ================= --}}
                        <div class="card ecf-section">
                            <div class="card-header">
                                <span class="ecf-icon"><i class="fa fa-graduation-cap"></i></span>
                                <div>
                                    <h4>Qualification Information</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Achieved Degree</label>
                                        <input type="text"
                                            class="form-control @error('achieved_degree') is-invalid @enderror"
                                            value="{{ old('achieved_degree', $editInfo->achieved_degree) }}"
                                            name="achieved_degree">
                                        @error('achieved_degree')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Institution</label>
                                        <input type="text"
                                            class="form-control @error('institution') is-invalid @enderror"
                                            value="{{ old('institution', $editInfo->institution) }}" name="institution">
                                        @error('institution')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Passing Year</label>
                                        <input type="number" min="1970" max="{{ date('Y') }}"
                                            class="form-control @error('passing_year') is-invalid @enderror"
                                            value="{{ old('passing_year', $editInfo->passing_year) }}"
                                            name="passing_year">
                                        @error('passing_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================= OFFICE INFORMATION ================= --}}
                        <div class="card ecf-section">
                            <div class="card-header">
                                <span class="ecf-icon"><i class="fa fa-briefcase"></i></span>
                                <div>
                                    <h4>Office Information</h4>
                                    <small>Position, branch, salary and attendance policy</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Joining Date</label>
                                        <input type="date"
                                            class="form-control @error('join_date') is-invalid @enderror"
                                            value="{{ old('join_date', $editInfo->join_date) }}"
                                            onfocus="this.showPicker()" name="join_date">
                                        @error('join_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>In Time</label>
                                        <input type="time"
                                            class="form-control @error('last_in_time') is-invalid @enderror"
                                            value="{{ old('last_in_time', $editInfo->last_in_time) }}"
                                            name="last_in_time">
                                        @error('last_in_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Department <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('department') is-invalid @enderror"
                                            name="department" value="{{ old('department', $editInfo->department) }}"
                                            required>
                                        @error('department')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Position <span class="text-danger">*</span></label>
                                        <select name="position_id" id="position_id"
                                            class="form-control select2 @error('position_id') is-invalid @enderror">

                                            <option value="" disabled>Select Position</option>

                                            @foreach ($positions as $value)
                                                <option value="{{ $value->id }}"
                                                    {{ old('position_id', $editInfo->position_id) == $value->id ? 'selected' : '' }}>
                                                    {{ $value->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('position_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Branch <span class="text-danger">*</span></label>
                                        <select name="branch_id"
                                            class="form-control select2 @error('branch_id') is-invalid @enderror">
                                            <option value="0">No
                                                Applicable</option>
                                            @foreach ($branchs as $value)
                                                <option value="{{ $value->id }}"
                                                    {{ old('branch_id', $editInfo->branch_id) == $value->id ? 'selected' : '' }}>
                                                    {{ $value->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @php
                                        $areasjson = json_decode($editInfo->area) ?? [];
                                    @endphp
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Area <span class="text-danger">*</span></label>
                                        <select name="area[]" id="area_select"
                                            class="form-control select2 @error('area') is-invalid @enderror" multiple>
                                            @foreach ($areasjson as $savedCode)
                                                <option value="{{ $savedCode }}" selected>{{ $savedCode }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text" id="area_loading_hint">
                                            <i class="fa fa-spinner fa-spin"></i> Loading area names...
                                        </small>
                                        @error('area')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Salary <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">৳</span>
                                            </div>
                                            <input type="number" min="0"
                                                class="form-control @error('salary') is-invalid @enderror"
                                                value="{{ old('salary', $editInfo->salary) }}" name="salary" required>
                                            @error('salary')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Overtime</label>
                                        <select name="over_time_is"
                                            class="form-control @error('over_time_is') is-invalid @enderror">
                                            <option value="yes"
                                                {{ old('over_time_is', $editInfo->over_time_is) == 'yes' ? 'selected' : '' }}>
                                                Yes
                                            </option>

                                            <option value="no"
                                                {{ old('over_time_is', $editInfo->over_time_is) == 'no' ? 'selected' : '' }}>
                                                No
                                            </option>
                                        </select>
                                        @error('over_time_is')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control @error('status') is-invalid @enderror"
                                            required>
                                            <option value="present"
                                                {{ old('status', $editInfo->status) == 'present' ? 'selected' : '' }}>
                                                Present
                                            </option>

                                            <option value="left"
                                                {{ old('status', $editInfo->status) == 'left' ? 'selected' : '' }}>
                                                Left
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label for="auto_checkout">
                                            Auto Check Out Allow <span class="text-danger">*</span>
                                            <i class="fa fa-question-circle text-muted" data-toggle="tooltip"
                                                data-placement="top"
                                                title="Yes: The system will shut down automatically if the employee forgets to check out. Applicable to general Day Shift employees. || No: Auto Check Out will be off — For Night Guard, Driver, Operator etc., they have to check out manually.">
                                            </i>
                                        </label>
                                        <select name="auto_checkout" id="auto_checkout"
                                            class="form-control @error('auto_checkout') is-invalid @enderror" required>
                                            <option value="1"
                                                {{ old('auto_checkout', $editInfo->auto_checkout) == 1 ? 'selected' : '' }}>
                                                Yes</option>
                                            <option value="0"
                                                {{ old('auto_checkout', $editInfo->auto_checkout) == 0 ? 'selected' : '' }}>
                                                No</option>
                                        </select>
                                        @error('auto_checkout')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================= USER ACCESS ================= --}}
                        @php

                            $linkedUser = $editInfo->user_id ? $editInfo->user : null;
                            $hasUser = (bool) $linkedUser;
                            $userIsActive = $hasUser && $linkedUser->status === 'Active';
                        @endphp


                        <div class="card ecf-section">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                <div class="d-flex align-items-center" style="gap:.6rem;">
                                    <span class="ecf-icon"><i class="fa fa-user-lock"></i></span>
                                    <div>
                                        <h4>User Access</h4>
                                        <small>
                                            @if ($hasUser)
                                                This employee already has a login account. Update the fields below or
                                                use the lock button to control login access.
                                            @else
                                                Enable to create a login account for this employee.
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center" style="gap:1rem;">
                                    {{-- Lock/Unlock icon: locked = user login Inactive, unlocked = user login Active --}}
                                    <button type="button" id="accountLockBtn" class="btn btn-sm btn-outline-secondary"
                                        style="{{ $hasUser ? '' : 'display:none;' }}"
                                        title="Click to toggle login access">
                                        <i class="fa fa-lock" id="accountLockIcon"></i>
                                        <span id="accountLockLabel">Locked (Inactive)</span>
                                    </button>
                                    <input type="hidden" name="account_lock" id="account_lock"
                                        value="{{ old('account_lock', $userIsActive ? 1 : 0) }}">

                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="create_user_toggle"
                                            name="create_user" value="1"
                                            {{ old('create_user', $hasUser ? '1' : null) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="create_user_toggle">
                                            {{ $hasUser ? 'Manage User Account' : 'Create User Account' }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body" id="userAccessBody"
                                style="{{ old('create_user', $hasUser ? '1' : null) ? '' : 'display:none;' }}">
                                <div class="row">
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Login Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('user_name') is-invalid @enderror"
                                            value="{{ old('user_name', $linkedUser->name ?? '') }}" name="user_name"
                                            id="user_name" placeholder="Auto-fill from Name field">
                                        @error('user_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Login Email <span class="text-danger">*</span></label>
                                        <input type="email"
                                            class="form-control @error('user_email') is-invalid @enderror"
                                            value="{{ old('user_email', $linkedUser->email ?? '') }}" name="user_email"
                                            id="user_email" placeholder="Auto-fill from Email field">
                                        @error('user_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Role <span class="text-danger">*</span></label>
                                        <select name="role_name" id="role_name"
                                            class="form-control select2 @error('role_name') is-invalid @enderror">
                                            <option value=""
                                                {{ old('role_name', $linkedUser->role_id ?? '') ? '' : 'selected disabled' }}>
                                                Select Role</option>
                                            @foreach ($userRoll as $value)
                                                <option value="{{ $value->id }}"
                                                    {{ old('role_name', $linkedUser->role_id ?? '') == $value->id ? 'selected' : '' }}>
                                                    {{ $value->role_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('role_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Type <span class="text-danger">*</span></label>
                                        <select name="type" id="user_type"
                                            class="form-control @error('type') is-invalid @enderror">
                                            <option value="" disabled>Select Type</option>
                                            <option value="Admin"
                                                {{ old('type', $linkedUser->type ?? 'Employee') == 'Admin' ? 'selected' : '' }}>
                                                Admin</option>
                                            <option value="Employee"
                                                {{ old('type', $linkedUser->type ?? 'Employee') == 'Employee' ? 'selected' : '' }}>
                                                Employee</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Password @if (!$hasUser)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            id="user_password"
                                            placeholder="{{ $hasUser ? 'Leave blank to keep current password' : 'Password' }}">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if ($hasUser)
                                            <small class="form-text">Leave blank if you don't want to change the
                                                password.</small>
                                        @endif
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Confirm Password @if (!$hasUser)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="password" class="form-control" name="password_confirmation"
                                            id="user_password_confirmation" placeholder="Confirm Password">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ecf-action-bar">
                            @if (helper::roleAccess('hrm.employee.index'))
                                <a href="{{ route('hrm.employee.index') }}" class="btn btn-outline-secondary">
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

    <script>
        var savedAreaCodes = @json($areasjson);

        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            // select2 init — saved area code
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // ---------- Bootstrap custom-file label + preview ----------
            $('.ecf-file-input').on('change', function() {
                var fileInput = this;
                var fileName = fileInput.files.length ? fileInput.files[0].name : 'Choose new file';
                $(this).next('.custom-file-label').text(fileName);

                var $preview = $('#preview-' + fileInput.id);
                if (fileInput.files && fileInput.files[0] && fileInput.files[0].type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $preview.css('display', 'flex');
                        $preview.find('img').attr('src', e.target.result);
                        $preview.find('span').text('New: ' + fileName);
                    };
                    reader.readAsDataURL(fileInput.files[0]);
                }
            });

            // ---------- Select2 manual validation helper ----------
            function validateSelect2Field($select, label) {
                var val = $select.val();
                var isEmpty = !val || (Array.isArray(val) && val.length === 0);
                var $container = $select.next('.select2-container');

                $container.next('.select2-error-msg').remove();

                if (isEmpty) {
                    $container.find('.select2-selection').addClass('is-invalid-select2');
                    $container.after('<div class="invalid-feedback d-block select2-error-msg">' + label +
                        ' is required.</div>');
                    return false;
                } else {
                    $container.find('.select2-selection').removeClass('is-invalid-select2');
                    return true;
                }
            }

            $(document).on('select2:select select2:unselect change', '#position_id, #area_select, #role_name',
                function() {
                    var label = $(this).attr('id') === 'position_id' ? 'Position' :
                        ($(this).attr('id') === 'area_select' ? 'Area' : 'Role');
                    validateSelect2Field($(this), label);
                });

            // ---------- Full form validation on submit (button never gets stuck disabled) ----------
            var form = document.getElementById('employeeEditForm');
            var $submitBtn = $('#ecfSubmitBtn');
            var submitBtnOriginalHtml = $submitBtn.html();

            function resetSubmitButton() {
                $submitBtn.prop('disabled', false).html(submitBtnOriginalHtml);
            }

            window.addEventListener('pageshow', function() {
                resetSubmitButton();
            });

            form.addEventListener('submit', function(event) {
                resetSubmitButton();

                var valid = false;
                var positionOk = false;
                var areaOk = false;
                var roleOk = false;

                try {
                    valid = form.checkValidity();
                    positionOk = validateSelect2Field($('#position_id'), 'Position');
                    areaOk = validateSelect2Field($('#area_select'), 'Area');

                    var userToggleOn = $('#create_user_toggle').is(':checked');
                    roleOk = userToggleOn ? validateSelect2Field($('#role_name'), 'Role') : true;
                } catch (err) {
                    console.error('Validation error:', err);
                    valid = false;
                }

                var isFormValid = valid && positionOk && areaOk && roleOk;

                if (!isFormValid) {
                    event.preventDefault();
                    event.stopPropagation();
                    resetSubmitButton();

                    var $firstInvalid = $(form).find(':invalid').first();
                    var $firstSelect2Error = $('.select2-error-msg').first();
                    var $scrollTarget = $firstInvalid.length ? $firstInvalid : $firstSelect2Error;

                    if ($scrollTarget.length) {
                        $('html, body').animate({
                            scrollTop: $scrollTarget.offset().top - 120
                        }, 300);
                    }
                    if ($firstInvalid.length) {
                        $firstInvalid.focus();
                    }
                } else {
                    $submitBtn.prop('disabled', true)
                        .html('<i class="fa fa-spinner fa-spin"></i> Updating...');
                }
                form.classList.add('was-validated');
            });

            loadZktecoAreas();
        });

        function loadZktecoAreas() {
            var $areaSelect = $('#area_select');
            var $hint = $('#area_loading_hint');

            $.ajax({
                url: "{{ route('hrm.employee.get-zkteco-areas') }}",
                method: "GET",
                timeout: 60000,
                success: function(response) {
                    var areas = response.area || [];

                    if (areas.length === 0) {
                        $hint.html(
                            '<i class="fa fa-info-circle"></i> Unable to load the latest area list. Previously saved areas remain unchanged.'
                        );
                        return;
                    }

                    // option value area_code দিয়ে বসানো হচ্ছে — কারণ database এটাই store করে
                    $areaSelect.empty();
                    $.each(areas, function(index, item) {
                        $areaSelect.append(new Option(item.area_name, item.area_code));
                    });

                    // savedAreaCodes অনুযায়ী select2-এর নিজস্ব API দিয়ে selection বসানো (bulletproof way)
                    var savedCodesAsString = savedAreaCodes.map(String);
                    $areaSelect.val(savedCodesAsString).trigger('change');

                    $hint.remove();
                },
                error: function() {
                    $hint.html(
                        '<i class="fa fa-exclamation-triangle text-danger"></i> Unable to load the latest areas, but previously selected areas remain unchanged.'
                    );
                }
            });
        }
    </script>

    <script>
        // ---------- User Access toggle + auto-fill ----------
        var $userToggle = $('#create_user_toggle');
        var $userBody = $('#userAccessBody');
        var hasExistingUser = {{ $hasUser ? 'true' : 'false' }};
        var userEdited = {
            user_name: hasExistingUser, // don't overwrite existing login name/email automatically
            user_email: hasExistingUser
        };

        function toggleUserFieldsRequired(state) {
            $userBody.find('#user_name, #user_email, #user_type, #user_password, #user_password_confirmation')
                .prop('required', state && !hasExistingUser ? true : false);
            // Login Name / Email / Type stay required whenever the section is open,
            // password stays optional on edit (blank = keep current password).
            $userBody.find('#user_name, #user_email, #user_type').prop('required', state);
            $userBody.find('#user_password, #user_password_confirmation').prop('required', state && !hasExistingUser);
        }

        function syncUserFieldsFromPersonalInfo() {
            if (!userEdited.user_name) {
                $('#user_name').val($('#emp_name').val());
            }
            if (!userEdited.user_email) {
                $('#user_email').val($('#emp_email').val());
            }
        }

        $('#user_name').on('input', function() {
            userEdited.user_name = true;
        });
        $('#user_email').on('input', function() {
            userEdited.user_email = true;
        });

        $('#emp_name').on('input', function() {
            if ($userToggle.is(':checked') && !userEdited.user_name) {
                $('#user_name').val($(this).val());
            }
        });
        $('#emp_email').on('input', function() {
            if ($userToggle.is(':checked') && !userEdited.user_email) {
                $('#user_email').val($(this).val());
            }
        });

        if ($userToggle.is(':checked')) {
            toggleUserFieldsRequired(true);
        }

        // ---------- Lock / Unlock login access ----------

        var $lockBtn = $('#accountLockBtn');
        var $lockIcon = $('#accountLockIcon');
        var $lockLabel = $('#accountLockLabel');
        var $lockInput = $('#account_lock');

        function renderLockState(isUnlocked) {
            $lockInput.val(isUnlocked ? 1 : 0);
            if (isUnlocked) {
                $lockIcon.removeClass('fa-lock text-danger').addClass('fa-unlock text-success');
                $lockLabel.text('Unlocked (Active)');
                $lockBtn.removeClass('btn-outline-secondary').addClass('btn-outline-success');
            } else {
                $lockIcon.removeClass('fa-unlock text-success').addClass('fa-lock text-danger');
                $lockLabel.text('Locked (Inactive)');
                $lockBtn.removeClass('btn-outline-success').addClass('btn-outline-secondary');
            }
        }

        // initialize from the existing user's current status (rendered server-side into the hidden input)
        renderLockState($lockInput.val() == '1');

        $lockBtn.on('click', function() {
            var currentlyUnlocked = $lockInput.val() == '1';
            renderLockState(!currentlyUnlocked);
        });

        // ---------- Toggle handler (create_user checkbox) ----------
        $userToggle.on('change', function() {
            if (this.checked) {
                $userBody.slideDown(150);
                toggleUserFieldsRequired(true);
                syncUserFieldsFromPersonalInfo();
                $lockBtn.show();
            } else {
                $userBody.slideUp(150);
                toggleUserFieldsRequired(false);
                $lockBtn.hide();
                if (!hasExistingUser) {
                    renderLockState(false); // reset only for brand-new (not-yet-created) accounts
                }
                $('#role_name').next('.select2-container').find('.select2-selection')
                    .removeClass('is-invalid-select2');
                $('#role_name').next('.select2-container').next('.select2-error-msg').remove();
            }
        });

        if ($userToggle.is(':checked')) {
            $lockBtn.show();
        }
    </script>
@endsection
