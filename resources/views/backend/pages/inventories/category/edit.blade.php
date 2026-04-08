@extends('backend.layouts.master')

@section('title')
    Settings - {{ $title }}
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
                        @if (helper::roleAccess('inventorySetup.category.index'))
                            <li class="breadcrumb-item"><a href="{{ route('inventorySetup.category.index') }}">Category
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Category</span></li>
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
                    <h3 class="card-title">Category List</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('inventorySetup.category.create'))
                            <a class="btn btn-default" href="{{ route('inventorySetup.category.create') }}"><i
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
                        action="{{ route('inventorySetup.category.update', $editInfo->id) }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Parent Category * :</label>
                                <select name="parent_id" id="" class="form-control select2">
                                    <option selected  value="0">Root</option>
                                    @foreach ($category as $key => $value)
                                        <option @if ($editInfo->parent_id == $value->id) selected @endif
                                            value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>

                                @error('parent_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Name * :</label>
                                <input type="text" name="name" class="form-control" id="validationCustom01"
                                    placeholder="ID" value="{{ $editInfo->name }}">
                                @error('name')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
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
@endsection
