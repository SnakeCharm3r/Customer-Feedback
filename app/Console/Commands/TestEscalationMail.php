<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\EscalationMail;
use App\Models\Escalation;
use App\Models\Feedback;
use App\Models\Hod;

class TestEscalationMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'escalation:test-mail {hod_id? : HOD ID to send test to} {--dry-run : Preview without sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the escalation email system';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('========================================');
        $this->info('   ESCALATION EMAIL SYSTEM TEST');
        $this->info('========================================');
        $this->newLine();

        // Check mail configuration
        $this->info('Mail Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['MAIL_MAILER', config('mail.default')],
                ['MAIL_HOST', config('mail.mailers.smtp.host')],
                ['MAIL_PORT', config('mail.mailers.smtp.port')],
                ['MAIL_FROM_ADDRESS', config('mail.from.address')],
                ['MAIL_FROM_NAME', config('mail.from.name')],
            ]
        );

        // Check Mailable class exists
        $this->newLine();
        $this->info('Mailable Classes:');
        $escalationMail = class_exists('App\Mail\EscalationMail');
        $this->table(
            ['Class', 'Status'],
            [
                ['App\Mail\EscalationMail', $escalationMail ? '✅ Available' : '❌ Missing'],
            ]
        );

        if (!$escalationMail) {
            $this->error('EscalationMail class not found!');
            return self::FAILURE;
        }

        // Check for HODs
        $this->newLine();
        $this->info('HODs Available:');
        $hods = Hod::where('is_active', true)->take(5)->get();
        $this->table(
            ['ID', 'Name', 'Department', 'Email'],
            $hods->map(fn($h) => [$h->id, $h->name, $h->department, $h->email])->toArray()
        );

        if ($hods->isEmpty()) {
            $this->error('No active HODs found in the database!');
            return self::FAILURE;
        }

        // Get HOD to test with
        $hodId = $this->argument('hod_id');
        if (!$hodId) {
            // Try to find Paul Kasanga specifically
            $hod = Hod::where('name', 'like', '%Kasanga%')->where('is_active', true)->first();
            if ($hod) {
                $this->info('Using Paul Kasanga for test: ' . $hod->name . ' (' . $hod->email . ')');
            } else {
                $hod = $hods->first();
                $this->warn('Paul Kasanga not found. Using first available HOD: ' . $hod->name);
            }
        } else {
            $hod = Hod::find($hodId);
            if (!$hod) {
                $this->error('HOD ID ' . $hodId . ' not found!');
                return self::FAILURE;
            }
        }

        // Create mock escalation data for testing
        $mockEscalation = new Escalation([
            'reference'    => 'ESC-TEST-' . now()->format('Ymd-His'),
            'token'        => bin2hex(random_bytes(32)),
            'hod_id'       => $hod->id,
            'message'      => 'This is a test escalation message.',
            'status'       => 'pending',
        ]);
        $mockEscalation->escalated_at = now();
        $mockEscalation->setRelation('hod', $hod);

        $mockFeedback = new Feedback([
            'reference_no'       => 'FB-TEST-' . now()->format('Ymd-His'),
            'feedback_type'      => 'complaint',
            'service_category'   => 'opd',
            'source'             => 'walk_in',
            'location'           => 'hq',
            'overall_experience' => 'This is a test feedback description for escalation testing purposes.',
            'service_rating'     => 'good',
        ]);
        $mockFeedback->created_at = now();

        // Generate the mail
        $this->newLine();
        $this->info('Creating EscalationMail...');

        try {
            $mail = new EscalationMail(
                escalation: $mockEscalation,
                feedback: $mockFeedback,
                hod: $hod
            );

            $this->info('✅ EscalationMail created successfully!');

            // Get rendered content preview
            $this->newLine();
            $this->info('Email Preview (HTML):');
            $this->line('─' . str_repeat('─', 70));

            // Render the view
            $html = view('emails.escalation', [
                'escalation'  => $mockEscalation,
                'feedback'    => $mockFeedback,
                'hod'         => $hod,
                'respondUrl'  => url('/escalations/respond/' . $mockEscalation->token),
            ])->render();

            // Show preview
            $preview = substr(strip_tags($html), 0, 500);
            $this->line($preview . '...');
            $this->line('─' . str_repeat('─', 70));

            // Check if dry-run
            if ($this->option('dry-run')) {
                $this->newLine();
                $this->warn('DRY RUN: Email not sent.');
                $this->info('To send the test email, run without --dry-run');
            } else {
                // Send the test email
                $this->newLine();
                $this->info('Sending test email to: ' . $hod->email);

                try {
                    // In production, this would actually send
                    // For testing, we'll log it or use a mailtrap
                    if (config('app.env') === 'local' || config('mail.default') === 'log') {
                        $this->warn('Environment is LOCAL. Email will be logged to storage/logs/laravel.log');
                        Mail::to($hod->email)->send($mail);
                        $this->info('✅ Test email logged successfully!');
                    } else {
                        Mail::to($hod->email)->send($mail);
                        $this->info('✅ Test email sent successfully!');
                    }
                } catch (\Exception $e) {
                    $this->error('❌ Failed to send email: ' . $e->getMessage());
                    return self::FAILURE;
                }
            }

            $this->newLine();
            $this->info('========================================');
            $this->info('   TEST COMPLETED SUCCESSFULLY');
            $this->info('========================================');

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error creating EscalationMail: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return self::FAILURE;
        }
    }
}
