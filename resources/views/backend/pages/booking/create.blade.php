@extends('backend.layouts.master')

@section('title')
Booking - Add New Booking
@endsection

@section('styles')

@endsection
@section('navbar-content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Booking</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">All Bookings</a>
                    </li>
                    <li class="breadcrumb-item active"><span>Add New</span></li>

                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

@endsection


@section('admin-content')

<!-- page title area end -->

<div class="row">
    <div class="col-md-12">
        <form class="form-horizontal" action="{{ route('admin.bookings.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card card-outline card-danger">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <h4>Invoice Info</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Booking Id </label>
                                <input type="text" value="{{ $bookingId }}" class="form-control cname" readonly placeholder="Name" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Merchent Invoice Id</label>
                                <input type="text" name="marchent_invoice_id" value="{{ old('marchent_invoice_id') }}" class="form-control" placeholder="client invoice id" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-danger">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <h4>Sender / Merchent Info</h4>
                        </div>
                        <br>
                        <br>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3">Sender / Merchent </label>
                        <div class="col-sm-6">
                            @if($role == 'Merchent')
                            <select name="merchent_id" class="select2 form-control merchent_id readonly" required>
                                @foreach($merchent as $key => $value)
                                <option @if(old('merchent_id')==$value->id) selected @endif
                                    value="{{ $value->id}}">{{ $value->username}} [{{ $value->pickup_phone}}]</option>
                                @endforeach
                            </select>
                            @else
                            <select name="merchent_id" class="select2 form-control merchent_id" required>
                                <option value="" selected disabled>--Select Sender--</option>
                                @foreach($merchent as $key => $value)
                                <option @if(old('merchent_id')==$value->id) selected @endif
                                    value="{{ $value->id}}">{{ $value->username}} [{{ $value->pickup_phone}}]</option>
                                @endforeach
                            </select>
                            @endif
                            @error('merchent_id')
                            <span class="error text-red text-bold"> {{ $message }}</span>
                            @enderror
                        </div>
                        @if ($usr->can('merchent.create'))
                        @if($role != 'Merchent')
                        <div class="col-sm-3">
                            <a href="{{ route('admin.merchents.create') }}" class="btn btn-danger"> <i class="fa fa-plus"></i> Add Sender</a>
                        </div>
                        @endif
                        @endif
                        <hr>
                    </div>
                    <div class="row merchentInfo">

                        <input type="hidden" value="" class="inside_dhaka">
                        <input type="hidden" value="" class="outside_dhaka">
                        <!-- <div class="col-sm-4">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" readonly class="form-control username" placeholder="User Name" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Full Name </label>
                                <input type="text" readonly class="form-control fullname" placeholder="Full Name" />
                            </div>
                        </div> -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Shopname </label>
                                <input type="text" readonly class="form-control shopname" placeholder="Shop Name" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label> Pickup Phone</label>
                                <input type="text" readonly class="form-control pickupphone" placeholder="Phone" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Pickup Address</label>
                                <input type="text" readonly class="form-control pickupaddress" placeholder="Pickup address" />
                            </div>
                        </div>

                        <!-- <div class="col-sm-4">
                            <div class="form-group">
                                <label>Shop Address</label>
                                <input type="text" readonly class="form-control shopaddress" placeholder="Shop address" />
                            </div>
                        </div> -->


                    </div>
                </div>
            </div>

            <div class="card card-outline card-danger">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <h4>Receiver / Customer Info</h4>
                        </div>
                    </div>
                    <!-- <div class="form-group row">
                        <label class="col-sm-3">Receiver / Customer </label>
                        <div class="col-sm-6">
                            <select name="receiver_id" class="select2 form-control receiver_id" required>
                                <option value="" selected disabled>--Select Receiver--</option>
                                @foreach($receiver as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->name }} [{{ $value->phone }}]</option>
                                @endforeach
                            </select>
                            @error('receiver_id')
                            <span class="error text-red text-bold"> {{ $message }}</span>
                            @enderror

                        </div>
                        @if ($usr->can('receiver.create'))
                        <div class="col-sm-3">
                            <a href="{{route('admin.receivers.create')}}" class="btn btn-danger"> <i
                                    class="fa fa-plus"></i> Add Receiver</a>
                        </div>
                        @endif
                    </div> -->
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Phone</label>
                                <input maxlength="11" type="text" value="{{old('receiverPhone')}}" class="form-control typeahead" name="receiverPhone" placeholder="01710000000" />
                                <input type="hidden" class="form-control receiver_id " name="receiver_id" placeholder="01710000000" />
                                @error('receiverPhone')
                                <span class="error text-red text-bold"> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label> Customer Name </label>
                                <input type="text" class="form-control receiver_name" value="{{old('receiverName')}}" name="receiverName" placeholder="Name" />
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label> Address</label>
                                <input type="text" class="form-control receiver_address" value="{{old('receiverAddress')}}" name="receiverAddress" placeholder="Address" />
                            </div>
                            @error('receiverAddress')
                            <span class="error text-red text-bold"> {{ $message }}</span>
                            @enderror
                        </div>


                    </div>
                </div>
            </div>
            <div class="card card-outline card-danger">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <h4>Shipping Info</h4>
                        </div>

                        <br>
                    </div>

                    <div class="row">

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Shipping Area </label>
                                <select name="shipping_mode_type" class="select2 form-control shippingarea" required>
                                    <option value="" selected disabled>--Select Area--</option>
                                    <option @if(old('shipping_mode_type')==1) selected @endif value="1">Inside Dhaka
                                    </option>
                                    <option @if(old('shipping_mode_type')==2) selected @endif value="2">Outside Dhaka
                                    </option>
                                </select>
                            </div>
                            @error('shipping_mode_type')
                            <span class="error text-red text-bold"> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Estimated deliver date</label>
                                <div class="input-group date"  data-target-input="nearest">
                                    <input readonly name="delivery_date" value="{{ old('delivery_date') ?? date('m/d/Y') }}" type="text" class="form-control datetimepicker-input estimateDate" data-target="#reservationdate" required>
                                    <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            @error('delivery_date')
                            <span class="error text-red text-bold"> {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Delivery Time</label>
                                <input type="hidden" name="time_sloat" class="time_sloat_value" value=""/>
                                <select  class="select2 form-control time_sloat" disabled required>
                                    <option value="" selected disabled>--Select Courier--</option>
                                    @foreach($timeSloat as $key => $value)
                                    <option @if(old('time_sloat')==$value->id) selected @endif
                                        value="{{ $value->id }}">{{ $value->time_sloat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('time_sloat')
                            <span class="error text-red text-bold"> {{ $message }}</span>
                            @enderror
                        </div>
                        @if($role != 'Merchent')
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Assain Driver</label>
                                <select name="driver_id" class="select2 form-control">
                                    <option value="" selected disabled>--Select Driver--</option>
                                    @foreach($driver as $key => $value)
                                    <option @if(old('driver_id')==$value->id) selected @endif
                                        value="{{ $value->id }}">{{ $value->username }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('driver_id')
                            <span class="error text-red text-bold"> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Delivery Status</label>
                                <select name="delivery_status" class="select2 form-control" required>
                                    <option value="" selected disabled>--Select Status--</option>
                                    @foreach($status as $key => $value)
                                    <option @if($value->id == 1) selected @endif @if(old('delivery_status')==$value->id) selected @endif
                                        value="{{ $value->id }}">{{ $value->status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('delivery_status')
                            <span class="error text-red text-bold"> {{ $message }}</span>
                            @enderror
                        </div>
                        @endif
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Cash Collection</label>
                                <input type="text" name="cash_collection" value="{{ old('cash_collection') }}" class="form-control" placeholder="0.00" required />
                            </div>
                            @error('cash_collection')
                            <span class="error text-red text-bold"> {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Invoice Amount</label>
                                <input type="text" name="parcel_equivalent_price" value="{{ old('parcel_equivalent_price') }}" class="form-control" placeholder="0.00" required />
                            </div>
                            @error('parcel_equivalent_price')
                            <span class="error text-red text-bold"> {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Delivery Charge</label>
                                <input type="text" readonly name="delivery_charge" value="{{ old('delivery_charge') }}" class="form-control deliverCharge" placeholder="0.00" required />
                            </div>
                            @error('delivery_charge')
                            <span class="error text-red text-bold"> {{ $message }}</span>
                            @enderror
                        </div>

                        <!-- <div class="col-sm-4">
                            <div class="form-group">
                                <label>Attach files</label>
                                <input type="file" name="file" class="form-control" placeholder="0.00" />
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="card card-outline card-danger">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <h4>Add box to list</h4>
                        </div>
                        <div class="col-md-8"></div>
                        <div class="col-md-2"></div>
                        <br>
                        <br>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            @error('product_type')
                            <span class="error text-red text-bold"> {{ $message }}</span>
                            @enderror

                        </div>


                        <div class="col-sm-12">
                            <table class=" table table-bordered" id="show_item">
                                <thead>
                                    <tr>
                                        <th width="25%" nowrap><b>Category</b></th>
                                        <th width="30%" nowrap><b>Description</b></th>
                                        <th width="20%" nowrap><b>Quantity</b></th>
                                        <th width="20%" nowrap><b>Weight (gm)</b></th>
                                        <!-- <th width="10%" nowrap><b>Length (cm)</b></th>
                                        <th width="10%" nowrap><b>Width (cm)</b></th>
                                        <th width="10%" nowrap><b>Height (cm)</b></th>
                                        <th>Action</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="card-hover">
                                        <td>
                                            <select class="form-control select2" id="product_type" name="product_type" required>
                                                <option value="0" disabled selected>--Select Category--</option>
                                                @foreach($itemCategory as $key => $value)
                                                <option @if(old('product_type')==$value->id) selected @endif
                                                    value="{{ $value->id }}">{{ $value->title}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="item_description" class="form-control description" placeholder="Description" value="{{ old('item_description') }}" required />
                                        </td>
                                        <td>
                                            <input type="number" name="quantity" class="form-control qty" value="{{old('quantity') ?? 1}}" placeholder="0" />
                                        </td>
                                        <td>
                                            <input type="number" name="weight" class="form-control weightib" value="{{ 500 ?? old('weight') }}" min="500" placeholder="0" />
                                        </td>
                                        <!-- <td>
                                            <input type="number" name="length[]" class="form-control lengthcm" value=""
                                                placeholder="0" />
                                        </td>

                                        <td>
                                            <input type="number" name="width[]" class="form-control widthcm" value=""
                                                placeholder="0" />
                                        </td>

                                        <td>
                                            <input type="number" name="height[]" class="form-control heightcm" value=""
                                                placeholder="0" />
                                        </td>
                                        <td><button class="btn btn-success " type="button" id="add_item"><i
                                                    class="fa fa-plus"></i> </button></td> -->
                                    </tr>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="form-group row">

                    <button type="submit" class=" btn btn-danger"><i class="fa fa-save"></i> Save</button>
                </div>
            </div>
        </form>
    </div>
    <!-- /.col-->
</div>

@endsection

@section('scripts')
@include('backend.pages.booking.partials.scripts')
@endsection