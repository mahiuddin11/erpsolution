@extends('backend.layouts.master')

@section('title')
    Dashboard Page - Admin Panel
@endsection
<style>
    .small-box>.small-box-footer {
    border: none;
    width: 100%;
}
</style>


@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a href="#">Dashboard</a></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
        <div class="row">
            @if ($user->branch_id !== null)
                <div class="col-md-6 offset-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-md-12">
                                <div class="position-relative p-3 bg-green" style="height: 150px">
                                    <div class="ribbon-wrapper ribbon-xl">
                                        <div class="ribbon bg-red">
                                            {{ $user->branch->branchCode ?? "" }} <br>
                                            {{ $user->branch->name ?? "" }}
                                        </div>
                                    </div>
                                    <h3> Today : {{ date('d-M-Y') }}
                                    </h3>
                                    <h2>Hello <br> {{ $user->name }}</h2>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="row">
            @if (in_array( 18, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $totalemployee }}</h3>
                        <p>Total Employee</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{route('hrm.employee.index')}}"  class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif

            @if (in_array( 19, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $todayattendance }}</h3>
                        <p>Today Attendance</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a  href="{{route("hrm.attendancelog.index")}}" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif
            @if (in_array( 1, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($leave_aplication) }}</h3>

                        <p>Today Leaves</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{route('hrm.leaveapprove.index')}}" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif

            @if (in_array( 2, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($total_absent) }}</h3>

                        <p>Today Absent</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{route('hrm.attendancelog.absent')}}" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif

            @if (in_array( 3, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($approved_leave) }}</h3>

                        <p>Approved Leave</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{route('hrm.leave.index')}}" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif

            @if (in_array( 4, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($monthly_created_employee) }}</h3>

                        <p>This Month New Employee</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{route('hrm.employee.newemployee')}}" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif

            @if (in_array( 5, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($monthly_lone) }}</h3>

                        <p>This Month Loan Application</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{route('hrm.lone.index')}}" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif

            @if (in_array( 5, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($monthly_lone_approved) }}</h3>

                        <p>This Month Loan Approved</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{route('hrm.loneapprove.index')}}" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif

            @if (in_array( 1, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <form action="{{route('report.cashbook.cashbook')}}" method="post">
                    <input type="hidden" name="account_id" value="{{getAccountByUniqueID(7)->id}}">
                    <input type="hidden" name="start_date" value="{{date('Y-01-01')}}">
                    <input type="hidden" name="to_date" value="{{date('Y-m-d')}}">
                @csrf
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($totalcashbalance) }}</h3>
                        <p>Cash Balance</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <button href="" class="small-box-footer bg-success">More info <i
                        class="fas fa-arrow-circle-right"></i></button>
                    </div>
                </form>
            </div>

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <form action="{{route('report.dashboard.trialbalance')}}" method="post">
                    @csrf
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($totalbankbalance) }}</h3>
                        <p>Bank Balance</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <button href="" class="small-box-footer bg-success">More info <i
                        class="fas fa-arrow-circle-right"></i></button>
                    </div>
                </form>

            </div>
            @endif



            @if (in_array( 2, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($purchase) }}</h3>

                        <p>Total Purchases</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{route('inventorySetup.purchase.index')}}" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif

            @if (in_array( 3, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($totdayPurchase) }}</h3>
                        <p>Today Purchase</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="{{route('inventorySetup.purchase.index',['date'=> date('Y-m-d') ])}}" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif

            @if (in_array( 4, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($sale) }}</h3>
                        <p>Total Sales</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{route('sale.sale.index')}}" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif

            @if (in_array( 5, $rollper))
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($todaySale) }}</h3>

                        <p>Today Sale</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{route('sale.sale.index', ['date'=> date('Y-m-d') ])}}" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif

            @if (in_array( 6, $rollper))
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <form action="{{route('report.expense',['to_date' => date('Y-m-d') ])}}" method="post">
                    @csrf
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($expense) }}</h3>

                        <p>Total Expense</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <button  class="small-box-footer bg-success">More info <i
                        class="fas fa-arrow-circle-right"></i></button>
                </div>
            </form>
            </div>
            @endif


            @if (in_array( 7, $rollper))
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <form action="{{route('report.expense',['from_date' => date('Y-m-d'),'to_date' => date('Y-m-d') ])}}" method="post">
                    @csrf
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($todayExpense) }}</h3>

                        <p>Today Expense</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>

                    <button  class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></button>
                </div>
            </form>

            </div>
            @endif

            @if (in_array( 8, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ number_format($suppplierPayment) }}</h3>
                        <p>Total Payment</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{route('settings.dabit.voucher.index')}}" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif

            @if (in_array( 9, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $customerPayment }}</h3>
                        <p>Total Received</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{route('settings.credit.voucher.index')}}" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif

            @if (in_array( 10, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <form action="{{route('report.supledger.supledger',['supplier_id' => "all"])}}" method="post">
                    @csrf
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $supplierDue }}</h3>
                        <p>Supplier Due</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <button  class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></button>
                </div>
             </form>

            </div>
            @endif

            @if (in_array( 11, $rollper))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <form action="{{route('report.custledger.custledger',['customer_id' => "all"])}}" method="post">
                    @csrf
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $customerDue }}</h3>

                        <p>Customer Due</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <button  class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></button>
                </div>
             </form>

            </div>
            @endif



            @if (in_array( 12, $rollper))
            {{-- <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $totalService }}</h3>

                        <p>Total Service</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div> --}}
            @endif


            @if ($user->branch_id == null)
            @if (in_array( 13, $rollper))
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title" style="color:green; font-weight: bold">Branch Stock</h3>
                                <a class="btn btn-xs btn-info"
                                    href="{{ route('inventorySetup.currentStock.index') }}">View
                                    Report</a>
                            </div>
                        </div>
                        <div class="panel panel-default">

                            <div class="panel-body">
                                <canvas id="canvas" height="148"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if (in_array( 14, $rollper))
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title" style="color:green; font-weight: bold">Full Summary</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-responsive">
                                <div id="piechart" style="width: 600px; height: 309px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if (in_array( 15, $rollper))
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title" style="color:green; font-weight: bold">Pending Requisitions :
                                    Branch</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-responsive">
                                <div class="row">

                                    <div class="col-lg-4 text-center">
                                        <a href="{{ route('inventorySetup.purchaserequisition.index') }}">

                                            <input type="text" class="knob" value="{{ $prPending }}"
                                                data-skin="tron" data-thickness="0.2" data-width="90" data-height="90"
                                                data-fgColor="green" data-readonly="true">
                                            <div class="knob-label">
                                                PR
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-4 text-center">
                                        <a href="{{ route('inventorySetup.purchaseorder.index') }}">
                                            <input type="text" class="knob" value="{{ $poPending }}"
                                                data-skin="tron" data-thickness="0.2" data-width="90" data-height="90"
                                                data-fgColor="orange" data-readonly="true">
                                            <div class="knob-label">
                                                PO
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-4 text-center">
                                        <a href="{{ route('inventorySetup.purchase.pvindex') }}">
                                            <input type="text" class="knob" value="{{ $pvPending }}"
                                                data-skin="tron" data-thickness="0.2" data-width="90" data-height="90"
                                                data-fgColor="red" data-readonly="true">
                                            <div class="knob-label">
                                                PV
                                            </div>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card" style="height: 269px">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title" style="color: blue; font-weight: bold"> Pending Requisitions :
                                    Project</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-responsive">
                                <div class="row">
                                    <div class="col-lg-6 text-center">
                                        <a href="{{ route('project.RequisitionAction.index') }}">
                                            <input type="text" class="knob" value="{{ $PendingReq }}"
                                                data-skin="tron" data-thickness="0.2" data-width="120" data-height="120"
                                                data-fgColor="green" data-readonly="true">
                                            <div class="knob-label">
                                                Requisitions
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-6 text-center">
                                        <a href="{{ route('project.projectreturn.index') }}">
                                            <input type="text" class="knob" value="{{ $PendingReturn }}"
                                                data-skin="tron" data-thickness="0.2" data-width="120" data-height="120"
                                                data-fgColor="orange" data-readonly="true">
                                            <div class="knob-label">
                                                Return
                                            </div>
                                        </a>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- ## Rabbi Work -->
                @if (in_array( 16, $rollper))
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title" style="color:green; font-weight: bold">Pending Stock Manage :
                                    Branch</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-responsive">
                                <div class="row">

                                    <div class="col-lg-6 text-center">
                                        <a href="{{ route('inventorySetup.transfer.index') }}">
                                            <input type="text" class="knob" value="{{ $trPending }}"
                                                data-skin="tron" data-thickness="0.2" data-width="90" data-height="90"
                                                data-fgColor="green" data-readonly="true">
                                            <div class="knob-label">
                                                Transfer
                                            </div>
                                        </a>
                                    </div>

                                    <div class="col-lg-6 text-center">
                                        <a href="{{ route('inventorySetup.stockAdjustment.index') }}">
                                            <input type="text" class="knob" value="{{ $adjPending }}"
                                                data-skin="tron" data-thickness="0.2" data-width="90" data-height="90"
                                                data-fgColor="orange" data-readonly="true">
                                            <div class="knob-label">
                                                Adjustment
                                            </div>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                @endif

            @endif
        </div>
    {{-- @if ($user->type == 'Project')
        <div class="row">
            <div class="col-md-12 bg-info">
                <h3 align="center">{{ $prjectDetails->name }}</h3>
            </div>

            <div class="col-lg-3 col-6">
                <!-- small box -->

                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $prjectDetails->budget }}</h3>

                        <p>Project Budget</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->

                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $totalprojectexpence }}</h3>

                        <p>Total Project Expense</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $todayprojectexpence }}</h3>

                        <p>Today Project Expense</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $productreq }}</h3>

                        <p>Total Product Requisition</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $productreqtoday }}</h3>

                        <p>Today Product Requisition</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $useproduct }}</h3>

                        <p>Total Use Product</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>


            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $usetotaltoday }}</h3>

                        <p>Today Use Product</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $returntotal }}</h3>

                        <p>Total Return Product</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $returntoday }}</h3>

                        <p>Today Return Product</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

        </div>
    @endif --}}

    {{-- @if ($user->type == 'Employee')
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $userrole }}</h3>
                        <p>Roles</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>435</h3>

                        <p>Admins</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $customer }}</h3>
                        <p>Total Customer</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $Supplier }}</h3>

                        <p>Total Supplier</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>


            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $sale }}</h3>

                        <p>Total Sales</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>{{ $purchase }}</h3>

                        <p>Total Purchases</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>4534</h3>

                        <p>Payment</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-default">
                    <div class="inner">
                        <h3>4534</h3>

                        <p>Received</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="" class="small-box-footer bg-success">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

        </div>
    @endif --}}


    <script>
        var url = "{{ url('admin/chart/chart') }}";
        var branchCode = new Array();
        var Labels = new Array();
        var quantity = new Array();
        $(document).ready(function() {
            $.get(url, function(response) {

                console.log(response);
                response.forEach(function(data) {
                    branchCode.push(data.branchCode);
                    Labels.push(data.stockName);
                    quantity.push(data.quantity);
                });
                var ctx = document.getElementById("canvas").getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: branchCode,
                        datasets: [{
                            label: 'Quantity',
                            data: quantity,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
            });
        });
    </script>


    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            var data = google.visualization.arrayToDataTable([
                ['Task', 'Hours per Day'],
                ['Purchase', {
                    {
                        $purchase
                    }
                }],
                ['Sale', {
                    {
                        $sale
                    }
                }],
                ['Expense', {
                    {
                        $expense
                    }
                }],

            ]);

            var options = {
                title: 'Summary'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

            chart.draw(data, options);
        }
    </script>

    <script>
        $(function() {
            /* jQueryKnob */

            $('.knob').knob({

                draw: function() {

                    // "tron" case
                    if (this.$.data('skin') == 'tron') {

                        var a = this.angle(this.cv) // Angle
                            ,
                            sa = this.startAngle // Previous start angle
                            ,
                            sat = this.startAngle // Start angle
                            ,
                            ea // Previous end angle
                            ,
                            eat = sat + a // End angle
                            ,
                            r = true

                        this.g.lineWidth = this.lineWidth

                        this.o.cursor &&
                            (sat = eat - 0.3) &&
                            (eat = eat + 0.3)

                        if (this.o.displayPrevious) {
                            ea = this.startAngle + this.angle(this.value)
                            this.o.cursor &&
                                (sa = ea - 0.3) &&
                                (ea = ea + 0.3)
                            this.g.beginPath()
                            this.g.strokeStyle = this.previousColor
                            this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false)
                            this.g.stroke()
                        }

                        this.g.beginPath()
                        this.g.strokeStyle = r ? this.o.fgColor : this.fgColor
                        this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false)
                        this.g.stroke()

                        this.g.lineWidth = 2
                        this.g.beginPath()
                        this.g.strokeStyle = this.o.fgColor
                        this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth *
                            2 / 3, 0, 2 * Math.PI, false)
                        this.g.stroke()

                        return false
                    }
                }
            })
            /* END JQUERY KNOB */

            //INITIALIZE SPARKLINE CHARTS
            var sparkline1 = new Sparkline($('#sparkline-1')[0], {
                width: 240,
                height: 70,
                lineColor: '#92c1dc',
                endColor: '#92c1dc'
            })
            var sparkline2 = new Sparkline($('#sparkline-2')[0], {
                width: 240,
                height: 70,
                lineColor: '#f56954',
                endColor: '#f56954'
            })
            var sparkline3 = new Sparkline($('#sparkline-3')[0], {
                width: 240,
                height: 70,
                lineColor: '#3af221',
                endColor: '#3af221'
            })

            sparkline1.draw([1000, 1200, 920, 927, 931, 1027, 819, 930, 1021])
            sparkline2.draw([515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921])
            sparkline3.draw([15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21])

        })
    </script>


@endsection
