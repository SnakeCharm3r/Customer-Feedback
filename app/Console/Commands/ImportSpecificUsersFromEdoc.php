<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportSpecificUsersFromEdoc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:import-specific {--dry-run : Show what would be imported without actually importing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import users with incharge, line-manager, and platform-manager roles from edoc_db';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting specific user import from edoc_db...');

        try {
            // Get role names from edoc_db (only incharge, line-manager, platform-manager)
            $edocRoles = DB::connection('edoc_db')
                ->table('roles')
                ->whereIn('id', [13, 5, 18]) // incharge (13), line-manager (5), platform-manager (18)
                ->pluck('name', 'id')
                ->toArray();

            $this->info('Using actual role names from edoc_db:');
            foreach ($edocRoles as $roleId => $roleName) {
                $this->line("  - Role ID {$roleId}: {$roleName}");
            }
            $this->newLine();

            // Find users with specific roles
            $targetRoleIds = array_keys($edocRoles);
            $edocUsers = DB::connection('edoc_db')
                ->table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->whereIn('model_has_roles.role_id', $targetRoleIds)
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->select('users.id', 'users.fname', 'users.mname', 'users.lname', 'users.email', 'users.password', 'users.DOB', 'users.email_verified_at', 'users.created_at', 'users.updated_at', 'model_has_roles.role_id')
                ->get();

            $this->info("Found {$edocUsers->count()} users with incharge, line-manager, or platform-manager roles");

            if ($this->option('dry-run')) {
                $this->newLine();
                $this->info('DRY RUN - No changes will be made:');
                foreach ($edocUsers as $edocUser) {
                    $roleName = $edocRoles[$edocUser->role_id];
                    $fullName = trim($edocUser->fname . ' ' . $edocUser->mname . ' ' . $edocUser->lname);
                    $this->line("  - {$fullName} ({$edocUser->email}) - Role: {$roleName}");
                }
                return Command::SUCCESS;
            }

            $imported = 0;
            $updated = 0;
            $skipped = 0;
            $errors = 0;

            foreach ($edocUsers as $edocUser) {
                try {
                    $roleName = $edocRoles[$edocUser->role_id];
                    $fullName = trim(($edocUser->fname ?? '') . ' ' . ($edocUser->mname ?? '') . ' ' . ($edocUser->lname ?? ''));

                    // Check if user already exists by email
                    $existingUser = User::where('email', $edocUser->email)->first();

                    if ($existingUser) {
                        // Update existing user's role
                        $existingUser->update([
                            'role' => $roleName,
                            'is_active' => true, // Activate these users
                            'approved_at' => $existingUser->approved_at ?? now(),
                        ]);
                        $this->info("Updated: {$edocUser->email} - Role: {$roleName}");
                        $updated++;
                        continue;
                    }

                    // Create new user
                    User::create([
                        'name' => $fullName,
                        'fname' => $edocUser->fname ?? null,
                        'mname' => $edocUser->mname ?? null,
                        'lname' => $edocUser->lname ?? null,
                        'email' => $edocUser->email,
                        'dob' => !empty($edocUser->DOB) ? date('Y-m-d', strtotime($edocUser->DOB)) : null,
                        'password' => $edocUser->password ?? Hash::make('password'),
                        'role' => $roleName,
                        'is_active' => true, // Activate these users
                        'is_first_user' => false,
                        'email_verified_at' => $edocUser->email_verified_at,
                        'approved_by' => null,
                        'approved_at' => now(),
                        'created_at' => $edocUser->created_at ?? now(),
                        'updated_at' => $edocUser->updated_at ?? now(),
                    ]);

                    $this->info("Imported: {$edocUser->email} - Role: {$roleName}");
                    $imported++;

                } catch (\Exception $e) {
                    $this->error("Error importing {$edocUser->email}: {$e->getMessage()}");
                    $errors++;
                }
            }

            $this->newLine();
            $this->info('Import completed:');
            $this->info("  - Imported: {$imported}");
            $this->info("  - Updated: {$updated}");
            $this->info("  - Skipped: {$skipped}");
            $this->info("  - Errors: {$errors}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Import failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
