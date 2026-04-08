@extends('backend.layouts.master')

@section('title')
Settings - {{$title}}
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
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    @if(helper::roleAccess('settings.customerOpening.index'))
                    <li class="breadcrumb-item"><a href="{{ route('settings.customerOpening.index') }}">Customer Opening Balance List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Opening</span></li>
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
                <h3 class="card-title">Add New Customer Opening Balance </h3>
                <div class="card-tools">
                    @if(helper::roleAccess('settings.branch.index'))
                    <a class="btn btn-default" href="{{ route('settings.customerOpening.index') }}"><i class="fa fa-list"></i>
                        Customer Opening List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('settings.customerOpening.store') }}" novalidate>
                    @csrf
                    <div class="form-row"> 

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Date * :</label>
                            <input type="date" name="date" class="form-control" id="validationCustom01" placeholder="Date" >
                            @error('date')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>



                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Branch Name * :</label>
                            <select class="form-control select2" name="branch_id">
                                <option selected disabled value="">--Select--</option>

                                @foreach($branch as $key => $value)
                                <option value="{{$value->id}}">{{$value->branchCode.' - '.$value->name}}</option>
                                @endforeach
                            </select>
                            @error('branch_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">To Customer * :</label>
                            <select class="form-control select2" name="customer_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach($customer as $key => $value)
                                <option value="{{$value->id}}">{{$value->customerCode.' - '.$value->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>

                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                         <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Amount * :</label>
                            <input type="text" name="amount" class="form-control"  id="amount" placeholder="Amount" value="{{ old('amount') }}" required>
                            @error('amount')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Amount * :</label>
                            <input type="text" name="amount" class="form-control"  id="amount" placeholder="Amount" value="{{ old('amount') }}" required>
                            @error('amount')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom02">Note* :</label>
                            <textarea name="note" rows="1" class="form-control" id="validationCustom02" placeholder="Note" value="{{ old('note') }}" required></textarea>
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
//    $(document).ready(function() {
//        $('.checkamount').keyup(function () {
//            alert('sdf');
//        });
//    });

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
                // lert('Error while request..');
                alertMessage.error('Error while request.');

            }
        });
    }

    function cehckBalance(amount) {

        var reminingAmount = $("#showamount").attr('data-id');

        if (reminingAmount < parseFloat(amount)) {
            // lert('Opps !! Your desired amount of money is not in the Account...');
            alertMessage.error('Your desired amount of money is not in the Account...');

            $("#amount").val('');
        }
    }
</script>




@endsection