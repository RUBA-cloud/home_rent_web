<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $supportedLocales = Config::get('app.supported_locales', ['en', 'ar']);
        $fallbackLocale = Config::get('app.fallback_locale', 'en');

        // Get locale from session or fallback
        $locale = Session::get('locale', $fallbackLocale);

        // Ensure it's supported
        if (!in_array($locale, $supportedLocales)) {
            $locale = $fallbackLocale;
        }

        // Apply locale globally
        App::setLocale($locale);

        return $next($request);
    }
}
