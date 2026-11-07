<?php

namespace Modules\Sms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CommunicationLog;
use App\Services\SmsService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmsController extends Controller
{
    /**
     * Display SMS & WhatsApp communication tracker dashboard.
     */
    public function index(Request $request): View
    {
        $channel = $request->input('channel');
        $status = $request->input('status');
        $search = $request->input('search');
        $timeFilter = $request->input('time', 'all');

        $query = CommunicationLog::query()->latest('id');

        if ($timeFilter === 'today') {
            $query->whereDate('created_at', today());
        }

        if (!empty($channel) && $channel !== 'all') {
            $query->where('channel', $channel);
        }

        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('wapptophone', 'like', "%{$search}%")
                    ->orWhere('wauser', 'like', "%{$search}%")
                    ->orWhere('waregisterno', 'like', "%{$search}%")
                    ->orWhere('jiotraceid', 'like', "%{$search}%");
            });
        }

        // 10 records per page so pagination is immediately accessible and visible
        $logs = $query->paginate(10)->withQueryString();

        // Metrics for summary cards (with Today metrics)
        $totalLogs = CommunicationLog::count();
        $totalWhatsapp = CommunicationLog::where('channel', 'whatsapp')->count();
        $totalSms = CommunicationLog::where('channel', 'sms')->count();
        $totalSuccess = CommunicationLog::whereIn('status', ['SUBMITTED', 'DELIVERED', 'SENT'])->count();
        $totalFailed = CommunicationLog::where(function ($q) {
            $q->where('status', 'FAILED')
                ->orWhere('status', 'like', 'HTTP%')
                ->orWhere('status', 'like', '%EXCEPTION%');
        })->count();

        // Today Reports metrics
        $todayDispatched = CommunicationLog::whereDate('created_at', today())->count();
        $todaySuccess = CommunicationLog::whereDate('created_at', today())->whereIn('status', ['SUBMITTED', 'DELIVERED', 'SENT'])->count();
        $todayFailed = CommunicationLog::whereDate('created_at', today())->where(function($q) {
            $q->where('status', 'FAILED')->orWhere('status', 'like', 'HTTP%')->orWhere('status', 'like', '%EXCEPTION%');
        })->count();

        $user = auth()->user();
        $roleId = $user ? $user->getActiveRoleId() : 4;
        $roleName = $user ? $user->role_label : 'Tele Caller';

        return view('sms::index', compact(
            'logs',
            'totalLogs',
            'totalWhatsapp',
            'totalSms',
            'totalSuccess',
            'totalFailed',
            'todayDispatched',
            'todaySuccess',
            'todayFailed',
            'channel',
            'status',
            'search',
            'timeFilter',
            'user',
            'roleId',
            'roleName'
        ));
    }

    /**
     * Send test WhatsApp message (OTP or Admission campaign).
     */
    public function sendTestWhatsApp(Request $request, WhatsAppService $whatsAppService): RedirectResponse
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'campaign_type' => ['required', 'string', 'in:otp,admission'],
            'test_name' => ['nullable', 'string'],
            'test_program' => ['nullable', 'string'],
        ]);

        $phone = $request->input('phone');
        $type = $request->input('campaign_type');

        if ($type === 'otp') {
            $testOtp = (string) mt_rand(100000, 999999);
            $res = $whatsAppService->sendOtp(
                phone: $phone,
                otp: $testOtp,
                user: 'Test User (' . ($request->user()?->name ?? 'Admin') . ')',
                registerNo: 'TEST_REG',
                ip: $request->ip()
            );
        } else {
            $res = $whatsAppService->sendAdmissionInfo(
                phone: $phone,
                candidateName: $request->input('test_name') ?: 'Prospective Student',
                applicationId: '2026' . mt_rand(100000, 999999),
                programName: $request->input('test_program') ?: 'Master of Business Administration (MBA)',
                contactInfo: 'admissions@vignan.ac.in / 0863-2344700',
                user: $request->input('test_name') ?: 'Prospective Student',
                registerNo: 'APP-' . mt_rand(1000, 9999),
                ip: $request->ip()
            );
        }

        if ($res['success'] ?? false) {
            return redirect()->route('sms.index')
                ->with('status', "WhatsApp message dispatched successfully to +91 {$phone} (Trace ID: " . ($res['trace_id'] ?? 'N/A') . ')');
        }

        return redirect()->route('sms.index')
            ->with('error', "WhatsApp dispatch returned status: " . ($res['status'] ?? 'FAILED'));
    }

    /**
     * Send test SMS (Admission DLT template 1777179354065677511 or OTP).
     */
    public function sendTestSms(Request $request, SmsService $smsService): RedirectResponse
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'sms_type' => ['required', 'string', 'in:admission,otp'],
            'student_name' => ['nullable', 'string'],
            'program_name' => ['nullable', 'string'],
            'branch_name' => ['nullable', 'string'],
        ]);

        $phone = $request->input('phone');
        $type = $request->input('sms_type');

        if ($type === 'admission') {
            $res = $smsService->sendAdmissionSms(
                phone: $phone,
                name: $request->input('student_name') ?: 'Student Candidate',
                program: $request->input('program_name') ?: 'B.Tech / MBA',
                branch: $request->input('branch_name') ?: 'Computer Science',
                campus: 'Vadlamudi (Guntur)',
                applicationNo: '2026' . mt_rand(100000, 999999)
            );
        } else {
            $testOtp = (string) mt_rand(100000, 999999);
            $res = $smsService->sendOtp($phone, $testOtp);
        }

        if ($res['success'] ?? false) {
            return redirect()->route('sms.index')
                ->with('status', "SMS dispatched successfully to +91 {$phone}");
        }

        return redirect()->route('sms.index')
            ->with('error', "SMS dispatch returned status: " . ($res['status'] ?? 'FAILED'));
    }

    /**
     * Get real-time status of a communication log by ID.
     */
    public function show($id): JsonResponse
    {
        $log = CommunicationLog::findOrFail($id);

        return response()->json([
            'success' => true,
            'log' => $log,
        ]);
    }
}
