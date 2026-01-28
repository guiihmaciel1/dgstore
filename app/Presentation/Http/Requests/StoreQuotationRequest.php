<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'ulid', 'exists:suppliers,id'],
            'product_id' => ['nullable', 'ulid', 'exists:products,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'unit_price' => ['required', 'numeric', 'min:0.01'],
            'quantity' => ['nullable', 'numeric', 'min:0.01'],
            'unit' => ['nullable', 'string', 'max:20'],
            'quoted_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'supplier_id' => 'fornecedor',
            'product_id' => 'produto',
            'product_name' => 'nome do produto',
            'unit_price' => 'preço unitário',
            'quantity' => 'quantidade',
            'unit' => 'unidade',
            'quoted_at' => 'data da cotação',
            'notes' => 'observações',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Selecione um fornecedor.',
            'supplier_id.exists' => 'Fornecedor não encontrado.',
            'product_name.required' => 'Informe o nome do produto.',
            'unit_price.required' => 'Informe o preço unitário.',
            'unit_price.min' => 'O preço deve ser maior que zero.',
            'quoted_at.required' => 'Informe a data da cotação.',
        ];
    }
}
