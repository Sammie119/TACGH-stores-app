<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        // Only super admin can manage roles
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('super_admin')) {
                abort(403, 'Only super admins can manage roles and permissions.');
            }
            return $next($request);
        });
    }

    // ── Roles index ───────────────────────────────────────────
    public function index()
    {
        $roles = Role::with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->get();

        $permissions = Permission::orderBy('name')->get()
            ->groupBy(fn($p) => explode(' ', $p->name)[1] ?? 'other');

        return view('settings.roles.index', compact('roles', 'permissions'));
    }

    // ── Create role ───────────────────────────────────────────
    public function create()
    {
        $permissions = Permission::orderBy('name')->get()
            ->groupBy(fn($p) => explode(' ', $p->name)[1] ?? 'other');

        return view('settings.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:50|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        // Prevent reserved names
        if (in_array($request->name, ['super_admin'])) {
            return back()->with('error',
                'This role name is reserved and cannot be created.')
                ->withInput();
        }

        $role = Role::create([
            'name'       => strtolower(str_replace(' ', '_', $request->name)),
            'guard_name' => 'web',
        ]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        activity()
            ->causedBy(auth()->user())
            ->log('Created role: ' . $role->name);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    // ── Edit role ─────────────────────────────────────────────
    public function edit(Role $role)
    {
        if ($role->name === 'super_admin') {
            return redirect()->route('roles.index')
                ->with('error', 'The super admin role cannot be edited.');
        }

        $permissions = Permission::orderBy('name')->get()
            ->groupBy(fn($p) => explode(' ', $p->name)[1] ?? 'other');

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('settings.roles.edit', compact(
            'role', 'permissions', 'rolePermissions'
        ));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'super_admin') {
            return back()->with('error',
                'The super admin role cannot be modified.');
        }

        $request->validate([
            'permissions' => 'nullable|array',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        activity()
            ->causedBy(auth()->user())
            ->log('Updated permissions for role: ' . $role->name);

        return redirect()->route('roles.index')
            ->with('success', 'Role permissions updated successfully.');
    }

    // ── Delete role ───────────────────────────────────────────
    public function destroy(Role $role)
    {
        if (in_array($role->name, [
            'super_admin', 'regional_manager',
            'branch_admin', 'store_manager',
        ])) {
            return back()->with('error',
                'This system role cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error',
                'Cannot delete a role that has users assigned to it. '
                . 'Re-assign those users first.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    // ── Permissions list ──────────────────────────────────────
    public function permissions()
    {
        $permissions = Permission::orderBy('name')->get()
            ->groupBy(fn($p) => explode(' ', $p->name)[1] ?? 'other');

        $roles = Role::with('permissions')->orderBy('name')->get();

        return view('settings.roles.permissions',
            compact('permissions', 'roles'));
    }

    // ── User roles ────────────────────────────────────────────
    public function userRoles(Request $request)
    {
        $users = \App\Models\User::with('roles')
            ->when($request->search, fn($q) =>
            $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%'))
            ->paginate(20)->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('settings.roles.users',
            compact('users', 'roles'));
    }

    public function updateUserRole(Request $request, \App\Models\User $user)
    {
        // Prevent changing super_admin role
        if ($user->hasRole('super_admin') &&
            !in_array('super_admin', $request->roles ?? [])) {
            return back()->with('error',
                'Cannot remove the super admin role from this user.');
        }

        $request->validate([
            'roles' => 'required|array|min:1',
        ]);

        $user->syncRoles($request->roles);

        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        activity()
            ->causedBy(auth()->user())
            ->log("Updated roles for user: {$user->name} → "
                . implode(', ', $request->roles));

        return back()->with('success',
            "Roles updated for {$user->name}.");
    }
}
