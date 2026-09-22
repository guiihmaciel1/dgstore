<?php

declare(strict_types=1);

namespace App\Domain\Fragrance\Services;

use App\Domain\Fragrance\DTOs\FragranceScrapedData;
use App\Domain\Fragrance\Models\FragranceProduct;
use InvalidArgumentException;

class FragranticaScraperService
{
    public function __construct(
        private readonly FragranticaHtmlParser $parser,
    ) {}

    /**
     * Importa perfume a partir de HTML já obtido pelo browser do usuário.
     */
    public function importFromHtml(string $url, string $html): FragranceProduct
    {
        $data = $this->parser->parse($url, $html);

        return $this->saveScrapedData($data);
    }

    /**
     * Re-importa dados de um produto existente a partir de HTML atualizado.
     */
    public function rescrapeFromHtml(FragranceProduct $product, string $html): FragranceProduct
    {
        $data = $this->parser->parse($product->fragrantica_url, $html);

        return $this->updateScrapedData($product, $data);
    }

    // ---- Persistence ----

    private function saveScrapedData(FragranceScrapedData $data): FragranceProduct
    {
        $existing = FragranceProduct::withTrashed()
            ->where('fragrantica_id', $data->fragranticaId)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }

            return $this->updateScrapedData($existing, $data);
        }

        $product = FragranceProduct::create($data->toProductArray());

        $this->syncAccords($product, $data->accords);
        $this->syncNotes($product, $data->notes);

        return $product->load(['accords', 'notes']);
    }

    private function updateScrapedData(FragranceProduct $product, FragranceScrapedData $data): FragranceProduct
    {
        $updateData = $data->toProductArray();
        unset($updateData['fragrantica_id'], $updateData['fragrantica_url']);

        // Não sobrescreve inspired_by com null se já foi preenchido manualmente
        if ($data->inspiredBy === null && $product->inspired_by !== null) {
            unset($updateData['inspired_by']);
        }

        $product->update($updateData);

        $this->syncAccords($product, $data->accords);
        $this->syncNotes($product, $data->notes);

        return $product->fresh(['accords', 'notes']);
    }

    /**
     * @param  array<int, array{name: string, percentage: float, color: string}>  $accords
     */
    private function syncAccords(FragranceProduct $product, array $accords): void
    {
        $product->accords()->delete();

        foreach ($accords as $i => $accord) {
            $product->accords()->create([
                'name'       => $accord['name'],
                'percentage' => $accord['percentage'],
                'color'      => $accord['color'],
                'sort_order' => $i,
            ]);
        }
    }

    /**
     * @param  array<int, array{layer: string, name: string, image_url: ?string}>  $notes
     */
    private function syncNotes(FragranceProduct $product, array $notes): void
    {
        $product->notes()->delete();

        foreach ($notes as $i => $note) {
            $product->notes()->create([
                'layer'      => $note['layer'],
                'name'       => $note['name'],
                'image_url'  => $note['image_url'] ?? null,
                'sort_order' => $i,
            ]);
        }
    }
}
