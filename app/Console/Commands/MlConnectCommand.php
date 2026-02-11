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
            $this->line('  (sem refresh token, o scraper usará fallback de scraping)');
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

    private function testApi(): int
    {
        $this->info('=== Teste da API Mercado Livre ===');
        $this->newLine();

        $token = ApiToken::forProvider('mercadolivre');

        if (!$token || !$token->isValid()) {
            $this->error('Token não encontrado ou expirado. Conecte primeiro.');
            return self::FAILURE;
        }

        $accessToken = $token->access_token;
        $userId = $token->external_user_id;
        $this->info("Token: ...{$this->maskToken($accessToken)}");
        $this->line("User ID: {$userId}");
        $this->line("Scopes: " . ($token->scopes ? implode(', ', $token->scopes) : 'N/A'));
        $this->newLine();

        $http = fn () => \Illuminate\Support\Facades\Http::withToken($accessToken)->timeout(10);

        // 1) /users/me
        $this->line('1. /users/me ...');
        $r = $http()->get('https://api.mercadolibre.com/users/me');
        $this->resultLine($r, fn ($d) => "Nickname: {$d['nickname']}, Country: {$d['country_id']}");

        // 2) /sites/MLB (info do site)
        $this->line('2. /sites/MLB ...');
        $r = $http()->get('https://api.mercadolibre.com/sites/MLB');
        $this->resultLine($r, fn ($d) => "Site: {$d['name']}");

        // 3) /categories/MLB1055 (info da categoria)
        $this->line('3. /categories/MLB1055 ...');
        $r = $http()->get('https://api.mercadolibre.com/categories/MLB1055');
        $this->resultLine($r, fn ($d) => "Categoria: {$d['name']}");

        // 4) /sites/MLB/search (o que falha)
        $this->line('4. /sites/MLB/search?q=iphone ...');
        $r = $http()->get('https://api.mercadolibre.com/sites/MLB/search', ['q' => 'iphone', 'limit' => 2]);
        $this->resultLine($r, fn ($d) => "Total: " . ($d['paging']['total'] ?? 'N/A'));

        // 5) /highlights/MLB/category/MLB1055
        $this->line('5. /highlights/MLB/category/MLB1055 ...');
        $r = $http()->get('https://api.mercadolibre.com/highlights/MLB/category/MLB1055');
        $this->resultLine($r, fn ($d) => "Items: " . count($d['content'] ?? []));

        // 6) /sites/MLB/search/recent (tendências)
        $this->line('6. /trends/MLB/search ...');
        $r = $http()->get('https://api.mercadolibre.com/trends/MLB');
        $this->resultLine($r, fn ($d) => "Trends: " . count($d));

        // 7) /products/search (catálogo)
        $this->line('7. /products/search?q=iphone 15 pro max ...');
        $r = $http()->get('https://api.mercadolibre.com/products/search', [
            'site_id' => 'MLB',
            'q' => 'iphone 15 pro max',
            'status' => 'active',
        ]);
        $this->resultLine($r, fn ($d) => "Results: " . count($d['results'] ?? []));

        // 8) Dump detalhado do /products/search
        $this->line('8. /products/search - resposta detalhada ...');
        $r = $http()->get('https://api.mercadolibre.com/products/search', [
            'site_id' => 'MLB',
            'q' => 'iphone 15 pro max 256gb',
            'status' => 'active',
        ]);
        if ($r->successful()) {
            $data = $r->json();
            $results = $data['results'] ?? [];
            $this->info("   ✓ HTTP {$r->status()} | Results: " . count($results));

            foreach (array_slice($results, 0, 3) as $i => $product) {
                $this->newLine();
                $this->line("   --- Produto " . ($i + 1) . " ---");
                $this->line("   ID: " . ($product['id'] ?? 'N/A'));
                $this->line("   Name: " . ($product['name'] ?? 'N/A'));
                $this->line("   Status: " . ($product['status'] ?? 'N/A'));
                $this->line("   Domain: " . ($product['domain_id'] ?? 'N/A'));

                // Mostrar atributos importantes
                $attrs = $product['attributes'] ?? [];
                foreach ($attrs as $attr) {
                    $name = $attr['id'] ?? '';
                    if (in_array($name, ['BRAND', 'MODEL', 'LINE', 'INTERNAL_MEMORY', 'MAIN_COLOR'])) {
                        $this->line("   {$name}: " . ($attr['value_name'] ?? 'N/A'));
                    }
                }

                // Buy box ou preço
                if (isset($product['buy_box_winner'])) {
                    $bb = $product['buy_box_winner'];
                    $this->line("   Buy Box Price: R\$ " . number_format($bb['price'] ?? 0, 2, ',', '.'));
                    $this->line("   Buy Box Item ID: " . ($bb['item_id'] ?? 'N/A'));
                }

                if (isset($product['prices'])) {
                    $this->line("   Prices: " . json_encode($product['prices']));
                }

                // Chaves de nível superior
                $keys = array_keys($product);
                $this->line("   Keys: " . implode(', ', $keys));
            }
        } else {
            $this->error("   ✗ HTTP {$r->status()} | " . mb_substr($r->body(), 0, 200));
        }
        $this->newLine();

        // 9) Testar /items/{id} a partir do highlights
        $this->line('9. Teste /items/{id} (do highlights) ...');
        $rH = $http()->get('https://api.mercadolibre.com/highlights/MLB/category/MLB1055');
        if ($rH->successful()) {
            $items = $rH->json()['content'] ?? [];
            $itemIds = array_slice(array_column($items, 'id'), 0, 3);

            if (!empty($itemIds)) {
                $idsStr = implode(',', $itemIds);
                $rItems = $http()->get("https://api.mercadolibre.com/items", ['ids' => $idsStr]);

                if ($rItems->successful()) {
                    $itemsData = $rItems->json();
                    $this->info("   ✓ HTTP {$rItems->status()} | Items: " . count($itemsData));

                    foreach ($itemsData as $wrapper) {
                        $item = $wrapper['body'] ?? $wrapper;
                        $title = $item['title'] ?? 'N/A';
                        $price = $item['price'] ?? 0;
                        $condition = $item['condition'] ?? 'N/A';
                        $this->line("     - [{$condition}] {$title} => R\$ " . number_format($price, 2, ',', '.'));
                    }
                } else {
                    $this->error("   ✗ HTTP {$rItems->status()} | " . mb_substr($rItems->body(), 0, 200));
                }
            } else {
                $this->warn('   Nenhum item no highlights.');
            }
        }
        $this->newLine();

        // 10) Testar /products/{id}/items (listagens de um produto)
        $this->line('10. Teste /products/{id}/items ...');
        $rProd = $http()->get('https://api.mercadolibre.com/products/search', [
            'site_id' => 'MLB',
            'q' => 'iphone 15 pro max',
            'status' => 'active',
        ]);
        if ($rProd->successful()) {
            $products = $rProd->json()['results'] ?? [];
            $productId = $products[0]['id'] ?? null;

            if ($productId) {
                $this->line("   Produto: {$productId}");

                // Tentar /products/{id}/items
                $rPI = $http()->get("https://api.mercadolibre.com/products/{$productId}/items");
                $this->line("   /products/{$productId}/items => HTTP {$rPI->status()}");
                if ($rPI->successful()) {
                    $this->info("   ✓ " . mb_substr($rPI->body(), 0, 300));
                } else {
                    $this->warn("   ✗ " . mb_substr($rPI->body(), 0, 200));
                }
            }
        }

        $this->newLine();
        $this->info('=== Teste concluído ===');

        return self::SUCCESS;
    }

    private function resultLine($response, \Closure $successExtractor): void
    {
        $status = $response->status();

        if ($response->successful()) {
            try {
                $msg = $successExtractor($response->json());
                $this->info("   ✓ HTTP {$status} | {$msg}");
            } catch (\Throwable) {
                $this->info("   ✓ HTTP {$status} | " . mb_substr($response->body(), 0, 200));
            }
        } else {
            $this->error("   ✗ HTTP {$status} | " . mb_substr($response->body(), 0, 200));
        }

        $this->newLine();
    }

    private function testCurlSearch(string $accessToken): void
    {
        $url = 'https://api.mercadolibre.com/sites/MLB/search?q=iphone+15+pro+max&condition=used&limit=2';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$accessToken}",
                'Accept: application/json',
                'User-Agent: DGStore/1.0',
            ],
        ]);

        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->error("   ✗ cURL error: {$error}");
            return;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($body, true);
            $total = $data['paging']['total'] ?? 'N/A';
            $count = count($data['results'] ?? []);
            $this->info("   ✓ HTTP {$httpCode} | Total: {$total}, Results: {$count}");

            foreach (array_slice($data['results'] ?? [], 0, 2) as $item) {
                $price = number_format($item['price'] ?? 0, 2, ',', '.');
                $this->line("     - {$item['title']} => R\$ {$price}");
            }
        } else {
            $this->error("   ✗ HTTP {$httpCode} | " . mb_substr($body, 0, 200));
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
