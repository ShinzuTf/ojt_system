<?php

namespace App\Services;

use App\Models\User;
use App\Models\PastOjtRecord;
use App\Models\OjtInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OjtDeactivationService
{
    /**
     * End of semester deactivation and archival
     * 
     * Deactivates all active student OJT accounts and archives their OJT records.
     * Called at the end of each OJT semester.
     * 
     * @param int|null $adminId The admin user ID performing the deactivation
     * @param string|null $notes Optional notes for archival
     * @return array Result with counts and status
     */
    public static function deactivateAndArchiveOjtRecords(?int $adminId = null, ?string $notes = null): array
    {
        try {
            DB::beginTransaction();

            $deactivatedCount = 0;
            $archivedCount = 0;
            $errors = [];

            // Get all active student accounts with OJT info
            $activeStudents = User::where('role', 'student')
                ->where('status', 'active')
                ->with('ojtInfo')
                ->get();

            foreach ($activeStudents as $student) {
                try {
                    // Check if student has OJT info
                    if ($student->ojtInfo) {
                        // Archive the OJT record to past_ojt_records
                        self::archiveOjtRecord($student, $adminId, $notes);
                        $archivedCount++;
                    }

                    // Deactivate the student account
                    $student->update(['status' => 'inactive']);
                    $deactivatedCount++;

                    // Log the activity
                    ActivityLogService::logOjtDeactivation(
                        $student->id,
                        $student->email,
                        $student->ojtInfo?->company_name
                    );

                } catch (\Exception $e) {
                    $errors[] = [
                        'student_id' => $student->id,
                        'student_email' => $student->email,
                        'error' => $e->getMessage(),
                    ];
                    Log::error('Error deactivating student: ' . $e->getMessage(), [
                        'student_id' => $student->id,
                    ]);
                }
            }

            DB::commit();

            // Log the bulk operation
            ActivityLogService::logBulkOjtDeactivation($deactivatedCount, $archivedCount, count($errors));

            return [
                'success' => true,
                'deactivated_count' => $deactivatedCount,
                'archived_count' => $archivedCount,
                'errors' => $errors,
                'message' => "Successfully deactivated {$deactivatedCount} OJT accounts and archived {$archivedCount} records.",
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('OJT deactivation service error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error during OJT deactivation: ' . $e->getMessage(),
                'deactivated_count' => 0,
                'archived_count' => 0,
                'errors' => [],
            ];
        }
    }

    /**
     * Archive a single student's OJT record to past_ojt_records
     * 
     * @param User $student
     * @param int|null $adminId
     * @param string|null $notes
     * @return PastOjtRecord
     */
    private static function archiveOjtRecord(User $student, ?int $adminId = null, ?string $notes = null): PastOjtRecord
    {
        $ojtInfo = $student->ojtInfo;

        return PastOjtRecord::create([
            'user_id' => $student->id,
            'student_number' => $ojtInfo->student_number,
            'course' => $ojtInfo->course,
            'year_level' => $ojtInfo->year_level,
            'company_name' => $ojtInfo->company_name,
            'company_email' => $ojtInfo->company_email,
            'company_address' => $ojtInfo->company_address,
            'supervisor_name' => $ojtInfo->supervisor_name,
            'supervisor_contact' => $ojtInfo->supervisor_contact,
            'ojt_start' => $ojtInfo->ojt_start,
            'ojt_end' => $ojtInfo->ojt_end,
            'required_hours' => $ojtInfo->required_hours,
            'rendered_hours' => $ojtInfo->rendered_hours,
            'ojt_status' => $ojtInfo->ojt_status,
            'archived_by' => $adminId,
            'archive_notes' => $notes ?? 'End of semester deactivation',
        ]);
    }

    /**
     * Reactivate a single student account
     * Useful if a student needs to re-enroll for next semester
     * 
     * @param int $studentId
     * @return array
     */
    public static function reactivateStudent(int $studentId): array
    {
        try {
            $student = User::findOrFail($studentId);

            if ($student->role !== 'student') {
                return [
                    'success' => false,
                    'message' => 'Only student accounts can be reactivated.',
                ];
            }

            $student->update(['status' => 'active']);

            ActivityLogService::logOjtReactivation($student->id, $student->email);

            return [
                'success' => true,
                'message' => 'Student account reactivated successfully.',
            ];

        } catch (\Exception $e) {
            Log::error('Error reactivating student: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error reactivating student: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get summary of past OJT records for a student
     * 
     * @param int $studentId
     * @return array
     */
    public static function getPastOjtRecordsSummary(int $studentId): array
    {
        $pastRecords = PastOjtRecord::where('user_id', $studentId)
            ->orderBy('archived_at', 'desc')
            ->get();

        return [
            'total_past_records' => $pastRecords->count(),
            'records' => $pastRecords,
        ];
    }
}
