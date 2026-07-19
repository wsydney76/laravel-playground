<?php

namespace App\Http\Middleware;

use App\Enums\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->route('locale');

        if (Locale::tryFrom($locale) === null) {
            abort(404);
        }


        App::setLocale($locale);

        return $next($request);
    }
}
