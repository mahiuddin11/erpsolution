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
                        project </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('project.balance.index'))
                            <li class="breadcrumb-item"><a href="{{ route('project.balance.index') }}">Project Balance
                                    List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Project Balance</span></li>
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
                    <h3 class="card-title">Project Balance Edit</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('project.balance.create'))
                            <a class="btn btn-default" href="{{ route('project.balance.create') }}"><i
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
                        action="{{ route('project.balance.update', $editInfo->id) }}" novalidate>
                        @csrf
                        <div class="form-row">

                            <div class="col-md-4 mb-3">

                                <label>Date:</label>
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

                            <div class="col-md-3 mb-3">
                                <label for="validationCustom01">Project * : </label>
                                <select class="form-control select2" name="project_id">
                                    <option selected disabled value="">--Select--</option>
                                    @foreach ($project as $key => $value)
                                        <option {{ $editInfo->project_id == $value->id ? 'selected' : '' }}
                                            value="{{ $value->id }}">
                                            {{ $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Account * :</label>
                                <select class="form-control select2" name="account_id">
                                    <option selected disabled value="">--Select Account--</option>
                                    @foreach ($account as $key => $value)
                                        <option {{ $editInfo->account_id == $value->id ? 'selected' : '' }}
                                            value="{{ $value->id }}">
                                            {{ $value->accountCode . ' - ' . $value->account_code . ' - ' . $value->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span style="color :red; " id="showamount"></span>
                                @error('account_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Amount * :</label>
                                <input type="text" name="debit" class="form-control" value="{{ $editInfo->debit }}"
                                    id="validationCustom01" placeholder="Amount" value="{{ old('debit') }}">
                                @error('number')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Note * :</label>
                                <textarea type="text" rows="1" name="note" class="form-control" id="validationCustom01"
                                    placeholder="Note here">{{ $editInfo->note }}</textarea>
                                @error('note')
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
