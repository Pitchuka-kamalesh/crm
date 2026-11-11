@if(($activeTab ?? '') === 'allocation')
    <div class="table-responsive">
        <table class="tele-table">
            <thead>
                <tr>
                    <th style="width: 42px; text-align: center; vertical-align: middle;">
                        <input type="checkbox" id="selectAllLeadsCheckbox" onchange="toggleSelectAllLeads(this.checked)" title="Select all leads on this page" style="width: 17px; height: 17px; accent-color: #10b981; cursor: pointer; border-radius: 4px; vertical-align: middle;">
                    </th>
                    <th>Phone Number</th>
                    <th>Timestamp</th>
                    <th>Empcode</th>
                    <th>Allocated Timestamp</th>
                </tr>
            </thead>
            <tbody id="teleTableBody">
                @forelse($leads as $lead)
                    @php
                        $leadPhone = (string) ($lead->phoneno ?? '');
                        $leadAppNo = !empty($lead->applino) ? (string) $lead->applino : '0';
                        $hasEmpCode = !empty($lead->empcode) && $lead->empcode !== '0';
                        $empCodeVal = $hasEmpCode ? $lead->empcode : '0';

                        $leadTimestamp = '0';
                        if (!empty($lead->log_timestamp)) {
                            $leadTimestamp = $lead->log_timestamp instanceof \DateTimeInterface 
                                ? $lead->log_timestamp->format('Y-m-d H:i:s') 
                                : (string) $lead->log_timestamp;
                        } elseif (!empty($lead->enquirydate)) {
                            $leadTimestamp = $lead->enquirydate instanceof \DateTimeInterface 
                                ? $lead->enquirydate->format('Y-m-d H:i:s') 
                                : (string) $lead->enquirydate;
                        }

                        $allocTimestamp = '0';
                        if ($hasEmpCode && !empty($lead->allocated_at)) {
                            $allocTimestamp = $lead->allocated_at instanceof \DateTimeInterface 
                                ? $lead->allocated_at->format('Y-m-d H:i:s') 
                                : (string) $lead->allocated_at;
                        }
                    @endphp
                    <tr id="row-lead-{{ $leadPhone }}" style="{{ $hasEmpCode ? 'background-color: #f8fafc;' : '' }}">
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="checkbox" 
                                   class="lead-select-chk" 
                                   value="{{ $leadAppNo }}" 
                                   data-phone="{{ $leadPhone }}" 
                                   data-name="{{ $leadPhone }}" 
                                   onchange="updateSelectedLeadsCount()" 
                                   style="width: 17px; height: 17px; accent-color: #10b981; cursor: pointer; border-radius: 4px; vertical-align: middle;">
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a; font-family: 'JetBrains Mono', monospace; font-size: 0.88rem;">
                                {{ $leadPhone }}
                            </div>
                        </td>
                        <td>
                            <div style="color: #334155; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">
                                {{ $leadTimestamp }}
                            </div>
                        </td>
                        <td>
                            @if($hasEmpCode)
                                <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.78rem; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; border-radius: 4px; padding: 2px 8px; font-weight: 700; font-family: 'JetBrains Mono', monospace;">
                                    👤 {{ $empCodeVal }}
                                </span>
                            @else
                                <span style="font-weight: 600; color: #64748b; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">0</span>
                            @endif
                        </td>
                        <td>
                            @if($allocTimestamp !== '0')
                                <span style="color: #0f172a; font-weight: 600; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">
                                    {{ $allocTimestamp }}
                                </span>
                            @else
                                <span style="font-weight: 600; color: #64748b; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">0</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 3rem 1.5rem; color: #94a3b8;">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.5rem;">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <div>No leads found in leads table.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@elseif(($activeTab ?? '') === 'allotted')
    <div class="table-responsive">
        <table class="tele-table">
            <thead>
                <tr>
                    <th>Phone Number</th>
                    <th>Student Created Timestamp</th>
                    <th>Allocated Timestamp</th>
                    <th>Current Step</th>
                    <th>Application</th>
                    <th>CRM ID</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody id="teleTableBody">
                @forelse($leads as $lead)
                    @php
                        $leadPhone = (string) ($lead->phoneno ?? '0');
                        $leadAppli = (!empty($lead->student_applinano) && $lead->student_applinano !== '0') 
                            ? (string) $lead->student_applinano 
                            : ((!empty($lead->applino) && $lead->applino !== '0') ? (string) $lead->applino : '0');
                        $leadCrmId = (!empty($lead->crmid) && $lead->crmid !== '0') ? (string) $lead->crmid : '0';
                        if ($leadCrmId === '0' && !empty($leadAppli) && $leadAppli !== '0') {
                            $leadCrmId = \App\Services\CrmIdService::getOrGenerateByAppli($leadAppli) ?: '0';
                        }

                        // 1. Student Created Timestamp taken directly from leads table column log_timestamp
                        $studentCreatedTs = '0';
                        if (!empty($lead->lead_log_timestamp)) {
                            $studentCreatedTs = $lead->lead_log_timestamp instanceof \DateTimeInterface 
                                ? $lead->lead_log_timestamp->format('Y-m-d H:i:s') 
                                : date('Y-m-d H:i:s', strtotime($lead->lead_log_timestamp));
                        } elseif (!empty($lead->lead_enquirydate)) {
                            $studentCreatedTs = $lead->lead_enquirydate instanceof \DateTimeInterface 
                                ? $lead->lead_enquirydate->format('Y-m-d') 
                                : (string) $lead->lead_enquirydate;
                        }

                        // 2. Allocated Timestamp
                        $allocatedTs = '0';
                        if (!empty($lead->assigndatetime)) {
                            $allocatedTs = $lead->assigndatetime instanceof \DateTimeInterface 
                                ? $lead->assigndatetime->format('Y-m-d H:i:s') 
                                : date('Y-m-d H:i:s', strtotime($lead->assigndatetime));
                        } elseif (!empty($lead->lead_allocated_at)) {
                            $allocatedTs = $lead->lead_allocated_at instanceof \DateTimeInterface 
                                ? $lead->lead_allocated_at->format('Y-m-d H:i:s') 
                                : date('Y-m-d H:i:s', strtotime($lead->lead_allocated_at));
                        }

                        // 3. Current Step determined by user rules:
                        // - if applino not created & not assigned -> New Lead
                        // - if applino not created & assigned -> Telecaller Stage
                        // - if applino created & not assigned -> Register Form
                        // - if applino not paid -> Payment Stage
                        // - if applino paid, personal not filled -> Personal Details
                        // - if applino paid, personal filled, academic not filled -> Academic Details
                        $currentStep = \App\Services\TelecallerService::determineLeadStage(
                            $leadAppli,
                            $lead->empcode ?? null,
                            $lead->student_stage1 ?? null,
                            $lead->student_stage2 ?? null,
                            $lead->student_stage3 ?? null
                        );
                        $badgeStyle = \App\Services\TelecallerService::getStageBadgeStyle($currentStep);

                        $candidateName = $lead->student_name ?: 'Candidate';
                        $candidateProgram = $lead->student_program ?: 'UG Program';
                        $candidateRemarks = $lead->student_remarks ?: '';
                    @endphp
                    <tr id="row-allotted-{{ $leadPhone }}">
                        <!-- 1. Phone Number -->
                        <td>
                            <div style="font-weight: 700; color: #0f172a; font-family: 'JetBrains Mono', monospace; font-size: 0.88rem;">
                                {{ $leadPhone }}
                            </div>
                        </td>

                        <!-- 2. Student Created Timestamp (sourced from leads table log_timestamp) -->
                        <td>
                            @if($studentCreatedTs !== '0')
                                <div style="color: #334155; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">
                                    {{ $studentCreatedTs }}
                                </div>
                            @else
                                <span style="font-weight: 600; color: #64748b; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">0</span>
                            @endif
                        </td>

                        <!-- 3. Allocated Timestamp -->
                        <td>
                            @if($allocatedTs !== '0')
                                <div style="color: #334155; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">
                                    {{ $allocatedTs }}
                                </div>
                            @else
                                <span style="font-weight: 600; color: #64748b; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">0</span>
                            @endif
                        </td>

                        <!-- 4. Current Step -->
                        <td>
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2.5px 8px; border-radius: 4px; font-size: 0.78rem; font-weight: 700; background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; border: 1px solid {{ $badgeStyle['border'] }};">
                                <span>{{ $badgeStyle['icon'] }}</span> {{ $currentStep }}
                            </span>
                        </td>

                        <!-- 5. Application -->
                        <td>
                            @if($leadAppli !== '0')
                                <span style="font-weight: 700; color: #0f172a; font-family: 'JetBrains Mono', monospace; font-size: 0.84rem;">
                                    {{ $leadAppli }}
                                </span>
                            @else
                                <span style="font-weight: 600; color: #64748b; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">0</span>
                            @endif
                        </td>

                        <!-- 6. CRM ID -->
                        <td>
                            @if($leadCrmId !== '0')
                                <span style="display: inline-flex; align-items: center; font-size: 0.76rem; font-weight: 700; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 1.5px 6px; border-radius: 4px; font-family: 'JetBrains Mono', monospace;">
                                    {{ $leadCrmId }}
                                </span>
                            @else
                                <span style="font-weight: 600; color: #64748b; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">0</span>
                            @endif
                        </td>

                        <!-- 7. Action Button -->
                        <td style="text-align: right;">
                            <button type="button" 
                                    class="tele-btn-action tele-btn-wizard" 
                                    onclick="openWizardModal('{{ $leadAppli !== '0' ? $leadAppli : $leadPhone }}', '{{ addslashes($candidateName) }}', '{{ $leadPhone }}', '{{ addslashes($candidateProgram) }}', {}, '{{ addslashes($candidateRemarks) }}', '{{ addslashes($currentStep) }}')"
                                    title="Open action wizard to log call & update status">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                <span>Action</span>
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
                            <div>No leads allocated to your queue yet.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@else
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
                        @php
                            $leadStage = \App\Services\TelecallerService::determineLeadStage(
                                $leadAppNo,
                                $alloc?->empcode ?? null,
                                $lead->stage1 ?? null,
                                $lead->stage2 ?? null,
                                $lead->stage3 ?? null
                            );
                            $leadBadge = \App\Services\TelecallerService::getStageBadgeStyle($leadStage);
                        @endphp
                        <td>
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2.5px 8px; border-radius: 4px; font-size: 0.78rem; font-weight: 700; background: {{ $leadBadge['bg'] }}; color: {{ $leadBadge['color'] }}; border: 1px solid {{ $leadBadge['border'] }};">
                                <span>{{ $leadBadge['icon'] }}</span> {{ $leadStage }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <button type="button" 
                                    class="tele-btn-action tele-btn-wizard" 
                                    onclick="openWizardModal('{{ $leadAppNo }}', '{{ addslashes($leadName) }}', '{{ $leadPhone }}', '{{ $leadProgram }}', {}, '{{ addslashes($lead->remarks ?? '') }}', '{{ addslashes($leadStage) }}')"
                                    title="Open action wizard to log call & update status">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                <span>Action</span>
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
                            <div>No leads found matching your filter criteria.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif

<!-- PAGINATION BAR -->
@if($leads->hasPages())
    <div class="tele-pagination-bar">
        <div class="tele-pagination-meta">
            Showing <span style="font-weight: 700; color: #0f172a;">{{ $leads->firstItem() ?? 0 }}</span> to <span style="font-weight: 700; color: #0f172a;">{{ $leads->lastItem() ?? 0 }}</span> of <span style="font-weight: 700; color: #0f172a;">{{ $leads->total() }}</span> records ({{ $leads->perPage() }} per page)
        </div>
        <div class="tele-pagination-links">
            {{ $leads->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endif
