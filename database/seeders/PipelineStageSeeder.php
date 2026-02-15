<?php

namespace Database\Seeders;

use App\Domain\CRM\Models\PipelineStage;
use Illuminate\Database\Seeder;

class PipelineStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            ['name' => 'Novo Lead', 'color' => '#3b82f6', 'position' => 0, 'is_default' => true, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Contato Feito', 'color' => '#0891b2', 'position' => 1, 'is_default' => false, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Em Negociação', 'color' => '#d97706', 'position' => 2, 'is_default' => false, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Proposta Enviada', 'color' => '#8b5cf6', 'position' => 3, 'is_default' => false, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Fechamento', 'color' => '#ea580c', 'position' => 4, 'is_default' => false, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Ganho', 'color' => '#059669', 'position' => 5, 'is_default' => false, 'is_won' => true, 'is_lost' => false],
            ['name' => 'Perdido', 'color' => '#dc2626', 'position' => 6, 'is_default' => false, 'is_won' => false, 'is_lost' => true],
        ];

        foreach ($stages as $stage) {
            PipelineStage::updateOrCreate(
                ['name' => $stage['name']],
                $stage
            );
        }
    }
}
