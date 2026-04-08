@extends('backend.layouts.master')

@section('title')
    Cashbook - {{ $title }}
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
                        @if (helper::roleAccess('inventorySetup.adjust.index'))
                            <li class="breadcrumb-item"><a href="{{ route('inventorySetup.adjust.index') }}">Customer
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Customer</span></li>
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
                    <h3 class="card-title">Add New Adjust</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('settings.adjust.index'))
                            <a class="btn btn-default" href="{{ route('inventorySetup.adjust.index') }}"><i
                                    class="fa fa-list"></i>
                                Adjust List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('inventorySetup.adjust.store') }}"
                        novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Date * :</label>
                                <input type="date" name="date" class="form-control" id="validationCustom01"
                                    placeholder="Date">
                                @error('date')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01"> Branch Name * : * :</label>
                                <select class="form-control select2" name="branch_id">
                                    <option selected disabled value="">--Select--</option>
                                    @foreach ($branch as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->branchCode . ' - ' . $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Type * :</label>
                                <select class="form-control" name="payment_type" onchange="showAccountList(this.value)">
                                    <option selected="" disabled="">Select</option>
                                    <option value="Credit">Credit</option>
                                    <option value="Deposit">Deposit</option>
                                </select>
                                @error('payment_type')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        
                            <div id="accountList" class="col-md-4 mb-3" style="display: none;">
                                <label for="validationCustom01">Account Name * :</label>
                                <select class="form-control select2" name="account_id">
                                    <option selected disabled value="">--Select--</option>
                                    @foreach ($account as $key => $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->accountCode . ' - ' . $value->account_name }}</option>
                                    @endforeach
                                </select>
                        
                                @error('account_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div id="expireddate" class="col-md-4 mb-3" style="display: none;">
                                <label for="validationCustom01">Expire Date * :</label>
                                <input type="date" name="expire_date" class="form-control" id="validationCustom01"
                                    placeholder="Date">
                                @error('expire_date')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01"> Customer * :</label>
                                <select class="form-control select2" name="customer_id">
                                    <option selected disabled value="">--Select--</option>
                                    @foreach ($customer as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->customerCode . ' - ' . $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Amount * :</label>
                                <input type="text" name="amount" id="amount" class="form-control"
                                   placeholder="Amount" value="{{ old('amount') }}"
                                    required>
                                @error('amount')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                          
                        
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom02">Note * :</label>
                                <input name="note" class="form-control" id="validationCustom02" placeholder="Note"
                                    value="{{ old('note') }}" required>
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

        function showAccountList(typeofadjustomet){
            if(typeofadjustomet == 'Deposit'){
                $("#accountList").show(500);
                $("#expireddate").hide(500);
            }else{
                $("#accountList").hide(500);
                $("#expireddate").show(500);
            }
        }

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
                    // lert('Error while request..');
                    alertMessage.error(' Error while request..');
                }
            });
        }

        function cehckBalance(amount) {

            var reminingAmount = $("#showamount").attr('data-id');

            if (reminingAmount < parseFloat(amount)) {
                // lert('Opps !! Your desired amount of money is not in the Account...');
                alertMessage.error(' Your desired amount of money is not in the Account...');

                $("#amount").val('');
            }
        }
    </script>

@endsection
