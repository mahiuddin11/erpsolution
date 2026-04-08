<style>
    .bootstrap-switch-large {
        width: 100%;
    }

    .buttons-excel {
        padding: 7px 10px 6px 10px;
        margin-right: 5px;
    }
</style>

<script>

    codeListTable = $("#datatablexcel").DataTable({
        "bPaginate": false,
        "bLengthChange": false,
        "bFilter": false,
        "bInfo": false,
        "bAutoWidth": false,
        "order": [[1, "asc"]] 
    });
    new $.fn.dataTable.Buttons(codeListTable, {
        buttons: [

            {
                extend: 'excel',
                text: '<i class="fa fa-files-o"></i> Excel',
                titleAttr: 'Excel',
                footer: true,
                className: 'btn btn-default btn-sm',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fa fa-files-o"></i> CSV',
                titleAttr: 'Excel',
                footer: true,
                className: 'btn btn-default btn-sm',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-files-o"></i> PDF',
                titleAttr: 'Excel',
                footer: true,
                className: 'btn btn-default btn-sm',
                exportOptions: {
                    columns: ':visible'
                }
            },

        ]
    });
    codeListTable.buttons().container().appendTo('#tableActions');


</script>