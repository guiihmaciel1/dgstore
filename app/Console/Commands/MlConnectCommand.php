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
        } else {
            $this->warn('  ✗ Token expirado. Reconecte: php artisan valuation:ml-connect');
        }

        $this->newLine();
        $this->line('  Proxy (ScraperAPI): ' . ($this->apiService->isProxyConfigured()
            ? '✓ Configurado'
            : '✗ Não configurado (recomendado para contornar bloqueio de IP)'));

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

    private function testApi(): int
    {
        $this->info('=== Teste da API Mercado Livre ===');
        $this->newLine();

        $token = ApiToken::forProvider('mercadolivre');
        $hasProxy = $this->apiService->isProxyConfigured();

        // Status geral
        if ($token && $token->isValid()) {
            $this->info("✓ Token válido (expira {$token->expires_at->diffForHumans()})");
        } else {
            $this->warn('✗ Token ausente ou expirado');
        }

        $this->line('Proxy (ScraperAPI): ' . ($hasProxy ? '✓ Configurado' : '✗ Não configurado'));
        $this->newLine();

        $accessToken = $token?->isValid() ? $token->access_token : null;

        // 1) /users/me (se tem token)
        if ($accessToken) {
            $this->line('1. /users/me ...');
            $r = \Illuminate\Support\Facades\Http::withToken($accessToken)->timeout(10)
                ->get('https://api.mercadolibre.com/users/me');
            $this->showResult($r, fn ($d) => "Nickname: {$d['nickname']}, Country: {$d['country_id']}");
        }

        // 2) /sites/MLB/search direto (esperamos 403)
        $this->line('2. /sites/MLB/search (direto) ...');
        $http = $accessToken
            ? \Illuminate\Support\Facades\Http::withToken($accessToken)->timeout(10)
            : \Illuminate\Support\Facades\Http::timeout(10);
        $r = $http->get('https://api.mercadolibre.com/sites/MLB/search', [
            'q' => 'iphone 15 pro max',
            'condition' => 'used',
            'limit' => 2,
        ]);
        $this->showResult($r, fn ($d) => "Total: " . ($d['paging']['total'] ?? 'N/A'));

        // 3) /sites/MLB/search via ScraperAPI (se configurado)
        if ($hasProxy) {
            $this->line('3. /sites/MLB/search (via proxy) ...');
            $targetUrl = 'https://api.mercadolibre.com/sites/MLB/search?'
                . http_build_query(['q' => 'iphone 15 pro max', 'condition' => 'used', 'limit' => 2]);

            $proxyUrl = config('services.scraper_proxy.base_url', 'https://api.scraperapi.com')
                . '?' . http_build_query([
                    'api_key' => config('services.scraper_proxy.key'),
                    'url' => $targetUrl,
                ]);

            $r = \Illuminate\Support\Facades\Http::timeout(30)->get($proxyUrl);
            if ($r->successful()) {
                $data = $r->json();
                if (is_array($data) && isset($data['results'])) {
                    $total = $data['paging']['total'] ?? 0;
                    $count = count($data['results']);
                    $this->info("   ✓ HTTP {$r->status()} | Total: {$total}, Results: {$count}");

                    foreach (array_slice($data['results'], 0, 3) as $item) {
                        $price = number_format($item['price'] ?? 0, 2, ',', '.');
                        $cond = $item['condition'] ?? 'N/A';
                        $this->line("     - [{$cond}] {$item['title']} => R\$ {$price}");
                    }
                } else {
                    $this->warn("   ⚠ HTTP {$r->status()} mas resposta não é JSON da API ML.");
                    $this->line("     Preview: " . mb_substr($r->body(), 0, 200));
                }
            } else {
                $this->error("   ✗ HTTP {$r->status()} | " . mb_substr($r->body(), 0, 200));
            }
            $this->newLine();
        } else {
            $this->newLine();
            $this->warn('3. Proxy não configurado. Para habilitar:');
            $this->line('   a) Crie conta grátis em https://www.scraperapi.com (5000 créditos/mês)');
            $this->line('   b) Copie sua API Key');
            $this->line('   c) No .env: SCRAPER_API_KEY=sua_chave_aqui');
            $this->line('   d) Rode: php artisan config:cache');
            $this->newLine();
        }

        // 4) Teste integrado usando o service
        $this->line('4. Teste integrado (MercadoLivreApiService::search) ...');
        try {
            $this->apiService->onProgress(function (string $msg, string $type = 'info') {
                $this->line("   {$msg}");
            });

            $result = $this->apiService->search('iphone 15 pro max 256GB', 3);
            $this->info("   Total: {$result['total']}, Items retornados: " . count($result['items']));

            foreach (array_slice($result['items'], 0, 3) as $item) {
                $price = number_format($item['price'] ?? 0, 2, ',', '.');
                $this->line("     - {$item['title']} => R\$ {$price}");
            }
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

    private function maskToken(string $token): string
    {
        return substr($token, -8);
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
