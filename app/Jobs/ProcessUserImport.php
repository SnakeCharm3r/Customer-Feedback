<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProcessUserImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $filePath;
    private $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, int $userId)
    {
        $this->filePath = $filePath;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $spreadsheet = IOFactory::load($this->filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            $headerRow = array_shift($rows);

            // Validate headers
            $expectedHeaders = [
                'Full Name',
                'First Name',
                'Middle Name',
                'Last Name',
                'Email Address',
                //'Date of Birth (YYYY-MM-DD)', //not required by the management
                'Role (admin, qa_officer, call_center, qa_hod, coo, line_manager)',
            ];

            if ($headerRow !== $expectedHeaders) {
                Log::error('Invalid Excel template for user import', ['file' => $this->filePath]);
                return;
            }

            // Validate and prepare data
            $validUsers = [];
            $errors = [];
            $validRoles = ['admin', 'qa_officer', 'call_center', 'qa_hod', 'coo', 'line_manager'];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                $user = [
                    'name' => trim($row[0] ?? ''),
                    'fname' => trim($row[1] ?? ''),
                    'mname' => trim($row[2] ?? ''),
                    'lname' => trim($row[3] ?? ''),
                    'email' => trim($row[4] ?? ''),
                    //'dob' => trim($row[5] ?? ''),//not required by the management
                    'role' => trim(strtolower($row[6] ?? '')),
                ];

                $rowErrors = [];

                // Validate required fields
                if (empty($user['email'])) {
                    $rowErrors[] = 'Email is required';
                } elseif (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                    $rowErrors[] = 'Invalid email format';
                } elseif (User::where('email', $user['email'])->exists()) {
                    $rowErrors[] = 'Email already exists in system';
                }

                if (empty($user['role'])) {
                    $rowErrors[] = 'Role is required';
                } elseif (!in_array($user['role'], $validRoles)) {
                    $rowErrors[] = 'Invalid role. Must be one of: ' . implode(', ', $validRoles);
                }

                // Validate date of birth if provided
                if (!empty($user['dob'])) {
                    try {
                        $dob = \DateTime::createFromFormat('Y-m-d', $user['dob']);
                        if (!$dob) {
                            $rowErrors[] = 'Invalid date format. Use YYYY-MM-DD';
                        }
                    } catch (\Exception $e) {
                        $rowErrors[] = 'Invalid date format. Use YYYY-MM-DD';
                    }
                }

                if (empty($rowErrors)) {
                    $validUsers[] = $user;
                } else {
                    $errors[$rowNumber] = [
                        'data' => $user,
                        'errors' => $rowErrors,
                    ];
                }
            }

            // Import valid users
            $importedCount = 0;
            $failedCount = 0;

            foreach ($validUsers as $userData) {
                try {
                    User::create([
                        'name' => $userData['name'],
                        'fname' => $userData['fname'],
                        'mname' => $userData['mname'],
                        'lname' => $userData['lname'],
                        'email' => $userData['email'],
                        'dob' => !empty($userData['dob']) ? $userData['dob'] : null,
                        'password' => '', // Empty password - user will set via registration link after approval
                        'role' => $userData['role'],
                        'is_active' => false,
                        'is_first_user' => false,
                    ]);

                    $importedCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error('Failed to import user: ' . $e->getMessage(), ['user' => $userData]);
                }
            }

            // Clean up the file
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }

            Log::info('User import completed', [
                'imported' => $importedCount,
                'failed' => $failedCount,
                'errors' => count($errors),
                'user_id' => $this->userId,
            ]);

        } catch (\Exception $e) {
            Log::error('User import job failed: ' . $e->getMessage(), [
                'file' => $this->filePath,
                'user_id' => $this->userId,
            ]);
        }
    }
}
