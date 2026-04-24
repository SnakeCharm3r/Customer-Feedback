<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_submissions', function (Blueprint $table) {
            $table->foreignId('department_id')
                  ->nullable()
                  ->after('service_units')
                  ->constrained('departments')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('feedback_submissions', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Department::class);
            $table->dropColumn('department_id');
        });
    }
};
