@extends('backend.layouts.master')

@section('title')
Settings - {{ $title }}
@endsection
@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"> User List</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('usermanage.user.index'))
                    <li class="breadcrumb-item"><a href="{{ route('usermanage.user.index') }}">User List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New User</span></li>
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
                <h3 class="card-title">{{ $title }}</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('usermanage.user.index'))
                    <a class="btn btn-default" href="{{ route('usermanage.user.index') }}"><i class="fa fa-list"></i>
                        User List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('usermanage.user.store') }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Name :</label>
                            <input type="text" name="name" class="form-control" id="validationCustom01"
                                placeholder="Name" value="{{ old('name') }}">
                            @error('name')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom02"> Email * :</label>
                            <input type="text" name="email" class="form-control" id="validationCustom02"
                                placeholder="Email" value="{{ old('email') }}" required>
                            @error('email')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Role Name * :</label>
                            <select name="role_name" id="" class="form-control ">
                                @foreach ($userRoll as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->role_name }}</option>
                                @endforeach
                            </select>
                            @error('role_name')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom02"> Phone * :</label>
                            <input type="text" name="phone" class="form-control" id="validationCustom02"
                                placeholder="Phone" value="{{ old('phone') }}" required>
                            @error('phone')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom02"> Type *:</label>
                            <select name="type" id="" onchange="showBranchDiv(this.value)" class="form-control select2">
                                <option value="" selected disabled>Select Type</option>
                                <option value="Admin">Admin</option>
                                <option value="Employee">Employee</option>
                                <!-- <option value="Project">Project</option> -->
                            </select>
                            @error('type')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3" >
                            <label for="validationCustom01">Employee  * :</label>
                            <select name="employee_id" class="form-control select2">
                                <option selected  value="0">--Select Employee--</option>
                                @foreach ($employess as $key => $employes)
                                <option value="{{ $employes->id }}">{{ $employes->name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3" id="showBranch" style="display: none">
                            <label for="validationCustom01">Branch Name * :</label>
                            <select name="branch_id" id="" class="form-control select2">
                                <option selected value="0">All Branch</option>
                                @foreach ($branchs as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->branchCode.'-'.$value->name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Password * :</label>
                            <input type="password" name="password" class="form-control" id="validationCustom01"
                                placeholder="Password" value="{{ old('password') }}" required>
                            @error('password')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Confirm Password * :</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                id="validationCustom01" placeholder="Confirm Password"
                                value="{{ old('password_confirmation') }}" required>
                            @error('password_confirmation')
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

<script>
    function showBranchDiv(type) {
        if (type != "Project") {
            $("#showBranch").show(500);
        } else {
            $("#showBranch").hide(500);
        }
    }
</script>
@endsection