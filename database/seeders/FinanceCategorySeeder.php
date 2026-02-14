<?php

namespace Database\Seeders;

use App\Domain\Finance\Models\FinancialAccount;
use App\Domain\Finance\Models\FinancialCategory;
use Illuminate\Database\Seeder;

class FinanceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $incomeCategories = [
            ['name' => 'Venda', 'color' => '#16a34a', 'icon' => 'cart'],
            ['name' => 'Sinal de Reserva', 'color' => '#2563eb', 'icon' => 'calendar'],
            ['name' => 'Suprimento', 'color' => '#0891b2', 'icon' => 'plus'],
            ['name' => 'Outras Receitas', 'color' => '#7c3aed', 'icon' => 'dots'],
        ];

        $expenseCategories = [
            ['name' => 'Compra Fornecedor', 'color' => '#dc2626', 'icon' => 'truck'],
            ['name' => 'Frete', 'color' => '#ea580c', 'icon' => 'package'],
            ['name' => 'Aluguel', 'color' => '#9333ea', 'icon' => 'home'],
            ['name' => 'Internet/Telefone', 'color' => '#0284c7', 'icon' => 'wifi'],
            ['name' => 'Salários', 'color' => '#4f46e5', 'icon' => 'users'],
            ['name' => 'Impostos', 'color' => '#b91c1c', 'icon' => 'document'],
            ['name' => 'Manutenção', 'color' => '#ca8a04', 'icon' => 'wrench'],
            ['name' => 'Marketing', 'color' => '#db2777', 'icon' => 'megaphone'],
            ['name' => 'Custo de Mercadoria', 'color' => '#be123c', 'icon' => 'tag'],
            ['name' => 'Trade-in', 'color' => '#f97316', 'icon' => 'refresh'],
            ['name' => 'Outras Despesas', 'color' => '#6b7280', 'icon' => 'dots'],
        ];

        foreach ($incomeCategories as $cat) {
            FinancialCategory::firstOrCreate(
                ['name' => $cat['name'], 'type' => 'income'],
                array_merge($cat, ['type' => 'income', 'is_system' => true])
            );
        }

        foreach ($expenseCategories as $cat) {
            FinancialCategory::firstOrCreate(
                ['name' => $cat['name'], 'type' => 'expense'],
                array_merge($cat, ['type' => 'expense', 'is_system' => true])
            );
        }

        // Conta padrão: Caixa Loja
        FinancialAccount::firstOrCreate(
            ['name' => 'Caixa Loja'],
            [
                'type' => 'cash',
                'initial_balance' => 0,
                'current_balance' => 0,
                'color' => '#16a34a',
                'is_default' => true,
                'is_active' => true,
            ]
        );
    }
}
