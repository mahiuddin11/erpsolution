<script type="text/javascript">
    let table = $('#systemDatatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "{{ route('payment.supplier.dataProcessingSupplierPayment') }}",
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
                "data": "branch_id",
                "orderable": true
            },
         
            // {
            //     "data": "account_id",
            //     "orderable": true
            // },
       
            {
                "data": "supplier_id",
                "orderable": true
            },

            {
                "data": "branch_id",
                "orderable": true
            },


       
            {
                "data": "debit",
                "orderable": true
            },
            {
                "data": "credit",
                "orderable": true
            },
            // {
            //     "data": "total_due",
            //     "orderable": true
            // },

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