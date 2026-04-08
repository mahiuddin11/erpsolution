@extends('backend.layouts.master')

@section('title')
    Settings - {{ $title }}
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
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('project.project.index'))
                            <li class="breadcrumb-item"><a href="{{ route('project.project.index') }}">Project List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Project</span></li>
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
                    <h3 class="card-title">Add New Project</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('project.project.index'))
                            <a class="btn btn-default" href="{{ route('project.project.index') }}"><i
                                    class="fa fa-list"></i>
                                Project List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('project.project.store') }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <span class="bg-green" style="padding: 5px; font-weight : bold"
                                    for="validationCustom01">Project Code * : {{ $projectCode }}</span>
                                <input type="hidden" name="projectCode" class="form-control" id=""
                                    value="{{ $projectCode }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="validationCustom01">Project Name * :</label>
                                <input type="text" name="name" class="form-control" id="validationCustom01"
                                    placeholder="Project Name" value="{{ old('name') }}">
                                @error('name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="ledger_id">Company Name *:
                                    {{-- <button type="button" class="btn btn-sm btn-primary ml-2" data-toggle="modal"
                                        data-target="#addCustomerModel">
                                    </button> --}}
                                </label>
                                <select class="form-control select2" name="ledger_id" id="ledger_id">
                                    <option selected disabled value="">--Select Company--</option>

                                    @foreach ($ledgers as $ledger)
                                        <option value="{{ $ledger->id }}">
                                            {{ $ledger->account_name }}
                                        </option>
                                    @endforeach

                                </select>
                                @error('ledger_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- <div class="col-md-3 mb-3">
                                <label for="validationCustom01">Company Name * :</label>
                                <select class="form-control select2" name="customer_id">
                                    <option selected disabled value="">--Select--</option>
                                    @foreach ($customer as $key => $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->co_name }}</option>
                                    @endforeach
                                </select>
                                <span style="color :red; " id="showamount"></span>
                                @error('customer_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div> --}}



                            <div class="col-md-3 mb-3">
                                <label for="validationCustom01">Manager Name * :</label>
                                <select class="form-control select2" id="manager_id" name="manager_id">
                                    <option selected disabled value="">--Select Branch--</option>
                                    @foreach ($managers as $key => $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->branchCode . ' - ' . $value->name }}</option>
                                    @endforeach
                                </select>
                                <span style="color :red; " id="showamount"></span>
                                @error('manager_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="validationCustom01">Budget * :</label>
                                <input type="text" name="budget" class="form-control" id="validationCustom01"
                                    placeholder="Project Total Budget" value="{{ old('budget') }}">
                                @error('budget')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Estimate Profit * :</label>
                                <input type="number" name="estimate_profit" class="form-control" id="validationCustom01"
                                    placeholder="Amount" value="{{ old('estimate_profit') }}">
                                @error('estimate_profit')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- <div class="col-md-3 mb-3">

                            <label for="validationCustom01">Received Amount * :</label>
                            <input type="text" name="received_amount" class="form-control" id="validationCustom01"
                                placeholder="Project Total Budget Received" value="{{ old('received_amount') }}">
                            @error('received_amount')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div> --}}
                            <div class="col-md-3 mb-3">
                                <label>Start Date:</label>
                                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                    <input type="text" name="start_date" data-toggle="datetimepicker"
                                        value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
                                        data-target="#reservationdate" />
                                    <div class="input-group-append" data-target="#reservationdate"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                @error('start_date')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Complete Date:</label>
                                <div class="input-group date" id="reservationdate1" data-target-input="nearest">
                                    <input type="text" name="end_date" data-toggle="datetimepicker"
                                        value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
                                        data-target="#reservationdate1" />
                                    <div class="input-group-append" data-target="#reservationdate1"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                @error('end_date')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="validationCustom01">Address * :</label>
                                <textarea type="text" rows="1" name="address" class="form-control" id="validationCustom01"
                                    placeholder="Project Address">{{ old('address') }}</textarea>
                                @error('address')
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
        $(document).ready(function() {
            $('#branch_id').on('change', function() {
                var self = $(this).val();
                $.ajax({
                    url: "{{ route('project.project.loadmanager') }}",
                    method: "GET",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        branch_id: self
                    },
                    success: function(data) {
                        $('#manager_id').html(data);
                    }
                })

            })
        })
    </script>
@endsection
