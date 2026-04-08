@extends('backend.layouts.master')
@section('title')
    Settings - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Assets </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('candidate.shortlist.index'))
                            <li class="breadcrumb-item"><a href="{{ route('candidate.shortlist.index') }}">Candidate
                                    ShortLists</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span> Candidate ShortLists</span></li>
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
                    <h3 class="card-title">CandidateShort List</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('candidate.shortlist.create'))
                            <a class="btn btn-default" href="{{ route('candidate.shortlist.create') }}"><i
                                    class="fas fa-plus"></i>Add
                                New</a>
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
                                    <th>SL</th>
                                    <th>Candidate Name</th>
                                    <th>Job Position</th>
                                    <th>Short List Date</th>
                                    <th>Interview Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>SL</th>
                                    <th>Candidate Name</th>
                                    <th>Job Position</th>
                                    <th>Short List Date</th>
                                    <th>Interview Date</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
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
    @include('backend.pages.candidate_shortlist.script')
@endsection
