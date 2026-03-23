<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update evaluations table to use performance-based fields instead of DTR
        Schema::table('evaluations', function (Blueprint $table) {
            // Drop old DTR columns if they exist
            if (Schema::hasColumn('evaluations', 'hours_rendered')) {
                $table->dropColumn('hours_rendered');
            }
            if (Schema::hasColumn('evaluations', 'tasks_accomplished')) {
                $table->dropColumn('tasks_accomplished');
            }
            if (Schema::hasColumn('evaluations', 'evaluation_comments')) {
                $table->dropColumn('evaluation_comments');
            }
            if (Schema::hasColumn('evaluations', 'attendance_rating')) {
                $table->dropColumn('attendance_rating');
            }
            if (Schema::hasColumn('evaluations', 'performance_rating')) {
                $table->dropColumn('performance_rating');
            }
            if (Schema::hasColumn('evaluations', 'conduct_rating')) {
                $table->dropColumn('conduct_rating');
            }
        });

        Schema::table('evaluations', function (Blueprint $table) {
            // Add new performance evaluation fields if they don't exist
            if (!Schema::hasColumn('evaluations', 'strengths')) {
                $table->text('strengths')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'areas_for_improvement')) {
                $table->text('areas_for_improvement')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'skills_to_develop')) {
                $table->text('skills_to_develop')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'overall_comments')) {
                $table->text('overall_comments')->nullable();
            }
            
            // Add new rating fields
            if (!Schema::hasColumn('evaluations', 'technical_skills_rating')) {
                $table->tinyInteger('technical_skills_rating')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'communication_rating')) {
                $table->tinyInteger('communication_rating')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'teamwork_rating')) {
                $table->tinyInteger('teamwork_rating')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'professionalism_rating')) {
                $table->tinyInteger('professionalism_rating')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'initiative_rating')) {
                $table->tinyInteger('initiative_rating')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // Drop new performance columns
            if (Schema::hasColumn('evaluations', 'strengths')) {
                $table->dropColumn('strengths');
            }
            if (Schema::hasColumn('evaluations', 'areas_for_improvement')) {
                $table->dropColumn('areas_for_improvement');
            }
            if (Schema::hasColumn('evaluations', 'skills_to_develop')) {
                $table->dropColumn('skills_to_develop');
            }
            if (Schema::hasColumn('evaluations', 'overall_comments')) {
                $table->dropColumn('overall_comments');
            }
            if (Schema::hasColumn('evaluations', 'technical_skills_rating')) {
                $table->dropColumn('technical_skills_rating');
            }
            if (Schema::hasColumn('evaluations', 'communication_rating')) {
                $table->dropColumn('communication_rating');
            }
            if (Schema::hasColumn('evaluations', 'teamwork_rating')) {
                $table->dropColumn('teamwork_rating');
            }
            if (Schema::hasColumn('evaluations', 'professionalism_rating')) {
                $table->dropColumn('professionalism_rating');
            }
            if (Schema::hasColumn('evaluations', 'initiative_rating')) {
                $table->dropColumn('initiative_rating');
            }
        });

        // Restore old DTR columns
        Schema::table('evaluations', function (Blueprint $table) {
            $table->integer('hours_rendered')->nullable();
            $table->text('tasks_accomplished')->nullable();
            $table->text('evaluation_comments')->nullable();
            $table->tinyInteger('attendance_rating')->nullable();
            $table->tinyInteger('performance_rating')->nullable();
            $table->tinyInteger('conduct_rating')->nullable();
        });
    }
};
