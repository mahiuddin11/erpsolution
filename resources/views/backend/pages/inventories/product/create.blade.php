@extends('backend.layouts.master')

@section('title')
    Product - {{ $title }}
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Product </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('inventorySetup.product.index'))
                            <li class="breadcrumb-item"><a href="{{ route('inventorySetup.product.index') }}">Product
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Product</span></li>
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
                    <h3 class="card-title">Add New Product</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('inventorySetup.product.index'))
                            <a class="btn btn-default" href="{{ route('inventorySetup.product.index') }}"><i
                                    class="fa fa-list"></i>
                                Product List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('inventorySetup.product.store') }}"
                        novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <span class="bg-green" style="padding: 5px; font-weight : bold"
                                    for="validationCustom01">Product Code * : {{ $productCode }}</span>
                                <input type="hidden" name="productCode" class="form-control" id=""
                                    value="{{ $productCode }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Name * :</label>
                                <input type="text" name="name" class="form-control" id="validationCustom01"
                                    placeholder="Name" value="{{ old('name') }}">
                                @error('name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">
                                    Category Name * :
                                    <button type="button" class="btn btn-sm btn-primary ml-2" data-toggle="modal"
                                        data-target="#addCategoryModal">
                                        +
                                    </button>
                                </label>
                                <select class="form-control select2" name="category_id">
                                    <option selected disabled value="">--Select--</option>
                                    @foreach ($categorys as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                                @error('name')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Brand Name * :

                                    <button type="button" class="btn btn-sm btn-primary ml-2" data-toggle="modal"
                                        data-target="#addBrandModal">
                                        +
                                    </button>
                                </label>
                                <select class="form-control select2" name="brand_id">
                                    <option selected disabled value="">--Select--</option>
                                    <option value="0">Brand</option>
                                    @foreach ($brands as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                                @error('name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Unit Name * :
                                    <button type="button" class="btn btn-sm btn-primary ml-2" data-toggle="modal"
                                        data-target="#addUnitModal">
                                        +
                                    </button>
                                </label>
                                <select class="form-control select2" name="unit_id">
                                    <option selected disabled value="">--Select--</option>
                                    <option value="0">Unit</option>
                                    @foreach ($units as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                                @error('name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Purchases Price * :</label>
                                <input type="number" class="form-control" name="purchases_price_single">
                                @error('purchases_price_single')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Sale Price * :</label>
                                <input type="number" class="form-control" name="sale_price_single">
                                @error('sale_price_single')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Box:</label>
                                <input type="number" class="form-control" name="box">
                                @error('box')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-12 mb-3">

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Purchases Price</th>
                                            <th scope="col">Sale Price </th>
                                            <th scope="col">Action </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" name="product_name[]">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" name="purchases_price[]">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" name="sale_price[]">
                                            </td>
                                            <td>
                                                <button class="btn btn-info addProduct" type="button"><i
                                                        class="fas fa-plus"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <button class="btn btn-info" type="submit"><i class="fa fa-save"></i> &nbsp;Save</button>
                    </form>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">

                </div>
            </div>
        </div>
        <!-- /.col-->
    </div>

    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" id="addCategoryForm"
                    action="{{ route('inventorySetup.production.quickCategoryStore') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="categoryName">Name *</label>
                            <input type="text" name="name" class="form-control" id="categoryName"
                                placeholder="Category Name" required>
                        </div>
                        <div class="form-group">
                            <label for="parentCategory">Parent Category *</label>
                            <select class="form-control select2" name="parent_id" id="parentCategory">
                                <option selected value="0">Root</option>
                                @foreach ($category as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBrandModalLabel">Add Brand</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" id="addBrandForm"
                    action="{{ route('inventorySetup.production.quickBrandStore') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="categoryName">Name *</label>
                            <input type="text" name="name" class="form-control" id="categoryName"
                                placeholder="Name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="addUnitModal" tabindex="-1" role="dialog" aria-labelledby="addUnitModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUnitModalLabel">Add Unit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" id="addUnitForm"
                    action="{{ route('inventorySetup.production.quickUnitStore') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="categoryName">Name *</label>
                            <input type="text" name="name" class="form-control" id="categoryName"
                                placeholder="Name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Unit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.addProduct', function() {
                let html = `<tr>
                       <td>
                          <input type="text" class="form-control" value="" name="product_name[]">
                       </td>
                        <td>
                           <input type="number" class="form-control" name="purchases_price[]">
                        </td>
                       <td>
                          <input type="number" class="form-control" name="sale_price[]">
                       </td>
                       <td>
                          <button class="btn btn-danger removeProduct" type="button"><i class="fas fa-minus"></i></button> 
                       </td>
                  </tr>`;
                $('tbody').append(html);
            })

            $(document).on('click', '.removeProduct', function() {
                if (confirm('Are You Sure')) {
                    $(this).closest('tr').remove();
                }
            })

        })

        $(document).ready(function() {
            // Category Create 
            $('#addCategoryForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#addCategoryModal').modal('hide');

                            // Add the new category to the dropdown
                            $('select[name="category_id"]').append(
                                `<option value="${response.category.id}" selected>${response.category.name}</option>`
                            );
                        } else {
                            alert('Error adding category');
                        }
                    },
                    error: function(error) {
                        alert('An error occurred');
                    }
                });
                $("button[type='submit']").prop('disabled', false);
            });

            // Brand Create 
            $('#addBrandForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#addBrandModal').modal('hide');

                            // Add the new brand to the dropdown
                            $('select[name="brand_id"]').append(
                                `<option value="${response.brand.id}" selected>${response.brand.name}</option>`
                            );
                        } else {
                            alert('Error adding brand');
                        }
                    },
                    error: function(error) {
                        alert('An error occurred');
                    }
                });
                $("button[type='submit']").prop('disabled', false);
            });

            // Unit Create 
            $('#addUnitForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#addUnitModal').modal('hide');

                            // Add the new brand to the dropdown
                            $('select[name="unit_id"]').append(
                                `<option value="${response.unit.id}" selected>${response.unit.name}</option>`
                            );
                        } else {
                            alert('Error adding Unit');
                        }
                    },
                    error: function(error) {
                        alert('An error occurred');
                    }
                });
                $("button[type='submit']").prop('disabled', false);
            });
        });
    </script>
@endsection
