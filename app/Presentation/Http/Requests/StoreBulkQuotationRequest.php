<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkQuotationRequest extends FormRequest
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
            'quotations' => ['required', 'array', 'min:1'],
            'quotations.*.product_id' => ['nullable', 'ulid', 'exists:products,id'],
            'quotations.*.product_name' => ['required', 'string', 'max:255'],
            'quotations.*.unit_price' => ['required', 'numeric', 'min:0.01'],
            'quotations.*.quantity' => ['nullable', 'numeric', 'min:0.01'],
            'quotations.*.unit' => ['nullable', 'string', 'max:20'],
            'quotations.*.notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'supplier_id' => 'fornecedor',
            'quoted_at' => 'data da cotação',
            'quotations' => 'cotações',
            'quotations.*.product_name' => 'nome do produto',
            'quotations.*.unit_price' => 'preço unitário',
            'quotations.*.quantity' => 'quantidade',
            'quotations.*.unit' => 'unidade',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Selecione um fornecedor.',
            'supplier_id.exists' => 'Fornecedor não encontrado.',
            'quoted_at.required' => 'Informe a data da cotação.',
            'quotations.required' => 'Adicione pelo menos uma cotação.',
            'quotations.min' => 'Adicione pelo menos uma cotação.',
            'quotations.*.product_name.required' => 'Informe o nome do produto.',
            'quotations.*.unit_price.required' => 'Informe o preço unitário.',
            'quotations.*.unit_price.min' => 'O preço deve ser maior que zero.',
        ];
    }
}
