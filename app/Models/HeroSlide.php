<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeroSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image_path',
        'button_text',
        'button_link',
        'order_index',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'order_index' => 'integer',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index', 'asc');
    }

    // Accessors
    public function getImageUrlAttribute(): string
    {
        return asset('images/hero-slides/' . $this->image_path);
    }

    public function getHasButtonAttribute(): bool
    {
        return !empty($this->button_text) && !empty($this->button_link);
    }
}
