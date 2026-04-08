@extends('backend.layouts.master')

@section('title')
Expense - {{ $title }}
@endsection
@section('navbar-content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"> Expense </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('project.projectexpense.index'))
                    <li class="breadcrumb-item"><a href="{{ route('project.projectexpense.index') }}">Project Expense
                            List</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Project Expense</span></li>
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
                <h3 class="card-title">Add New Project Expense</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('project.projectexpense.index'))
                    <a class="btn btn-default" href="{{ route('project.projectexpense.index') }}"><i
                            class="fa fa-list"></i>
                        Project Expense List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('project.projectexpense.store') }}"
                    novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label>Date:</label>
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
                           <label for="validationCustom01">Account Head * :</label>
                           <select class="form-control select2" name="account_id" id="account_id">
                               <option selected disabled value="">--Select--</option>
                               <x-account :setAccounts="$accounts" />  
                           </select>
                           <span class="error text-red text-bold"></span>
                       </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Project * :</label>
                            <select class="form-control select2" onchange="getSubCat(this.value)" name="project_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($projects as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Category Name * :</label>
                            <select class="form-control select2" onchange="getSubCat(this.value)" name="category_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach ($category as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Sub-Category * :</label>
                            <select class="form-control select2" id="showsubhead" name="subcategory_id">
                                <option selected disabled value="">--Select--</option>
                            </select>
                            @error('subcategory_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Amount * :</label>
                            <input type="text" name="amount" id="amount" class="form-control"
                                onkeyup="cehckBalance(this.value)" placeholder="Amount" value="{{ old('amount') }}"
                                required>
                            @error('amount')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Note :</label>
                            <textarea type="text" name="note" rows="1" class="form-control"
                                placeholder="Note"></textarea>
                            @error('note')
                            <span class="error text-red text-bold">{{ $message }}</span>
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
    function getSubCat(catId) {
        $.ajax({
            url: "/admin/getSubCategory/", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                catId: catId
            },
            success: function (val) {
                $("#showsubhead").html(val);
            },
            error: function () {
                alert('Error while request..');
            }
        });
    }

    function getAccountBalance(account_id) {
        $.ajax({
            url: "/admin/getAccountBalance/", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                account_id: account_id
            },
            success: function (val) {
                $("#showamount").html('<span>Cureent Balance : ' + val + '</span>');
                $("#showamount").attr('data-id', val);
                $("#currentBalance").val(val);

            },
            error: function () {
                // alert('Error while request..');
                alertMessage.error('Error while request..');

            }
        });
    }

    function cehckBalance(amount) {

        // var reminingAmount = $("#showamount").attr('data-id');

        // if (reminingAmount == undefined) {
        //     $("#amount").val('');
        //     // alert('Please select Account Name*')
        //     alertMessage.error('Please select Account Name*');

        // }

        // if (reminingAmount < parseFloat(amount)) {
        //     // lert('Opps !! Your desired amount of money is not in the Account...');
        //     alertMessage.error('Your desired amount of money is not in the Account...');

        //     $("#amount").val('');
        // }
    }
</script>

@endsection