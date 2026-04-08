@extends('backend.layouts.master')

@section('title')
Settings - {{ $title }}
@endsection


@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    Settings </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('settings.branch.index'))
                    <li class="breadcrumb-item"><a href="{{ route('settings.openingbalance.index') }}">Cash
                            Book</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Edit Opening balance</span></li>
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
                <h3 class="card-title">Edit Opening Balance</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('settings.branch.create'))
                    <a class="btn btn-default" href="{{ route('settings.branch.create') }}"><i class="fas fa-plus"></i>
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
                    action="{{ route('settings.openingbalance.update', $transections->id) }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label>Date:</label>
                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                <input type="text" name="date" data-toggle="datetimepicker"
                                    value="{{ $transections->date_ }}" class="form-control datetimepicker-input"
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
                        <div class="col-md-4 mb-4 accountList" id="accountId">
                            <label for="validationCustom01">Accounts * :</label>
                            <select name="to_account_id" class="form-control accountsList select2 accountList">
                                <option value='' selected disabled>--Select Accounts--</option>
                                @foreach($accounts as $value)
                                <x-account :setAccounts="$accounts" :selectVal="$editInfo->account_id"></x-account>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Amount * :</label>
                            <input type="text" value="{{ $transections->amount }}" name="amount" class="form-control"
                                id="validationCustom01" placeholder="Amount" required>
                            @error('amount')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom02">Note* :</label>
                            <textarea name="note" rows="1" class="form-control" id="validationCustom02"
                                placeholder="Note" required>{{ $transections->note }}</textarea>
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
    function getAccountList(branchId) {
        $.ajax({
            url: "/admin/getAllAccountHead/", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                branchId: branchId
            },
            success: function (data) {
                $("#accountId").show()
                let html = `
              
                        <div class="form-group">
                            <label>Accounts</label>
                            <select name="to_account_id" class="form-control accountsList select2 accountList" >
                                ${data}
                            </select>
                        </div>
                `;
                $('.accountList').html(html);
                $('.accountsList').select2();
            },
        });
    }

</script>


@endsection