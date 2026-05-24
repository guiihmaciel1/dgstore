<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SupplierAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('supplier')->check()) {
            return redirect()->route('supplier.login');
        }

        return $next($request);
    }
}
