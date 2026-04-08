<script type="text/javascript">
    let table = $('#systemDatatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "{{ route('hrm.leave.dataProcessingLeaveApplication') }}",
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
            "data": "employee_id",
            "orderable": true
        },
        {
            "data": "branch_id",
            "orderable": true
        },
        {
            "data": "days",
            "orderable": true
        },
        {
            "data": "apply_date",
            "orderable": true
        },
        {
            "data": "end_date",
            "orderable": true
        },
        {
            "data": "reason",
            "orderable": true
        },
        {
        "data": "payment_status",
        "orderable": true
        },
        {
            "data": "status",
            "orderable": true
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