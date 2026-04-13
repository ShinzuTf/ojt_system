<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('target_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('activity'); // e.g., 'user_login', 'document_generated', 'evaluation_submitted'
            $table->string('module')->nullable(); // e.g., 'auth', 'document', 'evaluation', 'admin'
            $table->string('action'); // e.g., 'login', 'create', 'update', 'delete', 'view'
            $table->string('description')->nullable(); // e.g., 'User logged in successfully'
            $table->json('data')->nullable(); // Additional data (old values, new values, etc.)
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('status')->default('success'); // success, failed, pending
            $table->timestamps();

            // Indexes for common queries
            $table->index('user_id');
            $table->index('activity');
            $table->index('module');
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
