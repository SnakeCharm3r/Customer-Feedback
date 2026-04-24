<?php

namespace Database\Seeders;

use App\Models\Hod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HodSeeder extends Seeder
{
    public function run(): void
    {
        $staff = DB::select('
            SELECT DISTINCT
                u.fname, u.mname, u.lname,
                u.email, u.mobile,
                d.dept_name,
                r.name AS role_name
            FROM edoc_permissions_chek.model_has_roles mhr
            INNER JOIN edoc_permissions_chek.users u ON u.id = mhr.model_id
            INNER JOIN edoc_permissions_chek.roles r ON r.id = mhr.role_id
            LEFT  JOIN edoc_permissions_chek.departments d ON d.id = u.deptId
            WHERE mhr.role_id IN (5, 6, 13, 22)
              AND u.status = \'active\'
              AND (u.delete_status IS NULL OR u.delete_status != \'deleted\')
            ORDER BY d.dept_name, u.lname
        ');

        $imported = 0;
        $skipped  = 0;

        foreach ($staff as $s) {
            $parts = array_filter([$s->fname, $s->mname, $s->lname]);
            $name  = trim(implode(' ', $parts));
            $email = strtolower(trim($s->email ?? ''));
            $dept  = trim($s->dept_name ?? 'General');

            if (!$email || !$name) {
                $skipped++;
                continue;
            }

            if (Hod::whereRaw('LOWER(email) = ?', [$email])->exists()) {
                $skipped++;
                continue;
            }

            $roleLabel = match ($s->role_name) {
                'line-manager'        => 'Line Manager',
                'acting-line-manager' => 'Acting Line Manager',
                'in-charge', 'incharge' => 'In-Charge Officer',
                default               => ucfirst(str_replace('-', ' ', $s->role_name)),
            };

            Hod::create([
                'name'       => $name,
                'department' => $dept,
                'email'      => $s->email,
                'phone'      => $s->mobile ?? null,
                'notes'      => $roleLabel,
                'is_active'  => true,
            ]);

            $imported++;
        }

        $this->command->info("HOD seeder: {$imported} imported, {$skipped} skipped.");
    }
}
