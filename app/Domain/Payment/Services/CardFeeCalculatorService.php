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
     * Truncamento: floor por parcela (padrão Stone) para bater com a maquininha
     * 
     * @param float $netDesired Valor líquido que o lojista deseja receber
     * @param string $type 'credit'
     * @param int $installments Número de parcelas (1-21)
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

        if ($installments < 1 || $installments > 21) {
            throw new InvalidArgumentException('Número de parcelas deve estar entre 1 e 21');
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

        // Calcula valor por parcela e trunca (floor) — padrão Stone
        $parcelaBruta = bcdiv($brutoBcmath, (string) $installments, 4);
        $parcelaArredondada = floor((float) $parcelaBruta * 100) / 100;

        // Valor bruto final = parcela truncada * número de parcelas
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
     * Calcula todas as opções de pagamento disponíveis (crédito 1x-21x)
     * 
     * @param float $netDesired Valor líquido que o lojista deseja receber
     * @return array Array de CardFeeCalculationResult
     */
    public function calculateAllOptions(float $netDesired): array
    {
        $results = [];

        for ($i = 1; $i <= 21; $i++) {
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
            1  => 2.96,  2  => 4.03,  3  => 4.72,  4  => 5.41,  5  => 6.09,  6  => 6.78,
            7  => 7.62,  8  => 8.31,  9  => 8.99,  10 => 9.68,  11 => 10.36, 12 => 11.05,
            13 => 11.74, 14 => 12.42, 15 => 13.11, 16 => 13.79, 17 => 14.48, 18 => 15.17,
            19 => 15.85, 20 => 16.54, 21 => 17.23,
        ];

        return $creditRates[$installments] ?? null;
    }
}
