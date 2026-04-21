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
        Schema::table('feedback_submissions', function (Blueprint $table) {
            $table->json('service_units')->nullable()->after('phone');
            $table->string('service_rating')->nullable()->after('feedback_type');
            $table->boolean('confidentiality_respected')->nullable()->after('service_rating');
            $table->text('confidentiality_comment')->nullable()->after('confidentiality_respected');
            $table->text('overall_experience')->nullable()->after('visit_date');
            $table->text('improvement_suggestion')->nullable()->after('overall_experience');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'service_units',
                'service_rating',
                'confidentiality_respected',
                'confidentiality_comment',
                'overall_experience',
                'improvement_suggestion',
            ]);
        });
    }
};
