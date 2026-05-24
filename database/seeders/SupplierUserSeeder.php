<?php

namespace Database\Seeders;

use App\Domain\Supplier\Enums\SupplierOrigin;
use App\Domain\Supplier\Models\Supplier;
use App\Domain\Supplier\Models\SupplierUser;
use Illuminate\Database\Seeder;

class SupplierUserSeeder extends Seeder
{
    public function run(): void
    {
        $supplier = Supplier::firstOrCreate(
            ['email' => 'andre@solerbastos.com'],
            [
                'name' => 'Andre Soler Bastos',
                'origin' => SupplierOrigin::Br,
                'phone' => '(11) 99999-9999',
                'active' => true,
                'notes' => 'Fornecedor exclusivo de estoque consignado',
            ]
        );

        SupplierUser::firstOrCreate(
            ['email' => 'andre@solerbastos.com'],
            [
                'supplier_id' => $supplier->id,
                'password' => 'DGStore2026!',
                'active' => true,
            ]
        );

        $this->command->info('Fornecedor Andre Soler Bastos e usuário criados com sucesso!');
        $this->command->info('Email: andre@solerbastos.com');
        $this->command->info('Senha: DGStore2026! (solicite ao fornecedor alterar no primeiro login)');
    }
}
