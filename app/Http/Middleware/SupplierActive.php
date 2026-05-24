<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SupplierActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $supplierUser = Auth::guard('supplier')->user();
        
        if (!$supplierUser || !$supplierUser->isActive() || !$supplierUser->supplier->active) {
            Auth::guard('supplier')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('supplier.login')
                ->withErrors(['email' => 'Conta desativada. Contate a DG Store.']);
        }
        
        return $next($request);
    }
}
