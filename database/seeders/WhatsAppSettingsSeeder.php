<?php

namespace Database\Seeders;

use App\Domain\System\Models\SystemSetting;
use Illuminate\Database\Seeder;

class WhatsAppSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'wa_away_enabled' => 'true',
            'wa_away_message' => 'Olá! No momento estamos fora do horário de atendimento. Responderemos assim que possível. Obrigado!',
            'wa_business_start' => '9',
            'wa_business_end' => '18',
        ];

        foreach ($settings as $key => $value) {
            SystemSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
