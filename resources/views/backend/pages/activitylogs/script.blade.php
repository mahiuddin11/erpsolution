<script type="text/javascript">
    let table = $('#systemDatatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "{{ route('activitylog.dataProcessingActivity') }}",
            "dataType": "json",
            "type": "GET"
        },
        "columns": [{
                "data": "id",
                "orderable": true,
                "class": "text-center"
            },
            {
                "data": "created_at",
                "orderable": true,
                "class": "text-center"
            },
            {
                "data": "user_name",
                "orderable": true
            },
            {
                "data": "action",
                "orderable": true,
                "class": "text-center"
            },
            {
                "data": "module",
                "orderable": true
            },
            {
                "data": "description",
                "orderable": false
            },
            {
                "data": "changed_fields",
                "orderable": false,
                "class": "text-left"
            },
            {
                "data": "ip_address",
                "orderable": true,
                "class": "text-center"
            },
            {
                "data": "status",
                "orderable": false,
                "class": "text-center"
            },
            {
                "data": "user_agent",
                "class": 'text-nowrap text-center',
                "searchable": false,
                "orderable": false
            }
        ],

        "order": [
            [0, 'desc']
        ], // সবচেয়ে নতুন লগ প্রথমে দেখাবে

        "fnDrawCallback": function() {
            // যদি কোনো bootstrap switch থাকে তাহলে এখানে initialize করবে
        },

    });

    // Export Buttons
    var buttons = new $.fn.dataTable.Buttons(table, {
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5',
            'print'
        ]
    }).container().appendTo($('#buttons'));
</script>
