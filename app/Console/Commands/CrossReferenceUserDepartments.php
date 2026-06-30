<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CrossReferenceUserDepartments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cross-reference-departments {--dry-run : Show what would be updated without actually updating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cross-reference users in ccbrt_feedback with edoc_db and update their department associations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting department cross-reference...');

        try {
            // Create department mapping: edoc_db dept_id -> ccbrt_feedback dept_id
            $deptMapping = [
                3 => 12,   // Pediatrics
                4 => 1,    // Finance
                6 => 24,   // IT & Business Applications
                16 => 1,   // Financial Planning (FPA) -> Finance
                7 => 31,   // Anaesthesia
                9 => 32,   // OBGYN
                12 => 20,  // Facility Management
                13 => 18,  // Biomedical Maintenance
                18 => 17,  // Advocacy & Disability Inclusion
                19 => 19,  // CCBRT Academy
                20 => 26,  // Moshi
                24 => 23,  // Internal Medicine
                25 => 21,  // General Surgery
                29 => 6,   // Laboratory
                30 => 27,  // Pharmacy Services
                31 => 22,  // Imaging
                35 => 10,  // Optical
                36 => 11,  // Orthopedics & Traumatology
                39 => 16,  // Resource Mobilisation
                40 => 7,   // Marketing & communication
                41 => 3,   // Business Development Administration -> General Administration
                42 => 14,  // Quality Assurance & Customer Care
                43 => 4,   // Human resources
                44 => 28,  // Procurement
                45 => 3,   // General Administration
                46 => 29,  // Warehouse -> Security & Logistics
                47 => 29,  // Security & Logistics
                50 => 5,   // Billing & Credit Control -> General Accounts
                51 => 15,  // Rehabilitation
                52 => 30,  // Hospitality -> Social Service Department
                53 => 3,   // Hospital administration -> General Administration
                54 => 9,   // Ophthalmology(Eye)
                57 => 25,  // Mabinti Center
                58 => 8,   // Nursing
                60 => 9,   // Retina -> Ophthalmology(Eye)
                61 => 13,  // Project Administrations
                62 => 30,  // Social Service Department
                63 => 5,   // Claims and Billing -> General Accounts
                64 => 2,   // General Accounts
                65 => 5,   // Internal Audit
            ];

            // Get all users in ccbrt_feedback
            $localUsers = User::all();
            $this->info("Found {$localUsers->count()} users in ccbrt_feedback");

            $updated = 0;
            $notFound = 0;
            $noDept = 0;
            $errors = 0;

            foreach ($localUsers as $localUser) {
                try {
                    // Skip admin user
                    if ($localUser->email === 'simon.k.mtebe@gmail.com') {
                        continue;
                    }

                    // Find corresponding user in edoc_db
                    $edocUser = DB::connection('edoc_db')
                        ->table('users')
                        ->where('email', $localUser->email)
                        ->first();

                    if (!$edocUser) {
                        $this->warn("Not found in edoc_db: {$localUser->email}");
                        $notFound++;
                        continue;
                    }

                    // Check if user has deptId in edoc_db
                    if (!$edocUser->deptId) {
                        $this->warn("No deptId in edoc_db: {$localUser->email}");
                        $noDept++;
                        continue;
                    }

                    // Map edoc_db deptId to ccbrt_feedback deptId
                    $localDeptId = $deptMapping[$edocUser->deptId] ?? null;

                    if (!$localDeptId) {
                        $this->warn("No mapping for deptId {$edocUser->deptId}: {$localUser->email}");
                        continue;
                    }

                    // Check if department needs updating
                    if ($localUser->department_id == $localDeptId) {
                        continue; // Already correct
                    }

                    if ($this->option('dry-run')) {
                        $this->line("Would update: {$localUser->email} - deptId {$edocUser->deptId} -> localDeptId {$localDeptId}");
                        $updated++;
                        continue;
                    }

                    // Update user's department
                    $localUser->update(['department_id' => $localDeptId]);
                    $this->info("Updated: {$localUser->email} - Department ID: {$localDeptId}");
                    $updated++;

                } catch (\Exception $e) {
                    $this->error("Error processing {$localUser->email}: {$e->getMessage()}");
                    $errors++;
                }
            }

            $this->newLine();
            $this->info('Cross-reference completed:');
            $this->info("  - Updated: {$updated}");
            $this->info("  - Not found in edoc_db: {$notFound}");
            $this->info("  - No deptId in edoc_db: {$noDept}");
            $this->info("  - Errors: {$errors}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Cross-reference failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
