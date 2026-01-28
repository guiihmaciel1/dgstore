<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\DTOs\SaleData;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Domain\Stock\Services\StockService;
use Exception;

class CreateSaleUseCase
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly StockService $stockService
    ) {}

    /**
     * Executa a criação de uma nova venda
     * 
     * @throws Exception se não houver estoque suficiente
     */
    public function execute(SaleData $data): Sale
    {
        // 1. Validar disponibilidade de estoque
        $this->validateStockAvailability($data);

        // 2. Criar a venda (o repository já cuida de criar items e movimentar estoque)
        $sale = $this->saleRepository->create($data);

        return $sale;
    }

    /**
     * Valida se há estoque disponível para todos os itens
     * 
     * @throws Exception
     */
    private function validateStockAvailability(SaleData $data): void
    {
        $unavailableItems = [];

        foreach ($data->items as $item) {
            $product = $this->productRepository->find($item->productId);

            if (!$product) {
                $unavailableItems[] = [
                    'product_id' => $item->productId,
                    'message' => 'Produto não encontrado',
                ];
                continue;
            }

            if (!$product->active) {
                $unavailableItems[] = [
                    'product_id' => $item->productId,
                    'product_name' => $product->name,
                    'message' => 'Produto inativo',
                ];
                continue;
            }

            if ($product->stock_quantity < $item->quantity) {
                $unavailableItems[] = [
                    'product_id' => $item->productId,
                    'product_name' => $product->name,
                    'requested' => $item->quantity,
                    'available' => $product->stock_quantity,
                    'message' => "Estoque insuficiente. Disponível: {$product->stock_quantity}",
                ];
            }
        }

        if (!empty($unavailableItems)) {
            throw new Exception(
                'Não foi possível completar a venda. Verifique o estoque dos produtos: ' .
                collect($unavailableItems)->pluck('message')->implode('; ')
            );
        }
    }
}
