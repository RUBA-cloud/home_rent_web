<?php

// app/Http/Controllers/PermissionController.php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Models\Permission;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('search', ''));

        $permissions = Permission::with(['module','user'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name_en', 'like', "%{$q}%")
                        ->orWhere('name_ar', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('permissions.index', compact('permissions', 'q'));

    }

public function create()
    {
        // Get the current user's modules row (the one with feature flags)
        $modulesRow = Module::where('is_active', true)->latest()->first();
        abort_if(!$modulesRow, 404, '');

        // Feature labels (requires Module::FEATURES and Module::featureLabels())
        $allLabels = Module::featureLabels();

        // Keep only active features for this row
        $activeKeys = collect(Module::FEATURES)
            ->filter(fn ($f) => (bool) data_get($modulesRow, $f, false))
            ->values();

        // Map to [feature_key => label]
        $featuresForRadios = $activeKeys
            ->mapWithKeys(fn ($f) => [$f => $allLabels[$f] ?? $f])
            ->all();
        $defaultFeatureKey = old('main_module', $activeKeys->first());

        return view('permissions.create', compact(
            'modulesRow',            // ->id used for hidden module_id
            'featuresForRadios',     // [key => label]
            'defaultFeatureKey'
        ));
    }

        // Grab whatever you want to display; id is usually enough


    public function store(Request $request)
    {
        $table = Module::where('is_active', true)->latest()->first();

        $data = $request->validate([
            'module_name'=>'required',
            'name_en'          => ['required', 'string', 'max:255'],
            'name_ar'          => ['required', 'string', 'max:255'],
            'can_edit'         => ['nullable'],
            'can_delete'       => ['nullable'],
            'can_add'          => ['nullable'],
            'can_view_history' => ['nullable'],
            'is_active'        => ['required'],
        ]);
        $data['module_id']=$table->id;
        $data['user_id'] = Auth::id();


        // Normalize checkboxes to booleans
        foreach (['can_edit','can_delete','can_add','can_view_history','is_active'] as $f) {
            $data[$f] = $request->boolean($f);
        }

        $permission = Permission::create($data);

        return redirect()
            ->route('permissions.show', $permission)
            ->with('success', 'Permission created successfully.');
    }

    public function show(Permission $permission)
    {
        $permission->load('module');
        return view('permissions.show', compact('permission'));
    }

public function edit(Permission $permission)
{
    // 1) Get the module row tied to this permission
    $modulesRow = Module::findOrFail($permission->module_id);

    // 2) Map feature flags → labels
    $allLabels = Module::featureLabels();

    // 3) Only features that are enabled on this module row
    $activeKeys = collect(Module::FEATURES)
        ->filter(fn ($f) => (bool) $modulesRow->{$f})
        ->values(); // zero-based collection of keys like ['product_module', 'order_module', ...]

    // 4) Build [feature_key => label]
    $featuresForRadios = $activeKeys
        ->mapWithKeys(fn ($f) => [$f => $allLabels[$f] ?? $f])
        ->all(); // e.g. ['product_module' => 'Products', ...]

    // 5) Default radio: old() → existing permission feature → first active
    $candidate = old('feature_key', $permission->feature_key);
    $defaultFeatureKey = in_array($candidate, $activeKeys->all(), true)
        ? $candidate
        : $activeKeys->first(); // may be null if no features enabled

    return view('permissions.edit', compact(
        'permission',
        'modulesRow',
        'featuresForRadios',
        'defaultFeatureKey'
    ));
}



    public function update(Request $request, Permission $permission)
    {
        $table = Module::where('is_active', true)->latest()->first();

        $data = $request->validate([

            'name_en'          => ['required', 'string', 'max:255'],
            'name_ar'          => ['required', 'string', 'max:255'],
            'can_edit'         => ['nullable'],
            'can_delete'       => ['nullable'],
            'can_add'          => ['nullable'],
            'can_view_history' => ['nullable'],
            'is_active'        => ['nullable'],
        ]);
        $data['user_id'] = Auth::id();
        $data['module_id']=$table->id;
        foreach (['can_edit','can_delete','can_add','can_view_history','is_active'] as $f) {
            $data[$f] = $request->boolean($f);
        }

        $permission->update($data);
        broadcast(new \App\Events\PermissionEventUpdate($permission))->toOthers();

        return redirect()
            ->route('permissions.show', $permission)
            ->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
