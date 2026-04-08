@extends('backend.layouts.master')

@section('title')
Booking - Booking List
@endsection

@section('styles')

@endsection

@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"> @if(!empty($deliveryStatus))   {{$deliveryStatus->status}} Booking List @else All Booking List @endif</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><span>Booking List</span></li>
                    @if (Auth::guard('admin')->user()->can('booking.create'))
                    <li class="breadcrumb-item active"> <a class="btn btn-danger text-white"
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
    <div class="col-md-12">
        <div class="card card-outline card-info">
            <div class="card-body">

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Creation Date</th>
                            <th>Tracking ID</th>
                            <th>Merchent Name</th>
                            <th>Merchent Phone</th>
                            <th>Merchent Address</th>
                            <th>Receiver Name</th>
                            <th>Receiver Phone</th>
                            <th>Receiver Address</th>
                            <th>Payment Info</th>
                            <th>Payment Status</th>
                            <th>Booking Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking as $key => $value)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{ $value->created_at}}</td>
                            <td><a href="{{ route('admin.bookings.show', $value->id) }}">{{ $value->voucher_id}}</a>
                            </td>
                            <td><a
                                    href="{{ route('merchent.profile', $value->merchent_id) }}">{{ $value->merchent->username}}</a>
                            </td>

                            <td>{{ $value->merchent->pickup_phone}}</td>
                            <td>{{ $value->merchent->pickup_address}}</td>

                            <td>{{ $value->receiver->name}}</td>
                            <td>{{ $value->receiver->phone}}</td>
                            <td>{{ $value->receiver->address}}</td>
                            <td>
                                Tk. {{$value->cash_collection }} Cash Collection<br>
                                Tk. {{$value->delivery_charge }} Charge
                            </td>
                            <td>
                                @if($value->payment_status == 1)

                                    <span class="badge badge-info"><i class=" fa fa-spinner"></i>Pending</span>
                                @else
                                <span class="badge badge-success"><i class=" fa fa-check"></i>Settle</span>
                                @endif
                            </td>
                            <td>
                                <span class="{{$value->status->color_code}}"> <i class="{{$value->status->icon}}"></i>
                                    {{ $value->status->status}}</span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button " class="btn btn btn-sm btn-danger">
                                        Action</button>
                                    <button type="button" class="btn btn btn-sm btn-danger dropdown-toggle dropdown-icon"
                                        data-toggle="dropdown">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">


                                        @if (Auth::guard('admin')->user()->can('booking.edit') ||
                                        Auth::guard('admin')->user()->can('booking.delete') ||
                                        Auth::guard('admin')->user()->can('booking.view'))
                                        @if (Auth::guard('admin')->user()->can('booking.view'))
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{ route('admin.bookings.show', $value->id) }}">
                                            View
                                        </a>
                                        @endif

                                        @if($value->delivery_status ==11 && $value->delivery_status !=13 && $value->delivery_status !=9 && $value->payment_status == 1 && $role == 'Superadmin')
                                           <div class="dropdown-divider"></div>
                                           <button class="dropdown-item" onclick="changeStatus('{{$value->id}}')"
                                               data-toggle="modal" data-target="#modal-default">Change Status</button>
                                        @endif

                                        @if($value->delivery_status !=11 && $value->delivery_status !=13 && $value->delivery_status !=10 &&
                                        $value->delivery_status !=9 && $role == 'Superadmin')
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item " href="{{ route('admin.bookings.edit', $value->id) }}">
                                            Edit
                                        </a>
                                        @if (Auth::guard('admin')->user()->can('booking.delete'))
                                        <div class="dropdown-divider"></div>
                                        <a onclick="return confirm('Do you really want to delete this booking?');" class="dropdown-item" href="{{ route('admin.bookingDelete', $value->id) }}">
                                            Delete
                                        </a>
                                        <form  id="delete-form-{{ $value->id }}"
                                            action="{{ route('admin.bookings.destroy', $value->id) }}" method="POST"
                                            style="display: none;">
                                            @method('DELETE')
                                            @csrf
                                        </form>
                                        @endif

                                        @endif


                                        @if($role == 'Merchent' && $value->delivery_status ==1)
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item " href="{{ route('admin.bookings.edit', $value->id) }}">
                                            Edit</a>


                                        @if (Auth::guard('admin')->user()->can('booking.delete'))
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item"
                                            href="{{ route('admin.bookings.destroy', $value->id) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $value->id }}').submit();">
                                            Delete
                                        </a>
                                        <form id="delete-form-{{ $value->id }}"
                                            action="{{ route('admin.bookings.destroy', $value->id) }}" method="POST"
                                            style="display: none;">
                                            @method('DELETE')
                                            @csrf
                                        </form>
                                        @endif




                                        @endif


                                        @else

                                        @endif

                                        @if($value->delivery_status !=11 && $value->delivery_status !=13 && $value->delivery_status !=10 &&
                                        $value->delivery_status !=9)
                                        @if($role == 'Superadmin')
                                        <div class="dropdown-divider"></div>
                                        <button class="dropdown-item" onclick="changeStatus('{{$value->id}}')"
                                            data-toggle="modal" data-target="#modal-default">Change Status</button>
                                        @endif
                                        @endif

                                        @if($value->delivery_status !=11 && $value->delivery_status !=13 && $value->delivery_status !=10 &&
                                        $value->delivery_status !=9 && $value->delivery_status !=1 &&
                                        $value->issue_id ==null)

                                        <div class="dropdown-divider"></div>
                                        <button class="dropdown-item" onclick="raiseIssue('{{$value->id}}')"
                                            data-toggle="modal" data-target="#modal-issue">Issue Raise</button>

                                        @endif


                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>SL</th>
                            <th>Creation Date</th>
                            <th>Tracking ID</th>
                            <th>Merchent Name</th>
                            <th>Merchent Phone</th>
                            <th>Merchent Address</th>
                            <th>Receiver Name</th>
                            <th>Receiver Phone</th>
                            <th>Receiver Address</th>
                            <th>Payment Info</th>
                            <th>Payment Status</th>
                            <th>Booking Status</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                </table>



                <!-- /.col -->

            </div>
            <div class="card-footer">

            </div>
        </div>
        </form>
    </div>
    <!-- /.col-->
</div>
@endsection


@section('scripts')

<script>

function checkstatus(){



}

$('.buttonclick').click(function(){
        var allVals = [];
        $('#example1 :checked').each(function(i){
            allVals.push($(this).val());
            alert('hello');
        });
        console.log(allVals);
      });
function raiseIssue(booking_id) {
    $('#bid').val(booking_id);
}
</script>
<div class="modal fade" id="modal-issue">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Issue creation </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('issueRaiseUpdate')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card card-outline card-info">
                        <div class="card-body">
                            <input type="hidden" value="" id="bid" name="booking_id">
                            <div class="form-group row">
                                <label class="col-sm-3">Status </label>
                                <div class="col-sm-8">
                                    <select name="issue_id" class="form-control select2">
                                        <option value="" selected="" disabled="">--Select Issue -- </option>
                                        @foreach($issue as $key => $value)
                                        <option value="{{$value->id}}">{{$value->title}} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row driver">
                                <label class="col-sm-3">Issue Note</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" name="issue_note"
                                        placeholder="Issue Note"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="form-group row">
                                <button type="submit" class=" btn btn-danger"><i class="fa fa-save"></i> Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@endsection
