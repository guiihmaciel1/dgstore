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

    protected function prepareForValidation(): void
    {
        if ($this->condition === 'new') {
            $this->merge([
                'has_box' => null,
                'has_cable' => null,
                'battery_health' => null,
            ]);
        }
    }

    public function rules(): array
    {
        $productId = $this->route('product');
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($productId)],
            'category' => ['required', Rule::enum(ProductCategory::class)],
            'model' => ['required', 'string', 'max:100'],
            'storage' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:50'],
            'condition' => ['required', Rule::enum(ProductCondition::class)],
            'has_box' => ['nullable', 'boolean'],
            'has_cable' => ['nullable', 'boolean'],
            'battery_health' => ['nullable', 'integer', 'min:0', 'max:100'],
            'imei' => ['nullable', 'string', 'max:20', Rule::unique('products', 'imei')->ignore($productId)],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'resale_price' => ['nullable', 'numeric', 'min:0'],
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
            'has_box' => 'tem caixa',
            'has_cable' => 'tem cabo',
            'battery_health' => 'saúde da bateria',
            'imei' => 'IMEI',
            'cost_price' => 'preço de custo',
            'sale_price' => 'preço de venda',
            'resale_price' => 'preço de repasse',
            'stock_quantity' => 'quantidade em estoque',
            'min_stock_alert' => 'alerta de estoque mínimo',
            'supplier' => 'fornecedor',
            'notes' => 'observações',
            'active' => 'ativo',
        ];
    }
}
