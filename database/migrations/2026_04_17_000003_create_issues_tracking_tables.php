<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Issues - Supervisor can report absences, drops, transfers, etc.
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('reported_by'); // supervisor
            $table->unsignedBigInteger('company_id')->nullable(); // which company
            
            // Issue details
            $table->enum('issue_type', ['absence', 'drop', 'transfer', 'behavioral', 'performance', 'other'])->default('other');
            $table->date('issue_date');
            $table->text('description');
            $table->text('action_taken')->nullable();
            
            // Evidence/attachments
            $table->string('attachment_path')->nullable();
            
            // Status tracking
            $table->enum('status', ['reported', 'acknowledged', 'investigating', 'resolved', 'closed'])->default('reported');
            $table->unsignedBigInteger('assigned_to')->nullable(); // coordinator assigned to handle
            $table->text('resolution_notes')->nullable();
            $table->date('resolution_date')->nullable();
            
            // For drops/transfers
            $table->date('effective_date')->nullable();
            $table->enum('student_status', ['active', 'dropped', 'transferred', 'suspended'])->nullable();
            
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });

        // Issue history for tracking updates
        Schema::create('issue_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('issue_id');
            $table->unsignedBigInteger('updated_by');
            $table->text('update_description');
            $table->timestamps();

            $table->foreign('issue_id')->references('id')->on('issues')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_updates');
        Schema::dropIfExists('issues');
    }
};
