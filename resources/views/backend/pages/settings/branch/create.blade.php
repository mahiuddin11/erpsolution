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
                        @if (helper::roleAccess('settings.branch.index'))
                            <li class="breadcrumb-item"><a href="{{ route('settings.branch.index') }}">Branch List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Branch</span></li>
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
                    <h3 class="card-title">Add New Branch</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('settings.branch.index'))
                            <a class="btn btn-default" href="{{ route('settings.branch.index') }}"><i
                                    class="fa fa-list"></i> Branch List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('settings.branch.store') }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <span class="bg-green" style="padding: 5px; font-weight: bold;"
                                    for="validationCustom01">Branch Code *: {{ $branchCode }}</span>
                                <input type="hidden" name="branchCode" class="form-control" value="{{ $branchCode }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Branch Name *:</label>
                                <input type="text" name="name" class="form-control" id="validationCustom01"
                                    placeholder="Branch Name" value="{{ old('name') }}">
                                @error('name')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom02">E-mail *:</label>
                                <input type="text" name="email" class="form-control" id="validationCustom02"
                                    placeholder="E-mail" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Phone *:</label>
                                <input type="text" name="phone" class="form-control" id="validationCustom01"
                                    placeholder="Phone" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom02">Address *:</label>
                                <input name="address" class="form-control" id="validationCustom02" placeholder="Address"
                                    value="{{ old('address') }}" required>
                                @error('address')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="row  text-right">
                            <div class="col-md-12">
                                <button class="btn btn-info" type="submit"><i class="fa fa-save"></i>
                                    &nbsp;Save</button>
                            </div>

                        </div>
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
        let warehouseIndex = 1; // Initialize warehouse index

        document.getElementById('add-warehouse').addEventListener('click', function() {
            const warehousesDiv = document.getElementById('warehouses');
            const newWarehouse = document.createElement('div');
            newWarehouse.className = 'form-row warehouse-row';
            newWarehouse.innerHTML = `
            <div class="col-md-5 mb-3">
                <label>Warehouse Name:</label>
                <input type="text" name="warehouses[${warehouseIndex}][name]" class="form-control" placeholder="Warehouse Name" required>
            </div>
            <div class="col-md-5 mb-3">
                <label>Warehouse Location:</label>
                <input type="text" name="warehouses[${warehouseIndex}][location]" class="form-control" placeholder="Warehouse Location" required>
            </div>
            <div class="col-md-2 mb-3 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-warehouse"><i class="fa fa-trash"></i></button>
            </div>`;

            warehousesDiv.appendChild(newWarehouse);
            warehouseIndex++;
        });

        document.getElementById('warehouses').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-warehouse')) {
                e.target.closest('.warehouse-row').remove();
            }
        });
    </script>
@endsection
