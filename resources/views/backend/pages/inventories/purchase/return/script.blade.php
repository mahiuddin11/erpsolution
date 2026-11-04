<script type="text/javascript">
    let table = $('#systemDatatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "{{ route('inventorySetup.purchase.return.dataProcessing') }}",
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
                "data": "return_no",
                "orderable": true
            },

            {
                "data": "return_date",
                "orderable": true
            },
            {
                "data": "branch_name",
                "orderable": true
            },
            {
                "data": "warehouse",
                "orderable": true
            },
            {
                "data": "supplier_name",
                "orderable": true
            },
            {
                "data": "total_return_qty",
                "orderable": false
            },
            {
                "data": "grand_total",
                "orderable": true
            },
            {
                "data": "return_type",
                "orderable": true
            },
            {
                "data": "status",
                "orderable": true
            },
            {
                "data": "action",
                "class": "text-nowrap",
                "searchable": false,
                "orderable": false
            }
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
