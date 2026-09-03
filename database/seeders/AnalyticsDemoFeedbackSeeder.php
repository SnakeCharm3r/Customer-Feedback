<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnalyticsDemoFeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['opd', 'positive', 'client_experience', 'walk_in', 'hq', 'The reception team guided me clearly and the visit was smooth.'],
            ['opd', 'positive', 'client_satisfaction', 'walk_in', 'hq', 'I received all the services I needed and I am satisfied.'],
            ['opd', 'positive', 'customer_care_staff', 'portal', 'hq', 'The nurses were respectful, patient, and helpful.'],
            ['opd', 'positive', 'staff_appreciation', 'calls', 'hq', 'Thank you to the eye clinic team for the excellent care.'],
            ['opd', 'positive', 'enviro_housekeeping', 'walk_in', 'moshi', 'The clinic was clean and comfortable.'],
            ['opd', 'positive', 'client_outcome', 'manual', 'hq', 'The treatment has improved my condition.'],
            ['opd', 'negative', 'waiting_time', 'walk_in', 'hq', 'The waiting time before seeing the doctor was too long.'],
            ['opd', 'negative', 'inadequate_information', 'calls', 'tegeta', 'I was not informed when the clinic schedule changed.'],
            ['opd', 'negative', 'billing_issues', 'portal', 'hq', 'The billing explanation was not clear.'],
            ['opd', 'neutral', 'client_experience', 'walk_in', 'hq', 'The service was acceptable but directions could be clearer.'],
            ['opd', 'neutral', 'waiting_time', 'manual', 'hq', 'Waiting time was average today.'],

            ['ipd', 'positive', 'client_satisfaction', 'ward_visit', 'hq', 'The patient is comfortable and satisfied with ward care.'],
            ['ipd', 'positive', 'customer_care_staff', 'ward_visit', 'hq', 'Ward staff responded quickly whenever we needed help.'],
            ['ipd', 'positive', 'enviro_housekeeping', 'ward_visit', 'moshi', 'The ward and washrooms were kept clean.'],
            ['ipd', 'positive', 'client_outcome', 'calls', 'hq', 'Recovery is progressing well after admission.'],
            ['ipd', 'negative', 'medication_issues', 'ward_visit', 'hq', 'Medication delivery should be explained more clearly.'],
            ['ipd', 'negative', 'few_staff', 'ward_visit', 'hq', 'More staff are needed during the evening shift.'],
            ['ipd', 'neutral', 'client_experience', 'ward_visit', 'tegeta', 'The ward experience was generally fair.'],

            ['theatre', 'positive', 'client_experience', 'manual', 'hq', 'The theatre team explained the procedure and made me comfortable.'],
            ['theatre', 'positive', 'staff_appreciation', 'walk_in', 'hq', 'I appreciate the surgical team for their professionalism.'],
            ['theatre', 'negative', 'delayed_slow_service', 'walk_in', 'hq', 'The procedure started much later than scheduled.'],
            ['theatre', 'negative', 'inadequate_information', 'walk_in', 'moshi', 'The family needed more updates after the procedure.'],
            ['theatre', 'neutral', 'client_experience', 'walk_in', 'hq', 'The procedure went as expected with no major concerns.'],

            ['other', 'positive', 'general_positive_feedback', 'manual', 'mabinti', 'The Mabinti Centre products and support were very helpful.'],
        ];

        $dates = [
            '2026-07-08 09:15:00', '2026-07-15 11:30:00', '2026-07-22 14:10:00',
            '2026-08-03 08:20:00', '2026-08-03 10:45:00', '2026-08-04 09:05:00',
            '2026-08-04 12:30:00', '2026-08-05 08:50:00', '2026-08-05 13:15:00',
            '2026-08-06 09:40:00', '2026-08-06 15:00:00', '2026-08-07 08:10:00',
            '2026-08-07 10:25:00', '2026-08-10 09:30:00', '2026-08-11 11:05:00',
            '2026-08-12 12:45:00', '2026-08-13 14:20:00', '2026-08-14 09:00:00',
            '2026-08-17 08:35:00', '2026-08-18 10:50:00', '2026-08-19 13:25:00',
            '2026-09-01 09:10:00', '2026-09-01 14:40:00', '2026-09-02 08:30:00',
        ];

        foreach ($rows as $index => [$category, $sentiment, $theme, $source, $location, $message]) {
            $createdAt = Carbon::parse($dates[$index]);
            $isMabinti = $location === 'mabinti';
            $feedbackType = match ($sentiment) {
                'positive' => 'compliment',
                'negative' => 'complaint',
                default => 'suggestion',
            };

            DB::table('feedback_submissions')->updateOrInsert(
                ['reference_no' => 'DEMO-AN-' . str_pad((string)($index + 1), 3, '0', STR_PAD_LEFT)],
                [
                    'patient_name' => 'Demo Customer ' . ($index + 1),
                    'phone' => '071200' . str_pad((string)($index + 1), 4, '0', STR_PAD_LEFT),
                    'service_units' => json_encode([$category === 'theatre' ? 'theatre' : ($category === 'ipd' ? 'general_ward' : 'eye')]),
                    'service_category' => $category,
                    'department_type' => $category,
                    'wing' => $category === 'ipd' ? 'standard' : 'private',
                    'theme' => $theme,
                    'sentiment' => $sentiment,
                    'feedback_type' => $feedbackType,
                    'service_rating' => $sentiment === 'positive' ? 'good' : ($sentiment === 'negative' ? 'poor' : 'average'),
                    'visit_date' => $createdAt->toDateString(),
                    'location' => $location,
                    'message' => $message,
                    'overall_experience' => $message,
                    'is_urgent' => false,
                    'consent_given' => true,
                    'source' => $source,
                    'status' => $index % 4 === 0 ? 'under_review' : ($index % 5 === 0 ? 'responded' : 'new'),
                    'product_satisfied' => $isMabinti ? 1 : null,
                    'product_satisfaction_comment' => $isMabinti ? 'The products met my expectations.' : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );
        }

        $this->command?->info(count($rows) . ' analytics demo feedback records are ready.');
    }
}
