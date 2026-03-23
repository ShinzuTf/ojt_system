<?php

namespace App\Services;

use App\Models\User;
use App\Models\RequiredDocument;

class TemplateAssignmentService
{
    /**
     * The 4 required templates for all students
     */
    protected static array $requiredTemplates = [
        'Training Agreement (MOA)',
        'NBI Endorsement Letter',
        'Parental Consent Form',
        'Communication Letter (Single)',
    ];

    /**
     * Auto-assign required templates when student completes OJT profile
     */
    public static function assignRequiredTemplatesForStudent(User $student): void
    {
        // Only assign once - check if any templates are already assigned
        $existingCount = RequiredDocument::where('student_id', $student->id)->count();
        
        if ($existingCount > 0) {
            return; // Already assigned
        }

        $adminId = auth()->id() ?? 1; // Default admin if no auth context

        foreach (self::$requiredTemplates as $templateName) {
            RequiredDocument::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'document_name' => $templateName,
                ],
                [
                    'description' => 'Required OJT template - auto-assigned upon profile completion',
                    'assigned_by' => $adminId,
                    'is_fulfilled' => false,
                ]
            );
        }
    }

    /**
     * Get list of required templates
     */
    public static function getRequiredTemplates(): array
    {
        return self::$requiredTemplates;
    }
}
