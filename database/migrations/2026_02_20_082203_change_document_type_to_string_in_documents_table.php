<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Change document_type from enum to string
            $table->string('document_type')->change();
            
            // Add required_document_id to link submission to the admin's slot
            $table->unsignedBigInteger('required_document_id')->nullable()->after('user_id');
            $table->foreign('required_document_id')->references('id')->on('required_documents')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['required_document_id']);
            $table->dropColumn('required_document_id');
            // Reverting to string is fine, revert to enum would require specific logic if needed
        });
    }
};
