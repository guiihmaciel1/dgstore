<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Aceita um ou múltiplos roles separados por vírgula.
     * Exemplos: middleware('role:admin_geral')
     *           middleware('role:admin_geral,admin_b2b')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Sua conta está inativa.');
        }

        if (!empty($roles) && !in_array($user->role->value, $roles, true)) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
