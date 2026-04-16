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
                        @if (helper::roleAccess('hrm.lone.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.lone.index') }}">Lone
                                    Application List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Loan Application</span></li>
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
                    <h3 class="card-title">Add New Loan Applicaion</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('hrm.lone.index'))
                            <a class="btn btn-default" href="{{ route('hrm.lone.index') }}"><i class="fa fa-list"></i>
                                Salary Sheet List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('hrm.lone.store') }}"
                        enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="row">

                            <div class="col-md-4 mb-1">
                                <label for=""> EMPLOYEE NAME <span class="text-danger">*</span></label>
                                @if (auth()->user()->type != 'Admin' && auth()->user()->employee)
                                    <h4> {{ auth()->user()->employee->name ?? '' }}</h4>
                                    <input type="hidden" name="employee_id"
                                        value="{{ auth()->user()->employee->id ?? 0 }}">
                                @else
                                    <select class="select2 form-control select2-lg" aria-label=".select2-lg example"
                                        name="employee_id">
                                        <option selected disabled>Select employee</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('employee_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-1">
                                <label for=""> BRANCH NAME <span class="text-danger">*</span></label>
                                <select class="select2 form-control select2-lg" aria-label=".select2-lg example"
                                    name="branch_id">
                                    <option selected value="0">Not Applicable</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-1">
                                <label for="">Amount *:</label>
                                <input type="number" class="form-control input-rounded" name="amount"
                                    placeholder="Amount">
                                @error('amount')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-md-4 mb-1">
                                <label for="">Loan Adjustment *:</label>
                                <input type="number" class="form-control input-rounded" name="lone_adjustment"
                                    placeholder="Loan Adjustment Amount">
                                @error('lone_adjustment')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-1">
    <label>Adjustment Starting Month:</label>
    <input type="month" class="form-control input-rounded" name="adjustment_start">
    
    @error('adjustment_start')
        <span class="error text-red text-bold">{{ $message }}</span>
    @enderror
</div>



                            <div class="col-md-4 mb-1">
                                <label for="">Loan Document [Image/File]:</label>
                                <input type="file" class="form-control input-rounded" name="file"
                                    placeholder="Application End Date">
                                @error('file')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-md-12 mb-1">
                                <label for="">Loan Application Reason</label>

                                <textarea name="reason" id="" cols="30" class="form-control" rows="3"></textarea>
                                @error('reason')
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
