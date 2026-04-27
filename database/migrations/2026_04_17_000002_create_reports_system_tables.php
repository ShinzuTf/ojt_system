<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Reports - Weekly and Monthly reports from students/supervisors
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('submitted_by'); // student or supervisor
            $table->enum('report_type', ['weekly', 'monthly', 'incident'])->default('weekly');
            $table->date('report_period_start');
            $table->date('report_period_end');
            
            // Report content
            $table->text('accomplishments'); // What was accomplished
            $table->text('activities'); // Activities done
            $table->text('challenges')->nullable(); // Challenges faced
            $table->text('learnings')->nullable(); // Key learnings
            $table->text('recommendations')->nullable(); // Recommendations
            
            // Attachments
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            
            // Review workflow
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected'])->default('draft');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->text('reviewer_comments')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            // Escalation (if needed)
            $table->unsignedBigInteger('escalated_to')->nullable();
            $table->text('escalation_reason')->nullable();
            $table->timestamp('escalated_at')->nullable();
            
            $table->timestamps();

            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('escalated_to')->references('id')->on('users')->onDelete('set null');
        });

        // Report history/versions for tracking changes
        Schema::create('report_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->text('changes_description');
            $table->unsignedBigInteger('changed_by');
            $table->timestamps();

            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_histories');
        Schema::dropIfExists('reports');
    }
};
