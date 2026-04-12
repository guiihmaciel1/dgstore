<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\News\Services\AppleNewsService;
use Illuminate\Console\Command;

class FetchAppleNewsCommand extends Command
{
    protected $signature = 'news:fetch-apple';
    protected $description = 'Busca notícias Apple dos feeds RSS e atualiza o cache';

    public function handle(AppleNewsService $service): int
    {
        $this->info('Buscando notícias Apple...');

        try {
            $items = $service->fetchAndCache();
            $this->info(count($items) . ' notícia(s) carregada(s) com sucesso.');
        } catch (\Throwable $e) {
            $this->error('Erro ao buscar notícias: ' . $e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
