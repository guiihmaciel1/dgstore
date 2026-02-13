<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImportQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'ulid', 'exists:suppliers,id'],
            'quoted_at' => ['required', 'date'],
            'exchange_rate' => ['required', 'numeric', 'min:0.0001'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.price_usd' => ['required', 'numeric', 'min:0.01'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:1'],
            'items.*.category' => ['nullable', 'string', 'max:255'],
            'items.*.selected' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'supplier_id' => 'fornecedor',
            'quoted_at' => 'data da cotação',
            'exchange_rate' => 'taxa de câmbio',
            'items' => 'itens',
            'items.*.product_name' => 'nome do produto',
            'items.*.price_usd' => 'preço em dólar',
            'items.*.quantity' => 'quantidade',
            'items.*.category' => 'categoria',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Selecione um fornecedor.',
            'supplier_id.exists' => 'Fornecedor não encontrado.',
            'quoted_at.required' => 'Informe a data da cotação.',
            'exchange_rate.required' => 'Informe a taxa de câmbio (cotação do dólar).',
            'exchange_rate.min' => 'A taxa de câmbio deve ser maior que zero.',
            'items.required' => 'Nenhum item para importar.',
            'items.min' => 'Selecione pelo menos um item para importar.',
            'items.*.product_name.required' => 'Informe o nome do produto.',
            'items.*.price_usd.required' => 'Informe o preço em dólar.',
            'items.*.price_usd.min' => 'O preço deve ser maior que zero.',
        ];
    }
}
