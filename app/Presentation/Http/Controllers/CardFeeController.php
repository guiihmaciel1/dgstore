<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Payment\Services\CardFeeCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class CardFeeController
{
    public function __construct(
        private readonly CardFeeCalculatorService $calculatorService
    ) {}

    /**
     * Calcula uma opção específica de pagamento
     * 
     * POST /api/card-fees/calculate
     * Body: { "net_amount": 1000, "payment_type": "credit", "installments": 12 }
     */
    public function calculate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'net_amount' => 'required|numeric|min:0.01',
            'payment_type' => 'required|in:debit,credit',
            'installments' => 'required|integer|min:1|max:18',
        ], [
            'net_amount.required' => 'O valor líquido é obrigatório',
            'net_amount.numeric' => 'O valor líquido deve ser numérico',
            'net_amount.min' => 'O valor líquido deve ser maior que zero',
            'payment_type.required' => 'O tipo de pagamento é obrigatório',
            'payment_type.in' => 'Tipo de pagamento inválido',
            'installments.required' => 'O número de parcelas é obrigatório',
            'installments.integer' => 'O número de parcelas deve ser um inteiro',
            'installments.min' => 'O número de parcelas deve ser no mínimo 1',
            'installments.max' => 'O número de parcelas deve ser no máximo 18',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $result = $this->calculatorService->calculateGrossAmount(
                netDesired: (float) $request->input('net_amount'),
                type: $request->input('payment_type'),
                installments: (int) $request->input('installments')
            );

            return response()->json([
                'success' => true,
                'data' => $result->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Calcula todas as opções de pagamento disponíveis
     * 
     * POST /api/card-fees/calculate-all
     * Body: { "net_amount": 1000 }
     */
    public function calculateAll(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'net_amount' => 'required|numeric|min:0.01',
        ], [
            'net_amount.required' => 'O valor líquido é obrigatório',
            'net_amount.numeric' => 'O valor líquido deve ser numérico',
            'net_amount.min' => 'O valor líquido deve ser maior que zero',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $results = $this->calculatorService->calculateAllOptions(
                netDesired: (float) $request->input('net_amount')
            );

            return response()->json([
                'success' => true,
                'data' => array_map(fn($result) => $result->toArray(), $results),
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Calcula com entrada em Pix
     * 
     * POST /api/card-fees/calculate-with-down-payment
     * Body: { "total_amount": 1500, "down_payment": 500 }
     */
    public function calculateWithDownPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'total_amount' => 'required|numeric|min:0.01',
            'down_payment' => 'required|numeric|min:0',
        ], [
            'total_amount.required' => 'O valor total é obrigatório',
            'total_amount.numeric' => 'O valor total deve ser numérico',
            'total_amount.min' => 'O valor total deve ser maior que zero',
            'down_payment.required' => 'A entrada é obrigatória',
            'down_payment.numeric' => 'A entrada deve ser numérica',
            'down_payment.min' => 'A entrada não pode ser negativa',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $results = $this->calculatorService->calculateWithDownPayment(
                totalAmount: (float) $request->input('total_amount'),
                downPayment: (float) $request->input('down_payment')
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'down_payment' => (float) $request->input('down_payment'),
                    'remaining' => (float) $request->input('total_amount') - (float) $request->input('down_payment'),
                    'options' => array_map(fn($result) => $result->toArray(), $results),
                ],
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Calcula com trade-in
     * 
     * POST /api/card-fees/calculate-with-trade-in
     * Body: { "device_price": 2000, "trade_in_value": 800 }
     */
    public function calculateWithTradeIn(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_price' => 'required|numeric|min:0.01',
            'trade_in_value' => 'required|numeric|min:0',
        ], [
            'device_price.required' => 'O preço do aparelho é obrigatório',
            'device_price.numeric' => 'O preço do aparelho deve ser numérico',
            'device_price.min' => 'O preço do aparelho deve ser maior que zero',
            'trade_in_value.required' => 'O valor do trade-in é obrigatório',
            'trade_in_value.numeric' => 'O valor do trade-in deve ser numérico',
            'trade_in_value.min' => 'O valor do trade-in não pode ser negativo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $results = $this->calculatorService->calculateWithTradeIn(
                devicePrice: (float) $request->input('device_price'),
                tradeInValue: (float) $request->input('trade_in_value')
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'device_price' => (float) $request->input('device_price'),
                    'trade_in_value' => (float) $request->input('trade_in_value'),
                    'remaining' => (float) $request->input('device_price') - (float) $request->input('trade_in_value'),
                    'options' => array_map(fn($result) => $result->toArray(), $results),
                ],
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
