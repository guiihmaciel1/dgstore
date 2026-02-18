<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class B2BApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $retailer = Auth::guard('b2b')->user();

        if (!$retailer) {
            return redirect()->route('b2b.login');
        }

        if ($retailer->isBlocked()) {
            Auth::guard('b2b')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('b2b.login')
                ->with('error', 'Sua conta foi bloqueada. Entre em contato com o administrador.');
        }

        if ($retailer->isPending()) {
            return redirect()->route('b2b.pending');
        }

        return $next($request);
    }
}
