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
            $this->info('3. Verificando JSON-LD no HTML do ML...');
            $jsonLdCount = substr_count($mlResult['body'], 'application/ld+json');
            $productCount = substr_count($mlResult['body'], '"@type":"Product"');
            $this->info("   Blocos JSON-LD: {$jsonLdCount}");
            $this->info("   Produtos encontrados: {$productCount}");

            // Tenta extrair um preço para confirmar
            if (preg_match('/"price":(\d+(?:\.\d+)?)/', $mlResult['body'], $m)) {
                $this->info("   Exemplo de preço: R$ " . number_format((float) $m[1], 2, ',', '.'));
            }

            // Verifica se é um CAPTCHA/challenge
            if (str_contains($mlResult['body'], 'captcha') || str_contains($mlResult['body'], 'challenge')) {
                $this->warn('   ⚠ HTML contém referência a CAPTCHA/challenge!');
            }

            // Verifica se é redirect para login
            if (str_contains($mlResult['body'], 'login') && !str_contains($mlResult['body'], '"price"')) {
                $this->warn('   ⚠ HTML pode ser uma página de login/bloqueio!');
            }

            // Mostra primeiros 500 chars do body para inspeção
            $this->info('   Primeiros 300 chars do body:');
            $preview = mb_substr(strip_tags($mlResult['body']), 0, 300);
            $this->line('   ' . preg_replace('/\s+/', ' ', $preview));
        }
        $this->newLine();

        // 4. Testa API do ML (para referência)
        $this->info('4. Testando API Mercado Libre...');
        $apiUrl = 'https://api.mercadolibre.com/sites/MLB/search?q=iphone+15+pro&limit=2';
        $apiResult = $this->testUrl($apiUrl);
        $this->displayResult($apiResult);
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
