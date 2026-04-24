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
        Schema::create('escalations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('feedback_id')->constrained('feedback_submissions')->cascadeOnDelete();
            $table->foreignId('hod_id')->constrained('hods')->cascadeOnDelete();
            $table->foreignId('escalated_by')->constrained('users')->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->text('message')->nullable();
            $table->string('status')->default('pending');
            $table->text('hod_response')->nullable();
            $table->string('hod_name')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('escalated_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalations');
    }
};
