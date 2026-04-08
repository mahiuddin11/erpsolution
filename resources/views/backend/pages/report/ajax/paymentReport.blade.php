<div class="card card-outline card-info">
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
                Date From {{$fromdate}} To {{$todate}}
              </address>
            </center>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">

            </div>
            <!-- /.col -->

            <!-- /.col -->
        </div>

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
                    <th>Payment Status</th>
                    <th>Payment Info</th>

                </tr>
            </thead>
            <tbody>

                @foreach($result as $key => $value)
                <tr>
                    <td>{{ $key+1}}</td>
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
                        @if($value->payment_status == 1)
                            <span class="badge badge-info"><i class=" fa fa-spinner"></i>Pending</span>
                        @else
                        <span class="badge badge-success"><i class=" fa fa-check"></i>Settle</span>
                        @endif
                    </td>

                    <td>
                        Tk. {{$value->cash_collection }} Cash Collection<br>
                        Tk. {{$value->delivery_charge }} Charge
                    </td>

                </tr>
                @endforeach



            </tbody>

        </table>
    </div>
</div>
