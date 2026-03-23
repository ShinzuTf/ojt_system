<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove coordinator_id, year_level, course, student_number from users
        // (these move to ojt_info table)
        Schema::table('users', function (Blueprint $table) {
            // Drop columns that are now in ojt_info
            $table->dropColumn(['student_number', 'course', 'year_level', 'coordinator_id']);
        });

        // OJT Info table — one per student, filled in by the student themselves
        Schema::create('ojt_info', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('student_number', 30)->nullable();
            $table->string('course', 15)->nullable(); // BSIT, BSCS, BSIS, ACT
            $table->tinyInteger('year_level')->nullable(); // 3 or 4
            $table->string('company_name')->nullable();
            $table->string('company_email')->nullable(); // company Gmail/email
            $table->string('company_address')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('supervisor_contact')->nullable();
            $table->date('ojt_start')->nullable();
            $table->date('ojt_end')->nullable();
            $table->integer('required_hours')->nullable()->default(486);
            $table->integer('rendered_hours')->nullable()->default(0);
            $table->enum('ojt_status', ['pending', 'ongoing', 'completed', 'withdrawn'])->default('pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Required Documents table — admin assigns additional required docs to a student
        Schema::create('required_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // the student it targets
            $table->string('document_name'); // e.g. "Birth Certificate", "Medical Certificate"
            $table->text('description')->nullable();
            $table->boolean('is_fulfilled')->default(false);
            $table->unsignedBigInteger('assigned_by'); // admin user id
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('required_documents');
        Schema::dropIfExists('ojt_info');

        Schema::table('users', function (Blueprint $table) {
            $table->string('student_number', 20)->nullable()->unique();
            $table->string('course', 10)->nullable();
            $table->tinyInteger('year_level')->nullable();
            $table->unsignedBigInteger('coordinator_id')->nullable();
        });
    }
};
