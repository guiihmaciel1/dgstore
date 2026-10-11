<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\WhatsApp\Services\UazapiWebhookService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UazapiWebhookController extends Controller
{
    public function __construct(
        private readonly UazapiWebhookService $webhookService
    ) {}

    /**
     * POST /api/uazapi/webhook
     * Recebe eventos da UazAPI (mensagens, status, connection).
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (!$this->webhookService->isValidToken($payload)) {
            Log::warning('UazAPI webhook: token inválido');
            return response()->json(['error' => 'Invalid token'], 403);
        }

        try {
            $this->webhookService->processWebhook($payload);
        } catch (\Throwable $e) {
            Log::error('UazAPI webhook: erro ao processar', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['status' => 'received']);
    }
}
