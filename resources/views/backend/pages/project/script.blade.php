<script type="text/javascript">
    let table = $('#systemDatatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "{{ route('project.project.dataProcessingProject') }}",
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
            "data": "name",
            "orderable": true
        },
        {
            "data": "projectCode",
            "orderable": true
        },
        {
            "data": "manager_id",
            "orderable": true

        },
        {
            "data": "budget",
            "orderable": true
        },
        // {
        //     "data": "received_amount",
        //     "orderable": false,

        // },
        {
            "data": "address",
            "orderable": false,

        },
        {
            "data": "start_date",
            "orderable": false,

        },
        {
            "data": "end_date",
            "orderable": false,

        },
        {
            "data": "status",
            "orderable": false,
            "class": 'text-nowrap',
        },
        {
            "data": "condition",
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