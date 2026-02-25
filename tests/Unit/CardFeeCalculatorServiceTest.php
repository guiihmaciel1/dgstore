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
        
        // Cálculo esperado: 1000 * (1 + 0.0999) = 1099.90
        // Parcela: 1099.90 / 12 = 91.658333... → round = 91.66
        // Total: 91.66 * 12 = 1099.92
        $this->assertEquals(1099.92, $result->grossAmount);
        $this->assertEquals(99.92, $result->feeAmount);
        $this->assertEquals(91.66, $result->installmentValue);
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
        
        // Cálculo: 1000 * (1 + 0.0109) = 1010.90
        $this->assertEquals(1010.90, $result->grossAmount);
        $this->assertEquals(10.90, $result->feeAmount);
        $this->assertEquals(1010.90, $result->installmentValue);
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
        
        // Cálculo: 1000 * (1 + 0.0319) = 1031.90
        $this->assertEquals(1031.90, $result->grossAmount);
        $this->assertEquals(31.90, $result->feeAmount);
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
        
        // Cálculo: 1000 * (1 + 0.0759) = 1075.90
        // Parcela: 1075.90 / 6 = 179.316666... → round = 179.32
        // Total: 179.32 * 6 = 1075.92
        $this->assertEquals(1075.92, $result->grossAmount);
        $this->assertEquals(179.32, $result->installmentValue);
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
        
        // Cálculo: 1000 * (1 + 0.1635) = 1163.50
        // Parcela: 1163.50 / 18 = 64.638888... → round = 64.64
        // Total: 64.64 * 18 = 1163.52
        $this->assertEquals(1163.52, $result->grossAmount);
        $this->assertEquals(64.64, $result->installmentValue);
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
