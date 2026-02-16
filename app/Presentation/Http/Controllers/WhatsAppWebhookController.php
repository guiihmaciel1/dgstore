<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\WhatsApp\Services\WhatsAppWebhookService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        private readonly WhatsAppWebhookService $webhookService
    ) {}

    /**
     * GET /api/whatsapp/webhook
     * Verificação do webhook pelo Meta (hub.verify_token + hub.challenge).
     */
    public function verify(Request $request): Response
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $expectedToken = config('services.whatsapp.verify_token');

        if ($mode === 'subscribe' && $token === $expectedToken) {
            Log::info('WhatsApp webhook: verificação bem-sucedida');

            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('WhatsApp webhook: verificação falhou', [
            'mode' => $mode,
            'token_match' => $token === $expectedToken,
        ]);

        return response('Forbidden', 403);
    }

    /**
     * POST /api/whatsapp/webhook
     * Recebe eventos do WhatsApp (mensagens, status, etc).
     */
    public function handle(Request $request): JsonResponse
    {
        if (! $this->isValidSignature($request)) {
            Log::warning('WhatsApp webhook: assinatura inválida');

            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $payload = $request->all();

        if (($payload['object'] ?? '') !== 'whatsapp_business_account') {
            return response()->json(['status' => 'ignored']);
        }

        try {
            $this->webhookService->processWebhook($payload);
        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook: erro ao processar', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['status' => 'received']);
    }

    /**
     * Valida a assinatura X-Hub-Signature-256 do Meta.
     */
    private function isValidSignature(Request $request): bool
    {
        $appSecret = config('services.whatsapp.app_secret');

        if (empty($appSecret)) {
            Log::warning('WhatsApp webhook: WHATSAPP_APP_SECRET não configurado, pulando validação');
            return true;
        }

        $signature = $request->header('X-Hub-Signature-256');

        if (! $signature) {
            return false;
        }

        $expectedHash = 'sha256=' . hash_hmac('sha256', $request->getContent(), $appSecret);

        return hash_equals($expectedHash, $signature);
    }
}
