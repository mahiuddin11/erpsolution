@extends('backend.layouts.master')
@section('title')
Stock - {{ $title }}
@endsection

@section('styles')
<style>
    .bootstrap-switch-large {
        width: 200px;
    }
</style>
@endsection

@section('navbar-content')
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    Stock </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('inventorySetup.transfer.index'))
                    <li class="breadcrumb-item"><a href="{{ route('inventorySetup.transfer.index') }}">Stock
                            Manage</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active"><span>Tranfer Approve List</span></li>
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
                <h3 class="card-title">Stock Tranfer </h3>
            </div>
            <div class="card-body">
                <form class="needs-validation" method="POST"
                    action="{{ route('inventorySetup.transfer.approveedit', $id) }}" novalidate>
                    @csrf
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">Invoice Number :</label>
                            <input class="bg-green form-control" readonly=""
                                style="padding: 5px; font-weight : bold; width: 100%"
                                value="{{ $transfe->voucher_code }}" for="validationCustom01">
                        </div>
                        <input type="hidden" name="approvalstatus" value="{{ $transfe->status }}">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">Date * :</label>
                            <input type="date" class="form-control" id="validationCustom01" disabled
                                value="{{ $transfe->date }}" placeholder="Date">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">From Branch * :</label>
                            <select class="form-control select2" disabled>
                                <option selected disabled value="">--Select Branch--</option>
                                @foreach ($branch as $key => $value)
                                <option {{ $transfe->from_branch_id == $value->id ? 'selected' : '' }}
                                    value="{{ $value->id }}">
                                    {{ $value->branchCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">To Branch * :</label>
                            <select class="form-control select2" disabled>
                                <option value="">--Select Branch--</option>
                                @foreach ($branch as $key => $value)
                                <option {{ $transfe->to_branch_id == $value->id ? 'selected' : '' }}
                                    value="{{ $value->id }}">
                                    {{ $value->branchCode . ' - ' . $value->name }}
                                </option>
                                @endforeach
                            </select>
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
                                    <div class="col-md-12">
                                        <div class="col-md-9 float-left">
                                            <div class="panel panel-default">
                                                <div class="panel-body">

                                                    <table class="table table-bordered table-hover tableAddItem"
                                                        id="show_item">
                                                        <thead>
                                                            <tr>

                                                                <th nowrap style="width:20%" align="center" id="">
                                                                    <strong>Product Category <span style="color:red;">
                                                                            *</span></strong>
                                                                </th>
                                                                <th nowrap style="width:25%" align="center" id="">
                                                                    <strong>Product <span style="color:red;">
                                                                            *</span></strong>
                                                                </th>
                                                                <th nowrap style="width:10%" align="center">
                                                                    <strong>Quantity <span style="color:red;">
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
                                                            @foreach ($transfeDetails as $value)
                                                            <tr>
                                                                <td style="padding-left:15px;">
                                                                    {{ $value->category->name }}</td>
                                                                <td align="right">
                                                                    {{ $value->product->name }} </td>
                                                                <input type="hidden" name="transDetail[]"
                                                                    value="{{ $value->id }}">
                                                                <td align="right"><input type="number"
                                                                        class="ttlqty form-control" name="qty[]"
                                                                        curentval="{{ $value->qty }}"
                                                                        oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');"
                                                                        value="{{ $value->qty }}">
                                                                </td>
                                                                <td align="right">{{ $value->unit_price }}<input
                                                                        type="hidden" class="unitparice"
                                                                        name="unitprice[]"
                                                                        value="{{ $value->unit_price }}"></td>
                                                                <td align="right">
                                                                    <samp>{{ $value->total_price }}</samp> <input
                                                                        type="hidden" class="grandtotal" name="total[]"
                                                                        value="{{ $value->total_price }}">
                                                                </td>

                                                                <td><a class="delete_item btn form-control btn-danger"
                                                                        href="javascript:;" title=""><i
                                                                            class="fa fa-times"></i></a></td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td align="right"><strong>Sub-Total(BDT)</strong></td>
                                                                <td align="right"><strong class=""></strong></td>
                                                                <td align="
                                                                            right"><strong
                                                                        class="ttlqty">{{ $transfeDetails->sum('qty') }}</strong>
                                                                </td>
                                                                <td align="right"><strong
                                                                        class="ttlunitprice">{{ $transfeDetails->sum('unit_price') }}</strong>
                                                                </td>
                                                                <td align="right"><strong
                                                                        class="grandtotal">{{ $transfeDetails->sum('total_price') }}</strong>
                                                                </td>
                                                                <td align="right"><strong class=""></strong></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>

                                                    <table class="">
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
                                        <div class="col-md-3 float-right">
                                            <div class="panel  panel-default">
                                                <div class="panel-body">

                                                    <table class="table table-bordered table-hover ">
                                                        <tbody>
                                                            <tr>
                                                                <td nowrap align="right"><strong>Total </strong></td>
                                                                <td align="right"> <strong id="gtoal"
                                                                        class="grandtotal">{{ $transfeDetails->sum('total_price') }}.00</strong>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td nowrap align="right"><strong>Shiping Charge ( + )
                                                                    </strong></td>
                                                                <td>
                                                                    <input type="text" autocomplete="off" id="disCount"
                                                                        style="text-align: right" disabled
                                                                        value="{{ $transfe->shipping }}"
                                                                        class="form-control" placeholder="0.00"
                                                                        oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" />
                                                                </td>
                                                                @php
                                                                $nettotal = $transfeDetails->sum('total_price') +
                                                                $transfe->shipping;
                                                                @endphp
                                                            </tr>
                                                            <tr>
                                                                <td nowrap align="right"><strong>Net Total</strong></td>
                                                                <td align="right"><strong id="ntotal"
                                                                        class="grandtotal abc">{{ abs($nettotal) }}.00</strong>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" style="height: 102px;">

                                                                    <div class="clearfix"></div>
                                                                    <div class="clearfix form-actions float-right">
                                                                        <div class="col-md-offset-1 col-md-10">
                                                                            <button class="btn btn-info"
                                                                                id="subMitButton" type="submit">
                                                                                Save
                                                                            </button>
                                                                            &nbsp; &nbsp; &nbsp;

                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>


                                                        </tbody>
                                                    </table>

                                                </div>
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

<script>
    $(document).ready(function() {
            $('.delete_item').on('click', function() {
                // if (confirm('Are You sure')) {
                //     $(this).closest('tr').remove();
                //     findqtyamount();
                //     findunitamount();
                //     findgrandtottal();
                // } else {
                //     return;
                // }
                let deleteitem = () => {
                    $(this).closest('tr').remove();
                    findqtyamount();
                    findunitamount();
                    findgrandtottal();
                }

                alertMessage.confirm('You want to remove this', deleteitem);

            })

            $('.ttlqty').on('keyup change', function() {
                var qty = $(this).val();
                var corentqty = $(this).attr('curentval');

                // alert(corentqty);
                if (parseFloat(qty) <= corentqty) {
                    console.log(qty);
                    console.log(corentqty);
                    var unitprice = $(this).closest('tr').find('.unitparice').val();
                    var total = unitprice * qty;
                    $(this).closest('tr').find('samp').text(total);
                    $(this).closest('tr').find('.grandtotal').val(total);
                    findqtyamount();
                    findunitamount();
                    findgrandtottal();
                } else {
                    $(this).val(corentqty);
                    // lert('You cannot increase quantity');
                alertMessage.error('You cannot increase quantity');

                }
            })

            var findqtyamount = function() {

                var ttlqty = 0;
                $.each($('.ttlqty'), function() {
                    qty = $(this).val();
                    qty = Number(qty);
                    ttlqty += qty;
                });
                $('.ttlqty').text(parseFloat(ttlqty).toFixed(2));

            };

            var findunitamount = function() {
                var ttlunitprice = 0;
                $.each($('.unitparice'), function() {
                    unitprice = $(this).val();
                    unitprice = Number(unitprice);
                    ttlunitprice += unitprice;
                });
                $('.ttlunitprice').text(parseFloat(ttlunitprice).toFixed(2));
            };

            var findgrandtottal = function() {
                var grandtotal = 0;
                $.each($('.grandtotal'), function() {
                    total = $(this).val();
                    total = Number(total);
                    grandtotal += total;
                });
                $('.grandtotal').text(parseFloat(grandtotal).toFixed(2));
            };

        })
</script>

@endsection
@section('scripts')
@endsection