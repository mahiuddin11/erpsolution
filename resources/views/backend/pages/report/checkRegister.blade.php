@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ 'Report' ?? 'Cheque Report' }} </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('inventorySetup.productOS.index'))
                            <li class="breadcrumb-item"><a
                                    href="{{ route('inventorySetup.productOS.index') }}">{{ $title ?? 'Cheque Report' }}
                                </a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>{{ $title ?? 'Cheque Report' }}</span></li>
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
                    <h3 class="card-title">{{ $title ?? 'Cheque Report' }}</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('inventorySetup.productOS.create'))
                            <a class="btn btn-default" href="{{ route('inventorySetup.productOS.create') }}"><i
                                    class="fas fa-plus"></i>Add New</a>
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
                <!-- Filter Section -->
                {{-- <div class="card card-default mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Bank Account</label>
                                <select id="bank_account_id" class="form-control select2">
                                    <option value="">-- All Accounts --</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>From Date</label>
                                <input type="date" id="from_date" class="form-control"
                                    value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                            </div>
                            <div class="col-md-2">
                                <label>To Date</label>
                                <input type="date" id="to_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-2">
                                <label>Status</label>
                                <select id="status" class="form-control">
                                    <option value="">-- All --</option>
                                    <option value="Issued">Issued</option>
                                    <option value="Cleared">Cleared</option>
                                    <option value="Bounced">Bounced</option>
                                    <option value="Void">Void</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button id="btnFilter" class="btn btn-primary mr-1">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <button id="btnReset" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="systemDatatable" class="display table-hover table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="50">SL</th>
                                    <th width="100">Date</th>
                                    <th width="120">Cheque No</th>
                                    <th>From Account </th>
                                    <th>To Account </th>
                                    <th> Description</th>
                                    <th class="text-right">Debit</th>
                                    <th class="text-right">Credit</th>
                                    <th width="100">Status</th>
                                    <th width="100">Reference</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- AJAX / DataTable  -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>SL</th>
                                    <th>Date</th>
                                    <th>Cheque No</th>
                                    <th>From Account </th>
                                    <th>To Account </th>
                                    <th> Description</th>
                                    <th class="text-right">Debit</th>
                                    <th class="text-right">Credit</th>
                                    <th>Status</th>
                                    <th>Reference</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">

                </div>
            </div>
        </div>
        <!-- /.col-->
    </div>
@endsection
@section('scripts')
    <script>
        // Bank Account Load via AJAX
        $.get("{{ route('reports.check_register.banks') }}", function(data) {
            $.each(data, function(i, bank) {
                $('#bank_account_id').append(
                    `<option value="${bank.id}">${bank.account_name}</option>`
                );
            });
            if ($('#bank_account_id').hasClass('select2')) {
                $('#bank_account_id').select2();
            }
        });

        // DataTable Init
        let table = $('#systemDatatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('reports.check_register.dataprocess') }}",
                type: "GET",
                data: function(d) {
                    d.bank_account_id = $('#bank_account_id').val();
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d.status = $('#status').val();
                }
            },
            columns: [{
                    data: 'sl',
                    orderable: false,
                    className: 'text-center'
                },
                {
                    data: 'transaction_date',
                    orderable: true,
                    className: 'text-center'
                },
                {
                    data: 'cheque_no',
                    orderable: true,
                    className: 'text-center font-weight-bold'
                },
                {
                    data: 'from_account',
                    orderable: true,
                    className: 'text-center'
                },
                {
                    data: 'to_account',
                    orderable: true,
                    className: 'text-center'
                },
                {
                    data: 'description',
                    orderable: true
                },
                {
                    data: 'debit',
                    orderable: true,
                    className: 'text-right'
                },
                {
                    data: 'credit',
                    orderable: true,
                    className: 'text-right'
                },
                {
                    data: 'status',
                    orderable: true,
                    className: 'text-center'
                },
                {
                    data: 'invoice',
                    orderable: true,
                    className: 'text-center'
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-center text-nowrap'
                },
            ],
            order: [
                [1, 'desc']
            ],
            language: {
                processing: "Loading...",
                search: "Search:",
                lengthMenu: "Show _MENU_ records",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No records available",
            }
        });

        // Filter Button
        $('#btnFilter').on('click', function() {
            table.ajax.reload();
        });

        // Reset Button
        $('#btnReset').on('click', function() {
            $('#bank_account_id').val('').trigger('change');
            $('#from_date').val("{{ date('Y-m-d', strtotime('-30 days')) }}");
            $('#to_date').val("{{ date('Y-m-d') }}");
            $('#status').val('');
            table.ajax.reload();
        });

        // Export Buttons
        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5', 'print']
        }).container().appendTo($('#buttons'));
    </script>
@endsection


{{-- @section('scripts')
    <script type="text/javascript">
        let table = $('#systemDatatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('reports.check_register.dataprocess') }}",
                "dataType": "json",
                "type": "GET",
                "data": function(d) {
                    d.bank_account_id = $('#bank_account_id').val();
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d.status = $('#status').val();
                }
            },
            "columns": [{
                    "data": "sl",
                    "orderable": false,
                    "class": "text-center"
                }, // SL
                {
                    "data": "transaction_date",
                    "orderable": true,
                    "class": "text-center"
                }, // Date
                {
                    "data": "cheque_no",
                    "orderable": true,
                    "class": "text-center font-weight-bold"
                }, // Cheque No

                {
                    "data": "from_account",
                    "orderable": true,
                    "class": "text-center font-weight-bold"
                }, // ← From Account

                {
                    "data": "to_account",
                    "orderable": true,
                    "class": "text-center font-weight-bold"
                },
                {
                    "data": "description",
                    "orderable": true
                }, // Payee / Description
                {
                    "data": "debit",
                    "orderable": true,
                    "class": "text-right"
                }, // Debit
                {
                    "data": "credit",
                    "orderable": true,
                    "class": "text-right"
                }, // Credit

                {
                    "data": "status",
                    "orderable": true,
                    "class": "text-center"
                }, // Status
                {
                    "data": "invoice",
                    "orderable": true,
                    "class": "text-center"
                }, // Reference
                {
                    "data": "action",
                    "orderable": false,
                    "class": "text-center text-nowrap"
                } // Action
            ],

            "order": [
                [1, "desc"]
            ], // Default sort by Date (descending)

            "fnDrawCallback": function() {
                // Bootstrap Switch যদি থাকে
                $("[name='my-checkbox']").bootstrapSwitch({
                    size: "small",
                    onColor: "success",
                    offColor: "danger"
                });
            },

            "language": {
                "processing": "Loading Cheque Register data...",
                "search": "Search:",
                "lengthMenu": "Show _MENU_ records",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No records available",
            }
        });

        // Buttons (Copy, Excel, PDF, Print)
        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5',
                'print'
            ]
        }).container().appendTo($('#buttons'));
    </script>
@endsection --}}
