@extends('backend.layouts.master')

@section('title')
Driver - Driver Report
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
                <h1 class="m-0">Driver Report</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <!-- <li class="breadcrumb-item"><a href="{{ route('admin.couriers.index') }}">All Courier</a> -->
                    </li>
                    <li class="breadcrumb-item active"><span>Driver Report</span></li>
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
                                <label>Driver </label>
                                <select class="form-control select2  driver_id" style="width: 100%;">
                                    <option valu="all" selected>All</option>
                                    @foreach($driver as $key => $value)
                                    <option value="{{ $value->id}}">{{ $value->username }}
                                        [{{$value->registration_number}}]</option>
                                    @endforeach
                                </select>
                                @error('company')
                                <span class="error text-red text-bold"> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status </label>
                                <select multiple="multiple"  class=" select2  status_id" style="width: 100%;">
                                    <option valu="all" selected>All</option>
                                    @foreach($status as $key => $value)
                                    <option value="{{ $value->id}}">{{ $value->status }}</option>
                                    @endforeach
                                </select>
                                @error('company')
                                <span class="error text-red text-bold"> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
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
    listResult($('.daterange').val(), $('.driver_id').val(), $('.status_id').val());

    $('.daterange').on('change', function() {
        listResult($('.daterange').val(), $('.driver_id').val(), $('.status_id').val());
    });
    $('.driver_id').on('change', function() {
        listResult($('.daterange').val(), $('.driver_id').val(), $('.status_id').val());
    });
    $('.status_id').on('change', function() {
        listResult($('.daterange').val(), $('.driver_id').val(), $('.status_id').val());
    });

    function listResult(daterange, driver_id, status_id) {
        $.ajax({
            type: 'get',
            url: "{{ route('report.driver.result') }}",
            data: {
                daterange: daterange,
                driver_id: driver_id,
                status_id: status_id,
            },
            success: function(data) {
                $('#load_data').empty().html(data.html);
            }
        })
    }
});
</script>












@endsection