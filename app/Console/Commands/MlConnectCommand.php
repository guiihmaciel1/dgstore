<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Valuation\Models\ApiToken;
use App\Domain\Valuation\Services\MercadoLivreApiService;
use Illuminate\Console\Command;

class MlConnectCommand extends Command
{
    protected $signature = 'valuation:ml-connect
                            {--status : Mostra o status atual da conexão}
                            {--disconnect : Desconecta a conta do ML}
                            {--refresh : Força renovação do token}';

    protected $description = 'Conecta, verifica ou desconecta a conta do Mercado Livre (OAuth2)';

    public function __construct(
        private readonly MercadoLivreApiService $apiService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('status')) {
            return $this->showStatus();
        }

        if ($this->option('disconnect')) {
            return $this->disconnect();
        }

        if ($this->option('refresh')) {
            return $this->refresh();
        }

        return $this->connect();
    }

    private function showStatus(): int
    {
        $this->info('=== Status da Conexão ML ===');
        $this->newLine();

        if (!$this->apiService->isConfigured()) {
            $this->error('ML_CLIENT_ID e ML_CLIENT_SECRET não configurados no .env');
            return self::FAILURE;
        }

        $this->info('✓ Credenciais configuradas');

        $token = ApiToken::forProvider('mercadolivre');

        if (!$token) {
            $this->warn('✗ Nenhum token salvo. Execute: php artisan valuation:ml-connect');
            return self::SUCCESS;
        }

        $this->line("  Token criado em: {$token->created_at->format('d/m/Y H:i')}");
        $this->line("  Atualizado em: {$token->updated_at->format('d/m/Y H:i')}");
        $this->line("  Expira em: {$token->expires_at->format('d/m/Y H:i')}");
        $this->line("  User ID ML: {$token->external_user_id}");

        if ($token->isValid()) {
            $remaining = $token->expires_at->diffForHumans();
            $this->info("  ✓ Token válido (expira {$remaining})");
        } elseif ($token->needsRefresh()) {
            $this->warn('  ⚠ Token expirado, mas tem refresh token. Renovação automática.');
        } else {
            $this->error('  ✗ Token inválido. Reconecte: php artisan valuation:ml-connect');
        }

        return self::SUCCESS;
    }

    private function disconnect(): int
    {
        $token = ApiToken::forProvider('mercadolivre');

        if (!$token) {
            $this->info('Nenhuma conexão ativa.');
            return self::SUCCESS;
        }

        $token->delete();
        $this->info('Conta do Mercado Livre desconectada.');

        return self::SUCCESS;
    }

    private function refresh(): int
    {
        try {
            $token = $this->apiService->refreshToken();
            $this->info("Token renovado! Válido até: {$token->expires_at->format('d/m/Y H:i')}");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Erro: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    private function connect(): int
    {
        if (!$this->apiService->isConfigured()) {
            $this->error('Configure ML_CLIENT_ID e ML_CLIENT_SECRET no .env primeiro.');
            return self::FAILURE;
        }

        if ($this->apiService->isConnected()) {
            $this->info('Já conectado ao Mercado Livre!');

            if (!$this->confirm('Deseja reconectar?')) {
                return self::SUCCESS;
            }
        }

        $authUrl = $this->apiService->getAuthorizationUrl();

        $this->newLine();
        $this->info('╔════════════════════════════════════════════════════╗');
        $this->info('║   CONECTAR MERCADO LIVRE                          ║');
        $this->info('╚════════════════════════════════════════════════════╝');
        $this->newLine();
        $this->line('1. Abra o link abaixo no navegador:');
        $this->newLine();
        $this->line("   {$authUrl}");
        $this->newLine();
        $this->line('2. Faça login na sua conta do Mercado Livre');
        $this->line('3. Autorize o aplicativo');
        $this->line('4. Você será redirecionado para o seu site');
        $this->line('   (o token será salvo automaticamente via callback)');
        $this->newLine();
        $this->warn('Se o redirect não funcionar, cole aqui o código da URL:');
        $this->line('   (a URL terá ?code=XXXXXXX)');
        $this->newLine();

        $code = $this->ask('Cole o código (ou Enter se o redirect já funcionou)');

        if (!$code) {
            $this->info('Verifique: php artisan valuation:ml-connect --status');
            return self::SUCCESS;
        }

        try {
            $token = $this->apiService->exchangeCode($code);
            $this->newLine();
            $this->info('✓ Conectado com sucesso ao Mercado Livre!');
            $this->line("  User ID: {$token->external_user_id}");
            $this->line("  Token válido até: {$token->expires_at->format('d/m/Y H:i')}");
            $this->newLine();
            $this->info('Agora rode: php artisan valuation:scrape');
        } catch (\Throwable $e) {
            $this->error("Erro: {$e->getMessage()}");
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
