<div class="card card-outline card-info" style="width: 100%!important;overflow-y:scroll; ">
    <div class="card-body">
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">

            </div>

            <div class="col-sm-4 invoice-col">
                <center>
                    <address>
                        <strong style="font-size: 40px">The Rapid Crew</strong><br>
                         House: 5, Road:10,Block:B,Section-13<br>
                        Mirpur-1216, Dhaka Division, Bangladesh<br>
                        Phone: 01682747714,01673520304<br>
                        Email: jahidsalman977@gmail.com<br>
                        Website: www.therapidcrew.net<br>
                        Facebook: www.fb.com/TheRapidCrew<br>

                      </address>
            </center>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">

            </div>
            <!-- /.col -->

            <!-- /.col -->
        </div>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Order Number</th>
                    <th>Marchent Name</th>
                    <th>Customer Info</th>
                    <th>Rider Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($result as $key => $value)
                <tr>
                    <td>{{ $key+1}}</td>
                    <td><a href="{{ route('admin.bookings.show', $value->id) }}">{{ $value->booking->voucher_id}}</a>
                    </td>
                    <td><a
                            href="{{ route('merchent.profile', $value->merchent_id) }}">{{ $value->merchent->username ?? ''}}</a>
                    </td>
                    <td>Name: {{ $value->receiver->name}}<br>
                        Phone: {{ $value->receiver->phone}}<br>
                        Address: {{ $value->receiver->address}}
                    </td>
                    <td>
                        {{$value->driver->username ?? ''}}
                    </td>
                    <td>
                        @if($value->return_status == 0)
                        <span class="badge badge-info">Pending</span>
                        @elseif($value->return_status == 1)
                        <span class="badge badge-success">Received</span>
                        @elseif($value->return_status == 2)
                        <span class="badge badge-warning">Assain Driver</span>
                        @elseif($value->return_status == 3)
                        <span class="badge badge-primary">Delivered</span>
                        @else
                        <span class="badge badge-warning">Payment Settled</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
