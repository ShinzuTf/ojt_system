# OJT System - Quick Start Guide

## 🎯 Getting Started with the New Monitoring & Evaluation System

This guide will get you up and running with the restructured OJT system in 5 minutes.

---

## 1️⃣ Register API Routes

**File**: `routes/api.php`

Add this at the end of the file:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ✨ OJT Monitoring & Evaluation System Routes
require base_path('routes/ojt_api.php');
```

---

## 2️⃣ Register Middleware

**File**: `app/Http/Kernel.php`

Find the `$routeMiddleware` array and add:

```php
protected $routeMiddleware = [
    // ... existing middleware

    'role' => \App\Http\Middleware\EnsureUserRole::class,
];
```

---

## 3️⃣ Run Migrations

```bash
cd c:\xampp\htdocs\ojt_system

php artisan migrate
```

**Expected Output**:
```
Migrating: 2026_04_17_000001_create_dtr_system_tables
Migrated:  2026_04_17_000001_create_dtr_system_tables (XXXms)
Migrating: 2026_04_17_000002_create_reports_system_tables
Migrated:  2026_04_17_000002_create_reports_system_tables (XXXms)
... (and so on)
```

---

## 4️⃣ Verify Database Tables

Check that these tables were created:

```
✓ daily_time_records
✓ dtr_corrections
✓ reports
✓ report_histories
✓ issues
✓ issue_updates
✓ ojt_placements
✓ certifications
✓ completion_records
✓ (users table updated)
```

---

## 5️⃣ Test API Endpoints

Use Postman or curl to test:

### A. Create DTR Entry (as Student)

```bash
POST http://localhost/api/dtr
Authorization: Bearer {token}
Content-Type: application/json

{
  "record_date": "2026-04-17",
  "time_in": "08:00",
  "time_out": "17:00",
  "notes": "Completed project tasks"
}
```

### B. Verify DTR (as Supervisor)

```bash
PATCH http://localhost/api/dtr/1/verify
Authorization: Bearer {token}
Content-Type: application/json

{
  "supervisor_remarks": "Approved"
}
```

### C. Submit Report (as Student)

```bash
POST http://localhost/api/reports
Authorization: Bearer {token}
Content-Type: application/json

{
  "report_type": "weekly",
  "report_period_start": "2026-04-14",
  "report_period_end": "2026-04-18",
  "accomplishments": "Completed feature X",
  "activities": "Code review, testing",
  "challenges": "Database optimization",
  "learnings": "Better practices",
  "recommendations": "Need more resources"
}
```

---

## 🗂️ File Changes Made

### New Files Created:

**Migrations** (5 files):
- `database/migrations/2026_04_17_000001_create_dtr_system_tables.php`
- `database/migrations/2026_04_17_000002_create_reports_system_tables.php`
- `database/migrations/2026_04_17_000003_create_issues_tracking_tables.php`
- `database/migrations/2026_04_17_000004_create_certification_tables.php`
- `database/migrations/2026_04_17_000005_update_users_table_with_roles.php`

**Models** (9 files):
- `app/Models/DailyTimeRecord.php`
- `app/Models/DtrCorrection.php`
- `app/Models/Report.php`
- `app/Models/ReportHistory.php`
- `app/Models/Issue.php`
- `app/Models/IssueUpdate.php`
- `app/Models/OjtPlacement.php`
- `app/Models/Certification.php`
- `app/Models/CompletionRecord.php`

**Controllers** (4 files):
- `app/Http/Controllers/DailyTimeRecordController.php`
- `app/Http/Controllers/ReportController.php`
- `app/Http/Controllers/IssueController.php`
- `app/Http/Controllers/OjtPlacementController.php`

**Middleware** (1 file):
- `app/Http/Middleware/EnsureUserRole.php`

**Services** (1 file):
- `app/Services/OjtMonitoringService.php`

**Routes** (1 file):
- `routes/ojt_api.php`

**Updated Files**:
- `app/Models/User.php` (added relationships)

**Documentation** (2 files):
- `SYSTEM_RESTRUCTURING.md` (detailed architecture)
- `IMPLEMENTATION_CHECKLIST.md` (next steps)
- `QUICKSTART.md` (this file)

---

## 📊 System Architecture

```
┌─────────────────────────────────────────────────┐
│         OJT Monitoring & Evaluation              │
│              System (NEW)                        │
├─────────────────────────────────────────────────┤
│                                                  │
│  Module 1: DTR Tracking                         │
│  └─ Students log time → Supervisors verify     │
│                                                  │
│  Module 2: Reports Management                   │
│  └─ Submit → Review → Approve workflow         │
│                                                  │
│  Module 3: Evaluation System                    │
│  └─ Performance ratings (1-5 scale)            │
│                                                  │
│  Module 4: Issue Tracking                       │
│  └─ Report issues → Track resolution           │
│                                                  │
│  Module 5: Certification & Completion           │
│  └─ Certify hours → Generate certificate       │
│                                                  │
│  [SECONDARY] Template Generation                │
│  └─ Auto-generate documents as needed          │
│                                                  │
└─────────────────────────────────────────────────┘
```

---

## 👥 User Roles & Permissions

| Action | Student | Supervisor | Coordinator | Admin |
|--------|---------|-----------|-------------|-------|
| Create DTR | ✅ | ❌ | ❌ | ❌ |
| Verify DTR | ❌ | ✅ | ❌ | ✅ |
| Submit Report | ✅ | ✅ | ❌ | ❌ |
| Review Report | ❌ | ✅ | ✅ | ✅ |
| Report Issue | ❌ | ✅ | ❌ | ❌ |
| Resolve Issue | ❌ | ❌ | ✅ | ✅ |
| Create Placement | ❌ | ❌ | ✅ | ✅ |
| Submit Certificate | ❌ | ✅ | ❌ | ❌ |
| Approve Certificate | ❌ | ❌ | ✅ | ✅ |

---

## 🔑 Key Database Tables

### Daily Time Records
```sql
daily_time_records
├─ id
├─ student_id (FK)
├─ record_date
├─ time_in / time_out
├─ hours_worked (auto-calculated)
├─ status (pending / verified / rejected)
└─ verified_by (FK to supervisor)
```

### Reports
```sql
reports
├─ id
├─ submitted_by (FK to student/supervisor)
├─ report_type (weekly / monthly / incident)
├─ report_period_start / report_period_end
├─ accomplishments, activities, challenges
├─ status (draft / submitted / under_review / approved / rejected)
├─ reviewed_by (FK to reviewer)
└─ escalated_to (FK to coordinator if needed)
```

### Issues
```sql
issues
├─ id
├─ student_id (FK)
├─ reported_by (FK to supervisor)
├─ issue_type (absence / drop / transfer / behavioral / performance)
├─ issue_date
├─ description
├─ status (reported / acknowledged / investigating / resolved / closed)
└─ assigned_to (FK to coordinator)
```

### OJT Placements
```sql
ojt_placements
├─ id
├─ student_id (FK)
├─ company_id (FK to supervisor's company)
├─ supervisor_id (FK)
├─ coordinator_id (FK)
├─ start_date / end_date
├─ total_required_hours (default 480)
└─ status (active / completed / cancelled / suspended)
```

### Certifications
```sql
certifications
├─ id
├─ placement_id (FK)
├─ student_id (FK)
├─ issued_by (FK to supervisor)
├─ certification_date
├─ actual_hours_worked
├─ final_rating (1-5)
├─ status (submitted / verified / approved)
└─ certificate_path (file storage)
```

### Completion Records
```sql
completion_records
├─ id
├─ student_id (FK)
├─ placement_id (FK)
├─ completion_date
├─ total_hours_completed
├─ final_grade
├─ met_requirements (boolean)
├─ is_completed (boolean)
├─ certificate_number (unique)
└─ status (pending / approved / conditional)
```

---

## 📝 Common API Workflows

### Workflow 1: Daily Time Record Entry

```
1. Student: POST /api/dtr
   └─ Creates DTR entry with time_in, time_out

2. Supervisor: GET /api/dtr/supervisor/pending
   └─ Views pending DTR entries

3. Supervisor: PATCH /api/dtr/{id}/verify
   └─ Verifies DTR entry

4. Student: GET /api/dtr/summary/{id}
   └─ Views DTR summary and hours
```

### Workflow 2: Report Submission

```
1. Student: POST /api/reports
   └─ Creates draft report

2. Student: PATCH /api/reports/{id}
   └─ Edits draft (if needed)

3. Student: POST /api/reports/{id}/submit
   └─ Submits for review

4. Supervisor: PATCH /api/reports/{id}/approve
   └─ Approves report

5. OR Supervisor: PATCH /api/reports/{id}/reject
   └─ Requests revision (reverts to draft)
```

### Workflow 3: Issue Tracking

```
1. Supervisor: POST /api/issues
   └─ Reports issue (absence/drop/transfer)

2. Coordinator: PATCH /api/issues/{id}/acknowledge
   └─ Acknowledges issue

3. Coordinator: PATCH /api/issues/{id}/resolve
   └─ Resolves issue with notes

4. OR Coordinator: PATCH /api/issues/{id}/mark-dropped
   └─ Marks student as dropped
```

### Workflow 4: Certification Process

```
1. Coordinator: POST /api/placements
   └─ Creates OJT placement for student

2. [Throughout] Student: GET /api/placements/{id}/progress
   └─ Track hours and progress

3. Supervisor: POST /api/placements/{id}/certifications
   └─ Submits certification with hours/rating

4. Coordinator: PATCH /api/placements/certifications/{id}/approve
   └─ Approves certification

5. Coordinator: POST /api/placements/{id}/mark-completed
   └─ Marks placement as complete

6. System: Generates certificate number automatically
```

---

## 🧪 Quick Test Script

**File**: `test_ojt_api.sh`

```bash
#!/bin/bash

BASE_URL="http://localhost/api"
TOKEN="your_bearer_token_here"

echo "=== Testing OJT API ==="

# Test 1: Get DTRs
echo "Test 1: Get DTRs"
curl -X GET "$BASE_URL/dtr" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"

echo -e "\n---\n"

# Test 2: Create DTR
echo "Test 2: Create DTR"
curl -X POST "$BASE_URL/dtr" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "record_date": "2026-04-17",
    "time_in": "08:00",
    "time_out": "17:00",
    "notes": "Test entry"
  }'

echo -e "\n---\n"

# Test 3: Get Reports
echo "Test 3: Get Reports"
curl -X GET "$BASE_URL/reports" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"

echo -e "\n---\n"

# Test 4: Get Issues
echo "Test 4: Get Issues"
curl -X GET "$BASE_URL/issues" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

Run with:
```bash
bash test_ojt_api.sh
```

---

## 🚨 Common Errors & Fixes

### Error: "Undefined table: users"
**Cause**: Migrations not run
**Fix**: 
```bash
php artisan migrate
```

### Error: "Class not found: OjtPlacement"
**Cause**: Model namespace incorrect or file not created
**Fix**: Check file exists in `app/Models/OjtPlacement.php`

### Error: "403 Unauthorized"
**Cause**: Role middleware rejecting user
**Fix**: Check user's role in database:
```php
User::find(1)->update(['role' => 'student']);
```

### Error: "Route not found"
**Cause**: Routes not registered in routes/api.php
**Fix**: Add `require base_path('routes/ojt_api.php');` to routes/api.php

### Error: "Trying to get property of non-object"
**Cause**: Foreign key relationship missing
**Fix**: Verify relationships in models match database foreign keys

---

## 📞 Support Commands

```bash
# Check database structure
php artisan tinker
> \App\Models\DailyTimeRecord::all();

# View all routes
php artisan route:list

# Check model relationships
php artisan tinker
> $user = \App\Models\User::find(1);
> $user->placements;

# Clear cache if needed
php artisan cache:clear
php artisan config:clear
```

---

## ✨ Next Steps

1. ✅ Register routes in `routes/api.php`
2. ✅ Register middleware in `app/Http/Kernel.php`
3. ✅ Run migrations: `php artisan migrate`
4. ✅ Test APIs with Postman
5. ⬜ Create Blade views for each role
6. ⬜ Set up notifications/mail
7. ⬜ Create Vue/React frontend components
8. ⬜ Implement activity logging
9. ⬜ Set up background jobs (queue)
10. ⬜ Deploy to production

---

**Status**: Phase 1 Complete ✅
**Next Phase**: Frontend Views & Notifications
**Estimated Completion**: 2-3 weeks
