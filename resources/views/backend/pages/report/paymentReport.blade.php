@extends('backend.layouts.master')

@section('title')
Report - Return Report
@endsection

@section('styles')
<style>
table tr th {
    margin: 2px !important;
    padding: 2px !important;
}

table tr td {
    margin: 2px !important;
    padding: 2px !important;
}
</style>
@endsection
@section('navbar-content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Payment Report</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <!-- <li class="breadcrumb-item"><a href="{{ route('admin.couriers.index') }}">All Courier</a> -->
                    </li>
                    <li class="breadcrumb-item active"><span>Payment Report</span></li>
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
        <form action="{{ route('admin.couriers.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card card-outline card-info no-print">
                <div class="card-body">
                    <div class="row no-print">
                        <div class="box-header with-border" style="cursor: pointer;">
                            <h6 class="box-title">
                                <i class="fa fa-filter" aria-hidden="true"></i> Filters
                            </h6>
                        </div>
                    </div>
                    <div class="row no-print">

                        @if($role == 'Superadmin')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Merchent </label>
                                <select class="form-control select2 merchent_id">
                                    <option value="All" selected>All</option>
                                    @foreach($merchent as $key => $value)
                                    <option value="{{ $value->id}}">{{ $value->username }}
                                        [{{$value->pickup_phone}}]</option>
                                    @endforeach
                                </select>
                                @error('company')
                                <span class="error text-red text-bold"> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @else 

                        <input type="hidden" class="merchent_id" value="{{$admin_id}}"/>
                        @endif
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date Range:</label>
                                <input type="text" class="form-control daterange" value="" id="reservation" />
                                @error('company')
                                <span class="error text-red text-bold"> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="button" onclick="window.print()" class="btn btn-sm btn-danger"><i
                                        class="fa fa-print"></i>Print</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="load_data"></div>



        </form>
    </div>
    <!-- /.col-->
</div>

@endsection

@section('scripts')

<script type="text/javascript">
$(document).ready(function() {
    listResult($('.merchent_id').val(), $('.daterange').val());

    $('.merchent_id').on('change', function() {
        listResult($('.merchent_id').val(), $('.daterange').val());
    });
    $('.daterange').on('change', function() {
        listResult($('.merchent_id').val(), $('.daterange').val());
    });

    function listResult(merchent_id, date_range) {
        $.ajax({
            type: 'get',
            url: "{{ route('report.payment.result') }}",
            data: {
                merchent_id: merchent_id,
                date_range: date_range,
               
            },
            success: function(data) {
                $('#load_data').empty().html(data.html);
            }
        })
    }
});
</script>












@endsection