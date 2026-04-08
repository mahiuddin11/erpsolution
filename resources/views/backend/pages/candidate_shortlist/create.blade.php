@extends('backend.layouts.master')

@section('title')
    settings - {{ $title }}
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Candidate Shortlist </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('candidate.index'))
                            <li class="breadcrumb-item"><a href="{{ route('candidate.index') }}">Candidate Shortlist List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Shortlist </span></li>
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
                    <h3 class="card-title">Add New Shortlist</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('candidate.shortlist.index'))
                            <a class="btn btn-default" href="{{ route('candidate.shortlist.index') }}"><i
                                    class="fa fa-list"></i>
                                Shortlist List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('candidate.shortlist.store') }}"
                        novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Candidate Name * :</label>
                                <select name="candidateinfo_id" class="form-control">
                                    @foreach ($allCandidates as $value)
                                        <option value="{{ $value->id }}">{{ $value->first_name }}</option>
                                    @endforeach
                                </select>
                                @error('candidateinfo_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Job Position * :</label>
                                <input type="text" name="position" class="form-control" id="validationCustom01"
                                    placeholder="Last Name" value="{{ old('position') }}">
                                @error('position')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Interview Date * :</label>
                                <input type="date" name="interview_date" class="form-control" id="validationCustom01"
                                    placeholder="Last Name" value="{{ old('interview_date') }}">
                                @error('interview_date')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Shortlist Date * :</label>
                                <input type="date" name="date" class="form-control" id="validationCustom01"
                                    placeholder="Last Name" value="{{ old('date') }}">
                                @error('date')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Candidate Name * :</label>
                                <select name="candidateinfo_id" class="form-control">
                                    @foreach ($addList as $value)
                                        <option value="{{ $value->id }}">{{ $value->candiateInfo->shortlist }}</option>
                                    @endforeach
                                </select>
                                @error('candidateinfo_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div> --}}

                        </div>
                        <button class="btn btn-info" type="submit"><i class="fa fa-save"></i> &nbsp;Save</button>
                </div>



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
