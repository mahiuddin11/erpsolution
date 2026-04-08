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
                        @if (helper::roleAccess('settings.commissionRules.index'))
                            <li class="breadcrumb-item"><a href="{{ route('settings.commissionRules.index') }}">Commission Rules List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Commission Rules</span></li>
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
                    <h3 class="card-title">Add New Commission Rules</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('settings.commissionRules.index'))
                            <a class="btn btn-default" href="{{ route('settings.commissionRules.index') }}"><i
                                    class="fa fa-list"></i> Commission Rules List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('settings.commissionRules.store') }}" novalidate>
                        @csrf
                
                        <!-- Salesperson Dropdown -->
                        <label for="employee_id">Salesperson:</label>
                        <select name="employee_id" id="employee_id" class="form-control select2" required>
                            <option value="">-- Select Salesperson --</option>
                            @foreach($salespersons as $salesperson)
                                <option value="{{ $salesperson->id }}">{{ $salesperson->name }}</option>
                            @endforeach
                        </select>
                        @error('employee_id') <small class="text-danger">{{ $message }}</small> @enderror
                
                        <!-- Commission Type Dropdown -->
                        <label for="commission_type">Type:</label>
                        <select name="commission_type" id="commission_type" class="form-control" required>
                            <option value="fixed">Fixed</option>
                            <option value="tiered">Tiered</option>
                            <option value="product_based">Product Based</option>
                        </select>
                        @error('commission_type') <small class="text-danger">{{ $message }}</small> @enderror
                
                        <!-- Fixed Percentage (Only for Fixed Type) -->
                        <div id="fixed_percentage_wrapper">
                            <label for="fixed_percentage">Fixed Percentage:</label>
                            <input type="number" step="0.01" name="fixed_percentage" id="fixed_percentage" class="form-control">
                            @error('fixed_percentage') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                
                        <!-- Min & Max Amount (Only for Tiered Type) -->
                        <div id="tiered_wrapper" style="display: none;">
                            <label for="min_amount">Min Amount:</label>
                            <input type="number" step="0.01" name="min_amount" id="min_amount" class="form-control">
                            @error('min_amount') <small class="text-danger">{{ $message }}</small> @enderror
                
                            <label for="max_amount">Max Amount:</label>
                            <input type="number" step="0.01" name="max_amount" id="max_amount" class="form-control">
                            @error('max_amount') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                
                        <!-- Percentage (For Tiered & Product-Based Types) -->
                        <div id="percentage_wrapper" style="display: none;">
                            <label for="percentage">Percentage:</label>
                            <input type="number" step="0.01" name="percentage" id="percentage" class="form-control">
                            @error('percentage') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary mt-3">Save</button>
                    </form>
                </div>
                <!-- /.card-body -->
              
            </div>
        </div>
        <!-- /.col-->
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const commissionType = document.getElementById('commission_type');
            const fixedPercentageWrapper = document.getElementById('fixed_percentage_wrapper');
            const tieredWrapper = document.getElementById('tiered_wrapper');
            const percentageWrapper = document.getElementById('percentage_wrapper');
    
            function toggleFields() {
                const selectedType = commissionType.value;
                
                if (selectedType === 'fixed') {
                    fixedPercentageWrapper.style.display = 'block';
                    tieredWrapper.style.display = 'none';
                    percentageWrapper.style.display = 'none';
                } 
                else if (selectedType === 'tiered') {
                    fixedPercentageWrapper.style.display = 'none';
                    tieredWrapper.style.display = 'block';
                    percentageWrapper.style.display = 'block';
                } 
                else if (selectedType === 'product_based') {
                    fixedPercentageWrapper.style.display = 'none';
                    tieredWrapper.style.display = 'none';
                    percentageWrapper.style.display = 'block';
                }
            }
    
            commissionType.addEventListener('change', toggleFields);
            toggleFields(); // Call on load to handle default selection
        });
    </script>
@endsection
