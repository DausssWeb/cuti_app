<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LeaveApplication;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────────────
        User::create([
            'name'        => 'Administrator',
            'email'       => 'admin@gmail.com',
            'employee_id' => 'ADM001',
            'role'        => 'admin',
            'password'    => Hash::make('password'),
            'position'    => 'System Administrator',
            'join_date'   => '2020-01-01',
            'is_active'   => true,
        ]);

        // ── HRD ───────────────────────────────────────────────────
        $hrd = User::create([
            'name'        => 'Siti Rahma HRD',
            'email'       => 'hrd@gmail.com',
            'employee_id' => 'HRD001',
            'role'        => 'hrd',
            'password'    => Hash::make('password'),
            'position'    => 'HR Manager',
            'phone'       => '081234567890',
            'join_date'   => '2020-02-01',
            'is_active'   => true,
        ]);

        // ── Managers ──────────────────────────────────────────────
        $manager1 = User::create([
            'name'        => 'Budi Santoso Manager',
            'email'       => 'manager@gmail.com',
            'employee_id' => 'MGR001',
            'role'        => 'manager',
            'password'    => Hash::make('password'),
            'position'    => 'Project Manager',
            'phone'       => '081234567891',
            'join_date'   => '2021-01-15',
            'is_active'   => true,
        ]);

        // ── Employees ─────────────────────────────────────────────
        $employees = [
            ['name' => 'Ahmad Fauzi',          'email' => 'ah@gmail.com',     'employee_id' => 'EMP001', 'position' => 'Software Developer'],
            ['name' => 'Dewi Lestari',         'email' => 'dewi@gmail.com',   'employee_id' => 'EMP002', 'position' => 'UI/UX Designer'],
            ['name' => 'Raka Pratama',         'email' => 'raka@gmail.com',   'employee_id' => 'EMP003', 'position' => 'Backend Developer'],
            ['name' => 'Nurul Aini',           'email' => 'nurul@gmail.com',  'employee_id' => 'EMP004', 'position' => 'QA Engineer'],
            ['name' => 'Hendra Wijaya',        'email' => 'hendra@gmail.com', 'employee_id' => 'EMP005', 'position' => 'DevOps Engineer'],
            ['name' => 'Faisal Rahmat Firdaus','email' => 'faisal@gmail.com', 'employee_id' => 'EMP006', 'position' => 'Frontend Developer'],
            ['name' => 'Ady Hadiansyah',       'email' => 'ady@gmail.com',    'employee_id' => 'EMP007', 'position' => 'Network Engineer'],
            ['name' => 'Ina Ristiyani',        'email' => 'ina@gmail.com',    'employee_id' => 'EMP008', 'position' => 'Business Analyst'],
            ['name' => 'Fitri Nur Azni',       'email' => 'fitri@gmail.com',  'employee_id' => 'EMP009', 'position' => 'Finance Staff'],
        ];

        $empUsers = [];
        foreach ($employees as $emp) {
            $u = User::create(array_merge($emp, [
                'role'      => 'employee',
                'password'  => Hash::make('password'),
                'phone'     => '0812' . rand(10000000, 99999999),
                'join_date' => '2022-' . str_pad(rand(1,12),2,'0',STR_PAD_LEFT) . '-01',
                'is_active' => true,
            ]));
            $u->getOrCreateQuota(now()->year);
            $empUsers[] = $u;
        }
        $manager1->getOrCreateQuota(now()->year);
        $hrd->getOrCreateQuota(now()->year);

        // ── Sample Leave Applications ─────────────────────────────
        // Approved leave for EMP001
        // $app1 = LeaveApplication::create([
        //     'user_id'             => $empUsers[0]->id,
        //     'leave_type'          => 'annual',
        //     'start_date'          => now()->subDays(20),
        //     'end_date'            => now()->subDays(18),
        //     'total_days'          => 3,
        //     'reason'              => 'Keperluan keluarga menghadiri pernikahan saudara di Yogyakarta.',
        //     'status'              => 'hrd_approved',
        //     'year'                => now()->year,
        //     'manager_approved_by' => $manager1->id,
        //     'manager_approved_at' => now()->subDays(21),
        //     'manager_notes'       => 'Disetujui.',
        //     'hrd_approved_by'     => $hrd->id,
        //     'hrd_approved_at'     => now()->subDays(21),
        //     'hrd_notes'           => 'Disetujui.',
        // ]);
        // $empUsers[0]->getOrCreateQuota(now()->year)->increment('annual_used', 3);

        // // Pending leave for EMP002
        // LeaveApplication::create([
        //     'user_id'    => $empUsers[1]->id,
        //     'leave_type' => 'sick',
        //     'start_date' => now()->addDays(2),
        //     'end_date'   => now()->addDays(3),
        //     'total_days' => 2,
        //     'reason'     => 'Sakit demam berdarah, disertai surat keterangan dokter.',
        //     'status'     => 'pending',
        //     'year'       => now()->year,
        // ]);

        // // Manager approved, awaiting HRD for EMP003
        // LeaveApplication::create([
        //     'user_id'             => $empUsers[2]->id,
        //     'leave_type'          => 'annual',
        //     'start_date'          => now()->addDays(5),
        //     'end_date'            => now()->addDays(7),
        //     'total_days'          => 3,
        //     'reason'              => 'Liburan akhir tahun bersama keluarga.',
        //     'status'              => 'manager_approved',
        //     'year'                => now()->year,
        //     'manager_approved_by' => $manager1->id,
        //     'manager_approved_at' => now()->subDay(),
        //     'manager_notes'       => 'Disetujui, tidak ada project mendesak.',
        // ]);
    }
}
