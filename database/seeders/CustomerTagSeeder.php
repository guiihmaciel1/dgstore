<?php

namespace Database\Seeders;

use App\Domain\Customer\Models\CustomerTag;
use Illuminate\Database\Seeder;

class CustomerTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Atendido DG', 'slug' => 'atendido-dg', 'color' => '#3b82f6', 'description' => 'Cliente atendido pelo DG', 'position' => 1],
            ['name' => 'Atendido Sophia', 'slug' => 'atendido-sophia', 'color' => '#ec4899', 'description' => 'Cliente atendido pela Sophia', 'position' => 2],
            ['name' => 'VIP', 'slug' => 'vip', 'color' => '#f59e0b', 'description' => 'Cliente VIP', 'position' => 3],
            ['name' => 'Comprador Recorrente', 'slug' => 'comprador-recorrente', 'color' => '#10b981', 'description' => 'Cliente que compra com frequência', 'position' => 4],
            ['name' => 'Trade-in', 'slug' => 'trade-in', 'color' => '#8b5cf6', 'description' => 'Cliente interessado em trade-in', 'position' => 5],
            ['name' => 'Perfumaria', 'slug' => 'perfumaria', 'color' => '#f43f5e', 'description' => 'Cliente de perfumaria', 'position' => 6],
            ['name' => 'Revendedor', 'slug' => 'revendedor', 'color' => '#06b6d4', 'description' => 'Cliente revendedor', 'position' => 7],
            ['name' => 'Inadimplente', 'slug' => 'inadimplente', 'color' => '#ef4444', 'description' => 'Cliente com pendências financeiras', 'position' => 8],
        ];

        foreach ($tags as $tag) {
            CustomerTag::firstOrCreate(
                ['slug' => $tag['slug']],
                $tag
            );
        }
    }
}
