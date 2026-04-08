<script type="text/javascript">
let table = $('#systemDatatable').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "{{ route('settings.fiscal_year.dataProcessingFiscalYear') }}",
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
            "data": "fiscal_year",
            "orderable": true

        },
        {
            "data": "branch",
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