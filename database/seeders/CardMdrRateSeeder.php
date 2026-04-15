<?php

namespace Database\Seeders;

use App\Domain\Payment\Models\CardMdrRate;
use Illuminate\Database\Seeder;

class CardMdrRateSeeder extends Seeder
{
    /**
     * Popula as taxas MDR da Stone
     * 
     * Taxas Stone (percentuais) - Atualizado em Abril/2026 (+0.5%):
     * - Crédito: 1x=3.69%, 2x=4.99%, 3x=5.99%, 4x=6.89%, 5x=7.69%, 6x=8.09%,
     *            7x=9.09%, 8x=9.19%, 9x=9.49%, 10x=9.49%, 11x=10.47%, 12x=10.49%,
     *            13x=13.25%, 14x=13.97%, 15x=14.69%, 16x=15.41%, 17x=16.13%, 18x=16.85%
     */
    public function run(): void
    {
        CardMdrRate::truncate();

        $creditRates = [
            1 => 3.69,
            2 => 4.99,
            3 => 5.99,
            4 => 6.89,
            5 => 7.69,
            6 => 8.09,
            7 => 9.09,
            8 => 9.19,
            9 => 9.49,
            10 => 9.49,
            11 => 10.47,
            12 => 10.49,
            13 => 13.25,
            14 => 13.97,
            15 => 14.69,
            16 => 15.41,
            17 => 16.13,
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

        $this->command->info('✓ Taxas MDR Stone populadas com sucesso!');
        $this->command->info('  - 18 taxas de crédito (3.69% a 16.85%)');
    }
}
