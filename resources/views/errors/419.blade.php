@extends('errors.layout')

@section('title', '419 Page Expired')

@section('content')
    <div class="status-pill error-400">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        HTTP 419 &bull; CSRF Session Expired
    </div>

    <div class="error-code">419</div>
    <h1 class="error-title">Page &amp; Security Token Expired</h1>
    <p class="error-desc">
        Your browser session or CSRF security token has expired due to inactivity. Please refresh the page and submit your form again.
    </p>

    <div class="actions-group">
        <a href="javascript:window.location.reload();" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            Refresh &amp; Try Again
        </a>
        <a href="{{ route('logout') }}" class="btn btn-secondary btn-logout">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Sign Out / Reset
        </a>
        <a href="{{ route('login') }}" class="btn btn-secondary">
            Sign In Again
        </a>
    </div>

    <div class="details-box">
        <div class="details-header" onclick="toggleDetails()">
            <span>Session Diagnostics</span>
            <span style="font-size: 0.75rem; color: #64748b;">Click to view &darr;</span>
        </div>
        <div class="details-content" id="detailsContent">
            <div><span class="meta-tag">Target Path</span> {{ request()->fullUrl() }}</div>
            <div style="margin-top: 4px;"><span class="meta-tag">Method</span> {{ request()->method() }}</div>
        </div>
    </div>
@endsection
