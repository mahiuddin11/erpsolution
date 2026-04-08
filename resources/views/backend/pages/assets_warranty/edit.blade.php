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
                        Assets List </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('assets.warranty.index'))
                            <li class="breadcrumb-item"><a href="{{ route('assets.warranty.index') }}">Asset
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Edit Assets Warranty</span></li>
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
                    <h3 class="card-title">Asset Warranty List</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('assets.warranty.create'))
                            <a class="btn btn-default" href="{{ route('assets.warranty.create') }}"><i
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
                        action="{{ route('assets.warranty.update', $editInfo->id) }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6 mb-1">
                                <label for="">Asset Name</label>
                                <select class="select2 form-control" name="category_asset_id"
                                    aria-label=".select2-lg example">
                                    <option selected disabled>Select Asset Name</option>
                                    @foreach ($assetList as $value)
                                        <option value="{{ $value->id }}"
                                            {{ $value->id == $editInfo->assetlist_id ? 'selected' : '' }}>
                                            {{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label for="">Start Date<span class="text-danger">*</span></label>
                                <input type="text" value="{{ $editInfo->form_date }}" class="form-control input-rounded"
                                    name="form_date">
                            </div>
                            <div class="col-md-6 mb-1">
                                <label for="">End Date<span class="text-danger">*</span></label>
                                <input type="text" value="{{ $editInfo->to_date }}" class="form-control input-rounded"
                                    name="to_date">
                            </div>

                            <div class="col-md-6 mb-1">
                                <label for="">Type</label>
                                <select class="select2 form-control accounthead" onchange="availablebalance()"
                                    name="account_id">
                                    <option selected disabled>Select Account</option>
                                    <option value="1" @if ($editInfo->type == 'guarantee') {{ 'selected' }} @endif>
                                        Gurantee
                                    </option>
                                    <option value="2" @if ($editInfo->type == 'warranty') {{ 'selected' }} @endif>
                                        Warranty
                                    </option>
                                    <option value="3" @if ($editInfo->type == 'both') {{ 'selected' }} @endif>
                                        Both</option>

                                </select>
                                <span class="text-success account-message"></span>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label for="">Type</label>
                                <textarea name="desc" class="form-control" id="" cols="30" rows="5">
                                    {{ $editInfo->desc }}
                                </textarea>
                                <span class="text-success account-message"></span>
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
