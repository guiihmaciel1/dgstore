<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Perfumes;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePerfumeCustomerRequest extends FormRequest
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
            'name'       => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'min:10', 'max:11', "unique:perfume_customers,phone,{$customerId}"],
            'cpf'        => ['nullable', 'string', 'size:11', "unique:perfume_customers,cpf,{$customerId}"],
            'email'      => ['nullable', 'email', 'max:255'],
            'address'    => ['nullable', 'string'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'notes'      => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'       => 'nome',
            'phone'      => 'telefone',
            'cpf'        => 'CPF',
            'email'      => 'e-mail',
            'address'    => 'endereço',
            'birth_date' => 'data de nascimento',
            'notes'      => 'observações',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique' => 'Este telefone já está cadastrado.',
            'cpf.unique'   => 'Este CPF já está cadastrado.',
            'cpf.size'     => 'O CPF deve ter 11 dígitos.',
            'phone.min'    => 'O telefone deve ter no mínimo 10 dígitos.',
            'phone.max'    => 'O telefone deve ter no máximo 11 dígitos.',
        ];
    }
}
