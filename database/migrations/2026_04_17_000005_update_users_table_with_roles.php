<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Update role enum to include all 4 user types
            // Drop old role column if it exists
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            
            // Add new role with 4 user types
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['student', 'supervisor', 'coordinator', 'admin'])
                    ->default('student');
            }
            
            // Add company association for supervisors
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'company_position')) {
                $table->string('company_position')->nullable();
            }
            
            // Add department/unit info
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable();
            }
            if (!Schema::hasColumn('users', 'unit')) {
                $table->string('unit')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['company_id', 'company_name', 'company_position', 'department', 'unit']);
            
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            
            $table->enum('role', ['student', 'admin', 'coordinator'])
                ->default('student')
                ->after('year_level');
        });
    }
};
