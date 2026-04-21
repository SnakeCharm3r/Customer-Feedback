<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('feedback_submissions', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('source')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('feedback_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('feedback_submissions', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};
