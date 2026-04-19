@extends('backend.layouts.master')
@section('title')
    Hrm - {{ $title }}
@endsection

@section('styles')
    <style>
        .bootstrap-switch-large {
            width: 200px;
        }

        .bg-primary {
            background-color: #172a3e !important;
        }

        .bg-dark {
            background-color: #0c6a07 !important;
        }
    </style>
@endsection

@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Cash Requisition aprove
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('hrm.loneapprove.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.loneapprove.index') }}">Hrm</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active"><span>Loan Approve Application Details</span></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="container-fluid">

        <div class="row justify-content-center">

            <!-- LEFT SIDE: REQUEST INFO -->
            <div class="col-lg-4 col-md-12 mb-3">

                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0">Request Info</h5>
                    </div>

                    <div class="card-body text-center">

                        <div class="d-flex flex-column flex-md-row align-items-center text-center text-md-left">

                            <!-- Image -->
                            <div class="mb-3 mb-md-0 mr-md-3">
                                <img src="{{ $lone->employee->photo
                                    ? asset('storage/employee/' . $lone->employee->photo)
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($lone->employee->name) . '&background=0D8ABC&color=fff' }}"
                                    class="rounded-circle shadow" style="width: 90px; height: 90px; object-fit: cover;"
                                    alt="Employee Image">
                            </div>

                            <!-- Info -->
                            <div>

                                <h5 class="mb-1 font-weight-bold">
                                    {{ $lone->employee->name }}
                                </h5>

                                <div class="small text-muted">
                                    <div><strong>ID:</strong> {{ $lone->employee->id ?? '-' }}</div>
                                    <div><strong>Phone:</strong> {{ $lone->employee->personal_phone ?? '-' }}</div>
                                    <div><strong>Email:</strong> {{ $lone->employee->email ?? '-' }}</div>
                                    <div><strong>Address:</strong>
                                        {{ $lone->employee->present_address ?? $lone->employee->permanent_address }}</div>

                                </div>

                            </div>

                        </div>


                        <div class="border rounded p-3 mb-3 mt-2">
                            <h6 class="text-muted">Requested Amount</h6>
                            <h4 class="text-dark font-weight-bold">
                                {{ number_format($lone->amount, 2) }}
                            </h4>
                        </div>

                        <div class="mb-2">
                            <span
                                class="badge p-2 
        {{ $lone->status == 'approved' ? 'badge-success' : 'badge-warning' }}">

                                Status: {{ ucfirst($lone->status) }}
                            </span>
                        </div>

                        <hr>

                        <p class="text-left">
                            <strong>Reason:</strong><br>
                            {{ $lone->reason }}
                        </p>

                    </div>

                </div>

            </div>

            <!-- RIGHT SIDE: APPROVAL FORM -->
            <div class="col-lg-8 col-md-12">

                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-dark text-white text-center">
                        <h5 class="mb-0">Approval Panel</h5>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('hrm.cash-req.approve', $lone->id) }}" method="GET">

                            <div class="row">

                                <!-- Approve Amount -->
                                <div class="col-md-6 mb-3">
                                    <label>Approve Amount</label>
                                    <input type="number" name="amount" max="{{ $lone->amount }}"
                                        class="form-control form-control-lg" value="{{ $lone->approval_amount }}">
                                </div>

                                <!-- Check Number -->
                                <div class="col-md-6 mb-3">
                                    <label>Check Number</label>
                                    <input type="text" name="check_number" class="form-control form-control-lg"
                                        value="{{ $lone->check_number }}">
                                </div>

                                <!-- From Account -->
                                <div class="col-md-6 mb-3">
                                    <label>From Account</label>
                                    <select name="account_id" class="form-control select2">
                                        <option value="">Select Account</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">
                                                {{ $account->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Receive Account -->
                                <div class="col-md-6 mb-3">
                                    <label class="d-flex justify-content-between align-items-center">
                                        <span>Receive Account</span>
                                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal"
                                            data-target="#accountModal">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </label>
                                    <select name="recive_account_id" class="form-control select2">
                                        <option value="">Select Account</option>
                                        @foreach ($recived_accounts as $account)
                                            <option value="{{ $account->id }}">
                                                {{ $account->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <!-- ACTION -->
                            <div class="text-center mt-4">

                                <button type="submit" class="btn btn-success btn-lg px-5 shadow-sm">
                                    Approve Request
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="accountModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Create Account Leager</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Account Name</label>
                        <input type="text" id="account_name" class="form-control">
                    </div>
                    <button class="btn btn-success btn-block" onclick="saveAccount()">Save Account</button>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let empId = "{{ $lone->employee->id }}";
        let empName = "{{ $lone->employee->name }}";
        let branch_id = "{{ $lone->employee->branch_id }}"

        document.addEventListener('DOMContentLoaded', function() {
            let name = `Advance to  ${empName} - ${empId}`;
            document.getElementById('account_name').value = name;
        });

        function saveAccount() {

            let name = document.getElementById('account_name').value;
            $.ajax({
                url: "{{ route('hrm.account.leger.create') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    account_name: name,
                    employee_id: empId,
                    branch_id: branch_id,
                    parent_id: 4 // advance group
                },
                success: function(res) {

                    // dropdown এ add
                    let newOption = new Option(res.account_name, res.id, true, true);
                    $('select[name="recive_account_id"]').append(newOption).trigger('change');
                    // modal close
                    $('#accountModal').modal('hide');

                },
                error: function() {
                    alert('Something went wrong!');
                }
            });
        }
    </script>
@endsection
