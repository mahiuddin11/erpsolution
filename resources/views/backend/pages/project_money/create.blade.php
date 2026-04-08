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
                        @if (helper::roleAccess('project.balance.index'))
                            <li class="breadcrumb-item"><a href="{{ route('project.balance.index') }}">Project List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>{{ $title ?? '' }}</span></li>
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
                    <h3 class="card-title">{{ $title ?? '' }}</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('project.balance.index'))
                            <a class="btn btn-default" href="{{ route('project.balance.index') }}"><i
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
                    <form class="needs-validation" method="POST" action="{{ route('project.balance.store') }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <span class="bg-green" style="padding: 5px; font-weight : bold"
                                    for="validationCustom01">Project Code * : {{ $projectCode }}</span>
                                <input type="hidden" name="projectBananceCode" class="form-control" id=""
                                    value="{{ $projectCode }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Date:</label>
                                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                    <input type="text" name="date" data-toggle="datetimepicker"
                                        value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
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
                                <label for="validationCustom01">Project * :</label>
                                <select class="form-control select2" name="project_id">
                                    <option selected disabled value="">--Select Project--</option>
                                    @foreach ($project as $key => $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->projectCode . ' - ' . $value->name }}</option>
                                    @endforeach
                                </select>

                                @error('project_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Account * :</label>
                                <select class="form-control select2" name="account_id"
                                    onchange="getAccountBalance(this.value)">
                                    <option selected disabled value="">--Select Account--</option>
                                    @foreach ($account as $key => $value)
                                        <option value="{{ $value->id }}">
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
                                <input type="number" name="debit" id="amount" onkeyup="cehckBalance(this.value)"
                                    class="form-control" id="validationCustom01" placeholder="Amount"
                                    value="{{ old('debit') }}">
                                @error('debit')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Note * :</label>
                                <textarea type="text" rows="1" name="note" class="form-control" id="validationCustom01"
                                    placeholder="Note here">{{ old('note') }}</textarea>
                                @error('note')
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
        function getAccountBalance(account_id) {

            $.ajax({
                url: "/admin/getAccountBalance/", // path to function
                method: "GET",
                data: {
                    "_token": "{{ csrf_token() }}",
                    account_id: account_id
                },
                success: function(val) {
                    $("#showamount").html('<span>Cureent Balance : ' + val + '</span>');
                    $("#showamount").attr('data-id', val);
                    $("#currentBalance").val(val);

                },
                error: function() {
                    // alert('Error while request..');
                    alertMessage.error('Error while request..');
                }
            });
        }




        function cehckBalance(amount) {
            var reminingAmount = $("#showamount").attr('data-id');
            var inoviceDue = $("#showaDueAmount").attr('data-id');
            if ((reminingAmount < parseFloat(amount)) || (inoviceDue < parseFloat(amount))) {
                // lert('Opps !! Please check your account balance and due balance.');
                alertMessage.error('Please check your account balance and due balance.');
                $("#amount").val('');
            }
        }
    </script>
@endsection
