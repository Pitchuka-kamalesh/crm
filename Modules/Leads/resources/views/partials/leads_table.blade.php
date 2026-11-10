<div class="table-responsive">
    <table class="tele-table">
        <thead>
            <tr>
                <th style="width: 38px; text-align: center; vertical-align: middle;">
                    <input type="checkbox" id="selectAllLeadsCheckbox" onchange="toggleSelectAllLeads(this.checked)" title="Select all leads on this page" style="width: 17px; height: 17px; accent-color: #10b981; cursor: pointer; border-radius: 4px; vertical-align: middle;">
                </th>
                <th>Contact</th>
                <th>Candidate Details</th>
                <th>Program &amp; Details</th>
                <th>Admission Process</th>
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

                    // Telecaller allocation info from crm.telecaller_leads
                    $alloc = $dbAllocations[$leadAppNo] ?? null;
                    $leadTasks = $latestTasks[$leadAppNo] ?? collect();
                    $lastTask = $leadTasks->first();

                    // Compute progress across exactly 5 stages
                    $stagesMap = [];
                    $completedCount = 0;
                    foreach (['stage1','stage2','stage3','stage4','stage5'] as $stgKey) {
                        $val = strtolower($lead->{$stgKey} ?? 'locked');
                        $stagesMap[$stgKey] = $val;
                        if (in_array($val, ['completed', 'paid', 'approved'])) {
                            $completedCount++;
                        }
                    }
                    $progressPct = round(($completedCount / 5) * 100);
                    $leadCrmId = $lead->crmid ?? ($alloc?->crmid ?? \App\Services\CrmIdService::getOrGenerateByAppli($leadAppNo));
                @endphp
                <tr id="row-lead-{{ $leadAppNo }}">
                    <td style="text-align: center; vertical-align: middle;">
                        <input type="checkbox" class="lead-select-chk" value="{{ $leadAppNo }}" data-phone="{{ $leadPhone }}" data-name="{{ $leadName }}" onchange="updateSelectedLeadsCount()" style="width: 17px; height: 17px; accent-color: #10b981; cursor: pointer; border-radius: 4px; vertical-align: middle;">
                    </td>
                    <td>
                        <div>
                            <div class="lead-detail-primary" style="font-weight: 700; color: #0f172a;">+91 {{ $leadPhone }}</div>
                            <div style="font-size: 0.72rem; color: #64748b; max-width: 180px; overflow: hidden; text-overflow: ellipsis;" title="{{ $leadEmail }}">{{ $leadEmail }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="candidate-title">{{ $leadName }}</div>
                        <div class="candidate-sub" style="display: flex; align-items: center; gap: 5px; flex-wrap: wrap; margin-top: 3px;">
                            <span>App: <strong style="color: #334155;">{{ $leadAppNo }}</strong></span>
                            <span style="font-size: 0.68rem; font-weight: 700; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 1px 6px; border-radius: 4px; font-family: 'JetBrains Mono', monospace;" title="Branch CRM ID">{{ $leadCrmId }}</span>
                            @if(!empty($lead->state?->state_name)) • {{ $lead->state->state_name }}@endif
                        </div>
                        @if($alloc)
                            <div style="margin-top: 3px;">
                                <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.68rem; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; border-radius: 4px; padding: 1.5px 6px; font-weight: 700;">
                                    <span>👤</span> {{ $alloc->emp_name ?? $alloc->empcode }} (#{{ $alloc->leadcount ?? 1 }})
                                </span>
                            </div>
                        @else
                            <div style="margin-top: 3px;">
                                <span style="display: inline-flex; align-items: center; gap: 3px; font-size: 0.68rem; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; border-radius: 4px; padding: 1.5px 6px; font-weight: 600;">
                                    <span>⚠️</span> Unallocated
                                </span>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="lead-detail-primary">{{ $leadProgram }}</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Campus: {{ $leadCampus }}</div>
                        @if($lastTask)
                            <div style="font-size: 0.68rem; color: #4338ca; font-weight: 600; margin-top: 4px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $lastTask->stage_name }} • {{ $lastTask->stage_remark }}">
                                📌 {{ $lastTask->stage_name }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="stage-meter-badge {{ $completedCount === 5 ? 'is-completed-full' : '' }}">
                            <span>Stage {{ $completedCount }}/5</span>
                            <span style="opacity: 0.75; font-size: 0.72rem; margin-left: 2px;">({{ $progressPct }}%)</span>
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
                    <td colspan="6" style="text-align: center; padding: 3rem 1.5rem; color: #94a3b8;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.5rem;">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        @if(($activeTab ?? '') === 'allocation')
                            <div style="font-weight: 600; color: #059669;">✓ All leads have been allocated!</div>
                            <div style="font-size: 0.78rem; margin-top: 4px;">Check <strong>Lead input</strong> to view the entire dataset and assigned telecaller details.</div>
                        @else
                            <div>No leads found matching your filter criteria.</div>
                        @endif
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
