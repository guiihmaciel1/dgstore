<?php

namespace Tests\Unit;

use App\Domain\Payment\Models\CardMdrRate;
use App\Domain\Payment\Services\CardFeeCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class CardFeeCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    private CardFeeCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CardFeeCalculatorService();
        $this->seedTestRates();
    }

    private function seedTestRates(): void
    {
        // Taxa de débito
        CardMdrRate::create([
            'payment_type' => 'debit',
            'installments' => 1,
            'mdr_rate' => 1.09,
            'is_active' => true,
        ]);

        // Algumas taxas de crédito para teste
        $creditRates = [
            1 => 3.19,
            6 => 7.59,
            12 => 9.99,
            18 => 16.35,
        ];

        foreach ($creditRates as $installments => $rate) {
            CardMdrRate::create([
                'payment_type' => 'credit',
                'installments' => $installments,
                'mdr_rate' => $rate,
                'is_active' => true,
            ]);
        }
    }

    public function test_calculate_12x_with_1000_net_returns_correct_gross(): void
    {
        // Arrange: R$ 1000 líquido, 12x, taxa 9.99%
        $netAmount = 1000.00;
        $installments = 12;
        $expectedRate = 9.99;

        // Act
        $result = $this->service->calculateGrossAmount($netAmount, 'credit', $installments);

        // Assert
        $this->assertEquals('credit', $result->paymentType);
        $this->assertEquals(12, $result->installments);
        $this->assertEquals($expectedRate, $result->mdrRate);
        $this->assertEquals(1000.00, $result->netAmount);
        
        // Cálculo esperado (gross-up): 1000 / (1 - 0.0999) = 1110.9876
        // Parcela: 1110.9876 / 12 = 92.5823 → round = 92.58
        // Total: 92.58 * 12 = 1110.96
        $this->assertEquals(1110.96, $result->grossAmount);
        $this->assertEquals(110.96, $result->feeAmount);
        $this->assertEquals(92.58, $result->installmentValue);
    }

    public function test_debit_calculation(): void
    {
        // Arrange
        $netAmount = 1000.00;

        // Act
        $result = $this->service->calculateGrossAmount($netAmount, 'debit', 1);

        // Assert
        $this->assertEquals('debit', $result->paymentType);
        $this->assertEquals(1, $result->installments);
        $this->assertEquals(1.09, $result->mdrRate);
        
        // Cálculo (gross-up): 1000 / (1 - 0.0109) = 1011.02
        $this->assertEquals(1011.02, $result->grossAmount);
        $this->assertEquals(11.02, $result->feeAmount);
        $this->assertEquals(1011.02, $result->installmentValue);
    }

    public function test_credit_1x_calculation(): void
    {
        // Arrange
        $netAmount = 1000.00;

        // Act
        $result = $this->service->calculateGrossAmount($netAmount, 'credit', 1);

        // Assert
        $this->assertEquals('credit', $result->paymentType);
        $this->assertEquals(1, $result->installments);
        $this->assertEquals(3.19, $result->mdrRate);
        
        // Cálculo (gross-up): 1000 / (1 - 0.0319) = 1032.95
        $this->assertEquals(1032.95, $result->grossAmount);
        $this->assertEquals(32.95, $result->feeAmount);
    }

    public function test_credit_6x_calculation(): void
    {
        // Arrange
        $netAmount = 1000.00;

        // Act
        $result = $this->service->calculateGrossAmount($netAmount, 'credit', 6);

        // Assert
        $this->assertEquals(6, $result->installments);
        $this->assertEquals(7.59, $result->mdrRate);
        
        // Cálculo (gross-up): 1000 / (1 - 0.0759) = 1082.14
        // Parcela: 1082.14 / 6 = 180.356666... → round = 180.36
        // Total: 180.36 * 6 = 1082.16
        $this->assertEquals(1082.16, $result->grossAmount);
        $this->assertEquals(180.36, $result->installmentValue);
    }

    public function test_credit_18x_calculation(): void
    {
        // Arrange
        $netAmount = 1000.00;

        // Act
        $result = $this->service->calculateGrossAmount($netAmount, 'credit', 18);

        // Assert
        $this->assertEquals(18, $result->installments);
        $this->assertEquals(16.35, $result->mdrRate);
        
        // Cálculo (gross-up): 1000 / (1 - 0.1635) = 1195.46
        // Parcela: 1195.46 / 18 = 66.414444... → round = 66.41
        // Total: 66.41 * 18 = 1195.38
        $this->assertEquals(1195.38, $result->grossAmount);
        $this->assertEquals(66.41, $result->installmentValue);
    }

    public function test_bcmath_precision_with_small_values(): void
    {
        // Arrange: Testa precisão com valores pequenos
        $netAmount = 10.50;

        // Act
        $result = $this->service->calculateGrossAmount($netAmount, 'debit', 1);

        // Assert
        $this->assertEquals(10.50, $result->netAmount);
        // 10.50 * 1.0109 = 10.61445 → 10.61
        $this->assertEquals(10.61, $result->grossAmount);
    }

    public function test_invalid_net_amount_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('O valor líquido deve ser maior que zero');

        $this->service->calculateGrossAmount(0, 'credit', 12);
    }

    public function test_invalid_payment_type_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tipo de pagamento inválido');

        $this->service->calculateGrossAmount(1000, 'invalid', 12);
    }

    public function test_invalid_installments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Número de parcelas deve estar entre 1 e 18');

        $this->service->calculateGrossAmount(1000, 'credit', 20);
    }

    public function test_debit_with_multiple_installments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Débito só permite 1 parcela');

        $this->service->calculateGrossAmount(1000, 'debit', 2);
    }

    public function test_calculate_all_options_returns_all_rates(): void
    {
        // Act
        $results = $this->service->calculateAllOptions(1000);

        // Assert: Deve retornar 5 opções (1 débito + 4 crédito que seedamos)
        $this->assertCount(5, $results);
        
        // Verifica que tem débito
        $debitResults = array_filter($results, fn($r) => $r->paymentType === 'debit');
        $this->assertCount(1, $debitResults);
        
        // Verifica que tem crédito
        $creditResults = array_filter($results, fn($r) => $r->paymentType === 'credit');
        $this->assertCount(4, $creditResults);
    }

    public function test_calculate_with_down_payment(): void
    {
        // Arrange: Produto de R$ 1500, entrada de R$ 500
        $totalAmount = 1500.00;
        $downPayment = 500.00;

        // Act
        $results = $this->service->calculateWithDownPayment($totalAmount, $downPayment);

        // Assert: Deve calcular sobre R$ 1000 (restante)
        $this->assertNotEmpty($results);
        
        $firstResult = $results[0];
        $this->assertEquals(1000.00, $firstResult->netAmount);
    }

    public function test_calculate_with_trade_in(): void
    {
        // Arrange: Aparelho de R$ 2000, trade-in de R$ 800
        $devicePrice = 2000.00;
        $tradeInValue = 800.00;

        // Act
        $results = $this->service->calculateWithTradeIn($devicePrice, $tradeInValue);

        // Assert: Deve calcular sobre R$ 1200 (restante)
        $this->assertNotEmpty($results);
        
        $firstResult = $results[0];
        $this->assertEquals(1200.00, $firstResult->netAmount);
    }

    public function test_down_payment_equal_to_total_returns_empty(): void
    {
        // Arrange
        $totalAmount = 1000.00;
        $downPayment = 1000.00;

        // Act
        $results = $this->service->calculateWithDownPayment($totalAmount, $downPayment);

        // Assert
        $this->assertEmpty($results);
    }

    public function test_trade_in_equal_to_price_returns_empty(): void
    {
        // Arrange
        $devicePrice = 1000.00;
        $tradeInValue = 1000.00;

        // Act
        $results = $this->service->calculateWithTradeIn($devicePrice, $tradeInValue);

        // Assert
        $this->assertEmpty($results);
    }
}
