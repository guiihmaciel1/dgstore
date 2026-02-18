<?php

namespace Database\Seeders;

use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed dos usuários iniciais do sistema.
     */
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Administrador Geral',
                'email'    => 'admin@dgstore.com.br',
                'password' => Hash::make('DgStore@2026!'),
                'role'     => UserRole::AdminGeral,
                'active'   => true,
            ],
            [
                'name'     => 'Admin Distribuidora',
                'email'    => 'b2b@dgstore.com.br',
                'password' => Hash::make('DgStore@2026!'),
                'role'     => UserRole::AdminB2B,
                'active'   => true,
            ],
            [
                'name'     => 'Vendedor',
                'email'    => 'vendedor@dgstore.com.br',
                'password' => Hash::make('DgStore@2026!'),
                'role'     => UserRole::Seller,
                'active'   => true,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData,
            );
        }

        $this->command->info('Usuários iniciais criados com sucesso.');
    }
}
