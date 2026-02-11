<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DiagnosticScraperCommand extends Command
{
    protected $signature = 'valuation:diagnostic';

    protected $description = 'Testa conectividade com Mercado Livre e OLX para diagnóstico';

    public function handle(): int
    {
        $this->info('=== Diagnóstico de Scraping ===');
        $this->newLine();

        // 1. Verifica extensão cURL
        $this->info('1. Verificando extensão cURL...');
        if (extension_loaded('curl')) {
            $this->info('   ✓ cURL disponível: ' . curl_version()['version']);
            $this->info('   ✓ SSL: ' . curl_version()['ssl_version']);
        } else {
            $this->error('   ✗ cURL NÃO está instalado!');
            return self::FAILURE;
        }
        $this->newLine();

        // 2. Testa Mercado Livre (HTML)
        $this->info('2. Testando Mercado Livre (HTML)...');
        $mlUrl = 'https://lista.mercadolivre.com.br/iphone-15-pro-max-256gb-usado';
        $mlResult = $this->testUrl($mlUrl);
        $this->displayResult($mlResult);
        $this->newLine();

        // 3. Verifica se tem JSON-LD no HTML do ML
        if ($mlResult['body']) {
            $this->info('3. Verificando conteúdo HTML do ML...');
            $jsonLdCount = substr_count($mlResult['body'], 'application/ld+json');
            $productCount = substr_count($mlResult['body'], '"@type":"Product"');
            $bodySize = strlen($mlResult['body']);
            $this->info("   Tamanho do body: {$bodySize} bytes");
            $this->info("   Blocos JSON-LD: {$jsonLdCount}");
            $this->info("   Produtos encontrados: {$productCount}");

            if (preg_match('/"price":(\d+(?:\.\d+)?)/', $mlResult['body'], $m)) {
                $this->info("   Exemplo de preço: R$ " . number_format((float) $m[1], 2, ',', '.'));
            }

            if (str_contains($mlResult['body'], 'account-verification')) {
                $this->warn('   ⚠ Redirecionado para account-verification (bloqueio de cookies)!');
            }
            if (str_contains($mlResult['body'], 'cookie-consent-banner')) {
                $this->warn('   ⚠ Página de consentimento de cookies!');
            }
            if (str_contains($mlResult['body'], 'captcha')) {
                $this->warn('   ⚠ HTML contém referência a CAPTCHA!');
            }
        }
        $this->newLine();

        // 4. Testa ML com cookie jar (sessão aquecida)
        $this->info('4. Testando ML com cookie jar (bypass de verificação)...');
        $cookieResult = $this->testWithCookieJar();
        $this->newLine();

        // 5. Testa OLX
        $this->info('5. Testando OLX...');
        $olxUrl = 'https://www.olx.com.br/eletronicos-e-celulares/celulares/iphone/estado-sp?q=iphone+15+pro+max+256gb';
        $olxResult = $this->testUrl($olxUrl);
        $this->displayResult($olxResult);
        $this->newLine();

        // 6. Testa Google (referência de conectividade)
        $this->info('6. Testando conectividade geral (Google)...');
        $googleResult = $this->testUrl('https://www.google.com.br');
        $this->displayResult($googleResult);

        $this->newLine();
        $this->info('=== Diagnóstico concluído ===');

        return self::SUCCESS;
    }

    private function testWithCookieJar(): void
    {
        $cookieJar = tempnam(sys_get_temp_dir(), 'ml_diag_');
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36';

        $headers = [
            "User-Agent: {$ua}",
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
            'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
            'Accept-Encoding: gzip, deflate, br',
            'Sec-Ch-Ua: "Chromium";v="122", "Not(A:Brand";v="24", "Google Chrome";v="122"',
            'Sec-Ch-Ua-Mobile: ?0',
            'Sec-Ch-Ua-Platform: "Windows"',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: none',
            'Sec-Fetch-User: ?1',
            'Upgrade-Insecure-Requests: 1',
            'Referer: https://www.mercadolivre.com.br/',
        ];

        $makeRequest = function (string $url) use ($cookieJar, $headers) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 25,
                CURLOPT_ENCODING => '',
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_COOKIEJAR => $cookieJar,
                CURLOPT_COOKIEFILE => $cookieJar,
                CURLOPT_HTTPHEADER => $headers,
            ]);
            $body = curl_exec($ch);
            $info = curl_getinfo($ch);
            $error = curl_error($ch);
            curl_close($ch);

            return ['body' => $body, 'info' => $info, 'error' => $error];
        };

        // Passo 1: Visitar homepage
        $this->line('   Passo 1: Visitando homepage do ML...');
        $r1 = $makeRequest('https://www.mercadolivre.com.br');
        $this->line("   HTTP: {$r1['info']['http_code']} | Tamanho: " . number_format($r1['info']['size_download']) . " bytes");
        if ($r1['info']['url'] !== 'https://www.mercadolivre.com.br') {
            $this->line("   Redirecionado para: {$r1['info']['url']}");
        }
        sleep(1);

        // Passo 2: Visitar busca genérica
        $this->line('   Passo 2: Visitando busca genérica...');
        $r2 = $makeRequest('https://lista.mercadolivre.com.br/iphone');
        $this->line("   HTTP: {$r2['info']['http_code']} | Tamanho: " . number_format($r2['info']['size_download']) . " bytes");
        if (str_contains($r2['info']['url'], 'account-verification')) {
            $this->warn('   ⚠ Ainda redirecionando para account-verification');
        }
        sleep(1);

        // Passo 3: Visitar verificação diretamente
        $this->line('   Passo 3: Aceitando verificação...');
        $r3 = $makeRequest('https://www.mercadolivre.com.br/gz/account-verification?go=https%3A%2F%2Flista.mercadolivre.com.br%2Fiphone');
        $this->line("   HTTP: {$r3['info']['http_code']} | Tamanho: " . number_format($r3['info']['size_download']) . " bytes");
        sleep(1);

        // Passo 4: Tentar busca real com cookies
        $this->line('   Passo 4: Buscando com cookies (busca real)...');
        $r4 = $makeRequest('https://lista.mercadolivre.com.br/iphone-15-pro-max-256gb-usado');
        $this->line("   HTTP: {$r4['info']['http_code']} | Tamanho: " . number_format($r4['info']['size_download']) . " bytes");
        if ($r4['info']['url'] !== 'https://lista.mercadolivre.com.br/iphone-15-pro-max-256gb-usado') {
            $this->line("   URL final: {$r4['info']['url']}");
        }

        if ($r4['body']) {
            $jsonLdCount = substr_count($r4['body'], 'application/ld+json');
            $productCount = substr_count($r4['body'], '"@type":"Product"');
            $hasPrice = (bool) preg_match('/"price":\d+/', $r4['body']);

            $this->line("   JSON-LD: {$jsonLdCount} | Produtos: {$productCount} | Tem preço: " . ($hasPrice ? 'SIM' : 'NÃO'));

            if ($productCount > 0 || $hasPrice) {
                $this->info('   ✓ Cookie jar FUNCIONA! A busca retornou dados.');
            } else {
                $this->warn('   ✗ Cookie jar não resolveu. Verificar alternativas.');

                // Mostra trecho do body para debug
                $preview = mb_substr(strip_tags($r4['body']), 0, 200);
                $this->line('   Preview: ' . preg_replace('/\s+/', ' ', $preview));
            }
        }

        // Passo 5: Tentar URL alternativa
        $this->line('   Passo 5: Testando URL alternativa (jm/search)...');
        $r5 = $makeRequest('https://www.mercadolivre.com.br/jm/search?as_word=iphone+15+pro+max+256gb+usado');
        $this->line("   HTTP: {$r5['info']['http_code']} | Tamanho: " . number_format($r5['info']['size_download']) . " bytes");
        if ($r5['body']) {
            $productCount = substr_count($r5['body'], '"@type":"Product"');
            $hasPrice = (bool) preg_match('/"price":\d+/', $r5['body']);
            $this->line("   Produtos: {$productCount} | Tem preço: " . ($hasPrice ? 'SIM' : 'NÃO'));

            if ($productCount > 0 || $hasPrice) {
                $this->info('   ✓ URL alternativa FUNCIONA!');
            }
        }

        // Mostra cookies salvos
        $cookieContent = file_exists($cookieJar) ? file_get_contents($cookieJar) : '';
        $cookieLines = array_filter(explode("\n", $cookieContent), fn ($l) => $l && !str_starts_with($l, '#'));
        $this->line('   Cookies salvos: ' . count($cookieLines));

        @unlink($cookieJar);
    }

    private function testUrl(string $url): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_ENCODING => '',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                'Accept-Encoding: gzip, deflate, br',
                'Cache-Control: no-cache',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: none',
                'Sec-Fetch-User: ?1',
                'Upgrade-Insecure-Requests: 1',
            ],
        ]);

        $body = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'url' => $url,
            'http_code' => $info['http_code'],
            'size' => $info['size_download'],
            'time' => round($info['total_time'], 2),
            'redirect_count' => $info['redirect_count'],
            'effective_url' => $info['url'] !== $url ? $info['url'] : null,
            'error' => $error ?: null,
            'body' => $body ?: null,
        ];
    }

    private function displayResult(array $result): void
    {
        $this->line("   URL: {$result['url']}");

        if ($result['error']) {
            $this->error("   ✗ Erro: {$result['error']}");
            return;
        }

        $statusIcon = $result['http_code'] === 200 ? '✓' : '✗';
        $statusColor = $result['http_code'] === 200 ? 'info' : 'error';

        $this->line("   {$statusIcon} HTTP: {$result['http_code']}");
        $this->line("   Tamanho: " . number_format($result['size']) . " bytes");
        $this->line("   Tempo: {$result['time']}s");

        if ($result['redirect_count'] > 0) {
            $this->line("   Redirects: {$result['redirect_count']}");
        }

        if ($result['effective_url']) {
            $this->line("   URL final: {$result['effective_url']}");
        }
    }
}
