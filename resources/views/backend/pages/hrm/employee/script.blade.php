<script type="text/javascript">
    let currentStatus = 'all'; // default status

    let table = $('#systemDatatable').DataTable({
        processing: true,
        serverSide: true,
        "lengthMenu": [10, 25, 50, 100,200,500,1000],
        ajax: {
            url: "{{ route('hrm.employee.dataProcessingEmployee') }}",
            dataType: "json",
            type: "GET",
            data: function(d) {
                d._token = "{{ csrf_token() }}";
                d.status = currentStatus; // pass the filter status to backend
            }
        },
        columns: [
            { data: 'id', visible: false },
            { data: "sl", orderable: true },
            { data: "name", orderable: true },
            { data: "dob", orderable: true },
            { data: "gender", orderable: true },
            { data: "personal_phone", orderable: true },
            { data: "office_phone", orderable: true },
            { data: "nid", orderable: true },
            { data: "email", orderable: true },
            { data: "department", orderable: true },
            { data: "present_address", orderable: true },
            { data: "salary", orderable: true },
            { data: "over_time_is", orderable: true },
            { data: "join_date", orderable: true },
            {
                data: "action",
                class: 'text-nowrap',
                searchable: false,
                orderable: false
            },
        ],
        fnDrawCallback: function () {
            $("[name='my-checkbox']").bootstrapSwitch({
                size: "small",
                onColor: "success",
                offColor: "danger"
            });
        },
    });

    // DataTable Export Buttons
    var buttons = new $.fn.dataTable.Buttons(table, {
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5',
            'print',
        ]
    }).container().appendTo($('#buttons'));

    // Toggle Button Click Handler
    $('.filter-btn').on('click', function () {
        // UI toggle
        $('.filter-btn').removeClass('active btn-info').addClass('btn-secondary');
        $(this).addClass('active btn-info').removeClass('btn-secondary');

        // Change status & reload
        currentStatus = $(this).data('status');
        table.ajax.reload();
    });
</script>
