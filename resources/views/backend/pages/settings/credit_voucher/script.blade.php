<script type="text/javascript">
    let table = $('#systemDatatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "{{ route('settings.credit.voucher.dataProcessingDabitVoucher') }}",
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
                "data": "voucher_no",
                "orderable": true
            },
            {
                "data": "amount",
                "orderable": true
            },
            {
                "data": "project_id",
                "orderable": true
            },
            {
                "data": "approved_by",
                "orderable": true
            },
            {
                "data": "viewed",
                "orderable": true
            },
            {
                "data": "updated_by",
                "orderable": true
            },
            {
                "data": "date",
                "orderable": true
            },
            {
                "data": "note",
                "orderable": true

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
