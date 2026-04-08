@extends('backend.layouts.master')

@section('title')
Settings - {{$title}}
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
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    @if(helper::roleAccess('settings.general_setup.index'))
                    <li class="breadcrumb-item"><a href="{{ route('settings.general_setup.index') }}">General Setup List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New General Setup</span></li>
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
                <h3 class="card-title">Add New General Setup</h3>
                <div class="card-tools">
                @if(helper::roleAccess('settings.general_setup.index'))
                    <a class="btn btn-default" href="{{ route('settings.general_setup.index') }}"><i class="fa fa-list"></i>
                    @endif
                        General Setup List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('settings.general_setup.store') }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Currency * :</label>
                            <input type="text" name="currency" class="form-control" id="validationCustom01"
                                placeholder="Currency " value="{{ old('currency') }}">
                            @error('currency')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom02"> Currency Position * :</label>
                            <input type="text" name="currency_position" class="form-control" id="validationCustom02"
                                placeholder="Position" value="{{ old('currency_position') }}" required>
                            @error('currency_position')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Language * :</label>
                            <input type="text" name="language" class="form-control" id="validationCustom01"
                                placeholder="Language" value="{{ old('language') }}" required>
                            @error('language')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Timezone * :</label>
                            <input type="text" name="timezone" class="form-control" id="validationCustom01"
                                placeholder="Timezone" value="{{ old('timezone') }}" required>
                            @error('timezone')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Dateformat * :</label>
                            <input type="date" name="dateformat" class="form-control" id="validationCustom01"
                                placeholder="Dateformat" value="{{ old('dateformat') }}" required>
                            @error('dateformat')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Decimal Separate * :</label>
                            <input type="text" name="decimal_separate" class="form-control" id="validationCustom01"
                                placeholder="Decimal Separate" value="{{ old('decimal_separate') }}" required>
                            @error('decimal_separate')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Thousand Separate * :</label>
                            <input type="text" name="thousand_separate" class="form-control" id="validationCustom01"
                                placeholder="Thousand Separate" value="{{ old('thousand_separate') }}" required>
                            @error('thousand_separate')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
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










@endsection