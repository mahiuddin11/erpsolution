@extends('backend.layouts.master')
@section('title')
    Hrm - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }

        .profile-img {
            max-width: 300px;
            margin: 20px auto 0px;
        }

        .card-front-side {
            max-width: 300px;
            height: 508px;
            margin: 10px auto;
            border: 1px solid #28a745;
            box-shadow: 5px 10px 18px #1111;
            padding: 0px 0px 15px 0px;

        }

        .card-front-side h4 {
            background: #28a745;
            -webkit-print-color-adjust: exact;
            padding: 10px 5px;
            text-align: center;
            font-size: 20px;
            color: #fff;
        }

        .id-card-img {
            text-align: center;
        }

        .id-card-img img {
            width: 100%;
            max-width: 150px;
            height: 133px;
            border-radius: 5px;
        }

        .card-front-side table,
        tr,
        td,
            {
            border: none;
        }

        .card-front-side table {
            /* margin-top: 20px; */
            margin: 20px 0px 5px 20px;
        }

        .card-back-side table {
            /* margin-top: 20px; */
            font-size: 13px;
        }

        th,
        td {
            text-align: justify;
        }

        .signature-area {
            position: relative;
            padding: 26px 10px 0px 15px;
        }

        .signature-area b {
            font-size: 13px;
            margin-left: 15px
        }

        .signature-area img.author {
            position: absolute;
            right: 40px;
            top: 10;
            top: -2px;
        }

        img.emp {
            position: absolute;
            top: -3px;
            left: 14px;
        }

        .left {
            margin-right: 50px;
        }


        .card-back-side {
            max-width: 300px;
            height: 508px;
            margin: 10px auto 0px;
            border: 1px solid #28a745;
            box-shadow: 5px 10px 18px #1111;
            padding: 0px 0px 15px 0px;
        }

        .card-back-side p {
            font-size: 11px;
            font-weight: bold;
        }

        .card-back-side .table td,
        .table th {
            padding: 0.60rem;
        }

        .id-card-bottom img {
            width: 80px;
            height: 40px;
            margin-bottom: 5px;
        }

        /* table th.larg-colum{
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            rowspan:4;

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } */
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Hrm </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('candidate.index'))
                            <li class="breadcrumb-item"><a href="{{ route('candidate.index') }}">Hrm</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span> Candidate Details</span></li>
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
                {{-- <div class="card-header text-center">
                <h3 class="card-title">Employee Information Details</h3>
                
            </div> --}}
                <!-- /.card-header -->

                <div class="row">
                    <div class="col-md-12">
                        {{-- <div class="profile-img">
                            @if ($employee->image != null)
                                <img src="{{ asset('/storage/photo/' . $employee->image) }}" alt=""
                                    style="margin-bottom:10px">
                            @else
                                <img src="{{ asset('/storage/employee/profile/demo.jpeg') }}" alt=""
                                    style="margin-bottom:10px">
                            @endif
                        </div> --}}
                    </div>
                    <div class="col-lg-12 ">

                        <div class="card shadow-sm mt-3">
                            <div class="card-header bg-transparent border-0">
                                <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Personal Information</h3>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Name</th>
                                        <td width="2%">:</td>
                                        <td>{{ $candidateInformation->first_name }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Last Name</th>
                                        <td width="2%">:</td>
                                        <td>{{ $candidateInformation->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Email </th>
                                        <td width="2%">:</td>
                                        <td>{{ $candidateInformation->email }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Phone</th>
                                        <td width="2%">:</td>
                                        <td>{{ $candidateInformation->phone }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Alternate Phone</th>
                                        <td width="2%">:</td>
                                        <td>{{ $candidateInformation->alternate_phone ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">SSN</th>
                                        <td width="2%">:</td>
                                        <td>{{ $candidateInformation->ssn ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Present Address</th>
                                        <td width="2%">:</td>
                                        <td>{{ $candidateInformation->present_address }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Permanent Address</th>
                                        <td width="2%">:</td>
                                        <td>{{ $candidateInformation->permanent_address }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">CV</th>
                                        <td width="2%">:</td>
                                        <td><a href="{{ asset('storage/photo/' . $candidateInformation->image) }}"
                                                target="_blanck">Download
                                                CV</a>
                                        </td>
                                    </tr>

                                </table>
                            </div>

                        </div>

                    </div>
                    <div class="col-lg-6 ">
                        <div class="card shadow-sm mt-3">
                            <div class="card-header bg-transparent border-0">
                                <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Educational Information</h3>
                            </div>
                            <div class="card-body pt-0">
                                @foreach ($candidateInformation->eduInfo as $data)
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">university</th>
                                            <td width="2%">:</td>
                                            <td>{{ $data->institution }}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Obtain Degree</th>
                                            <td width="2%">:</td>
                                            <td>{{ $data->obtain_degree }}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">CGPA </th>
                                            <td width="2%">:</td>
                                            <td>{{ $data->cgpa }}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Commentes </th>
                                            <td width="2%">:</td>
                                            <td>{{ $data->comments }}</td>
                                        </tr>
                                    </table>
                                @endforeach
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card shadow-sm mt-3">
                            <div class="card-header bg-transparent border-0">
                                <h3 class="mb-0">
                                    <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Working Experience Info</h3>
                                </h3>
                            </div>
                            <div class="card-body pt-0">
                                @foreach ($candidateInformation->workInfo as $data)
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Company Name</th>
                                            <td width="2%">:</td>
                                            <td>{{ $data->company_name }}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Experience</th>
                                            <td width="2%">:</td>
                                            <td>{{ $data->experience }}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Supervisor </th>
                                            <td width="2%">:</td>
                                            <td>{{ $data->supervisor }}</td>
                                        </tr>
                                    </table>
                                @endforeach
                            </div>

                        </div>
                    </div>


                </div>

            </div>
        </div>
        <!-- /.col-->
    </div>
@endsection
