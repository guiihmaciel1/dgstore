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
        $creditRates = [
            1 => 3.69,
            6 => 8.09,
            12 => 10.49,
            18 => 16.85,
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
        $result = $this->service->calculateGrossAmount(1000.00, 'credit', 12);

        $this->assertEquals('credit', $result->paymentType);
        $this->assertEquals(12, $result->installments);
        $this->assertEquals(10.49, $result->mdrRate);
        $this->assertEquals(1000.00, $result->netAmount);
        $this->assertEquals(1117.20, $result->grossAmount);
        $this->assertEquals(117.20, $result->feeAmount);
        $this->assertEquals(93.10, $result->installmentValue);
    }

    public function test_credit_1x_calculation(): void
    {
        $result = $this->service->calculateGrossAmount(1000.00, 'credit', 1);

        $this->assertEquals('credit', $result->paymentType);
        $this->assertEquals(1, $result->installments);
        $this->assertEquals(3.69, $result->mdrRate);
        $this->assertEquals(1038.31, $result->grossAmount);
        $this->assertEquals(38.31, $result->feeAmount);
    }

    public function test_credit_6x_calculation(): void
    {
        $result = $this->service->calculateGrossAmount(1000.00, 'credit', 6);

        $this->assertEquals(6, $result->installments);
        $this->assertEquals(8.09, $result->mdrRate);
        $this->assertEquals(1088.04, $result->grossAmount);
        $this->assertEquals(181.34, $result->installmentValue);
    }

    public function test_credit_18x_calculation(): void
    {
        $result = $this->service->calculateGrossAmount(1000.00, 'credit', 18);

        $this->assertEquals(18, $result->installments);
        $this->assertEquals(16.85, $result->mdrRate);
        $this->assertEquals(1202.58, $result->grossAmount);
        $this->assertEquals(66.81, $result->installmentValue);
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

        $this->service->calculateGrossAmount(1000, 'debit', 1);
    }

    public function test_invalid_installments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Número de parcelas deve estar entre 1 e 18');

        $this->service->calculateGrossAmount(1000, 'credit', 20);
    }

    public function test_calculate_all_options_returns_all_rates(): void
    {
        $results = $this->service->calculateAllOptions(1000);

        $this->assertCount(18, $results);

        $creditResults = array_filter($results, fn($r) => $r->paymentType === 'credit');
        $this->assertCount(18, $creditResults);
    }

    public function test_calculate_with_down_payment(): void
    {
        $results = $this->service->calculateWithDownPayment(1500.00, 500.00);

        $this->assertNotEmpty($results);
        $this->assertEquals(1000.00, $results[0]->netAmount);
    }

    public function test_calculate_with_trade_in(): void
    {
        $results = $this->service->calculateWithTradeIn(2000.00, 800.00);

        $this->assertNotEmpty($results);
        $this->assertEquals(1200.00, $results[0]->netAmount);
    }

    public function test_down_payment_equal_to_total_returns_empty(): void
    {
        $results = $this->service->calculateWithDownPayment(1000.00, 1000.00);
        $this->assertEmpty($results);
    }

    public function test_trade_in_equal_to_price_returns_empty(): void
    {
        $results = $this->service->calculateWithTradeIn(1000.00, 1000.00);
        $this->assertEmpty($results);
    }
}
