@extends('backend.layouts.master')

@section('title')
    Settings - {{ $title }}
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Settings </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('inventory-purchaserequisition-list'))
                            <li class="breadcrumb-item"><a
                                    href="{{ route('inventory-purchaserequisition-list') }}">Customer
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Purchase Requisition</span></li>
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
                    <h3 class="card-title">Add New Purchase Requisition</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('inventory-purchaserequisition-list'))
                            <a class="btn btn-default" href="{{ route('inventory-purchaserequisition-list') }}"><i
                                    class="fa fa-list"></i>
                                Purchase Requisition List</a>
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
                    <form class="needs-validation" method="POST"
                        action="{{ route('inventorySetup.purchaserequisition.approveUpdate',$requisition->id) }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <span class="bg-green" style="padding: 5px; font-weight : bold"
                                    for="validationCustom01">Requisition Code * : {{ $requisition->invoice_no }}</span>
                                <input type="hidden" name="requisitionCode" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Date * :</label>
                                <input type="date" name="date" value="{{ $requisition->date }}" class="form-control"
                                    id="validationCustom01" placeholder="Date">
                                @error('date')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validationCustom01">Branch * :</label>
                                <select class="form-control select2" name="branch_id">
                                    <option selected disabled value="">--Select--</option>
                                    @foreach ($branch as $key => $value)
                                        <option {{ $requisition->branch_id == $value->id ? 'selected' : '' }}
                                            value="{{ $value->id }}">
                                            {{ $value->branchCode . ' - ' . $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <span class=" error text-red text-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <table class="table table-bordered table-hover" id="show_item">
                            <thead>
                                <tr>
                                    <th colspan="8">Select Product Item</th>
                                </tr>
                                <tr>
                                    <td class="text-center" style="width: 30%"><strong>Product Category</strong></td>
                                    <td class="text-center" style="width: 30%"><strong>Product</strong></td>
                                    <td class="text-center" style="width: 30%"><strong>Qty</strong></td>
                                    <td class="text-center" style="width: 5%"><strong>Action</strong></td>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="form-control select2" id="category_id">
                                            <option selected disabled>--Select--</option>
                                            @foreach ($category as $key => $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                    </td>
                                    <td>
                                        <select class="form-control select2" id="product_id">
                                            <option selected disabled>--Select--</option>
                                        </select>

                                    </td>
                                    <td><input type="text" class="form-control" id="qty" placeholder="0.00">

                                    </td>
                                    <td><a id="addvalue" class="btn btn-success btn-sm"><i
                                                class="fas fa-plus-circle"></i></a></td>
                                </tr>

                                @foreach ($requisitionDetails as $value)
                                <tr>
                                    <td>
                                       {{$value->category->name}}
                                        <input type="hidden" name="category_nm[]" value="{{$value->category_id}}">
                                    </td>
                                    <td>{{$value->product->name}} <input type="hidden" name="product_nm[]" value="{{$value->product_id}}"></td>
                                    <td>{{$value->qty}} <input type="hidden" name="qty[]" value="{{$value->qty}}"></td>
                                    <td> <a class="remove btn btn-danger btn-sm">
                                            <i class="far fa-minus-square"></i> </a>
                                    </td>
                                </tr>
                                    
                                @endforeach

                            </tbody>
                        </table>

                        <div class="form-row form-group">
                            <div class="col-md-6">
                                <label for="">Note</label>
                                <textarea name="note" class="form-control" name="note" id="" cols="10"
                                    rows="4">{{$requisition->note}}</textarea>
                            </div>
                        </div>

                        <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> &nbsp;Approve</button>
                    </form>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">

                </div>
            </div>
        </div>
        <!-- /.col-->
    </div>


    <script>
        $(document).ready(function() {
            $('#addvalue').on('click', function() {
                var pr_name = $('#product_id option:selected').text();
                var category_name = $('#category_id option:selected').text();
                var qty = $('#qty').val();
                let change = $('#product_id').val();
                let category_id = $('#category_id').val();
                if (change && qty) {
                    var data = '<tr> <td>' + category_name +
                        ' <input type="hidden" name="category_nm[]" value="' +
                        category_id + '""></td> <td>' + pr_name +
                        ' <input type="hidden" name="product_nm[]" value="' +
                        change + '""></td> <td>' + qty + ' <input type="hidden" name="qty[]" value="' +
                        qty +
                        '""></td> <td> <a  class="remove btn btn-danger btn-sm"> <i class="far fa-minus-square"></i>  </a> </td> </tr> ';
                    $('#product_id').val(null).trigger("change");
                    $('#category_id').val(null).trigger("change");
                    $('#qty').val('');
                    $('tbody').append(data);
                }
            });

            $('#category_id').on('change', function() {
                let id = $(this).val();
                $.ajax({
                    url: "{{ route('inventorySetup.purchaserequisition.filterproduct') }}",
                    method: "GET",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id: id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#product_id').html(data);
                    }
                })
            })
        })

        $(document).on('click', '.remove', function() {
            $(this).closest('tr').remove();
        })
    </script>

@endsection
