<?php

namespace App\Services;

use App\Models\School;
use App\Models\Department;
use App\Models\Program;
use App\Models\Regulation;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CivilServiceEnrollment;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheService
{
    /**
     * Standard Cache TTL Constants (in seconds)
     */
    public const TTL_24_HOURS = 86400; // 24 Hours
    public const TTL_2_HOURS  = 7200;  // 2 Hours
    public const TTL_1_HOUR   = 3600;  // 1 Hour
    public const TTL_15_MINS  = 900;   // 15 Minutes
    public const TTL_10_MINS  = 600;   // 10 Minutes

    /**
     * Cache Keys
     */
    public const KEY_SCHOOLS = 'lms_master_schools';
    public const KEY_DEPARTMENTS = 'lms_master_departments';
    public const KEY_DEPARTMENTS_WITH_SCHOOL = 'lms_master_departments_with_school';
    public const KEY_PROGRAMS = 'lms_master_programs';
    public const KEY_REGULATIONS = 'lms_master_regulations';
    public const KEY_SUPER_ADMIN_STATS = 'lms_super_admin_dashboard_stats';
    public const KEY_CIVIL_SERVICES_STATS = 'lms_civil_services_dashboard_stats';
    public const KEY_SSH_ADMIN_STATS = 'lms_ssh_admin_dashboard_stats';

    // -------------------------------------------------------------
    // 1. SCHOOLS MASTER LIST (24 Hours)
    // -------------------------------------------------------------
    public static function getSchools()
    {
        return Cache::remember(self::KEY_SCHOOLS, self::TTL_24_HOURS, function () {
            return School::all();
        });
    }

    public static function invalidateSchools(): void
    {
        Cache::forget(self::KEY_SCHOOLS);
        Cache::forget(self::KEY_DEPARTMENTS_WITH_SCHOOL);
    }

    // -------------------------------------------------------------
    // 2. DEPARTMENTS MASTER LIST (24 Hours)
    // -------------------------------------------------------------
    public static function getDepartments()
    {
        return Cache::remember(self::KEY_DEPARTMENTS, self::TTL_24_HOURS, function () {
            return Department::all();
        });
    }

    public static function getDepartmentsWithSchool()
    {
        return Cache::remember(self::KEY_DEPARTMENTS_WITH_SCHOOL, self::TTL_24_HOURS, function () {
            return Department::with('school')->get();
        });
    }

    public static function invalidateDepartments(): void
    {
        Cache::forget(self::KEY_DEPARTMENTS);
        Cache::forget(self::KEY_DEPARTMENTS_WITH_SCHOOL);
    }

    // -------------------------------------------------------------
    // 3. DEPARTMENT PROGRAMS (24 Hours)
    // -------------------------------------------------------------
    public static function getPrograms()
    {
        return Cache::remember(self::KEY_PROGRAMS, self::TTL_24_HOURS, function () {
            return Program::with('department')->get();
        });
    }

    public static function getProgramsByDepartment($departmentId)
    {
        $key = 'lms_programs_dept_' . $departmentId;
        return Cache::remember($key, self::TTL_24_HOURS, function () use ($departmentId) {
            return Program::where('department_id', $departmentId)->get();
        });
    }

    public static function invalidatePrograms(): void
    {
        Cache::forget(self::KEY_PROGRAMS);
    }

    // -------------------------------------------------------------
    // 4. ACADEMIC REGULATIONS (24 Hours)
    // -------------------------------------------------------------
    public static function getRegulations()
    {
        return Cache::remember(self::KEY_REGULATIONS, self::TTL_24_HOURS, function () {
            return Regulation::orderBy('created_at', 'desc')->get();
        });
    }

    public static function invalidateRegulations(): void
    {
        Cache::forget(self::KEY_REGULATIONS);
    }

    // -------------------------------------------------------------
    // 5. SUPER ADMIN DASHBOARD STATS (10 Minutes)
    // -------------------------------------------------------------
    public static function getSuperAdminDashboardStats()
    {
        return Cache::remember(self::KEY_SUPER_ADMIN_STATS, self::TTL_10_MINS, function () {
            return [
                'totalUsers' => User::count(),
                'totalCoordinators' => User::role('admin')->count(),
                'coordinators' => User::role('admin')->with('profile.school', 'profile.department')->latest()->take(6)->get(),
                'totalStaff' => User::role('sta')->count(),
                'recentStaff' => User::role('sta')->with('profile.school', 'profile.department')->latest()->take(5)->get(),
                'totalStudents' => User::role('stu')->count(),
                'recentStudents' => User::role('stu')->with('profile.school', 'profile.department')->latest()->take(5)->get(),
                'totalCivil' => CivilServiceEnrollment::count(),
                'totalCourses' => Course::count(),
                'totalDepartments' => Department::count(),
            ];
        });
    }

    public static function invalidateSuperAdminStats(): void
    {
        Cache::forget(self::KEY_SUPER_ADMIN_STATS);
    }

    // -------------------------------------------------------------
    // 6. COORDINATOR DASHBOARD STATS (10 Minutes)
    // -------------------------------------------------------------
    public static function getCoordinatorDashboardStats($deptCode, $schoolCode = null)
    {
        $key = 'lms_coordinator_stats_' . ($deptCode ?: 'nodept') . '_' . ($schoolCode ?: 'noschool');
        return Cache::remember($key, self::TTL_10_MINS, function () use ($deptCode, $schoolCode) {
            $studentCount = Profile::where('roles_id', 'stu')
                ->where('departments_id', $deptCode)
                ->count();
            $facultyCount = Profile::where('roles_id', 'sta')
                ->where('departments_id', $deptCode)
                ->count();
            $coursesCount = Course::where('department_id', $deptCode)->count();

            return [
                'studentCount' => $studentCount,
                'facultyCount' => $facultyCount,
                'coursesCount' => $coursesCount,
            ];
        });
    }

    public static function invalidateCoordinatorStats($deptCode = null): void
    {
        if ($deptCode) {
            Cache::forget('lms_coordinator_stats_' . $deptCode . '_noschool');
        }
    }

    // -------------------------------------------------------------
    // 7. CIVIL SERVICES DASHBOARD STATS (15 Minutes)
    // -------------------------------------------------------------
    public static function getCivilServicesDashboardStats()
    {
        return Cache::remember(self::KEY_CIVIL_SERVICES_STATS, self::TTL_15_MINS, function () {
            $totalEnrolled = CivilServiceEnrollment::count();
            $activeEnrolled = CivilServiceEnrollment::where('status', 'active')->count();
            $recentEnrollments = User::role('stu')
                ->whereHas('civilServiceEnrollment')
                ->with(['profile.school', 'profile.department', 'civilServiceEnrollment'])
                ->latest()
                ->take(8)
                ->get();

            $departmentBreakdown = Profile::whereHas('user.civilServiceEnrollment')
                ->select('departments_id', DB::raw('count(*) as count'))
                ->groupBy('departments_id')
                ->with('department')
                ->get();

            return [
                'totalEnrolled' => $totalEnrolled,
                'activeEnrolled' => $activeEnrolled,
                'recentEnrollments' => $recentEnrollments,
                'departmentBreakdown' => $departmentBreakdown,
            ];
        });
    }

    public static function invalidateCivilServicesStats(): void
    {
        Cache::forget(self::KEY_CIVIL_SERVICES_STATS);
    }

    // -------------------------------------------------------------
    // 8. SSH / 1ST YEAR STATS (10 Minutes)
    // -------------------------------------------------------------
    public static function getSshAdminDashboardStats()
    {
        return Cache::remember(self::KEY_SSH_ADMIN_STATS, self::TTL_10_MINS, function () {
            $shDeptCodes = Department::where('school_id', 'sc_ash')
                ->pluck('code')
                ->merge(['dep_ssh', 'dep_phy', 'dep_chem', 'dep_maths', 'dep_eng'])
                ->unique()
                ->values()
                ->toArray();

            $currentYearShort = date('y');
            $prevYearShort = str_pad((int)$currentYearShort - 1, 2, '0', STR_PAD_LEFT);

            $firstYearsQuery = User::role('stu')->where(function ($q) use ($currentYearShort, $prevYearShort, $shDeptCodes) {
                $q->where('username', 'like', $currentYearShort . '%')
                  ->orWhere('username', 'like', $prevYearShort . '%')
                  ->orWhereHas('profile', function ($pq) use ($shDeptCodes) {
                      $pq->whereIn('departments_id', $shDeptCodes)
                         ->orWhere('level', 'UG');
                  });
            });

            $totalFirstYears = (clone $firstYearsQuery)->count();

            $branchBreakdown = DB::table('users')
                ->join('profiles', 'users.profile_id', '=', 'profiles.id')
                ->leftJoin('departments', 'profiles.departments_id', '=', 'departments.code')
                ->where('profiles.roles_id', 'stu')
                ->select('departments.name as department_name', 'profiles.departments_id', DB::raw('count(*) as student_count'))
                ->groupBy('departments.name', 'profiles.departments_id')
                ->orderByDesc('student_count')
                ->get();

            $shFaculty = User::role('sta')->whereHas('profile', function ($q) use ($shDeptCodes) {
                $q->whereIn('departments_id', $shDeptCodes)->orWhere('schools_id', 'sc_ash');
            })->with(['profile.department'])->get();

            $firstYearCourses = Course::where('year', 1)
                ->with(['regulation', 'department', 'staff.profile'])
                ->get();

            $recentFreshers = (clone $firstYearsQuery)->with(['profile.school', 'profile.department', 'profile.program'])->latest('id')->take(6)->get();

            return [
                'totalFirstYears' => $totalFirstYears,
                'branchBreakdown' => $branchBreakdown,
                'totalShFaculty' => $shFaculty->count(),
                'shFaculty' => $shFaculty,
                'firstYearCourses' => $firstYearCourses,
                'totalFirstYearCourses' => $firstYearCourses->count(),
                'allocatedCoursesCount' => $firstYearCourses->filter(fn($c) => $c->staff->count() > 0)->count(),
                'recentFreshers' => $recentFreshers,
            ];
        });
    }

    public static function invalidateSshAdminStats(): void
    {
        Cache::forget(self::KEY_SSH_ADMIN_STATS);
    }

    // -------------------------------------------------------------
    // 9. CURRICULUM COURSES LIST (2 Hours)
    // -------------------------------------------------------------
    public static function getDepartmentCourses($departmentCode, $year = null, $semester = null)
    {
        $key = 'lms_dept_courses_' . $departmentCode . '_y' . ($year ?? 'all') . '_s' . ($semester ?? 'all');
        return Cache::remember($key, self::TTL_2_HOURS, function () use ($departmentCode, $year, $semester) {
            $query = Course::where('department_id', $departmentCode)->with(['regulation', 'department', 'staff']);
            if ($year) {
                $query->where('year', $year);
            }
            if ($semester) {
                $query->where('semester', $semester);
            }
            return $query->get();
        });
    }

    public static function invalidateCourses($departmentCode = null): void
    {
        if ($departmentCode) {
            for ($y = 1; $y <= 4; $y++) {
                for ($s = 1; $s <= 2; $s++) {
                    Cache::forget('lms_dept_courses_' . $departmentCode . '_y' . $y . '_s' . $s);
                }
                Cache::forget('lms_dept_courses_' . $departmentCode . '_y' . $y . '_sall');
            }
            Cache::forget('lms_dept_courses_' . $departmentCode . '_yall_sall');
        }
        self::invalidateSuperAdminStats();
        self::invalidateSshAdminStats();
    }

    // -------------------------------------------------------------
    // 10. FACULTY COURSE ALLOCATIONS (2 Hours)
    // -------------------------------------------------------------
    public static function getCourseAllocations($courseId)
    {
        $key = 'lms_course_allocations_' . $courseId;
        return Cache::remember($key, self::TTL_2_HOURS, function () use ($courseId) {
            $course = Course::with('staff.profile')->find($courseId);
            return $course ? $course->staff : collect();
        });
    }

    public static function invalidateCourseAllocations($courseId = null): void
    {
        if ($courseId) {
            Cache::forget('lms_course_allocations_' . $courseId);
        }
        self::invalidateSshAdminStats();
    }

    // -------------------------------------------------------------
    // 11. COURSE STUDY MATERIALS (1 Hour)
    // -------------------------------------------------------------
    public static function getCourseMaterials($courseId)
    {
        $key = 'lms_course_materials_' . $courseId;
        return Cache::remember($key, self::TTL_1_HOUR, function () use ($courseId) {
            return CourseMaterial::with(['staff.profile'])->where('course_id', $courseId)->latest()->get();
        });
    }

    public static function invalidateCourseMaterials($courseId): void
    {
        Cache::forget('lms_course_materials_' . $courseId);
    }

    // -------------------------------------------------------------
    // 12. COURSE SYLLABUS & OUTLINES (2 Hours)
    // -------------------------------------------------------------
    public static function getCourseSyllabus($courseId)
    {
        $key = 'lms_course_syllabus_' . $courseId;
        return Cache::remember($key, self::TTL_2_HOURS, function () use ($courseId) {
            return Course::with(['regulation'])->find($courseId);
        });
    }

    public static function invalidateCourseSyllabus($courseId): void
    {
        Cache::forget('lms_course_syllabus_' . $courseId);
    }

    // -------------------------------------------------------------
    // FLUSH ALL CACHES
    // -------------------------------------------------------------
    public static function flushAll(): void
    {
        self::invalidateSchools();
        self::invalidateDepartments();
        self::invalidatePrograms();
        self::invalidateRegulations();
        self::invalidateSuperAdminStats();
        self::invalidateCivilServicesStats();
        self::invalidateSshAdminStats();
    }
}

