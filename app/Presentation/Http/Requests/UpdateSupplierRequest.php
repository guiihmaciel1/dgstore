<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
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

        if ($this->cnpj) {
            $this->merge([
                'cnpj' => preg_replace('/\D/', '', $this->cnpj),
            ]);
        }
    }

    public function rules(): array
    {
        $supplierId = $this->route('supplier')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => ['nullable', 'string', 'size:14', Rule::unique('suppliers', 'cnpj')->ignore($supplierId)],
            'phone' => ['nullable', 'string', 'min:10', 'max:11'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'cnpj' => 'CNPJ',
            'phone' => 'telefone',
            'email' => 'e-mail',
            'address' => 'endereço',
            'contact_person' => 'pessoa de contato',
            'notes' => 'observações',
            'active' => 'ativo',
        ];
    }

    public function messages(): array
    {
        return [
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'cnpj.size' => 'O CNPJ deve ter 14 dígitos.',
            'phone.min' => 'O telefone deve ter no mínimo 10 dígitos.',
            'phone.max' => 'O telefone deve ter no máximo 11 dígitos.',
        ];
    }
}
