<?php

namespace Database\Seeders;

use App\Domain\CRM\Models\PipelineStage;
use Illuminate\Database\Seeder;

class PipelineStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            ['name' => 'Primeiro Atendimento',  'color' => '#3b82f6', 'position' => 0, 'is_default' => true,  'is_won' => false, 'is_lost' => false],
            ['name' => 'Interesse Identificado', 'color' => '#0891b2', 'position' => 1, 'is_default' => false, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Negociação',             'color' => '#d97706', 'position' => 2, 'is_default' => false, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Aguardando Estoque',     'color' => '#8b5cf6', 'position' => 3, 'is_default' => false, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Proposta/Orçamento',     'color' => '#ea580c', 'position' => 4, 'is_default' => false, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Ganho',                  'color' => '#059669', 'position' => 5, 'is_default' => false, 'is_won' => true,  'is_lost' => false],
            ['name' => 'Perdido',                'color' => '#dc2626', 'position' => 6, 'is_default' => false, 'is_won' => false, 'is_lost' => true],
        ];

        foreach ($stages as $stage) {
            PipelineStage::updateOrCreate(
                ['name' => $stage['name']],
                $stage
            );
        }
    }
}
