<script type="text/javascript">
let table = $('#systemDatatable').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "{{ route('project.transferproject.dataProcessing') }}",
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
            "data": "order_date",
            "orderable": true
        },
        {
            "data": "invoice_no",
            "orderable": true
        },
        // {
        //     "data": "supplier_id",
        //     "orderable": true
        // },
      
        {
            "data": "project_id",
            "orderable": true
        },
        // {
        //     "data": "total_bill",
        //     "orderable": true
        // },
        // {
        //     "data": "status",
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