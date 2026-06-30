<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportQADepartmentUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:import-qa-department {--dry-run : Show what would be imported without actually importing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import users from Quality Assurance department (excluding line-managers) from edoc_db';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting QA department user import from edoc_db...');

        try {
            // Quality Assurance department ID in edoc_db
            $edocDeptId = 42;
            // Quality Assurance department ID in ccbrt_feedback
            $localDeptId = 14;

            // Find users in QA department excluding line-managers
            $qaUsers = DB::connection('edoc_db')
                ->table('users')
                ->where('deptId', $edocDeptId)
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('model_has_roles')
                        ->whereColumn('users.id', 'model_has_roles.model_id')
                        ->where('model_has_roles.role_id', 5) // line-manager role ID
                        ->where('model_has_roles.model_type', 'App\\Models\\User');
                })
                ->get();

            $this->info("Found {$qaUsers->count()} users in QA department (excluding line-managers)");

            if ($this->option('dry-run')) {
                $this->newLine();
                $this->info('DRY RUN - No changes will be made:');
                foreach ($qaUsers as $user) {
                    $fullName = trim($user->fname . ' ' . $user->mname . ' ' . $user->lname);
                    $this->line("  - {$fullName} ({$user->email}) - deptId: {$user->deptId}");
                }
                return Command::SUCCESS;
            }

            $imported = 0;
            $updated = 0;
            $skipped = 0;
            $errors = 0;

            foreach ($qaUsers as $edocUser) {
                try {
                    $fullName = trim(($edocUser->fname ?? '') . ' ' . ($edocUser->mname ?? '') . ' ' . ($edocUser->lname ?? ''));

                    // Check if user already exists by email
                    $existingUser = User::where('email', $edocUser->email)->first();

                    if ($existingUser) {
                        // Update existing user's department
                        $existingUser->update([
                            'department_id' => $localDeptId,
                            'is_active' => true,
                            'approved_at' => $existingUser->approved_at ?? now(),
                        ]);
                        $this->info("Updated: {$edocUser->email} - Department ID: {$localDeptId}");
                        $updated++;
                        continue;
                    }

                    // Get user's role from edoc_db
                    $userRole = DB::connection('edoc_db')
                        ->table('model_has_roles')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->where('model_has_roles.model_id', $edocUser->id)
                        ->where('model_has_roles.model_type', 'App\\Models\\User')
                        ->value('roles.name');

                    // Create new user
                    User::create([
                        'name' => $fullName,
                        'fname' => $edocUser->fname ?? null,
                        'mname' => $edocUser->mname ?? null,
                        'lname' => $edocUser->lname ?? null,
                        'email' => $edocUser->email,
                        'dob' => !empty($edocUser->DOB) ? date('Y-m-d', strtotime($edocUser->DOB)) : null,
                        'password' => $edocUser->password ?? Hash::make('password'),
                        'role' => $userRole ?? 'qa_officer', // Default to qa_officer if no role
                        'department_id' => $localDeptId,
                        'is_active' => true,
                        'is_first_user' => false,
                        'email_verified_at' => $edocUser->email_verified_at,
                        'approved_by' => null,
                        'approved_at' => now(),
                        'created_at' => $edocUser->created_at ?? now(),
                        'updated_at' => $edocUser->updated_at ?? now(),
                    ]);

                    $this->info("Imported: {$edocUser->email} - Role: " . ($userRole ?? 'qa_officer') . " - Department ID: {$localDeptId}");
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
