<!-- Single-Line Compact Metrics -->
<div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:0.6rem 1.1rem; margin-bottom:0.75rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
    <div><span>Total Registered:</span> <strong>{{ number_format($totalRegistered) }}</strong></div>
    <div><span>Application Fee Paid:</span> <strong>{{ number_format($totalPaid) }}</strong></div>
    <div><span>Exam Slots Booked:</span> <strong>{{ number_format($totalExamBooked) }}</strong></div>
    <div><span>Fee Pending:</span> <strong>{{ number_format($totalPending) }}</strong></div>
</div>

<!-- Horizontal Filter Row with Export Icon -->
<form method="GET" action="{{ route('exam.totaldetails') }}" style="margin:0; width:100%;">
    <div class="total-details-toolbar">
        <div class="total-details-filters-group">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="App No, CRM ID, Name, Ph..." class="search-input-custom">
            <select name="programlevel" class="filter-select-custom" onchange="this.form.submit()">...</select>
            <select name="intergroup" class="filter-select-custom" onchange="this.form.submit()">...</select>
            <select name="feestatus" class="filter-select-custom" onchange="this.form.submit()">...</select>
            <select name="slotstatus" class="filter-select-custom" onchange="this.form.submit()">...</select>
            <button type="submit" class="btn">Filter</button>
            @if(request()->anyFilled(['search', 'programlevel', 'intergroup', 'feestatus', 'slotstatus']))
                <a href="{{ route('exam.totaldetails') }}" class="btn">✕ Reset</a>
            @endif
            <!-- Download Icon Button Only -->
            <a href="{{ route('exam.totaldetails.export', request()->query()) }}" class="btn-excel" style="height:32px; width:36px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:6px;" title="Export to Excel">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            </a>
        </div>
    </div>
</form>
