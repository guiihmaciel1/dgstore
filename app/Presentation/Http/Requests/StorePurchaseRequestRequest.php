<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequestRequest extends FormRequest
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
            // Cliente existente ou novo
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_name' => ['required_without:customer_id', 'nullable', 'string', 'max:255'],
            'customer_phone' => ['required_without:customer_id', 'nullable', 'string', 'min:10', 'max:11'],
            'customer_cpf' => ['nullable', 'string', 'size:11'],
            'customer_instagram' => ['nullable', 'string', 'max:255'],

            // Tipo
            'type' => ['required', 'in:direct_purchase,upgrade'],

            // Produto desejado
            'desired_product' => ['required', 'string', 'max:255'],
            'desired_model' => ['nullable', 'string', 'max:255'],
            'desired_storage' => ['nullable', 'string', 'max:20'],
            'desired_color' => ['nullable', 'string', 'max:50'],
            'desired_condition' => ['required', 'in:new,used'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],

            // Valores
            'sale_price' => ['required', 'numeric', 'min:0.01'],
            'down_payment' => ['required', 'numeric', 'min:100'],
            'down_payment_method' => ['required', 'in:pix,cash'],
            'payment_method' => ['required', 'in:pix,cash,credit_card,debit_card'],
            'installments' => ['nullable', 'required_if:payment_method,credit_card', 'integer', 'min:1', 'max:21'],

            // Upgrade
            'trade_in_device' => ['nullable', 'required_if:type,upgrade', 'string', 'max:255'],
            'trade_in_value' => ['nullable', 'required_if:type,upgrade', 'numeric', 'min:0'],
            'upgrade_difference' => ['nullable', 'numeric', 'min:0'],

            // Entrega
            'delivery_type' => ['required', 'in:pickup,delivery'],
            'delivery_address' => ['nullable', 'required_if:delivery_type,delivery', 'string', 'max:500'],
            'delivery_time' => ['nullable', 'string', 'max:50'],
            'delivery_notes' => ['nullable', 'string', 'max:2000'],

            // Meta
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Deve ter cliente
            if (!$this->customer_id && !$this->customer_name) {
                $validator->errors()->add(
                    'customer_id',
                    'Informe um cliente existente ou cadastre um novo.'
                );
            }

            // Sinal não pode ser maior que o preço de venda
            $salePrice = (float) $this->sale_price;
            $downPayment = (float) $this->down_payment;

            if ($downPayment > $salePrice && $salePrice > 0) {
                $validator->errors()->add(
                    'down_payment',
                    'O sinal não pode ser maior que o preço de venda.'
                );
            }

            // Se for upgrade, trade-in é obrigatório
            if ($this->type === 'upgrade') {
                if (empty($this->trade_in_device)) {
                    $validator->errors()->add(
                        'trade_in_device',
                        'Informe o aparelho do cliente para upgrade.'
                    );
                }
                if (empty($this->trade_in_value) || (float) $this->trade_in_value <= 0) {
                    $validator->errors()->add(
                        'trade_in_value',
                        'Informe o valor de avaliação do aparelho para upgrade.'
                    );
                }
            }

            // Se for entrega, endereço é obrigatório
            if ($this->delivery_type === 'delivery' && empty($this->delivery_address)) {
                $validator->errors()->add(
                    'delivery_address',
                    'Informe o endereço de entrega.'
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            'customer_id' => 'cliente',
            'customer_name' => 'nome do cliente',
            'customer_phone' => 'telefone do cliente',
            'customer_cpf' => 'CPF do cliente',
            'type' => 'tipo da solicitação',
            'desired_product' => 'produto desejado',
            'desired_condition' => 'condição',
            'sale_price' => 'preço de venda',
            'down_payment' => 'sinal',
            'down_payment_method' => 'método do sinal',
            'payment_method' => 'forma de pagamento',
            'installments' => 'parcelas',
            'trade_in_device' => 'aparelho do trade-in',
            'trade_in_value' => 'valor do trade-in',
            'delivery_type' => 'tipo de entrega',
            'delivery_address' => 'endereço de entrega',
        ];
    }

    public function messages(): array
    {
        return [
            'down_payment.min' => 'O sinal mínimo é de R$ 100,00.',
            'down_payment.required' => 'O sinal é obrigatório para blindar a compra.',
            'desired_product.required' => 'Descreva o produto desejado.',
            'sale_price.required' => 'Informe o preço de venda ao cliente.',
            'sale_price.min' => 'O preço de venda deve ser maior que zero.',
            'customer_name.required_without' => 'Informe o nome do cliente.',
            'customer_phone.required_without' => 'Informe o telefone do cliente.',
            'installments.required_if' => 'Informe o número de parcelas para pagamento no cartão.',
            'installments.max' => 'O máximo de parcelas é 21x.',
            'trade_in_device.required_if' => 'Informe o aparelho do cliente para upgrade.',
            'trade_in_value.required_if' => 'Informe o valor de avaliação para upgrade.',
            'delivery_address.required_if' => 'Informe o endereço de entrega.',
        ];
    }
}
