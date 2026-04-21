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
        Schema::create('feedback_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->string('patient_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('service_category', [
                'outpatient',
                'inpatient',
                'eye_surgery',
                'rehabilitation',
                'pharmacy',
                'reception_admin',
                'billing',
                'other'
            ]);
            $table->enum('feedback_type', ['compliment', 'complaint', 'suggestion', 'enquiry']);
            $table->date('visit_date')->nullable();
            $table->text('message');
            $table->boolean('is_urgent')->default(false);
            $table->string('attachment_path')->nullable();
            $table->boolean('consent_given')->default(false);
            $table->enum('status', ['new', 'under_review', 'responded', 'closed'])->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('reference_no');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_submissions');
    }
};
