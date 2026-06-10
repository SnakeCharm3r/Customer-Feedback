<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Auth\RegisteredUserController;

class GenerateInviteLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:invite-link {--email= : Email address to send invitation to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a secure invitation link for new user registration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $link = RegisteredUserController::generateInvitationLink();

        $this->info('========================================');
        $this->info('   USER INVITATION LINK GENERATED');
        $this->info('========================================');
        $this->newLine();
        $this->warn('Share this link with the new user:');
        $this->newLine();
        $this->line($link);
        $this->newLine();

        if ($this->option('email')) {
            $this->info('This link was intended for: ' . $this->option('email'));
        }

        $this->comment('Note: This link expires at midnight.');
        $this->comment('The new user will be set to pending approval after registration.');

        return self::SUCCESS;
    }
}
