<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
.ui-autocomplete {
    position: absolute;
    cursor: default;
    z-index: 1001 !important
}
</style>
<!-- <script type="text/javascript">
var path = "{{ route('getReceiver') }}";
$('input.typeahead').autocomplete({
    source: function(query, process) {
        return $.get(path, {
            query: query
        }, function(data) {
            return process(data);
        });
    }
});
</script> -->
<!-- Script -->
<script type="text/javascript">
// CSRF Token
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
$(document).ready(function() {



    $(".typeahead").autocomplete({
        source: function(request, response) {
            // Fetch data
            $.ajax({
                url: "{{route('getReceiver')}}",
                type: 'get',
                dataType: "JSON", // edit: fixed ;)
                data: {
                    _token: CSRF_TOKEN,
                    phone: request.term
                },
                success: function(data) {
                    if (data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.value,
                                value: item.phone,
                                id: item.id,
                                name: item.name,
                                address: item.address

                            };
                        }));
                    } else {
                        $('.receiver_id').val(''); // display the selected text
                        $('.typeahead').val(''); // display the selected text
                        $('.receiver_name').val(''); // display the selected text
                        $('.receiver_address').val('');
                    }

                }
            });
        },
        select: function(event, ui) {
            // Set selection
            $('.receiver_id').val(ui.item.id); // display the selected text
            $('.typeahead').val(ui.item.phone); // display the selected text
            $('.receiver_name').val(ui.item.name); // display the selected text
            $('.receiver_address').val(ui.item.address); // display the selected text
            //$('#employeeid').val(ui.item.value); // save selected id to input
            // return false;
        }
    });

});
</script>

<script>
$(document).ready(function() {
    var j = 6;
    $("#add_item").click(function() {
        $("#show_item  tbody").append('<tr class="new_item' + j + '>' +
            '<td>' +
            '<div class="form-group input-sm">' +
            '<input type="text" value="444444444" name="product[]" class="form-control" required>' +
            '</div>' +
            '</td>' +
            '<td>' +
            '<div class="form-group">' +
            '<div class="col-xs-12">' +
            '<select class="form-control vatrate select2" name="product_type[]" form="invoice">' +
            '<option value="" selected disabled>--Select Category--</option>' +
            <?php foreach ($itemCategory as $key => $value) : ?>

            '<option value="<?php echo $value->id; ?>"><?php echo $value->title; ?></option>' +
            <?php endforeach; ?> +

            '</select>' +
            '</div>' +
            '</div>' +
            '</td>' +

            '<td>' +
            '<div class="form-group input-sm">' +
            '<input type="text" name="item_description[]" value="" placeholder="Description" class="form-control">' +
            '</div>' +
            '</td>' +
            '<td>' +
            '<div class="form-group input-sm">' +
            '<input type="number" name="quantity[]"  value="" placeholder="0"  class="form-control quantity" required>' +
            '</div>' +
            '</td>' +
            '<td>' +
            '<div class="form-group input-sm">' +
            '<input type="number" name="weight[]"  value="" placeholder="0" class="form-control weight" required>' +
            '</div>' +
            '</td>' +
            '<td>' +
            '<div class="form-group input-sm">' +
            '<input type="number" name="length[]"  value="" placeholder="0" class="form-control length" required>' +
            '</div>' +
            '</td>' +
            '<td>' +
            '<div class="form-group input-sm">' +
            '<input type="number" name="width[]"  value="" placeholder="0" class="form-control width" required> ' +
            '</div>' +
            '</td>' +

            '<td>' +
            '<div class="form-group input-sm">' +
            '<input type="number" name="height[]"  value="" placeholder="0" class="form-control height" value="" required>' +
            '</div>' +
            '</td>' +

            '<td><a del_id="' + j +
            '" class="delete_item btn form-control btn-danger" href="javascript:;" title=""><i class="fa fa-times"></i></a></td></tr>'
        );
        j++;




    });

});


$(document).on('click', '.delete_item', function() {

    $(this).closest("tr").remove();
    // var id = $(this).attr("del_id");
    // console.log(id);
    // $('.new_item' + id).remove();


});



$(document).ready(function($) {

    <?php if ($role == 'Merchent') : ?>
    $('.merchentInfo').show();
    loadMerchent($('.merchent_id').val());

    <?php else : ?>
    $('.merchentInfo').hide();
    $('.receiverInfo').hide();
    $(".merchent_id").change(function() {
        loadMerchent($(this).val());
    });
    <?php endif; ?>

    function loadMerchent(merchent_id) {
        $.ajax({
            url: "/admin/merchents/" + merchent_id,
            method: 'GET',
            success: function(data) {
                if (data) {
                    $('.merchentInfo').show();
                    $('.username').val(data.username);
                    $('.fullname').val(data.full_name);
                    $('.shopname').val(data.shop_name);
                    $('.pickupaddress').val(data.pickup_address);
                    $('.shopaddress').val(data.shop_address);
                    $('.pickupphone').val(data.pickup_phone);
                    $('.inside_dhaka').val(data.inside_dhaka);
                    $('.outside_dhaka').val(data.outside_dhaka);
                } else {
                    $('.merchentInfo').hide();
                }

            }
        });

    }

    $(".receiver_id").change(function() {
        $.ajax({
            url: "/admin/receivers/" + $(this).val(),
            method: 'GET',
            success: function(data) {
                if (data) {
                    $('.receiverInfo').show();
                    $('.cname').val(data.name);
                    $('.cphone').val(data.phone);
                    $('.caddress').val(data.address);
                } else {
                    $('.receiverInfo').hide();
                }

            }
        });
    });

    $(".bookingStatus").change(function() {
        $.ajax({
            url: "/changeStatus/" + $(this).val(),
            method: 'GET',
            success: function(data) {
                if (data) {
                    $('.receiverInfo').show();
                    $('.cname').val(data.name);
                    $('.cphone').val(data.phone);
                    $('.caddress').val(data.address);
                } else {
                    $('.receiverInfo').hide();
                }

            }
        });
    });

    $(".shippingarea").change(function() {

        let areaid = $(this).val();
        let inside_dhaka = $('.inside_dhaka').val();
        let outside_dhaka = $('.outside_dhaka').val();
        let inside_dhaka_charge, outside_dhaka_charge;

        if (inside_dhaka === '' || inside_dhaka <= 0) {
            inside_dhaka_charge = 75;
        } else {
            inside_dhaka_charge = inside_dhaka;
        }
        if (outside_dhaka === '' || outside_dhaka <= 0) {
            outside_dhaka_charge = 135;
        } else {
            outside_dhaka_charge = outside_dhaka;
        }

        if (areaid == 1) {
            $('.deliverCharge').val(inside_dhaka_charge);
            var estimateDate = new Date(new Date().getTime() + (1 * 24 * 60 * 60 * 1000));
            var newDate = (estimateDate.getMonth() + 1) + '/' + estimateDate.getDate() + '/' +
                estimateDate.getFullYear();
            $('.estimateDate').val(newDate);

            $('.time_sloat').val(1).trigger('change');
            $('.time_sloat_value').val(1);


        } else {
            $('.deliverCharge').val(outside_dhaka_charge);
            var estimateDate = new Date(new Date().getTime() + (3 * 24 * 60 * 60 * 1000));
            var newDate = (estimateDate.getMonth() + 1) + '/' + estimateDate.getDate() + '/' +
                estimateDate.getFullYear();
            $('.estimateDate').val(newDate);
            $('.time_sloat').val(2).trigger('change');
            $('.time_sloat_value').val(2);
        }





    });



});
</script>