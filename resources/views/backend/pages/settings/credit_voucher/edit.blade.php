@extends('backend.layouts.master')

@section('title')
    Account - {{ $title }}
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
                        @if (helper::roleAccess('settings.credit.voucher.index'))
                            <li class="breadcrumb-item"><a href="{{ route('settings.credit.voucher.index') }}">Receive
                                    Voucher
                                    List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Receive Voucher</span></li>
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
                    <h3 class="card-title">Receive Voucher List</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('settings.credit.voucher.create'))
                            <a class="btn btn-default" href="{{ route('settings.credit.voucher.create') }}"><i
                                    class="fas fa-plus"></i>
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
                <form class="needs-validation" id="getform" method="POST"
                    action="{{ route('settings.credit.voucher.update', $editInfo->id) }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label>Invoice Number :</label>
                                <input class="bg-green form-control" readonly=""
                                    style="padding: 5px; font-weight : bold; width: 100%"
                                    value="{{ $editInfo->voucher_no }} ">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>Date:</label>
                                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                    <input type="text" id="date" name="date" data-toggle="datetimepicker"
                                        value="{{ date('Y-m-d') ?? $editInfo->date }}"
                                        class="form-control datetimepicker-input" data-target="#reservationdate" />
                                    <div class="input-group-append" data-target="#reservationdate"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                <span class=" error text-red text-bold"></span>
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
                                    <td width="10%"><strong>Invoice</strong></td>
                                    <td width="15%"><strong>Credit</strong></td>
                                    <td width="15%"><strong>Debit</strong></td>
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
                                @foreach ($editInfo->details as $details)
                                    <tr>
                                        <td>
                                            {{ $details->account->account_name }}
                                            <input type="hidden" value="{{ $details->account_id }}"
                                                name="account_id[]">


        <div class="d-flex align-items-start gap-1 flex-wrap">

            <div class="mr-3">
              <select name="cost_center_type[]" class="form-control form-control-sm cost_center_type">
                <option value="">Select</option>
                <option {{ !empty($details->branch_id) ? "selected":"" }} value="branch">Branch</option>
                <option {{ !empty($details->project_id) ? "selected":"" }} value="project">Project</option>
              </select>
            </div>

            <div class="{{ empty($details->branch_id) ? "d-none":"" }}">
              <select name="branch_id[]" class="form-control select2 form-control-sm branch-section " style="min-width: 150px;">
                <option value="">Select Branch</option>
                @foreach($branches as $branch)
                  <option {{ $details->branch_id == $branch->id ? "selected":"" }} value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="{{ empty($details->project_id) ? "d-none":"" }}">
              <select name="project_id[]" class="form-control select2 form-control-sm project-section " style="min-width: 150px;">
                <option value="">Select Project</option>
                @foreach($projects as $project)
                  <option {{ $details->project_id == $project->id ? "selected":"" }} value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

                                        </td>
                                        <td>
                                            {{ $details->payment_invoice }} <input type="hidden" readonly
                                                value="{{ $details->payment_invoice }}" name="payment_invoice[]">
                                        </td>
                                        <td>
                                            <input class="{{ $details->debit ? 'd-none' : '' }}" type="number"
                                                value="{{ $details->credit }}" name="credit[]">
                                        </td>
                                        <td>
                                            <input class="{{ $details->credit ? 'd-none' : '' }}" type="number"
                                                value="{{ $details->debit }}" name="debit[]">
                                        </td>
                                        <td>
                                            <a class="btn btn-danger remove_item" style="white-space: nowrap"
                                                href="javascript:;" title="Delete Item">
                                                <i class="fa fa-trash"></i>
                                            </a>

                                        </td>
                                    </tr>
                                @endforeach
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
                            <textarea type="text" id="note" name="note" rows="5" class="form-control" placeholder="Note">{{ $editInfo->note }}</textarea>
                            <span class="error text-red text-bold"></span>
                        </div>
                        <button class="btn btn-info" id="getsubmit" type="submit"><i class="fa fa-save"></i>
                            &nbsp;Save</button>
                        {{-- <a href="{{ route('settings.credit.voucher.approve', $editInfo->id) }}"
                            onclick="return confirm(`Are You Sure!`)"class="btn btn-info"><i class="fa fa-save"></i>
                            &nbsp;Approve</a> --}}
                    </div>
                </form>
            </div>
        </div>
        <!-- /.col-->
    </div>



    <script>
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
                    alert('Credit and  Debit Amount Not Same');
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
        })

        $(document).on('click', '.remove_item', function() {
            if (confirm('Are You Sure')) {
                $(this).closest('tr').remove();
            }
        })

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
        totalAmount();

        $('body').on('click', '#add_new', function() {
            let account_id = $('#account_id option:selected').val();
            let credit = $('#credit').val();
            let debit = $('#debit').val();
            let payment_invoice = $('#payment_invoice option:selected').val() == undefined ? "" : $(
                '#payment_invoice option:selected').val();

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
       <span>${$('#account_id option:selected').text()}</span>
       <input type="hidden" value="${account_id}" name="account_id[]">
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
            $('#account_id').select2().val(null);
            $('#credit').val('');
            $('#debit').val('');
            $('#showamount').html('');
            $('#payment-options-container').html("");
            totalAmount();
        })

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
                            if (response.bill_by_bill) {
                                // Show payment options
                                $('#payment-options-container').html(`
                            <select class="form-control select2" id="payment_invoice">

                                ${response.payment_invoices.map(invoice => `<option value="${invoice.invoice}">${invoice.invoice} (${invoice.amount})</option>`).join('')}
                            </select>
                        `);
                            } else {
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

        $(document).ready(function() {
            // Function to handle showing/hiding based on the cost center value
            function toggleCostCenterFields(value) {
                $('#project_div').hide();
                $('#branch_div').hide();

                // Clear selections when switching
                // $('#project_id').val('0').trigger('change');
                // $('#branch_id').val('0').trigger('change');

                if (value === 'project') {
                    $('#project_div').show();
                } else if (value === 'branch') {
                    $('#branch_div').show();
                }
            }

            // Initial check when the page loads
            var initialValue = $('#cost_center').val();
            toggleCostCenterFields(initialValue);

            // Event listener for change
            $(document).on('change', "#cost_center", function() {
                var value = this.value;
                toggleCostCenterFields(value);
            });
        });
    </script>
@endsection
