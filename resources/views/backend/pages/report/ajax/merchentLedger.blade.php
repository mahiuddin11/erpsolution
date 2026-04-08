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





        @if($merchent_id == 'all')



        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Merchent</th>
                    <th class="text-right">Payable</th>
                    <th class="text-right">Paid</th>
                    <th class="text-right">Balance</th>
                </tr>

            </thead>
            <tbody>


                <?php

                $tpayble = 0;
                $tpaid = 0;
                $tbalance = 0;
                ?>
                @foreach($reuslt as $key => $value)
                <?php
                $tpayble += $value->tpayable;
                $tpaid += $value->tpaid;
                ?>
                <tr>
                    <td>{{ $key+1}}</td>
                    <td><a
                            href="{{route('merchent.profile',$value->merchent->id) }}">{{ $value->merchent->username}}</a>
                    </td>
                    <td class="text-right">{{number_format($value->tpayable,2)}}</td>
                    <td class="text-right">{{number_format($value->tpaid,2)}}</td>
                    <td class="text-right">{{number_format($tpayble - $tpaid,2)}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-right" colspan="2">Total</th>
                    <th class="text-right">{{number_format($tpayble,2)}}</th>
                    <th class="text-right">{{number_format($tpaid,2)}}</th>
                    <th class="text-right">{{number_format($tpayble - $tpaid)}}</th>
                </tr>
            </tfoot>
        </table>

        @else

        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Merchent</th>
                    <th>Date</th>
                    <th>Booking Id</th>
                    <th>Payment Type</th>
                    <th>Movement</th>
                    <th class="text-right">Parchel Amount(+)</th>
                    <th class="text-right">Delivery Charge(-)</th>
                    <th class="text-right">Merchent Payment(-)</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @php
                $opening = 0
                @endphp

                @if(!empty($opening->totalbalacne))


                @php
                $opening = $opening->totalbalacne;
                @endphp
                <tr>
                    <td colspan="8" align="right">Opening Balance </td>
                    <td align="right">{{number_format($opening->totalbalacne,2)}}</td>
                </tr>
                @endif;
                <?php
                $tpayble = 0;
                $tpaid = 0;
                $tbalance = 0;

                $mpayment = 0;
                ?>
                @foreach($reuslt as $key => $value)
                <?php
                $tpayble += $value->payable;
                $tpaid += $value->paid;
                ?>
                <tr>
                    <td>{{ $key+1}}</td>
                    <td><a
                            href="{{route('merchent.profile',$value->merchent->id) }}">{{ $value->merchent->username}}</a>
                    </td>
                    <td>{{$value->date}}</td>
                    <td>
                        @if($value->receipt_status == 1)
                        {{substr($value->voucher_id,0,20) ?? substr($value->booking_id,0,20)}} ....
                        @else
                        {{ $value->voucher_id  ??  $value->booking_id }}
                        @endif
                    </td>
                    <td>{{$value->payment_type ?? 'N/A'}}</td>
                    <td>{{$value->movement}}</td>
                    <td class="text-right">{{number_format($value->payable,2)}}</td>
                    <td class="text-right">{{number_format($value->paid,2)}}</td>
                    <td class="text-right">
                        @if($value->receipt_status == 1)
                        @php
                        $mpayment+=$value->paid;
                        @endphp
                        {{number_format($value->paid,2)}}

                        @endif
                    </td>
                    <td class="text-right">{{number_format($tpayble - $tpaid+$opening,2)}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-right" colspan="6">Total</th>
                    <th class="text-right">{{number_format($tpayble,2)}}</th>
                    <th class="text-right">{{number_format($tpaid,2)}}</th>
                    <th class="text-right">{{number_format($mpayment,2) }}</th>
                    <th class="text-right">{{number_format($tpayble - $tpaid+$opening,2)}}</th>
                </tr>
            </tfoot>
        </table>
        <!-- /.col -->

        @endif
    </div>

    <div class="card-footer">

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <table>
                    <tr>
                        <td>Total Parcel Amount:</td>
                        <td>:</td>
                        <td>{{number_format($tpayble,2)}}</td>
                    </tr>
                    <tr>
                        <td>Total Delivery Charge:</td>
                        <td>:</td>
                        <td>{{ number_format($tpaid - $mpayment,2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Merchent Payment:</td>
                        <td>:</td>
                        <td><u>{{number_format($mpayment,2) }}</u></td>
                    </tr>

                    <tr>
                        <td>Current Balance:</td>
                        <td>:</td>
                        <td><u>{{number_format($tpayble - $tpaid+$opening,2)}}</u></td>
                    </tr>

                </table>
            </div>
            <div class="col-md-4"></div>

        </div>


    </div>




</div>