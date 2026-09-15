<?php

namespace Database\Seeders;

use App\Domain\Payment\Models\CardMdrRate;
use Illuminate\Database\Seeder;

class CardMdrRateSeeder extends Seeder
{
    /**
     * Popula as taxas MDR da Stone
     * 
     * Taxas Stone CET Incentivada - Atualizado em Setembro/2026:
     * - Crédito: 1x=2.81%, 2x=3.88%, 3x=4.57%, 4x=5.26%, 5x=5.94%, 6x=6.63%,
     *            7x=7.47%, 8x=8.16%, 9x=8.84%, 10x=9.53%, 11x=10.21%, 12x=10.90%,
     *            13x=11.59%, 14x=12.27%, 15x=12.96%, 16x=13.64%, 17x=14.33%, 18x=15.02%,
     *            19x=15.70%, 20x=16.39%, 21x=17.08%
     */
    public function run(): void
    {
        CardMdrRate::truncate();

        $creditRates = [
            1  => 2.81,
            2  => 3.88,
            3  => 4.57,
            4  => 5.26,
            5  => 5.94,
            6  => 6.63,
            7  => 7.47,
            8  => 8.16,
            9  => 8.84,
            10 => 9.53,
            11 => 10.21,
            12 => 10.90,
            13 => 11.59,
            14 => 12.27,
            15 => 12.96,
            16 => 13.64,
            17 => 14.33,
            18 => 15.02,
            19 => 15.70,
            20 => 16.39,
            21 => 17.08,
        ];

        foreach ($creditRates as $installments => $rate) {
            CardMdrRate::create([
                'payment_type' => 'credit',
                'installments' => $installments,
                'mdr_rate' => $rate,
                'is_active' => true,
            ]);
        }

        $this->command->info('✓ Taxas MDR Stone (CET Incentivada) populadas com sucesso!');
        $this->command->info('  - 21 taxas de crédito (2.81% a 17.08%)');
    }
}
