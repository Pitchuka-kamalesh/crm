<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\CrmStudentRegistration;
use Modules\Auth\Models\Employee;
use Modules\Leads\Models\Lead;
use Modules\Finance\Models\PayDetail;
use Modules\Finance\Models\ReceiptDetail;
use Modules\Leads\Models\TelecallerLead;
use Modules\Leads\Models\TelecallerLeadTask;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class KpiAnalyticsService
{
    /**
     * Cache verified paid application numbers in-memory per request lifecycle.
     */
    protected ?Collection $paidAppNumbers = null;

    /**
     * Retrieve all verified paid application numbers (status 'paid' or verified payment record).
     */
    public function getVerifiedPaidAppNumbers(): Collection
    {
        if ($this->paidAppNumbers !== null) {
            return $this->paidAppNumbers;
        }

        try {
            $pdPaid = PayDetail::where(function ($q) {
                $q->where('txnstatus', 'success')
                  ->orWhere('paidamount', '>', 0);
            })->pluck('applino')->map(fn ($v) => (string) $v);

            $rdPaid = ReceiptDetail::pluck('applino')->map(fn ($v) => (string) $v);
            $stage1Paid = CrmStudentRegistration::where('stage1', 'paid')->pluck('applinano')->map(fn ($v) => (string) $v);

            $this->paidAppNumbers = $pdPaid->merge($rdPaid)->merge($stage1Paid)->unique()->filter()->values();
        } catch (\Throwable $e) {
            $this->paidAppNumbers = collect();
        }

        return $this->paidAppNumbers;
    }

    /**
     * Compute Student Conversion Funnel & Branch Intake Demand metrics.
     * Flow: Registration -> Stage 1 (Personal Profile) -> Stage 2 (Academic Info) -> Stage 3 (Document Upload) -> Stage 4 (Fee Payment) -> Stage 5 (Exam Slot Booking).
     */
    public function getStudentKpiMetrics(): array
    {
        $paidAppNos = $this->getVerifiedPaidAppNumbers();
        $totalStudents = CrmStudentRegistration::count();
        $kpiTotal = max(1, $totalStudents);

        // Stage 1: Personal Profile
        $kpiStage1Profile = CrmStudentRegistration::where(function ($q) {
            $q->whereIn(DB::raw('LOWER(TRIM(stage1))'), ['completed', 'approved', 'done'])
              ->orWhereHas('personalInfo');
        })->count();

        // Stage 2: Academic Info
        $kpiStage2Academic = CrmStudentRegistration::where(function ($q) {
            $q->whereIn(DB::raw('LOWER(TRIM(stage2))'), ['completed', 'approved', 'done'])
              ->orWhereHas('academicDetails');
        })->count();

        // Stage 3: Document Upload
        $kpiStage3Docs = CrmStudentRegistration::where(function ($q) {
            $q->whereIn(DB::raw('LOWER(TRIM(stage3))'), ['completed', 'approved', 'done'])
              ->orWhereHas('uploads');
        })->count();

        // Stage 4: Fee Payment
        $kpiStage4Fee = CrmStudentRegistration::where(function ($q) use ($paidAppNos) {
            $q->whereIn(DB::raw('LOWER(TRIM(stage4))'), ['paid', 'completed'])
              ->orWhere('stage1', 'paid')
              ->orWhereIn('applinano', $paidAppNos);
        })->count();

        // Stage 5: Exam Slot Booking
        $kpiStage5Slot = CrmStudentRegistration::where(function ($q) {
            $q->whereIn(DB::raw('LOWER(TRIM(stage5))'), ['completed', 'approved', 'done'])
              ->orWhereHas('examSchedule');
        })->count();

        // Branch distribution aggregated via Eloquent Model query
        $branchCounts = CrmStudentRegistration::query()
            ->leftJoin('master.branches as b', 'crm.student_registration.branchid', '=', 'b.branch_id')
            ->select(DB::raw("COALESCE(b.branch_long_name, b.branch_short_name, 'General Engineering') as branch_name"), DB::raw('count(*) as aggregate_count'))
            ->groupBy(DB::raw("COALESCE(b.branch_long_name, b.branch_short_name, 'General Engineering')"))
            ->orderByDesc('aggregate_count')
            ->limit(6)
            ->pluck('aggregate_count', 'branch_name');

        return [
            'kpiTotal'              => $kpiTotal,
            'kpiStage1Profile'      => $kpiStage1Profile,
            'kpiStage2Academic'     => $kpiStage2Academic,
            'kpiStage3Docs'         => $kpiStage3Docs,
            'kpiStage4Fee'          => $kpiStage4Fee,
            'kpiStage5Slot'         => $kpiStage5Slot,

            // Backward compatibility aliases
            'kpiStage1Done'         => $kpiStage1Profile,
            'kpiStage2Done'         => $kpiStage2Academic,
            'kpiStage3Done'         => $kpiStage3Docs,
            'kpiStage4Done'         => $kpiStage4Fee,
            'kpiStage4Paid'         => $kpiStage4Fee,
            'kpiStage1Paid'         => $kpiStage4Fee,
            'kpiStage5Done'         => $kpiStage5Slot,

            'kpiBranchDistribution' => $branchCounts,
            'paidAppNos'            => $paidAppNos,
        ];
    }

    /**
     * Compute Telecaller Team Performance & Conversion statistics.
     */
    public function getTelecallerKpiMetrics(): array
    {
        $paidAppNos = $this->getVerifiedPaidAppNumbers();

        $totalTelecallerLeads = 0;
        try {
            $totalTelecallerLeads = TelecallerLead::count();
        } catch (\Throwable $e) {}

        $telecallerStats = collect();
        try {
            $telecallers = Employee::whereIn('emp_code', function ($q) {
                $q->select('emp_id')->from('master.roles_assigned')->where('roles_role_id', 4);
            })->orWhereIn('emp_code', function ($q) {
                $q->select('empcode')->from('crm.telecaller_leads');
            })->get();

            // Preload lead and task aggregations in bulk to eliminate N+1 query loop
            $leadCountsByEmp = TelecallerLead::select('empcode', DB::raw('count(*) as cnt'))
                ->groupBy('empcode')
                ->pluck('cnt', 'empcode');

            $taskCountsByEmp = TelecallerLeadTask::select('empcode', DB::raw('count(*) as cnt'))
                ->groupBy('empcode')
                ->pluck('cnt', 'empcode');

            $allLeadsByEmp = TelecallerLead::select('empcode', 'applino')->get()->groupBy('empcode');

            $telecallerStats = $telecallers->map(function ($emp) use ($leadCountsByEmp, $taskCountsByEmp, $allLeadsByEmp, $paidAppNos) {
                $empCode = (string) $emp->emp_code;
                $leadsCount = (int) ($leadCountsByEmp[$empCode] ?? 0);
                $tasksCount = (int) ($taskCountsByEmp[$empCode] ?? 0);

                $empLeads = $allLeadsByEmp[$empCode] ?? collect();
                $paidCount = $empLeads->filter(fn ($l) => $paidAppNos->contains((string) $l->applino))->count();

                $convRate = $leadsCount > 0 ? round(($paidCount / $leadsCount) * 100, 1) : 0;

                return (object) [
                    'emp_code'        => $empCode,
                    'name'            => $emp->full_name,
                    'email'           => $emp->ofc_mailid ?: ($emp->personal_mailid ?: 'N/A'),
                    'phone'           => $emp->phone_primary ?: 'N/A',
                    'leads_count'     => $leadsCount,
                    'tasks_count'     => $tasksCount,
                    'paid_count'      => $paidCount,
                    'conversion_rate' => $convRate,
                    'status'          => $leadsCount > 0 ? 'Active' : 'Standby',
                ];
            });
        } catch (\Throwable $e) {}

        return [
            'totalTelecallerLeads' => $totalTelecallerLeads,
            'telecallerStats'      => $telecallerStats,
        ];
    }

    /**
     * Compute Raw Leads Acquisition & Pipeline Volume.
     */
    public function getLeadsKpiMetrics(): array
    {
        $totalRawLeads = 0;
        $allocatedLeads = 0;
        $unallocatedLeads = 0;

        try {
            $totalRawLeads = Lead::count();
            $allocatedLeads = Lead::allocated()->count();
            $unallocatedLeads = Lead::unallocated()->count();
        } catch (\Throwable $e) {}

        return [
            'totalRawLeads'    => $totalRawLeads,
            'allocatedLeads'   => $allocatedLeads,
            'unallocatedLeads' => $unallocatedLeads,
        ];
    }
}


