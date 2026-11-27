@extends('errors.layout')

@section('title', '503 Service Unavailable')

@section('content')
    <div class="status-pill error-503">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        HTTP 503 &bull; Scheduled Maintenance
    </div>

    <div class="error-code">503</div>
    <h1 class="error-title">Service Temporarily Unavailable</h1>
    <p class="error-desc">
        The Admissions CRM portal is currently undergoing scheduled maintenance or database upgrades. Services will be restored shortly.
    </p>

    <div class="actions-group">
        <a href="javascript:window.location.reload();" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            Check Again
        </a>
        @if(auth()->check())
        <a href="{{ route('logout') }}" class="btn btn-secondary btn-logout">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Sign Out
        </a>
        @endif
    </div>

    <div class="details-box">
        <div class="details-header" onclick="toggleDetails()">
            <span>Maintenance Information</span>
            <span style="font-size: 0.75rem; color: #64748b;">Click to view &darr;</span>
        </div>
        <div class="details-content" id="detailsContent">
            <div><span class="meta-tag">Status</span> Maintenance Active</div>
            <div style="margin-top: 4px;"><span class="meta-tag">Server Time</span> {{ now()->timezone('Asia/Kolkata')->format('d M Y, h:i:s A') }} IST</div>
        </div>
    </div>
@endsection
