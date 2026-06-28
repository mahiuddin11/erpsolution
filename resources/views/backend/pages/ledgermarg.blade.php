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
                            {{-- <div id="btn_merge_wrapper" class="mt-3" style="display:none">
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
                            </div> --}}

                            {{-- Modified: 2026-06-27 — Print button add করা হয়েছে --}}
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
                                {{-- Added: 2026-06-27 — Details সহ print --}}
                                <button type="button" class="btn btn-outline-dark btn-lg ml-2 no-print"
                                    onclick="printMergePreview()">
                                    <i class="fas fa-print mr-1"></i> Print Preview
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
    <style>
        /* ===== Added: 2026-06-27 ===== */
        .detail-table-wrapper {
            overflow-x: auto;
            margin-top: 8px;
        }

        .detail-table-wrapper table {
            font-size: 11px;
        }

        .detail-loading {
            color: #888;
            font-size: 12px;
            padding: 6px 0;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                border: 1px solid #ccc !important;
                page-break-inside: avoid;
            }

            table {
                border-collapse: collapse !important;
                width: 100% !important;
            }

            th,
            td {
                border: 1px solid #aaa !important;
                padding: 4px 6px !important;
                font-size: 10px !important;
            }

            thead {
                background: #eee !important;
                -webkit-print-color-adjust: exact;
            }

            code {
                background: #f5f5f5;
                padding: 1px 3px;
                border-radius: 2px;
            }

            .badge {
                border: 1px solid #333 !important;
                padding: 1px 5px;
                border-radius: 8px;
                font-size: 10px;
                background: none !important;
                color: #000 !important;
            }

            h2,
            h5,
            h6 {
                margin: 4px 0 !important;
            }

            .alert {
                border: 1px solid #ccc !important;
                padding: 6px 10px !important;
                margin-bottom: 8px !important;
                background: none !important;
            }

            .card-body {
                padding: 0 !important;
            }

            .card-header {
                padding: 6px 10px !important;
            }
        }

        /* ===== End Added: 2026-06-27 ===== */
    </style>

    <script>
        var tableColumnMap = {
            'account_transactions': ['id', 'invoice', 'account_id', 'debit', 'credit', 'date', 'description'],
            'journal_voucher_details': ['id', 'account_id', 'debit', 'credit', 'journal_voucher_id', 'note'],
            'dabit_voucher_details': ['id', 'account_id', 'debit', 'credit', 'dabit_voucher_id'],
            'credit_vouchers': ['id', 'account_id', 'amount', 'date', 'invoice_no', 'note'],
            'credit_voucher_details': ['id', 'account_id', 'debit', 'credit', 'credit_voucher_id'],
            'purchases': ['id', 'supplier_id', 'ledger_id', 'invoice_no', 'date', 'total_amount', 'status'],
            'purchases_details': ['id', 'supplier_id', 'ledger_id', 'product_id', 'qty', 'unit_price', 'total'],
            'projects': ['id', 'ledger_id', 'name', 'status'],
            'supplier_select_prices': ['id', 'account_id', 'supplier_id', 'amount', 'product_id'],
            'purchase_orders': ['id', 'account_id', 'supplier_id', 'invoice_no', 'date', 'status'],
            'purchase_order_details': ['id', 'supplier_ledger_id', 'product_id', 'qty', 'unit_price'],
        };

        $(document).ready(function() {


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

            $('#keep_id, #remove_id').on('change', resetPreview);

            function resetPreview() {
                $('#preview_section').slideUp(200);
                $('#merge_result').hide();
                $('#preview_table_wrapper').html('');
                $('#no_affected').hide();
                $('#btn_merge_wrapper').hide();
            }


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

            function renderPreview(data) {

                $('#preview_keep').html(accountBadge(data.keep_account));
                $('#preview_remove').html(accountBadge(data.remove_account));

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

                if (data.account_preview && data.account_preview.length > 0) {
                    hasData = true;
                    html += buildPreviewTable(
                        'account_id Level — Chart of Account ID দিয়ে linked rows',
                        'info', 'fas fa-link',
                        data.account_preview,
                        data.total_account_affected,
                        'Remove account_id <code>' + data.remove_account.id +
                        '</code> → Keep account_id <code>' + data.keep_account.id + '</code>',
                        data.remove_account.id // Added: 2026-06-27
                    );
                }

                if (data.supplier_merge_possible) {
                    if (data.supplier_preview && data.supplier_preview.length > 0) {
                        hasData = true;
                        html += buildPreviewTable(
                            'Supplier ID Level — accountable_id দিয়ে linked rows',
                            'warning', 'fas fa-truck',
                            data.supplier_preview,
                            data.total_supplier_affected,
                            'Remove supplier_id <code>' + data.remove_accountable_id +
                            '</code> → Keep supplier_id <code>' + data.keep_accountable_id + '</code>',
                            data.remove_accountable_id // Added: 2026-06-27
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

                if (hasData) {
                    html += `<div class="alert alert-dark mt-2 py-2">
                    <strong>Total Affected Rows:</strong>
                    <span class="badge badge-danger badge-pill" style="font-size:14px">${data.total_affected}</span>
                </div>`;
                }

                $('#preview_table_wrapper').html(html);

                if (!hasData) {
                    $('#no_affected').show();
                } else {
                    $('#btn_merge_wrapper').show();

                    autoLoadAllDetails();
                }

                $('#preview_section').slideDown(300);
                $('html, body').animate({
                    scrollTop: $('#preview_section').offset().top - 80
                }, 400);
            }


            function buildPreviewTable(title, color, icon, rows, total, description, removeId) {
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
                                    <th width="35">#</th>
                                    <th>Table</th>
                                    <th>Column</th>
                                    <th class="text-right" width="110">Affected Rows</th>
                                </tr>
                            </thead>
                            <tbody>`;

                rows.forEach(function(row, i) {
                    var detailId = 'detail_' + row.table + '_' + removeId;
                    html += `
                <tr>
                    <td>${i + 1}</td>
                    <td><code>${row.table}</code></td>
                    <td><code>${row.column}</code></td>
                    <td class="text-right">
                        <span class="badge badge-warning badge-pill">${row.affected}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="p-0 bg-light">
                        {{-- Added: 2026-06-27 — auto-load detail section --}}
                        <div class="detail-section px-3 py-2"
                             id="${detailId}"
                             data-table="${row.table}"
                             data-column="${row.column}"
                             data-remove-id="${removeId}">
                            <div class="detail-loading">
                                <i class="fas fa-spinner fa-spin mr-1"></i> Loading details...
                            </div>
                        </div>
                    </td>
                </tr>`;
                });

                html += `
                            </tbody>
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


            function autoLoadAllDetails() {
                $('.detail-section').each(function() {
                    var $section = $(this);
                    var table = $section.data('table');
                    var column = $section.data('column');
                    var removeId = $section.data('remove-id');

                    $.ajax({
                        url: "{{ route('ledger.merge.tableDetail') }}",
                        method: 'GET',
                        data: {
                            table: table,
                            column: column,
                            remove_id: removeId
                        },
                        success: function(res) {
                            if (!res.rows || res.rows.length === 0) {
                                $section.html(
                                    '<div class="text-muted" style="font-size:12px;">কোনো data নেই।</div>'
                                );
                                return;
                            }


                            var allKeys = Object.keys(res.rows[0]);
                            var allowedCols = tableColumnMap[table] || allKeys;
                            var keys = allKeys.filter(function(k) {
                                return allowedCols.indexOf(k) > -1;
                            });

                            var tbl = `
                        <div class="detail-table-wrapper">
                            <table class="table table-bordered table-sm table-hover mb-0" style="font-size:11px;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        ${keys.map(k => '<th>' + k + '</th>').join('')}
                                    </tr>
                                </thead>
                                <tbody>`;

                            res.rows.forEach(function(r, idx) {
                                tbl += '<tr><td>' + (idx + 1) + '</td>';
                                keys.forEach(function(k) {
                                    var val = (r[k] !== null && r[k] !==
                                            undefined) ? r[k] :
                                        '<span class="text-muted">—</span>';
                                    tbl += '<td>' + val + '</td>';
                                });
                                tbl += '</tr>';
                            });

                            tbl += '</tbody></table></div>';

                            if (res.count >= 200) {
                                tbl +=
                                    '<small class="text-warning d-block mt-1"><i class="fas fa-exclamation-triangle mr-1"></i> সর্বোচ্চ ২০০ row দেখানো হচ্ছে।</small>';
                            }

                            $section.html(tbl);
                        },
                        error: function() {
                            $section.html(
                                '<div class="text-danger" style="font-size:12px;">Data load হয়নি।</div>'
                            );
                        }
                    });
                });
            }

            function accountBadge(account) {
                if (!account) return '—';
                return `<span class="badge badge-light border mr-1">${account.accountCode ?? ''}</span>
                    <strong>${account.account_name ?? ''}</strong>`;
            }


            window.printMergePreview = function() {
                var keepName = $('#preview_keep').html() || '—';
                var removeName = $('#preview_remove').html() || '—';
                var tableHtml = $('#preview_table_wrapper').html();

                var printHtml = `<!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Ledger Merge Preview</title>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
                    h2 { text-align: center; margin-bottom: 16px; }
                    .accounts-row { display: flex; gap: 16px; margin-bottom: 16px; }
                    .acc-box { flex: 1; border: 1px solid #ccc; padding: 8px 12px; border-radius: 4px; }
                    .acc-box.keep   { border-color: #28a745; background: #f0fff4; }
                    .acc-box.remove { border-color: #dc3545; background: #fff0f0; }
                    .card { border: 1px solid #ccc; margin-bottom: 14px; border-radius: 4px; page-break-inside: avoid; }
                    .card-header { background: #f5f5f5; padding: 6px 10px; font-weight: bold; font-size: 12px; border-bottom: 1px solid #ccc; }
                    table { border-collapse: collapse; width: 100%; margin-bottom: 0; }
                    th, td { border: 1px solid #aaa; padding: 3px 6px; text-align: left; font-size: 10px; }
                    thead { background: #e9e9e9; }
                    tfoot { background: #f5f5f5; }
                    .badge { border: 1px solid #333; padding: 1px 5px; border-radius: 8px; font-size: 10px; }
                    code { background: #f0f0f0; padding: 1px 3px; border-radius: 2px; font-size: 10px; }
                    .print-date { text-align: right; font-size: 10px; color: #888; margin-bottom: 8px; }
                    .alert-dark { background: #e9e9e9; border: 1px solid #aaa; padding: 6px 10px; border-radius: 4px; margin-bottom: 10px; }
                    .text-muted { color: #888; }
                    .no-print { display: none !important; }
                    small { font-size: 10px; color: #666; display: block; }
                </style>
            </head>
            <body>
                <div class="print-date">Printed: ${new Date().toLocaleString()}</div>
                <h2>Ledger Merge Preview</h2>
                <div class="accounts-row">
                    <div class="acc-box keep">
                        <strong>✔ Keep Account:</strong><br>${keepName}
                    </div>
                    <div class="acc-box remove">
                        <strong>✖ Remove Account (Inactive হবে):</strong><br>${removeName}
                    </div>
                </div>
                ${tableHtml}
            </body>
            </html>`;

                var printWin = window.open('', '_blank', 'width=1000,height=750');
                printWin.document.write(printHtml);
                printWin.document.close();
                printWin.focus();
                printWin.onload = function() {
                    printWin.print();
                };
            };


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


            function renderMergeResult(data) {
                var updated = data.updated || [];
                var accRows = updated.filter(r => r.level === 'account_id');
                var supRows = updated.filter(r => r.level === 'supplier_id');

                var html = `<div class="alert alert-success">
                <h5><i class="fas fa-check-circle mr-2"></i> Merge was successful!</h5>
                <p>${data.message}</p>`;

                if (accRows.length > 0) html += resultTable('account_id Level — Updated Rows', accRows);
                if (supRows.length > 0) html += resultTable('Supplier ID Level — Updated Rows', supRows);

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
                    <td>${i + 1}</td><td><code>${r.table}</code></td>
                    <td><code>${r.column}</code></td>
                    <td class="text-right"><span class="badge badge-success badge-pill">${r.affected}</span></td>
                </tr>`;
                });
                html += '</tbody></table></div>';
                return html;
            }


            $('#btn_cancel').on('click', function() {
                resetPreview();
                $('html, body').animate({
                    scrollTop: 0
                }, 300);
            });

        });
    </script>
@endsection
