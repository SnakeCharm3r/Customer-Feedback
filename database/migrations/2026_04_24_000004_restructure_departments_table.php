<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['category', 'wing']);
            $table->json('categories')->nullable()->after('slug');
            $table->foreignId('hod_id')->nullable()->after('description')
                  ->constrained('hods')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['hod_id']);
            $table->dropColumn(['categories', 'hod_id']);
            $table->enum('category', ['opd', 'ipd', 'theatre', 'other'])->default('opd')->after('slug');
            $table->enum('wing', ['private', 'maternity', 'standard', 'other'])->nullable()->after('category');
        });
    }
};
