<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = "setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function() use ($key, $default) {
            $setting = static::where('key', $key)->where('is_active', true)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value
     */
    public static function set(string $key, $value): bool
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'label' => static::humanizeKey($key),
                'type' => 'text',
                'group' => static::groupForKey($key),
                'is_active' => true,
            ]
        );

        // Clear cache
        Cache::forget("setting_{$key}");
        
        return $setting->wasRecentlyCreated || $setting->wasChanged();
    }

    private static function humanizeKey(string $key): string
    {
        return ucwords(str_replace('_', ' ', $key));
    }

    private static function groupForKey(string $key): string
    {
        if (str_starts_with($key, 'about_')) {
            return 'about';
        }

        if (str_starts_with($key, 'homepage_') || str_starts_with($key, 'organization_')) {
            return 'homepage';
        }

        return 'general';
    }

    /**
     * Get settings by group
     */
    public static function getByGroup(string $group)
    {
        $cacheKey = "settings_group_{$group}";
        
        return Cache::remember($cacheKey, 3600, function() use ($group) {
            return static::where('group', $group)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }

    /**
     * Clear settings cache
     */
    public static function clearCache()
    {
        Cache::flush();
    }

    /**
     * Boot method to clear cache on save/delete
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            Cache::forget("setting_{$setting->key}");
            Cache::forget("settings_group_{$setting->group}");
        });

        static::deleted(function ($setting) {
            Cache::forget("setting_{$setting->key}");
            Cache::forget("settings_group_{$setting->group}");
        });
    }
}
