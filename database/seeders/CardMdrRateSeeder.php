<?php

namespace Database\Seeders;

use App\Domain\Payment\Models\CardMdrRate;
use Illuminate\Database\Seeder;

class CardMdrRateSeeder extends Seeder
{
    /**
     * Popula as taxas MDR da Stone
     * 
     * Taxas Stone (percentuais):
     * - Débito: 1.09%
     * - Crédito: 1x=3.19%, 2x=4.49%, 3x=5.49%, 4x=6.39%, 5x=7.19%, 6x=7.59%,
     *            7x=8.59%, 8x=8.69%, 9x=8.99%, 10x=8.99%, 11x=9.97%, 12x=9.99%,
     *            13x=12.75%, 14x=13.47%, 15x=14.19%, 16x=14.91%, 17x=15.63%, 18x=16.35%
     */
    public function run(): void
    {
        // Limpa taxas existentes
        CardMdrRate::truncate();

        // Taxa de Débito
        CardMdrRate::create([
            'payment_type' => 'debit',
            'installments' => 1,
            'mdr_rate' => 1.09,
            'is_active' => true,
        ]);

        // Taxas de Crédito (1x a 18x)
        $creditRates = [
            1 => 3.19,
            2 => 4.49,
            3 => 5.49,
            4 => 6.39,
            5 => 7.19,
            6 => 7.59,
            7 => 8.59,
            8 => 8.69,
            9 => 8.99,
            10 => 8.99,
            11 => 9.97,
            12 => 9.99,
            13 => 12.75,
            14 => 13.47,
            15 => 14.19,
            16 => 14.91,
            17 => 15.63,
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

        $this->command->info('✓ Taxas MDR Stone populadas com sucesso!');
        $this->command->info('  - 1 taxa de débito (1.09%)');
        $this->command->info('  - 18 taxas de crédito (3.19% a 16.35%)');
    }
}
