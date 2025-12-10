<?php

use App\Models\CompanyInfo;

if (!function_exists('getCompanyColor')) {
    function getCompanyColor($key, $default = null)
    {
        $company = CompanyInfo::first();
        return $company->$key ?? $default;
    }
}
