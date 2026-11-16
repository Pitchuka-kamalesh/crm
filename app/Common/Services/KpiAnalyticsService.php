<?php

namespace App\Common\Services;

use App\Models\CrmStudentRegistration;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\PayDetail;
use App\Models\ReceiptDetail;
use App\Models\TelecallerLead;
use App\Models\TelecallerLeadTask;
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
     */
    public function getStudentKpiMetrics(): array
    {
        $paidAppNos = $this->getVerifiedPaidAppNumbers();
        $totalStudents = CrmStudentRegistration::count();
        $kpiTotal = max(1, $totalStudents);

        // High-performance Eloquent count aggregations
        $kpiStage1Paid = $paidAppNos->count() ?: CrmStudentRegistration::where('stage1', 'paid')->count();

        $kpiStage2Done = CrmStudentRegistration::whereIn(DB::raw('LOWER(TRIM(stage2))'), ['completed', 'approved', 'done'])->count();
        $kpiStage3Done = CrmStudentRegistration::whereIn(DB::raw('LOWER(TRIM(stage3))'), ['completed', 'approved', 'done'])->count();
        $kpiStage4Done = CrmStudentRegistration::whereIn(DB::raw('LOWER(TRIM(stage4))'), ['completed', 'approved', 'done'])->count();
        $kpiStage5Done = CrmStudentRegistration::whereIn(DB::raw('LOWER(TRIM(stage5))'), ['completed', 'approved', 'done'])->count();

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
            'kpiStage1Paid'         => $kpiStage1Paid,
            'kpiStage2Done'         => $kpiStage2Done,
            'kpiStage3Done'         => $kpiStage3Done,
            'kpiStage4Done'         => $kpiStage4Done,
            'kpiStage5Done'         => $kpiStage5Done,
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

