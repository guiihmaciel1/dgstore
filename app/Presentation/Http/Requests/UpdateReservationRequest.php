<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'product_description' => ['required', 'string', 'max:255'],
            'product_price' => ['required', 'numeric', 'min:0.01'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'expires_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_id' => 'cliente',
            'product_description' => 'descrição do produto',
            'product_price' => 'preço de venda',
            'cost_price' => 'preço de custo',
            'deposit_amount' => 'valor do sinal',
            'expires_at' => 'data limite',
            'notes' => 'observações',
        ];
    }
}
