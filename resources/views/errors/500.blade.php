@extends('errors.layout')

@section('title', '500 Server Error')

@section('content')
    <div class="status-pill error-500">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        HTTP 500 &bull; Internal Server Exception
    </div>

    <div class="error-code">500</div>
    <h1 class="error-title">Internal Server Error</h1>
    <p class="error-desc">
        An unexpected server error occurred while processing your request. The issue has been automatically logged with full technical trace to the Error Monitoring KPI.
    </p>

    @if(auth()->check())
    <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 8px; padding: 0.65rem 1rem; margin-bottom: 1.5rem; font-size: 0.85rem; color: #991b1b;">
        <strong>Active Account:</strong> {{ auth()->user()->name ?: auth()->user()->usercode }} &bull; 
        <strong>Role:</strong> <span style="background: #fecaca; padding: 2px 6px; border-radius: 4px; font-weight: 700;">{{ auth()->user()->role_label }}</span>
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
        <a href="javascript:window.location.reload();" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            Retry Request
        </a>
    </div>

    <div class="details-box">
        <div class="details-header" onclick="toggleDetails()">
            <span>System Log &amp; Diagnostic Details</span>
            <span style="font-size: 0.75rem; color: #64748b;">Click to view &darr;</span>
        </div>
        <div class="details-content" id="detailsContent">
            <div><span class="meta-tag">Message</span> {{ ($exception ?? null)?->getMessage() ?: 'An unhandled exception occurred.' }}</div>
            <div style="margin-top: 4px;"><span class="meta-tag">Route</span> {{ request()->method() }} {{ request()->fullUrl() }}</div>
            @if(config('app.debug') && isset($exception))
            <div style="margin-top: 4px;"><span class="meta-tag">File</span> {{ $exception->getFile() }}:{{ $exception->getLine() }}</div>
            @endif
            @if(auth()->check())
            <div style="margin-top: 4px;"><span class="meta-tag">Active User</span> {{ auth()->user()->usercode }} &bull; Role {{ auth()->user()->getActiveRoleId() }}</div>
            @endif
            <div style="margin-top: 4px;"><span class="meta-tag">Timestamp</span> {{ now()->toDateTimeString() }}</div>
        </div>
    </div>
@endsection
