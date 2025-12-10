<?php

namespace App\Helpers;

use App\Models\CompanyInfo;
use Illuminate\Support\Facades\Cache;


class CustomSettings
{
    public static function get()
    {
        return CompanyInfo::first(); // Adjust if multi-tenant
    }

    public static function appSettings()
    {
        $company = self::get();

        return [
            'image'=> $company->image ?? null,
            'name_en' => $company->name_en ?? 'Coffee Shop',
            'name_ar' => $company->name_ar ?? 'متجر القهوة',
            'phone' => $company->phone ?? null,
            'main_color' => $company->main_color ?? '#6C63FF',
            'sub_color' => $company->sub_color ?? '#B621FE',
            'text_color' => $company->text_color ?? '#22223B',
            'button_color' => $company->button_color ?? '#4A4E69',
            'icon_color' => $company->icon_color ?? '#9A8C98',
            'text_filed_color' => $company->text_filed_color ?? '#F2E9E4',
            'card_color' => $company->card_color ?? '#F2E9E4',
            'button_text_color' => $company->button_text_color ?? '#C9ADA7',
            'hint_color' => $company->hint_color ?? '#F2E9E4',
            'label_color' => $company->label_color ?? '#4A4E69',
        ];


    }


}
