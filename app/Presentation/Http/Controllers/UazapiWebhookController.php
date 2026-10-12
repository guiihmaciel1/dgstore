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
        if ($request->header('Content-Length', 0) > 512000) {
            return response()->json(['error' => 'Payload too large'], 413);
        }

        $payload = $request->all();

        Log::info('UazAPI webhook: RECEBIDO', [
            'event_type' => $payload['EventType'] ?? 'NENHUM',
            'has_message' => isset($payload['message']),
            'ip' => $request->ip(),
        ]);

        if (!$this->webhookService->isValidToken($payload)) {
            Log::warning('UazAPI webhook: token inválido', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Invalid token'], 403);
        }

        try {
            $this->webhookService->processWebhook($payload);
        } catch (\Throwable $e) {
            Log::error('UazAPI webhook: erro ao processar', [
                'error' => $e->getMessage(),
                'file' => basename($e->getFile()) . ':' . $e->getLine(),
            ]);
        }

        return response()->json(['status' => 'received']);
    }
}
