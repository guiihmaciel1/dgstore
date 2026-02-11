<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Valuation\Services\MercadoLivreApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class MercadoLivreAuthController extends Controller
{
    public function __construct(
        private readonly MercadoLivreApiService $apiService,
    ) {}

    /**
     * Callback do OAuth2 do Mercado Livre.
     */
    public function callback(Request $request): RedirectResponse
    {
        $code = $request->query('code');

        if (!$code) {
            $error = $request->query('error', 'Sem código de autorização');
            Log::error("[ML Auth] Callback sem código: {$error}");

            return redirect()
                ->route('valuations.index')
                ->with('error', "Erro ao conectar ML: {$error}");
        }

        try {
            $token = $this->apiService->exchangeCode($code);

            Log::info("[ML Auth] Conectado. User ID: {$token->external_user_id}");

            return redirect()
                ->route('valuations.index')
                ->with('success', 'Mercado Livre conectado com sucesso!');
        } catch (\Throwable $e) {
            Log::error("[ML Auth] Erro: {$e->getMessage()}");

            return redirect()
                ->route('valuations.index')
                ->with('error', "Erro ao conectar ML: {$e->getMessage()}");
        }
    }
}
