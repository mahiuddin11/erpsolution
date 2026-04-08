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
                    @if(helper::roleAccess('settings.currency.index'))
                    <li class="breadcrumb-item"><a href="{{route('settings.currency.index')}}">Currency List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Edit Currency</span></li>
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
                <h3 class="card-title">Currency List</h3>
                <div class="card-tools">
                @if(helper::roleAccess('settings.currency.create'))
                    <a class="btn btn-default" href="{{ route('settings.currency.create') }}"><i class="fas fa-plus"></i>
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
                    action="{{ route('settings.currency.update',$editInfo->id) }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Currency Name * :</label>
                            <input type="text" name="currency_name" class="form-control" id="validationCustom01"
                                placeholder="Currency Name" value="{{ $editInfo->currency_name }}">
                            @error('currency_name')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom02"> Currency Symbol * :</label>
                            <input type="text" name="currency_symbol" class="form-control" id="validationCustom02"
                                placeholder="Symbol" value="{{ $editInfo->currency_symbol  }}" required>
                            @error('currency_symbol')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Exchange Rate * :</label>
                            <input type="text" name="exchange_rate" class="form-control" id="validationCustom01"
                                placeholder="Rate" value="{{ $editInfo->exchange_rate  }}" required>
                            @error('exchange_rate')
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