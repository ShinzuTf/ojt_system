<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Evaluations table — supervisor/coordinator evaluates trainee's performance during OJT
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trainee_id'); // student being evaluated
            $table->unsignedBigInteger('supervisor_id'); // supervisor/coordinator doing the evaluation
            
            // Performance Evaluation Fields
            $table->date('evaluation_date'); // date of evaluation
            $table->text('strengths')->nullable(); // what the trainee did well
            $table->text('areas_for_improvement')->nullable(); // areas the trainee should improve
            $table->text('skills_to_develop')->nullable(); // skills the trainee needs to work on
            $table->text('overall_comments')->nullable(); // supervisor's overall feedback
            
            // Rating fields
            $table->tinyInteger('technical_skills_rating')->nullable(); // 1-5 scale
            $table->tinyInteger('communication_rating')->nullable(); // 1-5 scale
            $table->tinyInteger('teamwork_rating')->nullable(); // 1-5 scale
            $table->tinyInteger('professionalism_rating')->nullable(); // 1-5 scale
            $table->tinyInteger('initiative_rating')->nullable(); // 1-5 scale
            
            // Verification
            $table->enum('status', ['pending', 'approved', 'needs_revision'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();

            $table->foreign('trainee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('cascade');
            
            // Composite unique to prevent duplicate evaluations on same date for same trainee
            $table->unique(['trainee_id', 'supervisor_id', 'evaluation_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
