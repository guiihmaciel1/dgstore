<?php

namespace Database\Seeders;

use App\Domain\Payment\Models\CardMdrRate;
use Illuminate\Database\Seeder;

class CardMdrRateSeeder extends Seeder
{
    /**
     * Popula as taxas MDR da Stone
     * 
     * Base: Taxas CET Incentivada (Set/2026) + 0.15% spread antecipação (RAV)
     * Fórmula: taxa_efetiva = CET_incentivada + 0.15%
     * 
     * CET Incentivada: 1x=2.81%, 2x=3.88%, ..., 21x=17.08%
     * Taxa efetiva:    1x=2.96%, 2x=4.03%, ..., 21x=17.23%
     */
    public function run(): void
    {
        CardMdrRate::truncate();

        $creditRates = [
            1  => 2.96,
            2  => 4.03,
            3  => 4.72,
            4  => 5.41,
            5  => 6.09,
            6  => 6.78,
            7  => 7.62,
            8  => 8.31,
            9  => 8.99,
            10 => 9.68,
            11 => 10.36,
            12 => 11.05,
            13 => 11.74,
            14 => 12.42,
            15 => 13.11,
            16 => 13.79,
            17 => 14.48,
            18 => 15.17,
            19 => 15.85,
            20 => 16.54,
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

        $this->command->info('✓ Taxas MDR Stone (CET Incentivada + RAV 0.15%) populadas com sucesso!');
        $this->command->info('  - 21 taxas de crédito (2.96% a 17.23%)');
    }
}
