@extends('backend.layouts.master')

@section('title')
    {{ $pgTitle ?? 'SMS Configuration' }} - Admin Panel
@endsection

<!-- Common Dashboard CSS (shared by all department dashboards) -->
<link rel="stylesheet"
    href="{{ asset('css/dashboard-style.css') }}?v={{ filemtime(public_path('css/dashboard-style.css')) }}">

{{-- @section('navbar-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $pgTitle ?? 'SMS Configuration' }}</h1>
                    <p class="text-muted mb-0" style="font-size:.85rem">Manage SMS portal, templates and bulk sending
                    </p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">SMS</a></li>
                        <li class="breadcrumb-item active"><a href="#">Configuration</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection --}}

@section('admin-content')
    <div class="dashboard-wrap sms-wrap">

        {{-- ==========================================================================
             SECTION 1 : SMS Portal Overview + Configure button
        ========================================================================== --}}
        <div class="panel sms-panel mb-3">
            <div class="panel-header sms-panel-header">
                <div>
                    <span class="fin-panel-title">SMS Configaretion Overview</span>
                    <div class="sms-provider-line" id="smsProviderLine">
                        <span class="text-muted" style="font-size:.78rem" id="smsProviderText">Checking
                            connection&hellip;</span>
                        <span class="demo-pill" id="demoModePill"><i class="bi bi-info-circle"></i> Showing
                            sample data</span>
                    </div>
                </div>
                <button type="button" class="sms-btn sms-btn-primary" id="btnConfigurePortal">
                    <i class="bi bi-gear-fill"></i> Configure Portal
                </button>
            </div>
            <div class="panel-body">
                <div class="row g-3" id="smsStatsRow">
                    <div class="col-6 col-lg-3">
                        <div class="metric-card card-blue">
                            <div class="metric-top">
                                <span class="metric-title">SMS Balance</span>
                                <i class="bi bi-wallet2 metric-icon"></i>
                            </div>
                            <div class="metric-value" id="statBalance">&mdash;</div>
                            <div class="metric-sub" id="statBalanceSub">credits remaining</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="metric-card card-green">
                            <div class="metric-top">
                                <span class="metric-title">Messages Sent</span>
                                <i class="bi bi-send-check metric-icon"></i>
                            </div>
                            <div class="metric-value" id="statSent">&mdash;</div>
                            <div class="metric-sub" id="statSentSub">this month</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="metric-card card-red">
                            <div class="metric-top">
                                <span class="metric-title">Rejected / Failed</span>
                                <i class="bi bi-x-octagon metric-icon"></i>
                            </div>
                            <div class="metric-value" id="statRejected">&mdash;</div>
                            <div class="metric-sub" id="statRejectedSub">this month</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="metric-card card-purple">
                            <div class="metric-top">
                                <span class="metric-title">Portal Status</span>
                                <i class="bi bi-plug metric-icon"></i>
                            </div>
                            <div class="metric-value" style="font-size:1.1rem">
                                <span class="status-badge status-pending" id="statConnection">Checking</span>
                            </div>
                            <div class="metric-sub" id="statConnectionSub">&nbsp;</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==========================================================================
             SECTION 2 : Templates
        ========================================================================== --}}
        <div class="panel sms-panel mb-3">
            <div class="panel-header sms-panel-header">
                <span class="fin-panel-title">Message Templates</span>
                <div class="d-flex align-items-center gap-2 sms-header-actions">
                    <select class="form-control form-control-sm" id="tplTypeFilter" style="width:auto">
                        <option value="">All Types</option>
                        <option value="absence_notice">Absence Notice</option>
                        <option value="general_notice">General Notice</option>
                        <option value="warning_notice">Warning Notice</option>
                        <option value="joining_notice">Employee Joining Notice</option>
                        <option value="customer_notice">Customer / Supplier Notice</option>
                        <option value="custom">Custom</option>
                    </select>
                    <button type="button" class="sms-btn sms-btn-primary" id="btnNewTemplate">
                        <i class="bi bi-plus-lg"></i> New Template
                    </button>
                </div>
            </div>
            <div class="panel-body">
                <div class="tpl-grid" id="tplGrid">
                    <div class="text-center text-muted py-4">Loading templates&hellip;</div>
                </div>
            </div>
        </div>

        {{-- ==========================================================================
             SECTION 3 : Send SMS
        ========================================================================== --}}
        <div class="panel sms-panel mb-3">
            <div class="panel-header sms-panel-header">
                <span class="fin-panel-title">Send SMS</span>
            </div>
            <div class="panel-body">
                <div class="row g-3">
                    {{-- ---- Compose / Recipients ---- --}}
                    <div class="col-lg-6">
                        <div class="sms-field">
                            <label>Template</label>
                            <select class="form-control" id="sendTplSelect">
                                <option value="">-- Select a template --</option>
                            </select>
                        </div>

                        <div class="sms-field">
                            <label>Message</label>
                            <textarea class="form-control" id="sendMessageBox" rows="4"
                                placeholder="Pick a template above, or write a custom message here&hellip;"></textarea>
                            <div class="sms-char-counter" id="sendCharCounter">0 characters &middot; 0 SMS part(s)
                            </div>
                        </div>

                        <div class="sms-field">
                            <label>Send To</label>
                            <div class="recip-tabs" id="recipTabs">
                                <button type="button" class="recip-tab active" data-mode="all">
                                    <i class="bi bi-people-fill"></i> All Employees
                                </button>
                                <button type="button" class="recip-tab" data-mode="department">
                                    <i class="bi bi-diagram-3"></i> By Department
                                </button>
                                <button type="button" class="recip-tab" data-mode="single">
                                    <i class="bi bi-person"></i> Single Number
                                </button>
                            </div>

                            {{-- All employees --}}
                            <div class="recip-panel" id="recipPanelAll">
                                <div class="sms-note">
                                    <i class="bi bi-info-circle"></i>
                                    This will send the message to <b id="allEmpCount">&mdash;</b> active
                                    employee(s).
                                </div>
                            </div>

                            {{-- By department --}}
                            <div class="recip-panel" id="recipPanelDepartment" style="display:none">
                                <div class="dept-check-list" id="deptCheckList">
                                    <div class="text-center text-muted py-2">Loading departments&hellip;</div>
                                </div>
                            </div>

                            {{-- Single number --}}
                            <div class="recip-panel" id="recipPanelSingle" style="display:none">
                                <div class="sms-single-search">
                                    <input type="text" class="form-control" id="singleSearchInput"
                                        placeholder="Search employee, customer or supplier by name&hellip;">
                                    <div class="sms-search-results" id="singleSearchResults"></div>
                                </div>
                                <div class="sms-selected-chip" id="singleSelectedChip" style="display:none"></div>
                                <button type="button" class="sms-link-btn" id="btnManualNumber">
                                    <i class="bi bi-keyboard"></i> Enter a phone number manually instead
                                </button>
                                <div class="sms-field" id="manualNumberField" style="display:none">
                                    <input type="text" class="form-control" id="manualNumberInput"
                                        placeholder="e.g. 01712345678">
                                </div>
                            </div>
                        </div>

                        <button type="button" class="sms-btn sms-btn-send" id="btnSendSms">
                            <i class="bi bi-send"></i> Send SMS
                        </button>
                    </div>

                    {{-- ---- Live preview ---- --}}
                    <div class="col-lg-6">
                        <div class="sms-preview-wrap">
                            <div class="sms-preview-label">Live Preview</div>
                            <div class="sms-phone">
                                <div class="sms-phone-notch"></div>
                                <div class="sms-phone-header">
                                    <i class="bi bi-chat-dots-fill"></i> <span id="previewSenderName">WTBL BD</span>
                                </div>
                                <div class="sms-phone-body">
                                    <div class="sms-bubble" id="previewBubble">Your message preview will appear here.
                                    </div>
                                </div>
                            </div>
                            <div class="sms-preview-meta" id="previewMeta">Sample data is used for variables until a
                                specific recipient is selected.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ==========================================================================
         MODAL: Configure SMS Portal
    ========================================================================== --}}
    <div class="modal fade" id="smsConfigModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-gear-fill"></i> SMS Portal Configuration</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="smsConfigForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 ecf-field">
                                <label>Provider Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="cfgProviderName" required
                                    placeholder="e.g. Alpha SMS, Bulk SMS BD">
                            </div>
                            <div class="col-md-6 ecf-field">
                                <label>Sender ID / Mask</label>
                                <input type="text" class="form-control" id="cfgSenderId"
                                    placeholder="e.g. YourCompany">
                            </div>
                            <div class="col-md-12 ecf-field">
                                <label>API Base URL <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="cfgApiUrl" required
                                    placeholder="https://api.smsprovider.com/send">
                            </div>

                            <div class="col-md-6 ecf-field">
                                <label>API Key / Token <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="cfgApiKey" required
                                    placeholder="e.g. 8f92a1c3-xxxx-xxxx-xxxx">
                                <span class="form-text">Sent exactly as-is to the provider -- must match
                                    their dashboard value.</span>
                            </div>

                            <div class="col-md-6 ecf-field">
                                <label>Username (optional)</label>
                                <input type="text" class="form-control" id="cfgUsername"
                                    placeholder="Only if your provider requires it">
                            </div>

                            {{-- >>> FIX: Password field-e eye-toggle icon jog kora hoyeche,
                                 jate click korle password ta text hisebe dekha jay. <<< --}}
                            <div class="col-md-6 ecf-field">
                                <label>Password (optional)</label>
                                <div class="input-icon-group">
                                    <input type="password" class="form-control" id="cfgPassword"
                                        placeholder="Leave blank to keep the current password">
                                    <button type="button" class="input-icon-btn" id="toggleCfgPassword" tabindex="-1"
                                        aria-label="Show password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6 ecf-field d-flex align-items-end">
                                <div class="custom-control custom-switch mb-1">
                                    <input type="checkbox" class="custom-control-input" id="cfgEnabled" checked>
                                    <label class="custom-control-label" for="cfgEnabled">Enable SMS sending</label>
                                </div>
                            </div>
                        </div>
                        <div class="sms-note" id="cfgSavedNote" style="display:none">
                            <i class="bi bi-shield-check"></i> A configuration is already saved for this portal.
                            Saving again will overwrite it.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="sms-btn sms-btn-outline" id="btnTestConnection">
                            <i class="bi bi-broadcast"></i> Test Connection
                        </button>
                        <button type="button" class="sms-btn sms-btn-outline" data-dismiss="modal">
                            <i class="bi bi-x-lg"></i> Cancel
                        </button>
                        <button type="submit" class="sms-btn sms-btn-outline" id="btnSaveConfig">
                            <i class="bi bi-check-lg"></i> Save Configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==========================================================================
         MODAL: Create / Edit Template
    ========================================================================== --}}
    <div class="modal fade" id="templateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="templateModalTitle"><i class="bi bi-chat-left-text"></i> New
                        Template</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="templateForm">
                    <input type="hidden" id="tplId">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-7 ecf-field">
                                <label>Template Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tplName" required
                                    placeholder="e.g. Monthly Absence Reminder">
                            </div>
                            <div class="col-md-5 ecf-field">
                                <label>Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="tplType" required>
                                    <option value="absence_notice">Absence Notice</option>
                                    <option value="general_notice">General Notice</option>
                                    <option value="warning_notice">Warning Notice</option>
                                    <option value="joining_notice">Employee Joining Notice</option>
                                    <option value="customer_notice">Customer / Supplier Notice</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                            <div class="col-12 ecf-field">
                                <label>Insert a variable</label>
                                <div class="var-chip-row" id="varChipRow"></div>
                            </div>
                            <div class="col-12 ecf-field">
                                <label>Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="tplMessage" rows="5" required maxlength="480"
                                    placeholder="Dear {name}, this is to inform you that&hellip;"></textarea>
                                <div class="sms-char-counter" id="tplCharCounter">0 characters &middot; 0 SMS
                                    part(s)</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="sms-btn sms-btn-outline" data-dismiss="modal">
                            <i class="bi bi-x-lg"></i> Cancel
                        </button>
                        <button type="submit" class="sms-btn sms-btn-primary">
                            <i class="bi bi-check-lg"></i> Save Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="sms-toast" id="smsToast"></div>

    <style>
        .sms-wrap {
            --sms-blue: #2563eb;
            --sms-blue-dark: #1d4ed8;
        }

        .sms-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sms-provider-line {
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .demo-pill {
            display: none;
            align-items: center;
            gap: 5px;
            background: #fef9c3;
            color: #854d0e;
            font-size: .68rem;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 999px;
            border: 1px solid #fde68a;
        }

        /* ==========================================================================
                                                                                                                                                                                                                                   >>> FIX: Button base style -- age ekhane border: 1px solid #0c0101 ar
                                                                                                                                                                                                                                   .sms-btn-primary e color: #301010 bosano chilo, jar fole nil background-e
                                                                                                                                                                                                                                   ghar-kalo/lal-kalo lekha khub kom dekha jachhilo (readability nosto).
                                                                                                                                                                                                                                   Ekhon proper white text, transparent border, hover lift, focus-visible
                                                                                                                                                                                                                                   outline (accessibility), ar disabled state shoho design kora holo --
                                                                                                                                                                                                                                   button golo user-friendly ar consistent.
                                                                                                                                                                                                                                ========================================================================== */
        .sms-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 10px;
            padding: 9px 18px;
            font-size: .85rem;
            font-weight: 600;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background-color .15s, border-color .15s, transform .1s, box-shadow .15s;
        }

        .sms-btn:active {
            transform: translateY(1px);
        }

        .sms-btn:focus-visible {
            outline: 2px solid var(--sms-blue);
            outline-offset: 2px;
        }

        .sms-btn:disabled {
            opacity: .6;
            cursor: not-allowed;
            transform: none;
        }

        .sms-btn-primary {
            background: var(--sms-blue);
            color: #fff;
            box-shadow: 0 2px 6px -1px rgba(37, 99, 235, .35);
        }

        .sms-btn-primary:hover {
            background: var(--sms-blue-dark);
            color: #fff;
            box-shadow: 0 4px 10px -2px rgba(37, 99, 235, .45);
        }

        .sms-btn-outline {
            background: #fff;
            border-color: var(--gray-200);
            color: var(--gray-700);
        }

        .sms-btn-outline:hover {
            background: var(--gray-50);
            border-color: var(--gray-500);
            color: var(--gray-700);
        }

        /* <<< END FIX */

        .sms-btn-send {
            background: var(--emerald-600);
            color: #fff;
            width: 100%;
            justify-content: center;
            padding: 11px 16px;
            margin-top: 6px;
        }

        .sms-btn-send:hover {
            background: var(--emerald-700);
            color: #fff;
        }

        .sms-btn-send:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .sms-link-btn {
            background: none;
            border: none;
            color: var(--sms-blue);
            font-size: .78rem;
            font-weight: 600;
            padding: 6px 0;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .sms-link-btn:hover {
            text-decoration: underline;
        }

        /* ---- Template grid ---- */
        .tpl-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 14px;
        }

        .tpl-card {
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: 14px 16px;
            background: #fff;
            transition: .15s;
        }

        .tpl-card:hover {
            border-color: var(--gray-500);
            box-shadow: 0 8px 18px -12px rgba(0, 0, 0, .18);
            transform: translateY(-2px);
        }

        .tpl-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .tpl-type-badge {
            font-size: .68rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .tpl-type-absence_notice {
            background: #fef3c7;
            color: #92400e;
        }

        .tpl-type-general_notice {
            background: #dbeafe;
            color: #1e40af;
        }

        .tpl-type-warning_notice {
            background: #fee2e2;
            color: #991b1b;
        }

        .tpl-type-joining_notice {
            background: #dcfce7;
            color: #166534;
        }

        .tpl-type-customer_notice {
            background: #f3e8ff;
            color: #6b21a8;
        }

        .tpl-type-custom {
            background: var(--gray-100);
            color: var(--gray-700);
        }

        .tpl-card-name {
            font-weight: 700;
            font-size: .92rem;
            margin-bottom: 6px;
        }

        .tpl-card-preview {
            font-size: .78rem;
            color: var(--gray-500);
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 54px;
        }

        .tpl-card-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid var(--gray-100);
        }

        .tpl-card-actions button {
            flex: 1;
            border: 1px solid var(--gray-200);
            background: #fff;
            border-radius: 8px;
            padding: 6px 8px;
            font-size: .76rem;
            font-weight: 600;
            color: var(--gray-700);
        }

        .tpl-card-actions button:hover {
            background: var(--gray-50);
        }

        .tpl-card-actions .tpl-delete-btn:hover {
            background: var(--red-50);
            color: var(--red-700);
            border-color: var(--red-200);
        }

        /* ---- Variable chips ---- */
        .var-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .var-chip {
            background: var(--blue-50);
            color: var(--blue-700);
            border: 1px dashed var(--blue-200);
            border-radius: 999px;
            font-size: .74rem;
            font-weight: 600;
            padding: 4px 10px;
        }

        .var-chip:hover {
            background: var(--blue-100);
        }

        /* ---- Char counter ---- */
        .sms-char-counter {
            font-size: .72rem;
            color: var(--gray-500);
            margin-top: 4px;
            text-align: right;
        }

        .sms-field {
            margin-bottom: 14px;
        }

        .sms-field label {
            font-weight: 600;
            font-size: .82rem;
            color: var(--gray-700);
            margin-bottom: 5px;
            display: block;
        }

        /* ---- Recipient tabs ---- */
        .recip-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }

        .recip-tab {
            flex: 1;
            min-width: 130px;
            border: 1px solid var(--gray-200);
            background: #fff;
            border-radius: 10px;
            padding: 8px 10px;
            font-size: .8rem;
            font-weight: 600;
            color: var(--gray-600);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: .15s;
        }

        .recip-tab:hover {
            border-color: var(--gray-500);
        }

        .recip-tab.active {
            background: var(--blue-50);
            border-color: var(--blue-200);
            color: var(--blue-700);
        }

        .recip-panel {
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            border: 1px solid var(--gray-100);
            border-radius: 10px;
            padding: 12px;
            background: var(--gray-50);
            overflow: hidden;
        }

        .sms-note {
            font-size: .8rem;
            color: var(--gray-700);
            background: var(--blue-50);
            border: 1px solid var(--blue-100);
            border-radius: 8px;
            padding: 9px 12px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .sms-note i {
            margin-top: 2px;
            color: var(--blue-600);
        }

        /* ---- Department checklist ---- */
        .dept-check-list {
            width: 100%;
            height: 220px;
            max-height: 220px;

            overflow-y: auto !important;
            overflow-x: hidden !important;

            display: flex;
            flex-direction: column;
            gap: 6px;

            padding: 2px 8px 8px 2px;
            margin: 0;

            box-sizing: border-box;

            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
        }

        .dept-check-row {
            width: 100%;
            min-height: 42px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-sizing: border-box;
            flex: 0 0 auto;

            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            padding: 8px 10px;
            font-size: .82rem;
            transition: .15s;
        }


        .dept-check-row.is-checked {
            background: var(--blue-50);
            border-color: var(--blue-200);
        }

        #recipPanelDepartment {
            width: 100%;
            min-width: 0;
            overflow: hidden;
        }

        #deptCheckList {
            width: 100%;
            height: 220px;
            max-height: 220px;
            overflow-y: auto !important;
            overflow-x: hidden !important;

            display: flex;
            flex-direction: column;
            gap: 6px;

            padding: 4px 8px 8px 4px;
            margin: 0;

            box-sizing: border-box;
            position: relative;

            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
        }

        #deptCheckList .dept-check-row {
            position: relative;

            width: 100%;
            min-width: 0;
            min-height: 42px;

            flex: 0 0 auto;

            display: flex;
            align-items: center;
            justify-content: flex-start;

            padding: 8px 10px;

            box-sizing: border-box;

            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 8px;

            font-size: .82rem;
        }

        #deptCheckList .dept-check-row input[type="checkbox"] {
            position: static !important;

            width: 16px;
            height: 16px;

            margin: 0 10px 0 0 !important;

            flex: 0 0 auto;

            transform: none !important;
        }

        #deptCheckList .dept-check-row label {
            position: static !important;

            flex: 1;
            min-width: 0;

            margin: 0;

            cursor: pointer;

            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        #deptCheckList .dept-check-row.is-checked {
            background: var(--blue-50);
            border-color: var(--blue-200);
        }

        /* ---- Single number search ---- */
        .sms-single-search {
            position: relative;
        }

        .sms-search-results {
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 4px);
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 10px;
            box-shadow: 0 10px 24px -8px rgba(0, 0, 0, .18);
            max-height: 220px;
            overflow-y: auto;
            z-index: 6;
            display: none;
        }

        .sms-search-results.show {
            display: block;
        }

        .sms-search-result-item {
            padding: 9px 12px;
            font-size: .82rem;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .sms-search-result-item:last-child {
            border-bottom: none;
        }

        .sms-search-result-item:hover {
            background: var(--gray-50);
        }

        .sms-search-result-item .num {
            color: var(--gray-500);
            font-size: .74rem;
        }

        .sms-selected-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--emerald-50);
            border: 1px solid var(--emerald-200);
            color: var(--emerald-900);
            border-radius: 999px;
            padding: 6px 12px;
            font-size: .8rem;
            font-weight: 600;
            margin: 10px 0;
        }

        .sms-selected-chip .remove-chip {
            cursor: pointer;
            color: var(--red-600);
            font-weight: 800;
        }

        /* ---- Live preview phone mockup ---- */
        .sms-preview-wrap {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .sms-preview-label {
            font-weight: 700;
            font-size: .82rem;
            color: var(--gray-700);
            margin-bottom: 8px;
        }

        .sms-phone {
            background: #111827;
            border-radius: 26px;
            padding: 14px 12px 18px;
            max-width: 300px;
            margin: 0 auto;
            box-shadow: 0 16px 32px -14px rgba(0, 0, 0, .35);
        }


        .sms-phone-notch {
            width: 60px;
            height: 5px;
            background: #374151;
            border-radius: 999px;
            margin: 0 auto 10px;
        }

        .sms-phone-header {
            color: #f9fafb;
            font-size: .8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 0 4px 10px;
            border-bottom: 1px solid #374151;
            margin-bottom: 10px;
        }

        .sms-phone-body {
            min-height: 160px;
        }

        .sms-bubble {
            background: #f9fafb;
            color: #111827;
            border-radius: 4px 14px 14px 14px;
            padding: 10px 12px;
            font-size: .8rem;
            line-height: 1.5;
            max-width: 90%;
            word-break: break-word;
            white-space: pre-wrap;
        }

        .sms-preview-meta {
            font-size: .74rem;
            color: var(--gray-500);
            text-align: center;
            margin-top: 10px;
        }

        /* ---- Toast ---- */
        .sms-toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--gray-900);
            color: #fff;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: .85rem;
            font-weight: 600;
            box-shadow: 0 12px 28px -10px rgba(0, 0, 0, .4);
            z-index: 2000;
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
            transition: .2s;
        }

        .sms-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .sms-toast.error {
            background: var(--red-700);
        }

        .sms-toast.success {
            background: var(--emerald-700);
        }


        .input-icon-group {
            position: relative;
        }

        .input-icon-group .form-control {
            padding-right: 40px;
        }

        .input-icon-btn {
            position: absolute;
            top: 50%;
            right: 6px;
            transform: translateY(-50%);
            background: none;
            border: none;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-500);
            border-radius: 6px;
            cursor: pointer;
        }

        .input-icon-btn:hover {
            background: var(--gray-100);
            color: var(--gray-700);
        }

        .input-icon-btn:focus-visible {
            outline: 2px solid var(--sms-blue);
            outline-offset: 1px;
        }

        /* <<< END FIX */

        @media (max-width: 576px) {
            .recip-tab {
                min-width: 100%;
            }

            .sms-header-actions {
                width: 100%;
            }

            .sms-header-actions select {
                flex: 1;
            }
        }

        #smsConfigModal .modal-footer,
        #templateModal .modal-footer {
            flex-wrap: wrap;
            gap: 8px;
        }

        #smsConfigModal .modal-footer .sms-btn,
        #templateModal .modal-footer .sms-btn {
            flex: 1 1 auto;
            min-width: 140px;
        }

        @media (max-width: 576px) {

            #smsConfigModal .modal-footer,
            #templateModal .modal-footer {
                flex-direction: column-reverse;
            }

            #smsConfigModal .modal-footer .sms-btn,
            #templateModal .modal-footer .sms-btn {
                width: 100%;
            }
        }

        /* <<< END FIX */
    </style>

    <script>
        const SMS_API_BASE = '/api/sms-configuration';


        const TEMPLATE_TYPES = {
            absence_notice: {
                label: 'Absence Notice',
                icon: 'bi-calendar-x'
            },
            general_notice: {
                label: 'General Notice',
                icon: 'bi-megaphone'
            },
            warning_notice: {
                label: 'Warning Notice',
                icon: 'bi-exclamation-triangle'
            },
            joining_notice: {
                label: 'Employee Joining Notice',
                icon: 'bi-person-check'
            },
            customer_notice: {
                label: 'Customer / Supplier Notice',
                icon: 'bi-people'
            },
            custom: {
                label: 'Custom',
                icon: 'bi-chat-left-text'
            }
        };


        const SMS_VARIABLES = [{
                key: '{name}',
                label: 'User Name'
            },
            {
                key: '{contact_number}',
                label: 'Contact Number'
            },
            {
                key: '{address}',
                label: 'Address'
            },
            {
                key: '{department}',
                label: 'Department'
            },
            {
                key: '{designation}',
                label: 'Designation'
            },
            {
                key: '{company_name}',
                label: 'Company Name'
            },
            {
                key: '{date}',
                label: 'Date'
            }
        ];

        const SAMPLE_DATA = {
            name: 'MohiUddin',
            contact_number: '01712345678',
            address: 'Dhaka, Bangladesh',
            department: 'HR Department',
            designation: 'Executive',
            company_name: 'Your Company',
            date: new Date().toLocaleDateString()
        };


        const DEMO_STATS = {
            balance: 2450,
            sent_count: 1180,
            rejected_count: 26,
            connected: false,
            provider: null,
            sender_id: null
        };

        const DEMO_TEMPLATES = [{
                id: 'demo-1',
                name: 'Employee Absence Reminder',
                type: 'absence_notice',
                message: 'Dear {name}, you were marked absent on {date}. Please contact HR at {company_name} if this is incorrect.'
            },
            {
                id: 'demo-2',
                name: 'Office Holiday Announcement',
                type: 'general_notice',
                message: 'Dear {name}, please note {company_name} will remain closed on {date} for a public holiday.'
            },
            {
                id: 'demo-3',
                name: 'Attendance Warning',
                type: 'warning_notice',
                message: 'Dear {name}, this is a formal warning regarding repeated late attendance. Please report to HR immediately.'
            },
            {
                id: 'demo-4',
                name: 'Welcome Aboard',
                type: 'joining_notice',
                message: 'Dear {name}, welcome to {company_name}! Your first day is {date}. We are excited to have you in the {department} team.'
            },
            {
                id: 'demo-5',
                name: 'Payment Reminder',
                type: 'customer_notice',
                message: 'Dear {name}, your invoice payment is due. Please contact us at your earliest convenience. - {company_name}'
            }
        ];

        const DEMO_DEPARTMENTS = [{
                id: 'demo-d1',
                name: 'Human Resources',
                employee_count: 12
            },
            {
                id: 'demo-d2',
                name: 'Finance',
                employee_count: 8
            },
            {
                id: 'demo-d3',
                name: 'Sales & Marketing',
                employee_count: 15
            },
            {
                id: 'demo-d4',
                name: 'IT & Support',
                employee_count: 6
            },
            {
                id: 'demo-d5',
                name: 'Operations',
                employee_count: 20
            }
        ];

        const DEMO_CONTACTS = [{
                id: 'demo-c1',
                name: 'John Doe',
                phone: '01712345678',
                type: 'Employee'
            },
            {
                id: 'demo-c2',
                name: 'Sarah Khan',
                phone: '01898765432',
                type: 'Employee'
            },
            {
                id: 'demo-c3',
                name: 'Rahim Traders',
                phone: '01911223344',
                type: 'Supplier'
            },
            {
                id: 'demo-c4',
                name: 'Karim Enterprise',
                phone: '01611998877',
                type: 'Customer'
            },
            {
                id: 'demo-c5',
                name: 'Ayesha Siddiqua',
                phone: '01755566677',
                type: 'Employee'
            }
        ];


        let localDemoConfig = null; // holds a config saved locally while the API isn't reachable yet


        function apiGet(path, demoData) {
            return fetch(`${SMS_API_BASE}${path}`)
                .then(r => {
                    if (!r.ok) throw new Error('not ok');
                    return r.json();
                })
                .then(data => ({
                    demo: false,
                    data
                }))
                .catch(() => ({
                    demo: true,
                    data: demoData
                }));
        }


        function apiWrite(path, method, payload, onDemo) {
            return fetch(`${SMS_API_BASE}${path}`, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
                    },
                    body: payload ? JSON.stringify(payload) : undefined
                })
                .then(async r => {
                    const data = await r.json().catch(() => ({}));

                    if (r.ok) {
                        return {
                            demo: false,
                            data
                        };
                    }

                    // Server response dilo, kintu error status (422 validation,
                    // 500 server error ityadi) -- eta real error, demo mode na.
                    const errMsg = data?.message ||
                        (data?.errors ? Object.values(data.errors).flat().join(' ') : null) ||
                        `Server error (${r.status})`;

                    throw {
                        isServerError: true,
                        message: errMsg
                    };
                })
                .catch(err => {
                    if (err && err.isServerError) {
                        // Real validation/server error -- caller (submit handler)
                        // eta dhore real message dekhabe.
                        throw err;
                    }
                    // Network fail (server e pouchai ni) -- eta e sudhu demo mode.
                    return {
                        demo: true,
                        data: onDemo ? onDemo() : null
                    };
                });
        }

        function showToast(message, type = '') {
            const toast = document.getElementById('smsToast');
            toast.textContent = message;
            toast.className = 'sms-toast show' + (type ? ' ' + type : '');
            setTimeout(() => toast.classList.remove('show'), 3200);
        }

        function fillTemplate(text, data) {
            if (!text) return '';
            return text.replace(/\{(\w+)\}/g, (match, key) => (data && data[key] !== undefined) ? data[key] :
                match);
        }

        function smsPartsCount(text) {
            // Bangla/unicode text uses 70 chars/segment, plain ASCII uses 160.
            const isUnicode = /[^\x00-\x7F]/.test(text || '');
            const limit = isUnicode ? 70 : 160;
            const len = (text || '').length;
            const parts = len === 0 ? 0 : Math.ceil(len / limit);
            return {
                len,
                parts,
                limit
            };
        }

        /* ============================================================
           SECTION 1 -- Portal status + configuration
        ============================================================ */
        function loadSmsStats() {
            apiGet('/stats', {
                    ...DEMO_STATS,
                    ...(localDemoConfig ? {
                        connected: true,
                        provider: localDemoConfig.provider,
                        sender_id: localDemoConfig.sender_id
                    } : {})
                })
                .then(({
                    demo,
                    data
                }) => {
                    // >>> FIX: demo pill ekhon SHUDHU ei /stats call-er
                    // nijer result onujayi dekhano hoy -- onno section
                    // (templates/departments) demo-te thakleo eta
                    // prophavito hobe na.
                    document.getElementById('demoModePill').style.display = demo ? 'inline-flex' : 'none';

                    document.getElementById('statBalance').textContent = Number(data.balance ?? 0)
                        .toLocaleString();
                    document.getElementById('statSent').textContent = Number(data.sent_count ?? 0)
                        .toLocaleString();
                    document.getElementById('statRejected').textContent = Number(data.rejected_count ??
                        0).toLocaleString();

                    const connBadge = document.getElementById('statConnection');
                    const providerText = document.getElementById('smsProviderText');
                    const senderId = document.getElementById('previewSenderName');

                    if (data.connected) {
                        connBadge.textContent = demo ? 'Demo Mode' : 'Connected';
                        connBadge.className = 'status-badge ' + (demo ? 'status-pending' :
                            'status-approved');
                        providerText.innerHTML =
                            `Provider: <b>${data.provider ?? '—'}</b>${data.sender_id ? ' &middot; Sender ID: <b>' + data.sender_id + '</b>' : ''}`;
                        senderId.innerHTML = data.sender_id;

                    } else {
                        connBadge.textContent = demo ? 'Demo Mode' : 'Not Connected';
                        connBadge.className = 'status-badge ' + (demo ? 'status-pending' :
                            'status-rejected');
                        providerText.textContent = demo ?
                            'No portal configured yet -- showing sample stats below.' :
                            'No SMS portal configured yet.';
                        senderId.innerHTML = 'demo';
                    }
                });
        }

        document.getElementById('btnConfigurePortal').addEventListener('click', () => {
            document.getElementById('cfgSavedNote').style.display = 'none';
            fetch(`${SMS_API_BASE}/config`)
                .then(r => r.ok ? r.json() : null)
                .then(cfg => {
                    cfg = cfg || localDemoConfig;



                    if (cfg && cfg.provider) {
                        document.getElementById('cfgProviderName').value = cfg.provider ?? '';
                        document.getElementById('cfgSenderId').value = cfg.sender_id ?? '';
                        document.getElementById('cfgApiUrl').value = cfg.api_url ?? '';
                        document.getElementById('cfgApiKey').value = cfg.api_key ?? '';
                        document.getElementById('cfgUsername').value = cfg.username ?? '';
                        document.getElementById('cfgEnabled').checked = !!cfg.enabled;
                        document.getElementById('cfgSavedNote').style.display = 'flex';

                    }
                })
                .catch(() => {})
                .finally(() => $('#smsConfigModal').modal('show'));
        });


        document.getElementById('toggleCfgPassword').addEventListener('click', function() {
            const input = document.getElementById('cfgPassword');
            const icon = this.querySelector('i');
            const willShow = input.type === 'password';
            input.type = willShow ? 'text' : 'password';
            icon.className = willShow ? 'bi bi-eye-slash' : 'bi bi-eye';
            this.setAttribute('aria-label', willShow ? 'Hide password' : 'Show password');
        });

        document.getElementById('smsConfigForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const payload = {
                provider: document.getElementById('cfgProviderName').value,
                sender_id: document.getElementById('cfgSenderId').value,
                api_url: document.getElementById('cfgApiUrl').value,
                api_key: document.getElementById('cfgApiKey').value,
                username: document.getElementById('cfgUsername').value,
                password: document.getElementById('cfgPassword').value,
                enabled: document.getElementById('cfgEnabled').checked
            };

            const btn = document.getElementById('btnSaveConfig');
            btn.disabled = true;
            apiWrite('/send', 'POST', payload)
                .then(({
                    demo,
                    data
                }) => {

                    if (data?.success === false) {
                        throw new Error(
                            data.message || 'No recipients found for the selected option.'
                        );
                    }

                    showToast(
                        demo ?
                        'Demo mode: SMS simulated (not actually sent). Connect the API to send for real.' :
                        (data?.message || 'SMS queued for sending.'),
                        demo ? '' : 'success'
                    );

                    loadSmsStats();
                })
                .catch(err => {
                    showToast(
                        err.message || 'Could not send SMS.',
                        'error'
                    );
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-send"></i> Send SMS';
                });
        });

        document.getElementById('btnTestConnection').addEventListener('click', () => {
            const btn = document.getElementById('btnTestConnection');
            btn.disabled = true;
            fetch(`${SMS_API_BASE}/config/test`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
                    }
                })
                .then(r => {
                    if (!r.ok) throw new Error();
                    return r.json();
                })
                .then(res => showToast(res.message || (res.success ? 'Connection successful.' :
                    'Connection failed.'), res.success ? 'success' : 'error'))
                .catch(() => showToast(
                    'SMS API endpoint not connected yet -- test will work once it\'s built.'))
                .finally(() => btn.disabled = false);
        });

        /* ============================================================
           SECTION 2 -- Templates
        ============================================================ */
        let allTemplates = [];
        let localOnlyTemplates = []; // templates created/edited while the API wasn't reachable
        const DEMO_TEMPLATES_HIDDEN = new Set(); // demo template ids removed locally

        function tplCard(t) {
            const meta = TEMPLATE_TYPES[t.type] || TEMPLATE_TYPES.custom;
            return `
  <div class="tpl-card" data-id="${t.id}">
    <div class="tpl-card-top">
      <span class="tpl-type-badge tpl-type-${t.type}"><i class="bi ${meta.icon}"></i> ${meta.label}</span>
    </div>
    <div class="tpl-card-name">${t.name}</div>
    <div class="tpl-card-preview">${t.message}</div>
    <div class="tpl-card-actions">
      <button type="button" class="tpl-edit-btn" data-id="${t.id}"><i class="bi bi-pencil"></i> Edit</button>
      <button type="button" class="tpl-delete-btn" data-id="${t.id}"><i class="bi bi-trash"></i> Delete</button>
    </div>
  </div>`;
        }

        function renderTemplates() {
            const filter = document.getElementById('tplTypeFilter').value;
            const box = document.getElementById('tplGrid');
            const rows = filter ? allTemplates.filter(t => t.type === filter) : allTemplates;

            box.innerHTML = rows.length ? rows.map(tplCard).join('') :
                `<div class="empty-state" style="grid-column:1/-1;height:180px"><i class="bi bi-chat-left-text"></i><p>No templates yet. Click "New Template" to create one.</p></div>`;

            box.querySelectorAll('.tpl-edit-btn').forEach(b => b.addEventListener('click', () =>
                openTemplateModal(b.dataset.id)));
            box.querySelectorAll('.tpl-delete-btn').forEach(b => b.addEventListener('click', () =>
                deleteTemplate(b.dataset.id)));

            populateSendTemplateSelect();
        }

        function loadTemplates() {
            apiGet('/templates', [
                    ...DEMO_TEMPLATES.filter(t => !DEMO_TEMPLATES_HIDDEN.has(String(t.id))),
                    ...localOnlyTemplates
                ])
                .then(({
                    data
                }) => {
                    allTemplates = data;
                    renderTemplates();
                });
        }

        document.getElementById('tplTypeFilter').addEventListener('change', renderTemplates);

        function renderVarChips() {
            document.getElementById('varChipRow').innerHTML = SMS_VARIABLES.map(v =>
                `<button type="button" class="var-chip" data-key="${v.key}">${v.label}</button>`
            ).join('');

            document.querySelectorAll('.var-chip').forEach(chip => {
                chip.addEventListener('click', () => {
                    const box = document.getElementById('tplMessage');
                    const start = box.selectionStart ?? box.value.length;
                    const end = box.selectionEnd ?? box.value.length;
                    box.value = box.value.slice(0, start) + chip.dataset.key + box.value.slice(end);
                    box.focus();
                    box.selectionStart = box.selectionEnd = start + chip.dataset.key.length;
                    updateTplCharCounter();
                });
            });
        }

        function updateTplCharCounter() {
            const {
                len,
                parts
            } = smsPartsCount(document.getElementById('tplMessage').value);
            document.getElementById('tplCharCounter').textContent =
                `${len} characters · ${parts} SMS part(s)`;
        }

        document.getElementById('tplMessage').addEventListener('input', updateTplCharCounter);

        function openTemplateModal(id) {
            const form = document.getElementById('templateForm');
            form.reset();
            document.getElementById('tplId').value = '';
            document.getElementById('templateModalTitle').innerHTML =
                '<i class="bi bi-chat-left-text"></i> New Template';

            if (id) {
                const t = allTemplates.find(x => String(x.id) === String(id));
                if (t) {
                    document.getElementById('tplId').value = t.id;
                    document.getElementById('tplName').value = t.name;
                    document.getElementById('tplType').value = t.type;
                    document.getElementById('tplMessage').value = t.message;
                    document.getElementById('templateModalTitle').innerHTML =
                        '<i class="bi bi-pencil"></i> Edit Template';
                }
            }

            updateTplCharCounter();
            $('#templateModal').modal('show');
        }

        document.getElementById('btnNewTemplate').addEventListener('click', () => openTemplateModal(null));

        document.getElementById('templateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('tplId').value;
            const payload = {
                name: document.getElementById('tplName').value,
                type: document.getElementById('tplType').value,
                message: document.getElementById('tplMessage').value
            };

            apiWrite(`/templates${id ? '/' + id : ''}`, id ? 'PUT' : 'POST', payload, () => {
                    if (id) {
                        const idx = localOnlyTemplates.findIndex(t => String(t.id) === String(id));
                        const updated = {
                            id,
                            ...payload
                        };
                        if (idx > -1) localOnlyTemplates[idx] = updated;
                        else localOnlyTemplates.push(updated);
                    } else {
                        localOnlyTemplates.push({
                            id: 'local-' + Date.now(),
                            ...payload
                        });
                    }
                })
                .then(({
                    demo
                }) => {
                    showToast(
                        demo ?
                        (id ? 'Updated locally in demo mode.' : 'Created locally in demo mode.') :
                        (id ? 'Template updated.' : 'Template created.'),
                        demo ? '' : 'success'
                    );
                    $('#templateModal').modal('hide');
                    loadTemplates();
                })
                .catch(err => {
                    showToast(err.message || 'Could not save template.', 'error');
                });
        });

        function deleteTemplate(id) {
            if (!confirm('Delete this template? This cannot be undone.')) return;
            apiWrite(`/templates/${id}`, 'DELETE', null, () => {
                    localOnlyTemplates = localOnlyTemplates.filter(t => String(t.id) !== String(id));
                    DEMO_TEMPLATES_HIDDEN.add(String(id));
                })
                .then(({
                    demo
                }) => {
                    showToast(demo ? 'Removed locally in demo mode.' : 'Template deleted.', demo ? '' :
                        'success');
                    loadTemplates();
                })
                .catch(err => {
                    showToast(err.message || 'Could not delete template.', 'error');
                });
        }

        /* ============================================================
           SECTION 3 -- Send SMS
        ============================================================ */
        const sendState = {
            mode: 'all',
            departmentIds: [],
            selectedContact: null, // {id, name, phone, type}
            manualNumber: ''
        };

        function populateSendTemplateSelect() {
            const sel = document.getElementById('sendTplSelect');
            const current = sel.value;
            sel.innerHTML = '<option value="">-- Select a template --</option>' +
                allTemplates.map(t => `<option value="${t.id}">${t.name}</option>`).join('');
            if (current) sel.value = current;
        }

        document.getElementById('sendTplSelect').addEventListener('change', function() {
            const t = allTemplates.find(x => String(x.id) === String(this.value));
            document.getElementById('sendMessageBox').value = t ? t.message : '';
            updateSendCharCounter();
            updatePreview();
        });

        document.getElementById('sendMessageBox').addEventListener('input', () => {
            updateSendCharCounter();
            updatePreview();
        });

        function updateSendCharCounter() {
            const {
                len,
                parts
            } = smsPartsCount(document.getElementById('sendMessageBox').value);
            document.getElementById('sendCharCounter').textContent =
                `${len} characters · ${parts} SMS part(s)`;
        }

        function updatePreview() {
            const raw = document.getElementById('sendMessageBox').value;
            let data = SAMPLE_DATA;
            let meta = 'Sample data is used for variables until a specific recipient is selected.';

            if (sendState.mode === 'single' && sendState.selectedContact) {
                data = {
                    ...SAMPLE_DATA,
                    name: sendState.selectedContact.name,
                    contact_number: sendState.selectedContact.phone
                };
                meta = `Previewing the message for ${sendState.selectedContact.name}.`;
            } else if (sendState.mode === 'single' && sendState.manualNumber) {
                data = {
                    ...SAMPLE_DATA,
                    contact_number: sendState.manualNumber
                };
                meta = `Previewing the message for ${sendState.manualNumber}.`;
            } else if (sendState.mode === 'department') {
                meta = 'Sample data is shown -- each recipient will see their own details.';
            } else if (sendState.mode === 'all') {
                meta = 'Sample data is shown -- each employee will see their own details.';
            }

            document.getElementById('previewBubble').textContent = fillTemplate(raw, data) ||
                'Your message preview will appear here.';
            document.getElementById('previewMeta').textContent = meta;
        }

        // ---- recipient tabs ----
        document.querySelectorAll('.recip-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.recip-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                sendState.mode = this.dataset.mode;

                document.getElementById('recipPanelAll').style.display = sendState.mode === 'all' ? '' :
                    'none';
                document.getElementById('recipPanelDepartment').style.display = sendState.mode ===
                    'department' ? '' : 'none';
                document.getElementById('recipPanelSingle').style.display = sendState.mode === 'single' ?
                    '' : 'none';

                updatePreview();
            });
        });

        function loadAllEmployeeCount() {
            apiGet('/recipients-count?type=all', {
                    count: DEMO_DEPARTMENTS.reduce((s, d) => s + d.employee_count, 0)
                })
                .then(({
                        data
                    }) => document.getElementById('allEmpCount').textContent = Number(data.count ?? 0)
                    .toLocaleString());
        }

        function loadDepartments() {
            apiGet('/departments', DEMO_DEPARTMENTS)
                .then(({
                    data
                }) => {
                    const rows = data;
                    const box = document.getElementById('deptCheckList');
                    box.innerHTML = rows.length ? rows.map(d => `
    <label class="dept-check-row">
      <span><input type="checkbox" class="form-check-input dept-check" value="${d.id}"> ${d.name}</span>
      <span class="dept-check-count">${d.employee_count ?? 0}</span>
    </label>`).join('') :
                        `<div class="text-center text-muted py-2">No departments found</div>`;

                    box.querySelectorAll('.dept-check').forEach(cb => {
                        cb.addEventListener('change', () => {
                            // checked/unchecked অনুযায়ী row highlight টগল
                            cb.closest('.dept-check-row').classList.toggle('is-checked', cb.checked);

                            sendState.departmentIds = Array.from(box.querySelectorAll(
                                    '.dept-check:checked'))
                                .map(el => el.value);
                        });
                    });
                });
        }

        // ---- single recipient search ----
        let singleSearchTimer;
        document.getElementById('singleSearchInput').addEventListener('input', function() {
            clearTimeout(singleSearchTimer);
            const q = this.value.trim();
            const resultsBox = document.getElementById('singleSearchResults');
            if (q.length < 2) {
                resultsBox.classList.remove('show');
                return;
            }
            singleSearchTimer = setTimeout(() => {
                apiGet(`/contacts?search=${encodeURIComponent(q)}`,
                        DEMO_CONTACTS.filter(c => c.name.toLowerCase().includes(q.toLowerCase())))
                    .then(({
                        data
                    }) => {
                        const rows = data;

                        resultsBox.innerHTML = rows.length ? rows.map(c => `
      <div class="sms-search-result-item" data-id="${c.id}" data-name="${c.name}" data-phone="${c.phone}" data-type="${c.type}">
        <span>${c.name} <span class="text-muted" style="font-size:.7rem">(${c.type})</span></span>
        <span class="num">${c.phone}</span>
      </div>`).join('') :
                            `<div class="sms-search-result-item text-muted">No matches found</div>`;
                        resultsBox.classList.add('show');

                        resultsBox.querySelectorAll('.sms-search-result-item[data-id]').forEach(
                            item => {
                                item.addEventListener('click', function() {
                                    sendState.selectedContact = {
                                        id: this.dataset.id,
                                        name: this.dataset.name,
                                        phone: this.dataset.phone,
                                        type: this.dataset.type
                                    };
                                    sendState.manualNumber = '';
                                    document.getElementById('manualNumberField').style
                                        .display = 'none';
                                    document.getElementById('singleSearchInput').value = '';
                                    resultsBox.classList.remove('show');
                                    renderSelectedChip();
                                    updatePreview();
                                });
                            });
                    });
            }, 350);
        });

        function renderSelectedChip() {
            const chip = document.getElementById('singleSelectedChip');




            if (sendState.selectedContact) {
                chip.style.display = 'inline-flex';
                chip.innerHTML =
                    `<i class="bi bi-person-check"></i> ${sendState.selectedContact.name} (${sendState.selectedContact.phone}) <span class="remove-chip" id="removeChip">&times;</span>`;
                document.getElementById('removeChip').addEventListener('click', () => {
                    sendState.selectedContact = null;
                    chip.style.display = 'none';
                    updatePreview();
                });
            } else {
                chip.style.display = 'none';
            }
        }

        document.getElementById('btnManualNumber').addEventListener('click', () => {
            const field = document.getElementById('manualNumberField');
            field.style.display = field.style.display === 'none' ? '' : 'none';
            sendState.selectedContact = null;
            renderSelectedChip();
        });

        document.getElementById('manualNumberInput').addEventListener('input', function() {
            sendState.manualNumber = this.value.trim();
            updatePreview();
        });

        // ---- send ----
        document.getElementById('btnSendSms').addEventListener('click', function() {
            const message = document.getElementById('sendMessageBox').value.trim();
            if (!message) {
                showToast('Please select a template or write a message first.', 'error');
                return;
            }



            const payload = {
                template_id: document.getElementById('sendTplSelect').value || null,
                message,
                recipient_type: sendState.mode
            };


            if (sendState.mode === 'department') {
                if (!sendState.departmentIds.length) {
                    showToast('Please select at least one department.', 'error');
                    return;
                }
                payload.department_ids = sendState.departmentIds;
            }

            if (sendState.mode === 'single') {

                if (sendState.selectedContact) {
                    payload.contact_id = sendState.selectedContact.id; // employid na, contact_id
                } else if (sendState.manualNumber) {
                    payload.phone = sendState.manualNumber;
                } else {
                    showToast('Please select a recipient or enter a phone number.', 'error');
                    return;
                }
            }

            if (!confirm('Send this SMS now? This action cannot be undone.')) return;

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sending...';

            apiWrite('/send', 'POST', payload)
                .then(({
                    demo,
                    data
                }) => {
                    showToast(
                        demo ?
                        'Demo mode: SMS simulated (not actually sent). Connect the API to send for real.' :
                        (data?.message || 'SMS queued for sending.'),
                        demo ? '' : 'success'
                    );
                    loadSmsStats();
                })
                .catch(err => {
                    showToast(err.message || 'Could not send SMS.', 'error');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-send"></i> Send SMS';
                });
        });


        renderVarChips();
        loadSmsStats();
        loadTemplates();
        loadAllEmployeeCount();
        loadDepartments();
        updatePreview();
    </script>
@endsection
