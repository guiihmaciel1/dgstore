<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->customer_phone) {
            $this->merge([
                'customer_phone' => preg_replace('/\D/', '', $this->customer_phone),
            ]);
        }

        if ($this->customer_cpf) {
            $this->merge([
                'customer_cpf' => preg_replace('/\D/', '', $this->customer_cpf),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            // Produto
            'product_imei' => ['required', 'string', 'max:20'],
            'product_id' => ['nullable', 'exists:products,id'],
            'consignment_item_id' => ['nullable', 'exists:consignment_stock_items,id'],
            'product_source' => ['required', 'in:own_stock,consignment'],

            // Cliente existente ou novo
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_name' => ['required_without:customer_id', 'nullable', 'string', 'max:255'],
            'customer_phone' => ['required_without:customer_id', 'nullable', 'string', 'min:10', 'max:11'],
            'customer_cpf' => ['nullable', 'string', 'size:11'],
            'customer_instagram' => ['nullable', 'string', 'max:255'],

            // Valores
            'unit_price' => ['required', 'numeric', 'min:0.01'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'condition' => ['required', 'in:new,used'],

            // Sinal
            'down_payment' => ['required', 'numeric', 'min:50'],
            'down_payment_method' => ['required', 'in:pix,cash'],

            // Pagamento do saldo
            'payment_method' => ['required', 'in:pix,cash,credit_card'],
            'installments' => ['nullable', 'required_if:payment_method,credit_card', 'integer', 'min:1', 'max:18'],
            'card_gross_amount' => ['nullable', 'numeric', 'min:0'],
            'card_net_amount' => ['nullable', 'numeric', 'min:0'],
            'card_fee_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],

            // Trade-in
            'trade_in_model' => ['nullable', 'string', 'max:255'],
            'trade_in_value' => ['nullable', 'numeric', 'min:0'],
            'trade_in_condition' => ['nullable', 'string', 'max:50'],

            // Saldo final
            'final_balance' => ['required', 'numeric', 'min:0'],

            // Meta
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Preço não pode ser menor que custo
            $unitPrice = (float) $this->unit_price;
            $costPrice = (float) $this->cost_price;

            if ($unitPrice > 0 && $costPrice > 0 && $unitPrice < $costPrice) {
                $validator->errors()->add(
                    'unit_price',
                    'O preço de venda não pode ser menor que o custo (R$ ' . number_format($costPrice, 2, ',', '.') . ').'
                );
            }

            // Deve ter produto de alguma fonte
            if (!$this->product_id && !$this->consignment_item_id) {
                $validator->errors()->add(
                    'product_imei',
                    'Produto não encontrado no sistema. Verifique o IMEI informado.'
                );
            }

            // Deve ter cliente
            if (!$this->customer_id && !$this->customer_name) {
                $validator->errors()->add(
                    'customer_id',
                    'Informe um cliente existente ou cadastre um novo.'
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            'product_imei' => 'IMEI',
            'customer_id' => 'cliente',
            'customer_name' => 'nome do cliente',
            'customer_phone' => 'telefone do cliente',
            'customer_cpf' => 'CPF do cliente',
            'unit_price' => 'preço de venda',
            'cost_price' => 'custo',
            'down_payment' => 'sinal',
            'down_payment_method' => 'método do sinal',
            'payment_method' => 'forma de pagamento',
            'installments' => 'parcelas',
            'trade_in_value' => 'valor do trade-in',
            'final_balance' => 'saldo restante',
        ];
    }

    public function messages(): array
    {
        return [
            'product_imei.required' => 'O IMEI do produto é obrigatório na pré-venda.',
            'down_payment.min' => 'O sinal mínimo é de R$ 50,00.',
            'down_payment.required' => 'O sinal é obrigatório.',
            'installments.required_if' => 'Informe o número de parcelas para pagamento no cartão.',
            'installments.max' => 'O máximo de parcelas é 18x.',
            'customer_name.required_without' => 'Informe o nome do cliente.',
            'customer_phone.required_without' => 'Informe o telefone do cliente.',
            'unit_price.required' => 'Informe o preço de venda.',
            'unit_price.min' => 'O preço de venda deve ser maior que zero.',
        ];
    }
}
