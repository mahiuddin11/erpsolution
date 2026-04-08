@extends('backend.layouts.master')

@section('title')
Service - {{ $title }}
@endsection
@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"> Service </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('service.service.index'))
                    <li class="breadcrumb-item"><a href="{{ route('service.service.index') }}">Service List</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Service</span></li>
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
                <h3 class="card-title">Add New Service</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('service.service.index'))
                    <a class="btn btn-default" href="{{ route('service.service.index') }}"><i class="fa fa-list"></i>
                        Service List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('service.service.store') }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <span class="bg-green" style="padding: 5px; font-weight : bold"
                                for="validationCustom01">Service Code * : {{ $projectCode }}</span>
                            <input type="hidden" name="serciveCode" class="form-control" id=""
                                value="{{ $projectCode }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label> Date:</label>
                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                <input type="text" name="date" data-toggle="datetimepicker" value="{{ date('Y-m-d') }}"
                                    class="form-control datetimepicker-input" data-target="#reservationdate" />
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
                            <select class="form-control select2" onchange="getAccountList(this.value)" id="branch_id"
                                name="branch_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($branchs as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->branchCode ." - ". $value->name }}</option>
                                @endforeach
                            </select>

                            @error('branch_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Account Name * :</label>
                            <select class="form-control select2" id="account_id" name="account_id">
                                <option selected disabled value="">--Select--</option>
                            </select>

                            @error('account_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Customer Name * :</label>
                            <select class="form-control select2" id="customer_id" name="customer_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($customer as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->customerCode ." - ". $value->name }}</option>
                                @endforeach
                            </select>

                            @error('customer_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Details * :</label>
                            <textarea type="text" rows="1" name="details" class="form-control" id="validationCustom01"
                                placeholder="Type here your service details separated by comma">{{ old('details') }}</textarea>
                            @error('details')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Amount * :</label>
                            <input type="text" name="amount" class="form-control" id="validationCustom01"
                                placeholder="Amount" value="{{ old('amount') }}">
                            @error('amount')
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
    function getAccountList(branchId) {
        $.ajax({
            url: "/admin/getAllAccountHead/", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                branchId: branchId
            },
            success: function (data) {
                $('#account_id').html(data);
            },
        });
    }

</script>

@endsection