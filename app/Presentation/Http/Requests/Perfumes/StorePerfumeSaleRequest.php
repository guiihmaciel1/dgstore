<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Perfumes;

use Illuminate\Foundation\Http\FormRequest;

class StorePerfumeSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perfume_customer_id'      => ['required', 'exists:perfume_customers,id'],
            'payment_method'           => ['required', 'in:cash,card,pix,mixed'],
            'payment_amount'           => ['required', 'numeric', 'min:0'],
            'installments'             => ['nullable', 'integer', 'min:1', 'max:12'],
            'discount'                 => ['nullable', 'numeric', 'min:0'],
            'notes'                    => ['nullable', 'string'],
            'from_reservation_id'      => ['nullable', 'exists:perfume_reservations,id'],
            
            // Items
            'items'                    => ['required', 'array', 'min:1'],
            'items.*.perfume_product_id' => ['required', 'exists:perfume_products,id'],
            'items.*.quantity'         => ['required', 'integer', 'min:1'],
            'items.*.unit_price'       => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'perfume_customer_id'      => 'cliente',
            'payment_method'           => 'forma de pagamento',
            'payment_amount'           => 'valor pago',
            'installments'             => 'parcelas',
            'discount'                 => 'desconto',
            'notes'                    => 'observações',
            'items'                    => 'itens',
            'items.*.perfume_product_id' => 'produto',
            'items.*.quantity'         => 'quantidade',
            'items.*.unit_price'       => 'preço unitário',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Adicione pelo menos um produto.',
            'items.min'      => 'Adicione pelo menos um produto.',
        ];
    }
}
