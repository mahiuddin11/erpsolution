@extends('backend.layouts.master')
@section('title')
Production - {{ $title }}
@endsection
@section('navbar-content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"> Production </h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('production.production.index'))
                    <li class="breadcrumb-item"><a href="{{ route('production.production.index') }}">Production
                            List</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Production</span></li>
                </ol>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</div>
@endsection
@section('admin-content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Add New Production</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('production.production.index'))
                    <a class="btn btn-default" href="{{ route('production.production.index') }}"><i
                            class="fa fa-list"></i>
                        Production List</a>
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
                <div class="col-md-12 mb-3 bg-black card">
                    <div class="card-body " style="text-align: center">

                        <h5 style="color: white;">You can not edit after completing the process. So Save the Data
                            carefully.</h5>
                    </div>
                </div>
                <form class="needs-validation" method="POST" action="{{ route('production.production.store') }}">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label>Production Number :</label>
                            <input class="bg-green form-control" readonly=""
                                style="padding: 5px; font-weight : bold; width: 100%" value="{{ $ProductionCode }} ">
                            <input type="hidden" name="productionCode" class="form-control" id=""
                                value="{{ $ProductionCode }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Date:</label>
                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                <input type="text" name="date" data-toggle="datetimepicker"
                                    value="{{ date('YYYY-mm-dd') }}" class="form-control datetimepicker-input"
                                    data-target="#reservationdate" />
                                <div class="input-group-append" data-target="#reservationdate"
                                    data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            @error('date')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Branch * :</label>
                            <select class="form-control select2" id="branch_id" name="branch_id"
                                onchange="getProductListForThisBranch(this.value)" required>
                                <option selected disabled value="">--Select Branch--</option>
                                @foreach ($branch as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->branchCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3 productlist" id="invid" style="display: none">
                            <label for="validationCustom01">Products * :</label>
                        </div>
                        <div class="col-md-12 mb-3 ">
                            <label for="validationCustom01">To Product * :</label>
                            <select class="form-control select2" onchange="toProductDetails(this.value)"
                                name="to_product_id" required>
                                <option selected disabled value="">--Select--</option>
                                @foreach($products as $key => $value)
                                <option value="{{$value->id}}">{{ $value->productCode.' - '.$value->name}}</option>
                                @endforeach
                            </select>
                            @error('product_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>


                    </div>

            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Calculation</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="form-row">
                    <div class="col-md-12 ">
                        <label>Current Stock :</label>
                        <input class="form-control currentStock" id="a1" readonly="">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12 ">
                        <label for="validationCustom01">Conversion * :</label>
                        <select class="form-control select2" id="conid" name="conversion_id"
                            onchange="hideshowgame(this.value)" required>
                            <option selected disabled value="">--Select--</option>
                            @foreach($conversion as $key => $value)
                            <option value="{{$value->id}}">{{$value->rate }}</option>
                            @endforeach
                        </select>
                        <span id="mySpan" style="display: none; color: red">Please select convertion rate.</span>
                        @error('conversion_id')
                        <span class=" error text-red text-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12 ">
                        <label>Convert Quantity * :</label>
                        <input class="form-control" name="deduct_quantiry" oninput="calculator()" id="a2"
                            autocomplete="off" required>
                    </div>
                    <span id="mySpanStock" style="display: none; color: red">Conversion quantity can be greater than
                        stock
                        quantity.</span>
                    <span id="qtyValidation" style="display: none; color: red">Conversion quantity cannot be less than
                        1!.</span>
                </div>
                <div class="form-row">
                    <div class="col-md-12 ">
                        <label>Remaining Quantity * :</label>
                        <input class="form-control total" id="total" readonly autocomplete="off">
                    </div>
                </div>

                <input type="hidden" class="totalQty">
                <input type="hidden" class="avgPrice">
                <input type="hidden" class="eachPrice">
                <div class="form-row">
                    <div class="col-md-12 ">
                        <label>Purchase Price * :</label>
                        <input class="form-control purcsePrice" name="purchases_price" readonly autocomplete="off">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12 ">
                        <label>Sale Price * :</label>
                        <input class="form-control new_sale_price" readonly name="sale_price" autocomplete="off"
                            required>
                    </div>
                </div>

                <br>
                <div class="form-row">
                    <div class="col-md-12 ">
                        <button class="btn btn-info " type="submit" id="myBtn11">
                            <i class="fa fa-save"></i>&nbsp; &nbsp; Save
                        </button>

                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
    </form>
</div>
<!-- /.col-->
<script>
    function getProductListForThisBranch(branch_id) {
        $.ajax({
            url: "/admin/getProductListForThisBranchWise/", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                branch_id: branch_id
            },
            success: function (val) {
                $("#invid").show()
                let html = `

       <div class="form-group">
           <label>Stock Poducts *</label>
           <select name="product_id" class="form-control abc productslist select2 productlist"
               onchange="getCurrentStockAndRate(this.value)">
               ${val}
           </select>
           <span style="color :red; " id="showaDueAmount"></span>

       </div>
       `;
                $('.productlist').html(html);
                $('.productslist').select2();
            },

        });
    }

    function hideshowgame(conversionRate){
        if(!conversionRate){
            $('#mySpan').show();
            }else{
            $('#mySpan').hide();
        }
    }
</script>
<script>
    function getCurrentStockAndRate(product_id) {
        let branch_id = $("#branch_id").val();

        $.ajax({
            url: "/admin/getCurrentStockAndRateofThisProduct/", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                product_id: product_id,
                branch_id: branch_id
            },
            success: function (val) {
                $(".currentStock").val(val.quantity)
            },

        });
    }
    function toProductDetails(product_id) {


        $.ajax({
            url: "/admin/getToProPrice/", // path to function
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
                product_id: product_id,
            },
            success: function (val) {
       
                $(".new_sale_price").val(val.sale_price)
            },

        });
    }
</script>

<script>
    $(document).ready(function () {
        $("#invid").change(function () {
            let pid = $(".abc").val();

            $.ajax({
                url: "/admin/purchaseDetailsByProduct/", // path to function
                method: "GET",
                data: {
                    "_token": "{{ csrf_token() }}",
                    pid: pid,
                },
                success: function (val) {
                    let totalQty = val.ttlqty;
                    let avgPrice = val.avg;
                    // let eachPrice = avgPrice / totalQty;
                    let sale_price = val.sale_price;
                    $(".totalQty").val(totalQty)
                    $(".avgPrice").val(avgPrice)
                    $(".sale_price").val(sale_price)
                    // $(".eachPrice").val(eachPrice)
                    // $(".currentStock").val(val.quantity)
                },
            });
        });
    });
</script>

<script>
    function calculator() {
       // let eachPrice = $(".eachPrice").val();

        let conversionRate = $("#conid").val();
        var conversionValue = $('#conid option:selected').text();
        var avgPrice = $('.avgPrice').val();

        if(!conversionRate){
            $('#mySpan').show();
        }else{
           $('#mySpan').hide();
        }
        var stock = document.getElementById("a1").value;
        var stock = parseInt(stock, 10);
        var quantity = document.getElementById("a2").value;

        let qtyCheck = quantity * conversionValue;
        // console.log(stock);
        // console.log(conversionValue);
   

        if(parseFloat(qtyCheck) < 1){
            document.getElementById("myBtn11").disabled = true;
           $('#qtyValidation').show();
        }else{
            document.getElementById("myBtn11").disabled = false;
           $('#qtyValidation').hide();
        }


        if(parseInt(stock) < parseInt(quantity)){
            $('#mySpanStock').show();
        }else{
            $('#mySpanStock').hide();
        }

        var quantity = parseInt(quantity, 10);
        let eachPrice = avgPrice / conversionValue;
                        $(".eachPrice").val(eachPrice)
        if (parseInt(stock) < parseInt(quantity)) {
            $("#a2").val(''); $(".total").val('');
        } else {
            let calculatorulateValue = eachPrice * parseInt(quantity);
            var newcalculatorulateValue = parseFloat(calculatorulateValue).toFixed(2)
            $(".purcsePrice").val(newcalculatorulateValue);
            var total = stock - quantity;
            document.getElementById("total").value = total;
        }
    }
</script>
@endsection