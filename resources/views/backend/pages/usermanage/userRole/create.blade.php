@extends('backend.layouts.master')

@section('title')
    Settings - {{ $title }}
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> User Role </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('usermanage.userRole.index') }}">User Role List</a>
                        </li>
                        <li class="breadcrumb-item active"><span>Add New User ROle</span></li>
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
                        <a class="btn btn-default" href="{{ route('usermanage.userRole.index') }}"><i
                                class="fa fa-list"></i>
                            Role List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('usermanage.userRole.store') }}"
                        novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Role Name * :
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" class="checkPermissionAll" id="checkPermissionAll">
                                        <label for="checkPermissionAll">
                                            All Check
                                        </label>
                                    </div>
                                </label>
                                <input type="text" name="role_name" class="form-control" id="validationCustom01"
                                    placeholder="Role Name" value="{{ old('role_name') }}">
                                @error('role_name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">

                                @error('child_menu')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror



                            </div>
                        </div>
                      <div class="row mb-3">
                          <div class="col-md-12">
                         <h3>Dashboard Access</h3>
                          </div>
                          <div class="col-md-2">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="1" id="flexCheckDefault1">
                                  <label class="form-check-label" for="flexCheckDefault1">
                                    Account Balance
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-2">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="2" id="flexCheckDefault2">
                                  <label class="form-check-label" for="flexCheckDefault2">
                                    Total Purchases
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-2">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="3" id="flexCheckDefault3">
                                  <label class="form-check-label" for="flexCheckDefault3">
                                    Today Purchase
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-2">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="4" id="flexCheckDefault4">
                                  <label class="form-check-label" for="flexCheckDefault4">
                                    Total Sales
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-2">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="5" id="flexCheckDefault5">
                                  <label class="form-check-label" for="flexCheckDefault5">
                                    Today Sale
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-2">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="6" id="flexCheckDefault6">
                                  <label class="form-check-label" for="flexCheckDefault6">
                                    Total Expense
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-2">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="7" id="flexCheckDefault7">
                                  <label class="form-check-label" for="flexCheckDefault7">
                                    Today Expense
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-2">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="8" id="flexCheckDefault8">
                                  <label class="form-check-label" for="flexCheckDefault8">
                                    Total Payment
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-2">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="9" id="flexCheckDefault9">
                                  <label class="form-check-label" for="flexCheckDefault9">
                                    Total Received
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-2">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="10" id="flexCheckDefault10">
                                  <label class="form-check-label" for="flexCheckDefault10">
                                    Supplier Due
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-2">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="11" id="flexCheckDefault11">
                                  <label class="form-check-label" for="flexCheckDefault11">
                                    Customer Due
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-2">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="12" id="flexCheckDefault12">
                                  <label class="form-check-label" for="flexCheckDefault12">
                                    Total Service
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-2">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="13" id="flexCheckDefault13">
                                  <label class="form-check-label" for="flexCheckDefault13">
                                    Branch Stock Chart
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-2">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="14" id="flexCheckDefault14">
                                  <label class="form-check-label" for="flexCheckDefault14">
                                    Full Summary
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="15" id="flexCheckDefault15">
                                  <label class="form-check-label" for="flexCheckDefault15">
                                    Pending Requisitions : Project
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="16" id="flexCheckDefault16">
                                  <label class="form-check-label" for="flexCheckDefault16">
                                    Pending Stock Manage : Branch
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="17" id="flexCheckDefault17">
                                  <label class="form-check-label" for="flexCheckDefault17">
                                    Pending Requisitions : Project
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="18" id="flexCheckDefault18">
                                  <label class="form-check-label" for="flexCheckDefault18">
                                    Total Employee
                                  </label>
                                </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="dashboardCHeck[]" value="19" id="flexCheckDefault19">
                                  <label class="form-check-label" for="flexCheckDefault19">
                                    Today Attendance
                                  </label>
                                </div>
                          </div>
                      </div>

                        <div class="row">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th withd="5%!important">#</th>
                                        <th width="20%!important;">Module</th>
                                        <th width="20%!important;">Menu</th>
                                        <th width="60%!important;">Permission</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    <!-- <tr>
                                        <td>1</td>
                                        <td>Branch</td>
                                        <td>Branch
                                            <br>
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" class="submenu submenu_bid" serial_id="bid"
                                                    id="sub_branch0">
                                                <label for="sub_branch0">
                                                    Select All
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <table class="table table-bordered">
                                                @foreach ($branch as $key => $eachBranch)
                                                <tr>
                                                    <td>
                                                        <div class="icheck-primary d-inline">
                                                            <input type="checkbox" name="branch[]"
                                                                value="{{ $eachBranch->id }}" class="child_menu_bid"
                                                                id="bid_{{ $key }}">
                                                            <label for="bid_{{ $key }}">
                                                                {{ $eachBranch->name }}
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </table>
                                        </td>
                                    </tr> -->
                                    @foreach ($userRole as $key1 => $value)
                                        <tr>
                                            <td>{{ $key1 + 2 }}</td>
                                            <td>{{ $value['label'] }}</td>
                                            <td>{{ $value['sub_menu'] }}
                                                <br>
                                                <div class="icheck-primary d-inline">
                                                    <input type="checkbox" name="parent_id[]" 
                                                    
                                                     value="{{$value['uniqueName']}}" class="submenu submenu_{{ $key1 }}"
                                                        serial_id="{{ $key1 }}"
                                                        id="sub_{{ $value['sub_menu'] }}{{ $key1 }}">
                                                    <label for="sub_{{ $value['sub_menu'] }}{{ $key1 }}">
                                                        Select All
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <table class="table table-bordered">

                                                    @foreach ($value['child_menu'] as $key => $submenu)
                                                        <tr>
                                                            <td>
                                                                <div class="icheck-primary d-inline">
                                                                    <input  type="checkbox" name="permission[]"
                                                                    value="{{ $submenu->route }}"
                                                                    class="child_menu_{{ $key1 }}"
                                                                    id="child_{{ $value['sub_menu'] }}{{ $key }}">
                                                                    <label
                                                                        for="child_{{ $value['sub_menu'] }}{{ $key }}">
                                                                        {{ $submenu->label }}
                                                                    </label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
@section('scripts')
    @include('backend.pages.usermanage.userRole.script')
@endsection
