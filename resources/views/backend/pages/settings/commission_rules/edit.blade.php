@extends('backend.layouts.master')

@section('title')
Settings - {{$title}}
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
                    @if(helper::roleAccess('settings.commissionRules.index'))
                    <li class="breadcrumb-item"><a href="{{route('settings.commissionRules.index')}}">Commission Rules List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Edit Commission Rules</span></li>
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
                <h3 class="card-title">Commission Rules List</h3>
                <div class="card-tools">
                    @if(helper::roleAccess('settings.commissionRules.create'))
                    <a class="btn btn-default" href="{{ route('settings.commissionRules.create') }}"><i class="fas fa-plus"></i>
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
                    action="{{ route('settings.commissionRules.update',$editInfo->id) }}" novalidate>
                    @csrf
            
                    <!-- Salesperson Dropdown -->
                    <label for="employee_id">Salesperson:</label>
                    <select name="employee_id" class="form-control select2" required>
                        @foreach($salespersons as $employee)
                            <option value="{{ $employee->id }}" {{ $editInfo->employee_id == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id') <small class="text-danger">{{ $message }}</small> @enderror
            
                    <!-- Commission Type Dropdown -->
                    <label for="commission_type">Type:</label>
                    <select name="commission_type" id="commission_type" class="form-control" required>
                        <option value="fixed" {{ $editInfo->commission_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                        <option value="tiered" {{ $editInfo->commission_type == 'tiered' ? 'selected' : '' }}>Tiered</option>
                        <option value="product_based" {{ $editInfo->commission_type == 'product_based' ? 'selected' : '' }}>Product Based</option>
                    </select>
                    @error('commission_type') <small class="text-danger">{{ $message }}</small> @enderror
            
                    <!-- Fixed Percentage -->
                    <div id="fixed_percentage_wrapper">
                        <label for="fixed_percentage">Fixed Percentage:</label>
                        <input type="number" step="0.01" name="fixed_percentage" id="fixed_percentage" class="form-control" value="{{ $editInfo->fixed_percentage }}">
                        @error('fixed_percentage') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
            
                    <!-- Tiered Fields -->
                    <div id="tiered_wrapper" style="display: none;">
                        <label for="min_amount">Min Amount:</label>
                        <input type="number" step="0.01" name="min_amount" id="min_amount" class="form-control" value="{{ $editInfo->min_amount }}">
                        @error('min_amount') <small class="text-danger">{{ $message }}</small> @enderror
            
                        <label for="max_amount">Max Amount:</label>
                        <input type="number" step="0.01" name="max_amount" id="max_amount" class="form-control" value="{{ $editInfo->max_amount }}">
                        @error('max_amount') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
            
                    <!-- Percentage Field -->
                    <div id="percentage_wrapper" style="display: none;">
                        <label for="percentage">Percentage:</label>
                        <input type="number" step="0.01" name="percentage" id="percentage" class="form-control" value="{{ $editInfo->percentage }}">
                        @error('percentage') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
            
                    <button type="submit" class="btn btn-primary mt-3">Update</button>
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
        toggleFields(); // Call on page load to set initial state
    });
</script>
@endsection