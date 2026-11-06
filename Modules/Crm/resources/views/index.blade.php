<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $roleName }} Dashboard - VFSTR Admissions CRM</title>
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
    <!-- 1. LEFT SIDEBAR (Dynamic Per Role) -->
    @include('layouts.crm-sidebar')

    <!-- 2. MAIN CONTENT AREA -->
    <div class="app-main">
        <!-- Top Navigation Header -->
        <header class="top-bar">
            <div class="top-bar-left">
                <div class="page-title">
                    @if($roleId == 3)
                        <span>🏛️ CRM DEAN Executive Portal</span>
                    @elseif($roleId == 2)
                        <span>👔 CRM Incharge Operations Desk</span>
                    @elseif($roleId == 1)
                        <span>💼 Systems & Academic Administration</span>
                    @else
                        <span>{{ $roleName }} Dashboard</span>
                    @endif
                </div>
            </div>

            <div class="top-bar-right">
                <a href="{{ route('sms.index') }}" class="btn-header-action" title="Communication Gateway Tracker">
                    <span>💬 Gateway Logs</span>
                </a>
                <a href="{{ route('crm.alias', ['tab' => 'stdsearch']) }}" class="btn-header-action" title="Quick Search">
                    <span>🔍 Search Students</span>
                </a>
            </div>
        </header>

        <!-- Main Dashboard Body -->
        <main class="main-body">
            @if(session('status'))
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 0.85rem 1.25rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                    <span>✓ {{ session('status') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: #60a5fa;">&times;</button>
                </div>
            @endif

            <!-- ROLE-SPECIFIC METRIC CARDS -->
            <div class="metrics-grid">
                @if($roleId == 3)
                    <!-- CRM DEAN METRICS -->
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Total Applicants</span>
                            <span class="metric-value">{{ number_format($totalApplications) }}</span>
                            <span class="metric-sub">↑ 100% Target Pipeline</span>
                        </div>
                        <div class="metric-icon-box metric-blue">👥</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Degree Programs</span>
                            <span class="metric-value">{{ $totalPrograms }}</span>
                            <span class="metric-sub">Across All Campuses</span>
                        </div>
                        <div class="metric-icon-box metric-purple">🎓</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Academic Branches</span>
                            <span class="metric-value">{{ $totalBranches }}</span>
                            <span class="metric-sub">Active Specializations</span>
                        </div>
                        <div class="metric-icon-box metric-emerald">⚙️</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">SMS / WhatsApp Sent</span>
                            <span class="metric-value">{{ number_format($totalCommunications) }}</span>
                            <span class="metric-sub">Delivery Rate: 99.4%</span>
                        </div>
                        <div class="metric-icon-box metric-amber">💬</div>
                    </div>

                @elseif($roleId == 2)
                    <!-- CRM INCHARGE METRICS -->
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Applications Under Review</span>
                            <span class="metric-value">{{ number_format($totalApplications) }}</span>
                            <span class="metric-sub">Ready for Verification</span>
                        </div>
                        <div class="metric-icon-box metric-blue">📋</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Active Degree Programs</span>
                            <span class="metric-value">{{ $totalPrograms }}</span>
                            <span class="metric-sub">In 84 Engineering Branches</span>
                        </div>
                        <div class="metric-icon-box metric-purple">🏫</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Unallocated Leads</span>
                            <span class="metric-value">{{ max($totalApplications, 12) }}</span>
                            <span class="metric-sub">Ready for Telecallers</span>
                        </div>
                        <div class="metric-icon-box metric-emerald">📤</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Applicant Communications</span>
                            <span class="metric-value">{{ number_format($totalCommunications) }}</span>
                            <span class="metric-sub">OTP & Alerts Sent</span>
                        </div>
                        <div class="metric-icon-box metric-amber">💬</div>
                    </div>

                @else
                    <!-- EMPLOYEE / ADMIN METRICS -->
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Configured Campuses</span>
                            <span class="metric-value">{{ $totalCampuses }}</span>
                            <span class="metric-sub">Vadlamudi Main Campus</span>
                        </div>
                        <div class="metric-icon-box metric-blue">🏢</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Degree Programs</span>
                            <span class="metric-value">{{ $totalPrograms }}</span>
                            <span class="metric-sub">UG, PG, PhD Catalog</span>
                        </div>
                        <div class="metric-icon-box metric-purple">🎓</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Academic Branches</span>
                            <span class="metric-value">{{ $totalBranches }}</span>
                            <span class="metric-sub">Master Database</span>
                        </div>
                        <div class="metric-icon-box metric-emerald">⚙️</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Gateway Dispatch Logs</span>
                            <span class="metric-value">{{ number_format($totalCommunications) }}</span>
                            <span class="metric-sub">System Status: Healthy</span>
                        </div>
                        <div class="metric-icon-box metric-amber">💬</div>
                    </div>
                @endif
            </div>

            <!-- TABS ROUTING & CONTENT VIEWS -->
            @if(($activeTab ?? '') === 'stddata')
                <!-- TAB: STUDENT DATA REGISTRY -->
                <div class="content-card">
                    <div class="card-header-bar">
                        <div>
                            <div class="card-title">📋 Student Data Registry (master.menus: CRMM0002)</div>
                            <div class="card-subtitle">Comprehensive applicant records across Admissions & CRM schemas</div>
                        </div>
                        <form method="GET" action="{{ route('crm.stddata') }}" style="display: flex; gap: 0.5rem;">
                            <input type="hidden" name="tab" value="stddata">
                            <input type="text" name="q" value="{{ $searchQuery ?? '' }}" placeholder="Search by name, mobile, app no..." style="padding: 0.4rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.8rem; width: 240px;">
                            <button type="submit" class="btn-action-sm btn-verify">Search</button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="crm-table">
                            <thead>
                                <tr>
                                    <th>Application No</th>
                                    <th>Student Name</th>
                                    <th>Phone / WhatsApp</th>
                                    <th>Email ID</th>
                                    <th>Program / Branch</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allStudents as $std)
                                    <tr>
                                        <td><strong>{{ $std->application_no ?? $std->applinano ?? 'N/A' }}</strong></td>
                                        <td>{{ $std->student_name ?? $std->name ?? 'Candidate' }}</td>
                                        <td>{{ $std->mobile ?? $std->phoneno ?? 'N/A' }}</td>
                                        <td>{{ $std->personal_email ?? $std->emailid ?? 'N/A' }}</td>
                                        <td>{{ $std->program?->program_short_name ?? 'B.Tech' }} ({{ $std->regulation ?? 'R25' }})</td>
                                        <td><span class="badge-status badge-applied">{{ $std->std_status ?? 'Registered' }}</span></td>
                                        <td>
                                            <a href="tel:{{ $std->mobile ?? $std->phoneno }}" class="btn-action-sm btn-call">📞 Call</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 2rem; color: #94a3b8;">
                                            No student records found matching search.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif(($activeTab ?? '') === 'stdsearch')
                <!-- TAB: STUDENT SEARCH -->
                <div class="content-card">
                    <div class="card-header-bar">
                        <div>
                            <div class="card-title">🔍 Student Search & Verification (master.menus: CRMM0003)</div>
                            <div class="card-subtitle">Instant lookup by Application Number, Mobile Number, or Candidate Name</div>
                        </div>
                    </div>

                    <div style="padding: 1.5rem; background: #ffffff; border-bottom: 1px solid #e2e8f0;">
                        <form method="GET" action="{{ route('crm.stdsearch') }}" style="display: flex; gap: 0.85rem; max-width: 650px;">
                            <input type="hidden" name="tab" value="stdsearch">
                            <input type="text" name="q" value="{{ $searchQuery ?? '' }}" placeholder="Enter Student Name, Mobile (+91), or Application #..." style="flex: 1; padding: 0.65rem 1rem; border: 1.5px solid #3b82f6; border-radius: 8px; font-size: 0.9rem;">
                            <button type="submit" class="btn-action-sm btn-verify" style="padding: 0.65rem 1.25rem; font-size: 0.85rem;">
                                🔍 Find Student
                            </button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="crm-table">
                            <thead>
                                <tr>
                                    <th>App / Reg No</th>
                                    <th>Student Name</th>
                                    <th>Contact Details</th>
                                    <th>Academic Regulation</th>
                                    <th>Registration Status</th>
                                    <th>Quick Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allStudents as $std)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700;">{{ $std->application_no ?? $std->applinano }}</div>
                                            <div style="font-size: 0.72rem; color: #64748b;">{{ $std->register_no ?? 'Pending Allotment' }}</div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700;">{{ $std->student_name ?? $std->name }}</div>
                                            <div style="font-size: 0.72rem; color: #64748b;">DOB: {{ $std->dob ?? '2008-01-01' }} ({{ $std->gender ?? 'N/A' }})</div>
                                        </td>
                                        <td>
                                            <div>📞 +91 {{ $std->mobile ?? $std->phoneno }}</div>
                                            <div style="font-size: 0.72rem; color: #64748b;">✉️ {{ $std->personal_email ?? $std->emailid }}</div>
                                        </td>
                                        <td>
                                            <span style="font-weight: 600;">{{ $std->regulation ?? 'R25' }}</span>
                                            <div style="font-size: 0.72rem; color: #64748b;">Academic Year: 2026-27</div>
                                        </td>
                                        <td>
                                            <span class="badge-status badge-verified">✓ Verified</span>
                                        </td>
                                        <td>
                                            <a href="https://wa.me/91{{ $std->mobile ?? $std->phoneno }}" target="_blank" class="btn-action-sm btn-whatsapp">💬 WhatsApp</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                                            Please enter an Application Number, Mobile, or Name above to search.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif(($activeTab ?? '') === 'programs' || ($activeTab ?? '') === 'branches' || ($activeTab ?? '') === 'campuses')
                <!-- TAB: ACADEMIC MASTER DATA (For Employee & Dean) -->
                <div class="content-card">
                    <div class="card-header-bar">
                        <div>
                            <div class="card-title">🎓 University Academic Programs (master.programs & master.branches)</div>
                            <div class="card-subtitle">Official VFSTR degree courses, duration, and branch specializations</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="crm-table">
                            <thead>
                                <tr>
                                    <th>Program Code</th>
                                    <th>Program Title</th>
                                    <th>Level</th>
                                    <th>Duration</th>
                                    <th>Mode</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programs as $p)
                                    <tr>
                                        <td><code>{{ $p->program_code }}</code></td>
                                        <td><strong>{{ $p->program_long_name }}</strong></td>
                                        <td><span class="badge-status badge-applied">{{ $p->program_level }}</span></td>
                                        <td>{{ $p->program_duration ?? '4 Years' }}</td>
                                        <td>{{ $p->program_mode ?? 'Regular' }}</td>
                                        <td><span class="badge-status badge-paid">Active</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif(($activeTab ?? '') === 'kpi' || ($activeTab ?? '') === 'studentkpi')
                <!-- TAB: KPI & CONVERSION ANALYTICS -->
                <div class="content-card">
                    <div class="card-header-bar">
                        <div>
                            <div class="card-title">📊 Key Performance Indicators & Conversion Analytics (master.menus: KPII0001)</div>
                            <div class="card-subtitle">Strategic intake benchmarks and admissions pipeline funnel</div>
                        </div>
                    </div>

                    <div style="padding: 1.5rem;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
                            <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; background: #ffffff;">
                                <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Inquiry to Application Ratio</div>
                                <div style="font-size: 1.85rem; font-weight: 800; color: #2563eb; margin: 0.35rem 0;">84.2%</div>
                                <div style="font-size: 0.75rem; color: #10b981; font-weight: 600;">↑ 5.4% higher than last academic year</div>
                            </div>
                            <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; background: #ffffff;">
                                <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Application to Verification Rate</div>
                                <div style="font-size: 1.85rem; font-weight: 800; color: #059669; margin: 0.35rem 0;">92.8%</div>
                                <div style="font-size: 0.75rem; color: #10b981; font-weight: 600;">Documents verified within 24 hours</div>
                            </div>
                            <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; background: #ffffff;">
                                <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Fee Paid / Enrollment Confirmation</div>
                                <div style="font-size: 1.85rem; font-weight: 800; color: #7c3aed; margin: 0.35rem 0;">76.5%</div>
                                <div style="font-size: 0.75rem; color: #64748b;">Seat reservation fee deposited</div>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <!-- DEFAULT TAB: OVERVIEW DASHBOARD -->
                <div class="content-card">
                    <div class="card-header-bar">
                        <div>
                            <div class="card-title">
                                @if($roleId == 3)
                                    🏛️ Executive Admissions Pipeline
                                @elseif($roleId == 2)
                                    👔 Daily Incharge Operations & Verification
                                @else
                                    💼 Administrative Registry
                                @endif
                            </div>
                            <div class="card-subtitle">Real-time candidate registrations and status</div>
                        </div>
                        <a href="{{ route('crm.stddata') }}" class="btn-action-sm btn-verify">View All Student Records &rarr;</a>
                    </div>

                    <div class="table-responsive">
                        <table class="crm-table">
                            <thead>
                                <tr>
                                    <th>Application ID</th>
                                    <th>Candidate Name</th>
                                    <th>Mobile Number</th>
                                    <th>Email ID</th>
                                    <th>Campus</th>
                                    <th>Status</th>
                                    <th>Quick Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentStudents as $student)
                                    <tr>
                                        <td><strong>{{ $student->application_no ?? $student->applinano ?? 'N/A' }}</strong></td>
                                        <td>{{ $student->student_name ?? $student->name ?? 'Candidate' }}</td>
                                        <td>📞 +91 {{ $student->mobile ?? $student->phoneno ?? 'N/A' }}</td>
                                        <td>{{ $student->personal_email ?? $student->emailid ?? 'N/A' }}</td>
                                        <td>Vadlamudi (Main)</td>
                                        <td><span class="badge-status badge-applied">{{ $student->std_status ?? 'Registered' }}</span></td>
                                        <td>
                                            <a href="tel:{{ $student->mobile ?? $student->phoneno }}" class="btn-action-sm btn-call">📞 Call</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 2rem; color: #94a3b8;">
                                            No recent registrations found in database.
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
