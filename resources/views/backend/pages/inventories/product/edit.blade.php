@extends('backend.layouts.master')

@section('title')
    Product - {{ $title }}
@endsection


@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Inventory </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('inventorySetup.product.index'))
                            <li class="breadcrumb-item"><a href="{{ route('inventorySetup.product.index') }}">Product
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Product</span></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    @php
        $editInfo->attributeSkip = true;
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Product List</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('inventorySetup.product.create'))
                            <a class="btn btn-default" href="{{ route('inventorySetup.product.create') }}"><i
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
                <div class="card-body">

                    <form class="needs-validation" method="POST"
                        action="{{ route('inventorySetup.product.update', $editInfo->id) }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Name * :</label>
                                <input type="text" name="name" class="form-control" id="validationCustom01"
                                    placeholder="Name" value="{{ $editInfo->name }}">
                                @error('name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Category Name * :
                                    <button type="button" class="btn btn-sm btn-primary ml-2" data-toggle="modal"
                                    data-target="#addCategoryModal">
                                    +
                                </button>
                                </label>
                                <select name="category_id" id="" class="form-control select2">
                                    @foreach ($categorys as $key => $value)
                                        <option @if ($editInfo->category_id == $value->id) selected @endif
                                            value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>

                                @error('name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
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
                                <select name="brand_id" id="" class="form-control select2">
                                    @foreach ($brands as $key => $value)
                                        <option @if ($editInfo->brand_id == $value->id) selected @endif
                                            value="{{ $value->id }}">{{ $value->name }}</option>
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
                                <select name="unit_id" id="" class="form-control select2">
                                    @foreach ($units as $key => $value)
                                        <option @if ($editInfo->unit_id == $value->id) selected @endif
                                            value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>

                                @error('name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Purchases Price * :</label>
                                <input type="number" class="form-control" value="{{ $editInfo->purchases_price }}"
                                    name="purchases_price_single">
                                @error('purchases_price_single')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Sale Price * :</label>
                                <input type="number" class="form-control" value="{{ $editInfo->sale_price }}"
                                    name="sale_price_single">
                                @error('sale_price_single')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Box:</label>
                                <input type="number" class="form-control" name="box" value="{{ $editInfo->box }}">
                                @error('box')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-12 text-right">
                                <button class="btn btn-info addProduct" type="button"><i class="fas fa-plus"></i> Add Sub
                                    Product</button>
                            </div>
                            <div class="col-md-12 mb-3">

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Purchases Price</th>
                                            <th scope="col">Sale Price </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($editInfo->subproduct as $value)
                                            @php
                                                $value->attributeSkip = true;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <input type="hidden" class="form-control" value="{{ $value->id }}"
                                                        name="product_id_old[]">
                                                    <input type="text" class="form-control" value="{{ $value->name }}"
                                                        name="product_name_old[]">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control"
                                                        value="{{ $value->purchases_price }}" name="purchases_price_old[]">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control"
                                                        value="{{ $value->sale_price }}" name="sale_price_old[]">
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <button class="btn btn-info" type="submit"><i class="fa fa-save"></i>&nbsp;Update</button>
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
