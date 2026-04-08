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
                        @if (helper::roleAccess('settings.openingbalance.index'))
                            <li class="breadcrumb-item"><a href="{{ route('settings.openingbalance.index') }}">Opening
                                    Balance List</a></li>
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
                    <h3 class="card-title">Add New Opening Balance</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('settings.openingbalance.index'))
                            <a class="btn btn-default" href="{{ route('settings.openingbalance.index') }}"><i
                                    class="fa fa-list"></i>
                                Opening List
                            </a>
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
                    <form class="needs-validation" method="POST" action="{{ route('settings.openingbalance.store') }}"
                        novalidate id="openingBalanceForm">
                        @csrf
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Account Name</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <x-opening-account-balance :accounts="$accounts" />
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th class="totalDebit"></th>
                                    <th class="totalCredit"></th>
                                </tr>
                            </tfoot>
                        </table>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
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
            // Store original values
            let originalValues = {};

            // Store original values for all inputs
            $('.debit, .credit').each(function() {
                let fieldName = $(this).attr('name');
                originalValues[fieldName] = $(this).val();
            });

            // Track changes and update totals
            function updateTotals() {
                let debit = 0;
                let credit = 0;

                // Loop through all elements with class "debit"
                $(".debit").each(function() {
                    const value = parseFloat($(this).val()) || 0;
                    debit += value;
                });

                // Loop through all elements with class "credit"
                $(".credit").each(function() {
                    const value = parseFloat($(this).val()) || 0;
                    credit += value;
                });

                // Set the total values
                $('.totalDebit').text(debit.toFixed(2));
                $('.totalCredit').text(credit.toFixed(2));
            }

            // Update totals on page load
            updateTotals();

            // Update totals when input values change
            $(document).on('input', '.debit, .credit', function() {
                updateTotals();
            });

            // Handle form submission
            $('#openingBalanceForm').on('submit', function(e) {
                e.preventDefault();

                let changedFields = {};
                let hasChanges = false;

                // Check for changes
                $('.debit, .credit').each(function() {
                    let fieldName = $(this).attr('name');
                    let currentValue = $(this).val();
                    let originalValue = originalValues[fieldName];

                    // If value has changed, include it in the submission
                    if (currentValue !== originalValue) {
                        changedFields[fieldName] = currentValue;
                        hasChanges = true;
                    }
                });

                if (!hasChanges) {
                    alert('No changes detected to save.');
                    return false;
                }

                // Create a temporary form with only changed fields
                let tempForm = $('<form>', {
                    'method': 'POST',
                    'action': $(this).attr('action')
                }).appendTo('body');

                // Add CSRF token
                tempForm.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('input[name="_token"]').val()
                }));

                // Add only changed fields
                $.each(changedFields, function(fieldName, value) {
                    tempForm.append($('<input>', {
                        'type': 'hidden',
                        'name': fieldName,
                        'value': value
                    }));
                });

                // Submit the temporary form
                tempForm.submit();
            });
        });

        function getAccountList(branchId) {
            $.ajax({
                url: "/admin/getAllAccountHead/",
                method: "GET",
                data: {
                    "_token": "{{ csrf_token() }}",
                    branchId: branchId
                },
                success: function(data) {
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
