@extends('backend.layouts.master')
@section('title')
    Report - {{ $title }}
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
                        Report </h1>
                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">




        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header no-print">
                    <h3 class="card-title">Stock Report</h3>
                    <a onclick="window.print()" target="_blank" class="btn btn-default float-right my-2 no-print"><i
                            class="fas fa-print"></i>
                        Print</a>
                    <div id="tableActions" class=" float-right my-2 no-print"></div>
                </div>

                <div class="card-body">

                    <div class="invoice p-3 mb-3">
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table  table-bordered">
                                    <tr>

                                        <td style="text-align: center">
                                            @if (isset($companyInfo->logo))
                                                <a href="{{ route('home') }}">
                                                    <img width="200px"
                                                        src="{{ asset('/backend/logo/' . $companyInfo->logo) }}"
                                                        style="" alt="">
                                                </a>
                                            @endif
                                        </td>

                                    </tr>
                                </table>
                                <table id="datatablexcel" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Branch</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                        @endphp
                                        @foreach ($StockDetails as $item)
                                            @if ($item->low_stock >= $item->quantity)
                                                <tr>
                                                    <td>{{ $i++ }}</td>

                                                    <td>{{ $item->bname . ' - ' . $item->bcode }}</td>
                                                    <td>{{ $item->pcode . ' - ' . $item->pname }}</td>
                                                    <td style="color: red">
                                                        {{ $item->quantity }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>


                                </table>
                            </div>





                            <div class="col-md-4  float-left">
                                <br>
                                <br>

                                <p>Prepared By:_____________<br />
                                    Date:____________________
                                </p>
                            </div>
                            <div class="col-md-6 text-center">
                            </div>
                            <div class="col-md-2  ">
                                <br>
                                <br>
                                <p>Approved By:________________<br />
                                    Date:_________________</p>
                            </div>

                            <hr>


                            <div class="col-md-12 bg-success" style="text-align: center">
                                Thank you for choosing Water Technology Ltd Company products.
                                We believe you will be satisfied by our services.
                            </div>
                            <!-- /.col -->



                        </div>
                        <!-- Table row -->

                    </div>

                </div>
            </div>
        </div>


        <!-- /.col-->
    </div>

    <script>
        function getProductList(cat_id) {
            if (cat_id == '' || cat_id == null || cat_id == 0) {
                return false;
            }
            $.ajax({
                "url": "{{ route('inventorySetup.purchase.getProductList') }}",
                "type": "GET",
                cache: false,
                data: {
                    "_token": "{{ csrf_token() }}",
                    cat_id: cat_id
                },
                success: function(data) {
                    $('#productID').select2();
                    $('#productID option').remove();
                    $('#productID').append($(data));
                    $("#productID").trigger("select2:updated");
                }
            });
        }

        $(document).ready(function() {
            $('#stocksub').on('submit', function() {
                var product_id = $(".proName").find('option:selected').val();
                var prhtml = '<input type="hidden" name="product_id" value="' + product_id + '" />';
                $(this).append(prhtml);
            })
        })
    </script>
@endsection
@section('scripts')
    @include('backend.pages.reports.excel')
@endsection
