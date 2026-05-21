<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_locations', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->string('label', 160);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed existing hard-coded locations
        DB::table('feedback_locations')->insert([
            ['key' => 'hq',     'label' => 'CCBRT Hospital HQ',    'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'moshi',  'label' => 'CCBRT Moshi',          'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tegeta', 'label' => 'CCBRT Tegeta Branch',  'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_locations');
    }
};
