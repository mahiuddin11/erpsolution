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
                    @if(helper::roleAccess('settings.fiscal_year.index'))
                    <li class="breadcrumb-item"><a href="{{route('settings.fiscal_year.index')}}">Fiscal Year List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Edit Fiscal Year</span></li>
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
                <h3 class="card-title">Fiscal Year List</h3>
                <div class="card-tools">
                @if(helper::roleAccess('settings.fiscal_year.create'))
                    <a class="btn btn-default" href="{{ route('settings.fiscal_year.create') }}"><i class="fas fa-plus"></i>
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
                    action="{{ route('settings.fiscal_year.update',$editInfo->id) }}" novalidate>
                    @csrf
                    <div class="form-row">
                   
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Date * :</label>
                            <input type="date" name="date" class="form-control" id="validationCustom01"
                                placeholder="Date" value="{{ $editInfo->date }}">
                            @error('date')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom02"> Fiscal Year * :</label>
                            <input type="date" name="fiscal_year" class="form-control" id="validationCustom02"
                                placeholder="Fiscal Year" value="{{ $editInfo->fiscal_year  }}" required>
                            @error('fiscal_year')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                    <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Branch Name * :</label>
                            <select name="branch_id" id="" class="form-control select2">
                                @foreach($branch as $key => $value)
                                <option @if($editInfo->branch_id == $value->id) selected @endif value="{{$value->id}}">{{$value->name}}</option>
                                @endforeach
                            </select>
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