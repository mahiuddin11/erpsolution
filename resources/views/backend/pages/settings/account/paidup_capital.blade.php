@extends('backend.layouts.master')

@section('title')
Account - {{$title}}
@endsection
@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"> Paidup Capital </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><span>Paidup Capital</span></li>
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
                <h3 class="card-title">Paidup Capital</h3>
                {{-- <div class="card-tools">
                @if(helper::roleAccess('settings.category.index'))
                    <a class="btn btn-default" href="{{ route('settings.category.index') }}"><i class="fa fa-list"></i>
                    Category List</a>
                        @endif
                    <span id="buttons"></span>

                    <a class="btn btn-tool btn-default" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </a>
                    <a class="btn btn-tool btn-default" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </a>
                </div> --}}
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <form class="needs-validation" method="POST" action="{{ route('paidup.capital.store') }} " novalidate enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <div class="mb-3">
                                <label for="name" class="form-label">Price</label>
                                <input type="number" class="form-control" value="{{  $paidcapital->price ?? "" }}" name="price" id="name" >
                              </div>
     
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="mb-3">
                                <label for="name" class="form-label">Share</label>
                                <input type="text" class="form-control" value="{{  $paidcapital->share ?? "" }}" name="share" id="name" >
                              </div>
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