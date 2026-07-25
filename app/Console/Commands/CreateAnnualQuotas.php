<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\LeaveQuota;
use Illuminate\Console\Command;

class CreateAnnualQuotas extends Command
{
    protected $signature   = 'app:create-annual-quotas {year?}';
    protected $description = 'Create annual leave quotas for all active employees and managers';

    public function handle(): int
    {
        $year  = (int) ($this->argument('year') ?? now()->year);
        $users = User::whereIn('role', ['employee', 'manager'])->where('is_active', true)->get();

        $created = 0;
        foreach ($users as $user) {
            $quota = LeaveQuota::firstOrCreate(
                ['user_id' => $user->id, 'year' => $year],
                ['annual_quota' => 12, 'annual_used' => 0, 'sick_used' => 0]
            );
            if ($quota->wasRecentlyCreated) {
                $created++;
                $this->line("  ✓ Created quota for {$user->name} ({$year})");
            }
        }

        $this->info("Done! Created {$created} new quota records for year {$year}.");
        return self::SUCCESS;
    }
}
