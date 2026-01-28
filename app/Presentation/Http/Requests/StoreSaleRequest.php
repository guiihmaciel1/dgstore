<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use App\Domain\Sale\Enums\PaymentMethod;
use App\Domain\Sale\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'payment_status' => ['required', Rule::enum(PaymentStatus::class)],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'installments' => ['nullable', 'integer', 'min:1', 'max:24'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_id' => 'cliente',
            'payment_method' => 'forma de pagamento',
            'payment_status' => 'status do pagamento',
            'discount' => 'desconto',
            'installments' => 'parcelas',
            'notes' => 'observações',
            'items' => 'itens',
            'items.*.product_id' => 'produto',
            'items.*.quantity' => 'quantidade',
            'items.*.unit_price' => 'preço unitário',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Adicione pelo menos um item à venda.',
            'items.min' => 'Adicione pelo menos um item à venda.',
            'items.*.product_id.required' => 'Selecione o produto para cada item.',
            'items.*.product_id.exists' => 'Produto inválido selecionado.',
            'items.*.quantity.min' => 'A quantidade deve ser pelo menos 1.',
        ];
    }
}
