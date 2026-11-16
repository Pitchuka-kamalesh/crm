<?php

namespace App\Common\Services;

use App\Models\CrmStudentRegistration;
use App\Models\Program;
use App\Models\StudentRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CrmIdService
{
    private static bool $columnEnsured = false;

    /**
     * Ensure the crmid column exists in crm.student_registration.
     */
    public static function ensureColumnExists(): void
    {
        if (self::$columnEnsured) {
            return;
        }

        try {
            DB::statement("ALTER TABLE crm.student_registration ADD COLUMN IF NOT EXISTS crmid VARCHAR(50);");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_crm_student_registration_crmid ON crm.student_registration (crmid);");
            self::$columnEnsured = true;
        } catch (\Throwable $e) {
            Log::warning('[CrmIdService] ensureColumnExists error: ' . $e->getMessage());
        }
    }

    /**
     * Map program, level, and branch to CRM ID prefix.
     * B.Tech -> V-SAT (e.g. V-SAT-0001)
     * M.Tech -> MTECH (e.g. MTECH-0001)
     * PhD    -> PHD   (e.g. PHD-0001)
     * MBA    -> MBA
     * MCA    -> MCA
     * B.Pharm-> BPHARM
     * M.Pharm-> MPHARM
     * BBA    -> BBA
     * BCA    -> BCA
     * B.Sc   -> BSC
     * M.Sc   -> MSC
     * B.Com  -> BCOM
     * Law    -> LAW
     * Arts   -> ARTS
     * Diploma-> DIPLOMA
     */
    public static function getPrefix(?string $progShort, ?string $progLevel = null, ?string $progLong = null, ?string $branchName = null): string
    {
        $short = strtoupper(trim((string) $progShort));
        $level = strtoupper(trim((string) $progLevel));
        $long = strtoupper(trim((string) $progLong));
        $branch = strtoupper(trim((string) $branchName));
        $combined = "{$short} {$level} {$long} {$branch}";

        // 1. Ph.D / Doctoral
        if (str_contains($combined, 'PHD') || str_contains($combined, 'PH.D') || str_contains($combined, 'DOCTORAL') || str_contains($level, 'PHD')) {
            return 'PHD';
        }

        // 2. M.Tech (Master of Technology)
        if (str_contains($combined, 'M.TECH') || str_contains($combined, 'MTECH') || str_contains($combined, 'MASTER OF TECHNOLOGY') || ($level === 'PG' && str_contains($combined, 'TECH'))) {
            return 'MTECH';
        }

        // 3. B.Tech (Bachelor of Technology) -> V-SAT
        if (str_contains($combined, 'B.TECH') || str_contains($combined, 'BTECH') || str_contains($combined, 'BACHELOR OF TECHNOLOGY') || str_contains($combined, 'VSAT') || ($level === 'UG' && str_contains($combined, 'TECH'))) {
            return 'V-SAT';
        }

        // 4. MBA
        if (str_contains($combined, 'MBA') || str_contains($combined, 'BUSINESS ADMIN')) {
            return 'MBA';
        }

        // 5. MCA
        if (str_contains($combined, 'MCA') || str_contains($combined, 'COMPUTER APPLICATION')) {
            return 'MCA';
        }

        // 6. Pharmacy
        if (str_contains($combined, 'M.PHARM') || str_contains($combined, 'MPHARM')) {
            return 'MPHARM';
        }
        if (str_contains($combined, 'B.PHARM') || str_contains($combined, 'BPHARM')) {
            return 'BPHARM';
        }
        if (str_contains($combined, 'PHARM.D') || str_contains($combined, 'PHARMD')) {
            return 'PHARMD';
        }

        // 7. Management & IT
        if (str_contains($combined, 'BBA') || str_contains($combined, 'BBM')) {
            return 'BBA';
        }
        if (str_contains($combined, 'BCA')) {
            return 'BCA';
        }

        // 8. Sciences & Commerce
        if (str_contains($combined, 'M.SC') || str_contains($combined, 'MSC')) {
            return 'MSC';
        }
        if (str_contains($combined, 'B.SC') || str_contains($combined, 'BSC')) {
            return 'BSC';
        }
        if (str_contains($combined, 'B.COM') || str_contains($combined, 'BCOM')) {
            return 'BCOM';
        }

        // 9. Law & Humanities
        if (str_contains($combined, 'LLB') || str_contains($combined, 'LAW')) {
            return 'LAW';
        }
        if (str_contains($combined, 'M.A') || str_contains($combined, 'B.A')) {
            return 'ARTS';
        }

        // 10. Diploma
        if (str_contains($combined, 'DIPLOMA')) {
            return 'DIPLOMA';
        }

        // Fallback for short program codes
        $clean = preg_replace('/[^A-Z]/', '', $short);
        return !empty($clean) ? $clean : 'V-SAT';
    }

    /**
     * Synchronize and populate unique branch-wise sequential CRM IDs for all existing student registrations.
     * Ensures B.Tech -> V-SAT-0001, V-SAT-0002..., M.Tech -> MTECH-0001..., PhD -> PHD-0001...
     */
    public static function syncAllStudentCrmIds(): void
    {
        self::ensureColumnExists();

        try {
            $students = CrmStudentRegistration::with(['program', 'branch'])
                ->orderBy('log_timestamp', 'asc')
                ->orderBy('applinano', 'asc')
                ->get();

            if ($students->isEmpty()) {
                return;
            }

            $counters = [];
            $assignedIds = [];

            foreach ($students as $student) {
                $program = $student->program;
                $branch = $student->branch;
                $prefix = self::getPrefix(
                    $program?->program_short_name ?? $student->programlevel,
                    $student->programlevel,
                    $program?->program_long_name,
                    $branch?->branch_short_name ?? $branch?->branch_long_name
                );

                if (!isset($counters[$prefix])) {
                    $counters[$prefix] = 0;
                }

                $currentCrmId = trim((string) $student->crmid);

                // Reassign if crmid is missing, doesn't match this prefix, or is a duplicate
                $isAlreadyValidAndUnique = !empty($currentCrmId)
                    && str_starts_with($currentCrmId, "{$prefix}-")
                    && !isset($assignedIds[$currentCrmId]);

                if ($isAlreadyValidAndUnique) {
                    $parts = explode('-', $currentCrmId);
                    $num = (int) end($parts);
                    if ($num > $counters[$prefix]) {
                        $counters[$prefix] = $num;
                    }
                    $assignedIds[$currentCrmId] = true;
                } else {
                    $counters[$prefix]++;
                    $newCrmId = sprintf('%s-%04d', $prefix, $counters[$prefix]);
                    $assignedIds[$newCrmId] = true;

                    if ($student->crmid !== $newCrmId) {
                        CrmStudentRegistration::where('applinano', $student->applinano)
                            ->update(['crmid' => $newCrmId]);
                        $student->crmid = $newCrmId;
                    }
                }

                // Sync each student's CRM ID to academic details, personal info, leads, coupons
                if (!empty($student->crmid)) {
                    self::syncRelatedTables($student->applinano, $student->crmid);
                }
            }

            // Clean up any remaining legacy CRMA_ or CRMP_ IDs in related tables
            self::fixAllLegacyCrmIds();
        } catch (\Throwable $e) {
            Log::error('[CrmIdService] syncAllStudentCrmIds error: ' . $e->getMessage());
        }
    }

    /**
     * Generate or retrieve the sequential CRM ID for a student registration.
     * E.g. V-SAT-0001, MTECH-0001, PHD-0001.
     */
    public static function generateForStudent(CrmStudentRegistration $student): string
    {
        self::ensureColumnExists();

        $program = $student->program;
        $branch = $student->branch;
        $prefix = self::getPrefix(
            $program?->program_short_name ?? $student->programlevel,
            $student->programlevel,
            $program?->program_long_name,
            $branch?->branch_short_name ?? $branch?->branch_long_name
        );

        // If student already has a valid format crmid matching prefix and not duplicate
        if (!empty($student->crmid) && str_starts_with($student->crmid, "{$prefix}-")) {
            try {
                $duplicateCount = CrmStudentRegistration::where('crmid', $student->crmid)
                    ->where('applinano', '!=', $student->applinano)
                    ->count();

                if ($duplicateCount === 0) {
                    return $student->crmid;
                }
            } catch (\Throwable $e) {}
        }

        // Find the maximum existing sequence number for this prefix
        $maxNum = 0;
        try {
            $existingCrmIds = CrmStudentRegistration::whereNotNull('crmid')
                ->where('crmid', 'like', "{$prefix}-%")
                ->pluck('crmid');

            foreach ($existingCrmIds as $cid) {
                $parts = explode('-', $cid);
                $num = (int) end($parts);
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        } catch (\Throwable $e) {}

        $nextNum = $maxNum + 1;
        $crmId = sprintf('%s-%04d', $prefix, $nextNum);

        // Save in student registration record via Eloquent update
        try {
            CrmStudentRegistration::where('applinano', $student->applinano)
                ->update(['crmid' => $crmId]);
            $student->crmid = $crmId;
            self::syncRelatedTables($student->applinano, $crmId);
        } catch (\Throwable $e) {
            Log::error("[CrmIdService] Error saving crmid: {$e->getMessage()}");
        }

        return $crmId;
    }

    /**
     * Synchronize the student's CRM ID (e.g. V-SAT-0001) across all database tables.
     */
    public static function syncRelatedTables(string $applino, string $crmId): void
    {
        if (empty($applino) || empty($crmId)) {
            return;
        }

        // 1. Sync crm.student_academic_details
        try {
            StudentAcademicDetail::where('applino', $applino)
                ->update(['crmid' => $crmId]);
        } catch (\Throwable $e) {}

        // 2. Sync crm.student_personal_info
        try {
            StudentPersonalInfo::where('applino', $applino)
                ->update(['crmid' => $crmId]);
        } catch (\Throwable $e) {}

        // 3. Sync crm.telecaller_leads
        try {
            TelecallerLead::where('applino', $applino)
                ->update(['crmid' => $crmId]);
        } catch (\Throwable $e) {}

        // 4. Sync crm.telecaller_leads_tasks
        try {
            TelecallerLeadTask::where('applino', $applino)
                ->update(['crmid' => $crmId]);
        } catch (\Throwable $e) {}

        // 5. Sync finance.couponinfo
        try {
            \Modules\Finance\Models\Coupous::where('applino', $applino)
                ->update(['crmid' => $crmId]);
        } catch (\Throwable $e) {}
    }

    /**
     * Clean up and fix any legacy CRMA_ or CRMP_ or out-of-sync crmid values across all tables.
     */
    public static function fixAllLegacyCrmIds(): void
    {
        try {
            // Fix crm.student_academic_details
            DB::statement("
                UPDATE crm.student_academic_details cad
                SET crmid = sr.crmid
                FROM crm.student_registration sr
                WHERE cad.applino = sr.applinano
                  AND sr.crmid IS NOT NULL
                  AND (cad.crmid IS NULL OR cad.crmid LIKE 'CRMA_%' OR cad.crmid LIKE 'CRMP_%' OR cad.crmid != sr.crmid);
            ");

            // Fix crm.student_personal_info
            DB::statement("
                UPDATE crm.student_personal_info cpi
                SET crmid = sr.crmid
                FROM crm.student_registration sr
                WHERE cpi.applino = sr.applinano
                  AND sr.crmid IS NOT NULL
                  AND (cpi.crmid IS NULL OR cpi.crmid LIKE 'CRMA_%' OR cpi.crmid LIKE 'CRMP_%' OR cpi.crmid != sr.crmid);
            ");

            // Fix crm.telecaller_leads
            DB::statement("
                UPDATE crm.telecaller_leads tl
                SET crmid = sr.crmid
                FROM crm.student_registration sr
                WHERE tl.applino = sr.applinano
                  AND sr.crmid IS NOT NULL
                  AND (tl.crmid IS NULL OR tl.crmid = '0' OR tl.crmid != sr.crmid);
            ");

            // Fix finance.couponinfo
            DB::statement("
                UPDATE finance.couponinfo ci
                SET crmid = sr.crmid
                FROM crm.student_registration sr
                WHERE ci.applino = sr.applinano
                  AND sr.crmid IS NOT NULL
                  AND (ci.crmid IS NULL OR ci.crmid != sr.crmid);
            ");
        } catch (\Throwable $e) {
            Log::warning('[CrmIdService] fixAllLegacyCrmIds notice: ' . $e->getMessage());
        }
    }

    /**
     * Get or generate CRM ID by application number.
     */
    public static function getOrGenerateByAppli(string $applinano): string
    {
        self::ensureColumnExists();

        try {
            $student = CrmStudentRegistration::with(['program', 'branch'])->where('applinano', $applinano)->first();
            if ($student) {
                return self::generateForStudent($student);
            }

            // Fallback for admissions table if not in CRM table
            $adm = StudentRegistration::where('application_no', $applinano)->first();
            if ($adm) {
                $prefix = self::getPrefix($adm->degree_level ?? $adm->program_name);
                return sprintf('%s-%04d', $prefix, 1);
            }
        } catch (\Throwable $e) {
            Log::warning('[CrmIdService] getOrGenerateByAppli error: ' . $e->getMessage());
        }

        return 'V-SAT-0001';
    }

    /**
     * Helper to find a student by either application number or CRM ID.
     */
    public static function findStudentByAppliOrCrmId(string $identifier): ?CrmStudentRegistration
    {
        self::ensureColumnExists();

        $identifier = trim($identifier);
        if (empty($identifier)) {
            return null;
        }

        try {
            return CrmStudentRegistration::with(['program', 'branch'])
                ->where('applinano', $identifier)
                ->orWhere('crmid', $identifier)
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
