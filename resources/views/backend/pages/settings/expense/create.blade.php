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
                    @if (helper::roleAccess('settings.expense.index'))
                    <li class="breadcrumb-item"><a href="{{ route('settings.expense.index') }}">Expense List</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Expense</span></li>
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
                <h3 class="card-title">Add New Expense</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('settings.expense.index'))
                    <a class="btn btn-default" href="{{ route('settings.expense.index') }}"><i class="fa fa-list"></i>
                        Expense List</a>
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

                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Category Name * :</label>
                        <select class="form-control select2" onchange="getSubCat(this.value)" id="category_id">
                            <option selected disabled value="">--Select--</option>
                            @foreach ($category as $key => $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                        <span class="error text-red text-bold"></span>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Sub-Category * :</label>
                        <select class="form-control select2 subcategory_id" id="showsubhead">
                            <option selected disabled value="">--Select--</option>
                        </select>
                        <span class=" error text-red text-bold"></span>
                    </div>

                    <!-- @if (auth()->user()->type != 'Project') -->
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Branch Name * :</label>
                        <select class="form-control select2" id="branch_id">
                            <option selected disabled value="">--Select--</option>
                            @foreach ($branch as $key => $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                        <span class=" error text-red text-bold"></span>
                    </div>
                    <!-- @endif -->

                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Account Name * :</label>
                        <select class="form-control select2" id="account_id" onchange="getAccountBalance(this.value)">
                            <option selected disabled value="">--Select--</option>

                        </select>
                        <span style="color :red; " id="showamount"></span>
                        <span class=" error text-red text-bold"></span>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Amount * :</label>
                        <input type="text" id="amount" class="form-control" onkeyup="cehckBalance(this.value)"
                            placeholder="Amount" value="{{ old('amount') }}" required>
                        <span class=" error text-red text-bold"></span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Date:</label>
                        <div class="input-group date" id="reservationdate" data-target-input="nearest">
                            <input type="text" id="date" data-toggle="datetimepicker" value="{{ date('Y-m-d') }}"
                                class="form-control datetimepicker-input" data-target="#reservationdate" />
                            <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                        <span class=" error text-red text-bold"></span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Note :</label>
                        <textarea type="text" id="note" rows="1" class="form-control" placeholder="Note"></textarea>

                        <span class="error text-red text-bold"></span>
                    </div>
                    <div class="col-md-8 mt-3 text-right mb-3">
                        <button class="btn btn-info text-right" id="add_new" type="button"><i class="fa fa-plus"></i>
                            &nbsp;Add
                            New</button>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <form class="needs-validation" method="POST" action="{{ route('settings.expense.store') }}" novalidate>
                    @csrf
                    <table class="table table-bordered table-hover" id="show_item">
                        <thead>
                            <tr>
                                <th colspan="8">Expense</th>
                            </tr>
                            <tr>
                                <td class="text-center"><strong>Category</strong></td>
                                <td class="text-center"><strong>Sub-Category</strong></td>
                                <td class="text-center"><strong>Branch Name </strong></td>
                                <td class="text-center"><strong>Account Name </strong></td>
                                <td class="text-center"><strong>Amount</strong></td>
                                <td class="text-center"><strong>Date</strong></td>
                                {{-- <td class="text-center"><strong>Note</strong></td> --}}
                                <td class="text-center"><strong>Action</strong></td>
                            </tr>
                        </thead>
                        <tbody id="main-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-right" colspan="4"><strong>Sub-Total(BDT)</strong></td>
                                <td class="text-right"><strong class=""></strong></td>
                                <td class="text-right"><strong class=""></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    <button class="btn btn-info" type="submit"><i class="fa fa-save"></i> &nbsp;Save</button>
                </form>
            </div>
        </div>
    </div>
    <!-- /.col-->
</div>



<script>
    $('body').on('click','#add_new',function(){
      let  category_id= $('#category_id option:selected').val();
      let  showsubhead= $('#showsubhead option:selected').val();
      let  branch_id= $('#branch_id option:selected').val();
      let  account_id= $('#account_id option:selected').val();
      let  amount= $('#amount').val();
      let  date= $('#date').val();
      let  note= $('#note').val();
      
      (!category_id ?  $('#category_id').closest('div').find('.error').text(`Category Can't Empty`): $('#category_id').closest('div').find('.error').text('') );
      (!branch_id ?  $('#branch_id').closest('div').find('.error').text(`Branch Can't Empty`): $('#branch_id').closest('div').find('.error').text('') );
      (!account_id ?  $('#account_id').closest('div').find('.error').text(`Account Can't Empty`): $('#account_id').closest('div').find('.error').text('') );
      (!amount ?  $('#amount').closest('div').find('.error').text(`Amount Can't Empty`): $('#amount').closest('div').find('.error').text('') );
      (!date ?  $('#date').closest('div').find('.error').text(`Date Can't Empty`): $('#date').closest('div').find('.error').text('') );
      if(!category_id || !branch_id || !account_id || !amount || !date) 
       return false;

      let html = `<tr>
        <td>
            <span>${$('#category_id option:selected').text()}</span>
            <input type="hidden" value="${category_id}" name="category_id[]">
        </td>
        <td>
            <span>${$('#showsubhead option:selected').text()}</span>
            <input type="hidden" value="${showsubhead}" name="subcategory_id[]">
        </td>
        <td>
            <span>${$('#branch_id option:selected').text()}</span>
            <input type="hidden" value="${branch_id}" name="branch_id[]">
        </td>
        <td>
            <span>${$('#account_id option:selected').text()}</span>
            <input type="hidden" value="${account_id}" name="account_id[]">
        </td>
        <td>
            <span>${amount}</span>
            <input type="hidden" value="${amount}" name="amount[]">
        </td>
        <td>
            <span>${date}</span>
            <input type="hidden" value="${date}" name="date[]">
            <input type="hidden" value="${note}" name="note[]">
        </td>
        <td>
            <a id="add_item" class="btn btn-danger" style="white-space: nowrap" href="javascript:;" title="Delete Item">
                <i class="fa fa-trash"></i>
            </a>
        </td>
    </tr>`;
        $('#main-table').append(html);
        $('#category_id').select2().val(null).trigger("change");
        $('#showsubhead').select2().val(null).trigger("change");
        $('#branch_id').select2().val(null).trigger("change");
        $('#account_id').select2().val(null);
        $('#amount').val('');
        // $('#date').val('');
        $('#note').val('');
        $('#showamount').html('');
    })

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
               let totalamount = 0;
                $.each($("input[name='amount[]']"), function () {
                        amount = Number($(this).val());
                        totalamount += amount;
                        });
                        alert(totalamount);
               let newamount = val - totalamount
                $("#showamount").html('<span>Cureent Balance : ' + newamount + '</span>');
                $("#showamount").attr('data-id', newamount);
                $("#currentBalance").val(newamount);

            },
            error: function () {
                // alert('Error while request..');
                alertMessage.error('Error while request..');

            }
        });
    }

    function cehckBalance(amount) {

        var reminingAmount = $("#showamount").attr('data-id');

        if (reminingAmount == undefined) {
            $("#amount").val('');
            // alert('Please select Account Name*')
            alertMessage.error('Please select Account Name*');

        }

        if (reminingAmount < parseFloat(amount)) {
            // lert('Opps !! Your desired amount of money is not in the Account...');
            alertMessage.error('Your desired amount of money is not in the Account...');

            $("#amount").val('');
        }
    }

    $(document).ready(function () {
        $('#branch_id').on('change', function () {
            let branch_id = $(this).val();
            $.ajax({
                url: "{{route('settings.expense.accountsearch')}}",
                method: "GET",
                data: {
                    "_token": "{{csrf_token()}}",
                    branch_id: branch_id
                },
                success: function (data) {
                    // alert(data);
                    $('#account_id').html(data);
                }
            })
        })
    })


</script>

@endsection