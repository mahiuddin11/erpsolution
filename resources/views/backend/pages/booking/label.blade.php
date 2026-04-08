@extends('backend.layouts.master')

@section('title')
Settings - Product Type
@endsection
@section('styles')
@endsection
@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">View Booking #{{$booking->voucher_id}}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a onclick="window.print()" class="btn btn-danger text-white"><span
                                class="fa fa-print">Print</span></a></span>
                    </li>
                    @if (Auth::guard('admin')->user()->can('driver.create'))
                    <li class="breadcrumb-item active"> <a class="btn btn-danger text-white"
                            href="{{ route('admin.drivers.create') }}"><i class="fas fa-plus"></i> Add New</a></li>
                    @endif
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection
@section('admin-content')
<div class="row">
    <div class="col-12">

        <!-- Main content -->
        <div class="invoice p-3 mb-3">

            <div class="row">
                <div class="col-lg-12">

                    <table border="1" class="table table-bordered">
                        <tbody>
                            <tr>
                                <td width="10%"><b>MERCHENT: </b></td>
                                <td colspan="2"> <b>{{$booking->merchent->full_name}}</b> <br>
                                    {{$booking->merchent->pickup_address}}<br>
                                    <b> {{$booking->merchent->pickup_phone}}</b><br>
                                </td>
                            </tr>
                            <tr>
                                <td width="10%"><b>CUSTOMER: </b></td>
                                <td colspan="2">
                                    <b>{{$booking->receiver->name ?? 'N/A'}}</b><br>
                                    {{$booking->receiver->phone}}<br>
                                    <b>{{$booking->receiver->address}}</b><br>
                                </td>
                            </tr>
                            <tr>
                                <td width="10%"><b>INVOICE#: </b></td>
                                <td>
                                    {{$booking->voucher_id}}<br>

                                </td>
                                <td> Area : @if($booking->shipment->shipping_mode_type == 1)
                                    Inside Dhaka
                                    @else
                                    Outside Dhaka
                                    @endif</td>
                            </tr>
                            <tr>

                                <td style="height: 100px!important;">
                                    <?php echo DNS2D::getBarcodeHTML($booking->voucher_id, 'QRCODE', 5, 7); ?><br>
                                </td>
                                <td style="text-align: center;" colspan="2">

                                    <table class="table table-bordered center" border="1">
                                        <tr>
                                            <td colspan="2" style="text-align: center!important;">
                                                <br><?php
                                                    echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($booking->voucher_id, 'C39+', 1, 50) . '" alt="barcode"   />';
                                                    ?><br>
                                                {{$booking->voucher_id }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>PARCEL ID:</b> {{$booking->voucher_id }}</td>
                                            <td><b>PARCEL CREATED:</b>{{$booking->created_at }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>










                </div>
            </div>

            <!-- info row -->
            <!-- /.row -->
            <!-- /.row -->




        </div>
        <!-- /.invoice -->
    </div><!-- /.col -->
</div><!-- /.row -->
@endsection


@section('scripts')
<script>
window.onload = function() {
    window.print();
}
</script>
@endsection