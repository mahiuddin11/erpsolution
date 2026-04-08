@extends('backend.layouts.master')

@section('title')
    Account - {{ $title }}
@endsection
<style>
    .card.card-default {
        background: #d7b082;
    }
</style>
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Account </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('settings.credit.voucher.index'))
                            <li class="breadcrumb-item"><a href="{{ route('settings.credit.voucher.index') }}">Receive
                                    Voucher
                                    List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Receive Voucher</span></li>
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
                    <h3 class="card-title">Add New Receive Voucher</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('settings.credit.voucher.index'))
                            <a class="btn btn-default" href="{{ route('settings.credit.voucher.index') }}"><i
                                    class="fa fa-list"></i>
                                Receive Voucher List</a>
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
                <form class="needs-validation" id="getform" method="POST"
                    action="{{ route('settings.credit.voucher.store') }}" novalidate>
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label>Invoice Number :</label>
                                <input type="text" name="invoice_no"
                                    style="padding: 5px; font-weight: bold; width: 100%;" class=" form-control"
                                    id="invoice_no" value="{{ $invoice_no }}" oninput="removeSpaces(this)">
                            </div>

                            {{-- <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Debit Account Head * :</label>
                                <select class="form-control select2" name="credit_account_id" id="credit_account_id">
                                    <option selected disabled value="">--Select--</option>
                                    <x-account :setAccounts="$creditaccountheas" />
                                    <option value="5">Due</option>
                                </select>
                                <span class="error text-red text-bold"></span>
                            </div> --}}
                            {{-- <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Cost Center *:</label>
                                <select class="form-control select2" id="cost_center">
                                    <option selected value="0">No Cost Center</option>
                                    <option value="project">Project</option>
                                    <option value="branch">Branch</option>
                                </select>
                                <span class="error text-red text-bold"></span>
                            </div>

                            <div class="col-md-4 mb-3" id="project_div" style="display: none;">
                                <label for="validationCustom01">Project *:</label>
                                <select class="form-control select2" id="project_id" name="project_id">
                                    <option selected value="0">--Select--</option>
                                    @foreach ($projects as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <span class="error text-red text-bold"></span>
                            </div>

                            <div class="col-md-4 mb-3" id="branch_div" style="display: none;">
                                <label for="validationCustom01">Branch *:</label>
                                <select class="form-control select2" id="branch_id" name="branch_id">
                                    <option selected value="0">--Select--</option>
                                    @foreach ($branchs as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <span class="error text-red text-bold"></span>
                            </div> --}}

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

                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-6 mb-3 d-none" id="customervoucher">
                                <label for="validationCustom01">Customer Voucher * :</label>
                                <select class="form-control select2 custvoucher">
                                </select>
                                <span class="error text-red text-bold"></span>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-6 mb-3 d-none" id="purchasevoucher">
                                <label for="validationCustom01" class="lebel_name">:</label>
                                <select class="form-control select2 prvoucher">
                                </select>
                                <span class="error text-red text-bold"></span>
                            </div>
                        </div>
                    </div>

                    <!-- /.card-body -->
                    <div class="card-footer">
                        <table class="table table-bordered table-hover" id="show_item">
                            <thead>
                                <tr>
                                    <th colspan="8">Receive Voucher</th>
                                </tr>
                                <tr>
                                    <td width="50%"><strong>Account Name </strong></td>
                                    <td width="10%" ><strong>Invoice</strong></td>
                                    <td width="15%" ><strong>Credit</strong></td>
                                    <td width="15%" ><strong>Debit</strong></td>
                                    <td width="10%"><strong>Action</strong></td>
                                </tr>
                            </thead>
                            <tbody id="main-table">
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <select style="width: 90%;" class="form-control select2" id="account_id">
                                                <x-account :setAccounts="$accounts" />
                                            </select>
                                            <span class=" error text-red text-bold"></span>
                                            <select name="type" style="width: 10%; margin-left: 5px;">
                                                <option value="CR">CR</option>
                                                <option value="DR">DR</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td id="payment-options-container">
                                        <!-- Payment options will be inserted here if the account is bill-by-bill -->
                                    </td>
                                    <td>
                                        <input type="text" id="credit" class="form-control" placeholder="credit">
                                        <span class=" error text-red text-bold"></span>
                                    </td>
                                    <td>
                                        <input type="text" id="debit" class="form-control" placeholder="Debit">
                                        <span class=" error text-red text-bold"></span>
                                    </td>
                                    <td>
                                        <button class="btn btn-info text-right" id="add_new" type="button"><i
                                                class="fa fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right"><strong>Sub-Total(BDT)</strong></td>
                                    <td class="text-right"></td>
                                    <td class="text-right" id="creditTotal"></td>
                                    <td class="text-right" id="debitTotal"></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="col-md-12 mb-3">
                            <label for="validationCustom01">Note :</label>
                            <textarea type="text" id="note" name="note" rows="5" class="form-control" placeholder="Note"></textarea>

                            <span class="error text-red text-bold"></span>
                        </div>

                        <button class="btn btn-info" id="getsubmit" type="submit"><i class="fa fa-save"></i>
                            &nbsp;Save</button>

                    </div>
                </form>
            </div>
        </div>
        <!-- /.col-->
    </div>
@endsection


@section('scripts')
    <script>
        function removeSpaces(input) {
            input.value = input.value.replace(/\s+/g, '');
        }

        $(document).on("input", 'input[name="debit[]"],input[name="credit[]"]', function() {
            totalAmount();
        })

        function totalAmount() {
            let creditamount = 0;
            let debitamount = 0;

            $('input[name="credit[]"]').each(function() {
                creditamount += Number($(this).val());
            })

            $('input[name="debit[]"]').each(function() {
                debitamount += Number($(this).val());
            })

            $("#creditTotal").text(creditamount);
            $("#debitTotal").text(debitamount);

        }

        $(document).ready(function() {

            $(document).on('click', '#getsubmit', function(e) {
                e.preventDefault();
                let creditamount = 0;
                let debitamount = 0;

                $('input[name="credit[]"]').each(function() {
                    creditamount += Number($(this).val());
                })

                $('input[name="debit[]"]').each(function() {
                    debitamount += Number($(this).val());
                })

                if (creditamount != debitamount) {
                    alert('Credit and Debit Amount Not Same');
                } else {
                    $('#getform').submit();
                }
            })


            $('#debit').prop('disabled', true);
            $('select[name="type"]').on('change', function() {
                var selectedType = $(this).val();
                if (selectedType === 'DR') {
                    $('#credit').prop('disabled', true).val(''); // Disable credit input and clear value
                    $('#debit').prop('disabled', false); // Enable debit input
                } else if (selectedType === 'CR') {
                    $('#debit').prop('disabled', true).val(''); // Disable debit input and clear value
                    $('#credit').prop('disabled', false); // Enable credit input
                }
            });


            function refreshSelect(eloop) {
                eloop.removeClass('changeOption');
                $('.changeOption').select2().val(null);
                $('.changeOption').select2();
                eloop.addClass('changeOption');
            }


            $('#supplier_id').on('select2:closing', function() {

                refreshSelect($(this));

                $('.lebel_name').html("Supplier Voucher");
                $('#purchasevoucher').removeClass('d-none');
                let supplier = $(this).val();
                $.ajax({
                    "url": "{{ route('settings.credit.voucher.purchasevoucher') }}",
                    "method": "GET",
                    dataType: "html",
                    "data": {
                        "supplier_id": supplier
                    },
                    success: function(data) {
                        $('.prvoucher').html(data);

                    }
                })

            })

            $('#employee_id').on('select2:closing', function() {

                refreshSelect($(this));

                $('.lebel_name').html("Employee Voucher");
                $('#purchasevoucher').removeClass('d-none');
                let employee_id = $(this).val();
                $.ajax({
                    "url": "{{ route('settings.credit.voucher.employeevoucher') }}",
                    "method": "GET",
                    dataType: "html",
                    "data": {
                        "employee_id": employee_id
                    },
                    success: function(data) {
                        $('.prvoucher').html(data);

                    }
                })
            })

            $('#customer_id').on('select2:closing', function() {
                refreshSelect($(this));
                $('.lebel_name').html("Customer Voucher");
                $('#purchasevoucher').removeClass('d-none');
                let customer_id = $(this).val();
                $.ajax({
                    "url": "{{ route('settings.credit.voucher.customervoucher') }}",
                    "method": "GET",
                    dataType: "html",
                    "data": {
                        "customer_id": customer_id
                    },
                    success: function(data) {
                        $('.prvoucher').html(data);

                    }
                })

            })

            $(document).on('change', '.prvoucher', function() {
                let val = $(this).val();
                let accountid = $('.prvoucher option:selected').attr('accountid');
                let Accountname = $(".prvoucher option:selected").attr('accountname');
                let amount = $(".prvoucher option:selected").attr('amount');
                if ($("." + val).length == 0) {
                    let html = `<tr class="${val}">
                         <td>
                             <span>${val} ${Accountname}</span>
                             <input type="hidden" value="${accountid}" name="account_id[]">
                             <input type="hidden" value="${val}" name="payment_invoice[]">
                         </td>
                         <td>
                            <input class="" type="number" value="${amount}" name="credit[]">
                         </td>
                         <td>
                            <input class="d-none" type="number" value="" name="debit[]">
                         </td>
                         <td>
                             <a id="add_item" class="btn btn-danger" style="white-space: nowrap" href="javascript:;" title="Delete Item">
                                 <i class="fa fa-trash"></i>
                             </a>
                         </td>
                         </tr>`;
                    $('#main-table').append(html);
                }

            })

            $(document).on('change', '.slvoucher', function() {
                let val = $(this).val();
                let amount = $(".slvoucher option:selected").attr('amount');
                if ($("." + val).length == 0) {
                    let html = `<tr class="${val}">
                         <td>
                             <span>${val} Account Payable</span>
                             <input type="hidden" value="14" name="account_id[]">
                             <input type="hidden" value="${val}" name="payment_invoice[]">
                         </td>
                         <td>
                            <input type="number" value="${amount}" name="amount[]">
                         </td>
                         <td>
                             <a id="add_item" class="btn btn-danger" style="white-space: nowrap" href="javascript:;" title="Delete Item">
                                 <i class="fa fa-trash"></i>
                             </a>
                         </td>
                        </tr>`;
                    $('#main-table').append(html);
                }

            })

        });



        $('body').on('click', '#add_new', function() {
            let account_id = $('#account_id option:selected').val();
            let credit = $('#credit').val();
            let debit = $('#debit').val();
            let payment_invoice = $('#payment_invoice option:selected').val() == undefined ? "":$('#payment_invoice option:selected').val();

            (!account_id ? $('#account_id').closest('td').find('.error').text(`Account Can't Empty`) : $(
                '#account_id').closest('td').find('.error').text(''));

            if (!credit && !debit) {
                $('#credit').closest('td').find('.error').text(`Amount Can't Empty`);
                $('#debit').closest('td').find('.error').text(`Amount Can't Empty`);
                return false;
            } else {
                $('#credit').closest('td').find('.error').text('');
                $('#debit').closest('td').find('.error').text('');
            }

            let html = `<tr>

   <td>

    <div class="d-flex align-items-start gap-1 flex-wrap">
  <div class="mr-3">
    <span>${$('#account_id option:selected').text()}</span>
    <input type="hidden" value="${account_id}" name="account_id[]">
  </div>

  <div class="mr-3">
    <select name="cost_center_type[]" class="form-control form-control-sm cost_center_type">
      <option value="">Select</option>
      <option value="branch">Branch</option>
      <option value="project">Project</option>
    </select>
  </div>

  <div class="d-none">
    <select name="branch_id[]" class="form-control select2 form-control-sm branch-section " style="min-width: 150px;">
      <option value="">Select Branch</option>
      @foreach($branches as $branch)
        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
      @endforeach
    </select>
  </div>

  <div class="d-none">
    <select name="project_id[]" class="form-control select2 form-control-sm project-section " style="min-width: 150px;">
      <option value="">Select Project</option>
      @foreach($projects as $project)
        <option value="{{ $project->id }}">{{ $project->name }}</option>
      @endforeach
    </select>
  </div>
</div>

   </td>
      <td>
 <input type="hidden" readonly value="${payment_invoice}" name="payment_invoice[]"> ${payment_invoice}
   </td>
   <td>
    <input class="${!credit ? 'd-none':''}" type="number" value="${credit}" name="credit[]">
   </td>
   <td >
       <input class="${!debit ? 'd-none':''}" type="number" value="${debit}" name="debit[]">
   </td>

   <td>
       <a id="add_item" class="btn btn-danger" style="white-space: nowrap" href="javascript:;" title="Delete Item">
           <i class="fa fa-trash"></i>
       </a>
   </td>
</tr>`;
            $('#main-table').append(html);
            $('.select2').select2({
    theme: 'bootstrap4', // Optional, depends on your theme
    width: '100%'
});
            $('#account_id').select2().val(null);
            $('#credit').val('');
            $('#debit').val('');
            $('#showamount').html('');
            $('#payment-options-container').html("");
            totalAmount();
        })

        $(document).on('click', '#add_item', function() {
            if (confirm('Are You Sure')) {
                $(this).closest('tr').remove();
            }
        })


        $('#main-table').on('change', '.cost_center_type', function () {
    let type = $(this).val();
    let row = $(this).closest('td');

    if (type === 'branch') {
        row.find('.branch-section').closest("div").removeClass('d-none').prop('disabled', false);
        row.find('.project-section').closest("div").addClass('d-none').prop('disabled', true);
    } else if (type === 'project') {
        row.find('.project-section').closest("div").removeClass('d-none').prop('disabled', false);
        row.find('.branch-section').closest("div").addClass('d-none').prop('disabled', true);
    } else {
        row.find('.branch-section, .project-section').addClass('d-none').prop('disabled', true);
    }
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
                url: "/admin/getAccountBalance/", // path to function
                method: "GET",
                data: {
                    "_token": "{{ csrf_token() }}",
                    account_id: account_id
                },
                success: function(val) {
                    let totalamount = 0;
                    $.each($("input[name='amount[]']"), function() {
                        amount = Number($(this).val());
                        totalamount += amount;
                    });
                    alert(totalamount);
                    let newamount = val - totalamount
                    $("#showamount").html('<span>Cureent Balance : ' + newamount + '</span>');
                    $("#showamount").attr('data-id', newamount);
                    $("#currentBalance").val(newamount);

                },
                error: function() {
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

        $(document).ready(function() {
            $('#account_id').change(function() {
                var accountId = $(this).val(); // Get the selected account ID

                // Clear previous payment options
                $('#payment-options-container').empty();

                if (accountId) {
                    $.ajax({
                        url: '{{ route('settings.dabit.voucher.checkBillByBill') }}', // Route to check bill-by-bill flag
                        type: 'GET',
                        data: {
                            account_id: accountId
                        },
                        success: function(response) {
                            console.log(response);
                            if (response.bill_by_bill) {
                                // Show payment options
                                $('#payment-options-container').html(`
                         <select class="form-control select2" id="payment_invoice">
                            ${response.payment_invoices.map(invoice => `<option value="${invoice.invoice}">${invoice.invoice} (${invoice.amount}) ${invoice.date}</option>`).join('')}
                            </select>
                        `);
                            }else{
                                $('#payment-options-container').html("")
                            }
                        },
                        error: function(xhr) {
                            console.error('Failed to check bill-by-bill flag:', xhr);
                        }
                    });
                }
            });
        });
    </script>

    <script>
        $(document).on('change', "#cost_center", function() {
            var value = this.value;

            // Hide both the project and branch divs initially
            $('#project_div').hide();
            $('#branch_div').hide();

            // Reset selected options for both project and branch
            $('#project_id').val('0').trigger('change'); // trigger change event if using select2
            $('#branch_id').val('0').trigger('change'); // trigger change event if using select2

            // Show the relevant div based on the selected value
            if (value === 'project') {
                $('#project_div').show();
            } else if (value === 'branch') {
                $('#branch_div').show();
            }
        });
    </script>
@endsection
