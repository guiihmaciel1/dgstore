<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Perfumes;

use Illuminate\Foundation\Http\FormRequest;

class StorePerfumeReservationPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:pix,cash,card'],
            'paid_at'        => ['nullable', 'date'],
            'notes'          => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'amount'         => 'valor',
            'payment_method' => 'forma de pagamento',
            'paid_at'        => 'data do pagamento',
            'notes'          => 'observações',
        ];
    }
}
