<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use App\Models\EmployeeHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $employees = User::query()
            ->where('role', 'employee')
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->with('permissions')
            ->latest()               // created_at desc
            ->paginate(12)
            ->withQueryString();

        return view('employee.index', compact('employees', 'q'));
    }

    public function history(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $employees = EmployeeHistory::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->with('permissions')     // ensure relation exists on EmployeeHistory
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('employee.history', compact('employees', 'q'));
    }

    public function create()
    {
        // Get all permissions (adjust ordering as you prefer)
        $permissions = Permission::orderBy('name_en')->get();

        return view('employee.create', compact('permissions'));
    }

    public function store(\App\Http\Requests\StoreEmployeeRequest $request)
    {
        $data = $request->validated();

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role'        => 'employee',
            'avatar_path' => $avatarPath,
        ]);

        $user->permissions()->sync($data['permissions'] ?? []);

        return redirect()
            ->route('employees.show', $user)
            ->with('success', __('adminlte::menu.employees'));
    }

    public function reactivate($historyId)
    {
        return DB::transaction(function () use ($historyId) {
            // 1) Load snapshot
            $history = EmployeeHistory::with(['permissions:id'])->findOrFail($historyId);

            // Optional sanity check (if your model has this helper)
            if (method_exists($history, 'isEmployee') && ! $history->isEmployee()) {
                abort(404);
            }

            // 2) Try to find original user (including soft-deleted)
            $user = null;
            if (!empty($history->user_id)) {
                $user = User::withTrashed()->find($history->user_id);
            }
            if (!$user && !empty($history->email)) {
                $user = User::withTrashed()->where('email', $history->email)->first();
            }

            // 3) Choose a unique target email
            $targetEmail = (string) $history->email;
            $conflict = function ($email, $excludeId = null) {
                return User::withTrashed()
                    ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                    ->where('email', $email)
                    ->exists();
            };

            if ($user) {
                if ($conflict($targetEmail, $user->id)) {
                    $local  = Str::before($targetEmail, '@');
                    $domain = Str::after($targetEmail, '@');
                    $targetEmail = $local . '+' . now()->format('YmdHis') . '@' . $domain;
                }
            } else {
                if ($conflict($targetEmail)) {
                    $local  = Str::before($targetEmail, '@');
                    $domain = Str::after($targetEmail, '@');
                    $targetEmail = $local . '+' . now()->format('YmdHis') . '@' . $domain;
                }
            }

            // 4) Restore or create user
            if ($user) {
                if (method_exists($user, 'trashed') && $user->trashed()) {
                    $user->restore();
                }

                $user->name  = (string) $history->name;
                $user->email = $targetEmail;
                $user->role  = 'employee';

                if (!empty($history->avatar_path)) {
                    $user->avatar_path = $history->avatar_path;
                }

                // If history->password is a bcrypt hash, reuse; otherwise generate a random one
                if (!empty($history->password) && Str::startsWith($history->password, '$2y$')) {
                    $user->password = $history->password;
                } else {
                    $user->password = Hash::make(Str::password(16));
                }

                $user->save();
            } else {
                $password = !empty($history->password) && Str::startsWith($history->password, '$2y$')
                    ? $history->password
                    : Hash::make(Str::password(16));

                $user = User::create([
                    'name'        => (string) $history->name,
                    'email'       => $targetEmail,
                    'password'    => $password,
                    'role'        => 'employee',
                    'avatar_path' => $history->avatar_path ?: null,
                ]);
            }

            // 5) Re-attach permissions
            $permIds = [];
            if ($history->relationLoaded('permissions')) {
                $permIds = $history->permissions->pluck('id')->all();
            } elseif (is_array($history->permissions ?? null)) {
                $permIds = array_values(array_filter(array_map('intval', $history->permissions)));
            }
            if (!empty($permIds)) {
                $user->permissions()->sync($permIds);
            }

            // 6) Log reactivation (optional)
            if (method_exists(EmployeeHistory::class, 'log')) {
                EmployeeHistory::log($user, 'reactivated', [
                    'source'          => 'employees.reactivate',
                    'from_history_id' => $history->id,
                ], true);
            }

            return redirect()
                ->route('employees.show', $user)
                ->with('success', __('Employee reactivated successfully.'));
        });
    }

    public function show(User $employee)
    {
        abort_unless($employee->role === 'employee', 404);

        $employee->load('permissions');

        return view('employee.show', compact('employee'));
    }

    // Prefer route-model binding for consistency
    public function edit(User $employee)
    {
        abort_unless($employee->role === 'employee', 404);

        $permissions = Permission::orderBy('name_en')->get();
        $employee->load('permissions');

        return view('employee.edit', compact('employee', 'permissions'));
    }

    public function update(\App\Http\Requests\UpdateEmployeeRequest $request, User $employee)
    {
        abort_unless($employee->role === 'employee', 404);

        $data = $request->validated();

        // BEFORE snapshot
        if (method_exists(EmployeeHistory::class, 'log')) {
            EmployeeHistory::log($employee, 'updated', [
                'source' => 'employees.update',
                'when'   => 'before',
            ], true);
        }

        // Apply changes
        if ($request->hasFile('avatar')) {
            if ($employee->avatar_path && Storage::disk('public')->exists($employee->avatar_path)) {
                Storage::disk('public')->delete($employee->avatar_path);
            }
            $employee->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        $employee->name  = $data['name'];
        $employee->email = $data['email'];

        if (!empty($data['password'])) {
            $employee->password = Hash::make($data['password']);
        }

        $employee->save();
broadcast(new \App\Events\EmployeeEventUpdate($employee));
        $employee->permissions()->sync($data['permissions'] ?? []);

        // AFTER snapshot (optional)
        // EmployeeHistory::log($employee, 'updated', ['source' => 'employees.update', 'when' => 'after']);

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', __('adminlte::menu.employees'));
    }

    public function destroy(User $employee)
    {
        abort_unless($employee->role === 'employee', 404);

        // BEFORE delete
        if (method_exists(EmployeeHistory::class, 'log')) {
            EmployeeHistory::log($employee, 'deleted', [
                'source' => 'employees.destroy',
                'when'   => 'before',
            ], false);
        }

        if ($employee->avatar_path && Storage::disk('public')->exists($employee->avatar_path)) {
            Storage::disk('public')->delete($employee->avatar_path);
        }

        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', __('adminlte::menu.employees'));
    }
}
