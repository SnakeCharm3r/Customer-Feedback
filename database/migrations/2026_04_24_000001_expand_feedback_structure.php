<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. First make the column nullable so we can clear old enum values
        DB::statement("ALTER TABLE feedback_submissions MODIFY service_category VARCHAR(50) NULL DEFAULT NULL");

        // 2. Map old values to new equivalents where possible, null the rest
        DB::statement("UPDATE feedback_submissions SET service_category = CASE
            WHEN service_category IN ('outpatient','eye_surgery','rehabilitation','pharmacy','reception_admin','billing') THEN 'opd'
            WHEN service_category = 'inpatient' THEN 'ipd'
            ELSE 'other'
        END");

        // 3. Alter to new enum
        DB::statement("ALTER TABLE feedback_submissions MODIFY service_category ENUM(
            'opd','ipd','theatre','mixed','other'
        ) NULL DEFAULT NULL");

        Schema::table('feedback_submissions', function (Blueprint $table) {
            // 2. Department type: OPD / IPD / Theatre (auto-derived)
            $table->enum('department_type', ['opd', 'ipd', 'theatre', 'mixed', 'other'])
                  ->nullable()->after('service_units');

            // 3. Wing: Private / Maternity / Standard (admin can set; sometimes derivable)
            $table->enum('wing', ['private', 'maternity', 'standard', 'mixed', 'other'])
                  ->nullable()->after('department_type');

            // 4. Theme: QA-officer assigned categorisation
            $table->string('theme')->nullable()->after('wing');

            // 5. Sentiment: Positive / Negative — defaults from feedback_type, overridable
            $table->enum('sentiment', ['positive', 'negative', 'neutral'])
                  ->nullable()->after('theme');
        });
    }

    public function down(): void
    {
        Schema::table('feedback_submissions', function (Blueprint $table) {
            $table->dropColumn(['department_type', 'wing', 'theme', 'sentiment']);
        });

        DB::statement("ALTER TABLE feedback_submissions MODIFY service_category ENUM(
            'outpatient','inpatient','eye_surgery','rehabilitation','pharmacy','reception_admin','billing','other'
        ) NULL DEFAULT NULL");
    }
};
