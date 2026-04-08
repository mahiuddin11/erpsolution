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
                    @if (helper::roleAccess('hrm.salary.sheet.index'))
                    <li class="breadcrumb-item"><a href="{{ route('hrm.salary.sheet.index') }}">Salary Sheet
                            List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Salary Sheet</span></li>
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
                <h3 class="card-title">Add New Salary Sheet</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('hrm.salary.sheet.index'))
                    <a class="btn btn-default" href="{{ route('hrm.salary.sheet.index') }}"><i class="fa fa-list"></i>
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
                <form class="needs-validation" method="POST" action="{{ route('hrm.salary.sheet.store') }}" novalidate>
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-1">
                            <label for=""> EMPLOYEE NAME <span class="text-danger">*</span></label>
                            <select class="select2 form-control select2-lg" aria-label=".select2-lg example"
                                name="employee_id">
                                <option selected disabled>Select employee</option>
                                @foreach ($employees as $employee)
                                <option value="{{$employee->id}}">{{$employee->name}}</option>
                                @endforeach
                            </select>
                            @error('employee_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-1">
                            <label for="">MONTH</label>
                            <input type="month" class="form-control input-rounded" name="month"
                                placeholder="Account Details">
                            @error('month')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-1">
                            <label for="">PAID SALARY</label>
                            <input type="number" class="form-control input-rounded" name="paid_amount"
                                placeholder="Ex:2000" min="0">
                            @error('paid_amount')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-1">
                            <label for="">OVERTIME</label>
                            <input type="number" class="form-control input-rounded" name="overtime"
                                placeholder="Ex:2000" min="0">
                            @error('overtime')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-1">
                            <label for="">INCENTIVE</label>
                            <input type="number" class="form-control input-rounded" name="incentive"
                                placeholder="Ex:2000" min="0">
                            @error('incentive')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-1">
                            <label for="">BONUS</label>
                            <input type="number" class="form-control input-rounded" name="bonus" placeholder="Ex:2000"
                                min="0">
                            @error('bonus')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-1">
                            <label for="">PAID DATE</label>
                            <input type="date" class="form-control input-rounded" name="paid_date" placeholder="Ex:2000"
                                min="">
                            @error('paid_date')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-1">
                            <label for="">REMARKS/NOTE</label>
                            <input type="text" class="form-control input-rounded" name="reason" placeholder="Remarks">
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