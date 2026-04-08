@if (Session::has('success'))

<script>
    Toast.fire({
        icon: 'success',
        title: ' <?php echo Session::get('success'); ?>'
})
</script>
@endif

@if (Session::has('error'))
<script>
    Toast.fire({
        icon: 'error',
        title: ' <?php echo Session::get('error'); ?>'

})
</script>
@endif

@if (Session::has('info'))
<script>
    Toast.fire({
        icon: 'info',
        title: ' <?php echo Session::get('info'); ?> '
})
</script>
@endif


@if (Session::has('warning'))
<script>
    Toast.fire({
        icon: 'info',
        title: ' <?php echo Session::get('warning'); ?> '
})
</script>
@endif