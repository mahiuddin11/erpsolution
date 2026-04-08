<script type="text/javascript">
    let table
        = $('#systemDatatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('sale.sale.dataProcessingSale') }}",
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
                "data": "invoice_no",
                "orderable": true
            },
            {
                "data": "po_invoice",
                "orderable": true
            },
            {
                "data": "date",
                "orderable": true
            },
            {
                "data": "branch_id",
                "orderable": true
            },
            {
                "data": "customer_id",
                "orderable": true
            },
            {
                "data": "qty",
                "orderable": true
            },



            {
                "data": "sub_total",
                "orderable": true
            },
            {
                "data": "discount",
                "orderable": true
            },
            {
                "data": "net_total",
                "orderable": true
            },
            {
                "data": "partialPayment",
                "orderable": true
            },
            {
                "data": "grand_total",
                "orderable": true
            },


            // {
            //     "data": "sale_type",
            //     "orderable": true
            // },


            {
                "data": "action",
                "class": 'text-nowrap',
                "searchable": false,
                "orderable": false
            },
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