<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloqueia as rotas do CRM enquanto o pipeline estiver desativado
 * (config crm.pipeline_enabled).
 */
class EnsureCrmEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('crm.pipeline_enabled')) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'error' => 'Pipeline desativado.',
            ], 503);
        }

        return response()->view('crm.disabled');
    }
}
