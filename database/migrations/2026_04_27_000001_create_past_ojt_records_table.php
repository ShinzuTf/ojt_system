<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create past_ojt_records table to store archived OJT records
        Schema::create('past_ojt_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('student_number', 30)->nullable();
            $table->string('course', 15)->nullable(); // BSIT, BSCS, BSIS, ACT
            $table->tinyInteger('year_level')->nullable(); // 3 or 4
            $table->string('company_name')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_address')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('supervisor_contact')->nullable();
            $table->date('ojt_start')->nullable();
            $table->date('ojt_end')->nullable();
            $table->integer('required_hours')->nullable()->default(720);
            $table->integer('rendered_hours')->nullable()->default(0);
            $table->enum('ojt_status', ['pending', 'ongoing', 'completed', 'withdrawn'])->default('completed');
            
            // Archival metadata
            $table->timestamp('archived_at')->useCurrent();
            $table->unsignedBigInteger('archived_by')->nullable(); // admin who archived
            $table->text('archive_notes')->nullable();
            
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('archived_by')->references('id')->on('users')->onNullDelete();
            $table->index('user_id');
            $table->index('archived_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('past_ojt_records');
    }
};
