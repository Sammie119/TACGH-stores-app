<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function __construct()
    {
        // Only super admin can access settings
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('super_admin')) {
                abort(403, 'Only super admins can manage system settings.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        $groups   = [
            'general'       => 'General',
            'sales'         => 'Sales & invoicing',
            'notifications' => 'Notifications',
            'security'      => 'Security',
        ];

        return view('settings.index', compact('settings', 'groups'));
    }

    public function update(Request $request, string $group)
    {
        $settings = Setting::where('group', $group)->get();

        foreach ($settings as $setting) {
            $value = match($setting->type) {
                'boolean' => $request->has($setting->key) ? '1' : '0',
                default   => $request->input($setting->key, ''),
            };

            $setting->update(['value' => $value]);
            \Illuminate\Support\Facades\Cache::forget("setting_{$setting->key}");
        }

        // Clear config cache so changes take effect
        Artisan::call('config:clear');

        activity()
            ->causedBy(auth()->user())
            ->log('Updated system settings: ' . $group);

        return back()->with('success', ucfirst($group) . ' settings saved successfully.');
    }
}
