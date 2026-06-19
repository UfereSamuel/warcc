<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ComplaintCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'category', 'slug');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public static function makeSlug(string $name): string
    {
        return Str::slug($name, '_');
    }

    public static function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $base = self::makeSlug($name);
        $slug = $base;
        $counter = 1;

        while (self::query()
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base . '_' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * @return list<string>
     */
    public static function activeSlugs(): array
    {
        return self::active()->ordered()->pluck('slug')->all();
    }

    /**
     * @return list<string>
     */
    public static function allSlugs(): array
    {
        return self::ordered()->pluck('slug')->all();
    }
}
