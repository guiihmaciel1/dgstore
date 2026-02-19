<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Perfumes;

use Illuminate\Foundation\Http\FormRequest;

class StorePerfumeReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perfume_customer_id'  => ['required', 'exists:perfume_customers,id'],
            'perfume_product_id'   => ['nullable', 'exists:perfume_products,id'],
            'product_description'  => ['required_without:perfume_product_id', 'string'],
            'product_price'        => ['required', 'numeric', 'min:0'],
            'deposit_amount'       => ['required', 'numeric', 'min:0'],
            'initial_payment'      => ['nullable', 'numeric', 'min:0', 'lte:deposit_amount'],
            'payment_method'       => ['required_with:initial_payment', 'in:pix,cash,card'],
            'expires_at'           => ['nullable', 'date', 'after:today'],
            'notes'                => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'perfume_customer_id' => 'cliente',
            'perfume_product_id'  => 'produto',
            'product_description' => 'descrição do produto',
            'product_price'       => 'preço do produto',
            'deposit_amount'      => 'valor do sinal',
            'initial_payment'     => 'pagamento inicial',
            'payment_method'      => 'forma de pagamento',
            'expires_at'          => 'data de vencimento',
            'notes'               => 'observações',
        ];
    }

    public function messages(): array
    {
        return [
            'initial_payment.lte' => 'O pagamento inicial não pode ser maior que o valor do sinal.',
            'expires_at.after'    => 'A data de vencimento deve ser futura.',
        ];
    }
}
