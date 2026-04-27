<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // OJT Placements - Links student to company for their OJT training
        Schema::create('ojt_placements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('supervisor_id'); // company's supervisor
            $table->unsignedBigInteger('coordinator_id'); // school's coordinator
            
            // Placement duration
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_required_hours')->default(720);
            
            // Status
            $table->enum('status', ['active', 'completed', 'cancelled', 'suspended'])->default('active');
            
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('coordinator_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Certifications - Company submits completion certification to school
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('placement_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('issued_by'); // company supervisor
            $table->unsignedBigInteger('verified_by')->nullable(); // school coordinator verification
            
            // Certification details
            $table->date('certification_date');
            $table->integer('actual_hours_worked');
            $table->decimal('final_rating', 3, 2)->nullable(); // 1-5 average rating
            $table->text('remarks')->nullable();
            
            // Certification status
            $table->enum('status', ['submitted', 'pending_verification', 'verified', 'approved'])->default('submitted');
            $table->timestamp('verified_at')->nullable();
            
            // Certificate file
            $table->string('certificate_path')->nullable();
            $table->string('certificate_file_name')->nullable();
            
            $table->timestamps();

            $table->foreign('placement_id')->references('id')->on('ojt_placements')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('issued_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });

        // Completion records - track if student completed their OJT
        Schema::create('completion_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('placement_id');
            
            // Completion info
            $table->date('completion_date');
            $table->boolean('met_requirements')->default(false);
            $table->integer('total_hours_completed');
            $table->decimal('final_grade', 3, 2)->nullable();
            
            // Verification
            $table->enum('status', ['pending', 'approved', 'conditional'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('approval_remarks')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            // Result
            $table->boolean('is_completed')->default(false);
            $table->string('certificate_number')->nullable()->unique();
            
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('placement_id')->references('id')->on('ojt_placements')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('completion_records');
        Schema::dropIfExists('certifications');
        Schema::dropIfExists('ojt_placements');
    }
};
