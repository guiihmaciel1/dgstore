<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\User\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Verifica se o usuário está ativo
        if (!$user->active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Sua conta está inativa.');
        }

        // Verifica se o usuário tem a role necessária
        $requiredRole = UserRole::tryFrom($role);
        
        if ($requiredRole === UserRole::Admin && !$user->isAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
