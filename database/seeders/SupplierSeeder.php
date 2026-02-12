<?php

namespace Database\Seeders;

use App\Domain\Supplier\Models\Supplier;
use Illuminate\Database\Seeder;

/**
 * Seeder dos fornecedores da DG Store.
 *
 * Uso: php artisan db:seed --class=SupplierSeeder
 */
class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $count = 0;

        foreach ($this->suppliers() as $data) {
            Supplier::updateOrCreate(
                ['phone' => $data['phone']],
                $data,
            );
            $count++;
        }

        $this->command->info("{$count} fornecedores cadastrados com sucesso.");
    }

    private function suppliers(): array
    {
        return [
            // ── Fornecedores PY (Ciudad del Este, Paraguay) ──

            [
                'name'           => 'Atacado Conect',
                'contact_person' => 'Clara Valiente',
                'phone'          => '+595981791197',
                'email'          => null,
                'address'        => 'Avda. Carlos A. López, Ciudad del Este, Paraguay',
                'notes'          => 'Fornecedor PY. Electrónicos en Gral. Horário: seg-sex 06:00-16:00.',
                'active'         => true,
            ],
            [
                'name'           => 'Melody Atacado',
                'contact_person' => 'Karina Colman',
                'phone'          => '+595992545846',
                'email'          => null,
                'address'        => 'Galería Jebai 3ro andar, Av. Adrián Jara, Ciudad del Este, Paraguay',
                'notes'          => 'Fornecedor PY. Loja de celulares. Asesora de compras y ventas. Horário: 07:10-15:30.',
                'active'         => true,
            ],
            [
                'name'           => 'Fernando Orue',
                'contact_person' => 'Fernando Orue',
                'phone'          => '+595975829519',
                'email'          => null,
                'address'        => 'Ciudad del Este, Paraguay',
                'notes'          => 'Fornecedor PY.',
                'active'         => true,
            ],
            [
                'name'           => 'Mário Cell',
                'contact_person' => 'Verônica',
                'phone'          => '+595991560390',
                'email'          => 'vendas@mariocell.com',
                'address'        => 'Piribebuy, Ciudad del Este, Paraguay',
                'notes'          => 'Fornecedor PY. Vendedora Atacado. Produtos selecionados Apple. Horário: 07:00-15:00. Site: https://www.mariocell.com/',
                'active'         => true,
            ],
            [
                'name'           => 'Atlantico Shop (Nicole)',
                'contact_person' => 'Nicole Soto',
                'phone'          => '+595993439300',
                'email'          => null,
                'address'        => 'Ciudad del Este, Paraguay',
                'notes'          => 'Fornecedor PY. Ventas. Horário: 06:30-15:30. Site: https://atlanticoshop.com.py/',
                'active'         => true,
            ],
            [
                'name'           => 'Atlantico Shop (Rebeca)',
                'contact_person' => 'Rebeca Doria',
                'phone'          => '+595993288151',
                'email'          => null,
                'address'        => 'Jebai, Ciudad del Este 7000, Paraguay',
                'notes'          => 'Fornecedor PY. Horário: 06:30-15:30. Instagram: @atlanticoshopp',
                'active'         => true,
            ],

            // ── Fornecedores BR (Brasil) ──

            [
                'name'           => 'Prime Imports',
                'contact_person' => 'Prime',
                'phone'          => '+5517997580508',
                'email'          => null,
                'address'        => null,
                'notes'          => 'Fornecedor BR. Aberta somente com hora marcada.',
                'active'         => true,
            ],
            [
                'name'           => 'GV Cell',
                'contact_person' => 'Gv Cell',
                'phone'          => '+5517996602725',
                'email'          => null,
                'address'        => 'Nova Redentora, São José do Rio Preto SP, 15090-080, Brasil',
                'notes'          => 'Fornecedor BR. GV Celulares desde 2014. Horário: 10:30-19:00.',
                'active'         => true,
            ],
        ];
    }
}
