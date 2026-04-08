<script type="text/javascript">
    let table = $('#systemDatatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "{{ route('production.production.dataProcessingProduction') }}",
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
            "data": "date",
            "orderable": true
        },
        {
            "data": "product_id",
            "orderable": true
        },
        {
            "data": "productionCode",
            "orderable": true
        },


        {
            "data": "branch_id",
            "orderable": true

        },
        {
            "data": "conversion_id",
            "orderable": true

        },

        {
            "data": "product_qty",
            "orderable": true
        },

        {
            "data": "category_id",
            "orderable": true
        },
            // {
            //     "data": "status",
            //     "orderable": false,
            //     "class": 'text-nowrap',
            // },
            // {
            //     "data": "action",
            //     "class": 'text-nowrap',
            //     "searchable": false,
            //     "orderable": false
            // },
        ],

        "fnDrawCallback": function () {
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