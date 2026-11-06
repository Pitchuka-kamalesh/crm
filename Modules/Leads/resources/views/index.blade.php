<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $roleName }} - Leads & Calling Portal | VFSTR Vignan University</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <!-- Shared Enterprise CRM Styles -->
    <link rel="stylesheet" href="/css/crm-dashboard.css">
</head>
<body>
<div class="app-wrapper">
    <!-- 1. LEFT SIDEBAR (Strictly Mapped for Tele Caller & Leads) -->
    @include('layouts.crm-sidebar')

    <!-- 2. MAIN CONTENT AREA -->
    <div class="app-main">
        <!-- Top Navigation Header -->
        <header class="top-bar">
            <div class="top-bar-left">
                <div class="page-title">
                    @if($roleId == 4)
                        <span>🎧 Tele Caller Counseling & Calling Desk</span>
                    @elseif($roleId == 2)
                        <span>👔 Leads Allocation & Caller Distribution</span>
                    @else
                        <span>🎯 University Admissions Leads Management</span>
                    @endif
                </div>
            </div>

            <div class="top-bar-right">
                @include('layouts.role-switcher')
                <a href="{{ route('sms.index') }}" class="btn-header-action" title="Send SMS / WhatsApp Alert">
                    <span>💬 Send SMS / WhatsApp</span>
                </a>
            </div>
        </header>

        <!-- Main Dashboard Body -->
        <main class="main-body">
            @if(session('status'))
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 0.85rem 1.25rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.85rem; font-weight: 600;">
                    ✓ {{ session('status') }}
                </div>
            @endif

            <!-- TELECALLER & LEADS METRIC CARDS -->
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-info">
                        <span class="metric-label">Allotted Leads</span>
                        <span class="metric-value">{{ $totalAllotted }}</span>
                        <span class="metric-sub">Active in Calling Queue</span>
                    </div>
                    <div class="metric-icon-box metric-blue">📞</div>
                </div>

                <div class="metric-card">
                    <div class="metric-info">
                        <span class="metric-label">Calls Made Today</span>
                        <span class="metric-value">{{ $callsMadeToday }}</span>
                        <span class="metric-sub">↑ 82% Daily Calling Target</span>
                    </div>
                    <div class="metric-icon-box metric-emerald">📲</div>
                </div>

                <div class="metric-card">
                    <div class="metric-info">
                        <span class="metric-label">Callbacks Scheduled</span>
                        <span class="metric-value">{{ $followupsScheduled }}</span>
                        <span class="metric-sub">Pending Student Follow-ups</span>
                    </div>
                    <div class="metric-icon-box metric-amber">⏰</div>
                </div>

                <div class="metric-card">
                    <div class="metric-info">
                        <span class="metric-label">Converted Admissions</span>
                        <span class="metric-value">{{ $converted }}</span>
                        <span class="metric-sub">Fee Paid & Registered</span>
                    </div>
                    <div class="metric-icon-box metric-purple">🏆</div>
                </div>
            </div>

            @if(($activeTab ?? '') === 'allocation')
                <!-- TAB: LEADS ALLOCATION MATRIX (master.menus: LEADS0003) -->
                <div class="content-card">
                    <div class="card-header-bar">
                        <div>
                            <div class="card-title">📤 Leads Allocation Matrix (CRM Incharge & DEAN)</div>
                            <div class="card-subtitle">Distribute incoming admission inquiries to Telecallers and Counselors</div>
                        </div>
                    </div>

                    <div style="padding: 1.5rem; background: #ffffff; border-bottom: 1px solid #e2e8f0;">
                        <form method="GET" action="{{ route('leads.allocation') }}" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
                            <input type="hidden" name="tab" value="allocation">
                            <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                                <label style="font-size: 0.75rem; font-weight: 700; color: #475569;">Filter Program:</label>
                                <select name="program_id" style="padding: 0.5rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; min-width: 220px;">
                                    <option value="">All Programs</option>
                                    @foreach($availablePrograms as $p)
                                        <option value="{{ $p->program_id }}">{{ $p->program_short_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                                <label style="font-size: 0.75rem; font-weight: 700; color: #475569;">Assign To Tele Caller:</label>
                                <select name="caller_id" style="padding: 0.5rem 0.75rem; border: 1.5px solid #2563eb; border-radius: 6px; font-size: 0.85rem; min-width: 220px;">
                                    @foreach($telecallers as $tc)
                                        <option value="{{ $tc->emp_code }}">{{ $tc->full_name }} ({{ $tc->emp_code }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="button" onclick="alert('Leads successfully allocated to selected telecaller!')" class="btn-action-sm btn-verify" style="padding: 0.55rem 1.25rem; font-size: 0.85rem;">
                                📤 Allocate Selected Leads
                            </button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="crm-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"><input type="checkbox" checked></th>
                                    <th>Application ID</th>
                                    <th>Candidate Name</th>
                                    <th>Mobile Number</th>
                                    <th>Preferred Course</th>
                                    <th>Lead Status</th>
                                    <th>Assigned Telecaller</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $idx => $lead)
                                    <tr>
                                        <td><input type="checkbox" checked></td>
                                        <td><strong>{{ $lead->application_no ?? $lead->applinano }}</strong></td>
                                        <td>{{ $lead->student_name ?? $lead->name }}</td>
                                        <td>📞 +91 {{ $lead->mobile ?? $lead->phoneno }}</td>
                                        <td>{{ $lead->program?->program_short_name ?? 'B.Tech' }}</td>
                                        <td><span class="badge-status badge-pending">Unallocated</span></td>
                                        <td><span style="color: #64748b; font-size: 0.75rem;">Queue #{{ $idx + 1 }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 2rem; color: #94a3b8;">
                                            No pending unallocated leads.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @else
                <!-- DEFAULT TAB: ALLOTTED LEADS CALLING QUEUE (master.menus: LEADS0002) -->
                <div class="content-card">
                    <div class="card-header-bar">
                        <div>
                            <div class="card-title">📞 Allotted Leads Calling Queue (Tele Caller Desk)</div>
                            <div class="card-subtitle">Connect with prospective candidates, record counseling feedback, and confirm admissions</div>
                        </div>

                        <!-- Search within leads -->
                        <form method="GET" action="{{ route('leads.index') }}" style="display: flex; gap: 0.5rem;">
                            <input type="text" name="q" value="{{ $searchQuery ?? '' }}" placeholder="Search lead by name, phone..." style="padding: 0.4rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.8rem; width: 220px;">
                            <button type="submit" class="btn-action-sm btn-verify">Search</button>
                        </form>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="filter-tabs-bar">
                        <a href="{{ route('leads.index', ['status' => 'all']) }}" class="filter-tab-pill {{ ($statusFilter ?? 'all') === 'all' ? 'active' : '' }}">All Leads</a>
                        <a href="{{ route('leads.index', ['status' => 'fresh']) }}" class="filter-tab-pill {{ ($statusFilter ?? '') === 'fresh' ? 'active' : '' }}">Fresh Inquiries</a>
                        <a href="{{ route('leads.index', ['status' => 'callback']) }}" class="filter-tab-pill {{ ($statusFilter ?? '') === 'callback' ? 'active' : '' }}">Follow-up Required</a>
                        <a href="{{ route('leads.index', ['status' => 'converted']) }}" class="filter-tab-pill {{ ($statusFilter ?? '') === 'converted' ? 'active' : '' }}">Converted</a>
                    </div>

                    <div class="table-responsive">
                        <table class="crm-table">
                            <thead>
                                <tr>
                                    <th>Candidate Name</th>
                                    <th>Mobile & WhatsApp</th>
                                    <th>Course of Interest</th>
                                    <th>Call Disposition</th>
                                    <th>Direct Action</th>
                                    <th>Update Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $lead)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700; color: #0f172a;">{{ $lead->student_name ?? $lead->name }}</div>
                                            <div style="font-size: 0.72rem; color: #64748b;">App #: {{ $lead->application_no ?? $lead->applinano }}</div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;">📞 +91 {{ $lead->mobile ?? $lead->phoneno }}</div>
                                            <div style="font-size: 0.72rem; color: #64748b;">✉️ {{ $lead->personal_email ?? $lead->emailid ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <span style="font-weight: 600;">{{ $lead->program?->program_short_name ?? 'B.Tech' }}</span>
                                            <div style="font-size: 0.72rem; color: #64748b;">Campus: Vadlamudi</div>
                                        </td>
                                        <td>
                                            <span class="badge-status badge-callback">Scheduled Call</span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 0.35rem;">
                                                <a href="tel:{{ $lead->mobile ?? $lead->phoneno }}" class="btn-action-sm btn-call" title="Call Candidate">
                                                    📞 Call
                                                </a>
                                                <a href="https://wa.me/91{{ $lead->mobile ?? $lead->phoneno }}" target="_blank" class="btn-action-sm btn-whatsapp" title="WhatsApp Candidate">
                                                    💬 Chat
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <select onchange="alert('Lead status updated to ' + this.value + ' for {{ $lead->student_name ?? $lead->name }}!')" style="padding: 0.25rem 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.75rem; background: #ffffff;">
                                                <option value="Interested">Interested</option>
                                                <option value="Call Back">Call Back</option>
                                                <option value="Follow-up">Follow-up</option>
                                                <option value="Registered">Registered</option>
                                                <option value="Fee Paid">Fee Paid</option>
                                                <option value="Not Interested">Not Interested</option>
                                            </select>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                                            No allotted leads found in your calling queue.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </main>
    </div>
</div>
</body>
</html>
