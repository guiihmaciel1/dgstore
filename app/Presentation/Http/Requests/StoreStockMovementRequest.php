<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use App\Domain\Stock\Enums\StockMovementType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', Rule::in(['in', 'adjustment'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'product_id' => 'produto',
            'type' => 'tipo de movimentação',
            'quantity' => 'quantidade',
            'reason' => 'motivo',
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Tipo de movimentação inválido. Use "in" para entrada ou "adjustment" para ajuste.',
        ];
    }
}
