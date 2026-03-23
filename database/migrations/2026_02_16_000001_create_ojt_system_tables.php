<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modify users table for OJT system
        Schema::table('users', function (Blueprint $table) {
            // Drop the default 'name' column
            $table->dropColumn('name');

            // Add structured name fields
            $table->string('fname', 100)->after('id');
            $table->string('mname', 100)->nullable()->after('fname');
            $table->string('lname', 100)->after('mname');
            $table->string('suffix', 10)->nullable()->after('lname');

            // Student fields
            $table->string('student_number', 20)->nullable()->unique()->after('email');
            $table->string('course', 10)->nullable()->after('student_number');
            $table->tinyInteger('year_level')->nullable()->after('course');
            $table->unsignedBigInteger('coordinator_id')->nullable()->after('year_level');

            // System fields
            $table->enum('role', ['student', 'admin', 'coordinator'])->default('student')->after('coordinator_id');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
        });

        // Documents table
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('document_type', [
                'application_letter',
                'training_agreement',
                'consent_form',
                'progress_report',
                'accomplishment_report',
                'final_evaluation',
            ]);
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 10); // pdf, docx
            $table->integer('file_size')->nullable(); // in bytes
            $table->enum('status', [
                'submitted',
                'under_review',
                'approved',
                'rejected',
            ])->default('submitted');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });

        // Notifications table
        Schema::create('notifications_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'success', 'warning', 'danger'])->default('info');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Templates table
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 10);
            $table->integer('file_size')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
        Schema::dropIfExists('notifications_log');
        Schema::dropIfExists('documents');

        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->dropColumn([
                'fname', 'mname', 'lname', 'suffix',
                'student_number', 'course', 'year_level',
                'coordinator_id', 'role', 'status',
            ]);
        });
    }
};
