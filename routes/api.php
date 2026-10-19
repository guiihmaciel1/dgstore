<?php

use App\Presentation\Http\Controllers\UazapiWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas da API (sem CSRF, sem autenticação de sessão)
|--------------------------------------------------------------------------
|
| O webhook da UazAPI é validado pelo token da instância e processa apenas
| mensagens de fornecedores cadastrados (cotações).
|
*/

Route::post('/uazapi/webhook', [UazapiWebhookController::class, 'handle'])
    ->middleware('throttle:120,1');
