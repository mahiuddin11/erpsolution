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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

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
                        <button class="btn btn-outline-success btn-sm" id="exportExcelBtn" disabled>
                            <i class="fa fa-file-excel"></i> Excel এ Download করুন
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
                                        data-employee-id="{{ $employee->id }}" data-employee-name="{{ $employee->am_name }}"
                                        data-employee-idcard="{{ $employee->id_card }}"
                                        data-employee-status="{{ $employee->employee_status }}"
                                        data-employee-record-status="{{ $employee->status }}">
                                        <td>
                                            <strong>{{ $employee->am_name }}</strong><br>
                                            <span class="text-muted" style="font-size:.75rem;">ID Card:
                                                {{ $employee->id_card }}</span> <br>
                                            <span class="text-muted" style="font-size:.75rem;">Employee Status:
                                                {{ $employee->employee_status ?? '' }}</span> <br>
                                            <span class="text-muted" style="font-size:.75rem;">Status:
                                                {{ $employee->status ?? '' }}</span>

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

    {{-- ================= CORRECTION MODAL ================= --}}
    <div class="modal fade" id="correctionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-wrench"></i> Correction — <span id="correctionModalName"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="correctionModalBody">
                    {{-- JS দিয়ে dynamically fill হবে --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="applyCorrectionBtn">
                        <i class="fa fa-check"></i> Apply Correction
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const checkUrlTemplate = "{{ route('hrm.zkteco-sync.check', ':id') }}";
        const confirmUrlTemplate = "{{ route('hrm.zkteco-sync.confirm', ':id') }}";
        const applyCorrectionUrlTemplate = "{{ route('hrm.zkteco-sync.apply-correction', ':id') }}";
        const csrfToken = "{{ csrf_token() }}";

        const viaLabels = {
            device_id: 'Device ID দিয়ে পাওয়া',
            emp_code: 'ID Card (emp_code) দিয়ে পাওয়া',
            first_name: 'Name দিয়ে পাওয়া (দুর্বল match)'
        };

        // প্রতিটা employee-র check result এখানে জমা হবে — Excel export এখান থেকেই বানানো হবে
        var resultsStore = {};

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

        // এই function-টাই resultsStore-এ প্রতিটা employee-র রো Excel-friendly আকারে জমা রাখে
        function storeResult($row, statusLabel, extra) {
            var employeeId = $row.data('employee-id');

            var base = {
                'Employee Name': $row.data('employee-name') || '',
                'ID Card': $row.data('employee-idcard') || '',
                'Employee Status (present/left)': $row.data('employee-status') || '',
                'Record Status (Active/Inactive)': $row.data('employee-record-status') || '',
                'Software Device ID': $row.find('.zsync-current-id').text().trim(),
                'Check Result': statusLabel
            };

            resultsStore[employeeId] = Object.assign(base, extra || {});
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

                        var statusLabel = res.all_matched ? 'Matched' : 'Mismatch';

                        if (res.all_matched) {
                            $badge.attr('class', 'zsync-badge matched').text('Matched');
                            $row.addClass('matched');
                        } else {
                            $badge.attr('class', 'zsync-badge mismatch').text('Mismatch');
                            $row.addClass('mismatch');
                        }
                        // matched হোক বা mismatch — দুই ক্ষেত্রেই Update বাটন দেখানো হচ্ছে,
                        // যাতে admin চাইলে modal খুলে manual correction করতে পারে
                        $confirmBtn.show().prop('disabled', false);

                        storeResult($row, statusLabel, {
                            'Matched Via': viaLabels[res.matched_via] || res.matched_via || '',
                            'Verified Device ID': res.device_owner.id ?? '',
                            'Device emp_code': res.device_owner.emp_code ?? '',
                            'Device Name': res.device_owner.first_name ?? '',
                            'emp_code Match': res.comparison.emp_code.match ? 'Yes' : 'No',
                            'Name Match': res.comparison.first_name.match ? 'Yes' : 'No',
                            'card_no Match': res.comparison.card_no.match ? 'Yes' : 'No',
                        });

                    } else if (res.status === 'not_found') {
                        $ownerCell.html('<span class="text-danger">—</span>');
                        $diffCell.html(
                            '<span class="text-danger">device_id, emp_code, name — কোনোটা দিয়েই device-এ পাওয়া যায়নি।</span>'
                        );
                        $badge.attr('class', 'zsync-badge not-found').text('Not Found');
                        $row.addClass('not-found');
                        // Device-এ পাওয়া না গেলেও বাটন দেখানো হচ্ছে — status-only correction
                        // (যেমন employee_status = left হলে Inactive করা) তখনও প্রযোজ্য হতে পারে
                        $confirmBtn.show().prop('disabled', false);

                        storeResult($row, 'Not Found', {
                            'Matched Via': '',
                            'Verified Device ID': '',
                            'Device emp_code': '',
                            'Device Name': '',
                            'emp_code Match': '',
                            'Name Match': '',
                            'card_no Match': '',
                        });

                    } else {
                        $ownerCell.html('<span class="text-danger">—</span>');
                        $diffCell.html('<span class="text-danger">Device/Token error — পরে আবার চেষ্টা করুন।</span>');
                        $badge.attr('class', 'zsync-badge not-found').text('Error');
                        $confirmBtn.show().prop('disabled', false);

                        storeResult($row, 'Error', {});
                    }
                })
                .fail(function() {
                    $diffCell.html('<span class="text-danger">Request fail করেছে।</span>');
                    $badge.attr('class', 'zsync-badge not-found').text('Error');
                    $confirmBtn.show().prop('disabled', false);
                    storeResult($row, 'Request Failed', {});
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

                    // Excel export-এ যাতে "Saved" status ও নতুন device_id সঠিকভাবে দেখায়
                    if (resultsStore[employeeId]) {
                        resultsStore[employeeId]['Software Device ID'] = res.device_id;
                        resultsStore[employeeId]['Check Result'] = 'Matched (Saved)';
                    }
                }
            }).fail(function(xhr) {
                var msg = xhr.responseJSON?.message || 'Update fail করেছে।';
                alert(msg);
                $confirmBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Update');
            });
        }

        // resultsStore থেকে .xlsx বানিয়ে download করানো হয়
        function exportToExcel() {
            var rows = $('#zsyncTable tbody tr').toArray();
            var data = [];

            rows.forEach(function(row) {
                var employeeId = $(row).data('employee-id');
                if (resultsStore[employeeId]) {
                    data.push(resultsStore[employeeId]);
                } else {
                    // যেসব employee এখনো check করা হয়নি, তাদের জন্যও একটা placeholder row
                    data.push({
                        'Employee Name': $(row).data('employee-name') || '',
                        'ID Card': $(row).data('employee-idcard') || '',
                        'Employee Status (present/left)': $(row).data('employee-status') || '',
                        'Record Status (Active/Inactive)': $(row).data('employee-record-status') || '',
                        'Software Device ID': $(row).find('.zsync-current-id').text().trim(),
                        'Check Result': 'চেক করা হয়নি',
                        'Matched Via': '',
                        'Verified Device ID': '',
                        'Device emp_code': '',
                        'Device Name': '',
                        'emp_code Match': '',
                        'Name Match': '',
                        'card_no Match': '',
                    });
                }
            });

            var worksheet = XLSX.utils.json_to_sheet(data);
            var workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'ZKTeco Sync Report');

            // column width একটু বড় রাখা হচ্ছে, যাতে data কাটা না দেখায়
            worksheet['!cols'] = [{
                    wch: 26
                }, // Employee Name
                {
                    wch: 12
                }, // ID Card
                {
                    wch: 18
                }, // Employee Status
                {
                    wch: 20
                }, // Record Status
                {
                    wch: 16
                }, // Software Device ID
                {
                    wch: 16
                }, // Check Result
                {
                    wch: 22
                }, // Matched Via
                {
                    wch: 16
                }, // Verified Device ID
                {
                    wch: 16
                }, // Device emp_code
                {
                    wch: 22
                }, // Device Name
                {
                    wch: 12
                }, // emp_code Match
                {
                    wch: 12
                }, // Name Match
                {
                    wch: 12
                }, // card_no Match
            ];

            var today = new Date().toISOString().slice(0, 10);
            XLSX.writeFile(workbook, 'zkteco-sync-report-' + today + '.xlsx');
        }

        $(document).on('click', '.zsync-check-btn', function() {
            var employeeId = $(this).closest('tr').data('employee-id');
            checkEmployee(employeeId);
        });

        // ---------- Individual "Update" বাটন এখন সরাসরি save না করে, Correction Modal খোলে ----------
        $(document).on('click', '.zsync-confirm-btn', function() {
            var employeeId = $(this).closest('tr').data('employee-id');
            openCorrectionModal(employeeId);
        });

        function openCorrectionModal(employeeId) {
            var $row = $('#zrow-' + employeeId);
            var result = resultsStore[employeeId];

            if (!result) {
                alert('আগে এই Employee-কে Check করুন।');
                return;
            }

            var employeeStatus = ($row.data('employee-status') || '').toString().toLowerCase();
            var isLeft = employeeStatus === 'left';

            var softwareDeviceId = $row.find('.zsync-current-id').text().trim();
            var verifiedDeviceId = result['Verified Device ID'] || '';
            var deviceIdDiffers = verifiedDeviceId && String(softwareDeviceId) !== String(verifiedDeviceId);

            var currentIdCard = $row.data('employee-idcard') || '';
            var deviceCardNo = result['Device emp_code'] || ''; // card_no device তে সাধারণত emp_code-এর কাছাকাছি রাখা হয়
            var cardNoMismatch = result['card_no Match'] === 'No';

            $('#correctionModalName').text($row.data('employee-name') || '(নাম নেই)');

            var html = '';

            // ---- ১. Device ID correction ----
            html += '<div class="form-group border rounded p-2">';
            html += '<div class="custom-control custom-checkbox">';
            html += '<input type="checkbox" class="custom-control-input" id="cc_device_id" ' +
                (deviceIdDiffers ? 'checked' : '') + (verifiedDeviceId ? '' : 'disabled') + '>';
            html += '<label class="custom-control-label" for="cc_device_id">Software Device ID সংশোধন করুন</label>';
            html += '</div>';
            html += '<div class="text-muted" style="font-size:.78rem;margin-top:.3rem;">' +
                'বর্তমান: <strong>' + escapeHtml(softwareDeviceId) + '</strong> → সংশোধিত হবে: <strong>' +
                escapeHtml(verifiedDeviceId || '—') + '</strong></div>';
            html += '</div>';

            // ---- ২. Card No correction ----
            html += '<div class="form-group border rounded p-2 mt-2">';
            html += '<div class="custom-control custom-checkbox">';
            html += '<input type="checkbox" class="custom-control-input" id="cc_card_no" ' +
                (cardNoMismatch && verifiedDeviceId ? 'checked' : '') +
                (verifiedDeviceId ? '' : ' disabled') + '>';
            html += '<label class="custom-control-label" for="cc_card_no">Device-এ card_no সংশোধন করুন</label>';
            html += '</div>';
            html += '<input type="text" class="form-control form-control-sm mt-1" id="cc_card_no_value" ' +
                'value="' + escapeHtml(currentIdCard) + '" placeholder="নতুন card_no" ' +
                (verifiedDeviceId ? '' : 'disabled') + '>';
            html += '<div class="text-muted" style="font-size:.78rem;margin-top:.2rem;">Device-এ বর্তমানে আছে: ' +
                escapeHtml(deviceCardNo || '—') +
                (verifiedDeviceId ? '' :
                    ' <span class="text-danger">(device-এ employee পাওয়া যায়নি, তাই card_no correction সম্ভব না)</span>'
                ) +
                '</div>';
            html += '</div>';

            // ---- ৩. Status correction (শুধু employee_status = left হলে দেখাবে) ----
            if (isLeft) {
                html += '<div class="form-group border rounded p-2 mt-2" style="background:#fff8f2;">';
                html += '<div class="text-warning mb-1"><i class="fa fa-exclamation-triangle"></i> ' +
                    'এই Employee-র Status "Left" — সাধারণত এদের attendance বন্ধ করে দেওয়া উচিত।</div>';

                html += '<div class="custom-control custom-checkbox">';
                html += '<input type="checkbox" class="custom-control-input" id="cc_set_inactive" checked>';
                html += '<label class="custom-control-label" for="cc_set_inactive">Software Status → Inactive করুন</label>';
                html += '</div>';

                // Delete না করে শুধু attendance বন্ধ (enable_att = false) — কম destructive option
                html += '<div class="custom-control custom-checkbox mt-2">';
                html += '<input type="checkbox" class="custom-control-input" id="cc_disable_attendance" ' +
                    (verifiedDeviceId ? 'checked' : 'disabled') + '>';
                html += '<label class="custom-control-label" for="cc_disable_attendance">' +
                    'Device-এ রেখে দিন, শুধু <strong>Attendance বন্ধ</strong> করুন (delete না করেই)</label>';
                html += '</div>';
                html += '<div class="text-muted" style="font-size:.75rem;margin-left:1.5rem;">' +
                    'Employee record device-এ থেকে যাবে, শুধু biometric punch (enable_att) বন্ধ হয়ে যাবে — পরে দরকার হলে সহজেই আবার চালু করা যাবে।</div>';

                html += '<div class="custom-control custom-checkbox mt-2">';
                html += '<input type="checkbox" class="custom-control-input" id="cc_delete_device">';
                html += '<label class="custom-control-label" for="cc_delete_device">' +
                    '<span class="text-danger">ZKBio Time Device থেকে সম্পূর্ণ DELETE করুন</span> (স্থায়ীভাবে মুছে যাবে)</label>';
                html += '</div>';
                html +=
                    '<div class="text-muted" style="font-size:.75rem;margin-top:.3rem;">Delete করলে বাকি সব device-related option (Device ID, Card No, Disable Attendance) বাতিল হয়ে যাবে।</div>';
                html += '</div>';
            }

            $('#correctionModalBody').html(html);
            $('#applyCorrectionBtn').data('employee-id', employeeId).prop('disabled', false)
                .html('<i class="fa fa-check"></i> Apply Correction');

            // Delete select করলে বাকি device-related option গুলো disable — একসাথে delete + edit পাঠানো ঠেকাতে
            $('#cc_delete_device').off('change').on('change', function() {
                var checked = $(this).is(':checked');
                $('#cc_device_id, #cc_card_no, #cc_card_no_value, #cc_disable_attendance').prop('disabled',
                    checked);
                if (checked) {
                    $('#cc_disable_attendance').prop('checked', false);
                }
            });

            $('#correctionModal').modal('show');
        }

        $('#applyCorrectionBtn').on('click', function() {
            var $btn = $(this);
            var employeeId = $btn.data('employee-id');

            var payload = {
                update_device_id: $('#cc_device_id').is(':checked'),
                update_card_no: $('#cc_card_no').is(':checked'),
                new_card_no: $('#cc_card_no_value').val(),
                set_inactive: $('#cc_set_inactive').length ? $('#cc_set_inactive').is(':checked') : false,
                disable_attendance: $('#cc_disable_attendance').length ? $('#cc_disable_attendance').is(
                    ':checked') : false,
                delete_from_device: $('#cc_delete_device').length ? $('#cc_delete_device').is(':checked') :
                    false,
            };

            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Applying...');

            $.ajax({
                url: applyCorrectionUrlTemplate.replace(':id', employeeId),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: payload
            }).done(function(res) {
                if (res.status === 'success') {
                    var $row = $('#zrow-' + employeeId);
                    $row.find('.zsync-current-id').text(res.device_id ?? '—');
                    $row.find('.zsync-badge').attr('class', 'zsync-badge saved').text('Corrected ✓');
                    $row.find('.zsync-confirm-btn').hide();
                    $row.data('employee-record-status', res.software_status);

                    if (resultsStore[employeeId]) {
                        resultsStore[employeeId]['Software Device ID'] = res.device_id ?? '';
                        resultsStore[employeeId]['Check Result'] = 'Corrected: ' + res.actions.join('; ');
                        resultsStore[employeeId]['Record Status (Active/Inactive)'] = res.software_status;
                    }

                    $('#correctionModal').modal('hide');
                } else {
                    alert(res.message || 'Correction apply করা যায়নি।');
                    $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Apply Correction');
                }
            }).fail(function(xhr) {
                var msg = xhr.responseJSON?.message || 'Request fail করেছে।';
                alert(msg);
                $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Apply Correction');
            });
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
            $('#exportExcelBtn').prop('disabled', false);
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

        $('#exportExcelBtn').on('click', function() {
            exportToExcel();
        });
    </script>
@endsection
