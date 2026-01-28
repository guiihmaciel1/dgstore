<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['required', 'string', 'min:10', 'max:11', 'unique:customers,phone'],
            'cpf' => ['nullable', 'string', 'size:11', 'unique:customers,cpf'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
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
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique' => 'Este telefone já está cadastrado.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'cpf.size' => 'O CPF deve ter 11 dígitos.',
            'phone.min' => 'O telefone deve ter no mínimo 10 dígitos.',
            'phone.max' => 'O telefone deve ter no máximo 11 dígitos.',
        ];
    }
}
