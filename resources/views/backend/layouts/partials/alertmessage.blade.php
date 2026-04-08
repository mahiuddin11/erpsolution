<script>
    function alertMessage() {
        function error(message) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: message
            })
        }

        function confirm(message, deleteitem) {

            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteitem();
                    Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )

                }
            })
        }


        function formalConfirm(message, deleteitem) {

            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteitem();
                    Swal.fire(
                        'Update!',
                        'Status Update SuccessFully.',
                        'success'
                    )

                }
            })
        }

        alertMessage.error = error;
        alertMessage.confirm = confirm;
        alertMessage.formalConfirm = formalConfirm;
    }

    alertMessage();
</script>