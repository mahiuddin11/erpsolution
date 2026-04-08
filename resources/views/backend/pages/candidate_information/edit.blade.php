@extends('backend.layouts.master')

@section('title')
    Settings - {{ $title }}
@endsection


@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Assets Category </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('assets.category.index'))
                            <li class="breadcrumb-item"><a href="{{ route('assets.category.index') }}">Category
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Assets Cateogory</span></li>
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
                    <h3 class="card-title">Category List</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('assets.category.create'))
                            <a class="btn btn-default" href="{{ route('assets.category.create') }}"><i
                                    class="fas fa-plus"></i>
                                Add New</a>
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

                    <form class="needs-validation" method="POST"
                        action="{{ route('assets.category.update', $editInfo->id) }}" novalidate>
                        @csrf
                        <h3>Basic Information</h3>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">First Name * :</label>
                                <input type="text" name="first_name" class="form-control" id="validationCustom01"
                                    placeholder="First Name" value="{{ $editInfo->first_name }}">
                                @error('first_name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Last Name * :</label>
                                <input type="text" name="last_name" class="form-control" id="validationCustom01"
                                    placeholder="Last Name" value="{{ $editInfo->last_name }}">
                                @error('last_name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Phone Number* :</label>
                                <input type="text" name="phone" class="form-control" id="validationCustom01"
                                    placeholder="Phone Number" value="{{ $editInfo->phone }}">
                                @error('phone')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Email* :</label>
                                <input type="email" name="email" class="form-control" id="validationCustom01"
                                    placeholder="Email" value="{{ $editInfo->email }}">
                                @error('email')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Alternate Phone Number :</label>
                                <input type="text" name="alternate_phone" class="form-control" id="validationCustom01"
                                    placeholder="Alternate phone Number" value="{{ $editInfo->alternate_phone }}">
                                @error('alternate_phone')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">SSN :</label>
                                <input type="text" name="ssn" class="form-control" id="validationCustom01"
                                    placeholder="ssn" value="{{ $editInfo->ssn }}">
                                @error('ssn')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Present address*:</label>
                                <input type="text" name="present_address" class="form-control" id="validationCustom01"
                                    placeholder="Present Address" value="{{ $editInfo->present_address }}">
                                @error('present_address')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Permanent Address *:</label>
                                <input type="text" name="permanent_address" class="form-control"
                                    id="validationCustom01" placeholder="Permanent Address"
                                    value="{{ $editInfo->permanent_address }}">
                                @error('permanent_address')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <h3>Educationl Inforatmion</h3>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Obtain Degree *:</label>
                                <input type="text" name="obtain_degree" class="form-control" id="validationCustom01"
                                    placeholder="Alternate phone Number" value="{{ $editInfo->obtain_degree }}">
                                @error('obtain_degree')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">University :</label>
                                <input type="text" name="university" class="form-control" id="validationCustom01"
                                    placeholder="Alternate phone Number" value="{{ $editInfo->university }}">
                                @error('university')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Cgpa :</label>
                                <input type="text" name="cgpa" class="form-control" id="validationCustom01"
                                    placeholder="Cgpa" value="{{ $editInfo->cgpa }}">
                                @error('cgpa')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Comments :</label>
                                <input type="text" name="comments" class="form-control" id="validationCustom01"
                                    placeholder="Comments" value="{{ $editInfo->comments }}">
                                @error('comments')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <h3>Working Experience</h3>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Company Name:</label>
                                <input type="text" name="company_name" class="form-control" id="validationCustom01"
                                    placeholder="Company Name" value="{{ $editInfo->company_name }}">
                                @error('company_name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Experience :</label>
                                <input type="text" name="work_experience" class="form-control"
                                    id="validationCustom01" placeholder="Working Experience"
                                    value="{{ $editInfo->work_experience }}">
                                @error('work_experience')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Supervisor:</label>
                                <input type="text" name="supervisor" class="form-control" id="validationCustom01"
                                    placeholder="Supervisor" value="{{ $editInfo->supervisor }}">
                                @error('supervisor')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Upload CV* :</label>
                                <input type="file" name="image" class="form-control" id="validationCustom01"
                                    value="{{ $editInfo->image }}">
                                @error('image')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <button class="btn btn-info" type="submit"><i class="fa fa-save"></i>&nbsp;Update</button>
                    </form>

                </div>
                <!-- /.card-body -->
                <div class="card-footer">

                </div>
            </div>
        </div>
        <!-- /.col-->
    </div>
@endsection
