<script type="text/javascript">
    let table = $('#systemDatatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "{{ route('inventorySetup.product.dataProcessingProduct') }}",
            "type": "GET",
            "headers": {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            "data": function(d) {
                d.category_id = $('#category_id').val(); // Pass the selected category ID to the server
            },
            "dataType": "json",
            "error": function (xhr, error, thrown) {
                console.error('Data could not be loaded:', error);
            }
        },
        "columns": [
            { "data": "id", "orderable": true },
            { "data": "name", "orderable": true },
            { "data": "productCode", "orderable": true },
            { "data": "category_id", "orderable": true },
            { "data": "brand", "orderable": true },
            { "data": "productUnit", "orderable": true },
            { "data": "status", "orderable": false, "class": 'text-nowrap' },
            { "data": "action", "searchable": false, "orderable": false, "class": 'text-nowrap' }
        ],
        "lengthMenu": [
            [10, 25, 50, 100,10000], // Page lengths
            [10, 25, 50, 100,"All"] // Displayed options
        ],
        "pageLength": 10, // Default page length
        "fnDrawCallback": function() {
            $("[name='my-checkbox']").bootstrapSwitch({
                size: "small",
                onColor: "success",
                offColor: "danger"
            });
        }
    });

    // Trigger DataTable reload on category selection change
    $('#category_id').on('change', function() {
        table.ajax.reload(); // Reload DataTable with the selected category filter
    });

    // Initialize DataTable export buttons
    var datatableButtons = new $.fn.dataTable.Buttons(table, {
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5',
            'print'
        ]
    }).container().appendTo($('#buttons'));
</script>
