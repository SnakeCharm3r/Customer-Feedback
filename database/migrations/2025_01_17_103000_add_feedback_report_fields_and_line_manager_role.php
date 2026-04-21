<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('feedback_submissions', 'source')) {
                $table->string('source')->default('portal')->after('consent_given');
            }
            if (!Schema::hasColumn('feedback_submissions', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('feedback_submissions', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
        });

        if (Schema::hasColumn('feedback_submissions', 'source')) {
            DB::table('feedback_submissions')->whereNull('source')->update([
                'source' => 'portal',
            ]);
        }

        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'qa_officer', 'call_center', 'qa_hod', 'coo', 'line_manager') NOT NULL DEFAULT 'qa_officer'");
        } catch (\Exception $e) {
            // Role enum may already be extended
        }
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'qa_officer', 'call_center', 'qa_hod', 'coo') NOT NULL DEFAULT 'qa_officer'");

        Schema::table('feedback_submissions', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropIndex(['source']);
            $table->dropIndex(['reviewed_at']);
            $table->dropColumn(['source', 'reviewed_by', 'reviewed_at']);
        });
    }
};
