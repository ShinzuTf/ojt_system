# OJT System Implementation Checklist

This document tracks the progress of converting from Template Generator → Monitoring & Evaluation System

## ✅ COMPLETED PHASE 1: Database & Models

### Migrations Created
- [x] `2026_04_17_000001_create_dtr_system_tables.php` - DTR tracking
- [x] `2026_04_17_000002_create_reports_system_tables.php` - Report management
- [x] `2026_04_17_000003_create_issues_tracking_tables.php` - Issue tracking
- [x] `2026_04_17_000004_create_certification_tables.php` - Certifications & placements
- [x] `2026_04_17_000005_update_users_table_with_roles.php` - User role updates

### Models Created
- [x] DailyTimeRecord.php
- [x] DtrCorrection.php
- [x] Report.php
- [x] ReportHistory.php
- [x] Issue.php
- [x] IssueUpdate.php
- [x] OjtPlacement.php
- [x] Certification.php
- [x] CompletionRecord.php
- [x] User.php (relationships added)

### Controllers Created
- [x] DailyTimeRecordController.php
- [x] ReportController.php
- [x] IssueController.php
- [x] OjtPlacementController.php

### Middleware & Services
- [x] EnsureUserRole.php (role-based access)
- [x] OjtMonitoringService.php (dashboard statistics)

### Route Files
- [x] routes/ojt_api.php (all API endpoints)

---

## 🚀 NEXT STEPS (DO THESE)

### Step 1: Register Routes & Middleware

**File**: `bootstrap/app.php` or `app/Providers/RouteServiceProvider.php`

```php
// Register the ojt_api routes
Route::group(['prefix' => 'api'], function () {
    require base_path('routes/ojt_api.php');
});

// Register the role middleware
Route::middleware('role:student,supervisor,coordinator,admin')
     ->group(function () {
         // Routes here
     });
```

Or in `routes/api.php`, add:
```php
require base_path('routes/ojt_api.php');
```

### Step 2: Register Middleware in Kernel

**File**: `app/Http/Kernel.php`

Add to `$routeMiddleware`:
```php
'role' => \App\Http\Middleware\EnsureUserRole::class,
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

This will create all the new tables for the OJT system.

### Step 4: Create Test Seeder

**Create**: `database/seeders/OjtTestDataSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\OjtPlacement;
use Illuminate\Database\Seeder;

class OjtTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create test student
        $student = User::create([
            'fname' => 'John',
            'lname' => 'Doe',
            'email' => 'student@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'student_number' => 'CS-2024-001',
            'course' => 'BSCS',
            'year_level' => 4,
            'status' => 'active',
        ]);

        // Create test supervisor
        $supervisor = User::create([
            'fname' => 'Jane',
            'lname' => 'Smith',
            'email' => 'supervisor@example.com',
            'password' => bcrypt('password'),
            'role' => 'supervisor',
            'company_id' => 1,
            'company_name' => 'Tech Corp',
            'company_position' => 'HR Manager',
            'status' => 'active',
        ]);

        // Create test coordinator
        $coordinator = User::create([
            'fname' => 'Dr.',
            'lname' => 'Admin',
            'email' => 'coordinator@example.com',
            'password' => bcrypt('password'),
            'role' => 'coordinator',
            'status' => 'active',
        ]);

        // Create test placement
        OjtPlacement::create([
            'student_id' => $student->id,
            'company_id' => $supervisor->company_id,
            'supervisor_id' => $supervisor->id,
            'coordinator_id' => $coordinator->id,
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'total_required_hours' => 480,
            'status' => 'active',
        ]);
    }
}
```

Run with:
```bash
php artisan db:seed --class=OjtTestDataSeeder
```

### Step 5: Create Dashboards/Views

Create these Blade templates:

#### `resources/views/student/dashboard.blade.php`
- Show DTR summary
- Show pending reports
- Show evaluations
- Show progress bar
- Show active placement info

#### `resources/views/supervisor/dashboard.blade.php`
- List assigned trainees
- Show pending DTRs to verify
- Show pending reports to review
- Show issues reported
- Quick stats

#### `resources/views/coordinator/dashboard.blade.php`
- Overall student monitoring
- Active placements
- Pending issues
- Pending certifications
- System-wide statistics

#### `resources/views/admin/dashboard.blade.php`
- User management
- System configuration
- Database maintenance
- Activity logs

### Step 6: Create Notification Events

**File**: `app/Events/DtrSubmittedEvent.php`
```php
<?php

namespace App\Events;

use App\Models\DailyTimeRecord;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DtrSubmittedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public DailyTimeRecord $dtr) {}
}
```

Create listeners for:
- DtrSubmittedListener (notify supervisor)
- ReportSubmittedListener (notify reviewer)
- IssueReportedListener (notify coordinator)
- CertificationSubmittedListener (notify coordinator)

### Step 7: Add Mail Templates

Create mail classes:

```bash
php artisan make:mail DtrVerificationReminder
php artisan make:mail ReportRejectionNotification
php artisan make:mail IssueReportedNotification
php artisan make:mail CertificationReceivedNotification
```

### Step 8: Create API Documentation

Generate with:
```bash
php artisan install:api
```

Document endpoints with:
- Request/response examples
- Authentication requirements
- Permission requirements

### Step 9: Frontend Integration (Vue.js / React)

Create components for:
- DTR entry form
- Report submission form
- Issue reporting form
- Progress dashboard
- Status tracking

Example Vue component structure:
```
resources/js/components/
├── DailyTimeRecord/
│   ├── DtrEntry.vue
│   ├── DtrList.vue
│   └── DtrVerification.vue
├── Reports/
│   ├── ReportForm.vue
│   ├── ReportList.vue
│   └── ReportReview.vue
├── Issues/
│   ├── IssueForm.vue
│   ├── IssueList.vue
│   └── IssueTracking.vue
├── Dashboards/
│   ├── StudentDashboard.vue
│   ├── SupervisorDashboard.vue
│   ├── CoordinatorDashboard.vue
│   └── AdminDashboard.vue
└── Common/
    ├── ProgressBar.vue
    ├── StatusBadge.vue
    └── UserProfile.vue
```

### Step 10: Testing

Create feature tests:

```bash
php artisan make:test DailyTimeRecordTest --feature
php artisan make:test ReportSubmissionTest --feature
php artisan make:test IssueTrackingTest --feature
php artisan make:test OjtPlacementTest --feature
```

Run tests:
```bash
php artisan test
```

---

## 📋 Validation Checklist

Before going live:

- [ ] All migrations run successfully
- [ ] Database relationships working
- [ ] API endpoints tested with Postman/Insomnia
- [ ] Role-based access working
- [ ] Student can create DTR entries
- [ ] Supervisor can verify DTRs
- [ ] Reports workflow complete
- [ ] Issue tracking functional
- [ ] Certifications workflow complete
- [ ] Notifications sending
- [ ] Dashboards displaying correctly
- [ ] User authentication working
- [ ] Email notifications configured
- [ ] Error handling implemented
- [ ] Activity logging working

---

## 🔑 Key Configuration

### Environment Variables (.env)

```env
APP_NAME="OJT Monitoring System"
APP_URL=http://localhost

MAIL_MAILER=smtp
MAIL_HOST=mail.example.com
MAIL_PORT=465
MAIL_USERNAME=noreply@example.com
MAIL_PASSWORD=password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="OJT System"

# For notifications
QUEUE_CONNECTION=sync  # or database, redis
```

### Sanctum Configuration (API Auth)

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

Add to User model:
```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
}
```

### CORS Configuration

**File**: `config/cors.php`

```php
'allowed_origins' => ['*'],
'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
'allowed_headers' => ['Content-Type', 'Authorization'],
'max_age' => 86400,
```

---

## 🐛 Troubleshooting

### Migration Fails
- Check if tables already exist
- Verify foreign key constraints
- Check column type compatibility

### Relationships Not Working
- Ensure models are in correct namespace
- Check foreign keys match column names
- Verify relationship method parameters

### API Returns 403 Unauthorized
- Check user role middleware
- Verify token is valid
- Check route middleware registration

### Notifications Not Sending
- Check MAIL_* environment variables
- Run `php artisan queue:work` if using queue
- Check Laravel logs in `storage/logs/`

---

## 📞 Support

For questions about the restructuring:
1. Check SYSTEM_RESTRUCTURING.md for architecture details
2. Review model relationships in app/Models/
3. Check controller logic in app/Http/Controllers/
4. Refer to OjtMonitoringService for business logic

---

## 📅 Timeline

- **Phase 1 (COMPLETED)**: Database setup, models, controllers
- **Phase 2 (CURRENT)**: API testing, route registration, middleware
- **Phase 3**: Notifications & email templates
- **Phase 4**: Frontend views/dashboards
- **Phase 5**: Testing & optimization
- **Phase 6**: Documentation & deployment

**Estimated Total Time**: 2-3 weeks for full implementation
