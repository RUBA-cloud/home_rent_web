<?php
// app/Http/Controllers/Auth/LoginController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Module;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * After successful login → redirect user to first allowed module
     */
    protected function authenticated(Request $request, User $user)
    {
        // 1️⃣  Handle email verification
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            $user->sendEmailVerificationNotification();

            return redirect()->route('verification.notice')
                ->with('status', 'verification-link-sent');
        }

        // 2️⃣  Find the user’s active modules
        $module = Module::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        // 3️⃣  Map module flag → main route name
        $map = [
            'company_dashboard_module'   => 'home',
            'company_info_module'        => 'companyInfo.index',
            'company_branch_module'      => 'companyBranch.index',
            'company_category_module'    => 'categories.index',
            'company_type_module'        => 'type.index',
            'company_size_module'        => 'sizes.index',
            'company_offers_type_module' => 'offers_type.index',
            'company_offers_module'      => 'offers.index',
            'product_module'             => 'product.index',
            'employee_module'            => 'employees.index',
            'order_module'               => 'orders.index',
            'order_status_module'        => 'order_status.index',
            'region_module'              => 'regions.index',
            'company_delivery_module'    => 'company_delivery.index',
            'payment_module'             => 'payment.index',
        ];

        // 4️⃣  Redirect to the first active module route
        if ($module) {
            foreach ($map as $flag => $route) {
                if (!empty($module->$flag) && $module->$flag === true) {
                    return redirect()->route($route);
                }
            }
        }

        // 5️⃣  Fallback: go home
        return redirect()->route('profile.edit');
    }
}
