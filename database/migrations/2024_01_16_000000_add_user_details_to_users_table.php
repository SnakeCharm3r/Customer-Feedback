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
        Schema::table('users', function (Blueprint $table) {
            $table->string('fname')->nullable()->after('name');
            $table->string('mname')->nullable()->after('fname');
            $table->string('lname')->nullable()->after('mname');
            $table->date('dob')->nullable()->after('lname');
            $table->enum('role', ['admin', 'qa_officer', 'call_center', 'qa_hod', 'coo'])->default('qa_officer')->after('dob');
            $table->boolean('is_active')->default(false)->after('role');
            $table->boolean('is_first_user')->default(false)->after('is_active');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('is_first_user');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['fname', 'mname', 'lname', 'dob', 'role', 'is_active', 'is_first_user', 'approved_by', 'approved_at']);
        });
    }
};
