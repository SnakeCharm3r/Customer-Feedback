<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('home_hero_title')->nullable()->after('contact_phone');
            $table->text('home_hero_subtitle')->nullable()->after('home_hero_title');
            $table->string('home_primary_cta_label')->nullable()->after('home_hero_subtitle');
            $table->string('home_secondary_cta_label')->nullable()->after('home_primary_cta_label');
            $table->text('footer_about_text')->nullable()->after('home_secondary_cta_label');
            $table->string('footer_location_text')->nullable()->after('footer_about_text');
            $table->string('footer_hours_text')->nullable()->after('footer_location_text');
            $table->text('footer_privacy_text')->nullable()->after('footer_hours_text');
            $table->string('privacy_policy_url')->nullable()->after('footer_privacy_text');
            $table->string('terms_of_use_url')->nullable()->after('privacy_policy_url');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_hero_title',
                'home_hero_subtitle',
                'home_primary_cta_label',
                'home_secondary_cta_label',
                'footer_about_text',
                'footer_location_text',
                'footer_hours_text',
                'footer_privacy_text',
                'privacy_policy_url',
                'terms_of_use_url',
            ]);
        });
    }
};