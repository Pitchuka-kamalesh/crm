@extends('errors.layout')

@section('title', '403 Forbidden')

@section('content')
    <div class="status-pill error-403">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        HTTP 403 &bull; Role Access Restricted
    </div>

    <div class="error-code">403</div>
    <h1 class="error-title">Unauthorized Role Access</h1>
    <p class="error-desc">
        {{ ($exception ?? null)?->getMessage() ?: 'You do not have the required role permissions to view or perform operations on this section.' }}
    </p>

    @if(auth()->check())
    <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1.5rem; font-size: 0.85rem; color: #92400e;">
        <strong>Logged in as:</strong> {{ auth()->user()->name ?: auth()->user()->usercode }} &bull; 
        <strong>Current Role:</strong> <span style="background: #fde68a; padding: 2px 6px; border-radius: 4px; font-weight: 700;">{{ auth()->user()->role_label }}</span> (Role {{ auth()->user()->getActiveRoleId() }})
    </div>
    @endif

    <div class="actions-group">
        <a href="{{ auth()->check() ? (auth()->user()->user_type == 2 ? route('crm.stddata') : route('dashboards.index')) : route('login') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Go to My Dashboard
        </a>
        <a href="{{ route('logout') }}" class="btn btn-secondary btn-logout">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Switch Account / Logout
        </a>
        <a href="{{ url()->previous() ?: route('login') }}" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Go Back
        </a>
    </div>

    <div class="details-box">
        <div class="details-header" onclick="toggleDetails()">
            <span>Role &amp; Security Diagnostics</span>
            <span style="font-size: 0.75rem; color: #64748b;">Click to view &darr;</span>
        </div>
        <div class="details-content" id="detailsContent">
            <div><span class="meta-tag">Requested Route</span> {{ request()->fullUrl() }}</div>
            @if(auth()->check())
            <div style="margin-top: 4px;"><span class="meta-tag">User Code</span> {{ auth()->user()->usercode }}</div>
            <div style="margin-top: 4px;"><span class="meta-tag">Active Role ID</span> {{ auth()->user()->getActiveRoleId() }}</div>
            <div style="margin-top: 4px;"><span class="meta-tag">Assigned Roles</span> {{ json_encode(auth()->user()->getAssignedRoleIds()) }}</div>
            @endif
            <div style="margin-top: 4px;"><span class="meta-tag">Client IP</span> {{ request()->ip() }}</div>
        </div>
    </div>
@endsection
