@extends('backend.layouts.master')

@section('title')
Production - {{ $title }}
@endsection


@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    Production </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('production.production.index'))
                    <li class="breadcrumb-item"><a href="{{ route('production.production.index') }}">Production List</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Edit Production</span></li>
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
                <h3 class="card-title">Production List</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('production.production.create'))
                    <a class="btn btn-default" href="{{ route('production.production.create') }}"><i
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
                    action="{{ route('production.production.update', $editInfo->id) }}" novalidate>
                    @csrf
                    <div class="form-row">

                        <div class="col-md-4 mb-3">
                            <label>Date * :</label>
                            <input type="date" value="{{ $editInfo->date }}" name="date" class="form-control"
                                placeholder="Date">
                            @error('date')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Branch * :</label>
                            <select class="form-control select2" name="branch_id">
                                <option selected disabled value="">--Select Branch--</option>
                                @foreach ($branch as $key => $value)
                                <option {{ $editInfo->branch_id == $value->id ? 'selected' : '' }}
                                    value="{{ $value->id }}">
                                    {{ $value->branchCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="">Product Type *</label>
                            <select name="product_type" class="form-control " onchange="productTypeViews(this.value)">
                                <option {{ $editInfo->product_type == "Existing" ? 'selected' : '' }} value="Existing">
                                    Existing</option>
                                <option {{ $editInfo->product_type == "New" ? 'selected' : '' }} value="New">New
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 showHideGame1">
                            <label for="validationCustom01">Product Name * :</label>
                            <select class="form-control select2" name="product_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach($products as $key => $value)
                                <option {{ $editInfo->product_id == $value->id ? 'selected' : '' }}
                                    value="{{$value->id}}">{{ $value->productCode.' - '.$value->name}}</option>
                                @endforeach
                            </select>
                            @error('product_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01">Conversion * :</label>
                            <select class="form-control select2" name="conversion_id">
                                <option selected disabled value="">--Select--</option>
                                @foreach($conversion as $key => $value)
                                <option {{ $editInfo->conversion_id == $value->id ? 'selected' : '' }}
                                    value="{{$value->id}}">{{$value->title.' - '.$value->rate  }}</option>
                                @endforeach
                            </select>
                            @error('conversion_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3 showHideGame">
                            <label for="validationCustom01">Name * :</label>
                            <input value="{{ $editInfo->name }}" type="text" name="name" class="form-control"
                                id="validationCustom01" placeholder="Name" value="{{ old('name') }}">
                            @error('name')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3 showHideGame">
                            <label for="validationCustom01">Category Name * :</label>
                            <select class="form-control select2" name="category_id">
                                <option selected disabled value="">--Select--</option>

                                @foreach($categorys as $key => $value)
                                <option {{ $editInfo->category_id == $value->id ? 'selected' : '' }}
                                    value="{{$value->id}}">{{$value->name}}</option>
                                @endforeach
                            </select>
                            @error('name')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3 showHideGame">
                            <label for="validationCustom01">Brand Name * :</label>
                            <select class="form-control select2" name="brand_id">
                                <option selected disabled value="">--Select--</option>

                                @foreach($brands as $key => $value)
                                <option {{ $editInfo->brand_id == $value->id ? 'selected' : '' }}
                                    value="{{$value->id}}">{{$value->name}}</option>
                                @endforeach
                            </select>
                            @error('name')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3 showHideGame">
                            <label for="validationCustom01">Unit Name * :</label>
                            <select class="form-control select2" name="unit_id">
                                <option selected disabled value="">--Select--</option>

                                @foreach($units as $key => $value)
                                <option {{ $editInfo->unit_id == $value->id ? 'selected' : '' }} value="{{$value->id}}">
                                    {{$value->name}}</option>
                                @endforeach
                            </select>
                            @error('name')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3 showHideGame">
                            <label for="validationCustom01">Purchases Price * :</label>
                            <input type="text" name="purchases_price" class="form-control" id="validationCustom01"
                                placeholder="Price" value="{{ $editInfo->purchases_price }}">
                            @error('purchases_price')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3 showHideGame">
                            <label for="validationCustom01">Sale Price * :</label>
                            <input type="text" name="sale_price" class="form-control" id="validationCustom01"
                                placeholder="Price" value="{{ $editInfo->sale_price }}">
                            @error('sale_price')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <button class="btn btn-info" type="submit"><i class="fa fa-save"></i>&nbsp;Update</button>

                </form>


            </div>
            <!-- /.card-body -->

        </div>
    </div>
    <!-- /.col-->
</div>
<script>
    $( document ).ready(function() {
        $(".showHideGame").hide();
    });

    function productTypeViews(prodcut_type){
       if(prodcut_type == "Existing"){
            $(".showHideGame").hide(500);
            $(".showHideGame1").show(500);
       }else{
            $(".showHideGame").show(500);
            $(".showHideGame1").hide(500);
       }
    }
</script>
@endsection