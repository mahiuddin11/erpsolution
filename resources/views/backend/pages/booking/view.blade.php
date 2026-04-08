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
                    @if($role !='Merchent')

                    <li class="breadcrumb-item"><span><a class="btn btn-danger btn-sm text-white"
                                href="{{route('printLevel',$booking->id) }}"><i class="fa fa-print"></i>Print
                                Level</a></span></li>
                    @endif
                    @if (Auth::guard('admin')->user()->can('driver.create'))
                    <li class="breadcrumb-item active"> <a class="btn btn-danger btn-sm text-white"
                            href="{{ route('admin.bookings.create') }}"><i class="fas fa-plus"></i> Add New</a></li>
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
        <div class="callout callout-danger">
            <div class="row invoice-danger">
                <div class="col-sm-4 invoice-col">
                    <h6> Sender / Merchent Info</h6>
                    <address>
                        <strong>{{$booking->merchent->username}}</strong><br>
                        <b>Shop Address:</b> {{$booking->merchent->shop_address}}<br>
                        <b>Pickup Address:</b> {{$booking->merchent->pickup_address}}<br>
                        <b>Phone:</b> {{$booking->merchent->pickup_phone}}<br>
                        <b>Email:</b> {{$booking->merchent->shop_email}}
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    <h6> Receiver / Customer Info</h6>
                    <address>
                        <strong>{{$booking->receiver->name}}</strong><br>
                        <b>Address:</b> {{$booking->receiver->address}}<br>
                        <b>Phone:</b> {{$booking->receiver->phone}}<br>
                        <!-- <b>Email:</b>{{$booking->receiver->email}} -->
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    <h6> Booking Info</h6>
                    <b>Voucher Id: {{$booking->voucher_id}}</b><br>
                    <b>Merchent Invoice:</b> {{ $booking->marchent_invoice_id}}<br>
                    <b>Booking Status:</b>{{ $booking->status->status}}<br>
                    <div class="row">
                        <?php
                        echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($booking->voucher_id, 'C39+', 1, 33) . '" alt="barcode"   />';
                        ?>
                    </div>
                </div>
                <!-- /.col -->
            </div>
        </div>
        <!-- Main content -->
        <div class="invoice p-3 mb-3">
            <!-- title row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-outline card-danger">
                        <div class="card-body">
                            <h5 class="card-title">Shipping Info</h5>
                            <br>
                            <hr>
                            <!-- <hr> -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="pull-left">
                                        <h6> &nbsp;<b>Shipping Area</b></h6>
                                        <p class="text-muted  m-l-5">
                                            @if($booking->shipment->shipping_mode_type == 1)
                                            Inside Dhaka
                                            @else
                                            Outside Dhaka
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="pull-left">
                                        <h6> &nbsp;<b>Estimated Delivery Date</b></h6>
                                        <p class="text-muted  m-l-5">{{$booking->shipment->delivery_date}}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="pull-left">
                                        <h6> &nbsp;<b>Delivery Time</b></h6>
                                        <p class="text-muted  m-l-5">{{ $booking->shipment->timeSloat->time_sloat}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @if(!empty($booking->shipment->driver))

                                <div class="col-md-4">
                                    <div class="pull-left">
                                        <h6> &nbsp;<b>Driver</b></h6>
                                        <p class="text-muted  m-l-5">{{$booking->shipment->driver->username}}
                                            [{{$booking->shipment->driver->phone}}]</p>
                                    </div>
                                </div>

                                @endif



                                <!-- <div class="col-md-4">
                                    <div class="pull-left">
                                        <h5> &nbsp;<b>City</b></h5>
                                        <p class="text-muted  m-l-5">11111</p>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-outline card-danger">
                        <div class="card-body">
                            <h5 class="card-title">Booking Items</h5>
                            <br>
                            <br>
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Product Type</th>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                            <!-- <th>Dimensions</th> -->
                                            <th>Weight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($booking->items as $key => $value)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$value->type->title}}</td>
                                            <td>{{$value->item_description}}</td>
                                            <td>{{$value->quantity}}</td>
                                            <!-- <td>{{$value->length}}cm * {{$value->width}}cm * {{$value->height}} cm</td> -->
                                            <td>{{$value->weight}}gm</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- info row -->
            <!-- /.row -->
            <!-- /.row -->
            <div class="row">
                <!-- accepted payments column -->
                <div class="col-6">
                    <!-- <p class="lead">Note:</p>
                    <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                    </p> -->
                    @if(!empty($booking->issue_id))
                    <p class="lead">Issue: {{$booking->issue->title}} </p>

                    <p class="" style="margin-top: 10px;"> {{$booking->issue_note}}</p>
                    @endif
                </div>
                <!-- /.col -->
                <div class="col-6">
                    <p class="lead">Payment Info</p>

                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th style="width:50%">Cash Collection:</th>
                                <td>{{ $booking->cash_collection}}</td>
                            </tr>
                            <tr>
                                <th>Delivery Charge (-)</th>
                                <td>{{ $booking->delivery_charge}}</td>
                            </tr>
                            <tr>
                                <th>Payemnt Payable:</th>
                                <td>{{ number_format($booking->cash_collection - $booking->delivery_charge,2)}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- this row will not appear when printing -->
            <div class="row no-print">
                <!-- <div class="col-12">
                    <a href="invoice-print.html" rel="noopener" target="_blank" class="btn btn-default"><i
                            class="fas fa-print"></i> Print</a>
                    <button type="button" class="btn btn-success float-right"><i class="far fa-credit-card"></i> Submit
                        Payment
                    </button>
                    <button type="button" class="btn btn-danger float-right" style="margin-right: 5px;">
                        <i class="fas fa-download"></i> Generate PDF
                    </button>
                </div> -->
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-danger">
                        <div class="card-body">
                            <!-- The time line -->
                            <div class="timeline">
                                <!-- timeline time label -->
                                <!-- END timeline item -->
                                <!-- timeline item -->

                                <?php



                                // echo "<pre>";
                                // print_r($booking->statusHistory);
                                // die;




                                ?>


                                @foreach($booking->statusHistory as $key => $value)
                                <?php
                                $color = explode("-", $value->status->color_code);
                                ?>
                                <div class="time-label">
                                    <span class="bg-{{ $color[1]}}">{{$value->created_at}}</span>
                                </div>
                                <div>
                                    <i class="{{$value->status->icon}} bg-{{ $color[1]}}"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i>
                                            {{ $value->created_at->diffForHumans()}}</span>
                                        <h3 class="timeline-header"><span
                                                class="{{$value->status->color_code}}">{{$value->status->status}}
                                            </span>
                                        </h3>
                                        <div class="timeline-body">
                                            <div class="embed-responsive">
                                                <p>{{$value->status_mode}}</p>
                                                @if($value->driver)

                                                <p>Driver: {{$value->driver->full_name}}
                                                    [{{$value->driver->registration_number}}]</p>
                                                @endif
                                                @if($value->note)
                                                <p class="text-danger">Reason: {{$value->note}}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="timeline-footer">
                                            <span class="{{$value->status->color_code}}">
                                                updated by {{$value->created_by}}</span>
                                        </div>
                                    </div>
                                </div>

                                @endforeach
                                <!-- END timeline item -->
                                <div>
                                    <i class="fas fa-clock bg-gray"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>
        </div>
        <!-- /.invoice -->
    </div><!-- /.col -->
</div><!-- /.row -->
@endsection


@section('scripts')

@endsection