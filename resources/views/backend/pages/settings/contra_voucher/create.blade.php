@extends('backend.layouts.master')

@section('title')
    Settings - {{ $title }}
@endsection
<style>
    .card.card-default {
        background: #b7dede;
    }
</style>
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
                        @if (helper::roleAccess('settings.contra.voucher.index'))
                            <li class="breadcrumb-item"><a href="{{ route('settings.contra.voucher.index') }}">Contra Voucher
                                    List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Contra Voucher</span></li>
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
                    <h3 class="card-title">Add New Contra Voucher</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('settings.contra.voucher.index'))
                            <a class="btn btn-default" href="{{ route('settings.contra.voucher.index') }}"><i
                                    class="fa fa-list"></i>
                                Contra Voucher List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('settings.contra.voucher.store') }}"
                    novalidate>
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label>Invoice Number :</label>
                                <input class="bg-green form-control" readonly=""
                                    style="padding: 5px; font-weight : bold; width: 100%" value="{{ $invoice_no }} ">
                                <input type="hidden" name="invoice_no" class="form-control" id=""
                                    value="{{ $invoice_no }}">
                            </div>


                            <div class="col-md-4 mb-3">
                                <label>Date:</label>
                                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                    <input type="text" id="date" name="date" data-toggle="datetimepicker"
                                        value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
                                        data-target="#reservationdate" />
                                    <div class="input-group-append" data-target="#reservationdate"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                <span class=" error text-red text-bold"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Check No:</label>
                                <input type="text" name="" class="form-control" id="">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Check Date:</label>
                                <input type="text" name="" class="form-control" id="">
                            </div>

                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">

                        <table class="table table-bordered table-hover" id="show_item">
                            <thead>
                                <tr>
                                    <th colspan="8">Contra Voucher</th>
                                </tr>
                                <tr>
                                    <td width="35%"><strong>From Account Name </strong></td>
                                    <td width="35%"><strong>To Account Name </strong></td>
                                    <td width="40%"><strong>Amount</strong></td>
                                    <td width="10%"><strong>Action</strong></td>
                                </tr>
                            </thead>
                            <tbody id="main-table">
                                <tr>
                                    <td>
                                        <select class="form-control select2" onchange="getAccountBalance(this.value)"
                                            id="account_id">
                                            <option selected disabled value="">--Select--</option>
                                            <x-account :setAccounts="$accounts" />
                                        </select>
                                        <span class="error account_id_amount text-red text-bold"></span>
                                    </td>
                                    <td>
                                        <select class="form-control select2" id="to_account_id">
                                            <option selected disabled value="">--Select--</option>
                                            <x-account :setAccounts="$accounts" />
                                        </select>
                                        <span class=" error account_to_id_amount text-red text-bold"></span>
                                    </td>
                                    <td>
                                        <input type="text" id="amount" class="form-control" placeholder="Amount">
                                        <span class=" error text-red text-bold"></span>
                                    </td>
                                    <td>
                                        <button class="btn btn-info text-right" id="add_new" type="button"><i
                                                class="fa fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="col-md-12 mb-3">
                            <label for="validationCustom01">Note :</label>
                            <textarea type="text" id="note" name="note" rows="6" class="form-control" placeholder="Note"></textarea>

                            <span class="error text-red text-bold"></span>
                        </div>
                        <button class="btn btn-info" type="submit"><i class="fa fa-save"></i> &nbsp;Save</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.col-->
    </div>



    <script>
        $('body').on('click', '#add_new', function() {
            let account_id = $('#account_id option:selected').val();
            let to_account_id = $('#to_account_id option:selected').val();
            let amount = $('#amount').val();


            (!amount ? $('#amount').closest('td').find('.error').text(`Amount Can't Empty`) : $('#amount').closest(
                'td').find('.error').text(''));

            if (amount == "" || amount == 0) {
                $('#amount').closest('td').find('.error').text(`Amount Can't Empty`);
                return false;

            } else {
                $('#amount').closest('td').find('.error').text('');
            }

            if (!account_id || !to_account_id || !amount)
                return false;

            let html = `<tr>

        <td>
            <span>${$('#account_id option:selected').text()}</span>
            <input type="hidden" value="${account_id}" name="account_id[]">
        </td>
        <td>
            <span>${$('#to_account_id option:selected').text()}</span>
            <input type="hidden" value="${to_account_id}" name="to_account_id[]">
        </td>
        <td>
            <span>${amount}</span>
            <input type="hidden" value="${amount}" name="amount[]">
        </td>
        <td>
            <a id="add_item" class="btn btn-danger" style="white-space: nowrap" href="javascript:;" title="Delete Item">
                <i class="fa fa-trash"></i>
            </a>
        </td>
    </tr>`;
            $('#main-table').append(html);
            $('#account_id').select2().val(null);
            $('#to_account_id').select2().val(null);
            $('#amount').val('');
            $('#showamount').html('');
        })

        $(document).on('click', '#add_item', function() {
            if (confirm('Are You Sure')) {
                $(this).closest('tr').remove();
            }
        })


        // $(document).on('input', '#amount', function() {
        //     let form = Number($('.account_id_amount').attr('amount'));
        //     let to = $(this).val();

        //     if (form < to) {
        //         alert('No Available Balance');
        //         $(this).val(0)
        //     }
        // });

        $(document).on('change', '#account_id', function() {
            const self = $(this);
            const val = self.val();

            $('#to_account_id').find('option').each(function() {
                if ($(this).val() == val) {
                    $(this).attr('hidden', 'hidden');
                } else {
                    $(this).removeAttr('hidden');
                }
            });
        });

        function getSubCat(catId) {
            $.ajax({
                url: "/admin/getSubCategory/", // path to function
                method: "GET",
                data: {
                    "_token": "{{ csrf_token() }}",
                    catId: catId
                },
                success: function(val) {
                    $("#showsubhead").html(val);
                },
                error: function() {
                    alert('Error while request..');
                }
            });
        }

        function getAccountBalance(account_id) {
            $.ajax({
                url: "{{ route('settings.contra.checkBalance') }}", // path to function
                method: "GET",
                data: {
                    account_id: account_id
                },
                success: function(val) {
                    $('.account_id_amount').text('Balance: ' + val);
                    $('.account_id_amount').attr('amount', val);
                },
                error: function() {
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
    </script>
@endsection
