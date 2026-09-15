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
            1  => 2.96,
            6  => 6.78,
            12 => 11.05,
            18 => 15.17,
            21 => 17.23,
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
        $this->assertEquals(11.05, $result->mdrRate);
        $this->assertEquals(1000.00, $result->netAmount);
        $this->assertEquals(1124.28, $result->grossAmount);
        $this->assertEquals(124.28, $result->feeAmount);
        $this->assertEquals(93.69, $result->installmentValue);
    }

    public function test_credit_1x_calculation(): void
    {
        $result = $this->service->calculateGrossAmount(1000.00, 'credit', 1);

        $this->assertEquals('credit', $result->paymentType);
        $this->assertEquals(1, $result->installments);
        $this->assertEquals(2.96, $result->mdrRate);
        $this->assertEquals(1030.50, $result->grossAmount);
        $this->assertEquals(30.50, $result->feeAmount);
    }

    public function test_credit_6x_calculation(): void
    {
        $result = $this->service->calculateGrossAmount(1000.00, 'credit', 6);

        $this->assertEquals(6, $result->installments);
        $this->assertEquals(6.78, $result->mdrRate);
        $this->assertEquals(1072.74, $result->grossAmount);
        $this->assertEquals(178.79, $result->installmentValue);
    }

    public function test_credit_18x_calculation(): void
    {
        $result = $this->service->calculateGrossAmount(1000.00, 'credit', 18);

        $this->assertEquals(18, $result->installments);
        $this->assertEquals(15.17, $result->mdrRate);
        $this->assertEquals(1178.82, $result->grossAmount);
        $this->assertEquals(65.49, $result->installmentValue);
    }

    public function test_credit_21x_calculation(): void
    {
        $result = $this->service->calculateGrossAmount(1000.00, 'credit', 21);

        $this->assertEquals(21, $result->installments);
        $this->assertEquals(17.23, $result->mdrRate);
        $this->assertEquals(1208.13, $result->grossAmount);
        $this->assertEquals(57.53, $result->installmentValue);
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
        $this->expectExceptionMessage('Número de parcelas deve estar entre 1 e 21');

        $this->service->calculateGrossAmount(1000, 'credit', 22);
    }

    public function test_calculate_all_options_returns_all_rates(): void
    {
        $results = $this->service->calculateAllOptions(1000);

        $this->assertCount(21, $results);

        $creditResults = array_filter($results, fn($r) => $r->paymentType === 'credit');
        $this->assertCount(21, $creditResults);
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
