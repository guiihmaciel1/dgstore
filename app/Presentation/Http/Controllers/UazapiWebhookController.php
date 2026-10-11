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

        Log::info('UazAPI webhook: RECEBIDO', [
            'event_type' => $payload['EventType'] ?? 'NENHUM',
            'owner' => $payload['owner'] ?? 'N/A',
            'has_message' => isset($payload['message']),
            'ip' => $request->ip(),
            'payload_keys' => array_keys($payload),
        ]);

        if (!$this->webhookService->isValidToken($payload)) {
            Log::warning('UazAPI webhook: token inválido', [
                'payload_token' => substr($payload['token'] ?? '', 0, 10) . '...',
                'expected_token' => substr(config('services.uazapi.instance_token', ''), 0, 10) . '...',
            ]);
            return response()->json(['error' => 'Invalid token'], 403);
        }

        try {
            $this->webhookService->processWebhook($payload);
            Log::info('UazAPI webhook: processado com sucesso');
        } catch (\Throwable $e) {
            Log::error('UazAPI webhook: erro ao processar', [
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['status' => 'received']);
    }
}
