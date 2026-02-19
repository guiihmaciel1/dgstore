<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Perfumes;

use App\Domain\Perfumes\Models\PerfumeProduct;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'payment_amount'           => ['nullable', 'numeric', 'min:0'],
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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Validar estoque para cada item
            if ($this->has('items') && is_array($this->items)) {
                foreach ($this->items as $index => $item) {
                    $product = PerfumeProduct::find($item['perfume_product_id'] ?? null);
                    
                    if ($product && isset($item['quantity'])) {
                        if ($item['quantity'] > $product->stock_quantity) {
                            $validator->errors()->add(
                                "items.{$index}.quantity",
                                "A quantidade solicitada ({$item['quantity']}) excede o estoque disponível ({$product->stock_quantity}) para o produto {$product->name}."
                            );
                        }
                    }
                }
            }

            // Validar que desconto não seja maior que subtotal
            if ($this->has('items') && $this->has('discount') && $this->discount > 0) {
                $subtotal = collect($this->items)->sum(function ($item) {
                    $product = PerfumeProduct::find($item['perfume_product_id'] ?? null);
                    return $product ? ($product->sale_price * ($item['quantity'] ?? 0)) : 0;
                });

                if ($this->discount > $subtotal) {
                    $validator->errors()->add('discount', 'O desconto não pode ser maior que o subtotal da venda.');
                }
            }
        });
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
