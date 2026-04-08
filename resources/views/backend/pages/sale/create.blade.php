@extends('backend.layouts.master')
@section('title')
inventory - {{ $title }}
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
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('sale.sale.index'))
                    <li class="breadcrumb-item"><a href="{{ route('sale.sale.index') }}">Sale</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Sale List</span></li>
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
                <h3 class="card-title">Sale Invoice</h3>
            </div>
            <div class="card-body">
                <form class="needs-validation" method="POST" action="{{ route('sale.sale.store') }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-2 mb-3">
                            <label for="validationCustom01">Invoice Number :</label>
                            <input class="bg-green form-control" readonly=""
                                style="padding: 5px; font-weight : bold; width: 100%" value="{{ $invoice_no }} "
                                for="validationCustom01">
                            <input type="hidden" name="invoice_no" class="form-control" id="" value="{{ $invoice_no }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="validationCustom01">Po Number :</label>
                            <input type="text" name="po_invoice" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Po Date:</label>
                            <div class="input-group date" id="reservationdate1" data-target-input="nearest">
                                <input type="text" name="po_date" data-toggle="datetimepicker"
                                    value="{{ date('YYYY-mm-dd') }}" class="form-control datetimepicker-input"
                                    data-target="#reservationdate1" />
                                <div class="input-group-append" data-target="#reservationdate1"
                                    data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            @error('date')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
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

                        <div class="col-md-2 mb-3">
                            <label for="validationCustom01">Branch * :</label>
                            <select class="form-control select2" id="branch_id" name="branch_id">
                                <option selected disabled value="">--Select Branch--</option>
                                @foreach ($branch as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->branchCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                            <span class="error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label for="validationCustom02">Sub-Warehouse * :</label>
                            <select class="form-control select2" id="sub_warehouse_id" name="sub_warehouse_id" >
                                <option selected disabled value="">--Select Sub-Warehouse--</option>
                                @foreach ($wearhouses as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->branchCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('sub_warehouse_id')
                            <span class="error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        

                        {{-- <div class="col-md-2 mb-3">
                            <label for="validationCustom01">Customer * :</label>
                            <select class="form-control select2" name="customer_id" id="customer_id">
                                <option selected disabled value="">--Select Customer--</option>
                                @foreach ($customer as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->customerCode . ' - ' . $value->co_name }}
                                </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div> --}}

                        <div class="col-md-2 mb-3">
                            <label for="ledger_id">Ledger * : 
                                <button type="button" class="btn btn-sm btn-primary ml-2" data-toggle="modal"
                                data-target="#addCustomerModel">
                                +
                            </button>
                            </label>
                            <select class="form-control select2" name="ledger_id" id="ledger_id">
                                <option selected disabled value="">--Select Ledger--</option>
                                <x-account :setAccounts="$ledgers" />
                            </select>
                            @error('ledger_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="validationCustom01">Balance * :</label>
                            <input type="text" id="customer_currentBalance" class="form-control" readonly>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="validationCustom01">Payment Type * :</label>
                            <br>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn bg-olive">
                                    <input type="radio" name="payment_type" value="Due"
                                        onchange="getCustomerBalance('Due')" checked id="option3" autocomplete="off"> Due
                                </label>
                                {{-- <label class="btn bg-olive active">
                                    <input type="radio" name="payment_type" value="Cash"
                                        onchange="getCustomerBalance('Cash')" id="option1" autocomplete="off"> Cash
                                </label> --}}
                                {{-- <label class="btn bg-olive">
                                    <input type="radio" name="payment_type" value="Deposit"
                                        onchange="getCustomerBalance('Deposit')" id="option2" autocomplete="off">
                                    Deposit
                                </label> --}}
                            
                            </div>
                            <input type="hidden" id="paymentType">
                            <input type="hidden" id="expireData">
                        </div>
                        <table class=" table-responsive table table-bordered">
                            <tr>
                                <td>
                                    <div class="col-md-9 float-left ">
                                        Sales Item
                                    </div>
                                    <div class="col-md-3 float-right">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 10px!important;">
                                    <div class="col-md-12">
                                        <div class="col-md-12 float-left">
                                            <div class="panel panel-default">
                                                <div class="panel-body">

                                                    <table class="table table-bordered table-hover tableAddItem"
                                                        id="show_item">
                                                        <thead>
                                                            <tr>
                                                                <th nowrap style="width:15%" align="center" id="">
                                                                    <strong>Product Category <span style="color:red;">
                                                                            *</span></strong>
                                                                </th>
                                                                <th nowrap style="width:15%" align="center" id="">
                                                                    <strong>Product <span style="color:red;">
                                                                            *</span></strong>
                                                                </th>
                                                                <th nowrap style="width:11%" align="center">
                                                                    <strong>Type <span style="color:red;">
                                                                            *</span></strong>
                                                                </th> 
                                                                <th nowrap style="width:11%" align="center">
                                                                    <strong>Quantity <span style="color:red;">
                                                                            *</span></strong>
                                                                </th>
                                                                <th nowrap style="width:11%" align="center">
                                                                    <strong>Vat <span style="color:red;">
                                                                            *</span></strong>
                                                                </th>
                                                                <th nowrap style="width:12%" align="center"><strong>Unit
                                                                        Price(BDT) <span style="color:red;">
                                                                            *</span></strong></th>
                                                                <th nowrap style="width:13%" align="center">
                                                                    <strong>Total Price(BDT) <span style="color:red;">
                                                                            *</span></strong>
                                                                </th>
                                                                <th align="center" style="width:5%">
                                                                    <strong>Action</strong>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td id="product_td">
                                                                    <select onchange="getProductList(this.value)"
                                                                        class="select2 form-control catName"
                                                                        id="form-field-select-3"
                                                                        data-placeholder="Search Category">
                                                                        <option disabled selected>--- Select Category
                                                                            ---</option>
                                                                        <?php foreach ($category_info as $eachInfo) : ?>
                                                                        <option catName="{{  $eachInfo->name }}"
                                                                            value="{{ $eachInfo->id }}">
                                                                            {{  $eachInfo->name }}</option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>
                                                                <td id="product_td">
                                                                    <select class="select2 form-control proName"
                                                                        id="productID" data-placeholder="Search Product"
                                                                        onchange="getUnitPrice(this.value)">
                                                                        <option disabled selected>---Select Product---
                                                                        </option>
                                                                    </select>
                                                                    <span class="text-success purchaseprice"></span>
                                                                </td>
                                                                <td>
                                                                  <select class="select2 form-control purchasetype" id="purchasetype"
                                                                      data-placeholder="Search Product" onchange="getUnitPrice(this.value)">
                                                                     @foreach (config('purchaseType') as $key => $value)
                                                                     <option value="{{ $key }}">{{$value}}</option>
                                                                     @endforeach
                                                                  </select>
                                                                  <span class="text-success purchasetypeerror"></span>
                                                                </td>
                                                                <td>
                                                                    <input type="text" readonly class="form-control"
                                                                        style="height: 20px;" id="currentStock"
                                                                        placeholder="0">
                                                                    <input type="text" style="height: 20px;"
                                                                        class="form-control qty" id="qty"
                                                                        onkeyup="qtyPriceCal();" placeholder="0">
                                                                </td>
                                                                {{-- <td>
                                                                    <input type="number" readonly class="form-control"
                                                                        id="gas_qty" min="0" value="0" placeholder="0">
                                                                    </td> --}}
                                                                    <td>
                                                                        <input type="text"
                                                                            class="form-control text-right vat"
                                                                            id="vat" onkeyup="qtyPriceCal();"
                                                                            placeholder="0.00">
                                                                    </td>
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control text-right  unitprice"
                                                                        id="unitpice" onkeyup="qtyPriceCal();"
                                                                        placeholder="0.00">
                                                                </td>
                                                                <td><input type="text"
                                                                        class="form-control text-right ttlamount total"
                                                                        id="total" placeholder="0.00"
                                                                        readonly="readonly"></td>
                                                                <td>
                                                                    <a id="add_item" class="btn btn-info form-control"
                                                                        href="javascript:;" title="Add Item">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td align="right"><strong>Sub-Total(BDT)</strong></td>
                                                                <td align="right"><strong class=""></strong></td>
                                                                <td align="right"><strong class=""></strong></td>
                                                                <td align="
                                                                                right"><strong class="ttlqty"></strong>
                                                                </td>
                                                                <td align="right"><strong class="ttlunitprice"></strong>
                                                                </td>
                                                                <td align="right"><strong class="grandtotal"></strong>
                                                                </td>
                                                                <td align="right"><strong class=""></strong></td>
                                                            </tr>
                                                        </tfoot>


                                                        <div class="
                                                                            col-md-9">
                                                            <table class="">
                                                                <tr>
                                                                    <td>
                                                                        <textarea style="" cols="
                                                                                    157" class="form-control"
                                                                            name="narration" placeholder="Note......"
                                                                            type="text">
                                                                                    </textarea>
                                                                    </td>
                                                                    <td>
                                                                        <div class="panel  panel-default">
                                                                            <div class="panel-body">

                                                                                <table
                                                                                    class="table table-bordered table-hover ">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td nowrap align="right">
                                                                                                <strong>Total </strong>
                                                                                            </td>
                                                                                            <td align="right"> <strong
                                                                                                    id="gtoal"
                                                                                                    class="grandtotal"></strong>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td nowrap align="right">
                                                                                                <strong>Discount ( - )</strong>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div class="input-group">
                                                                                                    <input type="text"
                                                                                                        autocomplete="off"
                                                                                                        onkeyup="discountCalculation(this.value)"
                                                                                                        id="disCount"
                                                                                                        style="text-align: right"
                                                                                                        name="discount"
                                                                                                        value=""
                                                                                                        class="form-control"
                                                                                                        placeholder="0.00"
                                                                                                        oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" />
                                                                                                    <select id="discountType" class="form-control" onchange="discountCalculation(document.getElementById('disCount').value)">
                                                                                                        <option value="flat">Flat</option>
                                                                                                        <option value="percentage">Percentage</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td nowrap align="right">
                                                                                                <strong>Carrying Cost ( + )
                                                                                                </strong>
                                                                                            </td>
                                                                                            <td><input type="text"
                                                                                                    autocomplete="off"
                                                                                                    onkeyup="carrying_cost_Calculation(this.value)"
                                                                                                    id="carrying_cost"
                                                                                                    style="text-align: right"
                                                                                                    name="carrying_cost"
                                                                                                    class="form-control"
                                                                                                    placeholder="0.00"
                                                                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" />
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td nowrap align="right">
                                                                                                <strong>Labor bill ( + )
                                                                                                </strong>
                                                                                            </td>
                                                                                            <td><input type="text"
                                                                                                    autocomplete="off"
                                                                                                    onkeyup="labor_bill_Calculation(this.value)"
                                                                                                    id="labor_bill"
                                                                                                    style="text-align: right"
                                                                                                    name="labor_bill"
                                                                                                    class="form-control"
                                                                                                    placeholder="0.00"
                                                                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" />
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr id="netTotal">
                                                                                            <td nowrap align="right">
                                                                                                <strong>Net
                                                                                                    Total</strong>
                                                                                            </td>
                                                                                            <td align="right"><strong
                                                                                                    id="ntotal"
                                                                                                    class="grandtotal"></strong>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr id="account_id" class="d-none">
                                                                                            <td nowrap align="right">
                                                                                                <strong>Account <span
                                                                                                        style="color:red;">
                                                                                                        *
                                                                                                    </span></strong>
                                                                                            </td>
                                                                                            <td>
                                                                                                <select
                                                                                                    class="form-control  select2"
                                                                                                    name="account_id"
                                                                                                    require>
                                                                                                    <option selected
                                                                                                        disabled>--
                                                                                                        Select a Account
                                                                                                        --</option>
                                                                                                <x-account  :setAccounts="$account" />

                                                                                                </select>
                                                                                            </td>
                                                                                        </tr>

                                                                                        <tr class="partisals d-none">
                                                                                            <td nowrap align="right">
                                                                                                <strong>Payment ( -
                                                                                                    )<span
                                                                                                        style="color:red;">
                                                                                                        *
                                                                                                    </span></strong>
                                                                                            </td>
                                                                                            <td><input type="text"
                                                                                                    id="payment"
                                                                                                    onkeyup="paymentCalculation(this.value)"
                                                                                                    style="text-align: right"
                                                                                                    name="partialPayment"
                                                                                                    value="" readonly
                                                                                                    class="form-control"
                                                                                                    autocomplete="off"
                                                                                                    placeholder="0.00"
                                                                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" />
                                                                                            </td>
                                                                              
                                                                                            <!-- <input type="hidden" id="duePayment" style="text-align: right" name="duePayment" value="" readonly class="form-control" placeholder="0.00" /> -->
                                                                                        </tr>

                                                                                        <tr>
                                                                                            <td nowrap align="right">
                                                                                                <strong>Total
                                                                                                    Due</strong>
                                                                                            </td>
                                                                                            <td align="right"><strong
                                                                                                    id="totalDue"
                                                                                                    class="grandtotal finalDue"></strong>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>

                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </table>

                                                </div>
                                <td>
                                    <div class="clearfix"></div>
                                    <div class="clearfix form-actions float-right">
                                        <div class="col-md-offset-1 col-md-10">
                                            <button class="btn btn-info float-right" id="subMitButton" type="submit">
                                                Save
                                            </button>
                                            &nbsp; &nbsp; &nbsp;

                                        </div>
                                    </div>
                                </td>
                    </div>
            </div>

        </div>

        </td>
        </tr>

        </table>



    </div>

    </form>
</div>
</div>
</div>
<!-- /.col-->
</div>

<div class="modal fade" id="addCustomerModel" tabindex="-1" role="dialog" aria-labelledby="addCustomerModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModelLabel">Add Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="addCustomerFOrm" action="{{ route('sale.sale.quiceAddCustomer') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="validationCustom01">Company Name *:</label>
                                <input type="text" name="co_name" class="form-control" id="validationCustom01" placeholder="Company Name" value="{{ old('co_name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="validationCustom01">Customer Group Name *:</label>
                                <select name="customergroup_id" class="form-control select2">
                                    <option value="0">Not Applicable</option>
                                    @foreach ($customerGroup as $data)
                                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="validationCustom01">Contact Person:</label>
                                <input type="text" name="name" class="form-control" id="validationCustom01" placeholder="Contact Person" value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="validationCustom02">E-mail:</label>
                                <input type="text" name="email" class="form-control" id="validationCustom02" placeholder="E-mail" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="validationCustom01">Phone:</label>
                                <input type="text" name="phone" class="form-control" id="validationCustom01" placeholder="Phone" value="{{ old('phone') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="validationCustom02">Address:</label>
                                <input name="address" class="form-control" id="validationCustom02" placeholder="Address" value="{{ old('address') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="validationCustom07">Bin:</label>
                                <input name="bin" class="form-control" id="validationCustom07" placeholder="Bin" value="{{ old('bin') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">

$(document).ready(function() {
            // Supplier  Create 
            $('#addCustomerFOrm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#addCustomerModel').modal('hide');
                            $('select[name="ledger_id"]').append(
                                `<option value="${response.accounts.id}" selected>${response.accounts.account_name}</option>`
                            );
                        } else {
                            alert('Error adding Unit');
                        }
                    },
                    error: function(error) {
                        alert('An error occurred');
                    }
                });
                $("button[type='submit']").prop('disabled', false);
            });
        });

    $(document).ready(function () {
        $('#cty_size, #qty').on('input', function () {
            let ctyval = $('#cty_size').val();
            let qty = $('#qty').val();
            let gas_qty = ctyval * qty;
            $('#gas_qty').val(gas_qty);
        })

        var findqtyamount = function () {
            var ttlqty = 0;
            $.each($('.ttlqty'), function () {
                qty = $(this).val();
                qty = Number(qty);
                ttlqty += qty;
            });
            $('.ttlqty').text(parseFloat(ttlqty).toFixed(2));
        };

        var findunitamount = function () {
            var ttlunitprice = 0;
            $.each($('.ttlunitprice'), function () {
                unitprice = $(this).val();
                unitprice = Number(unitprice);
                ttlunitprice += unitprice;
            });
            $('.ttlunitprice').text(parseFloat(ttlunitprice).toFixed(2));
        };

        var findgrandtottal = function () {
            var grandtotal = 0;
            $.each($('.grandtotal'), function () {
                total = $(this).val();
                total = Number(total);
                grandtotal += total;
            });
            $('.grandtotal').text(parseFloat(grandtotal).toFixed(2));
            var paymentType = $('input[name="payment_type"]:checked').val();
            if (paymentType == "Cash") {
                $('#payment').val(parseFloat(grandtotal).toFixed(2));
                paymentCalculation(parseFloat(grandtotal));
            }
        };


        $("#add_item").click(function () {

            // start check duplicate product  
            let seaschproduct = $('#productID option:selected')[0].getAttribute("value");
            let tbody = $('tbody').find(".new_item" + seaschproduct).length;
            let tbody2 = $('tbody').find("new_item" + seaschproduct);
            console.log(tbody);

            var purchasetypeval = $('.purchasetype').find('option:selected').val();
            var purchasetypetext = $('.purchasetype').find('option:selected').text();

            if (purchasetypeval == '' || purchasetypeval == null) {
                    alertMessage.error("Please Select Type.");
                    return false;
            }

            if (tbody > 0) {
                alertMessage.error('This product already exist');
                return;
            }
            // end check duplicate product

            // var supid = $('.supid').val();
            var catId = $('.catName').val();
            var catName = $(".catName").find('option:selected').attr('catName');


            var proId = $('.proName').val();
            var proName = $(".proName").find('option:selected').attr('proName');

            //            var unit_id = $('.unitName').val();
            //            var unitName = $(".unitName").find('option:selected').attr('unitName');

            var unit = $('.unit').val();
            var qty = $('.qty').val();
            var vat = parseFloat($('#vat').val()) || 0; 

            var patmentType = $('input[name="payment_type"]:checked').val();
            // alert(patmentType);
            var customer_id = $('#customer_id').val();

            var unitprice = $('.unitprice').val();

            var total = $('.total').val();

            if (catId == '' || catId == null) {
                alertMessage.error("Category can't be empty.");
                return false;
            }
            if (proId == '' || proId == null) {
                alertMessage.error("Product can't be empty.");

                return false;
            }


            if (qty == '' || qty == null || qty == 0) {
                alertMessage.error("Quantity can't be empty or zero.");

                return false;
            } else {

                if (customer_id === null) {
                    alertMessage.error("Please select a Customer.");
                    return false;
                }

                if (patmentType === undefined) {
                    alertMessage.error("Please select Payment Type.");
                    return false;
                }


                $("#show_item tbody").append('<tr class="new_item' + proId +
                    '">\n\
                                                                                                                                                                                                                                                                            <td style="padding-left:15px;">' +
                    catName +
                    '<input type="hidden" name="catName[]" value="' +
                    catId +
                    '"></td>\n\
                                                                                                                                                                                                                                                                            <td align="right">' +
                    proName +
                    '<input type="hidden" class="add_quantity" name="proName[]" value="' +
                    proId +
                    '"></td>\n\
                                                                                                                                                                                                                                                                            <td align="right">' +
                    purchasetypetext +
                    '<input type="hidden" class="add_quantity" name="purchasetype[]" value="' +
                    purchasetypeval +
                    '"></td>\n\\n\<td align="right">' +qty +'<input type="hidden" class="ttlqty" name="qty[]" value="' +qty +'">                               </td>\n\\n\  <td align="right">' +vat +'<input type="hidden" class="ttlqty" name="vat[]" value="' +vat +'"></td>\n\\n\                                                                                                                                                          <td align="right">' +
                    unitprice +
                    '<input type="hidden" class="ttlunitprice unitparice" name="unitprice[]" value="' +
                    unitprice +
                    '"></td>\n\
                                                                                                                                                                                                                                                                            <td align="right">' +
                    total +
                    '<input type="hidden" class="grandtotal" name="total[]" value="' +
                    total +
                    '"></td>\n\
                                                                                                                                                                                                                                                                            \n\
                                                                                                                                                                                                                                                                            \n\
                                                                                                                                                                                                                                                                            <td><a del_id="' +
                    proId +
                    '" class="delete_item btn form-control btn-danger" href="javascript:;" title=""><i class="fa fa-times"></i></a></td></tr>'
                );
            }

            $(".catName").val(null).trigger("change");
            $(".proName").val(null).trigger("change");
            $("#currentStock").val("");
            $("#qty").val("");
            $("#cty_size").val("");
            $("#gas_qty").val("");
            $(".unitprice").val("");
            $(".ttlamount").val("");


            findqtyamount();
            findunitamount();
            findgrandtottal();
            checkDepositAndCreditBalance();
        });

        $('#branch_id').on('change', function () {
            $(".catName").val(null).trigger("change");
            $(".proName").val(null).trigger("change");
        })

        $(document).on('click', '.delete_item', function () {
            // if (confirm("Are you sure?")) {
            //     var id = $(this).attr("del_id");
            //     $('.new_item' + id).remove();
            //     findqtyamount();
            //     findunitamount();
            //     findgrandtottal();
            //     checkDepositAndCreditBalance();
            // }

            let deleteitem = () => {
                var id = $(this).attr("del_id");
                $('.new_item' + id).remove();
                findqtyamount();
                findunitamount();
                findgrandtottal();
                checkDepositAndCreditBalance();
            }

            alertMessage.confirm('You want to remove this', deleteitem);

        });
    });



    function checkDepositAndCreditBalance() {

        var paymentType = $("#paymentType").val();

        if (paymentType == '') {
            paymentType = 'Cash';
        }

        console.log(paymentType);
        var customer_currentBalance = $("#customer_currentBalance").val();
        // var totalDue = document.getElementById("totalDue").innerText;

        var totalDue = $("#totalDue").text();
        var expireDatas = $("#expireData").val();


        var todaysDate = new Date().toISOString().slice(0, 10);

        if (expireDatas == '') {
            expireDatas = todaysDate;
        }
        var btn = document.getElementById('subMitButton');
        if ((paymentType == 'Deposit') && (parseFloat(customer_currentBalance) < parseFloat(totalDue))) {
            console.log('1');
            btn.disabled = true;
        // } else if (((paymentType == 'Credit') && (parseFloat(customer_currentBalance) < parseFloat(totalDue))) || (
        //     expireDatas < todaysDate)) {
        //     console.log('2');
        //     btn.disabled = true;
        } else if (paymentType == 'Cash') {
            console.log('3');
            btn.disabled = false;
        } else {
            console.log('4');
            btn.disabled = false;
        }

    }
</script>


<script>
    function gamount(){
        var gtoal = parseFloat(document.getElementById("gtoal").innerText);
        var carrying_cost =parseFloat($("#carrying_cost").val());  
        var discount = parseFloat($("#disCount").val());
        var labor_bill = parseFloat($("#labor_bill").val());

        return ((gtoal || 0) + (carrying_cost || 0) + (labor_bill || 0)) - (discount || 0);
    }
    function discountCalculation(amount) {
    var gtoal = parseFloat(document.getElementById("gtoal").innerText);
    var discountType = document.getElementById("discountType").value;
    var discount = parseFloat(amount) || 0; // Parse the discount value, default to 0

    // Calculate discount based on type
    if (discountType === "percentage") {
        if (discount > 100) {
            alertMessage.error("Percentage discount cannot exceed 100%");
            $('#disCount').val('');
            $('#ntotal').text(gtoal.toFixed(2));
            $('#totalDue').text(gtoal.toFixed(2));
            return;
        }
        discount = (gtoal * discount) / 100; // Convert percentage to amount
    }

    if (discount > gtoal) {
        alertMessage.error("Discount cannot be greater than the total amount");
        $('#disCount').val('');
        $('#ntotal').text(gtoal.toFixed(2));
        $('#totalDue').text(gtoal.toFixed(2));
        return;
    }

    var carrying_cost = parseFloat($("#carrying_cost").val()) || 0;
    var afterDiscount = gtoal - discount + carrying_cost;

    $('#ntotal').text(afterDiscount.toFixed(2));
    $('#totalDue').text(afterDiscount.toFixed(2));

    var paymentType = $('input[name="payment_type"]:checked').val();
    if (paymentType === "Cash") {
        $('#payment').val(afterDiscount.toFixed(2));
        paymentCalculation(afterDiscount);
    }
}

    function carrying_cost_Calculation(amount) {

        var gtoal = document.getElementById("gtoal").innerText;
        var discount = $("#disCount").val();
        var afterCarryingCost =  gamount();
        $('#ntotal').text(parseFloat(afterCarryingCost).toFixed(2));
        $('#totalDue').text(parseFloat(afterCarryingCost).toFixed(2));

    }
    function labor_bill_Calculation(amount) {
        var gtoal = document.getElementById("gtoal").innerText;
        var afterLaborBill =  gamount();
        $('#ntotal').text(parseFloat(afterLaborBill).toFixed(2));
        $('#totalDue').text(parseFloat(afterLaborBill).toFixed(2));
    }

    function paymentCalculation(payamount) {
        var ntotal = document.getElementById("ntotal").innerText;
        var totalDue = ntotal - payamount;
        $('.finalDue').text(parseFloat(totalDue).toFixed(2));
    }

    function qtyPriceCal() {
        var qty = $('#qty').val();
        var unitpice = $('#unitpice').val();
        var vat = parseFloat($('#vat').val()) || 0; 
        var currentStock = $('#currentStock').val();
        if (parseFloat(qty) > currentStock) {
            $('.ttlamount').val('');
            $('#qty').val('');
            // lert('The desired product stock is not available');
            alertMessage.error('The desired product stock is not available.');
        } else {
            var totalWithoutVAT = unitpice * qty;
            var vatAmount = (vat > 0) ? (vat / 100) * totalWithoutVAT : 0;
            var totalWithVAT = totalWithoutVAT + vatAmount;
            var ttlqtys = document.getElementById('total').value = totalWithVAT.toFixed(2);
        }
    }

    function getProductList(cat_id) {

        var branch_id = $('#branch_id').val();

        if (branch_id == null) {
            alertMessage.error('Branch Are not selected');
            return;
        }

        $.ajax({
            "url": "{{ route('sale.sale.getProductListForSale') }}",
            "type": "GET",
            cache: false,
            data: {
                "_token": "{{ csrf_token() }}",
                cat_id: cat_id,
                branch_id: branch_id
            },
            success: function (data) {
                $('#productID').select2();
                $('#productID option').remove();
                $('#productID').append($(data));
                $("#productID").trigger("select2:updated");
            }
        });
    }

    getCustomerBalance('Due')

    function getCustomerBalance(payment_type) {

        var customer_id = $('#customer_id').val();
        if(!customer_id){
            alertMessage.error('Please Select Customer');
          return
        }
        $("#paymentType").val(payment_type);

        if (payment_type == 'Cash') {
            $('#account_id').show();
            $('#netTotal').show();
            $('.partisals').show();
            calculatetotal();
        } else {
            calculatetotal();
            $('.partisals').css('display', 'none');
            $('#account_id').css('display', 'none');
            $('#netTotal').css('display', 'none');
        }

        $.ajax({
            "url": "{{ route('sale.sale.getCustomerBalance') }}",
            "type": "GET",
            cache: false,
            dataType: "json",
            data: {
                "_token": "{{ csrf_token() }}",
                customer_id: customer_id,
                payment_type: payment_type
            },
            success: function (data) {
                if (payment_type == 'Cash') {
                    $("#customer_currentBalance").val('');
                } else {
                    $("#customer_currentBalance").val(data.finalBalance);
                    $("#expireData").val(data.expireData);
                }

            }
        });
    }

    var calculatetotal = function () {
        var grandtotal = 0;
        $.each($('.grandtotal'), function () {
            total = $(this).val();
            total = Number(total);
            grandtotal += total;
        });

        if (parseFloat(grandtotal) > 0) {
            $('.grandtotal').text(parseFloat(grandtotal).toFixed(2));
            var paymentType = $('input[name="payment_type"]:checked').val();
            if (paymentType == "Cash") {
                $('#payment').val(parseFloat(grandtotal).toFixed(2));
                paymentCalculation(parseFloat(grandtotal));
            }
        }
    };

    function getUnitPrice(v) {
        let branch_id = $('#branch_id option:selected').val();
        let sub_branch_id = $('#sub_warehouse_id option:selected').val();
        let purchasetype = $('.purchasetype option:selected').val();
        let productId = $('#productID option:selected').val();
        $.ajax({
            "url": "{{ route('sale.sale.saleunitPrice') }}",
            "type": "GET",
            cache: false,
            data: {
                "_token": "{{ csrf_token() }}",
                'productId': productId,
            },
            success: function (data) {
                console.log(data);
                $("#unitpice").val(data.sale_price);
                $(".purchaseprice").html("Last PP :"+data.lastPurchasePrice);

            }
        });

        $.ajax({
            "url": "{{ route('sale.sale.getProductStock') }}",
            "type": "GET",
            cache: false,
            data: {
                "_token": "{{ csrf_token() }}",
                productId: productId,
                type: purchasetype,
                branch_id: branch_id,
                sub_branch_id: sub_branch_id,

            },
            success: function (data) {
                $("#currentStock").val(data);
            }
        });
    }
</script>

@endsection
