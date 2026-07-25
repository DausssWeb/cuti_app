<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveQuota extends Model
{
    protected $fillable = [
        'user_id', 'year', 'annual_quota', 'annual_used', 'sick_used',
    ];

    protected $casts = ['year' => 'integer'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRemainingAttribute(): int
    {
        return max(0, $this->annual_quota - $this->annual_used);
    }
}
