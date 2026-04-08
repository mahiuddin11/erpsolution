@extends('backend.layouts.master')

@section('title')
    settings - {{ $title }}
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Candidate Selection </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('candidate.selection.index'))
                            <li class="breadcrumb-item"><a href="{{ route('candidate.selection.index') }}">Candidate
                                    Selection List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Selection </span></li>
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
                    <h3 class="card-title">Add New Selection</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('candidate.selection.index'))
                            <a class="btn btn-default" href="{{ route('candidate.selection.index') }}"><i
                                    class="fa fa-list"></i>
                                Selection List</a>
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
                    <form class="needs-validation" method="POST" action="{{ route('candidate.selection.store') }}"
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
                                <label for="validationCustom01">Job Terms * :</label>
                                <textarea name="terms" class="form-control">

                               </textarea>
                                @error('terms')
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
