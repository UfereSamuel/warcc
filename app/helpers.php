<?php

if (!function_exists('setting')) {
    /**
     * Get setting value by key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('settings')) {
    /**
     * Get settings by group
     * 
     * @param string $group
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function settings(string $group)
    {
        return App\Models\Setting::getByGroup($group);
    }
}
