<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = in_array($request->header('Accept-Language'), ['ar', 'en']) ? $request->header('Accept-Language') : 'ar';
        app()->setLocale($locale);

        return $next($request);
    }
}
