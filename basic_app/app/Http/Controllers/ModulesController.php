<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Requests\ModuleRequest;

class ModulesController extends Controller
{
    /**
     * Display a listing of the modules.
     */
    public function index(Request $request)
    {
        $q      = $request->input('q');       // نص البحث
        $status = $request->input('status');  // active / inactive / null
        $sort   = $request->input('sort');    // oldest / enabled_desc / enabled_asc / null

        $modules = Module::query()
            ->with('user')

            // بحث بالاسم أو id
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->whereHas('user', function ($u) use ($q) {
                            $u->where('name', 'like', "%{$q}%");
                        })
                        ->orWhere('id', $q);
                });
            })

            // فلترة بالحالة
            ->when($status === 'active', function ($q2) {
                $q2->where('is_active', true);
            })
            ->when($status === 'inactive', function ($q2) {
                $q2->where('is_active', false);
            })

            // الترتيب
            ->when(in_array($sort, ['oldest', 'enabled_desc', 'enabled_asc']), function ($q2) use ($sort) {
                if ($sort === 'oldest') {
                    $q2->orderBy('created_at', 'asc');
                } else {
                    // عدد الفيلدات المفعّلة (قيمة = 1)
                    $columns = [
                        'company_dashboard_module',
                        'company_info_module',
                        'company_branch_module',
                        'company_category_module',
                        'home_rent_module',
                        'home_rent_feature_module',
                        'company_type_module',
                        'company_size_module',
                        'company_offers_type_module',
                        'company_offers_module',
                        'product_module',
                        'employee_module',
                        'order_module',
                        'order_status_module',
                        'regions_module',        // تأكدي من اسم العمود هنا
                        'company_delivery_module',
                        'additional_module',
                    ];

                    $sumExpr = implode(' + ', array_map(
                        fn ($c) => "($c = 1)",
                        $columns
                    ));

                    // مثال: ( (col1 = 1) + (col2 = 1) + ... ) DESC
                    $direction = $sort === 'enabled_desc' ? 'DESC' : 'ASC';
                    $q2->orderByRaw("($sumExpr) {$direction}");
                }
            }, function ($q2) {
                // default sort
                $q2->latest();
            })

            ->paginate(9);

        return view('modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a new module.
     */
    public function create()
    {
        return view('modules.create');
    }

    /**
     * Store a newly created module in storage.
     */
    public function store(ModuleRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        Module::create($data);

        return redirect()
            ->route('modules.index')
            ->with('success', 'Module created successfully!');
    }

    public function show(Module $module)
    {
        return view('modules.show', compact('module'));
    }

    /**
     * Show the form for editing the specified module.
     */
    public function edit(Module $module)
    {
        return view('modules.edit', compact('module'));
    }

    /**
     * Update the specified module in storage.
     */
    public function update(ModuleRequest $request, Module $module)
    {
        $data = $request->validated();
        $module->update($data);

        return redirect()
            ->route('modules.index')
            ->with('success', 'Module updated successfully!');
    }

    /**
     * Remove the specified module from storage.
     */
    public function destroy(Module $module)
    {
        $module->delete();

        return redirect()
            ->route('modules.index')
            ->with('success', 'Module deleted successfully!');
    }
}
