<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Daily Time Records (DTR) - for tracking student attendance
        Schema::create('daily_time_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->date('record_date');
            $table->time('time_in')->nullable(); // When student logged in
            $table->time('time_out')->nullable(); // When student logged out
            $table->decimal('hours_worked', 5, 2)->nullable(); // Calculated hours
            $table->text('notes')->nullable(); // Any notes by student
            
            // Supervisor verification
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->text('supervisor_remarks')->nullable();
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            
            // Unique per student per day
            $table->unique(['student_id', 'record_date']);
        });

        // DTR Corrections/Adjustments
        Schema::create('dtr_corrections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dtr_id');
            $table->unsignedBigInteger('student_id');
            $table->time('original_time_in');
            $table->time('new_time_in');
            $table->time('original_time_out');
            $table->time('new_time_out');
            $table->text('reason');
            
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();

            $table->foreign('dtr_id')->references('id')->on('daily_time_records')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dtr_corrections');
        Schema::dropIfExists('daily_time_records');
    }
};
