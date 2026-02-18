<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class B2BAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('b2b')->check()) {
            return redirect()->route('b2b.login');
        }

        return $next($request);
    }
}
