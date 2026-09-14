<script type="text/javascript">
    let table = $('#systemDatatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "{{ route('sale.return.dataProcessingSaleReturn') }}",
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
                "data": "sale_invoice_no",
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
                "data": "customer_name",
                "orderable": true
            },
            {
                "data": "sales_person_name",
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
                "data": "condition",
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


    function approveReturn(id) {
        alertMessage.confirm('Are you sure you want to approve this return?', function() {
            let url = "{{ route('sale.return.approve', ':id') }}".replace(':id', id);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    alertMessage.success(response.message || 'Return approved successfully.');
                    $('#systemDatatable').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON && xhr.responseJSON.message ?
                        xhr.responseJSON.message :
                        'Failed to approve return.';
                    alertMessage.error(msg);
                }
            });
        });
    }

    function rejectReturn(id) {
        alertMessage.confirm('Are you sure you want to reject this return?', function() {
            let url = "{{ route('sale.return.reject', ':id') }}".replace(':id', id);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    alertMessage.success(response.message || 'Return rejected.');
                    $('#systemDatatable').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON && xhr.responseJSON.message ?
                        xhr.responseJSON.message :
                        'Failed to reject return.';
                    alertMessage.error(msg);
                }
            });
        });
    }
</script>
