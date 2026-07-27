<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\System\Models\RadarProduct;
use Illuminate\Console\Command;

class ManageRadarProductsCommand extends Command
{
    protected $signature = 'radar:manage
        {action : Ação: add, list, remove, toggle}
        {--url= : URL do produto no Compras Paraguai (para add)}
        {--name= : Nome do produto (para add)}
        {--id= : ID do produto (para remove/toggle)}';

    protected $description = 'Gerencia produtos monitorados pelo Radar PY';

    public function handle(): int
    {
        return match ($this->argument('action')) {
            'add'    => $this->addProduct(),
            'list'   => $this->listProducts(),
            'remove' => $this->removeProduct(),
            'toggle' => $this->toggleProduct(),
            default  => $this->showUsage(),
        };
    }

    private function addProduct(): int
    {
        $url = $this->option('url');
        $name = $this->option('name');

        if (! $url) {
            $this->error('URL obrigatória. Use --url=<url>');

            return self::FAILURE;
        }

        if (! str_contains($url, 'comprasparaguai.com.br')) {
            $this->error('URL deve ser do site comprasparaguai.com.br');

            return self::FAILURE;
        }

        if (! str_contains($url, 'ordem=menor-preco')) {
            $url .= (str_contains($url, '?') ? '&' : '?') . 'ordem=menor-preco';
        }

        if (! $name) {
            $name = $this->extractNameFromUrl($url);
        }

        $product = RadarProduct::create([
            'name'   => $name,
            'url'    => $url,
            'active' => true,
        ]);

        $this->info("Produto adicionado: [{$product->id}] {$product->name}");
        $this->info("URL: {$product->url}");
        $this->info('Execute "php artisan radar:fetch-prices" para buscar preços agora.');

        return self::SUCCESS;
    }

    private function listProducts(): int
    {
        $products = RadarProduct::all();

        if ($products->isEmpty()) {
            $this->info('Nenhum produto cadastrado no Radar PY.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Nome', 'Ativo', 'URL'],
            $products->map(fn ($p) => [
                $p->id,
                $p->name,
                $p->active ? 'Sim' : 'Não',
                \Illuminate\Support\Str::limit($p->url, 60),
            ])
        );

        return self::SUCCESS;
    }

    private function removeProduct(): int
    {
        $id = $this->option('id');

        if (! $id) {
            $this->error('ID obrigatório. Use --id=<id>');

            return self::FAILURE;
        }

        $product = RadarProduct::find($id);

        if (! $product) {
            $this->error("Produto #{$id} não encontrado.");

            return self::FAILURE;
        }

        $product->delete();
        $this->info("Produto removido: [{$product->id}] {$product->name}");

        return self::SUCCESS;
    }

    private function toggleProduct(): int
    {
        $id = $this->option('id');

        if (! $id) {
            $this->error('ID obrigatório. Use --id=<id>');

            return self::FAILURE;
        }

        $product = RadarProduct::find($id);

        if (! $product) {
            $this->error("Produto #{$id} não encontrado.");

            return self::FAILURE;
        }

        $product->update(['active' => ! $product->active]);
        $status = $product->active ? 'ativado' : 'desativado';
        $this->info("Produto {$status}: [{$product->id}] {$product->name}");

        return self::SUCCESS;
    }

    private function showUsage(): int
    {
        $this->info('Uso: php artisan radar:manage {add|list|remove|toggle}');
        $this->info('  add    --url=<url> --name=<nome>  Adiciona produto');
        $this->info('  list                               Lista produtos');
        $this->info('  remove --id=<id>                   Remove produto');
        $this->info('  toggle --id=<id>                   Ativa/desativa');

        return self::SUCCESS;
    }

    private function extractNameFromUrl(string $url): string
    {
        // Ex: .../celular-apple-iphone-17-pro-max-256gb_64041/
        if (preg_match('/\.br\/([^\/\?]+?)(?:_\d+)?\/?(?:\?|$)/', $url, $m)) {
            return ucwords(str_replace('-', ' ', $m[1]));
        }

        return 'Produto sem nome';
    }
}
