@extends('backend.layouts.master')
@section('title')
    Hrm - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }
    </style>
    <style>
        .context-menu {
            background: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 160px;
            padding: 5px 0;
        }

        .context-menu .list-group-item {
            padding: 10px 15px;
            cursor: pointer;
            border: none;
        }

        .context-menu .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .context-menu .list-group-item i {
            margin-right: 8px;
            width: 18px;
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Hrm </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('inventorySetup.adjust.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.employee.index') }}">Hrm</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Employee List</span></li>
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
                    <h3 class="card-title">Employee List</h3>
                    <div class="card-tools">

                        <div class="btn-group mb-2" role="group">
                            <button class="btn btn-info filter-btn active" data-status="present">Present</button>
                            <button class="btn btn-secondary filter-btn" data-status="left">Left</button>
                        </div>
                        <span id="buttons" class="ml-2"></span>

                        @if (helper::roleAccess('hrm.employee.create'))
                            <a class="btn btn-default" href="{{ route('hrm.employee.create') }}"><i
                                    class="fas fa-plus"></i>Add
                                New</a>
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
                    <div class="table-responsive">
                        <table id="systemDatatable" class="display table-hover table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 1%; display: none;">ID</th>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Card No</th>
                                    <th>dob</th>
                                    <th>Gender</th>
                                    <th>Personal Phone</th>
                                    <th>Office Phone</th>
                                    <th>Nid</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Present Address</th>
                                    <th>Salary</th>
                                    <th>Overtime</th>
                                    <th>Join Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th style="width: 1%; display: none;">ID</th>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Card No</th>
                                    <th>dob</th>
                                    <th>Gender</th>
                                    <th>Personal Phone</th>
                                    <th>Office Phone</th>
                                    <th>Nid</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Present Address</th>
                                    <th>Salary</th>
                                    <th>Join Date</th>
                                    <th>Overtime</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Custom Right Click Context Menu with Permission -->
    <div id="contextMenu" class="context-menu" style="display: none; position: absolute; z-index: 10000;">
        <ul class="list-group">

            @if (helper::roleAccess('hrm.employee.show'))
                <li class="list-group-item context-item" data-action="view">
                    <i class="fas fa-eye"></i> View
                </li>
            @endif

            @if (helper::roleAccess('hrm.employee.edit'))
                <li class="list-group-item context-item" data-action="edit">
                    <i class="fas fa-edit"></i> Edit
                </li>
            @endif

            @if (helper::roleAccess('hrm.employee.destroy'))
                <li class="list-group-item context-item text-danger" data-action="delete">
                    <i class="fas fa-trash"></i> Delete
                </li>
            @endif

        </ul>
    </div>
@endsection
@section('scripts')
    @include('backend.pages.hrm.employee.script')

    <script>
        $(document).ready(function() {

            let currentRowData = {};

            // Right Click on row
            $('#systemDatatable tbody').on('contextmenu', 'tr', function(e) {
                e.preventDefault();

                let row = $('#systemDatatable').DataTable().row(this).data();
                if (!row) return;
                currentRowData = row;

                // Context Menu Position
                $('#contextMenu').css({
                    display: 'block',
                    left: e.pageX + 'px',
                    top: e.pageY + 'px'
                });
            });

            // Context Menu Item Click
            // Context Menu Item Click
            $('.context-item').on('click', function() {
                let action = $(this).data('action');


                let employeeId = currentRowData.id || currentRowData[0];

                if (!employeeId) {
                    toastr.error('Employee ID not found! Please refresh the page.');
                    $('#contextMenu').hide();
                    return;
                }

                if (action === 'view') {
                    window.location.href = "{{ route('hrm.employee.show', '') }}/" + employeeId;
                } else if (action === 'edit') {
                    window.location.href = "{{ route('hrm.employee.edit', '') }}/" + employeeId;
                } else if (action === 'delete') {
                    if (confirm(
                            'Are you sure you want to delete this employee? This action cannot be undone.'
                        )) {
                        $.ajax({
                            url: "{{ route('hrm.employee.destroy', '') }}/" + employeeId,
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function() {
                                $('#systemDatatable').DataTable().ajax.reload();
                                toastr.success('Employee deleted successfully');
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to delete employee');
                            }
                        });
                    }
                }

                $('#contextMenu').hide();
            });
            // Close menu when clicking anywhere
            $(document).on('click', function() {
                $('#contextMenu').hide();
            });

            $('#contextMenu').on('click', function(e) {
                e.stopImmediatePropagation();
            });
        });
    </script>
@endsection
