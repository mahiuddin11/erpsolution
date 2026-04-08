{{-- <style>
    .footer-bottom {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
    }
</style>
<footer class="main-footer no-print footer-bottom">
    @php
        $company = App\Models\Company::first();
    @endphp
    <p class="txt-color-white text-center">© 2023 by
        <b>{{ $company->company_name }}</b> | All
        rights reserved. Design and Developed By <a target="_blank" href="#">{{ $company->company_name  ?? 'wtbl'}}</a>
    </p>

</footer> --}}

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
</aside>
<!-- /.contro
