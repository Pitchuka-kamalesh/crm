<?php

namespace Modules\Dashboards\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Campus;
use App\Models\CrmStudentRegistration;
use App\Models\ExamSchedule;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamController extends Controller
{
    /**
     * Display the Exams Slot Data management table (matching master.menus: EXAM00003).
     */
    public function slotData(Request $request): View
    {
        $user = Auth::user();
        $roleId = $user ? (int) $user->getActiveRoleId() : 2;
        $roleName = $user ? $user->role_label : 'CRM Official';

        // Load all exam slot bookings from finance.exam_schedule with student associations
        $schedules = collect();
        try {
            $schedules = ExamSchedule::with(['student.program', 'student.branch', 'personalInfo'])
                ->orderBy('logdatetime', 'desc')
                ->get();
        } catch (\Throwable $e) {
            // Fallback via Eloquent Model
            try {
                $schedules = ExamSchedule::orderBy('logdatetime', 'desc')->get();
            } catch (\Throwable $e2) {}
        }

        // Summary Metric Counters
        $totalBookings = $schedules->count();
        $slot1Count = $schedules->filter(function ($s) {
            $slot = strtoupper((string) $s->exam_slot);
            return str_contains($slot, 'S1') || str_contains($slot, '11');
        })->count();

        $slot2Count = $schedules->filter(function ($s) {
            $slot = strtoupper((string) $s->exam_slot);
            return str_contains($slot, 'S2') || str_contains($slot, '2:00') || str_contains($slot, '2 PM');
        })->count();

        $confirmedCount = $schedules->filter(function ($s) {
            return (int) $s->exam_status === 1 || empty($s->exam_status);
        })->count();

        $todayDate = Carbon::today()->format('Y-m-d');
        $todayBookings = $schedules->filter(function ($s) use ($todayDate) {
            return !empty($s->examdate) && Carbon::parse($s->examdate)->format('Y-m-d') === $todayDate;
        })->count();

        $distinctDates = $schedules->pluck('examdate')->filter()->unique()->values();

        $viewName = view()->exists('dashboards::exam.examslotdata') ? 'dashboards::exam.examslotdata' : 'exam.examslotdata';

        return view($viewName, compact(
            'user',
            'roleId',
            'roleName',
            'schedules',
            'totalBookings',
            'slot1Count',
            'slot2Count',
            'confirmedCount',
            'todayBookings',
            'distinctDates'
        ));
    }
}

