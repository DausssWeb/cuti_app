<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeaveApplication extends Model
{
    protected $fillable = [
        'user_id', 'leave_type', 'start_date', 'end_date', 'total_days',
        'reason', 'sick_note', 'status', 'year',
        'manager_approved_by', 'manager_approved_at', 'manager_notes',
        'hrd_approved_by', 'hrd_approved_at', 'hrd_notes',
    ];

    protected $casts = [
        'start_date'          => 'date',
        'end_date'            => 'date',
        'manager_approved_at' => 'datetime',
        'hrd_approved_at'     => 'datetime',
    ];

    // ── Relations ─────────────────────────────────────────────────
    public function user()
    { 
        return $this->belongsTo(User::class); 
    }
    public function managerApprover() 
    { 
        return $this->belongsTo(User::class, 'manager_approved_by'); 
    }
    public function hrdApprover()
    { 
        return $this->belongsTo(User::class, 'hrd_approved_by'); 
    }

    // ── Status helpers ────────────────────────────────────────────
    public function isPending(): bool
    { 
        return $this->status === 'pending';
    }
    public function isManagerApproved(): bool
    { 
    return $this->status === 'manager_approved';
    }
    public function wasManagerApproved(): bool
    { 
        return in_array($this->status, [
            'manager_approved',
            'hrd_approved',
            'hrd_rejected',
        ]);
    }
    public function isManagerRejected(): bool
    { 
        return $this->status === 'manager_rejected'; 
    }
    public function isHrdApproved(): bool
    { 
        return $this->status === 'hrd_approved'; 
    }
    public function isHrdRejected(): bool
    { 
        return $this->status === 'hrd_rejected'; 
    }
    public function isFinallyApproved(): bool
    { 
        return $this->status === 'hrd_approved'; 
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'           => 'Menunggu Persetujuan',
            'manager_approved'  => 'Disetujui Manager',
            'manager_rejected'  => 'Ditolak Manager',
            'hrd_approved'      => 'Disetujui HRD',
            'hrd_rejected'      => 'Ditolak HRD',
            default             => 'Tidak Diketahui',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'           => 'warning',
            'manager_approved'  => 'info',
            'manager_rejected'  => 'danger',
            'hrd_approved'      => 'success',
            'hrd_rejected'      => 'danger',
            default             => 'secondary',
        };
    }

    public function getLeaveTypeLabelAttribute(): string
    {
        return match($this->leave_type) {
            'annual' => 'Cuti Tahunan',
            'sick'   => 'Cuti Sakit',
            default  => '-',
        };
    }

    // ── Scopes ────────────────────────────────────────────────────
    public function scopePending($q)         { return $q->where('status', 'pending'); }
    public function scopeManagerApproved($q) { return $q->where('status', 'manager_approved'); }
    public function scopeForEmployee($q)     { return $q->whereHas('user', fn($u) => $u->where('role', 'employee')); }
    public function scopeForManager($q)      { return $q->whereHas('user', fn($u) => $u->where('role', 'manager')); }

    // ── Calculate working days (exclude weekends) ─────────────────
    public static function calculateWorkingDays(Carbon $start, Carbon $end): int
    {
        $days = 0;
        $current = $start->copy();
        while ($current->lte($end)) {
            if (!$current->isWeekend()) {
                $days++;
            }
            $current->addDay();
        }
        return $days;
    }
}
