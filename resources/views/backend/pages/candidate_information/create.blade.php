@extends('backend.layouts.master')

@section('title')
    settings - {{ $title }}
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Candidate Information </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('candidate.index'))
                            <li class="breadcrumb-item"><a href="{{ route('candidate.index') }}">Candidate List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Candidate </span></li>
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
                    <h3 class="card-title">Add New Candidate</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('candidate.index'))
                            <a class="btn btn-default" href="{{ route('candidate.index') }}"><i class="fa fa-list"></i>
                                Category List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('candidate.store') }}" novalidate
                        enctype="multipart/form-data">
                        @csrf
                        <h3>Basic Information</h3>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">First Name * :</label>
                                <input type="text" name="first_name" class="form-control" id="validationCustom01"
                                    placeholder="First Name" value="{{ old('first_name') }}">
                                @error('first_name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Last Name * :</label>
                                <input type="text" name="last_name" class="form-control" id="validationCustom01"
                                    placeholder="Last Name" value="{{ old('last_name') }}">
                                @error('last_name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Phone Number* :</label>
                                <input type="text" name="phone" class="form-control" id="validationCustom01"
                                    placeholder="Phone Number" value="{{ old('phone') }}">
                                @error('phone')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Email* :</label>
                                <input type="email" name="email" class="form-control" id="validationCustom01"
                                    placeholder="Email" value="{{ old('email') }}">
                                @error('email')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Alternate Phone Number :</label>
                                <input type="text" name="alternate_phone" class="form-control" id="validationCustom01"
                                    placeholder="Alternate phone Number" value="{{ old('alternate_phone') }}">
                                @error('alternate_phone')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">SSN :</label>
                                <input type="text" name="ssn" class="form-control" id="validationCustom01"
                                    placeholder="ssn" value="{{ old('ssn') }}">
                                @error('ssn')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Present address*:</label>
                                <input type="text" name="present_address" class="form-control" id="validationCustom01"
                                    placeholder="Present Address" value="{{ old('present_address') }}">
                                @error('present_address')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Permanent Address *:</label>
                                <input type="text" name="permanent_address" class="form-control"
                                    id="validationCustom01" placeholder="Permanent Address"
                                    value="{{ old('permanent_address') }}">
                                @error('permanent_address')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Upload CV* :</label>
                                <input type="file" name="image" class="form-control" id="validationCustom01"
                                    value="{{ old('image') }}">
                                @error('image')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                        <h3>Educationl Inforatmion</h3>
                        <div class="edu-info-duplicate">
                            <div class="form-row edu-information">
                                <div class="col-md-6 mb-3">
                                    <label for="validationCustom01">Obtain Degree *:</label>
                                    <input type="text" name="obtain_degree[]" class="form-control "
                                        id="validationCustom01" placeholder="Obtain Degree">
                                    @error('obtain_degree')
                                        <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="validationCustom01">Institution :</label>
                                    <input type="text" name="institution[]" class="form-control "
                                        id="validationCustom01" placeholder="Alternate phone Number">
                                    @error('institution')
                                        <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="validationCustom01">Cgpa :</label>
                                    <input type="text" name="cgpa[]" class="form-control " id="validationCustom01"
                                        placeholder="Cgpa">
                                    @error('cgpa')
                                        <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="validationCustom01">Comments :</label>
                                    <input type="text" name="comments[]" class="form-control "
                                        id="validationCustom01" placeholder="Comments">
                                    @error('comments')
                                        <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button id="remove-edu" type="button" class="btn btn-danger">Remove</button>
                            </div>
                        </div>
                        <button id="add-edu" type="button" class="btn btn-info" style="float: right">Add
                            More</button>
                        <h3>Working Experience</h3>
                        <div class="work-info-duplicate">
                            <div class="form-row work-information">
                                <div class="col-md-6 mb-3">
                                    <label for="validationCustom01">Company Name:</label>
                                    <input type="text" name="company_name[]" class="form-control"
                                        id="validationCustom01" placeholder="Company Name">
                                    @error('company_name')
                                        <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="validationCustom01">Experience :</label>
                                    <input type="text" name="experience[]" class="form-control"
                                        id="validationCustom01" placeholder="Working Experience">
                                    @error('experience')
                                        <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="validationCustom01">Supervisor:</label>
                                    <input type="text" name="supervisor[]" class="form-control"
                                        id="validationCustom01" placeholder="Supervisor">
                                    @error('supervisor')
                                        <span class=" error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button id="remove-work" type="button" class="btn btn-danger"
                                    style="height: 40px;
    margin-top: 27px">Remove</button>
                            </div>
                        </div>
                        <button id="add-work" type="button" class="btn btn-info">Add More</button>

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


    <script type="text/javascript">
        $(document).ready(function() {
            // alert('ff');
            $("#add-edu").click(function() {
                $('.edu-info-duplicate .edu-information:last-child').clone().appendTo(
                    '.edu-info-duplicate');
            });

            $(document).on('click', '#remove-edu', function() {
                if ($('.edu-information').length > 1) {
                    $(this).parents('.edu-information').remove();
                }
            });

            $('#add-work').click(function() {
                $('.work-info-duplicate .work-information:last-child').clone().appendTo(
                    '.work-info-duplicate');
            });

            $(document).on('click', '#remove-work', function() {
                if ($('.work-information').length > 1) {
                    $(this).parents('.work-information').remove();
                }
            })


        });
    </script>
@endsection
