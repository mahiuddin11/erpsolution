@extends('backend.layouts.master')

@section('title')
Project - {{ $title }}
@endsection


@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    Invoice </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('project.invoiceCreate.index'))
                    <li class="breadcrumb-item"><a href="{{ route('project.invoiceCreate.index') }}">Invoice List</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Edit Invoice</span></li>
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
                <h3 class="card-title">Service List</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('project.invoiceCreate.create'))
                    <a class="btn btn-default" href="{{ route('project.invoiceCreate.create') }}"><i
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
                    action="{{ route('project.invoiceCreate.update', $editInfo->id) }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label> Date:</label>
                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                <input type="text" name="date" data-toggle="datetimepicker"
                                    value="{{ $editInfo->date }}" class="form-control datetimepicker-input"
                                    data-target="#reservationdate" />
                                <div class="input-group-append" data-target="#reservationdate"
                                    data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            @error('date')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Branch Name * :</label>
                            <select class="form-control select2" id="branch_id" name="branch_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($branchs as $key => $value)
                                <option {{$editInfo->branch_id == $value->id ? "selected":""}} value="{{ $value->id }}">
                                    {{ $value->branchCode ." - ". $value->name }}</option>
                                @endforeach
                            </select>

                            @error('branch_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Project Name * :</label>
                            <select class="form-control select2" id="project_id" name="project_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($project as $key => $value)
                                <option {{$editInfo->project_id == $value->id ? "selected":""}}
                                    value="{{ $value->id }}">
                                    {{ $value->projectCode ." - ". $value->name }}</option>
                                @endforeach
                            </select>

                            @error('project_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Customer Name * :</label>
                            <select class="form-control select2" id="customer_id" name="customer_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($customer as $key => $value)
                                <option {{$editInfo->customer_id == $value->id ? "selected":""}}
                                    value="{{ $value->id }}">
                                    {{ $value->customerCode ." - ". $value->name }}</option>
                                @endforeach
                            </select>

                            @error('customer_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Account Name * :</label>
                            <select class="form-control select2" id="account_id" name="account_id">
                                <option disabled value="">--Select--</option>
                                @foreach ($accounts as $key => $value)
                                <option {{ $editInfo->account_id == $value->id ? "selected":""}}
                                    value="{{ $value->id }}">
                                    {{ $value->accountCode ." - ". $value->account_name }}</option>
                                @endforeach
                            </select>

                            @error('account_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Profit * :</label>
                            <input type="text" name="profit" class="form-control" id="validationCustom01"
                                placeholder="0000" value="{{ $editInfo->profit }}">
                            @error('profit')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Total value * :</label>
                            <input type="text" name="total_value" class="form-control" id="validationCustom01"
                                placeholder="0000" value="{{ $editInfo->total_value }}">
                            @error('total_value')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom02">Details* :</label>
                            <textarea name="details" class="form-control" id="validationCustom02" placeholder="Address"
                                required>{{ $editInfo->note }}</textarea>
                            @error('details')
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