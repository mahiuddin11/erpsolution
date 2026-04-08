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
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Pick UP/ Collection Date</th>
                    <th>Today</th>
                    <th>Ageing (days)</th>
                    <th>Order Number</th>
                    <th>Marchent Name</th>
                    <th>Customer Info</th>
                    <th class="no-print">Pickup Rider</th>
                    <th>Delivery Rider</th>
                    <th>Payment Type</th>

                    <th>Parcel Amount</th>
                    <th class="no-print">TRC Charge</th>
                    <th class="no-print">Amount (After TRC)</th>
                    <th>Status</th>
                    <th class="no-print">Delivery Date</th>
                    <th class="no-print">Returned Date</th>

                </tr>
            </thead>
            <tbody>
                @php
                    $tparcel=0;
                    $ttrccharge=0;
                    $taftercharge=0;
               @endphp
                @foreach($result as $key => $value)
                @php
                    $tparcel+=$value->cash_collection;
                    $ttrccharge+=$value->delivery_charge;
                    $taftercharge+=$value->cash_collection - $value->delivery_charge;
                @endphp
                <tr>
                    <td>{{ $key+1}}</td>
                    <td nowrap>
                        @if(!empty($value->pickup_driver_id))
                        {{ date('Y-m-d',strtotime($value->pickup_date))}}
                        @endif
                    </td>
                    <td nowrap>{{ date('Y-m-d') }}</td>
                    <td>
                        @if(!empty($value->pickup_date))
                        {{ round((strtotime($value->pickup_date) - time()) / 86400) }}
                        @endif
                    </td>
                    <td><a href="{{ route('admin.bookings.show', $value->id) }}">{{ $value->voucher_id}}</a></td>
                    <td><a
                            href="{{ route('merchent.profile', $value->merchent_id) }}">{{ $value->merchent->username}}</a>
                    </td>
                    <td>Name: {{ $value->receiver->name}}<br>
                        Phone: {{ $value->receiver->phone}}<br>
                        Address: {{ $value->receiver->address}}
                    </td>
                    <td class="no-print">
                        {{$value->pdriver->username ?? 'N/A'}}
                    </td>
                    <td>
                        {{$value->ddriver->username ?? 'N/A'}}
                    </td>
                    <td>
                        @if($value->cash_collection)
                        COD
                        @else
                        NO COD
                        @endif
                    </td>


                    <td>{{number_format($value->cash_collection,2)}}</td>
                    <td  class="no-print">{{number_format($value->delivery_charge,2)}}</td>
                    <td  class="no-print">{{number_format($value->cash_collection - $value->delivery_charge,2)}}</td>
                    <td>
                        <span class="{{$value->status->color_code}}"> <i class="{{$value->status->icon}}"></i>
                            {{ $value->status->status}}</span>
                    </td>

                    <td class="no-print">
                        @if($value->delivery_status == 11)
                        {{date('Y-m-d',strtotime($value->status->created_at))}}
                        @endif
                    </td>

                    <td class="no-print"></td>


                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td  class="no-print"></td>
                    <td></td>

                    <td class="text-right">Total</td>
                    <td>{{number_format($tparcel,2)}}</td>
                    <td class="no-print">{{number_format($ttrccharge,2)}}</td>
                    <td class="no-print">{{number_format($taftercharge,2)}}</td>
                    <td></td>
                    <td class="no-print"></td>
                    <td class="no-print"></td>
                </tr>

            </tfoot>

        </table>
    </div>
</div>
