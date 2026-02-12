<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DiagnosticScraperCommand extends Command
{
    protected $signature = 'valuation:diagnostic';

    protected $description = 'Testa conectividade com a API do Mercado Livre para diagnóstico';

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

        // 2. Testa API ML (catálogo de produtos)
        $this->info('2. Testando API Mercado Livre (catálogo)...');
        $mlApiUrl = 'https://api.mercadolibre.com/products/search?site_id=MLB&q=iphone+15+pro+max+256GB&status=active';
        $mlApiResult = $this->testUrl($mlApiUrl);
        $this->displayResult($mlApiResult);
        $this->newLine();

        // 3. Testa Google (referência de conectividade)
        $this->info('3. Testando conectividade geral (Google)...');
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
                'Accept: application/json,text/html,*/*;q=0.8',
                'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
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

        $this->line("   {$statusIcon} HTTP: {$result['http_code']}");
        $this->line('   Tamanho: ' . number_format($result['size']) . ' bytes');
        $this->line("   Tempo: {$result['time']}s");

        if ($result['redirect_count'] > 0) {
            $this->line("   Redirects: {$result['redirect_count']}");
        }

        if ($result['effective_url']) {
            $this->line("   URL final: {$result['effective_url']}");
        }
    }
}
