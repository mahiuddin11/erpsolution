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
                    @if(helper::roleAccess('settings.store.index'))
                    <li class="breadcrumb-item"><a href="{{ route('settings.store.index') }}">Store List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Store</span></li>
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
                <h3 class="card-title">Add New Store</h3>
                <div class="card-tools">
                @if(helper::roleAccess('settings.store.index'))
                    <a class="btn btn-default" href="{{ route('settings.store.index') }}"><i class="fa fa-list"></i>
                    @endif
                        Store List</a>
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
                <form class="needs-validation" method="POST" action="{{ route('settings.store.store') }}" novalidate>
                    @csrf
                    <div class="form-row">
                    <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Branch Name * :</label>
                            <select name="branch_id" id="" class="form-control select2">
                                @foreach($branch as $key => $value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Name * :</label>
                            <input type="text" name="name" class="form-control" id="validationCustom01"
                                placeholder="store Name" value="{{ old('name') }}">
                            @error('name')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        
                    </div>
                    <div class="form-row">

                    <div class="col-md-6 mb-3">
                            <label for="validationCustom02"> E-mail * :</label>
                            <input type="text" name="email" class="form-control" id="validationCustom02"
                                placeholder="E-mail" value="{{ old('email') }}" required>
                            @error('email')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom01">Phone * :</label>
                            <input type="text" name="phone" class="form-control" id="validationCustom01"
                                placeholder="Phone" value="{{ old('phone') }}" required>
                            @error('phone')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        
                    </div>

                    <div class="form-row">
                    <div class="col-md-6 mb-3">
                            <label for="validationCustom02">Address* :</label>
                            <input name="address" class="form-control" id="validationCustom02" placeholder="Address"
                                value="{{ old('address') }}" required>
                            @error('address')
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