<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name')->default('CCBRT Hospital');
            $table->string('portal_name')->default('Customer Feedback Portal');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('mail_from_name')->default('CCBRT Hospital Quality Assurance');
            $table->string('mail_from_address')->nullable();
            $table->unsignedTinyInteger('login_max_attempts')->default(5);
            $table->unsignedSmallInteger('login_lockout_minutes')->default(1);
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};