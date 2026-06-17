<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'official_name',
        'flag_code',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the flag URL
     */
    public function getFlagUrlAttribute()
    {
        return "https://flagcdn.com/w80/{$this->flag_code}.png";
    }

    /**
     * Scope for active countries
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered countries
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
