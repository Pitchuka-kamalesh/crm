@extends('errors.layout')

@section('title', '404 Page Not Found')

@section('content')
    <div class="status-pill error-404">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        HTTP 404 &bull; Resource Missing
    </div>

    <div class="error-code">404</div>
    <h1 class="error-title">Page or Route Not Found</h1>
    <p class="error-desc">
        {{ ($exception ?? null)?->getMessage() ?: 'The URL endpoint you navigated to does not exist or may have been moved to another location in the portal.' }}
    </p>

    @if(auth()->check())
    <div style="background: #f0f9ff; border: 1px solid #e0f2fe; border-radius: 8px; padding: 0.65rem 1rem; margin-bottom: 1.5rem; font-size: 0.85rem; color: #0369a1;">
        <strong>Active Account:</strong> {{ auth()->user()->name ?: auth()->user()->usercode }} &bull; 
        <strong>Role:</strong> <span style="background: #bae6fd; padding: 2px 6px; border-radius: 4px; font-weight: 700;">{{ auth()->user()->role_label }}</span>
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
        <a href="{{ url()->previous() ?: '/' }}" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Go Back
        </a>
    </div>

    <div class="details-box">
        <div class="details-header" onclick="toggleDetails()">
            <span>Route Details</span>
            <span style="font-size: 0.75rem; color: #64748b;">Click to view &darr;</span>
        </div>
        <div class="details-content" id="detailsContent">
            <div><span class="meta-tag">Requested URI</span> {{ request()->path() }}</div>
            <div style="margin-top: 4px;"><span class="meta-tag">HTTP Method</span> {{ request()->method() }}</div>
        </div>
    </div>
@endsection
