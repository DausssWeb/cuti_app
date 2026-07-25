<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\LeaveApplication;
use App\Models\LeaveQuota;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'employee_id',
        'phone', 'position', 'join_date', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'join_date'         => 'date',
        'is_active'         => 'boolean',
    ];

    // ── Role helpers ──────────────────────────────────────────────
    public function isEmployee(): bool  { return $this->role === 'employee'; }
    public function isManager(): bool   { return $this->role === 'manager'; }
    public function isHrd(): bool       { return $this->role === 'hrd'; }
    public function isAdmin(): bool     { return $this->role === 'admin'; }

    // ── Relations ─────────────────────────────────────────────────
    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function leaveQuotas()
    {
        return $this->hasMany(LeaveQuota::class);
    }

    public function currentYearQuota()
    {
        return $this->hasOne(LeaveQuota::class)->where('year', now()->year);
    }

    // ── Quota helpers ─────────────────────────────────────────────
    public function getOrCreateQuota(int $year = null): LeaveQuota
    {
        $year = $year ?? now()->year;
        return LeaveQuota::firstOrCreate(
            ['user_id' => $this->id, 'year' => $year],
            ['annual_quota' => 12, 'annual_used' => 0, 'sick_used' => 0]
        );
    }

    public function getRemainingAnnualLeave(int $year = null): int
    {
        $quota = $this->getOrCreateQuota($year ?? now()->year);
        return max(0, $quota->annual_quota - $quota->annual_used);
    }
}
