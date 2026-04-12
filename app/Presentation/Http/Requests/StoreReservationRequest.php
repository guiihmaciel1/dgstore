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
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.cost_price' => ['required', 'numeric', 'min:0'],
            'items.*.sale_price' => ['required', 'numeric', 'min:0.01'],
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
            'items' => 'produtos',
            'items.*.product_id' => 'produto',
            'items.*.cost_price' => 'preço de custo',
            'items.*.sale_price' => 'preço de venda',
            'deposit_amount' => 'valor do sinal',
            'expires_at' => 'data limite',
            'initial_payment' => 'pagamento inicial',
            'payment_method' => 'forma de pagamento',
            'notes' => 'observações',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Selecione um cliente para a reserva.',
            'items.required' => 'Adicione pelo menos um produto à reserva.',
            'items.min' => 'Adicione pelo menos um produto à reserva.',
            'items.*.product_id.required' => 'Selecione o produto.',
            'items.*.product_id.exists' => 'Produto selecionado não encontrado.',
            'items.*.sale_price.required' => 'Informe o preço de venda do produto.',
            'items.*.sale_price.min' => 'O preço de venda deve ser maior que zero.',
        ];
    }
}
