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
     * Regra Stone: Taxa MDR é desconto sobre o bruto (cliente assume a taxa)
     * Fórmula: liquido = bruto * (1 - taxaDecimal)
     * Gross-up: bruto = liquido / (1 - taxaDecimal)
     * Arredondamento: Math.round por parcela para evitar diferenças de centavos
     * 
     * @param float $netDesired Valor líquido que o lojista deseja receber
     * @param string $type 'credit'
     * @param int $installments Número de parcelas (1-18)
     * @return CardFeeCalculationResult
     * @throws InvalidArgumentException
     */
    public function calculateGrossAmount(float $netDesired, string $type, int $installments): CardFeeCalculationResult
    {
        if ($netDesired <= 0) {
            throw new InvalidArgumentException('O valor líquido deve ser maior que zero');
        }

        if ($type !== 'credit') {
            throw new InvalidArgumentException('Tipo de pagamento inválido. Use "credit"');
        }

        if ($installments < 1 || $installments > 18) {
            throw new InvalidArgumentException('Número de parcelas deve estar entre 1 e 18');
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
        // Gross-up: bruto = liquido / (1 - taxaDecimal)
        $netStr = number_format($netDesired, 2, '.', '');
        $divisor = bcsub('1', $taxaDecimal, 4); // (1 - taxa)
        $brutoBcmath = bcdiv($netStr, $divisor, 4);

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
        if ($type !== 'credit') {
            return null;
        }

        $creditRates = [
            1 => 3.69, 2 => 4.99, 3 => 5.99, 4 => 6.89, 5 => 7.69, 6 => 8.09,
            7 => 9.09, 8 => 9.19, 9 => 9.49, 10 => 9.49, 11 => 10.47, 12 => 10.49,
            13 => 13.25, 14 => 13.97, 15 => 14.69, 16 => 15.41, 17 => 16.13, 18 => 16.85,
        ];

        return $creditRates[$installments] ?? null;
    }
}
