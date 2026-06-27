@extends('backend.layouts.master')

@section('title', 'Ledger Merge')

@section('admin-content')
    <div class="container-fluid py-3">
        <div class="row justify-content-center">
            <div class="col-12">

                <div class="card card-primary card-outline">

                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-code-branch mr-2"></i> Ledger Merge
                        </h3>
                        <h3 class="text-muted mr-2">Merge two Ledgers into one</h3>
                    </div>

                    <div class="card-body">

                        {{-- ===== STEP 1: Account Selection ===== --}}
                        <div id="step_select">

                            <div class="form-group row align-items-center">
                                <label class="col-sm-3 col-form-label font-weight-bold text-success">
                                    <i class="fas fa-check-circle mr-1"></i> Keep Account
                                    {{-- <small class="d-block text-muted font-weight-normal">(Keep Account)</small> --}}
                                </label>
                                <div class="col-sm-9">
                                    <select id="keep_id" name="keep_id" class="form-control" style="width:100%"></select>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-9 offset-sm-3 text-center text-muted" style="font-size:18px;">⇅</div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label class="col-sm-3 col-form-label font-weight-bold text-danger">
                                    <i class="fas fa-times-circle mr-1"></i> Remove Account
                                    {{-- <small class="d-block text-muted font-weight-normal">(Remove Account)</small> --}}
                                </label>
                                <div class="col-sm-9">
                                    <select id="remove_id" name="remove_id" class="form-control"
                                        style="width:100%"></select>
                                    <small class="text-danger mt-1 d-block">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        All transactions of this account will go to <strong>Keep Account</strong>- and it
                                        will be inactive.
                                    </small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-9 offset-sm-3">
                                    <button id="btn_preview" class="btn btn-info px-4">
                                        <i class="fas fa-eye mr-1"></i> Preview
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- ===== STEP 2: Preview Section ===== --}}
                        <div id="preview_section" style="display:none">
                            <hr>
                            <h5 class="mb-3">
                                <i class="fas fa-search mr-1 text-info"></i> Merge Preview
                            </h5>

                            {{-- Account Summary --}}
                            <div class="row mb-4">
                                <div class="col-md-5">
                                    <div class="alert alert-success mb-0 h-100">
                                        <strong><i class="fas fa-check-circle mr-1"></i> Keep Account:</strong>
                                        <div id="preview_keep" class="mt-1"></div>
                                        <small id="preview_keep_supplier" class="text-muted d-block mt-1"></small>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-center justify-content-center">
                                    <span class="text-muted" style="font-size:24px;">⟵</span>
                                </div>
                                <div class="col-md-5">
                                    <div class="alert alert-danger mb-0 h-100">
                                        <strong><i class="fas fa-times-circle mr-1"></i> Inactive হবে (Remove):</strong>
                                        <div id="preview_remove" class="mt-1"></div>
                                        <small id="preview_remove_supplier" class="text-muted d-block mt-1"></small>
                                    </div>
                                </div>
                            </div>

                            {{-- Tables wrapper --}}
                            <div id="preview_table_wrapper"></div>

                            <div id="no_affected" class="alert alert-warning" style="display:none">
                                <i class="fas fa-info-circle mr-1"></i>
                                This ledger is not used in any table — but the account will be inactive.
                            </div>

                            {{-- Merge Button --}}
                            <div id="btn_merge_wrapper" class="mt-3" style="display:none">
                                <div class="alert alert-warning py-2 mb-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <strong>Warning:</strong> Merge once done cannot be undone.
                                </div>
                                <button id="btn_merge" class="btn btn-danger btn-lg">
                                    <i class="fas fa-code-branch mr-1"></i> Confirm and merge
                                </button>
                                <button id="btn_cancel" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </button>
                            </div>
                        </div>

                        {{-- ===== STEP 3: Result ===== --}}
                        <div id="merge_result" class="mt-3" style="display:none"></div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        $(document).ready(function() {

            // ============================================================
            // Select2 AJAX — Keep
            // ============================================================
            $('#keep_id').select2({
                placeholder: 'Search by code or name...',
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('ledger.merge.search') }}",
                    dataType: 'json',
                    delay: 300,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data
                    }),
                    cache: true
                }
            });

            // ============================================================
            // Select2 AJAX — Remove
            // ============================================================
            $('#remove_id').select2({
                placeholder: 'Search by code or name...',
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('ledger.merge.search') }}",
                    dataType: 'json',
                    delay: 300,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data
                    }),
                    cache: true
                }
            });

            // selection বদলালে preview reset
            $('#keep_id, #remove_id').on('change', resetPreview);

            function resetPreview() {
                $('#preview_section').slideUp(200);
                $('#merge_result').hide();
                $('#preview_table_wrapper').html('');
                $('#no_affected').hide();
                $('#btn_merge_wrapper').hide();
            }

            // ============================================================
            // PREVIEW Button
            // ============================================================
            $('#btn_preview').on('click', function() {
                var keepId = $('#keep_id').val();
                var removeId = $('#remove_id').val();

                if (!keepId || !removeId) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Select Account',
                        text: 'Select both Keep and Remove.'
                    });
                }
                if (keepId === removeId) {
                    return Swal.fire({
                        icon: 'error',
                        title: 'একই Account!',
                        text: 'Keep and Remove Account cannot be same.'
                    });
                }

                var $btn = $(this).prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin mr-1"></i> Loading...');

                $.ajax({
                    url: "{{ route('ledger.merge.preview') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        keep_id: keepId,
                        remove_id: removeId
                    },
                    success: renderPreview,
                    error: function(xhr) {
                        var msg = xhr.responseJSON?.error ?? xhr.responseJSON?.message ??
                            'Preview error!';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(
                            '<i class="fas fa-eye mr-1"></i> Preview');
                    }
                });
            });

            // ============================================================
            // Render Preview
            // ============================================================
            function renderPreview(data) {

                // ── Account info badges ──────────────────────────────────
                $('#preview_keep').html(accountBadge(data.keep_account));
                $('#preview_remove').html(accountBadge(data.remove_account));

                // Supplier accountable_id info
                if (data.keep_accountable_id) {
                    $('#preview_keep_supplier').html(
                        '<i class="fas fa-truck mr-1"></i> Supplier ID: <code>' + data.keep_accountable_id +
                        '</code>'
                    );
                }
                if (data.remove_accountable_id) {
                    $('#preview_remove_supplier').html(
                        '<i class="fas fa-truck mr-1"></i> Supplier ID: <code>' + data.remove_accountable_id +
                        '</code>'
                    );
                }

                var html = '';
                var hasData = false;

                // ── Section 1: account_id level ──────────────────────────
                if (data.account_preview && data.account_preview.length > 0) {
                    hasData = true;
                    html += buildPreviewTable(
                        'account_id Level — Chart of Account ID দিয়ে linked rows',
                        'info',
                        'fas fa-link',
                        data.account_preview,
                        data.total_account_affected,
                        'Remove account_id <code>' + data.remove_account.id +
                        '</code> → Keep account_id <code>' + data.keep_account.id + '</code>'
                    );
                }

                // ── Section 2: supplier_id level ─────────────────────────
                if (data.supplier_merge_possible) {
                    if (data.supplier_preview && data.supplier_preview.length > 0) {
                        hasData = true;
                        html += buildPreviewTable(
                            'Supplier ID Level — accountable_id দিয়ে linked rows',
                            'warning',
                            'fas fa-truck',
                            data.supplier_preview,
                            data.total_supplier_affected,
                            'Remove supplier_id <code>' + data.remove_accountable_id +
                            '</code> → Keep supplier_id <code>' + data.keep_accountable_id + '</code>'
                        );
                    } else {
                        html += `<div class="alert alert-secondary">
                    <i class="fas fa-truck mr-1"></i>
                    <strong>Supplier ID Level:</strong>
                    Supplier ID <code>${data.remove_accountable_id}</code> Not used in any table।
                </div>`;
                    }
                } else if (data.remove_accountable_id) {
                    html += `<div class="alert alert-secondary">
                <i class="fas fa-info-circle mr-1"></i>
                Remove account এর <code>accountable_id = ${data.remove_accountable_id}</code> —
                Keep account's accountable_id same, so supplier_id update is not required।
            </div>`;
                }

                // ── Grand Total ──────────────────────────────────────────
                if (hasData) {
                    html += `<div class="alert alert-dark mt-2 py-2">
                <strong> Affected Rows: </strong>
                <span class="badge badge-danger badge-pill" style="font-size:14px">${data.total_affected}</span>
            </div>`;
                }

                $('#preview_table_wrapper').html(html);

                if (!hasData) {
                    $('#no_affected').show();
                } else {
                    $('#btn_merge_wrapper').show();
                }

                $('#preview_section').slideDown(300);
                $('html, body').animate({
                    scrollTop: $('#preview_section').offset().top - 80
                }, 400);
            }

            // ── Table builder helper ─────────────────────────────────────
            function buildPreviewTable(title, color, icon, rows, total, description) {
                var html = `
            <div class="card card-${color} card-outline mb-3">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">
                        <i class="${icon} mr-1"></i> ${title}
                    </h6>
                    <small class="text-muted">${description}</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="40">#</th>
                                    <th>Table</th>
                                    <th>Column</th>
                                    <th class="text-right" width="120">Affected Rows</th>
                                </tr>
                            </thead>
                            <tbody>`;

                rows.forEach(function(row, i) {
                    html += `<tr>
                        <td>${i + 1}</td>
                        <td><code>${row.table}</code></td>
                        <td><code>${row.column}</code></td>
                        <td class="text-right">
                            <span class="badge badge-warning badge-pill">${row.affected}</span>
                        </td>
                    </tr>`;
                });

                html += `       </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-right font-weight-bold">মোট:</td>
                                    <td class="text-right">
                                        <span class="badge badge-danger badge-pill">${total}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>`;
                return html;
            }

            function accountBadge(account) {
                if (!account) return '—';
                return `<span class="badge badge-light border mr-1">${account.accountCode ?? ''}</span>
                <strong>${account.account_name ?? ''}</strong>`;
            }

            // ============================================================
            // MERGE Button
            // ============================================================
            $('#btn_merge').on('click', function() {
                var keepId = $('#keep_id').val();
                var removeId = $('#remove_id').val();

                Swal.fire({
                    icon: 'warning',
                    title: 'Make Sure',
                    html: `<p>This action is permanent and cannot be undone।</p>
                   <p class="mb-0">Remove account <strong>Inactive</strong> হয়ে যাবে।</p>`,
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-code-branch mr-1"></i> Yes, do Merge',
                    cancelButtonText: 'No, Cancel it'
                }).then(function(result) {
                    if (!result.isConfirmed) return;

                    var $btn = $('#btn_merge').prop('disabled', true)
                        .html('<i class="fas fa-spinner fa-spin mr-1"></i> Merging...');

                    $.ajax({
                        url: "{{ route('ledger.merge.execute') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            keep_id: keepId,
                            remove_id: removeId
                        },
                        success: function(data) {
                            if (data.success) {
                                renderMergeResult(data);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Merge ব্যর্থ!',
                                    text: data.message
                                });
                                $btn.prop('disabled', false)
                                    .html(
                                        '<i class="fas fa-code-branch mr-1"></i> Confirm and merge'
                                    );
                            }
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON?.message ??
                                'There was a problem merging.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error',
                                text: msg
                            });
                            $btn.prop('disabled', false)
                                .html(
                                    '<i class="fas fa-code-branch mr-1"></i> Confirm and merge'
                                );
                        }
                    });
                });
            });

            // ============================================================
            // Render Merge Success
            // ============================================================
            function renderMergeResult(data) {
                var updated = data.updated || [];

                // level অনুযায়ী ভাগ করো
                var accRows = updated.filter(r => r.level === 'account_id');
                var supRows = updated.filter(r => r.level === 'supplier_id');

                var html = `<div class="alert alert-success">
            <h5><i class="fas fa-check-circle mr-2"></i> Merge was successful!</h5>
            <p>${data.message}</p>`;

                if (accRows.length > 0) {
                    html += resultTable('account_id Level — Updated Rows', accRows);
                }
                if (supRows.length > 0) {
                    html += resultTable('Supplier ID Level — Updated Rows', supRows);
                }

                html += `<hr>
            <a href="{{ route('ledger.merge.index') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus mr-1"></i> Merge new
            </a>
        </div>`;

                $('#preview_section').slideUp(200);
                $('#merge_result').html(html).slideDown(300);
                $('html, body').animate({
                    scrollTop: $('#merge_result').offset().top - 80
                }, 400);

                $('#keep_id, #remove_id').val(null).trigger('change');
            }

            function resultTable(title, rows) {
                var html = `<p class="mt-3 mb-1 font-weight-bold">${title}:</p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr><th>#</th><th>Table</th><th>Column</th><th class="text-right">Updated</th></tr>
                    </thead><tbody>`;
                rows.forEach(function(r, i) {
                    html += `<tr>
                <td>${i + 1}</td>
                <td><code>${r.table}</code></td>
                <td><code>${r.column}</code></td>
                <td class="text-right"><span class="badge badge-success badge-pill">${r.affected}</span></td>
            </tr>`;
                });
                html += '</tbody></table></div>';
                return html;
            }

            // ============================================================
            // Cancel
            // ============================================================
            $('#btn_cancel').on('click', function() {
                resetPreview();
                $('html, body').animate({
                    scrollTop: 0
                }, 300);
            });

        });
    </script>
@endsection
