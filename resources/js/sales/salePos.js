
function checkDuplicateCategory(catName) {
    var url = baseUrl + "SetupController/checkDuplicateCategory";
    $.ajax({
        type: 'POST',
        url: url,
        data: {
            'catName': catName
        },
        success: function (data)
        {
            if (data == 1) {
                $("#subBtn").attr('disabled', true);
                $("#errorMsg").show(10);
            } else {
                $("#subBtn").attr('disabled', false);
                $("#errorMsg").hide(10);
            }
        }
    });
}
setTimeout(function () {
    //$('.partisals').fadeOut('fast');
    $('#culinderReceive').fadeOut('fast');
}, 10);
function showCylinder() {
    $('#culinderReceive table tbody tr').remove();
    $("#culinderReceive").toggle(10);
}
$(document).ready(function () {
    var j = 0;
    $("#add_item2").click(function () {
        var productCatID = 2;
        var productID = $('#productID2').val();
        var productName = $('#productID2').find('option:selected').attr('productName');
        var quantity = $('.quantity2').val();
        var rate = $('.rate2').val();
        var price = $('.price2').val();
        var returnQuantity = $('.returnQuantity2').val();
        if (productCatID == '') {
            swal("Product Category can't be empty!", "Validation Error!", "error");
            return false;
        } else if (productID == '') {
            swal("Product Name can't be empty!", "Validation Error!", "error");
            return false;
        }else if (quantity == '') {
            swal("Quantity Can't be empty!", "Validation Error!", "error");
            return false;
        } else if (rate == '') {
            swal("Unit Price Can't be empty!", "Validation Error!", "error");
            return false;
        } else {
            $("#show_item2 tbody").append('<tr class="new_item2' + productCatID + productID + '"><input type="hidden" name="category_id2[]" value="' + productCatID + '"><td style="padding-left:15px;">' + productName + '<input type="hidden"  name="product_id2[]" value="' + productID + '"></td><td align="right"><input type="text" class="add_quantityPos2 text-right form-control decimal" name="quantity2[]" value="' + quantity + '"></td><td><a del_id2="' + productCatID + productID + '" class="delete_item2 btn form-control btn-danger" href="javascript:;" title=""><i class="fa fa-times"></i>&nbsp;Remove</a></td></tr>');
        }
        $("#subBtn").attr('disabled', false);
        $('.quantity2').val('');
        $('.rate2').val('');
        $('.price2').val('');
        $('.returnQuantity2').val('');
        $(".quantity2").attr("placeholder", "0");
        $('#productID2').val('').trigger('chosen:updated');
        $('#category_product2').val('').trigger('chosen:updated');
        findTotalCal2();
    });
    $(document).on('click', '.delete_item2', function () {
        var id = $(this).attr("del_id2");
        swal({
            title: "Are you sure ?",
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonColor: '#73AE28',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true,
            type: 'success'
        },
        function (isConfirm) {
            if (isConfirm) {
                $('.new_item2' + id).remove();
                findTotalCal2();
            } else {
                return false;
            }
        });
    });
});
function getCustomerCurrentBalace(customerId) {
    calculateCustomerDue();
}
function calculatePartialPayment() {
    calculateCustomerDue();
}
function showDuePayment(paymentValue) {
    var netAmount = parseFloat($("#netAmount").val());
    var allocateAmount = parseFloat(paymentValue);
    var dueAmount = netAmount - allocateAmount;
}
function showBankinfo(id) {
    $("#payment").val('');
    calculateCustomerDue();
    if (id == 3) {
        $("#showBankInfo").show(10);
    } else {
        $("#showBankInfo").hide(10);
    }
    if (id == 4) {
        $(".partisals").show(10);
    } else {
        $(".partisals").hide(10);
    }
    if (id == 2) {
        $(".creditDate").show(10);
    } else {
        $(".creditDate").hide(10);
    }
}
function saveNewCustomer() {
    var customerName = $("#customerName").val();
    var customerId = $("#customerId").val();
    if (customerName == '') {
        alert("Customer Name Can't be empty!!");
        return false;
    } else if (customerId == '') {
        alert("Customer Id Can't be empty!!");
        return false;
    } else {
        var url = baseUrl + "SalesController/saveNewCustomer";
        $.ajax({
            type: 'POST',
            url: url,
            data: $("#publicForm2").serializeArray(),
            success: function (data)
            {
                $('#myModal').modal('toggle');
                $('#newCustomerHide').hide();
                $('#customerid').chosen();
                //$('#customerid option').remove();
                $('#customerid').append($(data));
                $("#customerid").trigger("chosen:updated");
            }
        });
    }
}
function checkDuplicatePhone(phone) {
    var url = baseUrl + "SalesController/checkDuplicatePhone";
    $.ajax({
        type: 'POST',
        url: url,
        data: {
            'phone': phone
        },
        success: function (data)
        {
            if (data == 1) {
                $("#subBtn2").attr('disabled', true);
                $("#errorMsg").show(10);
            } else {
                $("#subBtn2").attr('disabled', false);
                $("#errorMsg").hide(10);
            }
        }
    });
}
$(document).ready(function () {
    $('.quantity').keyup(function () {
        priceCal();
    });
    $('.rate').keyup(function () {
        var rate = parseFloat($(this).val());
        if (isNaN(rate)) {
            rate = 0;
        }
        parseFloat($(this).val(rate)).toFixed(2)
        priceCal();
    });
});
function priceCal() {
    var quantity = parseFloat($('.quantity').val());
    if (isNaN(quantity)) {
        quantity = 0;
    }
    var rate = parseFloat($('.rate').val());
    if (isNaN(rate)) {
        rate = 0;
    }
    $('.price').val(parseFloat(rate * quantity).toFixed(2));
}
function calDiscount() {
    findDiscount();
    findVatAmount();
    calculatePartialPayment();
}
var findDiscount = function () {
    var totalPrice = parseFloat($(".total_price").text());
    if (isNaN(totalPrice)) {
        totalPrice = 0;
    }
    if (totalPrice == '') {
        $("#disCount").val('');
    }
    var disCount = parseFloat($("#disCount").val());
    if (isNaN(disCount)) {
        disCount = 0;
    }
    //var deductDiscount = (disCount / 100) * totalPrice;
    $("#grandTotal").val(parseFloat(Math.round(totalPrice - disCount)).toFixed(2));
};
var findVatAmount = function () {
    var vatValue = $("#vatAmount").val();
    if (isNaN(vatValue)) {
        vatValue = 0;
    }
    var grandTotal = parseFloat($("#grandTotal").val());
    if (isNaN(grandTotal)) {
        grandTotal = 0;
    }
    var vatForwardAmount = parseFloat((vatValue / 100) * grandTotal);
    if (isNaN(vatForwardAmount)) {
        vatForwardAmount = 0;
    }
    $(".totalVatAmount").html(parseFloat(Math.round(vatForwardAmount)).toFixed(2));
    $("#netAmount").val(parseFloat(Math.round(vatForwardAmount) + grandTotal).toFixed(2));
};
var findTotalQty = function () {
    var total_quantity = 0;
    $.each($('.add_quantity'), function () {
        quantity = $(this).val();
        quantity = Number(quantity);
        total_quantity += quantity;
    });
    $('.total_quantity').html(parseFloat(total_quantity));
};
var findTotalQty2 = function () {
    var total_quantity2 = 0;
    $.each($('.add_quantityPos2'), function () {
        quantity = $(this).val();
        quantity = Number(quantity);
        total_quantity2 += quantity;
    });
    $('.total_quantity2').html(parseFloat(total_quantity2));
};
var findTotalReturnQty = function () {
    var total_return_quantity = 0;
    $.each($('.add_ReturnQuantity'), function () {
        quantity = $(this).val();
        quantity = Number(quantity);
        total_return_quantity += quantity;
    });
    $('.total_return_quantity').html(parseFloat(total_return_quantity));
};
var findTotalRate = function () {
    var total_rate = 0;
    $.each($('.add_rate'), function () {
        rate = $(this).val();
        rate = Number(rate);
        total_rate += rate;
    });
    $('.total_rate').html(parseFloat(total_rate).toFixed(2));
};
var findTotalPrice = function () {
    var total_price = 0;
    $.each($('.add_price'), function () {
        price = $(this).val();
        price = Number(price);
        total_price += price;
    });
    $('.total_price').html(parseFloat(total_price).toFixed(2));
// discount(total_price);
// findVatAmount(total_price);
};
var calculateCustomerDue = function () {
    var customerid = parseFloat($('#customerid').val());
    var netAmount = parseFloat($('#netAmount').val());
    var payment = parseFloat($("#payment").val());
    if (isNaN(payment)) {
        payment = 0;
    }
    if (isNaN(netAmount)) {
        netAmount = 0;
    }
    var url = baseUrl + "SalesController/getCustomerCurrentBalance";
    $.ajax({
        type: 'POST',
        url: url,
        data: {
            customerId: customerid
        },
        success: function (data)
        {
            data = parseFloat(data);


            if (isNaN(data)) {
                data = 0;
            }
            $('.currentBalance').val(parseFloat((netAmount + data) - payment).toFixed(2));
        }
    });
};
var calculateCustomerDueDeduct = function () {
    var customerid = parseFloat($('#customerid').val());
    var netAmount = parseFloat($('#netAmount').val());
    var payment = parseFloat($("#payment").val());
    if (isNaN(payment)) {
        payment = 0;
    }
    if (isNaN(netAmount)) {
        netAmount = 0;
    }
    var url = baseUrl + "SalesController/getCustomerCurrentBalance";
    $.ajax({
        type: 'POST',
        url: url,
        data: {
            customerId: customerid
        },
        success: function (data)
        {
            data = parseFloat(data);
            if (isNaN(data)) {
                data = 0;
            }
            $('.currentBalance').val((netAmount + data) - payment);
        }
    });
};
var findTotalCal2 = function () {
    findTotalQty2();
};
var findTotalCal = function () {
    findTotalQty();
    findTotalReturnQty();
    findTotalRate();
    findTotalPrice();
    calDiscount();
    calculatePartialPayment();
};
function checkStockOverQty(givenStock) {
    var orgiStock = parseFloat($("#stockQty").val());
    var givenStock = parseFloat(givenStock);
    if (isNaN(givenStock)) {
        givenStock = 0;
    }
    if (isNaN(orgiStock)) {
        orgiStock = 0;
    }
    //  alert(orgiStock);
    if (orgiStock < givenStock) {
        $(".quantity").val('');
        $(".quantity").val(parseFloat(orgiStock));
        productItemValidation("Stock Quantity Not Available.");
    }
}
$(document).on("keyup", ".add_quantity", function () {
    var id_arr = $(this).attr('id');
    productTotal(id_arr)
});
function productTotal(id_arr) {
    //var id_arr = $(this).attr('id');
    var id = id_arr.split("_");
    var element_id = id[id.length - 1];
    var quantity = parseFloat($("#qty_" + element_id).val());
    if (isNaN(quantity)) {
        quantity = 0;
    }
    var rate = parseFloat($("#rate_" + element_id).val());
    if (isNaN(rate)) {
        rate = 0;
    }
    var totalAmount = quantity * rate;
    $("#tprice_" + element_id).val(parseFloat(totalAmount).toFixed(2));
    var row_total = 0;
    $.each($('.add_price'), function () {
        quantity = $(this).val();
        quantity = Number(quantity);
        row_total += quantity;
    });
    $('.total_price').html(parseFloat(row_total).toFixed(2));
    calculateCustomerDue();
    findTotalCal();
}
$(document).on("keyup", ".add_rate", function () {
    var id_arr = $(this).attr('id');
    var id = id_arr.split("_");
    var element_id = id[id.length - 1];
    var quantity = parseFloat($("#qty_" + element_id).val());
    if (isNaN(quantity)) {
        quantity = 0;
    }
    var rate = parseFloat($("#rate_" + element_id).val());
    if (isNaN(rate)) {
        rate = 0;
    }
    var totalAmount = quantity * rate;
    $("#tprice_" + element_id).val(parseFloat(totalAmount).toFixed(2));
    var row_total = 0;
    $.each($('.add_price'), function () {
        quantity = $(this).val();
        quantity = Number(quantity);
        row_total += quantity;
    });
    $('.total_price').html(parseFloat(row_total).toFixed(2));
    calculateCustomerDue();
    findTotalCal();
});
function checkProductAddedOrNot(pid) {
    var previousProductID = parseFloat($('#productID_' + pid).val());
    if (previousProductID == pid) {
        //givenQuantity = givenQuantity + previousProductQuantity;
        // $('#qty_' + productID).val(givenQuantity);
        //productTotal('_' + productID)
        return false;
    } else {
        return true;
    }
}
$(document).ready(function () {
    $("#add_item").click(function () {
        var productCatID = $('#productID').find('option:selected').attr('categoryId');
        var productCatName = $('#productID').find('option:selected').attr('categoryName');
        var productID = $('#productID').val();
        var productName = $('#productID').find('option:selected').attr('productName');

        var quantity = $('.quantity').val();
        var rate = $('.rate').val();
        var price = $('.price').val();
        var returnQuantity = $('.returnQuantity').val();
        var checkStatus = checkProductAddedOrNot(productID);
        if (checkStatus == true) {
            if (productCatID == '') {
                swal("Product Category can't be empty!", "Validation Error!", "error");
                return false;
            } else if (productID == '') {
                swal("Product Name can't be empty!", "Validation Error!", "error");
                return false;
            } else if (quantity == '') {
                swal("Quantity Can't be empty!", "Validation Error!", "error");
                return false;
            } else if (rate == '') {
                swal("Unit Price Can't be empty!", "Validation Error!", "error");
                return false;
            } else {
                var tab = "";
                if (productCatID == 2) {
                    tab = '<tr class="new_item' + productID + '">' +
                    '<td style="padding-left:15px;">' +
                    '[ ' + productCatName + '] -  '  + productName  +
                    '<input id="productID_' + productID + '" name="productID[]" value="' + productID + '" type="hidden">' +
                    '<input type="hidden" name="category_id[]" value="' + productCatID + '">' +
                    '</td>' +

                    '<input type="hidden"  name="product_id[]" value="' + productID + '">' +

                    '<td align="right">' +
                    '<input type="text" id="qty_' + productID + '" class="form-control text-right add_quantity decimal" value="' + quantity + '" placeholder="' + quantity + '" onkeyup="checkStockOverQty(this.value)" name="quantity[]" value="">' +
                    '</td>' +
                    '<td align="right">' +
                    '<input type="text" class="add_ReturnQuantity  text-right form-control decimal" name="returnQuantity[]" value="' + returnQuantity + '" >' +
                    '</td>' +
                    '<td align="right">' +
                    '<input type="text" id="rate_' + productID + '" class="form-control add_rate text-right decimal" name="rate[]" value="' + rate + '" placeholder="0.00">' +
                    '</td>' +
                    '<td align="right">' +
                    '<input readonly type="text" class="add_price text-right form-control" id="tprice_' + productID + '" name="price[]" value="' + price + '">' +
                    '</td>' +
                    '<td>' +
                    '<a del_id="' + productID + '" class="delete_item btn form-control btn-danger" href="javascript:;" title=""><i class="fa fa-times"></i>&nbsp;Remove</a>' +
                    '</td>' +
                    '</tr>';
                    $("#show_item tbody").append(tab);
                //$("#show_item tbody").append('' + '' + '<input type="hidden" name="category_id[]" value="' + productCatID + '"></td><td style="padding-left:15px;">' + productName + '<input type="hidden"  name="product_id[]" value="' + productID + '"></td><td style="padding-left:15px;">' + unitName + '<input type="hidden" name="unit_id[]" value="' + productUnit + '"></td><td align="right"><input type="text" id="qty_' + j + '" class="form-control text-right add_quantity decimal" onkeyup="checkStockOverQty(this.value)" name="quantity[]" value="' + quantity + '"></td><td align="right"><input type="text" class="add_ReturnQuantity  text-right form-control decimal" name="returnQuantity[]" value="' + returnQuantity + '"></td><td align="right"><input type="text" id="rate_' + j + '" class="form-control add_rate text-right decimal" name="rate[]" value="' + rate + '"></td><td align="right"><input readonly type="text" class="add_price text-right form-control" id="tprice_' + j + '" name="price[]" value="' + price + '"></td><td><a del_id="' + j + '" class="delete_item btn form-control btn-danger" href="javascript:;" title=""><i class="fa fa-times"></i>&nbsp;Remove</a></td></tr>');
                } else {
                    tab = '<tr class="new_item' + productID + '">' +
                    '<td style="padding-left:15px;">' +
                    '[ ' + productCatName + '] -  '  + productName  +
                    '<input id="productID_' + productID + '" name="productID[]" value="' + productID + '" type="hidden">' +
                    '<input type="hidden" name="category_id[]" value="' + productCatID + '">' +
                    '</td>' +

                    '<input type="hidden"  name="product_id[]" value="' + productID + '">' +


                    '<td align="right">' +
                    '<input type="text" id="qty_' + productID + '" class="form-control text-right add_quantity decimal" value="' + quantity + '" placeholder="' + quantity + '" onkeyup="checkStockOverQty(this.value)" name="quantity[]" value="">' +
                    '</td>' +
                    '<td align="right">' +
                    '<input type="text" class="add_ReturnQuantity  text-right form-control decimal" name="returnQuantity[]" value="' + returnQuantity + '" readonly>' +
                    '</td>' +
                    '<td align="right">' +
                    '<input type="text" id="rate_' + productID + '" class="form-control add_rate text-right decimal" name="rate[]" value="' + rate + '" placeholder="0.00">' +
                    '</td>' +
                    '<td align="right">' +
                    '<input readonly type="text" class="add_price text-right form-control" id="tprice_' + productID + '" name="price[]" value="' + price + '">' +
                    '</td>' +
                    '<td>' +
                    '<a del_id="' + productID + '" class="delete_item btn form-control btn-danger" href="javascript:;" title=""><i class="fa fa-times"></i>&nbsp;Remove</a>' +
                    '</td>' +
                    '</tr>';
                    $("#show_item tbody").append(tab);
                    //$('#ProductUnitId_'+productID).val(productUnit) ;
                //$("#show_item tbody").append('<tr class="new_item' + j + '"><td style="padding-left:15px;">' + productCatName + '<input type="hidden" name="category_id[]" value="' + productCatID + '"></td><td style="padding-left:15px;">' + productName + '<input type="hidden"  name="product_id[]" value="' + productID + '"></td><td style="padding-left:15px;">' +  unitName + '<input type="hidden" name="unit_id[]" value="' + productUnit + '"></td><td align="right"><input type="text" id="qty_'+ j +'" class="form-control text-right add_quantity decimal" onkeyup="checkStockOverQty(this.value)" name="quantity[]" value="' + quantity + '"></td><td align="right"><input type="text" class="add_ReturnQuantity  text-right form-control decimal" name="returnQuantity[]" value="' + returnQuantity + '" readonly></td><td align="right"><input type="text" id="rate_'+ j +'" class="form-control add_rate text-right decimal" name="rate[]" value="' + rate + '"></td><td align="right"><input readonly type="text" class="add_price text-right form-control" id="tprice_'+ j +'" name="price[]" value="' + price + '"></td><td><a del_id="'+j+'" class="delete_item btn form-control btn-danger" href="javascript:;" title=""><i class="fa fa-times"></i>&nbsp;Remove</a></td></tr>');
                }
            }
        } else {
            var givenQuantity = parseInt(quantity);
            var previousProductQuantity = parseInt($('#qty_' + productID).val());
            $('#qty_' + productID).val(givenQuantity + previousProductQuantity);
            productTotal('_'+productID);
        }
        $("#subBtn").attr('disabled', false);
        $('.quantity').val('');
        $('.rate').val('');
        $('.price').val('');
        $('.returnQuantity').val('');
        $(".quantity").attr("placeholder", "0");
        $('#productID').val('').trigger('chosen:updated');
        $('#category_product').val('').trigger('chosen:updated');
        findTotalCal();
        setTimeout(function () {
            calculateCustomerDue();
        }, 100);
    //   j++;
    });
    $(document).on('click', '.delete_item', function () {
        var id = $(this).attr("del_id");
        swal({
            title: "Are you sure ?",
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonColor: '#73AE28',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true,
            type: 'success'
        },
        function (isConfirm) {
            if (isConfirm) {
                $('.new_item' + id).remove();
                findTotalCal();
                setTimeout(function () {
                    calculateCustomerDue();
                }, 100);
            } else {
                return false;
            }
        });
    });
});
function getProductPrice(product_id) {
    $("#stockQty").val('');
    $(".quantity").val('');

    $.ajax({
        type: "POST",
        url: baseUrl + "FinaneController/getProductPriceForSale",
        data: 'product_id=' + product_id,
        success: function (data) {
            if(isNaN(data)){
                data=0;
            }
            $('.rate').val('');
        }
    });
    $.ajax({
        type: "POST",
        url: baseUrl + "FinaneController/getProductStock",
        data: 'product_id=' + product_id,
        success: function (data) {
            var mainStock = parseFloat(data);

            if(isNaN(mainStock)){
                mainStock=0;
            }
            if (data != '') {
                $("#stockQty").val(data);
                $(".quantity").attr("disabled", false);
                if (mainStock <= 0) {
                    $(".quantity").attr("disabled", true);
                    $(".quantity").attr("placeholder", "Stock is =0 ");
                } else {
                    $(".quantity").attr("disabled", false);
                    $(".quantity").attr("placeholder", "Stock is = " + mainStock);
                }
            } else {
                $("#stockQty").val('');
                $(".quantity").attr("disabled", true);
                $(".quantity").attr("placeholder", "Stock is = 0");
            }
        }
    });
}
function getProductPrice2(product_id) {
    $("#stockQty2").val('');
    $(".quantity2").val('');
    var total_quantity2 = 0;
    $.each($('.add_quantityPos2'), function () {
        quantity = $(this).val();
        quantity = Number(quantity);
        total_quantity2 += quantity;
    });
    if (isNaN(total_quantity2)) {
        total_quantity2 = 0;
    }
    $.ajax({
        type: "POST",
        url: baseUrl + "FinaneController/getProductPriceForSale",
        data: 'product_id=' + product_id,
        success: function (data) {
            if(isNaN(data)){
                data=0
            }
            if (data != '0.00') {
                $('.rate2').val(data);
            } else {
                $('.rate2').val('');
            }
        }
    });
    $.ajax({
        type: "POST",
        url: baseUrl + "FinaneController/getProductStock",
        data: 'product_id=' + product_id,
        success: function (data) {
            var mainStock = parseFloat(data) - parseFloat(total_quantity);

            if(isNaN(mainStock)){
                mainStock=0;
            }

            if (data != '') {
                $("#stockQty2").val(data);
                $(".quantity2").attr("disabled", false);
                if (mainStock <= 0) {
                    $(".quantity2").attr("disabled", true);
                    $(".quantity2").attr("placeholder", "Stock is =0 ");
                } else {
                    $(".quantity2").attr("disabled", false);
                    $(".quantity2").attr("placeholder", "Stock is = " + mainStock);
                }
            } else {
                $("#stockQty2").val('');
                $(".quantity2").attr("disabled", true);
                $(".quantity2").attr("placeholder", "Stock is = 0");
            }
        }
    });
}
function getProductList(cat_id) {
    if (cat_id == 2) {
        $(".returnQuantity").attr("readonly", false);
    } else {
        $(".returnQuantity").attr("readonly", true);
    }
    $.ajax({
        type: "POST",
        url: baseUrl + "InventoryController/getProductList",
        data: 'cat_id=' + cat_id,
        success: function (data) {
            $('#productID').chosen();
            $('#productID option').remove();
            $('#productID').append($(data));
            $("#productID").trigger("chosen:updated");
        }
    });
}
function getProductList2(cat_id) {
    $.ajax({
        type: "POST",
        url: baseUrl + "InventoryController/getProductList",
        data: 'cat_id=' + cat_id,
        success: function (data) {
            $('#productID2').chosen();
            $('#productID2 option').remove();
            $('#productID2').append($(data));
            $("#productID2").trigger("chosen:updated");
        }
    });
}