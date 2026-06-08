<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Traits\HasSoftDeleteActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use HasSoftDeleteActions;

    public function index(Request $request)
    {
        $query = User::with(['branch', 'roles']);

        if ($request->get('trashed')) {
            $query->onlyTrashed();
        }

        if ($request->get('branch')) {
            $query->where('branch_id', $request->get('branch'));
        }

        if ($request->get('role')) {
            $query->whereHas('roles', fn($q) =>
            $q->where('name', $request->get('role'))
            );
        }

        if ($request->get('search')) {
            $query->where(fn($q) =>
            $q->where('name', 'like', '%' . $request->get('search') . '%')
                ->orWhere('email', 'like', '%' . $request->get('search') . '%')
            );
        }

        $users          = $query->latest()->paginate(15)->withQueryString();
        $trashedCount   = User::onlyTrashed()->count();
        $showingTrashed = (bool) $request->get('trashed');
        $branches       = Branch::where('is_active', true)->get();
        $roles          = Role::all();

        return view('users.index', compact(
            'users', 'trashedCount', 'showingTrashed', 'branches', 'roles'
        ));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        $roles    = Role::all();
        return view('users.create', compact('branches', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
            'branch_id' => 'nullable|exists:branches,id',
            'roles'     => 'required|array|min:1',
            'roles.*'   => 'exists:roles,name',
            'is_active' => 'boolean',
        ]);

        if (!auth()->user()->hasRole('super_admin') &&
            !auth()->user()->hasRole('branch_admin')) {
            abort(403, 'You do not have permission to create users.');
        }

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'branch_id' => $validated['branch_id'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        $user->syncRoles($validated['roles']);

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Created user: ' . $user->name);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['branch', 'roles']);

        $recentActivity = \Spatie\Activitylog\Models\Activity::where('causer_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $totalSales = \App\Models\Sale::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        return view('users.show', compact('user', 'recentActivity', 'totalSales'));
    }

    public function edit(User $user)
    {
        $branches = Branch::where('is_active', true)->get();
        $roles    = Role::all();
        return view('users.edit', compact('user', 'branches', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => 'nullable|string|min:8|confirmed',
            'branch_id' => 'nullable|exists:branches,id',
            'roles'     => 'required|array|min:1',
            'roles.*'   => 'exists:roles,name',
        ]);

        if (!auth()->user()->hasRole('super_admin') &&
            !auth()->user()->hasRole('branch_admin')) {
            abort(403, 'You do not have permission to create users.');
        }

        $user->update([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'branch_id' => $validated['branch_id'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles($validated['roles']);

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Updated user: ' . $user->name);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        return $this->softDelete($user, 'users.index');
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        return $this->restoreModel($user, 'users.index');
    }

    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        return $this->forceDeleteModel($user, 'users.index');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log(($user->is_active ? 'Activated' : 'Deactivated') . ' user: ' . $user->name);

        return back()->with('success', 'User status updated.');
    }
}
