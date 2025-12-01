<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuário Admin Principal
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@investimentos.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
        ]);

        // Usuário comum de exemplo (opcional)
        User::create([
            'name' => 'Usuário Teste',
            'email' => 'usuario@investimentos.com',
            'password' => Hash::make('senha123'),
            'is_admin' => false,
        ]);
    }
}