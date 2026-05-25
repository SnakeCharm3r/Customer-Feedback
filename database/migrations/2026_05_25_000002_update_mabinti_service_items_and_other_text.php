<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add service_unit_other_text column to feedback_submissions
        Schema::table('feedback_submissions', function (Blueprint $table) {
            $table->string('service_unit_other_text', 255)->nullable()->after('service_units');
        });

        // 2. Rework Mabinti service items: keep only Bags & Accessories group,
        //    merge Clothing items into it, remove Ornaments & Stationery, add Other
        $mabintiId = DB::table('feedback_locations')->where('key', 'mabinti')->value('id');
        if (!$mabintiId) return;

        // Remove Ornaments, Stationery and Clothing group items
        DB::table('location_service_items')
            ->where('location_id', $mabintiId)
            ->whereIn('group_label', ['Ornaments', 'Stationery', 'Clothing'])
            ->delete();

        // Update remaining Bags & Accessories items to sort correctly
        DB::table('location_service_items')
            ->where('location_id', $mabintiId)->where('key', 'print_bags')
            ->update(['sort_order' => 1, 'updated_at' => now()]);

        DB::table('location_service_items')
            ->where('location_id', $mabintiId)->where('key', 'tote_bags')
            ->update(['sort_order' => 2, 'updated_at' => now()]);

        // Add Fashion Clothing and Kitenge Wear under Bags & Accessories
        DB::table('location_service_items')->insert([
            ['location_id' => $mabintiId, 'key' => 'fashion_clothes', 'label' => 'Fashion Clothing', 'group_label' => 'Bags & Accessories', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['location_id' => $mabintiId, 'key' => 'kitenge_wear',    'label' => 'Kitenge Wear',     'group_label' => 'Bags & Accessories', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['location_id' => $mabintiId, 'key' => 'other',           'label' => 'Other',            'group_label' => 'Bags & Accessories', 'is_active' => true, 'sort_order' => 99, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('feedback_submissions', function (Blueprint $table) {
            $table->dropColumn('service_unit_other_text');
        });

        $mabintiId = DB::table('feedback_locations')->where('key', 'mabinti')->value('id');
        if (!$mabintiId) return;

        // Remove added items
        DB::table('location_service_items')
            ->where('location_id', $mabintiId)
            ->whereIn('key', ['fashion_clothes', 'kitenge_wear', 'other'])
            ->delete();

        // Re-insert Ornaments, Stationery items
        DB::table('location_service_items')->insert([
            ['location_id' => $mabintiId, 'key' => 'beaded_jewellery', 'label' => 'Beaded Jewellery',      'group_label' => 'Ornaments',  'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['location_id' => $mabintiId, 'key' => 'bracelets',        'label' => 'Handcrafted Bracelets', 'group_label' => 'Ornaments',  'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['location_id' => $mabintiId, 'key' => 'ornaments_decor',  'label' => 'Decorative Ornaments',  'group_label' => 'Ornaments',  'is_active' => true, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['location_id' => $mabintiId, 'key' => 'greeting_cards',   'label' => 'Greeting Cards',        'group_label' => 'Stationery', 'is_active' => true, 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['location_id' => $mabintiId, 'key' => 'gift_wrap',        'label' => 'Gift Wrapping Service', 'group_label' => 'Stationery', 'is_active' => true, 'sort_order' => 9, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
};
