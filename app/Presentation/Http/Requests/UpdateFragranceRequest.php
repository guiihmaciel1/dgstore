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
            'cost_price'            => ['nullable', 'numeric', 'min:0'],
            'shipping_rate_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pix_price'            => ['nullable', 'numeric', 'min:0'],
            'pix_discount_percent' => ['required', 'integer', 'min:1', 'max:30'],
            'stock_quantity'       => ['required', 'integer', 'min:0'],
            'active'               => ['boolean'],
            'sort_order'           => ['integer', 'min:0'],
            'tags'                 => ['nullable', 'array'],
            'tags.*'               => ['integer', 'exists:fragrance_tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'cost_price.numeric'              => 'O custo deve ser um número válido.',
            'shipping_rate_percent.numeric'  => 'A taxa de frete deve ser um número válido.',
            'shipping_rate_percent.max'      => 'A taxa de frete não pode ultrapassar 100%.',
            'pix_price.numeric'              => 'O preço PIX deve ser um número válido.',
            'pix_discount_percent.required'  => 'Informe o percentual de desconto PIX.',
            'pix_discount_percent.min'       => 'O desconto deve ser pelo menos 1%.',
            'pix_discount_percent.max'       => 'O desconto não pode ultrapassar 30%.',
            'stock_quantity.required'        => 'Informe a quantidade em estoque.',
            'stock_quantity.integer'         => 'A quantidade deve ser um número inteiro.',
        ];
    }
}
