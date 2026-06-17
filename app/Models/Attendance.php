<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'date',
        'clock_in_time',
        'clock_out_time',
        'clock_in_latitude',
        'clock_in_longitude',
        'clock_out_latitude',
        'clock_out_longitude',
        'clock_in_address',
        'clock_out_address',
        'total_hours',
        'status',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in_latitude' => 'decimal:8',
        'clock_in_longitude' => 'decimal:8',
        'clock_out_latitude' => 'decimal:8',
        'clock_out_longitude' => 'decimal:8',
        'total_hours' => 'decimal:2',
    ];

    // Relationships
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    // Accessors
    public function getIsActiveAttribute(): bool
    {
        return $this->clock_in_time && !$this->clock_out_time;
    }

    public function getWorkingHoursAttribute(): ?int
    {
        if ($this->clock_in_time && $this->clock_out_time) {
            $clockIn = \Carbon\Carbon::parse($this->clock_in_time);
            $clockOut = \Carbon\Carbon::parse($this->clock_out_time);
            return $clockOut->diffInHours($clockIn);
        }
        return null;
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year);
    }
}
