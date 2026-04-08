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
                    @if(helper::roleAccess('settings.smpt.index'))
                    <li class="breadcrumb-item"><a href="{{ route('settings.smpt.index') }}">SMPT List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New SMPT</span></li>
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
                <h3 class="card-title">{{$title}}</h3>
                <div class="card-tools">
                @if(helper::roleAccess('settings.smpt.index'))
                    <a class="btn btn-default" href="{{ route('settings.smpt.index') }}"><i class="fa fa-list"></i>
                        SMPT List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('settings.smpt.store') }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">SMPT Protocol * :</label>
                            <input type="text" name="protocol" class="form-control" id="validationCustom01"
                                placeholder="SMPT Protocol" value="{{ old('protocol') }}">
                            @error('protocol')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom02"> Host * :</label>
                            <input type="text" name="smtp_host" class="form-control" id="validationCustom02"
                                placeholder="Host" value="{{ old('smtp_host') }}" required>
                            @error('smtp_host')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Port * :</label>
                            <input type="text" name="smtp_port" class="form-control" id="validationCustom01"
                                placeholder="Port" value="{{ old('smtp_port') }}" required>
                            @error('smtp_port')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom02">Mail* :</label>
                            <input name="sender_mail" class="form-control" id="validationCustom02" placeholder="Mail"
                                value="{{ old('sender_mail') }}" required>
                            @error('sender_mail')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Password * :</label>
                            <input type="text" name="password" class="form-control" id="validationCustom01"
                                placeholder="Password" value="{{ old('password') }}" required>
                            @error('password')
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