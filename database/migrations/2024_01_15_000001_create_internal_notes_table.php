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
        Schema::create('internal_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_id')->constrained('feedback_submissions')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users');
            $table->text('content');
            $table->boolean('is_coo_comment')->default(false);
            $table->timestamps();

            $table->index('feedback_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_notes');
    }
};
