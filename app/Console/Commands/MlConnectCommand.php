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
                            {--test : Testa a API com chamadas reais}';

    protected $description = 'Conecta, verifica ou desconecta a conta do Mercado Livre (OAuth2)';

    public function __construct(
        private readonly MercadoLivreApiService $apiService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('test')) {
            return $this->testApi();
        }

        if ($this->option('status')) {
            return $this->showStatus();
        }

        if ($this->option('disconnect')) {
            return $this->disconnect();
        }

        return $this->connect();
    }

    private function showStatus(): int
    {
        $this->info('=== Status da Conexão ML ===');
        $this->newLine();

        if (! $this->apiService->isConfigured()) {
            $this->error('ML_CLIENT_ID e ML_CLIENT_SECRET não configurados no .env');

            return self::FAILURE;
        }

        $this->info('✓ Credenciais configuradas');

        $token = ApiToken::forProvider('mercadolivre');

        if (! $token) {
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
        } else {
            $this->warn('  ✗ Token expirado. Reconecte: php artisan valuation:ml-connect');
        }

        return self::SUCCESS;
    }

    private function disconnect(): int
    {
        $token = ApiToken::forProvider('mercadolivre');

        if (! $token) {
            $this->info('Nenhuma conexão ativa.');

            return self::SUCCESS;
        }

        $token->delete();
        $this->info('Conta do Mercado Livre desconectada.');

        return self::SUCCESS;
    }

    private function testApi(): int
    {
        $this->info('=== Teste da API Mercado Livre ===');
        $this->newLine();

        $token = ApiToken::forProvider('mercadolivre');

        if ($token && $token->isValid()) {
            $this->info("✓ Token válido (expira {$token->expires_at->diffForHumans()})");
        } else {
            $this->warn('✗ Token ausente ou expirado');
        }

        $this->newLine();

        $accessToken = $token?->isValid() ? $token->access_token : null;

        // 1) /users/me (se tem token)
        if ($accessToken) {
            $this->line('1. /users/me ...');
            $r = \Illuminate\Support\Facades\Http::withToken($accessToken)->timeout(10)
                ->get('https://api.mercadolibre.com/users/me');
            $this->showResult($r, fn ($d) => "Nickname: {$d['nickname']}, Country: {$d['country_id']}");
        }

        // 2) /products/search (catálogo)
        $this->line('2. /products/search (catálogo) ...');
        $http = $accessToken
            ? \Illuminate\Support\Facades\Http::withToken($accessToken)->timeout(10)
            : \Illuminate\Support\Facades\Http::timeout(10);
        $r = $http->get('https://api.mercadolibre.com/products/search', [
            'site_id' => 'MLB',
            'q' => 'iphone 15 pro max 256GB',
            'status' => 'active',
        ]);
        $this->showResult($r, fn ($d) => 'Produtos encontrados: ' . count($d['results'] ?? []));

        // 3) Teste integrado usando o scraper de catálogo
        $this->line('3. Teste integrado (scrapeBySlug) ...');
        try {
            $this->apiService->onProgress(function (string $msg, string $type = 'info') {
                $this->line("   {$msg}");
            });

            $result = $this->apiService->scrapeBySlug('iphone-15-pro-max');
            $this->info("   Total: {$result['total_listings']} anúncios coletados");
        } catch (\Throwable $e) {
            $this->error("   ✗ Erro: {$e->getMessage()}");
        }

        $this->newLine();
        $this->info('=== Teste concluído ===');

        return self::SUCCESS;
    }

    private function showResult($response, \Closure $extractor): void
    {
        $status = $response->status();

        if ($response->successful()) {
            try {
                $this->info("   ✓ HTTP {$status} | " . $extractor($response->json()));
            } catch (\Throwable) {
                $this->info("   ✓ HTTP {$status} | " . mb_substr($response->body(), 0, 200));
            }
        } else {
            $this->error("   ✗ HTTP {$status} | " . mb_substr($response->body(), 0, 200));
        }

        $this->newLine();
    }

    private function connect(): int
    {
        if (! $this->apiService->isConfigured()) {
            $this->error('Configure ML_CLIENT_ID e ML_CLIENT_SECRET no .env primeiro.');

            return self::FAILURE;
        }

        if ($this->apiService->isConnected()) {
            $this->info('Já conectado ao Mercado Livre!');

            if (! $this->confirm('Deseja reconectar?')) {
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

        if (! $code) {
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
