{{--@extends("attends.layout")
@section('content')
    <div class="row" style="margin-top: 20px">
        <div style="col-lg-12 margin-tb">
            <h4>Emplyee Table</h4>
        </div>
        <div class="pull-right" style="margin-left:100px">
            <a class="btn btn-success" href="{{ route('attends.create')}}">
                Add New Emplyee
            </a>
        </div>
    </div>
    <br>
    @if($message=Session::get('success'))
        <div class="alert alert-success">
            <p>{{$message}}</p>
        </div>
    @endif

    <table class="table table-bordered" style="margin-top:20px">
        <tr>
            <th>I/D</th>
            <th>Emplyee_Id</th>
            <th>Date</th>
            <th>Sign_In</th>
            <th>Sign_Out</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        @foreach ($attends as $attend )
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $attend->emplyee_id }}</td>
                <td>{{ $attend->date }}</td>
                <td>{{ $attend->sign_in }}</td>
                <td>{{ $attend->sign_out }}</td>
                <td>{{ $attend->status }}</td>
                <td>
                    <form action="{{route('attends.destroy',$attend->id)}}" method="POST">
                        <a class="btn btn-info" href="{{route('attends.show',$attend->id)}}">Details</a>
                        <a class="btn btn-primary" href="{{route('attends.edit',$attend->id)}}">Edit</a>
                        @csrf
                        @method('DELETE')  
                        <button type="submit" class="btn btn-danger">Delete</button>  
                    </form>
                </td
            </tr>
        @endforeach
        {!! $attends->links() !!}
    </table>
@endsection --}}

@extends('attends.layouts')
@section('content')
{{--Hrm - {{$title}}--}}
@endsection

@section('styles')
<style>
    .bootstrap-switch-large {
        width: 200px;
    }
</style>
@endsection

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    HRM </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('home') }}">Dashboard</a></li>
                    @if(helper::roleAccess('inventorySetup.adjust.index'))
                    <li class="breadcrumb-item"><a href="{{route('hrm.employee.index') }}">HRM</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Employee List</span></li>
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
                <h3 class="card-title">Employee List</h3>
                <div class="card-tools">
                    @if(helper::roleAccess('hrm.employee.create'))
                    <a class="btn btn-default" href="{{ route('hrm.employee.create') }}"><i class="fas fa-plus"></i>Add
                        New Emplyee</a>
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
                <div class="table-responsive">
                    <table id="systemDatatable" class="display table-hover table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>S/L</th>
                                <th>Emplyee_ID</th>
                                <th>Date</th>
                                <th>Sign_In</th>
                                <th>Sign_Out</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        
                         
                    </table>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">

            </div>
        </div>
    </div>
    <!-- /.col-->
</div>
@endsection
@section('scripts')
{{--@include('backend.pages.hrm.employee.script')--}}
@endsection