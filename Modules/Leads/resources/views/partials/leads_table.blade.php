<div class="table-responsive">
    <table class="tele-table">
        <thead>
            <tr>
                <th>Candidate Details</th>
                <th>Contact</th>
                <th>Program &amp; Campus</th>
                <th>Admission Progress</th>
                <th>Counseling Status</th>
                <th>Direct Connect</th>
                <th style="text-align: right;">Assessment Wizard</th>
            </tr>
        </thead>
        <tbody id="teleTableBody">
            @forelse($leads as $lead)
                @php
                    $leadAppNo = $lead->applinano ?? $lead->applino ?? 'APP' . $lead->phoneno;
                    $leadName = $lead->name ?? $lead->student_name ?? 'Candidate';
                    $leadPhone = $lead->phoneno ?? $lead->mobile ?? '';
                    $leadEmail = $lead->emailid ?? $lead->personal_email ?? 'N/A';
                    $progShort = $lead->program?->program_short_name ?? $lead->programlevel ?? 'B.Tech';
                    $branchLabel = $lead->branch?->branch_long_name ?? $lead->branch?->branch_short_name ?? '';
                    $leadProgram = $branchLabel ? "{$progShort} - {$branchLabel}" : $progShort;
                    $leadCampus = $lead->campus?->campus_short_name ?? 'Vadlamudi';
                    $leadStatus = $lead->std_status ?? $lead->phnostatus ?? 'Interested';

                    // Compute progress
                    $stagesMap = [];
                    $completedCount = 0;
                    foreach (['stage1','stage2','stage3','stage4','stage5','stage6','stage7','stage8'] as $stgKey) {
                        $val = strtolower($lead->{$stgKey} ?? 'locked');
                        $stagesMap[$stgKey] = $val;
                        if (in_array($val, ['completed', 'paid', 'approved'])) {
                            $completedCount++;
                        }
                    }
                    $progressPct = round(($completedCount / 8) * 100);
                @endphp
                <tr>
                    <td>
                        <div class="candidate-title">{{ $leadName }}</div>
                        <div class="candidate-sub">App: {{ $leadAppNo }}@if(!empty($lead->state?->state_name)) • {{ $lead->state->state_name }}@endif</div>
                    </td>
                    <td>
                        <div class="lead-detail-primary">+91 {{ $leadPhone }}</div>
                        <div style="font-size: 0.72rem; color: #64748b;">{{ $leadEmail }}</div>
                    </td>
                    <td>
                        <div class="lead-detail-primary">{{ $leadProgram }}</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Campus: {{ $leadCampus }}</div>
                    </td>
                    <td>
                        <div class="stage-meter-badge {{ $completedCount === 8 ? 'is-completed-full' : '' }}">
                            <span>Stage {{ $completedCount }}/8</span>
                            <span style="opacity: 0.7;">({{ $progressPct }}%)</span>
                        </div>
                    </td>
                    <td>
                        <select class="tele-status-select" onchange="updateLeadDisposition('{{ $leadAppNo }}', this.value)">
                            <option value="Interested" {{ $leadStatus === 'Interested' ? 'selected' : '' }}>Interested</option>
                            <option value="Follow-up" {{ $leadStatus === 'Follow-up' ? 'selected' : '' }}>Follow-up</option>
                            <option value="Call Back" {{ $leadStatus === 'Call Back' ? 'selected' : '' }}>Call Back</option>
                            <option value="Registered" {{ $leadStatus === 'Registered' ? 'selected' : '' }}>Registered</option>
                            <option value="Fee Paid" {{ $leadStatus === 'Fee Paid' ? 'selected' : '' }}>Fee Paid</option>
                            <option value="Not Interested" {{ $leadStatus === 'Not Interested' ? 'selected' : '' }}>Not Interested</option>
                        </select>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.35rem;">
                            <a href="tel:{{ $leadPhone }}" class="tele-btn-action tele-btn-call" title="Call">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <span>Call</span>
                            </a>
                            <a href="https://wa.me/91{{ $leadPhone }}" target="_blank" class="tele-btn-action tele-btn-wapp" title="WhatsApp">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                </svg>
                                <span>Chat</span>
                            </a>
                        </div>
                    </td>
                    <td style="text-align: right;">
                        <button type="button" 
                                class="tele-btn-action tele-btn-wizard" 
                                onclick="openWizardModal('{{ $leadAppNo }}', '{{ addslashes($leadName) }}', '{{ $leadPhone }}', '{{ $leadProgram }}', {{ json_encode($stagesMap) }}, '{{ addslashes($lead->remarks ?? '') }}')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            <span>Wizard</span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem 1.5rem; color: #94a3b8;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.5rem;">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <div>No allotted leads found matching criteria.</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- PAGINATION BAR -->
@if($leads->hasPages())
    <div class="tele-pagination-bar">
        <div class="tele-pagination-meta">
            Showing <span style="font-weight: 700; color: #0f172a;">{{ $leads->firstItem() ?? 0 }}</span> to <span style="font-weight: 700; color: #0f172a;">{{ $leads->lastItem() ?? 0 }}</span> of <span style="font-weight: 700; color: #0f172a;">{{ $leads->total() }}</span> records (25 per page)
        </div>
        <div class="tele-pagination-links">
            {{ $leads->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endif

