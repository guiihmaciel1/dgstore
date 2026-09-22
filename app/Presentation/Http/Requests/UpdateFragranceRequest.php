<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFragranceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sale_price'     => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'active'         => ['boolean'],
            'sort_order'     => ['integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'sale_price.numeric'      => 'O preço deve ser um número válido.',
            'sale_price.min'          => 'O preço não pode ser negativo.',
            'stock_quantity.required' => 'Informe a quantidade em estoque.',
            'stock_quantity.integer'  => 'A quantidade deve ser um número inteiro.',
            'stock_quantity.min'      => 'A quantidade não pode ser negativa.',
        ];
    }
}
