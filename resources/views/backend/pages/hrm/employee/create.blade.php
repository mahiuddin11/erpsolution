@extends('backend.layouts.master')

@section('title')
    Employee - {{ $title }}
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
                            <li class="breadcrumb-item"><a href="{{ route('hrm.employee.index') }}">employee
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New employee</span></li>
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
            display: none;
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
                    <h3 class="card-title">Add New Employee</h3>
                    <div class="card-tools">
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
                            <strong><i class="fa fa-exclamation-triangle"></i> There is something wrong with the form
                                —</strong>
                            Correct the highlighted fields below and save again.
                        </div>
                    @endif

                    <div class="alert alert-light border d-flex align-items-center" style="font-size:.85rem;">
                        <i class="fa fa-info-circle text-primary mr-2"></i>
                        <span> Red <span class="text-danger">*</span> Marked fields are required.</span>
                    </div>

                    <form class="needs-validation" id="employeeCreateForm" method="POST"
                        action="{{ route('hrm.employee.store') }}" enctype="multipart/form-data" novalidate>
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
                                            value="{{ old('name') }}" name="name" placeholder="Example: Rahim Uddin"
                                            required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Attendance Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('am_name') is-invalid @enderror"
                                            value="{{ old('am_name') }}" name="am_name"
                                            placeholder="Name used in the biometric device" required>
                                        <small class="form-text">Use the same name as configured in the ZKTeco
                                            device.</small>
                                        @error('am_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Employee ID <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('id_card') is-invalid @enderror"
                                            value="{{ old('id_card', $newEmId) }}" name="id_card"
                                            placeholder="Example: 1024" required>
                                        @error('id_card')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" name="email" placeholder="name@example.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Personal Number <span class="text-danger">*</span></label>
                                        <input type="tel" pattern="[0-9]{10,14}" maxlength="14"
                                            class="form-control @error('personal_phone') is-invalid @enderror"
                                            value="{{ old('personal_phone') }}" name="personal_phone"
                                            placeholder="01XXXXXXXXX" required>
                                        @error('personal_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Office Number</label>
                                        <input type="tel" maxlength="14"
                                            class="form-control @error('office_phone') is-invalid @enderror"
                                            value="{{ old('office_phone') }}" name="office_phone" placeholder="Optional">
                                        @error('office_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-control @error('gender') is-invalid @enderror"
                                            required>
                                            <option value="" selected disabled>Select</option>
                                            <option value="male" @selected(old('gender') == 'male')>Male</option>
                                            <option value="female" @selected(old('gender') == 'female')>Female</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Marital Status</label>
                                        <select name="marital_status"
                                            class="form-control @error('marital_status') is-invalid @enderror">
                                            <option value="married" @selected(old('marital_status') == 'married')>Married</option>
                                            <option value="unmarried" @selected(old('marital_status') == 'unmarried')>Unmarried</option>
                                        </select>
                                        @error('marital_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Date Of Birth</label>
                                        <input type="date" class="form-control @error('dob') is-invalid @enderror"
                                            value="{{ old('dob') }}" onfocus="this.showPicker()" name="dob">
                                        @error('dob')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>NID</label>
                                        <input type="text" class="form-control @error('nid') is-invalid @enderror"
                                            value="{{ old('nid') }}" name="nid" placeholder="National ID number">
                                        @error('nid')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Blood Group</label>
                                        <select name="blood_group"
                                            class="form-control @error('blood_group') is-invalid @enderror">
                                            <option value="" selected>Select</option>
                                            @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                                <option value="{{ $bg }}" @selected(old('blood_group') == $bg)>
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
                                            value="{{ old('reference') }}" name="reference"
                                            placeholder="Reference person">
                                        @error('reference')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Guardian Number</label>
                                        <input type="tel" maxlength="14"
                                            class="form-control @error('guardian_number') is-invalid @enderror"
                                            value="{{ old('guardian_number') }}" name="guardian_number">
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
                                    <small>Optional </small>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-4 ecf-field">
                                        <label>Present Address</label>
                                        <textarea name="present_address" rows="3" class="form-control @error('present_address') is-invalid @enderror"
                                            placeholder="Present address">{{ old('present_address') }}</textarea>
                                        @error('present_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-lg-4 ecf-field">
                                        <label>Permanent Address</label>
                                        <textarea name="permanent_address" rows="3"
                                            class="form-control @error('permanent_address') is-invalid @enderror" placeholder="Permanent address">{{ old('permanent_address') }}</textarea>
                                        @error('permanent_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-lg-4 ecf-field">
                                        <label>Experience</label>
                                        <textarea name="experience" rows="3" class="form-control @error('experience') is-invalid @enderror"
                                            placeholder="Brief work experience">{{ old('experience') }}</textarea>
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
                                    <small>Upload JPG/PNG files, maximum 2 MB</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Profile Photo <span class="text-danger">*</span></label>
                                        <div class="ecf-upload">
                                            <div class="custom-file">
                                                <input type="file" accept="image/*"
                                                    class="custom-file-input ecf-file-input @error('image') is-invalid @enderror"
                                                    name="image" id="image">
                                                <label class="custom-file-label" for="image">Choose file</label>
                                            </div>
                                            <div class="ecf-upload-preview" id="preview-image">
                                                <img src="" alt="preview">
                                                <span></span>
                                            </div>
                                        </div>
                                        @error('image')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Signature <span class="text-muted">(PNG only)</span> <span
                                                class="text-danger">*</span></label>
                                        <div class="ecf-upload">
                                            <div class="custom-file">
                                                <input type="file" accept="image/png"
                                                    class="custom-file-input ecf-file-input @error('emp_signature') is-invalid @enderror"
                                                    name="emp_signature" id="emp_signature">
                                                <label class="custom-file-label" for="emp_signature">Choose file</label>
                                            </div>
                                            <div class="ecf-upload-preview" id="preview-emp_signature">
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
                                                <label class="custom-file-label" for="guardian_nid">Choose file</label>
                                            </div>
                                            <div class="ecf-upload-preview" id="preview-guardian_nid">
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
                                            value="{{ old('achieved_degree') }}" name="achieved_degree"
                                            placeholder=" BSc in CSE">
                                        @error('achieved_degree')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Institution</label>
                                        <input type="text"
                                            class="form-control @error('institution') is-invalid @enderror"
                                            value="{{ old('institution') }}" name="institution"
                                            placeholder="Institution name">
                                        @error('institution')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Passing Year</label>
                                        <input type="number" min="1970" max="{{ date('Y') }}"
                                            class="form-control @error('passing_year') is-invalid @enderror"
                                            value="{{ old('passing_year') }}" name="passing_year" placeholder="2022">
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
                                            value="{{ old('join_date') }}" onfocus="this.showPicker()" name="join_date">
                                        @error('join_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>In Time</label>
                                        <input type="time"
                                            class="form-control @error('last_in_time') is-invalid @enderror"
                                            value="{{ old('last_in_time') ?? '21:00:00' }}" name="last_in_time">
                                        @error('last_in_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Department <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('department') is-invalid @enderror"
                                            name="department" value="{{ old('department') }}"
                                            placeholder="Example: Accounts" required>
                                        @error('department')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Position <span class="text-danger">*</span></label>
                                        <select name="position_id" id="position_id"
                                            class="form-control select2 @error('position_id') is-invalid @enderror">
                                            <option value="" selected disabled>Select Position</option>
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
                                            <option selected value="0">No Applicable</option>
                                            @foreach ($branchs as $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Area <span class="text-danger">*</span></label>
                                        <select name="area[]" id="area_select"
                                            class="form-control select2 @error('area') is-invalid @enderror" multiple
                                            data-placeholder="Areas will be loaded from the branch/device...">
                                        </select>
                                        <small class="form-text" id="area_loading_hint">
                                            <i class="fa fa-spinner fa-spin"></i> Loading area list...
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
                                                value="{{ old('salary') }}" name="salary" placeholder="0" required>
                                            @error('salary')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Overtime</label>
                                        <select name="over_time_is"
                                            class="form-control @error('over_time_is') is-invalid @enderror">
                                            <option value="yes" @selected(old('over_time_is') == 'yes')>Yes</option>
                                            <option value="no" @selected(old('over_time_is') == 'no')>No</option>
                                        </select>
                                        @error('over_time_is')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control @error('status') is-invalid @enderror"
                                            required>
                                            <option value="present" @selected(old('status', 'present') == 'present')>Present
                                            </option>
                                            <option value="left" @selected(old('status') == 'left')>Left</option>
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
                                                title="Yes: The system will shut down automatically if the employee forgets to Check Out. Applicable to general Day Shift employees. || No: Auto Check Out will be off — For Night Guard, Driver, Operator etc., they have to check out manually.">
                                            </i>
                                        </label>
                                        <select name="auto_checkout" id="auto_checkout"
                                            class="form-control @error('auto_checkout') is-invalid @enderror" required>
                                            <option value="1" {{ old('auto_checkout', 1) == 1 ? 'selected' : '' }}>
                                                Yes
                                            </option>
                                            <option value="0" {{ old('auto_checkout', 1) == 0 ? 'selected' : '' }}>No
                                            </option>
                                        </select>
                                        @error('auto_checkout')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================= USER ACCESS ================= --}}
                        <div class="card ecf-section">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                <div class="d-flex align-items-center" style="gap:.6rem;">
                                    <span class="ecf-icon"><i class="fa fa-user-lock"></i></span>
                                    <div>
                                        <h4>User Access</h4>
                                        <small>Enable korle employee er jonno login account create hobe</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center" style="gap:1rem;">
                                    {{-- Lock/Unlock icon: locked = employee Inactive, unlocked = employee Active --}}
                                    <button type="button" id="accountLockBtn" class="btn btn-sm btn-outline-secondary"
                                        style="display:none;" title="Click to toggle account status">
                                        <i class="fa fa-lock" id="accountLockIcon"></i>
                                        <span id="accountLockLabel">Locked (Inactive)</span>
                                    </button>
                                    <input type="hidden" name="account_lock" id="account_lock" value="0">

                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="create_user_toggle"
                                            name="create_user" value="1" {{ old('create_user') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="create_user_toggle">Create User
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body" id="userAccessBody"
                                style="{{ old('create_user') ? '' : 'display:none;' }}">
                                <div class="row">
                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Login Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('user_name') is-invalid @enderror"
                                            value="{{ old('user_name') }}" name="user_name" id="user_name"
                                            placeholder="Auto-fill from Name field">
                                        @error('user_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Login Email <span class="text-danger">*</span></label>
                                        <input type="email"
                                            class="form-control @error('user_email') is-invalid @enderror"
                                            value="{{ old('user_email') }}" name="user_email" id="user_email"
                                            placeholder="Auto-fill from Email field">
                                        @error('user_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Role <span class="text-danger">*</span></label>
                                        <select name="role_name" id="role_name"
                                            class="form-control select2 @error('role_name') is-invalid @enderror">
                                            <option value="" selected disabled>Select Role</option>
                                            @foreach ($userRoll as $value)
                                                <option value="{{ $value->id }}">
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
                                            <option value="Admin" @selected(old('type') == 'Admin')>Admin</option>
                                            <option value="Employee" @selected(old('type', 'Employee') == 'Employee')>Employee</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Password <span class="text-danger">*</span></label>
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            id="user_password" placeholder="Password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-4 ecf-field">
                                        <label>Confirm Password <span class="text-danger">*</span></label>
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
                                <i class="fa fa-save"></i> &nbsp;Save
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
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            // select2 init (Branch and position load immediately; areas are loaded later via AJAX)
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // ---------- Bootstrap custom-file label update ----------
            $('.ecf-file-input').on('change', function() {
                var fileInput = this;
                var fileName = fileInput.files.length ? fileInput.files[0].name : 'Choose file';
                $(this).next('.custom-file-label').text(fileName);

                var $preview = $('#preview-' + fileInput.id);
                if (fileInput.files && fileInput.files[0] && fileInput.files[0].type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $preview.css('display', 'flex');
                        $preview.find('img').attr('src', e.target.result);
                        $preview.find('span').text(fileName);
                    };
                    reader.readAsDataURL(fileInput.files[0]);
                }
            });

            // ---------- Select2 manual validation helper ----------
            // Native `required` on a select2-hidden <select> doesn't reliably
            // trigger visible browser validation feedback, so we validate manually.
            function validateSelect2Field($select, label) {
                var val = $select.val();
                var isEmpty = !val || (Array.isArray(val) && val.length === 0);
                var $container = $select.next('.select2-container');

                // remove any previous error message right after this field's container
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

            // clear the error state as soon as the user picks a value
            $(document).on('select2:select select2:unselect change', '#position_id, #area_select, #role_name',
                function() {
                    var label = $(this).attr('id') === 'position_id' ? 'Position' :
                        ($(this).attr('id') === 'area_select' ? 'Area' : 'Role');
                    validateSelect2Field($(this), label);
                });

            // ---------- Full form validation on submit ----------
            var form = document.getElementById('employeeCreateForm');
            var $submitBtn = $('#ecfSubmitBtn');
            var submitBtnOriginalHtml = $submitBtn.html();

            function resetSubmitButton() {
                $submitBtn.prop('disabled', false).html(submitBtnOriginalHtml);
            }

            // Safety net: some browsers restore the page from cache (back/forward
            // navigation) with the button still showing the disabled "Saving..."
            // state from a previous attempt. Always reset it when the page is shown.
            window.addEventListener('pageshow', function(event) {
                resetSubmitButton();
            });

            form.addEventListener('submit', function(event) {
                // Always start from a clean, enabled button state for this attempt.
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
                    // If anything above throws, do NOT let it fall through to the
                    // "disable button" branch — treat it as invalid and let the
                    // user try again instead of getting stuck.
                    console.error('Validation error:', err);
                    valid = false;
                }

                var isFormValid = valid && positionOk && areaOk && roleOk;

                if (!isFormValid) {
                    event.preventDefault();
                    event.stopPropagation();

                    // Explicitly guarantee the button stays clickable so the user
                    // can fix the missing field(s) and press Save again.
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
                    // Only disable + show spinner when the form is actually
                    // about to submit to the server.
                    $submitBtn.prop('disabled', true)
                        .html('<i class="fa fa-spinner fa-spin"></i> Saving...');
                }
                form.classList.add('was-validated');
            });

            // ---------- Load ZKTeco Areas via AJAX (fast initial page load) ----------
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
                        $hint.html('<i class="fa fa-info-circle"></i> No areas found.');
                    } else {
                        $.each(areas, function(index, item) {
                            $areaSelect.append(
                                $('<option>', {
                                    value: item.id,
                                    text: item.area_name
                                })
                            );
                        });
                        $hint.remove();
                    }
                },
                error: function() {
                    $hint.html(
                        '<i class="fa fa-exclamation-triangle text-danger"></i> Unable to load areas. Please reload the page and try again.'
                    );
                },
                complete: function() {
                    $areaSelect.trigger('change');
                }
            });
        }
    </script>

    <script>
        // ---------- User Access toggle + auto-fill ----------
        var $userToggle = $('#create_user_toggle');
        var $userBody = $('#userAccessBody');
        var userEdited = {
            user_name: false,
            user_email: false
        };

        function toggleUserFieldsRequired(state) {
            $userBody.find('#user_name, #user_email, #user_type, #user_password, #user_password_confirmation')
                .prop('required', state);
            // role_name is a select2 field, validated manually (not via native required)
        }

        function syncUserFieldsFromPersonalInfo() {
            if (!userEdited.user_name) {
                $('#user_name').val($('input[name="name"]').val());
            }
            if (!userEdited.user_email) {
                $('#user_email').val($('input[name="email"]').val());
            }
        }

        $('#user_name').on('input', function() {
            userEdited.user_name = true;
        });
        $('#user_email').on('input', function() {
            userEdited.user_email = true;
        });

        $('input[name="name"]').on('input', function() {
            if ($userToggle.is(':checked') && !userEdited.user_name) {
                $('#user_name').val($(this).val());
            }
        });
        $('input[name="email"]').on('input', function() {
            if ($userToggle.is(':checked') && !userEdited.user_email) {
                $('#user_email').val($(this).val());
            }
        });

        if ($userToggle.is(':checked')) {
            toggleUserFieldsRequired(true);
        }

        // ---------- Lock / Unlock account status ----------
        // Locked  -> employee saved as Inactive
        // Unlocked -> employee saved as Active
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

        // default state: locked / inactive
        renderLockState(false);

        $lockBtn.on('click', function() {
            var currentlyUnlocked = $lockInput.val() == '1';
            renderLockState(!currentlyUnlocked);
        });

        // ---------- Single toggle handler (create_user checkbox) ----------
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
                renderLockState(false); // reset to locked/inactive when toggle turned off
                $('#role_name').next('.select2-container').find('.select2-selection')
                    .removeClass('is-invalid-select2');
                $('#role_name').next('.select2-container').next('.select2-error-msg').remove();
            }
        });

        // if the form re-renders with create_user already checked (validation error return), show lock button
        if ($userToggle.is(':checked')) {
            $lockBtn.show();
        }
    </script>
@endsection
