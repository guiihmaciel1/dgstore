<?php

declare(strict_types=1);

namespace App\Domain\Payment\Services;

use App\Domain\Payment\DTOs\CardFeeCalculationResult;
use App\Domain\Payment\Models\CardMdrRate;
use InvalidArgumentException;

class CardFeeCalculatorService
{
    /**
     * Calcula o valor bruto que o cliente deve pagar para que o lojista receba o valor líquido desejado
     * 
     * Regra Stone: A taxa é por conta do cliente (acréscimo)
     * Fórmula: bruto = liquido * (1 + taxaDecimal)
     * Arredondamento: Math.round por parcela para evitar diferenças de centavos
     * 
     * @param float $netDesired Valor líquido que o lojista deseja receber
     * @param string $type 'debit' ou 'credit'
     * @param int $installments Número de parcelas (1-18)
     * @return CardFeeCalculationResult
     * @throws InvalidArgumentException
     */
    public function calculateGrossAmount(float $netDesired, string $type, int $installments): CardFeeCalculationResult
    {
        if ($netDesired <= 0) {
            throw new InvalidArgumentException('O valor líquido deve ser maior que zero');
        }

        if (!in_array($type, ['debit', 'credit'])) {
            throw new InvalidArgumentException('Tipo de pagamento inválido. Use "debit" ou "credit"');
        }

        if ($installments < 1 || $installments > 18) {
            throw new InvalidArgumentException('Número de parcelas deve estar entre 1 e 18');
        }

        if ($type === 'debit' && $installments > 1) {
            throw new InvalidArgumentException('Débito só permite 1 parcela');
        }

        // Busca a taxa MDR (com fallback se banco não disponível)
        $mdrRate = null;
        
        try {
            $mdrRate = CardMdrRate::getRateFor($type, $installments);
        } catch (\Exception $e) {
            // Ignora erros de conexão e usa fallback
        }
        
        if ($mdrRate === null) {
            // Fallback: usa taxas hardcoded se banco não estiver populado/disponível
            $mdrRate = $this->getFallbackRate($type, $installments);
            
            if ($mdrRate === null) {
                throw new InvalidArgumentException("Taxa MDR não encontrada para {$type} {$installments}x");
            }
        }

        // Converte taxa percentual para decimal
        $taxaDecimal = bcdiv((string) $mdrRate, '100', 4);

        // Calcula o valor bruto usando BCMath para precisão
        // bruto = liquido * (1 + taxaDecimal)
        $netStr = number_format($netDesired, 2, '.', '');
        $multiplicador = bcadd('1', $taxaDecimal, 4);
        $brutoBcmath = bcmul($netStr, $multiplicador, 4);

        // Calcula valor por parcela e arredonda
        $parcelaBruta = bcdiv($brutoBcmath, (string) $installments, 4);
        $parcelaArredondada = round((float) $parcelaBruta, 2);

        // Valor bruto final = parcela arredondada * número de parcelas
        $grossAmount = $parcelaArredondada * $installments;
        
        // Taxa cobrada
        $feeAmount = $grossAmount - $netDesired;

        return new CardFeeCalculationResult(
            paymentType: $type,
            installments: $installments,
            mdrRate: $mdrRate,
            netAmount: round($netDesired, 2),
            grossAmount: round($grossAmount, 2),
            feeAmount: round($feeAmount, 2),
            installmentValue: $parcelaArredondada,
        );
    }

    /**
     * Calcula todas as opções de pagamento disponíveis (débito + crédito 1x-18x)
     * 
     * @param float $netDesired Valor líquido que o lojista deseja receber
     * @return array Array de CardFeeCalculationResult
     */
    public function calculateAllOptions(float $netDesired): array
    {
        $results = [];

        // Débito
        try {
            $results[] = $this->calculateGrossAmount($netDesired, 'debit', 1);
        } catch (InvalidArgumentException $e) {
            // Ignora se não houver taxa cadastrada
        }

        // Crédito 1x a 18x
        for ($i = 1; $i <= 18; $i++) {
            try {
                $results[] = $this->calculateGrossAmount($netDesired, 'credit', $i);
            } catch (InvalidArgumentException $e) {
                // Ignora se não houver taxa cadastrada
            }
        }

        return $results;
    }

    /**
     * Calcula considerando uma entrada em Pix
     * 
     * @param float $totalAmount Valor total do produto
     * @param float $downPayment Entrada em Pix
     * @return array Array de CardFeeCalculationResult para o valor restante
     */
    public function calculateWithDownPayment(float $totalAmount, float $downPayment): array
    {
        if ($downPayment >= $totalAmount) {
            return [];
        }

        $remaining = $totalAmount - $downPayment;
        return $this->calculateAllOptions($remaining);
    }

    /**
     * Calcula considerando trade-in
     * 
     * @param float $devicePrice Preço do aparelho novo
     * @param float $tradeInValue Valor do trade-in
     * @return array Array de CardFeeCalculationResult para o valor restante
     */
    public function calculateWithTradeIn(float $devicePrice, float $tradeInValue): array
    {
        if ($tradeInValue >= $devicePrice) {
            return [];
        }

        $remaining = $devicePrice - $tradeInValue;
        return $this->calculateAllOptions($remaining);
    }

    /**
     * Fallback: retorna taxas hardcoded caso banco não esteja populado
     * 
     * @param string $type
     * @param int $installments
     * @return float|null
     */
    private function getFallbackRate(string $type, int $installments): ?float
    {
        if ($type === 'debit') {
            return $installments === 1 ? 1.09 : null;
        }

        $creditRates = [
            1 => 3.19, 2 => 4.49, 3 => 5.49, 4 => 6.39, 5 => 7.19, 6 => 7.59,
            7 => 8.59, 8 => 8.69, 9 => 8.99, 10 => 8.99, 11 => 9.97, 12 => 9.99,
            13 => 12.75, 14 => 13.47, 15 => 14.19, 16 => 14.91, 17 => 15.63, 18 => 16.35,
        ];

        return $creditRates[$installments] ?? null;
    }
}
