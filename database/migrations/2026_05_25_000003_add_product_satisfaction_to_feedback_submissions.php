<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_submissions', function (Blueprint $table) {
            $table->tinyInteger('product_satisfied')->nullable()->after('confidentiality_comment'); // 1=yes, 0=no
            $table->string('product_satisfaction_comment', 1000)->nullable()->after('product_satisfied');
        });
    }

    public function down(): void
    {
        Schema::table('feedback_submissions', function (Blueprint $table) {
            $table->dropColumn(['product_satisfied', 'product_satisfaction_comment']);
        });
    }
};
