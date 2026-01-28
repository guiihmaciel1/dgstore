<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use App\Domain\Sale\Enums\PaymentMethod;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Enums\TradeInCondition;
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
            
            // Campos de pagamento misto
            'trade_in_value' => ['nullable', 'numeric', 'min:0'],
            'cash_payment' => ['nullable', 'numeric', 'min:0'],
            'card_payment' => ['nullable', 'numeric', 'min:0'],
            'cash_payment_method' => ['nullable', 'in:cash,pix'],
            
            // Campos de trade-in
            'trade_in' => ['nullable', 'array'],
            'trade_in.device_name' => ['required_with:trade_in.estimated_value', 'nullable', 'string', 'max:255'],
            'trade_in.device_model' => ['nullable', 'string', 'max:255'],
            'trade_in.imei' => ['nullable', 'string', 'max:50'],
            'trade_in.estimated_value' => ['required_with:trade_in.device_name', 'nullable', 'numeric', 'min:0'],
            'trade_in.condition' => ['nullable', Rule::enum(TradeInCondition::class)],
            'trade_in.notes' => ['nullable', 'string'],
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
            'trade_in_value' => 'valor do trade-in',
            'cash_payment' => 'entrada à vista',
            'card_payment' => 'valor no cartão',
            'cash_payment_method' => 'forma de entrada',
            'trade_in.device_name' => 'nome do aparelho',
            'trade_in.device_model' => 'modelo do aparelho',
            'trade_in.imei' => 'IMEI',
            'trade_in.estimated_value' => 'valor do aparelho',
            'trade_in.condition' => 'condição do aparelho',
            'trade_in.notes' => 'observações do aparelho',
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
            'trade_in.device_name.required_with' => 'Informe o nome do aparelho para o trade-in.',
            'trade_in.estimated_value.required_with' => 'Informe o valor do aparelho para o trade-in.',
        ];
    }
}
