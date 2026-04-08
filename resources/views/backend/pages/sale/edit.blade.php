@extends('backend.layouts.master')

@section('title')
Settings - {{ $title }}
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
                    <li class="breadcrumb-item"><a href="{{ route('sale.sale.index') }}">Category
                            List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Edit Category</span></li>
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
                <h3 class="card-title">Sale Edit</h3>
                <div class="card-tools">
                    @if (helper::roleAccess('sale.sale.create'))
                    <a class="btn btn-default" href="{{ route('sale.sale.create') }}"><i
                            class="fas fa-plus"></i>
                        Add New</a>
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
                <form class="needs-validation" method="POST" action="{{ route('sale.sale.update',$saletlist->id) }}"
                    novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-2 mb-3">
                            <label for="validationCustom01">Invoice Number :</label>
                            <input class="bg-green form-control" readonly=""
                                style="padding: 5px; font-weight : bold; width: 100%"
                                value="{{ $saletlist->invoice_no }} " for="validationCustom01">
                            <input type="hidden" name="invoice_no" class="form-control" id=""
                                value="{{ $saletlist->invoice_no }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="validationCustom01">Po Number :</label>
                            <input type="text" name="po_invoice" class="form-control" value="{{ $saletlist->po_invoice }}">
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
                                    value="{{ $saletlist->date }}" class="form-control datetimepicker-input"
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
                            @php
                                $sub_branch = App\Models\Branch::find($saletlist->branch_id);
                            @endphp
                            <select class="form-control select2" id="branch_id" name="branch_id">
                                <option selected disabled value="">--Select Branch--</option>
                                @foreach ($branch as $key => $value)
                                <option value="{{ $value->id }}" {{$sub_branch->parent_id == $value->id ? "selected":"" }} >
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
                                @foreach ($subWarehouses as $subWarehouse)
                                    <option value="{{ $subWarehouse->id }}" {{ $subWarehouse->id == $saletlist->branch_id ? 'selected' : '' }}>
                                        {{ $subWarehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                            <!-- Loading spinner -->
                            <div id="loadingSpinner" style="display:none;">
                                <img src="https://i.pinimg.com/originals/5c/87/9a/5c879ab8cba794923686df4b950f497b.gif" alt="Loading..."  width="10%"/>
                            </div>
                            @error('sub_warehouse_id')
                            <span class="error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        {{-- <div class="col-md-2 mb-3">
                            <label for="validationCustom01">Customer * :</label>
                            <select class="form-control select2" name="customer_id" id="customer_id">
                                <option selected disabled value="">--Select Customer--</option>
                                @foreach ($customer as $key => $value)
                                <option {{ $saletlist->customer_id == $value->id ? 'selected' : '' }}
                                    value="{{ $value->id }}">
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
                                <x-account :setAccounts="$ledgers" :selectVal="$saletlist->ledger_id"/>
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
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                {{-- <label class="btn bg-olive active">
                                    <input type="radio" name="payment_type" value="Cash"
                                        onchange="getCustomerBalance('Cash')" id="option1" {{$saletlist->payment_type ==
                                    "Cash" ? "checked":""}}> Cash
                                </label> --}}
                                {{-- <label class="btn bg-olive">
                                    <input type="radio" name="payment_type" value="Deposit"
                                        onchange="getCustomerBalance('Deposit')" id="option2" autocomplete="off"
                                        {{$saletlist->payment_type == "Deposit" ? "checked":""}}>
                                    Deposit
                                </label> --}}
                                <label class="btn bg-olive">
                                    <input type="radio" name="payment_type" value="Due"
                                        onchange="getCustomerBalance('Due')" {{$saletlist->payment_type ==
                                    "Due" ? "checked":""}} id="option3" autocomplete="off"> Due
                                </label>
                                {{-- <label class="btn bg-olive">
                                    <input type="radio" name="payment_type" value="Credit"
                                        onchange="getCustomerBalance('Credit')" id="option3" autocomplete="off"
                                        {{$saletlist->payment_type == "Credit" ? "checked":""}}> Credit
                                </label> --}}
                            </div>
                            <input type="hidden" id="paymentType">
                            <input type="hidden" id="expireData">
                            <input type="hidden" id="paymentypeValue" value="{{$saletlist->payment_type}}">
                        </div>
                        <table class=" table-responsive table table-bordered">
                            <tr>
                                <td>
                                    <div class="col-md-9 float-left ">
                                        Sales Item
                                    </div>
                                    <div class="col-md-3 float-right">
                                        Payment Calculation
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 10px!important;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12 float-left">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">

                                                        <table class="table table-bordered table-hover tableAddItem"
                                                            id="show_item">
                                                            <thead>
                                                                <tr>
                                                                    <th nowrap style="width:20%" align="center" id="">
                                                                        <strong>Product Category <span
                                                                                style="color:red;">
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
                                                                    <th nowrap style="width:10%" align="center">
                                                                        <strong>Quantity <span style="color:red;">
                                                                                *</span></strong>
                                                                    </th>
                                                                    <th nowrap style="width:11%" align="center">
                                                                        <strong>Vat <span style="color:red;">
                                                                                *</span></strong>
                                                                    </th>
                                                                    {{-- <th nowrap style="width:11%" align="center">
                                                                        <strong>Gas/Sp-qty <span style="color:red;">
                                                                                *</span></strong>
                                                                    </th> --}}
                                                                    <th nowrap style="width:12%" align="center">
                                                                        <strong>Unit
                                                                            Price(BDT) <span style="color:red;">
                                                                                *</span></strong>
                                                                    </th>
                                                                    <th nowrap style="width:13%" align="center">
                                                                        <strong>Total Price(BDT) <span
                                                                                style="color:red;">
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
                                                                            <option disabled selected>--- Select
                                                                                Category
                                                                                ---</option>
                                                                            <?php foreach ($category_info as $eachInfo) : ?>
                                                                            <option catName="{{ $eachInfo->name }}"
                                                                                value="{{ $eachInfo->id }}">
                                                                                {{  $eachInfo->name }}</option>
                                                                                
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </td>
                                                                    <td id="product_td">
                                                                        <select class="select2 form-control proName"
                                                                            id="productID"
                                                                            data-placeholder="Search Product"
                                                                            onchange="getUnitPrice(this.value)">
                                                                            <option disabled selected>---Select
                                                                                Product---
                                                                            </option>
                                                                        </select>
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
                                                                        <input type="text" readonly
                                                                            class="form-control  " style="height: 20px;"
                                                                            id="currentStock" placeholder="0">
                                                                        <input type="text" style="height: 20px;"
                                                                            class="form-control  qty" id="qty"
                                                                            onkeyup="qtyPriceCal();" placeholder="0">
                                                                    </td>
                                                                    {{-- <td>
                                                                        <input type="number" readonly
                                                                            class="form-control" min="0" value="0"
                                                                            id="gas_qty" placeholder="0">
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
                                                                        <a id="add_item"
                                                                            class="btn btn-info form-control"
                                                                            href="javascript:;" title="Add Item">
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                @foreach ($saledetails as $key => $value)

                                                                <tr class="new_item{{ $value->product_id }}">
                                                                    <td style="padding-left:15px;">
                                                                        {{ ($value->category->parent->name ?? "N/A") .'-'. $value->category->name ?? 'N/A'}}<input
                                                                            type="hidden" name="catName[]"
                                                                            value="{{ $value->category_id }}">
                                                                    </td>
                                                                    <td align="right">
                                                                        {{ $value->product->name ?? 'N/A'}}<input
                                                                            type="hidden" class="add_quantity"
                                                                            name="proName[]"
                                                                            value="{{ $value->product_id }}">
                                                                    </td>

                                                                    <td align="right">
                                                                        {{ $value->purchasetype }}
                                                                    <input type="hidden" class="add_quantity" name="purchasetype[]" value="{{ $value->purchasetype }}">
                                                                      
                                                                    </td>

                                                                    
                                                                    {{-- <td align="right">{{ $value->cty_size }}<input
                                                                            type="hidden" class="ttlqty"
                                                                            name="cty_size[]"
                                                                            value="{{ $value->cty_size }}">
                                                                    </td> --}}
                                                                    <td align="right">{{ $value->qty }}<input
                                                                            type="hidden" class="" name="qty[]"
                                                                            value="{{ $value->qty }}">
                                                                    </td>
                                                                    <td align="right">{{ $value->vat }}<input
                                                                            type="hidden" class="" name="vat[]"
                                                                            value="{{ $value->vat }}">
                                                                    </td>
                                                                    {{-- <td align="right">{{ $value->gas_qty }}<input
                                                                            type="hidden" class="" name="gas_qty[]"
                                                                            value="{{ $value->gas_qty }}">
                                                                    </td> --}}
                                                                    <td align="right">{{ $value->rate }}<input
                                                                            type="hidden"
                                                                            class="ttlunitprice unitparice"
                                                                            name="unitprice[]"
                                                                            value="{{ $value->rate }}">
                                                                    </td>
                                                                    @php
                                                                    $totale = $value->price;
                                                                    @endphp
                                                                    <td align="right">{{ $totale }}<input type="hidden"
                                                                            class="grandtotal" name="price[]"
                                                                            value="{{ $totale }}">
                                                                    </td>
                                                                    <td><a del_id="{{ $key + 1 }}"
                                                                            class="delete_item btn form-control btn-danger"
                                                                            href="javascript:;" title=""><i
                                                                                class="fa fa-times"></i></a>
                                                                    </td>
                                                                </tr>
                                                                @endforeach

                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td align="right"><strong>Sub-Total(BDT)</strong>
                                                                    </td>
                                                                    <td align="right"><strong class=""></strong>
                                                                    </td>
                                                                    <td align="right"><strong class=""></strong>
                                                                    </td>
                                                                    <td align="
                                                                    right">
                                                                    <strong
                                                                    class="ttlqty">{{$saledetails->sum('qty')}}</strong>
                                                                </td>
                                                                <td align="right"><strong class=""></strong>
                                                                </td>
                                                                <td align="right"><strong
                                                                    class="ttlunitprice">{{$saledetails->sum('rate')}}</strong>
                                                                </td>
                                                                    <td align="right"><strong
                                                                            class="grandtotal">{{$saledetails->sum('price')}}</strong>
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 float-right">
                                                <div class="panel  panel-default">
                                                    <div class="panel-body">

                                                        <table class="table table-bordered table-hover ">
                                                            <tbody>
                                                                <tr>
                                                                    <td nowrap align="right"><strong>Total
                                                                        </strong>
                                                                    </td>
                                                                    <td align="right"> <strong id="gtoal"
                                                                            class="grandtotal">{{$saledetails->sum('price')}}.00</strong>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td nowrap align="right">
                                                                        <strong>Discount ( - )
                                                                        </strong>
                                                                    </td>
                                                                    <td><input type="text" autocomplete="off"
                                                                            onkeyup="discountCalculation(this.value)"
                                                                            id="disCount" style="text-align: right"
                                                                            name="discount"
                                                                            value="{{$saletlist->discount}}"
                                                                            class="form-control" placeholder="0.00"
                                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" />
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
                                                                            value="{{$saletlist->carrying_cost}}"
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
                                                                            value="{{$saletlist->labor_bill}}"
                                                                            style="text-align: right"
                                                                            name="labor_bill"
                                                                            class="form-control"
                                                                            placeholder="0.00"
                                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" />
                                                                    </td>
                                                                </tr>
                                                                @php
                                                                $discount=$saledetails->sum('price') -
                                                                $saletlist->discount;
                                                                @endphp
                                                                <tr id="netTotal">
                                                                    <td nowrap align="right"><strong>Net
                                                                            Total</strong>
                                                                    </td>
                                                                    <td align="right"><strong id="ntotal"
                                                                            class="grandtotal">{{$discount}}.00</strong>
                                                                    </td>
                                                                </tr>
                                                                <tr id="account_id">
                                                                    <td nowrap align="right">
                                                                        <strong>Account</strong>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control account_id select2"
                                                                            name="account_id" require>
                                                                            <option>---- Select Account ----
                                                                            </option>
                                                                            @foreach ($account as $key =>
                                                                            $value)
                                                                            <option {{$transection->account_id ?? "0" ==
                                                                                $value->id ? "selected":""}} value="{{
                                                                                $value->id
                                                                                }}">
                                                                                {{ $value->accountCode . ' -
                                                                                ' .
                                                                                $value->account_name }}
                                                                            </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                                <tr class="partisals">
                                                                    <td nowrap align="right"><strong>Payment
                                                                            ( -
                                                                            )<span style="color:red;"> *
                                                                            </span></strong>
                                                                    </td>
                                                                    <td><input type="text" id="payment"
                                                                            onkeyup="paymentCalculation(this.value)"
                                                                            style="text-align: right"
                                                                            name="partialPayment" readonly
                                                                            value="{{$saletlist->partialPayment}}"
                                                                            class="form-control" autocomplete="off"
                                                                            placeholder="0.00"
                                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" />
                                                                    </td>
                                                                    <!-- <input type="hidden" id="duePayment" style="text-align: right" name="duePayment" value="" readonly class="form-control" placeholder="0.00" /> -->
                                                                </tr>

                                                                <tr>
                                                                    <td nowrap align="right"><strong>Total
                                                                            Due</strong>
                                                                    </td>
                                                                    <td align="right"><strong id="totalDue"
                                                                            class="grandtotal finalDue">{{$discount
                                                                            -
                                                                            $saletlist->partialPayment}}.00</strong>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-9 float-right">
                                                <div class="panel">
                                                    <div class="panel-body">
                                                        <table class="table ">
                                                            <tr>
                                                                <td>
                                                                    <textarea style="
                                                                                    border:none;" cols="157"
                                                                        class="form-control" name="narration"
                                                                        placeholder="Note......" type="text"></textarea>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                </td>
                            </tr>
                            <tr>

                            </tr>
                            <tr>
                                <td>
                                    <div class="clearfix"></div>
                                    <div class="clearfix form-actions float-right">
                                        <div class="col-md-offset-1 col-md-10">
                                            <button class="btn btn-info" id="subMitButton" type="submit">
                                                Save
                                            </button>
                                            &nbsp; &nbsp; &nbsp;

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>





                    </div>

                </form>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">

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

            let seaschproduct = $('#productID option:selected')[0].getAttribute("value");

            let tbody = $('tbody').find(".new_item" + seaschproduct).length;
            let tbody2 = $('tbody').find("new_item" + seaschproduct);
            console.log(tbody);

            
            var purchasetypeval = $('.purchasetype').find('option:selected').val();
            var purchasetypetext = $('.purchasetype').find('option:selected').text();


            if (tbody > 0) {
                alertMessage.error('This product already exist');
                return;
            }

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


            var unitprice = $('.unitprice').val();

            var total = $('.total').val();

            if (catId == '' || catId == null) {
                // productItemValidation("Category can't be empty.");
                return false;
            }
            if (proId == '' || proId == null) {
                // productItemValidation("Product can't be empty.");
                return false;
            }


            if (qty == '' || qty == null || qty == 0) {
                //   productItemValidation("Quantity can't be empty or zero.");
                return false;
            } else {

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
                    '"></td>\n\    \n\
                    <td align="right">' +
                    purchasetypetext +
                    '<input type="hidden" class="add_quantity" name="purchasetype[]" value="' +
                    purchasetypeval +
                    '"></td>\n\
                                                                                                                                                                                                                                                                                            <td align="right">' +
                    qty +
                    '<input type="hidden" class="ttlqty" name="qty[]" value="' +
                    qty +
                    '"></td>\n\
                        \n\    \n\\n\  <td align="right">' +vat +'<input type="hidden" class="ttlqty" name="vat[]" value="' +vat +'"></td>\n\\n\                                                                                                                                                                                                                                                                  <td align="right">' +
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

            $('.unitprice').val('');
            $('.qty').val('');
            $('.total').val('');
            // $('.unitName').val('').trigger('chosen:updated');
            $('.proName').val('').trigger('select2:updated');
            $('.catId').val('').trigger('select2:updated');
            //$('.subCat').val('').trigger('chosen:updated');
            findqtyamount();
            findunitamount();
            findgrandtottal();
            checkDepositAndCreditBalance();
        });

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
          if (paymentType == 'Cash') {
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
        var gtoal = document.getElementById("gtoal").innerText;
        var carrying_cost =$("#carrying_cost").val();

        if (parseFloat(gtoal) < parseFloat(amount)) {
            alertMessage.error("Discount Can'n Greater than Total amount");
            $('#disCount').val('');
            $('#ntotal').text(parseFloat(gtoal).toFixed(2));
            $('#totalDue').text(parseFloat(gtoal).toFixed(2));
            var paymentType = $('input[name="payment_type"]:checked').val();
            if (paymentType == "Cash") {
                $('#payment').val(parseFloat(gtoal));
                paymentCalculation(parseFloat(gtoal));
            }
            return;
        }

        var afterDiscount = gamount();
        $('#ntotal').text(parseFloat(afterDiscount).toFixed(2));
        $('#totalDue').text(parseFloat(afterDiscount).toFixed(2));

        var paymentType = $('input[name="payment_type"]:checked').val();
        if (paymentType == "Cash") {
            $('#payment').val(parseFloat(afterDiscount));
            paymentCalculation(parseFloat(afterDiscount));
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

    function getCustomerBalance(payment_type) {
        var customer_id = $('#customer_id').val();
        $("#paymentType").val(payment_type);

        if (payment_type == 'Cash') {
            $('#account_id').show();
            $('#netTotal').show();
            $('.partisals').show();
            calculatetotal();
        } else {
            calculatetotal();
            var discount = $('#disCount').val();
            discountCalculation(discount);
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

    var payTypeID = $('#paymentypeValue').val();
    getCustomerBalance(payTypeID);

    function getUnitPrice(v) {
        let branch_id = $('#branch_id option:selected').val();
        let purchasetype = $('.purchasetype option:selected').val();
        let productId = $('#productID option:selected').val();
        $.ajax({
            "url": "{{ route('sale.sale.saleunitPrice') }}",
            "type": "GET",
            cache: false,
            data: {
                "_token": "{{ csrf_token() }}",
                productId: productId
            },
            success: function (data) {
                $("#unitpice").val(data);
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

            },
            success: function (data) {
                $("#currentStock").val(data);
            }
        });
    }
</script>


@endsection