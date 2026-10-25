<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // admin URLs carry the locale as their first segment (/ar/admin, /en/admin)
        $locale = in_array($request->segment(1), ['ar', 'en'])
            ? $request->segment(1)
            : session('locale', config('app.locale'));

        if (in_array($locale, ['ar', 'en'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
