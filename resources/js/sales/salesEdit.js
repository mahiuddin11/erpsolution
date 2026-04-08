
$(document).on("keyup", ".add_quantity", function () {
    var id_arr = $(this).attr('id');
    var id = id_arr.split("_");
    var element_id = id[id.length - 1];
    var quantity = parseFloat($("#qty_" + element_id).val());
    if(isNaN(quantity)){
        quantity=0;
    }
    var rate= parseFloat($("#rate_" + element_id).val());
    if(isNaN(rate)){
        rate=0;
    }
    var totalAmount = quantity * rate;
    $("#tprice_"+ element_id).val(parseFloat(totalAmount).toFixed(2));
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
$(document).on("keyup", ".add_rate", function () {
    var id_arr = $(this).attr('id');
    var id = id_arr.split("_");
    var element_id = id[id.length - 1];
    var quantity = parseFloat($("#qty_" + element_id).val());
    if(isNaN(quantity)){
        quantity=0;
    }
    var rate= parseFloat($("#rate_" + element_id).val());
    if(isNaN(rate)){
        rate=0;
    }
    var totalAmount = quantity * rate;
    $("#tprice_"+ element_id).val(parseFloat(totalAmount).toFixed(2));
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

$(document).on("keyup", ".add_ReturnQuantity", function () {

    calculateCustomerDue();
    findTotalCal();

});


function checkDuplicateCategory(catName){
    var url = baseUrl+ "SetupController/checkDuplicateCategory";
    $.ajax({
        type: 'POST',
        url: url,
        data:{
            'catName': catName
        },
        success: function (data)
        {
            if(data == 1){
                $("#subBtn").attr('disabled',true);
                $("#errorMsg").show(10);
            }else{
                $("#subBtn").attr('disabled',false);
                $("#errorMsg").hide(10);
            }
        }
    });
}

function showCylinder(){
    $('#culinderReceive table tfoot tr').remove();
    $("#culinderReceive").toggle(10);
}
$(document).ready(function () {
    var j = 1;
    $("#add_item2").click(function () {

        var rowCount = $('#show_item2 tr').length;
        var productCatID = 2;
        var productID = $('#productID2').val();
        var productName = $('#productID2').find('option:selected').attr('productName');
        var quantity = $('.quantity2').val();
        var rate = $('.rate2').val();
        var price = $('.price2').val();
        var returnQuantity = $('.returnQuantity2').val();
        if(productCatID  == ''){
            swal("Product Category can't be empty!", "Validation Error!", "error");
            return false;
        }else if(productID == ''){
            swal("Product Name can't be empty!", "Validation Error!", "error");
            return false;
        }else if(quantity == ''){
            swal("Quantity Can't be empty!", "Validation Error!", "error");
            return false;
        }else if(rate == ''){
            swal("Unit Price Can't be empty!", "Validation Error!", "error");
            return false;
        }else{

            if(rowCount == 2){
                $("#show_item2 tfoot").prepend('<tr><td align="right"> Total </td><td align="right"><span class="total_quantity2">0.00</span></td></tr>');
            }


            $("#show_item2 tfoot").prepend('<tr class="new_item2' + productCatID + productID + j +'"><input type="hidden" name="category_id2[]" value="' + productCatID + '"><td style="padding-left:15px;">' + productName + '<input type="hidden"  name="product_id2[]" value="' + productID + '"></td><td align="right"><input type="text" class="add_quantity2 text-right form-control decimal" name="quantity2[]" value="' + quantity + '"></td><td><a del_id2="' + productCatID + productID  + j + '" class="delete_item2 btn form-control btn-danger" href="javascript:;" title=""><i class="fa fa-times"></i>&nbsp;Remove</a></td></tr>');
        }
        j++;
        $("#subBtn").attr('disabled',false);
        $('.quantity2').val('');
        $('.rate2').val('');
        $('.price2').val('');
        $('.returnQuantity2').val('');
        $(".quantity2").attr("placeholder", "0");
        $('#productID2').val('').trigger('chosen:updated');
        $('#category_product2').val('').trigger('chosen:updated');
        findTotalQty2();
    });
    $(document).on('click','.delete_item2', function () {
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
                findTotalQty2();
            }else{
                return false;
            }
        });
    });
});
function getCustomerCurrentBalace(customerId){
    calculateCustomerDue();
    setTimeout(function() {
        calcutateFinal();
    }, 100);
    $.ajax({
        type: "POST",
        url: baseUrl + "InventoryController/loadCusSupDetails",
        data: {
            supCusId:customerId,
            type:1
        },
        success: function (data) {
            $("#loadCusSup").html(data);
        }
    });
}


function getCustomerCurrentBala(){

    calculateCustomerDue();
    setTimeout(function() {
        calcutateFinalEdit();
    }, 100);

}




function calcutateFinalEdit(){
    var total_price = parseFloat($(".total_price").text());
    if(isNaN(total_price)){
        total_price=0;
    }
    var payment = parseFloat($("#payment").val());
    if(isNaN(payment)){
        payment=0;
    }
    var previousDue = parseFloat($("#previousDue").val());
    if(isNaN(previousDue)){
        previousDue=0;
    }


    var discount = parseFloat($("#disCount").val());
    if(isNaN(discount)){
        discount=0;
    }
    //var total_price = (total_price + transportation +  loader) - discount;




    if(payment > total_price ){
        $("#currentDue").val(parseFloat(0.00).toFixed(2));
    }else{
        $("#currentDue").val(parseFloat(total_price - payment).toFixed(2));
    }
    $("#previousDue").val(parseFloat(previousDue- (total_price - payment)).toFixed(2));
    $("#totalDue").val(parseFloat(((previousDue - (total_price - payment))+(total_price - payment))).toFixed(2));
}


function calculatePartialPayment(){
    setTimeout(function() {
        calcutateFinal();
    }, 100);

}
function showDuePayment(paymentValue){
    var netAmount = parseFloat($("#netAmount").val());
    var allocateAmount=parseFloat(paymentValue);
    var dueAmount =netAmount - allocateAmount;
}
function showBankinfo(id){
    $(".chaque_amount_class").hide(10);

    calcutateFinal();

    if(id == 3){
        $("#showBankInfo").show(10);
        $(".chaque_amount_class").show(10);
    }else{
        $("#showBankInfo").hide(10);
    }
    if(id == 4){
        $(".partisals").show(10);
    }else{
        $(".partisals").hide(10);
    }
    if(id == 2){
        $(".creditDate").show(10);
    }else{
        $(".creditDate").hide(10);
    }
}
function saveNewCustomer(){
    var customerName=$("#customerName").val();
    var customerId=$("#customerId").val();
    if(customerName == ''){
        alert("Customer Name Can't be empty!!");
        return false;
    }else if(customerId == ''){
        alert("Customer Id Can't be empty!!");
        return false;
    }else{
        var url = baseUrl + "SalesController/saveNewCustomer";
        $.ajax({
            type: 'POST',
            url: url,
            data:$("#publicForm2").serializeArray(),
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
function checkDuplicatePhone(phone){
    var url = baseUrl+ "SalesController/checkDuplicatePhone";
    $.ajax({
        type: 'POST',
        url: url,
        data:{
            'phone': phone
        },
        success: function (data)
        {
            if(data == 1){
                $("#subBtn2").attr('disabled',true);
                $("#errorMsg").show(10);
            }else{
                $("#subBtn2").attr('disabled',false);
                $("#errorMsg").hide(10);
            }
        }
    });
}
$(document).ready(function () {

    $('.rate').blur(function () {
        var rate = parseFloat($(this).val());
        if(isNaN(rate)){
            rate=0;
        }
        $(this).val(parseFloat(rate).toFixed(2));
    });

    $('.quantity').keyup(function () {
        priceCal();
    });
    $('.rate').keyup(function () {
        priceCal();
    });
});
function priceCal() {
    var quantity = parseFloat($('.quantity').val());
    var rate = parseFloat($('.rate').val());
    $('.price').val(parseFloat(rate * quantity).toFixed(2));
}
function calDiscount() {
    findDiscount();
    findVatAmount();
    calculatePartialPayment();
}
var findDiscount = function () {
    var totalPrice = parseFloat($(".total_price").text());
    if(isNaN(totalPrice)){
        totalPrice=0;
    }
    if(totalPrice == ''){
        $("#disCount").val('');
    }
    var disCount = parseFloat($("#disCount").val());
    if(isNaN(disCount)){
        disCount=0;
    }
    //var deductDiscount = (disCount / 100) * totalPrice;
    $("#grandTotal").val(parseFloat(Math.round(totalPrice - disCount)).toFixed(2));
    calcutateFinal();
};
var findVatAmount = function () {

    var loader = parseFloat($("#loader").val());
    if(isNaN(loader)){
        loader=0;
    }
    var transportation = parseFloat($("#transportation").val());
    if(isNaN(transportation)){
        transportation=0;
    }

    var vatValue = $("#vatAmount").val();
    if(isNaN(vatValue)){
        vatValue=0;
    }
    var grandTotal = parseFloat($("#grandTotal").val());
    if(isNaN(grandTotal)){
        grandTotal=0;
    }
    var vatForwardAmount = parseFloat((vatValue / 100) * grandTotal);
    if(isNaN(vatForwardAmount)){
        vatForwardAmount=0;
    }
    $(".totalVatAmount").html(parseFloat(Math.round(vatForwardAmount)).toFixed(2));
    $("#netAmount").val(parseFloat(Math.round(vatForwardAmount) + grandTotal + loader + transportation).toFixed(2));
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
    $.each($('.add_quantity2'), function () {
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
    $('.total_totalPrice').html(parseFloat(total_price).toFixed(2));
// discount(total_price);
// findVatAmount(total_price);
};
var calculateCustomerDue = function () {
    var customerid=parseFloat($('#customerid').val());
    var url = baseUrl + "SalesController/getCustomerCurrentBalance";
    $.ajax({
        type: 'POST',
        url: url,
        data:{
            customerId: customerid
        },
        success: function (data)
        {
            data=parseFloat(data);
            if(isNaN(data)){
                data=0;
            }
            $('#previousDue').val(parseFloat(data).toFixed(2));
        }
    });
};

var findTotalCal = function () {
    findTotalQty();
    findTotalReturnQty();
    findTotalRate();
    findTotalPrice();
    calDiscount();
    calculatePartialPayment();
};
function checkStockOverQty(givenStock){
    var orgiStock=parseFloat($("#stockQty").val());
    var givenStock= parseFloat(givenStock);
    if(isNaN(givenStock)){
        givenStock=0;
    }
    if(isNaN(orgiStock)){
        orgiStock=0;
    }
    //  alert(orgiStock);
    if(orgiStock < givenStock){
        $(".quantity").val('');
        $(".quantity").val(parseFloat(orgiStock));
        productItemValidation("Stock Quantity Not Available.");
    }
}
$(document).ready(function () {
    var j = 0;
    $("#add_item").click(function () {
        var productCatID = $('#categoryId').val();
        var productCatName = $('#categoryId').find('option:selected').attr('categoryName');

        var productID = $('#productID2').val();
        var productName = $('#productID2').find('option:selected').attr('productName');
        var quantity = $('.quantity').val();
        var bundle = $('.bundle').val();
        var bundleValue = $('#bundleValue').val();
        var rate = $('.rate').val();
        var price = $('.price').val();
        if(productCatID  == ''){
            swal("Product Category can't be empty!", "Validation Error!", "error");
            return false;
        }else if(productID == ''){
            swal("Product Name can't be empty!", "Validation Error!", "error");
            return false;
        }else if(productCatID == 1 && (bundleValue == '' || bundleValue <= 0)){
             swal("Bundle Qty Can't be empty!", "Validation Error!", "error");
            return false;
        }else if(quantity == ''){
            swal("Quantity Can't be empty!", "Validation Error!", "error");
            return false;
        }else if(rate == ''){
            swal("Unit Price Can't be empty!", "Validation Error!", "error");
            return false;
        }else{
            if(productCatID == 2){
                $("#show_item tfoot").prepend('<tr class="new_item' + productCatID + productID + j + '"><input type="hidden" name="category_id[]" value="' + productCatID + '"><td style="padding-left:15px;">' + productCatName + '</td><td style="padding-left:15px;">' + productName + '<input type="hidden"  name="product_id[]" value="' + productID + '"></td><td align="right"><input type="text" class="add_bundle decimal text-right form-control" name="bundle[]" value="' + bundle + '"  id="bundle_'+ j +'"><td align="right"><input type="text" class="add_quantity decimal text-right form-control" name="quantity[]" value="' + quantity + '"  id="qty_'+ j +'"></td><td align="right"><input type="text" class="add_rate  text-right form-control decimal" name="rate[]" value="' + rate + '"  id="rate_'+ j +'"></td><td align="right"><input type="text" class="add_price  text-right form-control" readonly name="price[]" value="' + price + '"  id="tprice_'+ j +'"></td><td><a del_id="' + productCatID + productID  + j + '" class="delete_item btn form-control btn-danger" href="javascript:;" title=""><i class="fa fa-times"></i>&nbsp;Remove</a></td></tr>');
            }else{
                $("#show_item tfoot").prepend('<tr class="new_item' + productCatID + productID + j + '"><input type="hidden" name="category_id[]" value="' + productCatID + '"><td style="padding-left:15px;">' + productCatName + '</td><td style="padding-left:15px;">' + productName + '<input type="hidden"  name="product_id[]" value="' + productID + '"></td><td align="right"><input type="text" class="add_bundle decimal text-right form-control" name="bundle[]" value="' + bundle + '"  id="bundle_'+ j +'"><td align="right"><input type="text" class="add_quantity decimal text-right form-control" name="quantity[]" value="' + quantity + '"  id="qty_'+ j +'"></td><td align="right"><input type="text" class="add_rate  text-right form-control decimal" name="rate[]" value="' + rate + '"  id="rate_'+ j +'"></td><td align="right"><input type="text" class="add_price  text-right form-control" readonly name="price[]" value="' + price + '"  id="tprice_'+ j +'"></td><td><a del_id="' + productCatID + productID  + j + '" class="delete_item btn form-control btn-danger" href="javascript:;" title=""><i class="fa fa-times"></i>&nbsp;Remove</a></td></tr>');
            }
            j++;
        }
        $("#subBtn").attr('disabled',false);
        $('.quantity').val('');
        $('.rate').val('');
        $('.bundle').val('');
        $('.price').val('');
        $(".quantity").attr("placeholder", "0");
        $('#categoryId').val('').trigger('chosen:updated');
        $('#productID2').val('').trigger('chosen:updated');
        $('#category_product').val('').trigger('chosen:updated');
        findTotalCal();
        setTimeout(function() {
            calcutateFinal();
        }, 100);
        j++;
    });
    $(document).on('click','.delete_item', function () {
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
                setTimeout(function() {
                    calcutateFinal();
                }, 100);
            }else{
                return false;
            }
        });
    });
});

function calcutateFinal(){

    findVatAmount();
    var loader = parseFloat($("#loader").val());
    if(isNaN(loader)){
        loader=0;
    }
    var transportation = parseFloat($("#transportation").val());
    if(isNaN(transportation)){
        transportation=0;
    }


    var total_price = parseFloat($(".total_price").text());
    if(isNaN(total_price)){
        total_price=0;
    }

    var discount = parseFloat($("#disCount").val());
    if(isNaN(discount)){
        discount=0;
    }
    var total_price = (total_price + transportation +  loader) - discount;

    var payment = parseFloat($("#payment").val());
    if(isNaN(payment)){
        payment=0;
    }
    var previousDue = parseFloat($("#previousDue").val());
    if(isNaN(previousDue)){
        previousDue=0;
    }
    if(payment > total_price ){
        $("#currentDue").val(parseFloat(0.00).toFixed(2));
    }else{
        $("#currentDue").val(parseFloat(total_price - payment).toFixed(2));
    }
    $("#totalDue").val(parseFloat((total_price+previousDue) - payment).toFixed(2));
}

function getProductPrice(product_id) {
    $("#stockQty").val('');
    $(".quantity").val('');

    var productCatID = parseFloat($('#productID').find('option:selected').attr('categoryId'));
    if(productCatID ==2){
        $(".returnQuantity").attr('readonly',false);
    }else{
        $(".returnQuantity").attr('readonly',true);
    }
    $.ajax({
        type: "POST",
        url: baseUrl + "FinaneController/getProductPriceForSale",
        data: 'product_id=' + product_id,
        success: function (data) {
            $('.rate').val('');
        }
    });
    $.ajax({
        type: "POST",
        url: baseUrl+ "FinaneController/getProductStock",
        data: 'product_id=' + product_id,
        success: function (data) {
            var mainStock = parseFloat(data);
            if(isNaN(mainStock)){
                mainStock=0;
            }

            if(data !=''){
                $("#stockQty").val(data);
                $(".quantity").attr("disabled",false);
                if(mainStock <= 0){
                    $(".quantity").attr("disabled",true);
                    $(".quantity").attr("placeholder", "0 ");
                }else{
                    $(".quantity").attr("disabled",false);
                    $(".quantity").attr("placeholder", " "+mainStock);
                }
            }else{
                $("#stockQty").val('');
                $(".quantity").attr("disabled",true);
                $(".quantity").attr("placeholder", "0");
            }
        }
    });
}
function getProductPrice2(product_id) {
    $("#stockQty2").val('');
    $(".quantity2").val('');
    var total_quantity2 = 0;
    $.each($('.add_quantity2'), function () {
        quantity = $(this).val();
        quantity = Number(quantity);
        total_quantity2 += quantity;
    });
    if(isNaN(total_quantity2)){
        total_quantity2=0;
    }
    $.ajax({
        type: "POST",
        url: baseUrl+ "FinaneController/getProductPriceForSale",
        data: 'product_id=' + product_id,
        success: function (data) {
            if (data != '0.00') {
                $('.rate2').val(data);
            } else {
                $('.rate2').val('');
            }
        }
    });
    $.ajax({
        type: "POST",
        url: baseUrl+ "FinaneController/getProductStock",
        data: 'product_id=' + product_id,
        success: function (data) {
            var mainStock = parseFloat(data) - parseFloat(total_quantity);
            if(data !=''){
                $("#stockQty2").val(data);
                $(".quantity2").attr("disabled",false);
                if(mainStock <= 0){
                    $(".quantity2").attr("disabled",true);
                    $(".quantity2").attr("placeholder", "0 ");
                }else{
                    $(".quantity2").attr("disabled",false);
                    $(".quantity2").attr("placeholder", ""+mainStock);
                }
            }else{
                $("#stockQty2").val('');
                $(".quantity2").attr("disabled",true);
                $(".quantity2").attr("placeholder", "0");
            }
        }
    });

}
function getProductList(cat_id) {
    if(cat_id == 2){
        $(".returnQuantity").attr("readonly",false);
    }else{
        $(".returnQuantity").attr("readonly",true);
    }
    $.ajax({
        type: "POST",
        url: baseUrl+ "InventoryController/getProductList",
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
        url: baseUrl+ "InventoryController/getProductList",
        data: 'cat_id=' + cat_id,
        success: function (data) {
            $('#productID2').chosen();
            $('#productID2 option').remove();
            $('#productID2').append($(data));
            $("#productID2").trigger("chosen:updated");
        }
    });

    if(cat_id ==1){
        $(".bundle").attr('readonly',false);
    }else{
        $(".bundle").attr('readonly',true);
    }

}