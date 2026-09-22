<?php

namespace Database\Seeders;

use App\Domain\Fragrance\Models\FragranceTag;
use Illuminate\Database\Seeder;

class FragranceTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Árabe',    'color' => '#d4a053', 'icon' => '🕌', 'sort_order' => 1],
            ['name' => 'Importado','color' => '#6366f1', 'icon' => '✈️', 'sort_order' => 2],
            ['name' => 'Nacional', 'color' => '#22c55e', 'icon' => '🇧🇷', 'sort_order' => 3],
            ['name' => 'Nicho',    'color' => '#a855f7', 'icon' => '💎', 'sort_order' => 4],
        ];

        foreach ($tags as $tag) {
            FragranceTag::firstOrCreate(
                ['name' => $tag['name']],
                $tag,
            );
        }
    }
}
