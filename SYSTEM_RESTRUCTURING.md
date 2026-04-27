# OJT Monitoring & Evaluation System - Restructuring Guide

## 🎯 System Overview

Your OJT system has been restructured from a **template generator** into a comprehensive **Monitoring & Evaluation Platform**. The old template generation feature remains as a secondary module.

## 👥 4 User Roles

### 1. **Student**
- **Role Identifier**: `student`
- **Permissions**: 
  - Encode daily time records (DTR)
  - Submit weekly/monthly reports
  - Request DTR corrections
  - View own evaluations and status
  - Track OJT progress

### 2. **Company OJT Supervisor**
- **Role Identifier**: `supervisor`
- **Associated Field**: `company_id` (links to company user)
- **Permissions**:
  - Monitor assigned trainees
  - Approve/reject DTR entries
  - Review and approve student reports
  - Evaluate student performance (ratings)
  - Report issues (absences, drops, transfers)
  - Submit certifications for students

### 3. **School OJT Coordinator**
- **Role Identifier**: `coordinator`
- **Permissions**:
  - Monitor all students system-wide
  - Receive and review reports from companies
  - Review evaluations submitted by supervisors
  - Validate student completion and certification
  - Track student progress and issues
  - Generate completion certificates
  - Assign issues to supervisors for resolution
  - Manage OJT placements

### 4. **Admin**
- **Role Identifier**: `admin`
- **Permissions**:
  - Manage all users
  - Manage system records
  - Maintain database
  - System configuration

---

## 📊 Core Modules

### Module 1: Daily Time Record (DTR) System

**Purpose**: Track student attendance and work hours during OJT

**Tables**:
- `daily_time_records` - Main DTR entries
- `dtr_corrections` - Correction requests for time entries

**Key Features**:
- Students log time in/out daily
- Automatic hour calculation
- Supervisor verification workflow
- Correction request system

**API Endpoints**:
```
GET    /api/dtr                                    # Student views their DTRs
POST   /api/dtr                                    # Student creates DTR entry
PATCH  /api/dtr/{dtr}                             # Student updates DTR
GET    /api/dtr/supervisor/pending                # Supervisor sees pending DTRs
PATCH  /api/dtr/{dtr}/verify                      # Supervisor verifies DTR
PATCH  /api/dtr/{dtr}/reject                      # Supervisor rejects DTR
POST   /api/dtr/{dtr}/request-correction          # Student requests correction
PATCH  /api/dtr/corrections/{correction}/approve  # Supervisor approves correction
```

---

### Module 2: Reports Management

**Purpose**: Manage weekly/monthly student progress reports

**Tables**:
- `reports` - Report submissions
- `report_histories` - Track report changes

**Key Features**:
- Draft → Submit → Review → Approve workflow
- Student writes accomplishments, activities, challenges
- Supervisor/Coordinator reviews and approves
- Escalation to coordinator if needed
- Version history tracking

**API Endpoints**:
```
GET    /api/reports                     # List reports
POST   /api/reports                     # Create draft report
PATCH  /api/reports/{report}            # Edit draft report
POST   /api/reports/{report}/submit     # Submit for review
PATCH  /api/reports/{report}/approve    # Approve report
PATCH  /api/reports/{report}/reject     # Reject/request revision
POST   /api/reports/{report}/escalate   # Escalate to coordinator
GET    /api/reports/{report}/history    # View report history
```

---

### Module 3: Evaluation System

**Purpose**: Supervisor evaluates student performance

**Table**: `evaluations` (already exists - extended in Phase 3)

**Rating Categories** (1-5 scale):
- Technical skills
- Communication
- Teamwork
- Professionalism
- Initiative

**Key Features**:
- Periodic evaluations by supervisor
- Automatic grade computation
- Coordinator review and approval
- Performance feedback and comments

---

### Module 4: Issue Tracking

**Purpose**: Report and track student issues

**Tables**:
- `issues` - Issue reports
- `issue_updates` - Track issue progress

**Issue Types**:
- `absence` - Student was absent
- `drop` - Student dropped OJT
- `transfer` - Student transferred to another company
- `behavioral` - Behavioral issues
- `performance` - Performance concerns
- `other` - Other issues

**Status Flow**: `reported` → `acknowledged` → `investigating` → `resolved` → `closed`

**Key Features**:
- Supervisor reports issues
- Coordinator acknowledges and investigates
- Student status updates (active/dropped/transferred/suspended)
- Resolution tracking

**API Endpoints**:
```
GET    /api/issues                           # List issues
POST   /api/issues                           # Create issue (supervisor only)
GET    /api/issues/{issue}                   # View issue details
PATCH  /api/issues/{issue}/acknowledge      # Acknowledge issue (coordinator)
PATCH  /api/issues/{issue}/resolve          # Resolve issue
PATCH  /api/issues/{issue}/mark-dropped     # Mark student as dropped
PATCH  /api/issues/{issue}/mark-transferred # Mark student as transferred
GET    /api/issues/{issue}/updates          # View issue update history
```

---

### Module 5: Certification & Completion

**Purpose**: Validate student OJT completion and issue certificates

**Tables**:
- `ojt_placements` - Links student to company for OJT period
- `certifications` - Company certification of completion
- `completion_records` - Final completion validation

**Key Features**:
- Supervisor submits certification with actual hours and ratings
- Coordinator verifies and approves
- Automatic completion record generation
- Certificate number generation
- Progress tracking

**API Endpoints**:
```
GET    /api/placements                                 # List placements
POST   /api/placements                                 # Create placement (coordinator)
GET    /api/placements/{placement}                     # View placement
GET    /api/placements/{placement}/progress            # Get progress %
GET    /api/placements/{placement}/certifications      # List certifications
POST   /api/placements/{placement}/certifications      # Create certification (supervisor)
PATCH  /api/placements/certifications/{cert}/verify    # Verify certificate
PATCH  /api/placements/certifications/{cert}/approve   # Approve certificate
GET    /api/placements/{placement}/completion          # Get completion record
POST   /api/placements/{placement}/mark-completed      # Mark completed
PATCH  /api/placements/completion/{record}/approve     # Approve completion
```

---

## 🗄️ Database Schema Overview

### Users Table (Updated)
```sql
- id
- fname, mname, lname, suffix
- email, password
- role: enum('student', 'supervisor', 'coordinator', 'admin')
- student_number (for students)
- course, year_level (for students)
- company_id (for supervisors)
- company_name, company_position (for supervisors)
- department, unit
- coordinator_id (student's assigned coordinator)
- status: enum('active', 'inactive')
- must_change_password
- timestamps
```

### Key Models & Relationships

```
User (student)
  ├─ HasMany DailyTimeRecord
  ├─ HasMany Report
  ├─ HasMany Issue (as student_id)
  ├─ HasMany OjtPlacement
  ├─ HasMany Evaluation (as trainee_id)
  └─ HasMany CompletionRecord

User (supervisor)
  ├─ HasMany DailyTimeRecord (as verified_by)
  ├─ HasMany Issue (as reported_by)
  ├─ HasMany OjtPlacement (as supervisor_id)
  ├─ HasMany Certification (as issued_by)
  └─ HasMany Evaluation (as supervisor_id)

User (coordinator)
  ├─ HasMany OjtPlacement (as coordinator_id)
  ├─ HasMany Issue (as assigned_to)
  ├─ HasMany Certification (as verified_by)
  └─ HasMany CompletionRecord (as approved_by)

DailyTimeRecord
  ├─ BelongsTo User (student)
  ├─ BelongsTo User (verified_by)
  └─ HasMany DtrCorrection

Report
  ├─ BelongsTo User (submitted_by)
  ├─ BelongsTo User (reviewed_by)
  ├─ BelongsTo User (escalated_to)
  └─ HasMany ReportHistory

Issue
  ├─ BelongsTo User (student)
  ├─ BelongsTo User (reported_by)
  ├─ BelongsTo User (assigned_to)
  └─ HasMany IssueUpdate

OjtPlacement
  ├─ BelongsTo User (student)
  ├─ BelongsTo User (company)
  ├─ BelongsTo User (supervisor)
  ├─ BelongsTo User (coordinator)
  ├─ HasMany Certification
  └─ HasMany CompletionRecord

Certification
  ├─ BelongsTo OjtPlacement
  ├─ BelongsTo User (student)
  ├─ BelongsTo User (issued_by)
  └─ BelongsTo User (verified_by)

CompletionRecord
  ├─ BelongsTo User (student)
  ├─ BelongsTo OjtPlacement
  └─ BelongsTo User (approved_by)

Evaluation
  ├─ BelongsTo User (trainee)
  └─ BelongsTo User (supervisor)
```

---

## 🔧 Status Workflows

### DTR Status
```
pending → verified (approved by supervisor)
       → rejected (rejected by supervisor)
```

### Report Status
```
draft → submitted → under_review → approved
     ↓
    rejected (with comments - student revises to draft)
    
under_review → (escalated_to coordinator if needed)
```

### Issue Status
```
reported → acknowledged → investigating → resolved → closed
```

### Certification Status
```
submitted → pending_verification → verified → approved
```

### Completion Status
```
pending → approved (meets requirements)
       → conditional (needs follow-up)
```

---

## 📁 File Structure

```
app/
├── Models/
│   ├── DailyTimeRecord.php
│   ├── DtrCorrection.php
│   ├── Report.php
│   ├── ReportHistory.php
│   ├── Issue.php
│   ├── IssueUpdate.php
│   ├── OjtPlacement.php
│   ├── Certification.php
│   ├── CompletionRecord.php
│   ├── Evaluation.php (existing)
│   └── User.php (updated)
│
├── Http/
│   ├── Controllers/
│   │   ├── DailyTimeRecordController.php
│   │   ├── ReportController.php
│   │   ├── IssueController.php
│   │   ├── OjtPlacementController.php
│   │   └── ... (existing controllers)
│   └── Middleware/
│       └── EnsureUserRole.php
│
├── Services/
│   └── OjtMonitoringService.php (new)
│
└── ... (other existing files)

database/
├── migrations/
│   ├── 2026_04_17_000001_create_dtr_system_tables.php
│   ├── 2026_04_17_000002_create_reports_system_tables.php
│   ├── 2026_04_17_000003_create_issues_tracking_tables.php
│   ├── 2026_04_17_000004_create_certification_tables.php
│   ├── 2026_04_17_000005_update_users_table_with_roles.php
│   └── ... (existing migrations)
│
└── seeders/ (to create)

routes/
├── ojt_api.php (new - all OJT API endpoints)
├── web.php (update with new views)
└── ... (existing routes)
```

---

## 🚀 Next Steps

1. **Update User Model** - Add relationships to all new models
2. **Run Migrations** - Execute database migrations
3. **Create Views/Dashboards** - Build UI for each user role
4. **Notification System** - Set up email notifications for key events
5. **Testing** - Test all API endpoints
6. **Seeding** - Create test data for development
7. **Frontend Integration** - Connect Vue.js/React frontend to APIs
8. **Evaluation Integration** - Integrate new evaluation system with Evaluation model

---

## 📝 Key Methods in Services/OjtMonitoringService

- `getStudentStats(User)` - Get student dashboard stats
- `getSupervisorStats(User)` - Get supervisor dashboard stats
- `getCoordinatorStats(User)` - Get coordinator dashboard stats
- `getStudentProgress(User)` - Calculate completion progress
- `isStudentOnTrack(User, Placement)` - Check if student is meeting requirements
- `generateMonitoringReport(startDate, endDate)` - Generate period report

---

## 🔗 API Authentication

All endpoints require:
```
Authorization: Bearer {token}
```

Use Laravel Sanctum for API token generation.

---

## Template Generation (Now Secondary)

The original template generation feature is still available:
- Document.php model
- DocumentGeneratorController
- Template system (backward compatible)

This is now a **supporting feature** rather than the core system.
