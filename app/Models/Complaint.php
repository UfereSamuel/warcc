<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_number',
        'category',
        'description',
        'suggested_solution',
        'is_anonymous',
        'complainant_name',
        'complainant_email',
        'complainant_phone',
        'evidence_path',
        'is_reviewed',
        'admin_notes',
        'staff_id',
        'ip_address',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_reviewed' => 'boolean',
    ];

    /**
     * Get the staff member who submitted the complaint (if any)
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Generate a unique complaint number
     */
    public static function generateComplaintNumber()
    {
        $year = date('Y');
        $lastComplaint = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastComplaint) {
            $lastNumber = (int) substr($lastComplaint->complaint_number, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "COMP-{$year}-{$newNumber}";
    }

    /**
     * Get the category relationship
     */
    public function categoryRelation()
    {
        return $this->belongsTo(ComplaintCategory::class, 'category', 'slug');
    }

    /**
     * Get available complaint categories (active only, for public forms)
     */
    public static function getCategories(): array
    {
        return ComplaintCategory::active()->ordered()->pluck('name', 'slug')->toArray();
    }

    /**
     * Get all complaint categories (for admin filters and reassignment)
     */
    public static function getAllCategories(): array
    {
        return ComplaintCategory::ordered()->pluck('name', 'slug')->toArray();
    }

    /**
     * Get the category label
     */
    public function getCategoryLabelAttribute(): string
    {
        if ($this->relationLoaded('categoryRelation') && $this->categoryRelation) {
            return $this->categoryRelation->name;
        }

        $category = ComplaintCategory::where('slug', $this->category)->first();

        if ($category) {
            return $category->name;
        }

        return ucwords(str_replace(['_', '-'], ' ', (string) $this->category));
    }

    /**
     * Scope for unreviewed complaints
     */
    public function scopeUnreviewed($query)
    {
        return $query->where('is_reviewed', false);
    }

    /**
     * Scope for reviewed complaints
     */
    public function scopeReviewed($query)
    {
        return $query->where('is_reviewed', true);
    }
}
