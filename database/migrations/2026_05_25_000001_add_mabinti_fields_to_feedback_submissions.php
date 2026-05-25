<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_submissions', function (Blueprint $table) {
            $table->string('organization_name', 255)->nullable()->after('patient_name');
            $table->string('submitter_location_text', 255)->nullable()->after('organization_name');
        });
    }

    public function down(): void
    {
        Schema::table('feedback_submissions', function (Blueprint $table) {
            $table->dropColumn(['organization_name', 'submitter_location_text']);
        });
    }
};
