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
        Schema::create('patient_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_id')->constrained('feedback_submissions')->cascadeOnDelete();
            $table->text('content');
            $table->foreignId('sent_by')->constrained('users');
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->index('feedback_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_responses');
    }
};
