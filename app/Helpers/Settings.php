<?php

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('branch_logo_url')) {
    function branch_logo_url(?string $logo, string $fallback = ''): string
    {
        if (!$logo) return $fallback;

        // New public path (uploads/branches/logos/...)
        if (str_starts_with($logo, 'uploads/')) {
            return asset($logo);
        }

        // Legacy storage path (branches/logos/...)
        return asset('storage/' . ltrim($logo, '/'));
    }
}
