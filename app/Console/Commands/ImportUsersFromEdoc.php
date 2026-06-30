<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportUsersFromEdoc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:import-from-edoc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import users from edoc_db database to ccbrt_feedback database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting user import from edoc_db...');

        try {
            // Get all users from edoc_db
            $edocUsers = DB::connection('edoc_db')->table('users')->get();

            $this->info("Found {$edocUsers->count()} users in edoc_db");

            $imported = 0;
            $skipped = 0;
            $errors = 0;

            foreach ($edocUsers as $edocUser) {
                try {
                    // Check if user already exists by email
                    $existingUser = User::where('email', $edocUser->email)->first();

                    if ($existingUser) {
                        $this->warn("Skipping existing user: {$edocUser->email}");
                        $skipped++;
                        continue;
                    }

                    // Build full name
                    $name = trim(($edocUser->fname ?? '') . ' ' . ($edocUser->mname ?? '') . ' ' . ($edocUser->lname ?? ''));

                    // Create new user
                    User::create([
                        'name' => $name,
                        'fname' => $edocUser->fname ?? null,
                        'mname' => $edocUser->mname ?? null,
                        'lname' => $edocUser->lname ?? null,
                        'email' => $edocUser->email,
                        'dob' => !empty($edocUser->DOB) ? date('Y-m-d', strtotime($edocUser->DOB)) : null,
                        'password' => $edocUser->password ?? Hash::make('password'), // Keep existing password or set default
                        'role' => 'qa_officer', // Default role
                        'is_active' => false, // Pending approval
                        'is_first_user' => false,
                        'email_verified_at' => $edocUser->email_verified_at,
                        'approved_by' => null,
                        'approved_at' => null,
                        'created_at' => $edocUser->created_at ?? now(),
                        'updated_at' => $edocUser->updated_at ?? now(),
                    ]);

                    $this->info("Imported: {$edocUser->email}");
                    $imported++;

                } catch (\Exception $e) {
                    $this->error("Error importing {$edocUser->email}: {$e->getMessage()}");
                    $errors++;
                }
            }

            // Set first user as admin if no users exist
            if ($imported > 0 && User::count() === $imported) {
                $firstUser = User::orderBy('id')->first();
                if ($firstUser) {
                    $firstUser->update([
                        'role' => 'admin',
                        'is_first_user' => true,
                        'is_active' => true,
                        'approved_at' => now(),
                    ]);
                    $this->info("Set first user ({$firstUser->email}) as admin");
                }
            }

            $this->newLine();
            $this->info('Import completed:');
            $this->info("  - Imported: {$imported}");
            $this->info("  - Skipped: {$skipped}");
            $this->info("  - Errors: {$errors}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Import failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
