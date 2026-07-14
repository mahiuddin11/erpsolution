@extends('backend.layouts.master')

@section('title')
    Hrm - ZKTeco Device Sync
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Hrm </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('hrm.employee.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.employee.index') }}">Employee List</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>ZKTeco Device Sync</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin-content')
    <style>
        .zsync-row.matched {
            background: #f3fbf5;
        }

        .zsync-row.mismatch {
            background: #fff8f2;
        }

        .zsync-row.not-found {
            background: #fdf3f3;
        }

        .zsync-badge {
            font-size: .74rem;
            padding: .3rem .55rem;
            border-radius: .3rem;
            font-weight: 600;
            display: inline-block;
        }

        .zsync-badge.checking {
            background: #eef1f5;
            color: #6b7280;
        }

        .zsync-badge.matched {
            background: #e6f7ea;
            color: #1e8a3d;
        }

        .zsync-badge.mismatch {
            background: #fff1e0;
            color: #b5670a;
        }

        .zsync-badge.not-found {
            background: #fdeaea;
            color: #c0392b;
        }

        .zsync-badge.saved {
            background: #e3ecff;
            color: #2547c4;
        }

        .zsync-diff {
            font-size: .76rem;
            line-height: 1.5;
        }

        .zsync-diff .zd-ok {
            color: #1e8a3d;
        }

        .zsync-diff .zd-bad {
            color: #c0392b;
            font-weight: 600;
        }

        .zsync-owner {
            font-size: .76rem;
            line-height: 1.5;
        }

        .zsync-owner .zo-label {
            color: #6b7280;
        }

        .zsync-via {
            font-size: .68rem;
            padding: .12rem .4rem;
            border-radius: .25rem;
            display: inline-block;
            margin-bottom: 3px;
            font-weight: 600;
        }

        .zsync-via.via-device_id {
            background: #e3ecff;
            color: #2547c4;
        }

        .zsync-via.via-emp_code {
            background: #eef4ff;
            color: #3b6fd1;
        }

        .zsync-via.via-first_name {
            background: #fff1e0;
            color: #b5670a;
        }

        .zsync-summary {
            font-size: .86rem;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">ZKTeco Device Sync / Reconciliation</h3>
                    <div class="card-tools">
                        <a class="btn btn-tool btn-default" data-card-widget="collapse"><i class="fas fa-minus"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-light border zsync-summary">
                        <i class="fa fa-info-circle text-primary"></i>
                        প্রথমে <strong>device_id</strong> দিয়ে device-এ খোঁজা হয়, না পেলে
                        <strong>id_card (emp_code)</strong> দিয়ে, সেটাও না পেলে <strong>name</strong> দিয়ে
                        খোঁজা হয়। যেভাবেই পাওয়া যাক, শেষে <strong>emp_code, name, card_no</strong> তিনটাই মিলিয়ে
                        দেখা হয় — সব মিললেই সবুজ badge, তখনই <strong>Update</strong> বাটন চাপলে
                        <code>device_id</code> save হবে। কোনো data automatic save হয় না।
                    </div>

                    <div class="d-flex flex-wrap align-items-center mb-3" style="gap:.5rem;">
                        <button class="btn btn-outline-primary btn-sm" id="checkAllBtn">
                            <i class="fa fa-sync"></i> সব Employee Check করুন
                        </button>
                        <button class="btn btn-success btn-sm" id="confirmAllMatchedBtn" disabled>
                            <i class="fa fa-check-double"></i> মিলে যাওয়া সবগুলো Update করুন
                        </button>
                        <span class="text-muted" style="font-size:.8rem;" id="progressText"></span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="zsyncTable">
                            <thead>
                                <tr>
                                    <th style="width:16%">Employee</th>
                                    <th style="width:9%">Software Device ID</th>
                                    <th style="width:19%">Device-এ যার নামে আছে</th>
                                    <th style="width:26%">Comparison (Software vs Device)</th>
                                    <th style="width:10%">Status</th>
                                    <th style="width:14%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr class="zsync-row" id="zrow-{{ $employee->id }}"
                                        data-employee-id="{{ $employee->id }}">
                                        <td>
                                            <strong>{{ $employee->am_name }}</strong><br>
                                            <span class="text-muted" style="font-size:.75rem;">ID Card:
                                                {{ $employee->id_card }}</span>
                                        </td>
                                        <td class="zsync-current-id">{{ $employee->device_id ?? '—' }}</td>
                                        <td class="zsync-owner-cell text-muted">চেক করা হয়নি</td>
                                        <td class="zsync-diff-cell text-muted">চেক করা হয়নি</td>
                                        <td><span class="zsync-badge checking">Pending</span></td>
                                        <td>
                                            <button class="btn btn-outline-secondary btn-sm zsync-check-btn">
                                                <i class="fa fa-search"></i> Check
                                            </button>
                                            <button class="btn btn-info btn-sm zsync-confirm-btn" disabled
                                                style="display:none;">
                                                <i class="fa fa-save"></i> Update
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const checkUrlTemplate = "{{ route('hrm.zkteco-sync.check', ':id') }}";
        const confirmUrlTemplate = "{{ route('hrm.zkteco-sync.confirm', ':id') }}";
        const csrfToken = "{{ csrf_token() }}";

        const viaLabels = {
            device_id: 'Device ID দিয়ে পাওয়া',
            emp_code: 'ID Card (emp_code) দিয়ে পাওয়া',
            first_name: 'Name দিয়ে পাওয়া (দুর্বল match)'
        };

        function escapeHtml(str) {
            if (str === null || str === undefined) return '—';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function renderDiff(comparison) {
            function line(label, item) {
                var cls = item.match ? 'zd-ok' : 'zd-bad';
                var icon = item.match ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
                return '<div class="' + cls + '">' + icon + ' ' + label + ': "' +
                    escapeHtml(item.software) + '" vs "' + escapeHtml(item.device) + '"</div>';
            }

            return '<div class="zsync-diff">' +
                line('emp_code', comparison.emp_code) +
                line('name', comparison.first_name) +
                line('card_no', comparison.card_no) +
                '</div>';
        }

        function renderOwner(owner, matchedVia) {
            var viaBadge = '<span class="zsync-via via-' + matchedVia + '">' +
                (viaLabels[matchedVia] || matchedVia) + '</span><br>';

            return '<div class="zsync-owner">' +
                viaBadge +
                '<div><span class="zo-label">Device ID:</span> ' + escapeHtml(owner.id) + '</div>' +
                '<div><span class="zo-label">emp_code:</span> ' + escapeHtml(owner.emp_code) + '</div>' +
                '<div><span class="zo-label">name:</span> ' + escapeHtml(owner.first_name) + '</div>' +
                '</div>';
        }

        function checkEmployee(employeeId) {
            var $row = $('#zrow-' + employeeId);
            var $badge = $row.find('.zsync-badge');
            var $ownerCell = $row.find('.zsync-owner-cell');
            var $diffCell = $row.find('.zsync-diff-cell');
            var $confirmBtn = $row.find('.zsync-confirm-btn');

            $badge.attr('class', 'zsync-badge checking').text('Checking...');
            $row.attr('class', 'zsync-row');
            $confirmBtn.hide().prop('disabled', true);

            return $.get(checkUrlTemplate.replace(':id', employeeId))
                .done(function(res) {
                    if (res.status === 'found') {
                        $ownerCell.html(renderOwner(res.device_owner, res.matched_via));
                        $diffCell.html(renderDiff(res.comparison));

                        if (res.all_matched) {
                            $badge.attr('class', 'zsync-badge matched').text('Matched');
                            $row.addClass('matched');
                            $confirmBtn.show().prop('disabled', false);
                        } else {
                            $badge.attr('class', 'zsync-badge mismatch').text('Mismatch');
                            $row.addClass('mismatch');
                            $confirmBtn.hide();
                        }
                    } else if (res.status === 'not_found') {
                        $ownerCell.html('<span class="text-danger">—</span>');
                        $diffCell.html(
                            '<span class="text-danger">device_id, emp_code, name — কোনোটা দিয়েই device-এ পাওয়া যায়নি।</span>'
                        );
                        $badge.attr('class', 'zsync-badge not-found').text('Not Found');
                        $row.addClass('not-found');
                        $confirmBtn.hide();
                    } else {
                        $ownerCell.html('<span class="text-danger">—</span>');
                        $diffCell.html('<span class="text-danger">Device/Token error — পরে আবার চেষ্টা করুন।</span>');
                        $badge.attr('class', 'zsync-badge not-found').text('Error');
                        $confirmBtn.hide();
                    }
                })
                .fail(function() {
                    $diffCell.html('<span class="text-danger">Request fail করেছে।</span>');
                    $badge.attr('class', 'zsync-badge not-found').text('Error');
                });
        }

        function confirmEmployee(employeeId) {
            var $row = $('#zrow-' + employeeId);
            var $badge = $row.find('.zsync-badge');
            var $confirmBtn = $row.find('.zsync-confirm-btn');
            var $currentIdCell = $row.find('.zsync-current-id');

            $confirmBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

            return $.ajax({
                url: confirmUrlTemplate.replace(':id', employeeId),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            }).done(function(res) {
                if (res.status === 'success') {
                    $badge.attr('class', 'zsync-badge saved').text('Saved ✓');
                    $currentIdCell.text(res.device_id);
                    $confirmBtn.hide();
                }
            }).fail(function(xhr) {
                var msg = xhr.responseJSON?.message || 'Update fail করেছে।';
                alert(msg);
                $confirmBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Update');
            });
        }

        $(document).on('click', '.zsync-check-btn', function() {
            var employeeId = $(this).closest('tr').data('employee-id');
            checkEmployee(employeeId);
        });

        $(document).on('click', '.zsync-confirm-btn', function() {
            var employeeId = $(this).closest('tr').data('employee-id');
            confirmEmployee(employeeId);
        });

        $('#checkAllBtn').on('click', async function() {
            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Checking...');

            var rows = $('#zsyncTable tbody tr').toArray();
            var total = rows.length;

            for (var i = 0; i < rows.length; i++) {
                var employeeId = $(rows[i]).data('employee-id');
                await checkEmployee(employeeId);
                $('#progressText').text((i + 1) + ' / ' + total + ' checked');
            }

            $btn.prop('disabled', false).html('<i class="fa fa-sync"></i> সব Employee Check করুন');
            $('#confirmAllMatchedBtn').prop('disabled', false);
        });

        $('#confirmAllMatchedBtn').on('click', async function() {
            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

            var matchedRows = $('#zsyncTable tbody tr.matched').toArray();

            for (var i = 0; i < matchedRows.length; i++) {
                var employeeId = $(matchedRows[i]).data('employee-id');
                await confirmEmployee(employeeId);
            }

            $btn.html('<i class="fa fa-check-double"></i> সব Update হয়ে গেছে');
        });
    </script>
@endsection
