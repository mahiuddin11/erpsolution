<script type="text/javascript">
let table = $('#systemDatatable').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "{{ route('settings.sms_setting.dataProcessingSmsSetting') }}",
        "dataType": "json",
        "type": "GET",
        "data": {
            "_token": "<?= csrf_token() ?>"
        }
    },
    "columns": [{
            "data": "id",
            "orderable": true
        },
        {
            "data": "api_key",
            "orderable": true
        },
        {
            "data": "api_secret",
            "orderable": true

        },
        {
            "data": "sender_mobile",
            "orderable": true

        },
        {
            "data": "sales",
            "orderable": true

        },
        {
            "data": "purchases",
            "orderable": true

        },
        {
            "data": "payment_voucher",
            "orderable": true

        },
        {
            "data": "receive_voucher",
            "orderable": true
        },
        {
            "data": "status",
            "orderable": false,
            "class": 'text-nowrap',
        },
        {
            "data": "action",
            "class": 'text-nowrap',
            "searchable": false,
            "orderable": false
        },
    ],

    "fnDrawCallback": function() {
        $("[name='my-checkbox']").bootstrapSwitch({
            size: "small",
            onColor: "success",
            offColor: "danger"
        });
    },

});


var buttons = new $.fn.dataTable.Buttons(table, {
    buttons: [
        'copyHtml5',
        'excelHtml5',
        'csvHtml5',
        'pdfHtml5',
        'print',
    ]
}).container().appendTo($('#buttons'));
</script>