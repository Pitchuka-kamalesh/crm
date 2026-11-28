@extends('errors.layout')

@section('title', '400 Bad Request')

@section('content')
    <div class="status-pill error-400">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        HTTP 400 &bull; Invalid Request
    </div>

    <div class="error-code">400</div>
    <h1 class="error-title">Bad Request Syntax</h1>
    <p class="error-desc">
        {{ ($exception ?? null)?->getMessage() ?: 'The server could not process the request because the syntax, headers, or query parameters were malformed or invalid.' }}
    </p>

    @if(auth()->check())
    <div style="background: #fff7ed; border: 1px solid #ffedd5; border-radius: 8px; padding: 0.65rem 1rem; margin-bottom: 1.5rem; font-size: 0.85rem; color: #9a3412;">
        <strong>Active Account:</strong> {{ auth()->user()->name ?: auth()->user()->usercode }} &bull; 
        <strong>Role:</strong> <span style="background: #fed7aa; padding: 2px 6px; border-radius: 4px; font-weight: 700;">{{ auth()->user()->role_label }}</span>
    </div>
    @endif

    <div class="actions-group">
        <a href="{{ auth()->check() ? (auth()->user()->user_type == 2 ? route('crm.stddata') : route('dashboards.index')) : route('login') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Return to Dashboard
        </a>
        @if(auth()->check())
        <a href="{{ route('logout') }}" class="btn btn-secondary btn-logout">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Sign Out / Switch Account
        </a>
        @endif
        <a href="{{ url()->previous() ?: route('login') }}" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Go Back
        </a>
    </div>

    <div class="details-box">
        <div class="details-header" onclick="toggleDetails()">
            <span>Technical Diagnostics</span>
            <span style="font-size: 0.75rem; color: #64748b;">Click to view &darr;</span>
        </div>
        <div class="details-content" id="detailsContent">
            <div><span class="meta-tag">URL</span> {{ request()->fullUrl() }}</div>
            <div style="margin-top: 4px;"><span class="meta-tag">Method</span> {{ request()->method() }}</div>
            <div style="margin-top: 4px;"><span class="meta-tag">IP</span> {{ request()->ip() }}</div>
            @if(auth()->check())
            <div style="margin-top: 4px;"><span class="meta-tag">User</span> {{ auth()->user()->usercode }} (Role {{ auth()->user()->getActiveRoleId() }})</div>
            @endif
        </div>
    </div>
@endsection
