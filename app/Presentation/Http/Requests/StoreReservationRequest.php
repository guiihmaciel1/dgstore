<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'product_id' => ['nullable'],
            'product_description' => ['required', 'string', 'max:255'],
            'source' => ['required', 'in:stock,quotation,manual'],
            'product_price' => ['required', 'numeric', 'min:0.01'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'expires_at' => ['required', 'date', 'after:today'],
            'initial_payment' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:cash,credit_card,debit_card,pix,bank_transfer'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_id' => 'cliente',
            'product_id' => 'produto',
            'product_description' => 'descrição do produto',
            'source' => 'origem',
            'product_price' => 'preço de venda',
            'cost_price' => 'preço de custo',
            'deposit_amount' => 'valor do sinal',
            'expires_at' => 'data limite',
            'initial_payment' => 'pagamento inicial',
            'payment_method' => 'forma de pagamento',
            'notes' => 'observações',
        ];
    }
}
