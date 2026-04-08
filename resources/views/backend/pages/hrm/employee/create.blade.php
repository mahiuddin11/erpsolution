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
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Add New Employee</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('hrm.employee.index'))
                            <a class="btn btn-default" href="{{ route('hrm.employee.index') }}"><i class="fa fa-list"></i>
                                Employee List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('hrm.employee.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Basic details</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-1">
                                        <label for="">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-rounded" value="{{ old('name') }}"
                                            name="name">
                                        @error('name')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Attendance names <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ old('am_name') }}" name="am_name">
                                        @error('name')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Employee Id<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ old('id_card') }}" name="id_card">
                                        @error('id_card')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Email</label>
                                        <input type="email" class="form-control input-rounded"
                                            value="{{ old('email') }}" name="email">
                                        @error('email')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Profile Photo<span class="text-danger">*</span></label>
                                        <input type="file" class="form-control input-rounded"
                                            value="{{ old('image') }}" name="image">
                                        @error('image')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Signature [PNG Photo Only]<span
                                                class="text-danger">*</span></label>
                                        <input type="file" class="form-control input-rounded"
                                            value="{{ old('emp_signature') }}" name="emp_signature">
                                        @error('emp_signature')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-1">
                                        <label for="">Personal Number <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control input-rounded"
                                            value="{{ old('personal_phone') }}" name="personal_phone">
                                        @error('personal_phone')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Office Number</label>
                                        <input type="number" class="form-control input-rounded"
                                            value="{{ old('office_phone') }}" name="office_phone">
                                        @error('office_phone')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Marital Status</label>
                                        <select name="marital_status" class="form-control">
                                            <option value="married">Married</option>
                                            <option value="unmarried">Unmarried</option>
                                        </select>
                                        @error('marital_status')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Nid</label>
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ old('nid') }}" name="nid">
                                        @error('nid')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Date Of Birth</label>
                                        <input type="date" class="form-control input-rounded"
                                            value="{{ old('dob') }}" onfocus="this.showPicker()" name="dob">
                                        @error('dob')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Blood Group</label>
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ old('blood_group') }}" name="blood_group">
                                        @error('blood_group')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-control">
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                        @error('gender')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-1">
                                        <label for="">Experience</label>
                                        <textarea value="{{ old('experience') }}" name="experience" class="form-control input-rounded"></textarea>
                                        @error('experience')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Present Address</label>
                                        <textarea value="{{ old('present_address') }}" name="present_address" class="form-control input-rounded"></textarea>
                                        @error('present_address')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Permanent Address</label>
                                        <textarea value="{{ old('permanent_address') }}" name="permanent_address" class="form-control input-rounded"></textarea>
                                        @error('permanent_address')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Reference</label>
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ old('reference') }}" name="reference">
                                        @error('reference')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Guardian Number</label>
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ old('guardian_number') }}" name="guardian_number">
                                        @error('guardian_number')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Guardian NID Photo</label>
                                        <input type="file" class="form-control input-rounded"
                                            value="{{ old('guardian_nid') }}" name="guardian_nid">
                                        @error('guardian_nid')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Qualification Info</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-1">
                                        <label for="">Achieved Degree</label>
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ old('achieved_degree') }}" name="achieved_degree">
                                        @error('achieved_degree')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Institution</label>
                                        <input type="text" class="form-control input-rounded"
                                            value="{{ old('institution') }}" name="institution">
                                        @error('institution')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Passing Year</label>
                                        <input type="number" class="form-control input-rounded"
                                            value="{{ old('passing_year') }}" name="passing_year">
                                        @error('passing_year')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Office Information</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-1">
                                        <label for="">Joining Date</label>
                                        <input type="date" class="form-control input-rounded"
                                            value="{{ old('join_date') }}" onfocus="this.showPicker()" name="join_date">
                                        @error('join_date')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">In Time</label>
                                        <input type="time" class="form-control input-rounded"
                                            value="{{ old('last_in_time') ?? '21:00:00' }}" name="last_in_time">
                                        @error('last_in_time')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Department <span class="text-danger">*</span></label>
                                        <input type="text" name="department" class="form-control">
                                        @error('department')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Position <span class="text-danger">*</span></label>
                                        <select name="position_id" class="form-control">
                                            <option selected disabled>Select Position</option>
                                            @foreach ($positions as $value)
                                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('position_id')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Branch <span class="text-danger">*</span></label>
                                        <select name="branch_id" class="form-control">
                                            <option selected value="0">No Applicable</option>
                                            @foreach ($branchs as $value)
                                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="">Area <span class="text-danger">*</span></label>
                                        <select name="area[]" class="form-control select2" multiple>
                                            @foreach ($area as $item)
                                                <option value="{{ $item['id'] }}">{{ $item['area_name'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <label for="">Salary <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control input-rounded"
                                            value="{{ old('salary') }}" name="salary">
                                        @error('salary')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <label for="">Overtime</label>
                                        <select name="over_time_is" class="form-control">
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                        @error('salary')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <label for="">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control">
                                            <option value="present" selected>Present</option>
                                            <option value="left">Left</option>
                                        </select>
                                        @error('status')
                                            <span class="error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- <div class="col-md-3 mb-1">
                                        <label for="auto_checkout">Auto Check Out Allow <span
                                                class="text-danger">*</span></label>
                                        <select name="auto_checkout" id="auto_checkout" class="form-control" required>
                                            <option value="1" {{ old('auto_checkout', 1) == 1 ? 'selected' : '' }}>
                                                Yes </option>
                                            <option value="0" {{ old('auto_checkout', 1) == 0 ? 'selected' : '' }}>No
                                               </option>
                                        </select>
                                        @error('auto_checkout')
                                            <span class="error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div> --}}

                                    <div class="col-md-3 mb-1">
                                        <label for="auto_checkout" class="form-label" data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Auto Check Out Allow:

• Yes: 
  The system will automatically close the attendance (Check Out) if the employee forgets to do so. 
  Recommended for regular day shift employees.

• No: 
  Auto Check Out feature will be disabled. 
  Use this option for Night Guards, Drivers, Department Operators, Security staff, and similar roles. 
  These employees must manually Check Out.">

                                            Auto Check Out Allow <span class="text-danger">*</span>
                                        </label>

                                        <select name="auto_checkout" id="auto_checkout" class="form-control" required>
                                            <option value="1" {{ old('auto_checkout', 1) == 1 ? 'selected' : '' }}>
                                                Yes </option>
                                            <option value="0" {{ old('auto_checkout', 1) == 0 ? 'selected' : '' }}>No
                                                </option>
                                        </select>

                                        @error('auto_checkout')
                                            <span class="error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                        </div>
                        <button class="btn btn-info" type="submit"><i class="fa fa-save"></i> &nbsp;Save</button>
                    </form>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">

                </div>
            </div>
        </div>
        <!-- /.col-->
    </div>

    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
