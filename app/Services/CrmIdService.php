<?php
namespace App\Services;

use App\Models\CrmStudentRegistration;
use App\Models\Program;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CrmIdService
{
    /**
     * Map program / degree level to CRM ID prefix.
     * B.Tech -> V-SAT
     * M.Tech -> MTECH
     * PhD    -> PHD
     * MBA    -> MBA
     * MCA    -> MCA
     * Diploma-> DIPLOMA
     * etc.
     */
    public static function getPrefix(?string $progShort, ?string $progLevel = null, ?string $progLong = null): string
    {
        $short = strtoupper(trim((string) $progShort));
        $level = strtoupper(trim((string) $progLevel));
        $long = strtoupper(trim((string) $progLong));

        // 1. B.Tech / VSAT
        if (str_contains($short, 'B.TECH') || str_contains($long, 'BACHELOR OF TECHNOLOGY') || str_contains($short, 'VSAT') || ($level === 'UG' && str_contains($short, 'TECH'))) {
            return 'V-SAT';
        }

        // 2. M.Tech
        if (str_contains($short, 'M.TECH') || str_contains($long, 'MASTER OF TECHNOLOGY')) {
            return 'MTECH';
        }

        // 3. PhD / Doctoral
        if (str_contains($short, 'PHD') || str_contains($short, 'PH.D') || str_contains($level, 'PHD') || str_contains($level, 'DOCTORAL')) {
            return 'PHD';
        }

        // 4. MBA
        if (str_contains($short, 'MBA')) {
            return 'MBA';
        }

        // 5. MCA
        if (str_contains($short, 'MCA')) {
            return 'MCA';
        }

        // 6. Diploma
        if (str_contains($short, 'DIPLOMA') || str_contains($level, 'DIPLOMA')) {
            return 'DIPLOMA';
        }

        // 7. Pharmacy
        if (str_contains($short, 'B.PHARM') || str_contains($short, 'BPHARM')) {
            return 'BPHARM';
        }
        if (str_contains($short, 'M.PHARM') || str_contains($short, 'MPHARM')) {
            return 'MPHARM';
        }
        if (str_contains($short, 'PHARM.D')) {
            return 'PHARMD';
        }

        // 8. Management & IT
        if (str_contains($short, 'BBA') || str_contains($short, 'BBM')) {
            return 'BBA';
        }
        if (str_contains($short, 'BCA')) {
            return 'BCA';
        }

        // 9. Sciences & Commerce
        if (str_contains($short, 'B.SC') || str_contains($short, 'BSC')) {
            return 'BSC';
        }
        if (str_contains($short, 'M.SC') || str_contains($short, 'MSC')) {
            return 'MSC';
        }
        if (str_contains($short, 'B.COM')) {
            return 'BCOM';
        }

        // 10. Law & Humanities
        if (str_contains($short, 'LLB') || str_contains($long, 'LAWS')) {
            return 'LAW';
        }
        if (str_contains($short, 'M.A') || str_contains($short, 'B.A')) {
            return 'ARTS';
        }

        $clean = preg_replace('/[^A-Z]/', '', $short);
        return !empty($clean) ? $clean : 'CRM';
    }

    /**
     * Generate or retrieve the sequential CRM ID for a student registration.
     * E.g. V-SAT-0001, MTECH-0001, PHD-0001.
     */
    public static function generateForStudent(CrmStudentRegistration $student): string
    {
        // If student already has a valid format crmid, return it
        if (!empty($student->crmid) && str_contains($student->crmid, '-')) {
            return $student->crmid;
        }

        $program = $student->program;
        $prefix = self::getPrefix(
            $program?->program_short_name ?? $student->programlevel,
            $student->programlevel,
            $program?->program_long_name
        );

        // Find the maximum existing sequence number for this prefix
        $pattern = "^{$prefix}-[0-9]+$";
        $maxNum = 0;

        try {
            $rows = CrmStudentRegistration::whereNotNull('crmid')
                ->where('crmid', '~', $pattern)
                ->pluck('crmid');

            foreach ($rows as $cid) {
                $parts = explode('-', $cid);
                $num = (int) end($parts);
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        } catch (\Throwable $e) {}

        $nextNum = $maxNum + 1;
        $crmId = sprintf('%s-%04d', $prefix, $nextNum);

        // Save in student registration record via Eloquent
        try {
            $student->update(['crmid' => $crmId]);
        } catch (\Throwable $e) {
            Log::error("[CrmIdService] Error saving crmid: {$e->getMessage()}");
        }

        return $crmId;
    }

    /**
     * Get or generate CRM ID by application number.
     */
    public static function getOrGenerateByAppli(string $applinano): string
    {
        $student = CrmStudentRegistration::with('program')->where('applinano', $applinano)->first();
        if ($student) {
            return self::generateForStudent($student);
        }

        return 'V-SAT-0001';
    }

    /**
     * Find student by application number or CRM ID.
     */
    public static function findStudentByAppliOrCrmId(string $identifier): ?CrmStudentRegistration
    {
        $id = trim($identifier);
        return CrmStudentRegistration::where('applinano', $id)
            ->orWhere('crmid', $id)
            ->orWhere('phoneno', $id)
            ->first();
    }
}

