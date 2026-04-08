@extends('backend.layouts.master')
@section('title')
inventory - {{$title}}
@endsection

@section('styles')
<style>
    .bootstrap-switch-large {
        width: 200px;
    }
</style>
@endsection

@section('navbar-content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    Inventory </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('home') }}">Dashboard</a></li>
                    @if(helper::roleAccess('inventorySetup.purchase.index'))
                    <li class="breadcrumb-item"><a href="{{route('inventorySetup.purchase.index') }}">Purchase
                            Manage</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Purchase (PV) List</span></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection

@section('admin-content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Purchase (PV) List</h3>
                <div class="card-tools">
                    @if(helper::roleAccess('inventorySetup.purchase.pvcreate'))
                    <a class="btn btn-default" href="{{ route('inventorySetup.purchase.pvcreate') }}"><i
                            class="fas fa-plus"></i>Add New</a>
                    @endif
                    <span id="buttons"></span>
                    <a class="btn btn-tool btn-default" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </a>
                    <a class="btn btn-tool btn-default" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table id="systemDatatable" class="display table-hover table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Invoice No</th>
                                <th>Date</th>
                                <th>Branch</th>
                                <th>Supplier</th>
                                <th>Payment Type</th>
                                <th>Subtotal</th>
                                <th>Discount</th>
                                <th>Grand Total</th>
                                <th>Status</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>SL</th>
                                <th>Invoice No</th>
                                <th>Date</th>
                                <th>Branch</th>
                                <th>Supplier</th>
                                <th>Payment Type</th>
                                <th>Subtotal</th>
                                <th>Discount</th>
                                <th>Grand Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">

            </div>
        </div>
    </div>
    <!-- /.col-->
</div>

<script>
    $(document).on('click', '.pvaction', function () {

        let updatestatus = () => {
            let statusId = $(this).attr('statusId');
            let id = $(this).attr('rowID');
            $.ajax({
                url: "{{route('inventorySetup.purchase.pvcloseopen')}}",
                method: "post",
                data: {
                    "_token": "{{csrf_token()}}",
                    status: statusId,
                    id: id
                },
                success: function (data) {
                    location.reload();
                }
            })
        }

        alertMessage.formalConfirm('Are You sure', updatestatus);


    })

</script>

@endsection
@section('scripts')
@include('backend.pages.inventories.purchase_pv.script')
@endsection