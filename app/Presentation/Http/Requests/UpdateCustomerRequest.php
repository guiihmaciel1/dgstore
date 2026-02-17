<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->phone) {
            $this->merge([
                'phone' => preg_replace('/\D/', '', $this->phone),
            ]);
        }

        if ($this->cpf) {
            $this->merge([
                'cpf' => preg_replace('/\D/', '', $this->cpf),
            ]);
        }
    }

    public function rules(): array
    {
        $customerId = $this->route('customer');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customerId)],
            'phone' => ['required', 'string', 'min:10', 'max:11', Rule::unique('customers', 'phone')->ignore($customerId)],
            'cpf' => ['nullable', 'string', 'size:11', Rule::unique('customers', 'cpf')->ignore($customerId)],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'birth_date' => ['nullable', 'date', 'before:today'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'e-mail',
            'phone' => 'telefone',
            'cpf' => 'CPF',
            'address' => 'endereço',
            'notes' => 'observações',
            'birth_date' => 'data de nascimento',
        ];
    }
}
