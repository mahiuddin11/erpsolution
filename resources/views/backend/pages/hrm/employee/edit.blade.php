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
                                            value="{{ old('name', $editInfo->name) }}" name="name" required>
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
                                            value="{{ old('email', $editInfo->email) }}" name="email">
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
                                            <option value="" disabled>Select</option>
                                            <option value="male">Male
                                            </option>
                                            <option value="female">
                                                Female</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Marital Status</label>
                                        <select name="marital_status"
                                            class="form-control @error('marital_status') is-invalid @enderror">
                                            <option value="married">
                                                Married</option>
                                            <option value="unmarried">
                                                Unmarried</option>
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
                                                <option value="{{ $bg }}">
                                                    {{ $bg }}</option>
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
                                        <select name="position_id"
                                            class="form-control select2 @error('position_id') is-invalid @enderror"
                                            required>
                                            <option value="" disabled>Select Position</option>
                                            @foreach ($positions as $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->name }}</option>
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
                                                <option value="{{ $value->id }}">
                                                    {{ $value->name }}</option>
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
                                            <option value="yes">
                                                Yes</option>
                                            <option value="no">
                                                No</option>
                                        </select>
                                        @error('over_time_is')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control @error('status') is-invalid @enderror"
                                            required>
                                            <option value="present">
                                                Present</option>
                                            <option value="left">
                                                Left</option>
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

            // ---------- Simple client-side validation feedback ----------
            var form = document.getElementById('employeeEditForm');
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
@endsection
