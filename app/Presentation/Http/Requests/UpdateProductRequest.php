<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Enums\ProductCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($productId)],
            'category' => ['required', Rule::enum(ProductCategory::class)],
            'model' => ['nullable', 'string', 'max:100'],
            'storage' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'condition' => ['required', Rule::enum(ProductCondition::class)],
            'imei' => ['nullable', 'string', 'max:20', Rule::unique('products', 'imei')->ignore($productId)],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'sku' => 'SKU',
            'category' => 'categoria',
            'model' => 'modelo',
            'storage' => 'armazenamento',
            'color' => 'cor',
            'condition' => 'condição',
            'imei' => 'IMEI',
            'cost_price' => 'preço de custo',
            'sale_price' => 'preço de venda',
            'stock_quantity' => 'quantidade em estoque',
            'min_stock_alert' => 'alerta de estoque mínimo',
            'supplier' => 'fornecedor',
            'notes' => 'observações',
            'active' => 'ativo',
        ];
    }
}
