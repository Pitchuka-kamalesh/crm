@extends('errors.layout')

@section('title', '401 Unauthorized')

@section('content')
    <div class="status-pill error-403">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        HTTP 401 &bull; Authentication Required
    </div>

    <div class="error-code">401</div>
    <h1 class="error-title">Authentication Required</h1>
    <p class="error-desc">
        {{ ($exception ?? null)?->getMessage() ?: 'Your session is not authenticated or has expired. Please sign in with your mobile number or employee code to continue.' }}
    </p>

    <div class="actions-group">
        <a href="{{ route('login') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            Sign In Now
        </a>
        <a href="{{ route('logout') }}" class="btn btn-secondary btn-logout">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Reset Session / Logout
        </a>
        <a href="{{ url()->previous() ?: '/' }}" class="btn btn-secondary">
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
            <div><span class="meta-tag">Target URL</span> {{ request()->fullUrl() }}</div>
            <div style="margin-top: 4px;"><span class="meta-tag">Client IP</span> {{ request()->ip() }}</div>
        </div>
    </div>
@endsection
