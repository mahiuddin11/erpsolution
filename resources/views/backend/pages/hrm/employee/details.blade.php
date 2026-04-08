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

        .profile-img > img {
    vertical-align: middle;
    border-style: none;
    width: 201px;
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
                        @if (helper::roleAccess('hrm.lone.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.lone.index') }}">Hrm</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span> Employee Details</span></li>
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
                        <div class="profile-img">
                            @if ($employee->image != null)
                                <img src="{{ asset('/storage/photo/' . $employee->image) }}" alt=""
                                    style="margin-bottom:10px">
                            @else
                                <img src="{{ asset('/storage/employee/profile/demo.jpeg') }}" alt=""
                                    style="margin-bottom:10px">
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6 ">

                        <div class="card shadow-sm mt-3">
                            <div class="card-header bg-transparent border-0">
                                <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Basic Information</h3>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Name</th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->name }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">ID Number</th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->id_card }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Email </th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->email }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Personal Number</th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->personal_phone }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Office Number</th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->office_phone }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Marial Status</th>
                                        <td width="2%">:</td>
                                        <td>{{ ucfirst($employee->marital_status) }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">NID</th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->nid }}</td>
                                    </tr>

                                    <tr>
                                        <th width="30%">DOB </th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->dob }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Blood Group </th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->blood_group ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Gender</th>
                                        <td width="2%">:</td>
                                        <td>{{ ucfirst($employee->gender) }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Guardian Number</th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->guardian_number }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Guardian Nid Photo</th>
                                        <td width="2%">:</td>
                                        <td> <img style="width:350px" src="{{ asset('/storage/photo/' . $employee->guardian_nid) }}"> </td>
                                    </tr>
                                   
                                    <tr>
                                        <th width="30%">Reference </th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->reference }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Experience </th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->experience }} Years</td>
                                    </tr>

                                </table>
                            </div>

                        </div>
                    </div>
                    
                    <div class="col-lg-6 ">
                        <div class="card shadow-sm mt-3">
                            <div class="card-header bg-transparent border-0">
                                <h3 class="mb-0">
                                    <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Office Information</h3>
                                </h3>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Joining date</th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->join_date }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Last In Time</th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->last_in_time }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Deperment </th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->department }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Position</th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->position->name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Branch</th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->branch->name ?? '' }} </td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Salary </th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->salary }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Overtime </th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->over_time_is }}</td>
                                    </tr>

                                </table>
                            </div>

                        </div>
                        <div class="card shadow-sm mt-3">
                            <div class="card-header bg-transparent border-0">
                                <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Qualification Information</h3>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Degree</th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->achieved_degree }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Institution</th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->institution }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Passing year </th>
                                        <td width="2%">:</td>
                                        <td>{{ $employee->passing_year }}</td>
                                    </tr>

                                </table>
                            </div>

                        </div>
                    </div>


                </div>
                <div class="row" id="idcardFontPrinting">
                    <div class="col-lg-6">

                        <div class="card-front-side">

                            <h4>{{ $company->company_name ?? 'N/A' }}</h4>
                            <div class="id-card-img">
                                @if ($employee->image != null)
                                    <img src="{{ asset('/storage/photo/' . $employee->image) }}">
                                @else
                                    <img src="{{ asset('/storage/employee/profile/demo.jpeg') }}">
                                @endif
                            </div>
                            <table class="">
                                <tr>
                                    <th width="35%">ID NO</th>
                                    <td width="1%">:</td>
                                    <td>{{ $employee->id_card }}</td>
                                    <hr style="background-color: #28a745">
                                </tr>

                                <tr>
                                    <th width="30%">Name</th>
                                    <td width="2%">:</td>
                                    <td>{{ $employee->name }}</td>
                                </tr>


                                <tr>
                                    <th width="30%">Designation </th>
                                    <td width="2%">:</td>
                                    <td>{{ $employee->position->name ?? '' }}</td>
                                </tr>
                                {{-- <tr>
                                    <th width="30%">সেকশন</th>
                                    <td width="2%">:</td>
                                    <td>{{$employee->deperment}}</td>
                                </tr>
                                <tr>
                                    <th width="30%">বিভাগ </th>
                                    <td width="2%">:</td>
                                    <td>{{$employee->over_time_is}}</td>
                                </tr> --}}
                                <tr>
                                    <th width="30%">Joining Date</th>
                                    <td width="2%">:</td>
                                    <td>{{ $employee->join_date }}</td>
                                </tr>
                                <tr>
                                    <th width="30%">Type of Job </th>
                                    <td width="2%">:</td>
                                    <td>{{ $employee->status }}</td>
                                </tr>

                            </table>
                            <Br><Br>
                            <div class="signature-area d-flex justify-content-between">
                                @if (isset($employee->emp_signature))
                                    <img class="emp"
                                        src="{{ asset('/storage/photo/' . $employee->emp_signature) }}"
                                        height="20px" width="80px">
                                @endif
                                <p>Signature of cardholder</p>
                                <img class="author" src="{{ asset('/backend/autority_signature/'. $company->autority_signature) }}"
                                    width="80">
                                <p>Signature of the approver</p>
                                {{-- <Br>
                                <Br>
                                <b><Br><Br>কার্ডধারীর স্বাক্ষর</b>

                                @if ($company->autority_signature)
                                    <b><img src="{{ asset('/backend/autority_signature/' . $company->autority_signature) }}"
                                            alt="" height="20px" width="90px"
                                            style="margin-left: 15px;"><Br>অনুমোদনকারীর স্বাক্ষর</b>
                                @else
                                    <b><Br><img
                                            src="{{ asset('/backend/autority_signature/autho.png') }}"style="margin-left: 35px;"
                                            alt="f" width="80">অনুমোদনকারীর স্বাক্ষর</b>
                                @endif --}}
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6" id="idcardBackPrinting">

                        <div class="card-back-side">

                            <table class="table table-bordered table-resposive">
                                <tr>
                                    <th width="37%" style="height: 10px">Blood Group</th>

                                    <td colspan="2" style="height: 10px">{{ $employee->blood_group ?? '' }}</td>

                                </tr>

                                <tr>
                                    <th>Issue Date</th>

                                    <td colspan="2">{{ date_format(date_create($employee->created_at), 'd M Y') }}</td>
                                </tr>

                                <tr>
                                    <th>Permanent address</th>

                                    <td colspan="2">{{ $employee->permanent_address }}</td>
                                </tr>
                                {{-- <tr>
                                    <th rowspan="4" style="vertical-align: middle">স্থায়ী ঠিকানা </th>
                                    <td>গ্রাম</td>
                                    <td>চারাবন</td>
                                </tr>
                                <tr>
                                    <td>ডাকঘর</td>
                                    <td>চারাগাহাট</td>
                                </tr>
                                <tr>
                                    <td>থানা</td>
                                    <td>মধুখালী</td>
                                </tr>
                                <tr>
                                    <td>জেলা</td>
                                    <td>ফরিদপুর</td>
                                </tr> --}}
                                <tr>
                                    <th>Emergency contact</th>
                                    <td colspan="2">{{ $employee->personal_phone }}</td>
                                </tr>
                                <tr>
                                    <th> NID / Birth Certificate </th>
                                    <td colspan="2">{{ $employee->nid }}</td>
                                </tr>
                                <tr>
                                    <th>Duration</th>

                                    <td colspan="2">
                                        {{ date('d F Y', strtotime($employee->created_at->addYear())) }}
                                    </td>
                                </tr>
                            </table>

                            <div class="id-card-bottom text-center">
                                <p>If the ID card is available, it is requested to submit it to the following or the nearest police station.
                                </p>
                                @if (isset($company->logo))
                                    <img src="{{ asset('/backend/logo/' . $company->logo) }}">
                                @endif
                                <p><b><u>Factory Office</b></u></p>
                                <p><b>{{$company->address}}</b></p>
                            </div>


                        </div>



                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <input type="button" class="btn btn-primary" style="margin-left: 200px"
                        onclick="printDiv('idcardFontPrinting')" value="print" />


                </div>
            </div>
        </div>
        <!-- /.col-->
    </div>
@endsection


@section('scripts')
    <script>
        function printDiv(cardId) {
            var printContents = document.getElementById(cardId).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
    </script>
@endsection
