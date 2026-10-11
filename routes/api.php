<?php

use App\Presentation\Http\Controllers\UazapiWebhookController;
use App\Presentation\Http\Controllers\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas da API (sem CSRF, sem autenticação)
|--------------------------------------------------------------------------
*/

Route::prefix('whatsapp')->group(function () {
    Route::get('/webhook', [WhatsAppWebhookController::class, 'verify']);
    Route::post('/webhook', [WhatsAppWebhookController::class, 'handle']);
});

Route::post('/uazapi/webhook', [UazapiWebhookController::class, 'handle']);
