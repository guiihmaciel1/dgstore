<?php

namespace Database\Seeders;

use App\Domain\B2B\Models\B2BSetting;
use Illuminate\Database\Seeder;

class B2BSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'minimum_order_amount' => '5000',
            'low_stock_threshold' => '5',
            'company_name' => 'Apple B2B',
            'admin_whatsapp' => '5517991665442',
        ];

        foreach ($defaults as $key => $value) {
            B2BSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->command->info('Configurações padrão B2B criadas.');
    }
}
