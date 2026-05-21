<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_service_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('feedback_locations')->cascadeOnDelete();
            $table->string('key', 80);
            $table->string('label', 160);
            $table->string('group_label', 120)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['location_id', 'key']);
        });

        // Seed Mabinti Center items (location key = 'mabinti')
        $mabintiId = DB::table('feedback_locations')->where('key', 'mabinti')->value('id');
        // Seed Tegeta optical items (location key = 'tegeta')
        $tegetaId  = DB::table('feedback_locations')->where('key', 'tegeta')->value('id');

        if ($mabintiId) {
            DB::table('location_service_items')->insert([
                ['location_id' => $mabintiId, 'key' => 'print_bags',       'label' => 'Cultured Print Bags',    'group_label' => 'Bags & Accessories', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $mabintiId, 'key' => 'tote_bags',        'label' => 'Tote Bags',              'group_label' => 'Bags & Accessories', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $mabintiId, 'key' => 'fashion_clothes',  'label' => 'Fashion Clothing',       'group_label' => 'Clothing',           'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $mabintiId, 'key' => 'kitenge_wear',     'label' => 'Kitenge Wear',           'group_label' => 'Clothing',           'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $mabintiId, 'key' => 'beaded_jewellery', 'label' => 'Beaded Jewellery',       'group_label' => 'Ornaments',          'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $mabintiId, 'key' => 'bracelets',        'label' => 'Handcrafted Bracelets',  'group_label' => 'Ornaments',          'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $mabintiId, 'key' => 'ornaments_decor',  'label' => 'Decorative Ornaments',   'group_label' => 'Ornaments',          'is_active' => true, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $mabintiId, 'key' => 'greeting_cards',   'label' => 'Greeting Cards',         'group_label' => 'Stationery',         'is_active' => true, 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $mabintiId, 'key' => 'gift_wrap',        'label' => 'Gift Wrapping Service',  'group_label' => 'Stationery',         'is_active' => true, 'sort_order' => 9, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($tegetaId) {
            DB::table('location_service_items')->insert([
                ['location_id' => $tegetaId, 'key' => 'prescription_glasses', 'label' => 'Prescription Glasses',    'group_label' => 'Eyewear',      'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $tegetaId, 'key' => 'sunglasses',           'label' => 'Sunglasses',              'group_label' => 'Eyewear',      'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $tegetaId, 'key' => 'contact_lenses',       'label' => 'Contact Lenses',          'group_label' => 'Eyewear',      'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $tegetaId, 'key' => 'reading_glasses',      'label' => 'Reading Glasses',         'group_label' => 'Eyewear',      'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $tegetaId, 'key' => 'eye_exam',             'label' => 'Eye Examination',         'group_label' => 'Eye Services', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $tegetaId, 'key' => 'lens_fitting',         'label' => 'Lens Fitting & Adjustments', 'group_label' => 'Eye Services', 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $tegetaId, 'key' => 'frame_repair',         'label' => 'Frame Repairs',           'group_label' => 'Eye Services', 'is_active' => true, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $tegetaId, 'key' => 'lens_replacement',     'label' => 'Lens Replacement',        'group_label' => 'Eye Services', 'is_active' => true, 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
                ['location_id' => $tegetaId, 'key' => 'optical_accessories',  'label' => 'Optical Accessories',     'group_label' => 'Accessories',  'is_active' => true, 'sort_order' => 9, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('location_service_items');
    }
};
