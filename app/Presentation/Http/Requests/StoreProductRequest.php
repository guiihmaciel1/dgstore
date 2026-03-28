<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Enums\ProductCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'category' => ['required', Rule::enum(ProductCategory::class)],
            'model' => ['required', 'string', 'max:100'],
            'storage' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:50'],
            'condition' => ['required', Rule::enum(ProductCondition::class)],
            'has_box' => ['nullable', 'boolean'],
            'has_cable' => ['nullable', 'boolean'],
            'battery_health' => ['nullable', 'integer', 'min:0', 'max:100'],
            'imei' => ['nullable', 'string', 'max:20', 'unique:products,imei'],
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
            'stock_quantity' => 'quantidade em estoque',
            'min_stock_alert' => 'alerta de estoque mínimo',
            'supplier' => 'fornecedor',
            'notes' => 'observações',
            'active' => 'ativo',
        ];
    }

    public function messages(): array
    {
        return [
            'sku.unique' => 'Este SKU já está em uso.',
            'imei.unique' => 'Este IMEI já está cadastrado.',
        ];
    }
}
