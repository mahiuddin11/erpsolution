@extends('backend.layouts.master')

@section('title')
Settings - {{$title}}
@endsection
@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"> Settings </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    @if(helper::roleAccess('settings.company.index'))
                    <li class="breadcrumb-item"><a href="{{ route('settings.company.index') }}">Company List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Company</span></li>
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
                <h3 class="card-title">Add New Company</h3>
                <div class="card-tools">
                @if(helper::roleAccess('settings.company.index'))
                    <a class="btn btn-default" href="{{ route('settings.company.index') }}"><i class="fa fa-list"></i>
                    Company List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('settings.company.store') }}" novalidate enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Company Name * :</label>
                            <input type="text" name="company_name" class="form-control" id="validationCustom01"
                                placeholder="Company Name" value="{{ old('company_name') }}">
                            @error('company_name')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom02"> Logo * :</label>
                            <input type="file" name="logo" class="form-control" id="validationCustom02"
                                placeholder="Logo" value="{{ old('logo') }}" required>
                            @error('logo')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Invoice Logo * :</label>
                            <input type="file" name="invoice_logo" class="form-control" id="validationCustom01"
                                placeholder="Invoice Logo" value="{{ old('invoice_logo') }}" required>
                            @error('invoice_logo')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Favicon * :</label>
                            <input type="file" name="favicon" class="form-control" id="validationCustom01"
                                placeholder="Favicon" value="{{ old('favicon') }}" required>
                            @error('favicon')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Authority Signature:</label>
                            <input type="file" name="autority_signature" class="form-control" id="validationCustom01" placeholder="autority signature"
                                value="{{ old('autority_signature') }}">
                            @error('autority_signature')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                       
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Website * :</label>
                            <input type="text" name="website" class="form-control" id="validationCustom01"
                                placeholder="Website" value="{{ old('website') }}" required>
                            @error('website')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Phone * :</label>
                            <input type="text" name="phone" class="form-control" id="validationCustom01"
                                placeholder="Phone" value="{{ old('phone') }}" required>
                            @error('phone')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                       
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Email * :</label>
                            <input type="text" name="email" class="form-control" id="validationCustom01"
                                placeholder="Email" value="{{ old('email') }}" required>
                            @error('email')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Address * :</label>
                            <input type="text" name="address" class="form-control" id="validationCustom01"
                                placeholder="Address" value="{{ old('address') }}" required>
                            @error('address')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                       
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Identification Number * :</label>
                            <input type="text" name="task_identification_number" class="form-control" id="validationCustom01"
                                placeholder="Identification Number" value="{{ old('task_identification_number') }}" required>
                            @error('task_identification_number')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
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










@endsection