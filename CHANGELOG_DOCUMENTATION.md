# VFSTR CRM & V-SAT Admissions Portal - Implementation & Changelog Documentation

This document contains a comprehensive record of the enhancements, bug fixes, database queries, and architectural updates implemented across the portal. Each section includes the exact file path, previous (old) code, updated (new) code, and associated database queries.

---

## Table of Contents
1. [Database Schema & Menu Configurations](#1-database-schema--menu-configurations)
2. [TotalDetails Directory & Comprehensive Excel Export (`/exam/TotalDetails`)](#2-totaldetails-directory--comprehensive-excel-export-examtotaldetails)
3. [Exams Slot Data Enhancements (`/exam/examslotdata`)](#3-exams-slot-data-enhancements-examexamslotdata)
4. [Exam Slot Notice & PhD Slot Exemption ("Exam is at university...")](#4-exam-slot-notice--phd-slot-exemption)
5. [CRM Tracking & PhD Academic Particulars (Postgraduate Details)](#5-crm-tracking--phd-academic-particulars-postgraduate-details)
6. [Mobile Responsive Layout & Navigation Boundary Fixes](#6-mobile-responsive-layout--navigation-boundary-fixes)
7. [Application Dossier Form: Partial Previews for Pending Applicants (`/application/preview`)](#7-application-dossier-form-partial-previews-for-pending-applicants-applicationpreview)
8. [Automated Notification Streamlining (4 WhatsApp + 2 SMS Rule)](#8-automated-notification-streamlining-4-whatsapp--2-sms-rule)
9. [CRM Student Search Stage Columns & Button Clean-up](#9-crm-student-search-stage-columns--button-clean-up)

---

## 1. Database Schema & Menu Configurations

### A. TotalDetails Menu Entries (`EXAM00004`)
Adds the `TotalDetails` menu under the `Exams` parent menu (`parent_menu_id = 125`) and grants access to CRM Incharge (`role_id = 2`).

```sql
-- 1. Insert Menu Entry in master.menus
INSERT INTO master.menus
(menu_id, menu_code, module_id, menu_name, controller_ref, icon_name, is_collapsible, lock_status, project_id, campus_id, log_userid, log_ipaddress, log_timestamp, parent_menu_id)
VALUES
(130, 'EXAM00004', 1, 'TotalDetails', 'exam/TotalDetails', 'fa fa-user-plus', true, 0, 1, '1', '02598', '192.168.111.111', CURRENT_TIMESTAMP, 125)
ON CONFLICT (menu_id) DO UPDATE SET 
    controller_ref = EXCLUDED.controller_ref,
    menu_name = EXCLUDED.menu_name;

-- 2. Assign Menu Access to Role 2 (CRM Incharge)
INSERT INTO master.menu_access_roles
(menu_code, menu_id, role_id, campus_id, morder, mposition, log_userid, log_ipaddress, log_timestamp)
VALUES
('EXAM00004', 130, 2, '1', 18, 2, '02598', '192.168.111.111', CURRENT_TIMESTAMP)
ON CONFLICT DO NOTHING;
```

---

### B. Leads Upload Dedicated Database Table & View
Creates the dedicated audit and tracking table `crm.leads_upload` and its alias view `crm.leads_uploads`.

```sql
CREATE SCHEMA IF NOT EXISTS crm;

CREATE TABLE IF NOT EXISTS crm.leads_upload (
    id BIGSERIAL PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_size VARCHAR(50),
    total_records INTEGER DEFAULT 0,
    new_leads_added INTEGER DEFAULT 0,
    duplicates_skipped INTEGER DEFAULT 0,
    upload_status VARCHAR(50) DEFAULT 'Completed',
    uploaded_by VARCHAR(50),
    upload_date_time TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE OR REPLACE VIEW crm.leads_uploads AS 
SELECT * FROM crm.leads_upload;
```

---

## 2. TotalDetails Directory & Comprehensive Excel Export (`/exam/TotalDetails`)

### A. Routing Setup
**File Location**: `crm/Modules/Dashboards/routes/web.php`

#### New Code Added:
```php
// TotalDetails Directory & Comprehensive Excel Export (menu: EXAM00004)
Route::middleware(['auth', 'role:1,2,3,4'])->group(function () {
    Route::get('/exam/TotalDetails', [ExamController::class, 'totalDetails'])->name('exam.totaldetails');
    Route::get('/exam/totaldetails', [ExamController::class, 'totalDetails']);
    Route::get('/exam/TotalDetails/export', [ExamController::class, 'exportTotalDetails'])->name('exam.totaldetails.export');
    Route::get('/exam/totaldetails/export', [ExamController::class, 'exportTotalDetails']);
});
```

---

### B. Menu Resolution & Active Route Matching
**File Location**: `crm/Modules/Auth/app/Services/MenuService.php`

#### Old Code:
```php
'exam/examslotdata', 'exam/index', 'examslotdata' => $safeRoute('exam.examslotdata'),
```
#### New Code:
```php
'exam/examslotdata', 'exam/index', 'examslotdata' => $safeRoute('exam.examslotdata'),
'exam/totaldetails', 'exam/TotalDetails', 'totaldetails' => $safeRoute('exam.totaldetails'),
```

#### Active Tab Matching (`isSubActive`):
```php
// Old Code:
'exam/examslotdata', 'exam/index' => request()->routeIs('exam.*') || str_contains($currentPath, 'exam'),

// New Code:
'exam/totaldetails', 'exam/TotalDetails', 'totaldetails' => request()->routeIs('exam.totaldetails*') || str_contains(strtolower($currentPath), 'totaldetails'),
'exam/examslotdata', 'exam/index' => (request()->routeIs('exam.*') && !request()->routeIs('exam.totaldetails*')) || (str_contains($currentPath, 'exam') && !str_contains(strtolower($currentPath), 'totaldetails')),
```

---

### C. Backend Controller (`totalDetails` & `exportTotalDetails`)
**File Location**: `crm/Modules/Dashboards/app/Http/Controllers/ExamController.php`

#### Key Functionality Implemented:
* `totalDetails(Request $request)`: Eager-loads all student relations (`personalInfo`, `academicDetails`, `payDetail`, `examSchedule`, `program`, `branch`, `campus`). Provides pagination and filters by search query, program level, inter group (`pu2_group`), fee status, and exam slot status.
* `exportTotalDetails(Request $request)`: Streams a UTF-8 BOM CSV/Excel file in chunks with **all 87+ columns** (Personal Profile, 10th academic, 12th/Inter academic, UG particulars, PG particulars, PayU transaction receipts, and Exam slot confirmations).

```php
public function totalDetails(Request $request): View
{
    $user = Auth::user();
    $roleId = $user ? (int) $user->getActiveRoleId() : 2;
    $roleName = $user ? $user->role_label : 'CRM Official';

    $query = CrmStudentRegistration::with([
        'personalInfo', 'academicDetails', 'payDetail', 'examSchedule',
        'program', 'branch', 'campus', 'country', 'state'
    ]);

    // Live search & filters (Program level, Inter Group, Fee Status, Slot status)
    if ($search = trim((string)$request->input('search'))) {
        $query->where(function ($q) use ($search) {
            $q->where('applinano', 'ilike', "%{$search}%")
              ->orWhere('crmid', 'ilike', "%{$search}%")
              ->orWhere('name', 'ilike', "%{$search}%")
              ->orWhere('phoneno', 'like', "%{$search}%")
              ->orWhere('emailid', 'ilike', "%{$search}%");
        });
    }

    $students = $query->paginate(25)->withQueryString();
    $totalRegistered = CrmStudentRegistration::count();
    $totalPaid = CrmStudentRegistration::where(function ($q) {
        $q->where('stage4', 'paid')->orWhere('stage1', 'paid')->orWhere('std_status', 'paid');
    })->count();
    $totalExamBooked = ExamSchedule::count();
    $totalPending = max(0, $totalRegistered - $totalPaid);
    $programs = Program::orderBy('program_short_name')->get();
    $distinctGroups = DB::table('crm.student_academic_details')->whereNotNull('pu2_group')->where('pu2_group', '!=', '')->distinct()->pluck('pu2_group')->sort()->values();

    return view('dashboards::exam.totaldetails', compact(
        'user', 'roleId', 'roleName', 'students', 'totalRegistered', 'totalPaid', 'totalExamBooked', 'totalPending', 'programs', 'distinctGroups'
    ));
}
```

---

### D. Single-Line Metrics Strip, Horizontal Filter Bar & Export Icon Button
**File Location**: `crm/Modules/Dashboards/resources/views/exam/totaldetails.blade.php`

#### Features:
1. **Compact 1-Line Metric Strip**: Replaced bulky card boxes and emojis with a single horizontal status bar (`TOTAL REGISTERED: 15 | APPLICATION FEE PAID: 8 | EXAM SLOTS BOOKED: 4 | FEE PENDING: 7`).
2. **Horizontal Toolbar**: Constrained input widths (`115px`–`175px`) and unified component height to `32px` to prevent multi-line wrapping.
3. **Compact Export Icon Button**: Removed lengthy text and placed a green `[ ↓ ]` icon button directly beside the `Filter` / `Reset` buttons on line 1.

```html
<!-- Single-Line Compact Metrics -->
<div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:0.6rem 1.1rem; margin-bottom:0.75rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
    <div><span>Total Registered:</span> <strong>{{ number_format($totalRegistered) }}</strong></div>
    <div><span>Application Fee Paid:</span> <strong>{{ number_format($totalPaid) }}</strong></div>
    <div><span>Exam Slots Booked:</span> <strong>{{ number_format($totalExamBooked) }}</strong></div>
    <div><span>Fee Pending:</span> <strong>{{ number_format($totalPending) }}</strong></div>
</div>

<!-- Horizontal Filter Row with Export Icon -->
<form method="GET" action="{{ route('exam.totaldetails') }}" style="margin:0; width:100%;">
    <div class="total-details-toolbar">
        <div class="total-details-filters-group">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="App No, CRM ID, Name, Ph..." class="search-input-custom">
            <select name="programlevel" class="filter-select-custom" onchange="this.form.submit()">...</select>
            <select name="intergroup" class="filter-select-custom" onchange="this.form.submit()">...</select>
            <select name="feestatus" class="filter-select-custom" onchange="this.form.submit()">...</select>
            <select name="slotstatus" class="filter-select-custom" onchange="this.form.submit()">...</select>
            <button type="submit" class="btn">Filter</button>
            @if(request()->anyFilled(['search', 'programlevel', 'intergroup', 'feestatus', 'slotstatus']))
                <a href="{{ route('exam.totaldetails') }}" class="btn">✕ Reset</a>
            @endif
            <!-- Download Icon Button Only -->
            <a href="{{ route('exam.totaldetails.export', request()->query()) }}" class="btn-excel" style="height:32px; width:36px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:6px;" title="Export to Excel">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            </a>
        </div>
    </div>
</form>
```

---

## 3. Exams Slot Data Enhancements (`/exam/examslotdata`)

### A. Separation of Program and Branch into Two Distinct Columns
**File Location**: `crm/Modules/Dashboards/resources/views/exam/examslotdata.blade.php`

#### Old Code (Combined Column):
```html
<th style="padding: 0.75rem 0.85rem; white-space: nowrap;">Program &amp; Branch</th>
...
<td>{{ $progTitle }} ({{ $branchTitle }})</td>
```

#### New Code (Separated Columns):
```html
<th style="padding: 0.75rem 0.85rem; white-space: nowrap;">Program</th>
<th style="padding: 0.75rem 0.85rem; white-space: nowrap;">Branch</th>
...
<td style="padding: 0.75rem 0.85rem; white-space: nowrap; font-weight: 700; color: #0f172a;">
    {{ $progTitle }}
</td>
<td style="padding: 0.75rem 0.85rem; white-space: nowrap; color: #475569;">
    {{ $branchTitle ?: '-' }}
</td>
```

---

### B. Addition of Inter Group (`pu2_group`) Column and Interactive Filter
**File Locations**: 
* Controller: `crm/Modules/Dashboards/app/Http/Controllers/ExamController.php`
* View: `crm/Modules/Dashboards/resources/views/exam/examslotdata.blade.php`

#### Controller Query Update:
```php
// Eager-load academicDetails for pu2_group extraction
$schedules = ExamSchedule::with(['student.program', 'student.branch', 'student.academicDetails', 'personalInfo'])
    ->orderBy('logdatetime', 'desc')
    ->get();

$distinctGroups = $schedules->map(function ($s) {
    return trim((string)($s->student?->academicDetails?->pu2_group ?? ''));
})->filter()->unique()->sort()->values();
```

#### View Table Header & Data:
```html
<th style="padding: 0.75rem 0.85rem; white-space: nowrap; text-align: center;">Inter Group</th>
...
<td style="padding: 0.75rem 0.85rem; text-align: center; white-space: nowrap;">
    @if($pu2GroupDisplay !== 'NA')
        <span class="group-pill">{{ $pu2GroupDisplay }}</span>
    @else
        <span style="color: #94a3b8; font-size: 0.76rem; font-weight: 600;">NA</span>
    @endif
</td>
```

#### Interactive Multi-Select Filter with Checkboxes:
```html
<div class="filter-item" style="position: relative;">
    <label class="filter-field-label">Inter Group</label>
    <div class="group-dropdown-wrap" style="position: relative;">
        <button type="button" id="btnGroupDropdown" class="btn-group-dropdown" onclick="toggleGroupDropdown(event)">
            <span id="groupDropdownLabel">All Groups ({{ $distinctGroups->count() }})</span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
        </button>
        <div id="groupDropdownMenu" class="group-dropdown-menu">
            <div class="group-dropdown-actions">
                <button type="button" class="btn-link-action" onclick="selectAllGroups(true)">Select All</button>
                <button type="button" class="btn-link-action" onclick="selectAllGroups(false)">Clear</button>
            </div>
            @foreach($distinctGroups as $grp)
                <label class="group-checkbox-item">
                    <input type="checkbox" class="cb-group-filter" value="{{ $grp }}" checked onchange="applyFilters()">
                    <span>{{ $grp }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>
```

---

## 4. Exam Slot Notice & PhD Slot Exemption

### A. Notice Text Below Exam Slot Selection:
**File Location**: `crm/Modules/Dashboards/resources/views/index.blade.php` (Line ~4770)

#### Code Added:
```html
<div style="margin-top: 4px; margin-left: 18px; font-size: 0.74rem; color: #047857; font-weight: 600;">
    Exam is at university, It is a computer based test
</div>
```

---

### B. Validation of Exam Slot for PhD as `NA` in Student Search:
**File Location**: `crm/Modules/Crm/resources/views/index.blade.php` (Lines ~515–530)

#### Old Code:
PhD students displayed "Completed" in the Exam Slot column of `stdsearch` because the stage column held a fallback value.

#### New Code:
```php
$progLevel = strtoupper(trim((string)($std->programlevel ?? '')));
$progShort = strtoupper(trim((string)($std->program_short_name ?? '')));
$isPhd = (
    $progLevel === 'PHD' ||
    $progLevel === 'DOCTORAL' ||
    str_contains($progLevel, 'DOCTOR') ||
    str_contains($progLevel, 'PHD') ||
    str_contains($progShort, 'PHD')
);

// 5. Exam Slot (stage5)
if ($isPhd) {
    // PhD candidates do not have entrance exam slot booking -> strictly NA
    $st5Pill = 'pill-na';
    $st5Text = 'NA';
    $st5Type = 'na';
} else {
    $hasBookedSlot = !empty($std->examSchedule) || in_array($s5, ['completed', 'booked', 'approved', 'done']);
    $st5Pill = $hasBookedSlot ? 'pill-done' : 'pill-na';
    $st5Text = $hasBookedSlot ? 'Completed' : 'NA';
    $st5Type = $hasBookedSlot ? 'done' : 'na';
}
```

---

## 5. CRM Tracking & PhD Academic Particulars (Postgraduate Details)

### A. Payload Mapping for PG Details
**File Location**: `crm/Modules/Crm/resources/views/index.blade.php` (Lines ~568–575)

#### Old Code:
```php
'ugCollege' => $acaInfo?->ug_college_name ?: 'N/A',
'ugUniversity' => $acaInfo?->ug_university_board ?: 'N/A',
'ugYear' => $acaInfo?->ug_passed_year ?: 'N/A',
'ugMarks' => $acaInfo?->ug_marks_earned ?: ($acaInfo?->ug_percentage ? $acaInfo->ug_percentage . '%' : 'N/A'),
```

#### New Code:
```php
'ugCollege' => $acaInfo?->ug_college_name ?: 'N/A',
'ugUniversity' => $acaInfo?->ug_university_board ?: 'N/A',
'ugYear' => $acaInfo?->ug_passed_year ?: 'N/A',
'ugMarks' => $acaInfo?->ug_marks_earned ?: ($acaInfo?->ug_percentage ? $acaInfo->ug_percentage . '%' : 'N/A'),
'ugStatus' => $acaInfo?->ug_current_status ?: 'N/A',
'pgCollege' => $acaInfo?->pg_college_name ?: 'N/A',
'pgUniversity' => $acaInfo?->pg_university_board ?: 'N/A',
'pgYear' => $acaInfo?->pg_passed_year ?: 'N/A',
'pgMarks' => $acaInfo?->pg_marks_earned ?: ($acaInfo?->pg_percentage ? $acaInfo->pg_percentage . '%' : 'N/A'),
'pgRoll' => $acaInfo?->pg_roll_no ?: 'N/A',
'pgStatus' => $acaInfo?->pg_current_status ?: 'N/A',
```

---

### B. Interactive Modal Rendering for Postgraduate Particulars
**File Location**: `crm/Modules/Crm/resources/views/index.blade.php` (Lines ~1190–1235)

#### Code Added:
```javascript
${aca.pgCollege && aca.pgCollege !== 'N/A' ? `
<!-- Postgraduate (PG) Degree Section (for PhD Candidates) -->
<div class="academic-card-block" style="margin-top: 1rem;">
    <div class="academic-card-header">
        <span style="display: flex; align-items: center; gap: 8px;">
            <span>🏛️</span>
            <span>Postgraduate (PG / Master's) Degree Particulars</span>
        </span>
        <span class="badge-paid">${safe(aca.pgMarks)}</span>
    </div>
    <div style="padding: 1rem 1.15rem;">
        <div class="stage-info-grid">
            <div class="stage-info-card">
                <span class="card-lbl">PG College Name</span>
                <span class="card-val" style="font-weight: 700;">${safe(aca.pgCollege)}</span>
            </div>
            <div class="stage-info-card">
                <span class="card-lbl">University / Board</span>
                <span class="card-val">${safe(aca.pgUniversity)}</span>
            </div>
            <div class="stage-info-card">
                <span class="card-lbl">Passing Year</span>
                <span class="card-val font-mono">${safe(aca.pgYear)}</span>
            </div>
            <div class="stage-info-card">
                <span class="card-lbl">Score / CGPA</span>
                <span class="card-val font-mono" style="color: #15803d; font-weight: 700;">${safe(aca.pgMarks)}</span>
            </div>
            ${aca.pgRoll && aca.pgRoll !== 'N/A' ? `
            <div class="stage-info-card">
                <span class="card-lbl">Roll / Reg Number</span>
                <span class="card-val font-mono">${safe(aca.pgRoll)}</span>
            </div>
            ` : ''}
            ${aca.pgStatus && aca.pgStatus !== 'N/A' ? `
            <div class="stage-info-card">
                <span class="card-lbl">Result Status</span>
                <span class="card-val">${safe(aca.pgStatus)}</span>
            </div>
            ` : ''}
        </div>
    </div>
</div>
` : ''}
```

---

## 6. Mobile Responsive Layout & Navigation Boundary Fixes

### A. Mobile Login Screen: "For more info" with Down Arrow & Brochures Below Form
**File Location**: `crm/resources/views/auth/login.blade.php`

#### Features:
* Moved admission information and brochures under the registration form in mobile viewports (`@media (max-width: 900px)`).
* Added a floating white indicator banner with a downward animated arrow:

```html
<!-- Mobile View Indicator: For more info -->
<div class="mobile-scroll-hint">
    <span>For more info</span>
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
    </svg>
</div>
```

```css
.mobile-scroll-hint {
    display: none;
    text-align: center;
    color: #ffffff;
    font-size: 0.78rem;
    font-weight: 700;
    margin: 1.25rem 0 1rem;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    letter-spacing: 0.02em;
    opacity: 0.95;
}
.mobile-scroll-hint svg {
    width: 14px;
    height: 14px;
    animation: bounceDown 1.6s infinite ease-in-out;
}
@media (max-width: 900px) {
    .mobile-scroll-hint {
        display: flex !important;
    }
}
```

---

### B. Mobile Dashboard Header Boundary & Sign Out Overflow Fix
**File Location**: `crm/Modules/Dashboards/resources/views/index.blade.php`

#### Old Behavior:
On small mobile viewports, candidate name and ID were placed on the right side, pushing the "Sign Out" button beyond the right screen boundary and creating a horizontal scroll white space.

#### New Solution:
* Moved the candidate Application ID to the left side directly below **"VIGNAN'S"**.
* Hid duplicate meta strings on the right.
* Reduced right padding and set `flex-shrink: 0` on `.btn-signout` so the entire header fits inside the mobile boundary.
* Added `max-width: 100vw; overflow-x: hidden;` across `html, body`.

```html
<a href="{{ route('dashboards.index') }}" class="brand-cluster">
    <img src="/images/vignan-shield-clean.png" alt="Vignan's University" class="brand-logo-img">
    <div class="brand-title-wrap">
        <div style="display:flex; align-items:center; gap:0.45rem;">
            <span class="brand-name-main">VIGNAN'S</span>
            <span class="brand-tag">V-SAT 2027 Portal</span>
        </div>
        <span class="brand-accred-sub">DEEMED TO BE UNIVERSITY &bull; NAAC A+</span>
        @if($appNo)
            <span class="brand-mobile-id">ID: #{{ $appNo }}</span>
        @endif
    </div>
</a>
```

---

## 7. Application Dossier Form: Partial Previews for Pending Applicants (`/application/preview`)

**File Locations**:
- `crm/Modules/Dashboards/app/Http/Controllers/ApplicationController.php`
- `crm/Modules/Dashboards/resources/views/application-form.blade.php`

### Problem:
Previously, the formal application print/download form assumed all applicants had completed Stage 4 payment and Stage 5 slot booking, crashing or printing placeholder values (`TXN_...`, `Booked in Stage 5`) for students who were still in progress.

### Solution:
- Added `is_paid` boolean check to differentiate paid vs unpaid applicants.
- Unpaid applicants now display an amber `PAYMENT PENDING` badge instead of green `PAYMENT COMPLETED`.
- Payment method, transaction ID, and exam slots show a clean dash (`-`) instead of placeholder mock IDs.
- Enabled CRM incharge to preview and print student forms at any point during onboarding.

```php
// ApplicationController.php: Pass accurate payment and booking status
'is_paid' => $isPaid,
'exam_date' => $examSchedule?->examdate ? \Carbon\Carbon::parse($examSchedule->examdate)->format('d M Y') : '-',
'exam_slot' => $examSchedule?->exam_slot ?? '-',
'payment_amount' => $paidAmountVal,
'payment_method' => $payDetail?->paytype ?? $receipt?->paytype ?? ($isPaid ? 'Online Settlement' : '-'),
'transaction_id' => $payDetail?->txnpayuid ?? $payDetail?->txncrmid ?? $receipt?->receiptno ?? ($isPaid ? ('TXN_' . $applino) : '-'),
'payment_status' => $isPaid ? 'Completed' : 'Pending',
```

---

## 8. Automated Notification Streamlining (4 WhatsApp + 2 SMS Rule)

### Policy Implemented:
* **WhatsApp Messages (4 total)**:
  1. OTP Verification
  2. Candidate Registration Confirmation
  3. Admission Fee Payment Confirmation (Stage 4 / PayU webhook)
  4. V-SAT Entrance Exam Slot Confirmation (Stage 5)
* **Text SMS Messages (2 total)**:
  1. OTP Verification
  2. Candidate Registration Confirmation
* **Intermediate Steps**: Personal bio updates, academic qualification saves, and KYC document uploads operate silently without triggering redundant messaging.

**File Locations**:
* `crm/Modules/Dashboards/app/Http/Controllers/DashboardsController.php` (Lines ~1450, ~1545, ~2030)
* `crm/Modules/Finance/app/Http/Controllers/PayUController.php` (Lines ~330–345)

```php
// In DashboardsController.php: Stage updates sent exclusively on fee payment and exam slot
protected function dispatchStageNotification(?CrmStudentRegistration $student, string $message, ?Request $request = null): void
{
    if (!$student) return;

    $cleanPhone = preg_replace('/\D/', '', (string)($student->phoneno ?? ''));
    if (strlen($cleanPhone) < 10) return;
    $cleanPhone = substr($cleanPhone, -10);

    $stdName = $student->name ?: 'Student';
    $appNo = $student->applinano ?: '';
    $ip = $request?->ip() ?? '127.0.0.1';

    // WhatsApp Notification via SmartPing (for Fee Payment & Exam Slot)
    if (class_exists(\Modules\Sms\Services\WhatsAppService::class)) {
        app(\Modules\Sms\Services\WhatsAppService::class)->sendStageUpdate(
            phone: $cleanPhone,
            stageMessage: $message,
            candidateName: $stdName,
            applicationId: $appNo,
            ip: $ip
        );
    }
}
```

---

## 9. CRM Student Search Stage Columns & Button Clean-up

**File Location**: `crm/Modules/Crm/resources/views/index.blade.php`

### Improvements:
1. **Renamed and Standardized Table Headers**:
   - `Personal Det` -> `Personal info`
   - `Acad Deat` -> `Academic info`
   - `Docs` -> `Documents`
   - `Payment` -> `Fee`
   - Added explicit `Exam Slot` tracking column.
2. **Simplified Actions**:
   - Replaced multi-button clutter (Preview, Print, Download) with a single, clear, high-contrast **"Preview"** button that links to the applicant dossier form with a dynamic return back-link.
   - Removed distracting emojis from buttons and toolbars for a professional CRM UI look.
   - Cleaned up stage status pills by removing SVG checkmark icons to keep table rows vertically slim and legible.

---

## Summary of Modified Files

| Component / Feature | File Path |
| :--- | :--- |
| **TotalDetails Route Setup** | `crm/Modules/Dashboards/routes/web.php` |
| **Menu Route Mapping** | `crm/Modules/Auth/app/Services/MenuService.php` |
| **TotalDetails Controller & Export** | `crm/Modules/Dashboards/app/Http/Controllers/ExamController.php` |
| **TotalDetails UI View** | `crm/Modules/Dashboards/resources/views/exam/totaldetails.blade.php` |
| **TotalDetails Global Blade Alias** | `crm/resources/views/exam/totaldetails.blade.php` |
| **Exam Slot Data Table & Filter** | `crm/Modules/Dashboards/resources/views/exam/examslotdata.blade.php` |
| **Mobile Header & Exam Notice** | `crm/Modules/Dashboards/resources/views/index.blade.php` |
| **Mobile Login & Brochures** | `crm/resources/views/auth/login.blade.php` |
| **CRM Tracking & PG Particulars** | `crm/Modules/Crm/resources/views/index.blade.php` |
| **Notification Policy (4 WA + 2 SMS)** | `crm/Modules/Dashboards/app/Http/Controllers/DashboardsController.php`<br>`crm/Modules/Finance/app/Http/Controllers/PayUController.php` |
