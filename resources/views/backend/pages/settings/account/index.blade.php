@extends('backend.layouts.master')
@section('title')
    Settings - {{ $title }}
@endsection

@section('styles')
    <style>
              .treeview {
            position: relative;
            font-family: Arial, sans-serif;
        }

        .treeview ul {
            list-style: none;
            padding-left: 20px;
        }

        .treeview li {
            margin: 5px 0;
            padding-left: 1em;
            position: relative;
        }

        .treeview li::before {
            content: "";
            position: absolute;
            left: -1em;
            top: 0;
            bottom: 0;
            border-left: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            width: 1em;
            height: 1em;
        }

        .treeview li:last-child::before {
            border-left: none;
        }

        .treeview span {
            display: inline-block;
            padding: 0.2em 0.5em;
            background: #fcffe9;
            border: 1px solid #ddd;
            border-radius: 0.25em;
        }

        .treeview .folder {
            font-weight: bold;
            cursor: pointer;
        }
    </style>
@endsection
@section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Settings </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('settings.account.index'))
                            <li class="breadcrumb-item"><a href="{{ route('settings.account.index') }}">Account</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Chart of Accounts</span></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Chart of Accounts</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('settings.account.create'))
                            <a class="btn btn-default" href="{{ route('settings.account.create') }}"><i
                                    class="fas fa-plus"></i>
                                Add New</a>
                        @endif
                        <span id="buttons"></span>
                        <a class="btn btn-tool btn-default" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </a>
                        <a class="btn btn-tool btn-default" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="treeview w-20 border">
                        <ul class="mb-1 pl-3 pb-2">
                            @foreach ($rootAccount as $account)
                                <li><i class="fas fa-folder-open folder-icone rotate"></i>
                                    <a class="text-dark" href="{{ route('settings.account.edit', $account->id) }}">
                                        <span>{{ $account->head_code ? $account->head_code . ' -' : '' }}
                                            {{ $account->account_name }}</span>
                                    </a>
                                    @if ($account->subAccount->isNotEmpty())
                                        <x-account-sub :subacounts="$account->subAccount" />
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
                <!-- /.card-body -->
                <div class="card-footer">

                </div>
            </div>
        </div>
        <!-- /.col-->
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.treeview').mdbTreeview();
        });
    </script>
@endsection
