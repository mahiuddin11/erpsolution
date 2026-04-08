<script type="text/javascript">
    let table = $('#systemDatatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "{{ route('inventorySetup.transfer.dataProcessingTransfer') }}",
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
                "data": "voucher_code",
                "orderable": true
            },
            {
                "data": "date",
                "orderable": true
            },
            {
                "data": "from_branch_id",
                "orderable": true

            },
            {
                "data": "to_branch_id",
                "orderable": true

            },
            {
                "data": "qty",
                "orderable": true
            },
            {
                "data": "approved_date",
                "orderable": false,

            },
            {
                "data": "net_total",
                "orderable": false,

            },
            {
                "data": "shipping",
                "orderable": false,

            },
            {
                "data": "subtotal",
                "orderable": false,

            },
            {
                "data": "status",
                "orderable": false,

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
